<?php
/**
 * Form Tambah / Edit Data Master Pelanggan - Tailwind CSS Clean Light Mode
 * LOGIX PRO - Sistem Manajemen Pengiriman Kargo & Kontainer
 */
require_once __DIR__ . '/includes/header.php';

$isEdit = false;
$id = (int)($_GET['id'] ?? 0);
$error = '';

$data = [
    'kode_pelanggan' => generateKodePelanggan($db),
    'nama_perusahaan' => '',
    'penanggung_jawab' => '',
    'no_telp' => '',
    'email' => '',
    'alamat' => ''
];

if ($id > 0) {
    $stmt = $db->prepare("SELECT * FROM pelanggan WHERE id = ?");
    $stmt->execute([$id]);
    $existing = $stmt->fetch();
    if ($existing) {
        $isEdit = true;
        $data = $existing;
    } else {
        setFlash('danger', 'Data pelanggan tidak ditemukan!');
        header('Location: pelanggan.php');
        exit;
    }
}

$pageTitle = $isEdit ? 'Edit Data Master Pelanggan' : 'Tambah Data Master Pelanggan Baru';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $kode_pelanggan = trim($_POST['kode_pelanggan'] ?? '');
    $nama_perusahaan = trim($_POST['nama_perusahaan'] ?? '');
    $penanggung_jawab = trim($_POST['penanggung_jawab'] ?? '');
    $no_telp = trim($_POST['no_telp'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $alamat = trim($_POST['alamat'] ?? '');

    if (empty($kode_pelanggan) || empty($nama_perusahaan) || empty($no_telp)) {
        $error = 'Validasi Gagal! Kode pelanggan, nama perusahaan, dan nomor telepon wajib diisi.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Validasi Gagal! Format alamat email pelanggan tidak valid.';
    } else {
        $stmtCheck = $db->prepare("SELECT id FROM pelanggan WHERE kode_pelanggan = ? AND id != ?");
        $stmtCheck->execute([$kode_pelanggan, $id]);
        if ($stmtCheck->fetch()) {
            $error = "Validasi Gagal! Kode pelanggan '{$kode_pelanggan}' sudah digunakan.";
        } else {
            try {
                if ($isEdit) {
                    $stmtUpdate = $db->prepare("
                        UPDATE pelanggan 
                        SET kode_pelanggan = ?, nama_perusahaan = ?, penanggung_jawab = ?, no_telp = ?, email = ?, alamat = ?
                        WHERE id = ?
                    ");
                    $stmtUpdate->execute([$kode_pelanggan, $nama_perusahaan, $penanggung_jawab, $no_telp, $email, $alamat, $id]);
                    setFlash('success', "Konfirmasi Berhasil: Data pelanggan {$nama_perusahaan} berhasil diperbarui!");
                } else {
                    $stmtInsert = $db->prepare("
                        INSERT INTO pelanggan (kode_pelanggan, nama_perusahaan, penanggung_jawab, no_telp, email, alamat)
                        VALUES (?, ?, ?, ?, ?, ?)
                    ");
                    $stmtInsert->execute([$kode_pelanggan, $nama_perusahaan, $penanggung_jawab, $no_telp, $email, $alamat]);
                    setFlash('success', "Konfirmasi Berhasil: Data master pelanggan {$nama_perusahaan} berhasil disimpan!");
                }
                header('Location: pelanggan.php');
                exit;
            } catch (Exception $e) {
                $error = 'Terjadi kesalahan sistem saat menyimpan data pelanggan: ' . $e->getMessage();
            }
        }
    }

    $data = [
        'kode_pelanggan' => $kode_pelanggan,
        'nama_perusahaan' => $nama_perusahaan,
        'penanggung_jawab' => $penanggung_jawab,
        'no_telp' => $no_telp,
        'email' => $email,
        'alamat' => $alamat
    ];
}
?>

<div class="mb-6">
    <a href="pelanggan.php" class="inline-flex items-center gap-1.5 text-xs font-semibold text-slate-500 hover:text-slate-800 transition-colors">
        <span>&larr;</span>
        <span>Kembali ke Daftar Pelanggan</span>
    </a>
</div>

<div class="max-w-3xl mx-auto bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden">
    <div class="p-6 md:p-8 border-b border-slate-100">
        <h3 class="text-lg font-bold text-slate-900"><?= $isEdit ? 'Formulir Edit Data Pelanggan' : 'Formulir Input Data Pelanggan Baru' ?></h3>
        <p class="text-xs text-slate-500 mt-1">Isi profil perusahaan pengirim kargo dan narahubung operasional.</p>
    </div>

    <div class="p-6 md:p-8">
        <?php if (!empty($error)): ?>
            <!-- Tampilan Konfirmasi Gagal Validasi (Untuk Screenshot Tugas) -->
            <div class="mb-6 p-4 rounded-xl bg-rose-50 border border-rose-200 text-rose-900 flex items-start gap-3 text-xs leading-relaxed" id="alertGagalPelanggan">
                <svg class="w-5 h-5 text-rose-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                <div><?= htmlspecialchars($error) ?></div>
            </div>
        <?php endif; ?>

        <form action="pelanggan_form.php<?= $isEdit ? '?id='.$id : '' ?>" method="POST" id="formMasterPelanggan" class="space-y-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <div>
                    <label for="kode_pelanggan" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Kode Pelanggan (ID Shipper) <span class="text-rose-500">*</span></label>
                    <input type="text" name="kode_pelanggan" id="kode_pelanggan" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-slate-800 text-sm font-mono font-bold focus:outline-none focus:ring-4 focus:ring-blue-100 focus:border-blue-500 transition-all" value="<?= htmlspecialchars($data['kode_pelanggan']) ?>" required>
                    <span class="text-[11px] text-slate-400 mt-1 block">Kode otomatis identitas mitra kargo IDUKA.</span>
                </div>

                <div>
                    <label for="nama_perusahaan" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Nama Perusahaan / PT / CV <span class="text-rose-500">*</span></label>
                    <input type="text" name="nama_perusahaan" id="nama_perusahaan" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-slate-800 text-sm focus:outline-none focus:ring-4 focus:ring-blue-100 focus:border-blue-500 font-medium transition-all" placeholder="Cth: PT Samudera Perkasa" value="<?= htmlspecialchars($data['nama_perusahaan']) ?>" required>
                </div>

                <div>
                    <label for="penanggung_jawab" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Nama PIC / Penanggung Jawab</label>
                    <input type="text" name="penanggung_jawab" id="penanggung_jawab" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-slate-800 text-sm focus:outline-none focus:ring-4 focus:ring-blue-100 focus:border-blue-500 font-medium transition-all" placeholder="Cth: Bambang Santoso" value="<?= htmlspecialchars($data['penanggung_jawab']) ?>">
                </div>

                <div>
                    <label for="no_telp" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Nomor Telepon / WhatsApp <span class="text-rose-500">*</span></label>
                    <input type="text" name="no_telp" id="no_telp" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-slate-800 text-sm focus:outline-none focus:ring-4 focus:ring-blue-100 focus:border-blue-500 font-medium transition-all" placeholder="Cth: 081299887766" value="<?= htmlspecialchars($data['no_telp']) ?>" required>
                </div>

                <div class="md:col-span-2">
                    <label for="email" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Alamat Email Operasional <span class="text-rose-500">*</span></label>
                    <input type="email" name="email" id="email" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-slate-800 text-sm focus:outline-none focus:ring-4 focus:ring-blue-100 focus:border-blue-500 font-medium transition-all" placeholder="Cth: logistik@perusahaan.com" value="<?= htmlspecialchars($data['email']) ?>" required>
                </div>

                <div class="md:col-span-2">
                    <label for="alamat" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Alamat Lengkap Gudang / Kantor Pabrik</label>
                    <textarea name="alamat" id="alamat" rows="3" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-slate-800 text-sm focus:outline-none focus:ring-4 focus:ring-blue-100 focus:border-blue-500 font-medium transition-all" placeholder="Masukkan alamat lengkap penjemputan barang kargo"><?= htmlspecialchars($data['alamat']) ?></textarea>
                </div>
            </div>

            <div class="flex items-center justify-end gap-3 pt-6 border-t border-slate-100">
                <a href="pelanggan.php" class="px-5 py-2.5 rounded-xl border border-slate-200 text-slate-600 hover:bg-slate-50 font-semibold text-xs transition-all">Batal</a>
                <button type="submit" class="inline-flex items-center gap-2 px-6 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs shadow-md shadow-blue-500/20 transition-all" id="btnSimpanPelanggan">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    <span><?= $isEdit ? 'Simpan Perubahan' : 'Tambahkan Pelanggan' ?></span>
                </button>
            </div>
        </form>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
