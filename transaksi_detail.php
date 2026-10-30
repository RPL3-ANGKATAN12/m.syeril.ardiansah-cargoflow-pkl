<?php
/**
 * Detail Transaksi Pengiriman, Update Status & Cetak Surat Jalan - Tailwind CSS Clean Light Mode
 * LOGIX PRO - Sistem Manajemen Pengiriman Kargo & Kontainer
 */
$pageTitle = 'Detail Surat Jalan Pengiriman';
require_once __DIR__ . '/includes/header.php';

$id = (int)($_GET['id'] ?? 0);
$stmt = $db->prepare("
    SELECT t.*, p.nama_perusahaan, p.penanggung_jawab, p.no_telp, p.email, p.alamat as alamat_shipper,
           k.nomor_kontainer, k.tipe_kontainer, k.kapasitas_ton, k.lokasi_depo,
           u.nama_lengkap as pembuat_surat
    FROM transaksi_pengiriman t
    JOIN pelanggan p ON t.id_pelanggan = p.id
    JOIN kontainer k ON t.id_kontainer = k.id
    LEFT JOIN users u ON t.created_by = u.id
    WHERE t.id = ?
");
$stmt->execute([$id]);
$trx = $stmt->fetch();

if (!$trx) {
    setFlash('danger', 'Data transaksi pengiriman tidak ditemukan!');
    header('Location: transaksi.php');
    exit;
}

// Handle Update Status & Tambah Riwayat Tracking (Form Tambahan)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'update_status') {
    $statusBaru = $_POST['status_pengiriman'] ?? $trx['status_pengiriman'];
    $lokasiUpdate = trim($_POST['lokasi_update'] ?? '');
    $keteranganUpdate = trim($_POST['keterangan_update'] ?? '');

    try {
        $db->prepare("UPDATE transaksi_pengiriman SET status_pengiriman = ? WHERE id = ?")->execute([$statusBaru, $id]);

        if ($statusBaru === 'Selesai' || $statusBaru === 'Tiba di Tujuan') {
            $db->prepare("UPDATE kontainer SET status = 'Tersedia' WHERE id = ?")->execute([$trx['id_kontainer']]);
        }

        if (!empty($lokasiUpdate) || !empty($keteranganUpdate)) {
            $stmtHist = $db->prepare("INSERT INTO riwayat_status (id_transaksi, status, lokasi, keterangan) VALUES (?, ?, ?, ?)");
            $stmtHist->execute([$id, $statusBaru, $lokasiUpdate, $keteranganUpdate]);
        }

        setFlash('success', "Konfirmasi Berhasil: Status pengiriman berhasil diperbarui menjadi '{$statusBaru}'!");
        header("Location: transaksi_detail.php?id={$id}");
        exit;
    } catch (Exception $e) {
        setFlash('danger', 'Gagal memperbarui status: ' . $e->getMessage());
    }
}

// Ambil Riwayat Tracking
$stmtHistory = $db->prepare("SELECT * FROM riwayat_status WHERE id_transaksi = ? ORDER BY waktu DESC");
$stmtHistory->execute([$id]);
$histories = $stmtHistory->fetchAll();
?>

<div class="no-print flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
    <a href="transaksi.php" class="inline-flex items-center gap-1.5 text-xs font-semibold text-slate-500 hover:text-slate-800 transition-colors">
        <span>&larr;</span>
        <span>Kembali ke Daftar Transaksi</span>
    </a>
    <div class="flex items-center gap-3">
        <button type="button" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-white border border-slate-200 text-slate-700 hover:bg-slate-50 font-bold text-xs shadow-sm transition-all" onclick="window.print()">
            <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
            <span>Cetak Surat Jalan (Print / PDF)</span>
        </button>
        <a href="konfirmasi_berhasil.php?id=<?= $trx['id'] ?>" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs shadow-md shadow-emerald-500/20 transition-all">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            <span>Lihat Halaman Bukti Sukses</span>
        </a>
    </div>
</div>

