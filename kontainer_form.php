<?php
/**
 * Form Tambah / Edit Data Master Kontainer - Tailwind CSS Clean Light Mode
 * LOGIX PRO - Sistem Manajemen Pengiriman Kargo & Kontainer
 */
require_once __DIR__ . '/includes/header.php';

$isEdit = false;
$id = (int)($_GET['id'] ?? 0);
$error = '';

$data = [
    'nomor_kontainer' => '',
    'tipe_kontainer' => 'Dry FCL 20ft',
    'kapasitas_ton' => 20.00,
    'lokasi_depo' => 'Depo Tanjung Priok, Jakarta',
    'kondisi' => 'Layak Muat (Grade A)',
    'status' => 'Tersedia'
];

if ($id > 0) {
    $stmt = $db->prepare("SELECT * FROM kontainer WHERE id = ?");
    $stmt->execute([$id]);
    $existing = $stmt->fetch();
    if ($existing) {
        $isEdit = true;
        $data = $existing;
    } else {
        setFlash('danger', 'Data kontainer tidak ditemukan!');
        header('Location: kontainer.php');
        exit;
    }
}

$pageTitle = $isEdit ? 'Edit Data Master Kontainer' : 'Tambah Data Master Kontainer Baru';

// Handle Submit Form
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nomor_kontainer = strtoupper(trim($_POST['nomor_kontainer'] ?? ''));
    $tipe_kontainer = trim($_POST['tipe_kontainer'] ?? '');
    $kapasitas_ton = floatval($_POST['kapasitas_ton'] ?? 0);
    $lokasi_depo = trim($_POST['lokasi_depo'] ?? '');
    $kondisi = trim($_POST['kondisi'] ?? '');
    $status = trim($_POST['status'] ?? 'Tersedia');

    // Validasi Form
    if (empty($nomor_kontainer) || empty($tipe_kontainer) || empty($lokasi_depo)) {
        $error = 'Validasi Gagal! Nomor kontainer, tipe kontainer, dan lokasi depo wajib diisi.';
    } elseif ($kapasitas_ton <= 0) {
        $error = 'Validasi Gagal! Kapasitas muatan kontainer harus lebih besar dari 0 Ton.';
    } else {
        // Cek duplikasi nomor kontainer
        $stmtCheck = $db->prepare("SELECT id FROM kontainer WHERE nomor_kontainer = ? AND id != ?");
        $stmtCheck->execute([$nomor_kontainer, $id]);
        if ($stmtCheck->fetch()) {
            $error = "Validasi Gagal! Nomor kontainer '{$nomor_kontainer}' sudah terdaftar dalam sistem.";
        } else {
            try {
                if ($isEdit) {
                    $stmtUpdate = $db->prepare("
                        UPDATE kontainer 
                        SET nomor_kontainer = ?, tipe_kontainer = ?, kapasitas_ton = ?, lokasi_depo = ?, kondisi = ?, status = ?
                        WHERE id = ?
                    ");
                    $stmtUpdate->execute([$nomor_kontainer, $tipe_kontainer, $kapasitas_ton, $lokasi_depo, $kondisi, $status, $id]);
                    setFlash('success', "Konfirmasi Berhasil: Data kontainer {$nomor_kontainer} berhasil diperbarui!");
                } else {
                    $stmtInsert = $db->prepare("
                        INSERT INTO kontainer (nomor_kontainer, tipe_kontainer, kapasitas_ton, lokasi_depo, kondisi, status)
                        VALUES (?, ?, ?, ?, ?, ?)
                    ");
                    $stmtInsert->execute([$nomor_kontainer, $tipe_kontainer, $kapasitas_ton, $lokasi_depo, $kondisi, $status]);
                    setFlash('success', "Konfirmasi Berhasil: Data master kontainer {$nomor_kontainer} berhasil disimpan ke sistem!");
                }
                header('Location: kontainer.php');
                exit;
            } catch (Exception $e) {
                $error = 'Terjadi kesalahan sistem saat menyimpan data: ' . $e->getMessage();
            }
        }
    }

    $data = [
        'nomor_kontainer' => $nomor_kontainer,
        'tipe_kontainer' => $tipe_kontainer,
        'kapasitas_ton' => $kapasitas_ton,
        'lokasi_depo' => $lokasi_depo,
        'kondisi' => $kondisi,
        'status' => $status
    ];
}
?>

<div class="mb-6">
    <a href="kontainer.php" class="inline-flex items-center gap-1.5 text-xs font-semibold text-slate-500 hover:text-slate-800 transition-colors">
        <span>&larr;</span>
        <span>Kembali ke Daftar Kontainer</span>
    </a>
</div>

<div class="max-w-3xl mx-auto bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden">
    <div class="p-6 md:p-8 border-b border-slate-100">
        <h3 class="text-lg font-bold text-slate-900"><?= $isEdit ? 'Formulir Edit Data Master Kontainer' : 'Formulir Input Data Master Kontainer Baru' ?></h3>
        <p class="text-xs text-slate-500 mt-1">Lengkapi spesifikasi teknis dan lokasi depo penumpukan kontainer armada.</p>
    </div>

    <div class="p-6 md:p-8">
        <?php if (!empty($error)): ?>
            <!-- Tampilan Konfirmasi Gagal Validasi (Untuk Screenshot Tugas) -->
            <div class="mb-6 p-4 rounded-xl bg-rose-50 border border-rose-200 text-rose-900 flex items-start gap-3 text-xs leading-relaxed" id="alertGagalKontainer">
                <svg class="w-5 h-5 text-rose-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                <div><?= htmlspecialchars($error) ?></div>
            </div>
        <?php endif; ?>

        <form action="kontainer_form.php<?= $isEdit ? '?id='.$id : '' ?>" method="POST" id="formMasterKontainer" class="space-y-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <div>
                    <label for="nomor_kontainer" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Nomor Kode Kontainer <span class="text-rose-500">*</span></label>
                    <input type="text" name="nomor_kontainer" id="nomor_kontainer" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-slate-800 text-sm font-mono uppercase focus:outline-none focus:ring-4 focus:ring-blue-100 focus:border-blue-500 font-bold transition-all" placeholder="Cth: JLOG-U260105" value="<?= htmlspecialchars($data['nomor_kontainer']) ?>" required>
                    <span class="text-[11px] text-slate-400 mt-1 block">Format standar ISO / Penomoran Depo IDUKA.</span>
                </div>

                <div>
                    <label for="tipe_kontainer" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Tipe / Dimensi Kontainer <span class="text-rose-500">*</span></label>
                    <select name="tipe_kontainer" id="tipe_kontainer" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-slate-800 text-sm focus:outline-none focus:ring-4 focus:ring-blue-100 focus:border-blue-500 font-medium transition-all" required>
                        <option value="Dry FCL 20ft" <?= $data['tipe_kontainer'] === 'Dry FCL 20ft' ? 'selected' : '' ?>>Dry FCL 20ft (Standar General Cargo)</option>
                        <option value="Dry FCL 40ft Standar" <?= $data['tipe_kontainer'] === 'Dry FCL 40ft Standar' ? 'selected' : '' ?>>Dry FCL 40ft Standar</option>
                        <option value="Dry FCL 40ft High Cube" <?= $data['tipe_kontainer'] === 'Dry FCL 40ft High Cube' ? 'selected' : '' ?>>Dry FCL 40ft High Cube (Tinggi Ekstra)</option>
                        <option value="Reefer Container 20ft (Pendingin)" <?= $data['tipe_kontainer'] === 'Reefer Container 20ft (Pendingin)' ? 'selected' : '' ?>>Reefer Container 20ft (Cold Chain)</option>
                        <option value="LCL Cargo Container" <?= $data['tipe_kontainer'] === 'LCL Cargo Container' ? 'selected' : '' ?>>LCL Cargo Container (Konsolidasi)</option>
                    </select>
                </div>

                <div>
                    <label for="kapasitas_ton" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Kapasitas Maksimal (Ton) <span class="text-rose-500">*</span></label>
                    <input type="number" step="0.1" name="kapasitas_ton" id="kapasitas_ton" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-slate-800 text-sm focus:outline-none focus:ring-4 focus:ring-blue-100 focus:border-blue-500 font-medium transition-all" placeholder="Contoh: 21.5" value="<?= htmlspecialchars($data['kapasitas_ton']) ?>" required>
                    <span class="text-[11px] text-slate-400 mt-1 block">Beban maksimal muatan kargo dalam satuan Ton.</span>
                </div>

                <div>
                    <label for="lokasi_depo" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Lokasi Depo Penumpukan <span class="text-rose-500">*</span></label>
                    <input type="text" name="lokasi_depo" id="lokasi_depo" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-slate-800 text-sm focus:outline-none focus:ring-4 focus:ring-blue-100 focus:border-blue-500 font-medium transition-all" placeholder="Cth: Depo Tanjung Priok, Jakarta" value="<?= htmlspecialchars($data['lokasi_depo']) ?>" required>
                </div>

                <div>
                    <label for="kondisi" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Kondisi Fisik Unit</label>
                    <select name="kondisi" id="kondisi" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-slate-800 text-sm focus:outline-none focus:ring-4 focus:ring-blue-100 focus:border-blue-500 font-medium transition-all">
                        <option value="Layak Muat (Grade A)" <?= $data['kondisi'] === 'Layak Muat (Grade A)' ? 'selected' : '' ?>>Layak Muat (Grade A - Siap Pakai)</option>
                        <option value="Layak Muat (Grade B)" <?= $data['kondisi'] === 'Layak Muat (Grade B)' ? 'selected' : '' ?>>Layak Muat (Grade B - Kargo Kering)</option>
                        <option value="Perlu Pengecekan Pintu" <?= $data['kondisi'] === 'Perlu Pengecekan Pintu' ? 'selected' : '' ?>>Perlu Pengecekan Pintu / Engsel</option>
                        <option value="Perlu Perbaikan Lantai" <?= $data['kondisi'] === 'Perlu Perbaikan Lantai' ? 'selected' : '' ?>>Perlu Perbaikan Lantai</option>
                    </select>
                </div>

                <div>
                    <label for="status" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Status Ketersediaan</label>
                    <select name="status" id="status" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-slate-800 text-sm focus:outline-none focus:ring-4 focus:ring-blue-100 focus:border-blue-500 font-medium transition-all">
                        <option value="Tersedia" <?= $data['status'] === 'Tersedia' ? 'selected' : '' ?>>Tersedia (Ready di Depo)</option>
                        <option value="Sedang Digunakan" <?= $data['status'] === 'Sedang Digunakan' ? 'selected' : '' ?>>Sedang Digunakan (Loading / On Process)</option>
                        <option value="Dalam Perjalanan" <?= $data['status'] === 'Dalam Perjalanan' ? 'selected' : '' ?>>Dalam Perjalanan (Sailing / Shipping)</option>
                        <option value="Perawatan" <?= $data['status'] === 'Perawatan' ? 'selected' : '' ?>>Perawatan (Maintenance di Depo)</option>
                    </select>
                </div>
            </div>

            <div class="flex items-center justify-end gap-3 pt-6 border-t border-slate-100">
                <a href="kontainer.php" class="px-5 py-2.5 rounded-xl border border-slate-200 text-slate-600 hover:bg-slate-50 font-semibold text-xs transition-all">Batal</a>
                <button type="submit" class="inline-flex items-center gap-2 px-6 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs shadow-md shadow-blue-500/20 transition-all" id="btnSimpanKontainer">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    <span><?= $isEdit ? 'Simpan Perubahan' : 'Tambahkan Kontainer' ?></span>
                </button>
            </div>
        </form>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
