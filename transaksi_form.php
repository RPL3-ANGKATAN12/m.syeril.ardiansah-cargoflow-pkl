<?php
/**
 * Form Input Transaksi Pengiriman Kargo Baru - Tailwind CSS Clean Light Mode
 * LOGIX PRO - Sistem Manajemen Pengiriman Kargo & Kontainer
 */
$pageTitle = 'Formulir Data Transaksi Pengiriman';
require_once __DIR__ . '/includes/header.php';

$error = '';
$noResiOtomatis = generateNoResi($db);

// Ambil List Master Pelanggan & Master Kontainer
$pelanggans = $db->query("SELECT id, kode_pelanggan, nama_perusahaan FROM pelanggan ORDER BY nama_perusahaan ASC")->fetchAll();
$kontainers = $db->query("SELECT id, nomor_kontainer, tipe_kontainer, kapasitas_ton, status FROM kontainer ORDER BY status ASC, nomor_kontainer ASC")->fetchAll();

if (empty($pelanggans) || empty($kontainers)) {
    setFlash('warning', 'Peringatan: Data master pelanggan atau kontainer masih kosong. Silakan lengkapi data master terlebih dahulu.');
}

$formData = [
    'no_resi' => $noResiOtomatis,
    'id_pelanggan' => '',
    'id_kontainer' => '',
    'pelabuhan_asal' => 'Pelabuhan Tanjung Priok, Jakarta',
    'pelabuhan_tujuan' => 'Pelabuhan Tanjung Perak, Surabaya',
    'jenis_muatan' => '',
    'berat_muatan_ton' => '',
    'tanggal_booking' => date('Y-m-d'),
    'tanggal_muat' => date('Y-m-d', strtotime('+1 day')),
    'estimasi_tiba' => date('Y-m-d', strtotime('+5 days')),
    'tarif_freight' => 15000000,
    'biaya_handling' => 1500000,
    'total_biaya' => 16500000,
    'status_pengiriman' => 'Draft / Booking',
    'catatan' => ''
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $formData['no_resi'] = trim($_POST['no_resi'] ?? $noResiOtomatis);
    $formData['id_pelanggan'] = (int)($_POST['id_pelanggan'] ?? 0);
    $formData['id_kontainer'] = (int)($_POST['id_kontainer'] ?? 0);
    $formData['pelabuhan_asal'] = trim($_POST['pelabuhan_asal'] ?? '');
    $formData['pelabuhan_tujuan'] = trim($_POST['pelabuhan_tujuan'] ?? '');
    $formData['jenis_muatan'] = trim($_POST['jenis_muatan'] ?? '');
    $formData['berat_muatan_ton'] = floatval($_POST['berat_muatan_ton'] ?? 0);
    $formData['tanggal_booking'] = $_POST['tanggal_booking'] ?? date('Y-m-d');
    $formData['tanggal_muat'] = $_POST['tanggal_muat'] ?? date('Y-m-d');
    $formData['estimasi_tiba'] = $_POST['estimasi_tiba'] ?? date('Y-m-d');
    $formData['tarif_freight'] = floatval($_POST['tarif_freight'] ?? 0);
    $formData['biaya_handling'] = floatval($_POST['biaya_handling'] ?? 0);
    $formData['total_biaya'] = $formData['tarif_freight'] + $formData['biaya_handling'];
    $formData['status_pengiriman'] = $_POST['status_pengiriman'] ?? 'Draft / Booking';
    $formData['catatan'] = trim($_POST['catatan'] ?? '');

    // 1. Validasi Kolom Wajib
    if (empty($formData['id_pelanggan']) || empty($formData['id_kontainer']) || empty($formData['jenis_muatan'])) {
        $error = 'Validasi Transaksi Gagal! Mohon pilih shipper pelanggan, unit kontainer, dan jenis muatan kargo.';
    } elseif ($formData['berat_muatan_ton'] <= 0) {
        $error = 'Validasi Transaksi Gagal! Berat muatan kargo harus lebih besar dari 0 Ton.';
    } else {
        // 2. Validasi Kapasitas Kontainer
        $stmtK = $db->prepare("SELECT kapasitas_ton, nomor_kontainer, status FROM kontainer WHERE id = ?");
        $stmtK->execute([$formData['id_kontainer']]);
        $kInfo = $stmtK->fetch();

        if ($kInfo && $formData['berat_muatan_ton'] > $kInfo['kapasitas_ton']) {
            $error = "Validasi Transaksi Gagal! Berat muatan ({$formData['berat_muatan_ton']} Ton) MELEBIHI kapasitas maksimal kontainer {$kInfo['nomor_kontainer']} ({$kInfo['kapasitas_ton']} Ton). Bahaya overload pelayaran!";
        } else {
            try {
                $stmtInsert = $db->prepare("
                    INSERT INTO transaksi_pengiriman (
                        no_resi, id_pelanggan, id_kontainer, pelabuhan_asal, pelabuhan_tujuan,
                        jenis_muatan, berat_muatan_ton, tanggal_booking, tanggal_muat, estimasi_tiba,
                        tarif_freight, biaya_handling, total_biaya, status_pengiriman, catatan, created_by
                    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
                ");
                $stmtInsert->execute([
                    $formData['no_resi'],
                    $formData['id_pelanggan'],
                    $formData['id_kontainer'],
                    $formData['pelabuhan_asal'],
                    $formData['pelabuhan_tujuan'],
                    $formData['jenis_muatan'],
                    $formData['berat_muatan_ton'],
                    $formData['tanggal_booking'],
                    $formData['tanggal_muat'],
                    $formData['estimasi_tiba'],
                    $formData['tarif_freight'],
                    $formData['biaya_handling'],
                    $formData['total_biaya'],
                    $formData['status_pengiriman'],
                    $formData['catatan'],
                    $_SESSION['user_id'] ?? 1
                ]);

                $newId = $db->lastInsertId();

                $db->prepare("UPDATE kontainer SET status = 'Sedang Digunakan' WHERE id = ?")->execute([$formData['id_kontainer']]);

                $stmtTrack = $db->prepare("
                    INSERT INTO riwayat_status (id_transaksi, status, lokasi, keterangan)
                    VALUES (?, ?, ?, ?)
                ");
                $stmtTrack->execute([
                    $newId,
                    'Surat Jalan Diterbitkan',
                    $formData['pelabuhan_asal'],
                    'Booking pengiriman kargo disetujui. Surat jalan diterbitkan.'
                ]);

                setFlash('success', "Konfirmasi Berhasil: Surat jalan pengiriman nomor {$formData['no_resi']} berhasil diterbitkan!");
                header("Location: transaksi_detail.php?id={$newId}");
                exit;
            } catch (Exception $e) {
                $error = 'Terjadi galat database saat menyimpan transaksi: ' . $e->getMessage();
            }
        }
    }
}
?>

<div class="mb-6">
    <a href="transaksi.php" class="inline-flex items-center gap-1.5 text-xs font-semibold text-slate-500 hover:text-slate-800 transition-colors">
        <span>&larr;</span>
        <span>Kembali ke Daftar Transaksi</span>
    </a>
</div>

<div class="max-w-4xl mx-auto bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden">
    <div class="p-6 md:p-8 border-b border-slate-100 flex items-center justify-between">
        <div>
            <h3 class="text-lg font-bold text-slate-900">Formulir Transaksi Penerbitan Pengiriman Kargo (Booking Shipment)</h3>
            <p class="text-xs text-slate-500 mt-1">Entri surat jalan resmi kargo kontainer IDUKA, penentuan rute, dan kalkulasi biaya freight.</p>
        </div>
        <span class="inline-block px-3 py-1.5 rounded-lg font-mono font-bold text-xs bg-blue-50 text-blue-700 border border-blue-200">
            <?= htmlspecialchars($formData['no_resi']) ?>
        </span>
    </div>

    <div class="p-6 md:p-8">
        <?php if (!empty($error)): ?>
            <!-- Tampilan Konfirmasi Gagal Validasi Transaksi (Untuk Screenshot Tugas) -->
            <div class="mb-6 p-4 rounded-xl bg-rose-50 border border-rose-200 text-rose-900 flex items-start gap-3 text-xs leading-relaxed" id="alertGagalTransaksi">
                <svg class="w-5 h-5 text-rose-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                <div><?= htmlspecialchars($error) ?></div>
            </div>
        <?php endif; ?>

        <form action="transaksi_form.php" method="POST" id="formTransaksiPengiriman" class="space-y-8">
            <input type="hidden" name="no_resi" value="<?= htmlspecialchars($formData['no_resi']) ?>">

            <!-- 1. Bagian Mitra & Kontainer -->
            <div>
                <h4 class="text-xs font-bold uppercase tracking-wider text-blue-600 border-b border-slate-100 pb-2 mb-4">
                    1. Data Mitra Shipper & Armada Kontainer
                </h4>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div>
                        <label for="id_pelanggan" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Pilih Shipper (Perusahaan Pengirim) <span class="text-rose-500">*</span></label>
                        <select name="id_pelanggan" id="id_pelanggan" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-slate-800 text-sm focus:outline-none focus:ring-4 focus:ring-blue-100 focus:border-blue-500 font-medium transition-all" required>
                            <option value="">-- Pilih Mitra Pelanggan --</option>
                            <?php foreach ($pelanggans as $p): ?>
                                <option value="<?= $p['id'] ?>" <?= $formData['id_pelanggan'] == $p['id'] ? 'selected' : '' ?>>
                                    [<?= htmlspecialchars($p['kode_pelanggan']) ?>] <?= htmlspecialchars($p['nama_perusahaan']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div>
                        <label for="id_kontainer" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Pilih Unit Kontainer Armada <span class="text-rose-500">*</span></label>
                        <select name="id_kontainer" id="id_kontainer" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-slate-800 text-sm focus:outline-none focus:ring-4 focus:ring-blue-100 focus:border-blue-500 font-medium transition-all" required>
                            <option value="">-- Pilih Unit Kontainer --</option>
                            <?php foreach ($kontainers as $k): ?>
                                <option value="<?= $k['id'] ?>" data-kapasitas="<?= $k['kapasitas_ton'] ?>" <?= $formData['id_kontainer'] == $k['id'] ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($k['nomor_kontainer']) ?> - <?= htmlspecialchars($k['tipe_kontainer']) ?> (Maks: <?= $k['kapasitas_ton'] ?> Ton) [<?= $k['status'] ?>]
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <div id="kapasitas_warning" class="hidden text-xs text-rose-600 font-bold mt-1.5"></div>
                    </div>
                </div>
            </div>

            <!-- 2. Bagian Rute & Kargo -->
            <div>
                <h4 class="text-xs font-bold uppercase tracking-wider text-blue-600 border-b border-slate-100 pb-2 mb-4">
                    2. Spesifikasi Kargo & Rute Pelayaran
                </h4>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div>
                        <label for="pelabuhan_asal" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Pelabuhan Muat Asal (POL) <span class="text-rose-500">*</span></label>
                        <input type="text" name="pelabuhan_asal" id="pelabuhan_asal" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-slate-800 text-sm focus:outline-none focus:ring-4 focus:ring-blue-100 focus:border-blue-500 font-medium transition-all" placeholder="Cth: Pelabuhan Tanjung Priok, Jakarta" value="<?= htmlspecialchars($formData['pelabuhan_asal']) ?>" required>
                    </div>

                    <div>
                        <label for="pelabuhan_tujuan" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Pelabuhan Bongkar Tujuan (POD) <span class="text-rose-500">*</span></label>
                        <input type="text" name="pelabuhan_tujuan" id="pelabuhan_tujuan" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-slate-800 text-sm focus:outline-none focus:ring-4 focus:ring-blue-100 focus:border-blue-500 font-medium transition-all" placeholder="Cth: Pelabuhan Tanjung Perak, Surabaya" value="<?= htmlspecialchars($formData['pelabuhan_tujuan']) ?>" required>
                    </div>

                    <div>
                        <label for="jenis_muatan" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Deskripsi Muatan Kargo <span class="text-rose-500">*</span></label>
                        <input type="text" name="jenis_muatan" id="jenis_muatan" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-slate-800 text-sm focus:outline-none focus:ring-4 focus:ring-blue-100 focus:border-blue-500 font-medium transition-all" placeholder="Cth: Komponen Elektronik & Suku Cadang" value="<?= htmlspecialchars($formData['jenis_muatan']) ?>" required>
                    </div>

                    <div>
                        <label for="berat_muatan_ton" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Berat Muatan (Ton) <span class="text-rose-500">*</span></label>
                        <input type="number" step="0.01" name="berat_muatan_ton" id="berat_muatan_ton" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-slate-800 text-sm focus:outline-none focus:ring-4 focus:ring-blue-100 focus:border-blue-500 font-medium transition-all" placeholder="Cth: 18.5" value="<?= htmlspecialchars($formData['berat_muatan_ton']) ?>" required>
                    </div>

                    <div>
                        <label for="tanggal_muat" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Tanggal Rencana Muat (ETD) <span class="text-rose-500">*</span></label>
                        <input type="date" name="tanggal_muat" id="tanggal_muat" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-slate-800 text-sm focus:outline-none focus:ring-4 focus:ring-blue-100 focus:border-blue-500 font-medium transition-all" value="<?= htmlspecialchars($formData['tanggal_muat']) ?>" required>
                    </div>

                    <div>
                        <label for="estimasi_tiba" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Estimasi Tiba Tujuan (ETA) <span class="text-rose-500">*</span></label>
                        <input type="date" name="estimasi_tiba" id="estimasi_tiba" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-slate-800 text-sm focus:outline-none focus:ring-4 focus:ring-blue-100 focus:border-blue-500 font-medium transition-all" value="<?= htmlspecialchars($formData['estimasi_tiba']) ?>" required>
                    </div>
                </div>
            </div>

            <!-- 3. Bagian Biaya & Status -->
            <div>
                <h4 class="text-xs font-bold uppercase tracking-wider text-blue-600 border-b border-slate-100 pb-2 mb-4">
                    3. Perhitungan Biaya Pengiriman (Freight & Handling)
                </h4>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div>
                        <label for="tarif_freight" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Tarif Uang Tambang (Ocean Freight) (Rp) <span class="text-rose-500">*</span></label>
                        <input type="number" name="tarif_freight" id="tarif_freight" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-slate-800 text-sm focus:outline-none focus:ring-4 focus:ring-blue-100 focus:border-blue-500 font-medium transition-all" value="<?= htmlspecialchars($formData['tarif_freight']) ?>" required>
                    </div>

                    <div>
                        <label for="biaya_handling" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Biaya Handling & Dermaga (Rp) <span class="text-rose-500">*</span></label>
                        <input type="number" name="biaya_handling" id="biaya_handling" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-slate-800 text-sm focus:outline-none focus:ring-4 focus:ring-blue-100 focus:border-blue-500 font-medium transition-all" value="<?= htmlspecialchars($formData['biaya_handling']) ?>" required>
                    </div>

                    <div>
                        <label for="total_biaya" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Total Tagihan Pengiriman (Rp)</label>
                        <input type="number" name="total_biaya" id="total_biaya" class="w-full bg-slate-100 border border-slate-200 rounded-xl px-4 py-3 text-emerald-700 font-extrabold text-base focus:outline-none transition-all" value="<?= htmlspecialchars($formData['total_biaya']) ?>" readonly>
                        <span class="text-xs text-slate-500 mt-1 block">Total terhitung otomatis: <strong id="total_biaya_preview" class="text-emerald-700 font-bold">Rp <?= number_format($formData['total_biaya'], 0, ',', '.') ?></strong></span>
                    </div>

                    <div>
                        <label for="status_pengiriman" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Status Operasional Awal</label>
                        <select name="status_pengiriman" id="status_pengiriman" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-slate-800 text-sm focus:outline-none focus:ring-4 focus:ring-blue-100 focus:border-blue-500 font-medium transition-all">
                            <option value="Draft / Booking" selected>Draft / Booking (Pemesanan Terdata)</option>
                            <option value="Loading Kontainer">Loading Kontainer (Proses Muat di Depo)</option>
                            <option value="Sailing / Dalam Perjalanan">Sailing / Dalam Perjalanan (Kapal Bertolak)</option>
                        </select>
                    </div>

                    <div class="md:col-span-2">
                        <label for="catatan" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Catatan Khusus Operasional / Instruksi Muat</label>
                        <textarea name="catatan" id="catatan" rows="2" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-slate-800 text-sm focus:outline-none focus:ring-4 focus:ring-blue-100 focus:border-blue-500 font-medium transition-all" placeholder="Contoh: Muatan mudah pecah / suhu reefer container harus dijaga."><?= htmlspecialchars($formData['catatan']) ?></textarea>
                    </div>
                </div>
            </div>

            <div class="flex items-center justify-end gap-3 pt-6 border-t border-slate-100">
                <a href="transaksi.php" class="px-5 py-2.5 rounded-xl border border-slate-200 text-slate-600 hover:bg-slate-50 font-semibold text-xs transition-all">Batal</a>
                <button type="submit" class="inline-flex items-center gap-2 px-6 py-3 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-bold text-sm shadow-md shadow-blue-500/20 transition-all" id="btnTerbitkanSuratJalan">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <span>Terbitkan Surat Jalan Pengiriman</span>
                </button>
            </div>
        </form>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