<!-- Lembar Surat Jalan Resmi (Print-Friendly Sheet) -->
<div class="bg-white rounded-3xl border border-slate-200/80 shadow-md p-8 md:p-12 max-w-4xl mx-auto mb-10">
    <!-- Header Surat Jalan -->
    <div class="flex flex-col sm:flex-row sm:items-start justify-between border-b-2 border-slate-200 pb-6 mb-8 gap-4">
        <div>
            <div class="flex items-center gap-3 mb-2">
                <div class="w-10 h-10 rounded-xl bg-blue-600 text-white font-extrabold flex items-center justify-center text-lg shadow-sm">
                    LX
                </div>
                <div>
                    <h2 class="text-2xl font-black text-slate-900 tracking-tight"><?= APP_NAME ?></h2>
                </div>
            </div>
            <div class="text-xs text-slate-500"><?= APP_COMPANY ?></div>
            <div class="text-[11px] text-slate-400">Surat Jalan Kargo Laut & Domestik IDUKA PKL</div>
        </div>
        <div class="sm:text-right">
            <div class="text-xs font-bold uppercase tracking-wider text-slate-400">SURAT JALAN & BILL OF LADING</div>
            <h3 class="text-xl font-extrabold font-mono text-blue-700 mt-0.5"><?= htmlspecialchars($trx['no_resi']) ?></h3>
            <div class="text-xs text-slate-500 mt-1">Tanggal Terbit: <?= tanggalIndo($trx['tanggal_booking']) ?></div>
            <div class="mt-2">
                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-blue-50 text-blue-700 border border-blue-200">
                    STATUS: <?= strtoupper($trx['status_pengiriman']) ?>
                </span>
            </div>
        </div>
    </div>

    <!-- Shipper & Kontainer Info -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
        <div class="bg-slate-50 p-5 rounded-2xl border border-slate-200/80 text-xs">
            <div class="text-[11px] font-bold uppercase tracking-wider text-slate-400 mb-2">PENGIRIM (SHIPPER):</div>
            <strong class="text-sm font-bold text-slate-900 block"><?= htmlspecialchars($trx['nama_perusahaan']) ?></strong>
            <div class="text-slate-600 mt-1">PIC: <?= htmlspecialchars($trx['penanggung_jawab']) ?></div>
            <div class="text-slate-600">Telp: <?= htmlspecialchars($trx['no_telp']) ?></div>
            <div class="text-slate-500 mt-2 leading-relaxed"><?= htmlspecialchars($trx['alamat_shipper']) ?></div>
        </div>

        <div class="bg-slate-50 p-5 rounded-2xl border border-slate-200/80 text-xs">
            <div class="text-[11px] font-bold uppercase tracking-wider text-slate-400 mb-2">UNIT KONTAINER & DEPO:</div>
            <strong class="text-sm font-bold font-mono text-blue-700 block"><?= htmlspecialchars($trx['nomor_kontainer']) ?></strong>
            <div class="text-slate-700 font-semibold mt-1"><?= htmlspecialchars($trx['tipe_kontainer']) ?></div>
            <div class="text-slate-600">Kapasitas Maks: <?= number_format($trx['kapasitas_ton'], 2) ?> Ton</div>
            <div class="text-slate-500 mt-2">Depo Asal: <?= htmlspecialchars($trx['lokasi_depo']) ?></div>
        </div>
    </div>

    <!-- Table of Charges -->
    <div class="overflow-x-auto mb-8">
        <table class="w-full text-left text-xs border border-slate-200 rounded-xl overflow-hidden">
            <thead class="bg-slate-100 text-slate-700 font-bold uppercase tracking-wider border-b border-slate-200">
                <tr>
                    <th class="p-3.5">Deskripsi Rute & Muatan Kargo</th>
                    <th class="p-3.5">Jadwal Pelayaran</th>
                    <th class="p-3.5">Bobot Muatan</th>
                    <th class="p-3.5 text-right">Subtotal</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-200">
                <tr>
                    <td class="p-3.5">
                        <strong class="text-slate-900 text-sm"><?= htmlspecialchars($trx['jenis_muatan']) ?></strong>
                        <div class="text-slate-500 mt-1">
                            Rute: <?= htmlspecialchars($trx['pelabuhan_asal']) ?> &rarr; <?= htmlspecialchars($trx['pelabuhan_tujuan']) ?>
                        </div>
                        <?php if (!empty($trx['catatan'])): ?>
                            <div class="text-rose-600 font-semibold mt-1">Instruksi: <?= htmlspecialchars($trx['catatan']) ?></div>
                        <?php endif; ?>
                    </td>
                    <td class="p-3.5 text-slate-600">
                        <div>ETD: <?= tanggalIndo($trx['tanggal_muat']) ?></div>
                        <div>ETA: <?= tanggalIndo($trx['estimasi_tiba']) ?></div>
                    </td>
                    <td class="p-3.5 font-bold text-slate-900 text-sm">
                        <?= number_format($trx['berat_muatan_ton'], 2) ?> Ton
                    </td>
                    <td class="p-3.5 text-right font-semibold text-slate-900 text-sm">
                        <?= rupiah($trx['tarif_freight']) ?>
                    </td>
                </tr>
                <tr class="bg-slate-50/50">
                    <td colspan="3" class="p-3.5 text-right font-medium text-slate-600">Biaya Terminal Handling Charges (THC) & Dermaga:</td>
                    <td class="p-3.5 text-right font-medium text-slate-900"><?= rupiah($trx['biaya_handling']) ?></td>
                </tr>
                <tr class="bg-blue-50/60 font-extrabold text-sm border-t-2 border-slate-300">
                    <td colspan="3" class="p-3.5 text-right text-slate-800">TOTAL BIAYA PENGIRIMAN:</td>
                    <td class="p-3.5 text-right text-blue-700 font-mono text-base"><?= rupiah($trx['total_biaya']) ?></td>
                </tr>
            </tbody>
        </table>
    </div>

    <!-- Signatures -->
    <div class="grid grid-cols-2 text-center text-xs mt-12 pt-4">
        <div>
            <div class="text-slate-400 mb-16">Shipper / Pengirim Kargo,</div>
            <strong class="text-slate-900 block font-bold">( <?= htmlspecialchars($trx['penanggung_jawab']) ?> )</strong>
            <div class="text-[11px] text-slate-400 mt-0.5"><?= htmlspecialchars($trx['nama_perusahaan']) ?></div>
        </div>
        <div>
            <div class="text-slate-400 mb-16">Petugas Operasional IDUKA,</div>
            <strong class="text-slate-900 block font-bold">( <?= htmlspecialchars($trx['pembuat_surat'] ?? 'Petugas Logistik') ?> )</strong>
            <div class="text-[11px] text-slate-400 mt-0.5">J3LOG Logistics Terminal</div>
        </div>
    </div>
