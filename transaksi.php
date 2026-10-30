<?php
/**
 * Data Transaksi Pengiriman - Tailwind CSS Clean Light Mode
 * LOGIX PRO - Sistem Manajemen Pengiriman Kargo & Kontainer
 */
$pageTitle = 'Data Transaksi Pengiriman Kargo';
require_once __DIR__ . '/includes/header.php';

// Filter Status
$filterStatus = $_GET['status'] ?? '';
$where = '';
$params = [];

if (!empty($filterStatus)) {
    $where = 'WHERE t.status_pengiriman = ?';
    $params[] = $filterStatus;
}

$sql = "
    SELECT t.*, p.nama_perusahaan, k.nomor_kontainer, k.tipe_kontainer
    FROM transaksi_pengiriman t
    JOIN pelanggan p ON t.id_pelanggan = p.id
    JOIN kontainer k ON t.id_kontainer = k.id
    {$where}
    ORDER BY t.id DESC
";
$stmt = $db->prepare($sql);
$stmt->execute($params);
$shipments = $stmt->fetchAll();

// Handle Delete Transaksi
if (isset($_GET['action']) && $_GET['action'] === 'delete' && !empty($_GET['id'])) {
    $idDelete = (int)$_GET['id'];
    try {
        $stmtDel = $db->prepare("DELETE FROM transaksi_pengiriman WHERE id = ?");
        $stmtDel->execute([$idDelete]);
        setFlash('success', 'Data transaksi pengiriman berhasil dihapus!');
    } catch (Exception $e) {
        setFlash('danger', 'Gagal menghapus transaksi: ' . $e->getMessage());
    }
    header('Location: transaksi.php');
    exit;
}
?>

