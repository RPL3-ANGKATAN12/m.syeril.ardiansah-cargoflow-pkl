<?php
/**
 * Data Master Kontainer - Tailwind CSS Clean Light Mode
 * LOGIX PRO - Sistem Manajemen Pengiriman Kargo & Kontainer
 */
$pageTitle = 'Data Master Kontainer Armada';
require_once __DIR__ . '/includes/header.php';

// Handle Hapus Kontainer
if (isset($_GET['action']) && $_GET['action'] === 'delete' && !empty($_GET['id'])) {
    $idDelete = (int)$_GET['id'];
    try {
        $stmtCheck = $db->prepare("SELECT COUNT(*) FROM transaksi_pengiriman WHERE id_kontainer = ?");
        $stmtCheck->execute([$idDelete]);
        if ($stmtCheck->fetchColumn() > 0) {
            setFlash('danger', 'Kontainer gagal dihapus! Kontainer ini masih terikat dengan data transaksi pengiriman.');
        } else {
            $stmtDel = $db->prepare("DELETE FROM kontainer WHERE id = ?");
            $stmtDel->execute([$idDelete]);
            setFlash('success', 'Data kontainer berhasil dihapus dari sistem!');
        }
    } catch (Exception $e) {
        setFlash('danger', 'Gagal menghapus kontainer: ' . $e->getMessage());
    }
    header('Location: kontainer.php');
    exit;
}

$stmt = $db->query("SELECT * FROM kontainer ORDER BY id DESC");
$kontainers = $stmt->fetchAll();
?>

<div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden">
    <div class="p-6 border-b border-slate-100 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h3 class="text-base font-bold text-slate-900">Daftar Data Master Kontainer</h3>
            <p class="text-xs text-slate-500 mt-0.5">Data seluruh unit kontainer (Dry FCL, LCL, Reefer) dan status depo penumpukan.</p>
        </div>
        <a href="kontainer_form.php" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs shadow-md shadow-blue-500/20 transition-all self-start sm:self-auto">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            <span>Tambah Kontainer Baru</span>
        </a>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-left text-sm text-slate-600">
            <thead class="bg-slate-50/80 text-[11px] font-bold text-slate-500 uppercase tracking-wider border-b border-slate-100">
                <tr>
                    <th class="px-6 py-4">No.</th>
                    <th class="px-6 py-4">Nomor Kontainer</th>
                    <th class="px-6 py-4">Tipe Kontainer</th>
                    <th class="px-6 py-4">Kapasitas (Ton)</th>
                    <th class="px-6 py-4">Lokasi Depo</th>
                    <th class="px-6 py-4">Kondisi Fisik</th>
                    <th class="px-6 py-4">Status</th>
                    <th class="px-6 py-4 text-center">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                <?php if (empty($kontainers)): ?>
                    <tr>
                        <td colspan="8" class="px-6 py-10 text-center text-slate-400">Belum ada data master kontainer. Silakan klik tombol "Tambah Kontainer Baru".</td>
                    </tr>
                <?php else: ?>
                    <?php $no = 1; foreach ($kontainers as $row): ?>
                        <tr class="hover:bg-slate-50/80 transition-colors">
                            <td class="px-6 py-4 text-xs text-slate-400 font-bold"><?= $no++ ?></td>
                            <td class="px-6 py-4 font-mono font-bold text-blue-700 text-sm">
                                <?= htmlspecialchars($row['nomor_kontainer']) ?>
                            </td>
                            <td class="px-6 py-4 font-bold text-slate-800">
                                <?= htmlspecialchars($row['tipe_kontainer']) ?>
                            </td>
                            <td class="px-6 py-4 text-slate-700 font-semibold">
                                <?= number_format($row['kapasitas_ton'], 2) ?> Ton
                            </td>
                            <td class="px-6 py-4 text-slate-600 text-xs">
                                <?= htmlspecialchars($row['lokasi_depo']) ?>
                            </td>
                            <td class="px-6 py-4 text-xs font-medium text-slate-500">
                                <?= htmlspecialchars($row['kondisi']) ?>
                            </td>
                            <td class="px-6 py-4">
                                <?php
                                $badgeClass = 'bg-emerald-50 text-emerald-700 border-emerald-200';
                                if ($row['status'] === 'Sedang Digunakan' || $row['status'] === 'Dalam Perjalanan') {
                                    $badgeClass = 'bg-amber-50 text-amber-700 border-amber-200';
                                } elseif ($row['status'] === 'Perawatan') {
                                    $badgeClass = 'bg-rose-50 text-rose-700 border-rose-200';
                                }
                                ?>
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold border <?= $badgeClass ?>">
                                    <?= htmlspecialchars($row['status']) ?>
                                </span>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <div class="inline-flex items-center gap-1.5">
                                    <a href="kontainer_form.php?id=<?= $row['id'] ?>" class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg text-xs font-semibold bg-white border border-slate-200 text-slate-700 hover:bg-slate-50 shadow-sm transition-all" title="Edit Data Kontainer">
                                        <svg class="w-3.5 h-3.5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                        <span>Edit</span>
                                    </a>
                                    <button type="button" class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg text-xs font-semibold bg-white border border-rose-200 text-rose-600 hover:bg-rose-50 shadow-sm transition-all" onclick="confirmDelete('kontainer.php?action=delete&id=<?= $row['id'] ?>', '<?= htmlspecialchars($row['nomor_kontainer']) ?>')" title="Hapus Kontainer">
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