</div>

<!-- Form Tambahan: Update Status Operasional & Catatan Tracking -->
<div class="no-print max-w-4xl mx-auto bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden mb-12">
    <div class="p-6 md:p-8 border-b border-slate-100">
        <h3 class="text-base font-bold text-slate-900">Formulir Pembaruan Status &amp; Riwayat Pelacakan Kargo</h3>
        <p class="text-xs text-slate-500 mt-0.5">Gunakan formulir ini untuk mengubah status perjalanan dan menambah riwayat lokasi kontainer.</p>
    </div>

    <div class="p-6 md:p-8">
        <form action="transaksi_detail.php?id=<?= $trx['id'] ?>" method="POST" class="space-y-5">
            <input type="hidden" name="action" value="update_status">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <div>
                    <label for="status_pengiriman" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Ubah Status Pengiriman</label>
                    <select name="status_pengiriman" id="status_pengiriman" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 text-slate-800 text-sm focus:outline-none focus:ring-4 focus:ring-blue-100 focus:border-blue-500 font-medium transition-all">
                        <option value="Draft / Booking" <?= $trx['status_pengiriman'] === 'Draft / Booking' ? 'selected' : '' ?>>Draft / Booking</option>
                        <option value="Loading Kontainer" <?= $trx['status_pengiriman'] === 'Loading Kontainer' ? 'selected' : '' ?>>Loading Kontainer</option>
                        <option value="Sailing / Dalam Perjalanan" <?= $trx['status_pengiriman'] === 'Sailing / Dalam Perjalanan' ? 'selected' : '' ?>>Sailing / Dalam Perjalanan</option>
                        <option value="Tiba di Tujuan" <?= $trx['status_pengiriman'] === 'Tiba di Tujuan' ? 'selected' : '' ?>>Tiba di Tujuan</option>
                        <option value="Selesai" <?= $trx['status_pengiriman'] === 'Selesai' ? 'selected' : '' ?>>Selesai</option>
                    </select>
                </div>

                <div>
                    <label for="lokasi_update" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Lokasi Saat Ini</label>
                    <input type="text" name="lokasi_update" id="lokasi_update" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 text-slate-800 text-sm focus:outline-none focus:ring-4 focus:ring-blue-100 focus:border-blue-500 font-medium transition-all" placeholder="Cth: Perairan Selat Sunda / Terminal Petikemas">
                </div>

                <div class="md:col-span-2">
                    <label for="keterangan_update" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Keterangan Aktivitas Logistik</label>
                    <input type="text" name="keterangan_update" id="keterangan_update" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 text-slate-800 text-sm focus:outline-none focus:ring-4 focus:ring-blue-100 focus:border-blue-500 font-medium transition-all" placeholder="Cth: Kapal berlayar sesuai jadwal, cuaca kondusif.">
                </div>
            </div>

            <div class="flex justify-end pt-3">
                <button type="submit" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs shadow-md shadow-blue-500/20 transition-all">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                    <span>Update Status Kargo</span>
                </button>
            </div>
        </form>

        <!-- Riwayat Timeline Tracking -->
        <div class="mt-8 pt-6 border-t border-slate-100">
            <h4 class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-4">Log Perjalanan Kontainer:</h4>
            <?php if (empty($histories)): ?>
                <p class="text-xs text-slate-400">Belum ada log pergerakan.</p>
            <?php else: ?>
                <ul class="space-y-4 border-l-2 border-blue-500 pl-4 ml-2">
                    <?php foreach ($histories as $h): ?>
                        <li class="relative">
                            <span class="absolute -left-[21px] top-1.5 w-2.5 h-2.5 rounded-full bg-blue-600 border-2 border-white"></span>
                            <div class="text-xs text-slate-400"><?= date('d M Y H:i', strtotime($h['waktu'])) ?> - <?= htmlspecialchars($h['lokasi']) ?></div>
                            <strong class="text-sm text-slate-900 font-bold"><?= htmlspecialchars($h['status']) ?></strong>
                            <div class="text-xs text-slate-600 mt-0.5"><?= htmlspecialchars($h['keterangan']) ?></div>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
