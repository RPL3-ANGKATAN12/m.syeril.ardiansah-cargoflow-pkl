<?php
/**
 * Data Master Pelanggan / Shipper - Tailwind CSS Clean Light Mode
 * LOGIX PRO - Sistem Manajemen Pengiriman Kargo & Kontainer
 */
$pageTitle = 'Data Master Pelanggan (Shipper)';
require_once __DIR__ . '/includes/header.php';

// Handle Hapus Pelanggan
if (isset($_GET['action']) && $_GET['action'] === 'delete' && !empty($_GET['id'])) {
    $idDelete = (int)$_GET['id'];
    try {
        $stmtCheck = $db->prepare("SELECT COUNT(*) FROM transaksi_pengiriman WHERE id_pelanggan = ?");
        $stmtCheck->execute([$idDelete]);
        if ($stmtCheck->fetchColumn() > 0) {
            setFlash('danger', 'Pelanggan gagal dihapus! Pelanggan ini memiliki riwayat data transaksi pengiriman.');
        } else {
            $stmtDel = $db->prepare("DELETE FROM pelanggan WHERE id = ?");
            $stmtDel->execute([$idDelete]);
            setFlash('success', 'Data pelanggan berhasil dihapus dari sistem!');
        }
    } catch (Exception $e) {
        setFlash('danger', 'Gagal menghapus data pelanggan: ' . $e->getMessage());
    }
    header('Location: pelanggan.php');
    exit;
}

$stmt = $db->query("SELECT * FROM pelanggan ORDER BY id DESC");
$pelanggans = $stmt->fetchAll();
?>

<div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden">
    <div class="p-6 border-b border-slate-100 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h3 class="text-base font-bold text-slate-900">Daftar Data Master Pelanggan (Shipper Perusahaan)</h3>
            <p class="text-xs text-slate-500 mt-0.5">Data relasi perusahaan pengirim kargo dan narahubung operasional.</p>
        </div>
        <a href="pelanggan_form.php" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs shadow-md shadow-blue-500/20 transition-all self-start sm:self-auto">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            <span>Tambah Pelanggan Baru</span>
        </a>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-left text-sm text-slate-600">
            <thead class="bg-slate-50/80 text-[11px] font-bold text-slate-500 uppercase tracking-wider border-b border-slate-100">
                <tr>
                    <th class="px-6 py-4">No.</th>
                    <th class="px-6 py-4">Kode Shipper</th>
                    <th class="px-6 py-4">Nama Perusahaan</th>
                    <th class="px-6 py-4">Penanggung Jawab (PIC)</th>
                    <th class="px-6 py-4">No. Telepon / WA</th>
                    <th class="px-6 py-4">Email</th>
                    <th class="px-6 py-4">Alamat Gudang / Pabrik</th>
                    <th class="px-6 py-4 text-center">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                <?php if (empty($pelanggans)): ?>
                    <tr>
                        <td colspan="8" class="px-6 py-10 text-center text-slate-400">Belum ada data pelanggan. Silakan klik tombol "Tambah Pelanggan Baru".</td>
                    </tr>
                <?php else: ?>
                    <?php $no = 1; foreach ($pelanggans as $row): ?>
                        <tr class="hover:bg-slate-50/80 transition-colors">
                            <td class="px-6 py-4 text-xs text-slate-400 font-bold"><?= $no++ ?></td>
                            <td class="px-6 py-4 font-mono font-bold text-blue-700 text-xs">
                                <?= htmlspecialchars($row['kode_pelanggan']) ?>
                            </td>
                            <td class="px-6 py-4 font-bold text-slate-900">
                                <?= htmlspecialchars($row['nama_perusahaan']) ?>
                            </td>
                            <td class="px-6 py-4 text-slate-700 font-medium">
                                <?= htmlspecialchars($row['penanggung_jawab']) ?>
                            </td>
                            <td class="px-6 py-4 font-mono text-xs text-slate-600">
                                <?= htmlspecialchars($row['no_telp']) ?>
                            </td>
                            <td class="px-6 py-4 text-xs">
                                <a href="mailto:<?= htmlspecialchars($row['email']) ?>" class="text-blue-600 hover:underline"><?= htmlspecialchars($row['email']) ?></a>
                            </td>
                            <td class="px-6 py-4 text-xs text-slate-500 max-w-xs">
                                <?= htmlspecialchars($row['alamat']) ?>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <div class="inline-flex items-center gap-1.5">
                                    <a href="pelanggan_form.php?id=<?= $row['id'] ?>" class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg text-xs font-semibold bg-white border border-slate-200 text-slate-700 hover:bg-slate-50 shadow-sm transition-all" title="Edit Data Pelanggan">
                                        <svg class="w-3.5 h-3.5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                        <span>Edit</span>
                                    </a>
                                    <button type="button" class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg text-xs font-semibold bg-white border border-rose-200 text-rose-600 hover:bg-rose-50 shadow-sm transition-all" onclick="confirmDelete('pelanggan.php?action=delete&id=<?= $row['id'] ?>', '<?= htmlspecialchars($row['nama_perusahaan']) ?>')" title="Hapus Pelanggan">
                                        <svg class="w-3.5 h-3.5 text-rose-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                        <span>Hapus</span>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