<div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden">
    <div class="p-6 border-b border-slate-100 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h3 class="text-base font-bold text-slate-900">Daftar Seluruh Transaksi Pengiriman Kargo (Shipment)</h3>
            <p class="text-xs text-slate-500 mt-0.5">Rekapitulasi surat jalan resmi, status perjalanan kargo, dan rekap tagihan freight.</p>
        </div>
        <div class="flex items-center gap-3 self-start sm:self-auto">
            <!-- Filter Status -->
            <form action="transaksi.php" method="GET" class="flex items-center">
                <select name="status" class="bg-slate-50 border border-slate-200 text-slate-700 text-xs font-semibold rounded-xl px-3 py-2.5 focus:outline-none focus:ring-4 focus:ring-blue-100 focus:border-blue-500 transition-all" onchange="this.form.submit()">
                    <option value="">-- Semua Status --</option>
                    <option value="Draft / Booking" <?= $filterStatus === 'Draft / Booking' ? 'selected' : '' ?>>Draft / Booking</option>
                    <option value="Loading Kontainer" <?= $filterStatus === 'Loading Kontainer' ? 'selected' : '' ?>>Loading Kontainer</option>
                    <option value="Sailing / Dalam Perjalanan" <?= $filterStatus === 'Sailing / Dalam Perjalanan' ? 'selected' : '' ?>>Sailing / Dalam Perjalanan</option>
                    <option value="Tiba di Tujuan" <?= $filterStatus === 'Tiba di Tujuan' ? 'selected' : '' ?>>Tiba di Tujuan</option>
                    <option value="Selesai" <?= $filterStatus === 'Selesai' ? 'selected' : '' ?>>Selesai</option>
                </select>
            </form>

            <a href="transaksi_form.php" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs shadow-md shadow-blue-500/20 transition-all">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                <span>Input Booking Baru</span>
            </a>
        </div>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-left text-sm text-slate-600">
            <thead class="bg-slate-50/80 text-[11px] font-bold text-slate-500 uppercase tracking-wider border-b border-slate-100">
                <tr>
                    <th class="px-6 py-4">No. Resi</th>
                    <th class="px-6 py-4">Shipper (Perusahaan)</th>
                    <th class="px-6 py-4">Kontainer Armada</th>
                    <th class="px-6 py-4">Rute Pelabuhan</th>
                    <th class="px-6 py-4">Muatan & Berat</th>
                    <th class="px-6 py-4">Jadwal Pelayaran</th>
                    <th class="px-6 py-4">Total Biaya</th>
                    <th class="px-6 py-4">Status</th>
                    <th class="px-6 py-4 text-center">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                <?php if (empty($shipments)): ?>
                    <tr>
                        <td colspan="9" class="px-6 py-10 text-center text-slate-400">Belum ada data transaksi pengiriman yang sesuai filter.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($shipments as $row): ?>
                        <tr class="hover:bg-slate-50/80 transition-colors">
                            <td class="px-6 py-4 font-mono font-bold text-blue-700">
                                <a href="transaksi_detail.php?id=<?= $row['id'] ?>" class="hover:underline">
                                    <?= htmlspecialchars($row['no_resi']) ?>
                                </a>
                                <div class="text-[11px] text-slate-400 font-sans font-normal mt-0.5"><?= tanggalIndo($row['tanggal_booking']) ?></div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="font-bold text-slate-900"><?= htmlspecialchars($row['nama_perusahaan']) ?></div>
                            </td>
                            <td class="px-6 py-4">
                                <span class="inline-block px-2.5 py-1 rounded-md text-xs font-mono font-bold bg-slate-100 text-slate-700 border border-slate-200">
                                    <?= htmlspecialchars($row['nomor_kontainer']) ?>
                                </span>
                                <div class="text-[11px] text-slate-500 mt-1"><?= htmlspecialchars($row['tipe_kontainer']) ?></div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-xs font-medium text-slate-900"><?= htmlspecialchars($row['pelabuhan_asal']) ?></div>
                                <div class="text-[11px] text-slate-500">&darr; <?= htmlspecialchars($row['pelabuhan_tujuan']) ?></div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-slate-800 text-xs font-medium"><?= htmlspecialchars($row['jenis_muatan']) ?></div>
                                <span class="text-xs text-emerald-600 font-bold"><?= number_format($row['berat_muatan_ton'], 2) ?> Ton</span>
                            </td>
                            <td class="px-6 py-4 text-xs text-slate-600">
                                <div>Muat: <?= date('d/m/y', strtotime($row['tanggal_muat'])) ?></div>
                                <div class="text-slate-400">ETA: <?= date('d/m/y', strtotime($row['estimasi_tiba'])) ?></div>
                            </td>
                            <td class="px-6 py-4 font-bold text-slate-900">
                                <?= rupiah($row['total_biaya']) ?>
                            </td>
                            <td class="px-6 py-4">
                                <?php
                                $badgeClass = 'bg-blue-50 text-blue-700 border-blue-200';
                                if ($row['status_pengiriman'] === 'Selesai' || $row['status_pengiriman'] === 'Tiba di Tujuan') {
                                    $badgeClass = 'bg-emerald-50 text-emerald-700 border-emerald-200';
                                } elseif ($row['status_pengiriman'] === 'Loading Kontainer') {
                                    $badgeClass = 'bg-amber-50 text-amber-700 border-amber-200';
                                }
                                ?>
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold border <?= $badgeClass ?>">
                                    <?= htmlspecialchars($row['status_pengiriman']) ?>
                                </span>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <div class="inline-flex items-center gap-1.5">
                                    <a href="transaksi_detail.php?id=<?= $row['id'] ?>" class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg text-xs font-semibold bg-white border border-slate-200 text-slate-700 hover:bg-slate-50 shadow-sm transition-all" title="Detail & Cetak Surat Jalan">
                                        <svg class="w-3.5 h-3.5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                        <span>Detail</span>
                                    </a>
                                    <button type="button" class="inline-flex items-center gap-1 p-1.5 rounded-lg text-xs font-semibold bg-white border border-rose-200 text-rose-600 hover:bg-rose-50 shadow-sm transition-all" onclick="confirmDelete('transaksi.php?action=delete&id=<?= $row['id'] ?>', 'Resi <?= htmlspecialchars($row['no_resi']) ?>')" title="Hapus Transaksi">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
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
