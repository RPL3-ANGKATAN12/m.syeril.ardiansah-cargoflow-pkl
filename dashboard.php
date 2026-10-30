<?php
/**
 * Beranda Dashboard Admin - Tailwind CSS Clean Light Mode
 * LOGIX PRO - Sistem Manajemen Pengiriman Kargo & Kontainer
 */
$pageTitle = 'Dashboard Operasional Logistik';
require_once __DIR__ . '/includes/header.php';

// Hitung Statistik Metrik
$totalPengiriman = $db->query("SELECT COUNT(*) FROM transaksi_pengiriman")->fetchColumn();
$totalKontainer = $db->query("SELECT COUNT(*) FROM kontainer")->fetchColumn();
$kontainerReady = $db->query("SELECT COUNT(*) FROM kontainer WHERE status = 'Tersedia'")->fetchColumn();
$totalPelanggan = $db->query("SELECT COUNT(*) FROM pelanggan")->fetchColumn();
$totalRevenue = $db->query("SELECT SUM(total_biaya) FROM transaksi_pengiriman")->fetchColumn() ?: 0;

// Ambil 5 Pengiriman Terbaru
$stmtRecent = $db->query("
    SELECT t.*, p.nama_perusahaan, k.nomor_kontainer, k.tipe_kontainer
    FROM transaksi_pengiriman t
    JOIN pelanggan p ON t.id_pelanggan = p.id
    JOIN kontainer k ON t.id_kontainer = k.id
    ORDER BY t.id DESC
    LIMIT 5
");
$recentShipments = $stmtRecent->fetchAll();
?>

<!-- Kartu Ringkasan Metrik (Stats Grid) -->
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5 mb-8">
    <!-- Stat 1: Total Pengiriman -->
    <div class="bg-white rounded-2xl p-6 border border-slate-200/80 shadow-sm hover:shadow-md transition-all flex items-start justify-between">
        <div>
            <div class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-1">Total Pengiriman</div>
            <div class="text-3xl font-extrabold text-slate-900"><?= number_format($totalPengiriman) ?></div>
            <div class="text-xs text-slate-500 mt-1">Transaksi kargo terdata</div>
        </div>
        <div class="w-12 h-12 rounded-xl bg-blue-50 border border-blue-100 text-blue-600 flex items-center justify-center">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        </div>
    </div>

    <!-- Stat 2: Armada Kontainer -->
    <div class="bg-white rounded-2xl p-6 border border-slate-200/80 shadow-sm hover:shadow-md transition-all flex items-start justify-between">
        <div>
            <div class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-1">Armada Kontainer</div>
            <div class="text-3xl font-extrabold text-slate-900"><?= number_format($totalKontainer) ?></div>
            <div class="text-xs text-emerald-600 font-semibold mt-1"><span class="font-bold"><?= $kontainerReady ?></span> unit siap muat di depo</div>
        </div>
        <div class="w-12 h-12 rounded-xl bg-emerald-50 border border-emerald-100 text-emerald-600 flex items-center justify-center">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10l8 4"/></svg>
        </div>
    </div>

    <!-- Stat 3: Mitra Shipper -->
    <div class="bg-white rounded-2xl p-6 border border-slate-200/80 shadow-sm hover:shadow-md transition-all flex items-start justify-between">
        <div>
            <div class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-1">Mitra Shipper</div>
            <div class="text-3xl font-extrabold text-slate-900"><?= number_format($totalPelanggan) ?></div>
            <div class="text-xs text-slate-500 mt-1">Perusahaan pengirim aktif</div>
        </div>
        <div class="w-12 h-12 rounded-xl bg-amber-50 border border-amber-100 text-amber-600 flex items-center justify-center">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
        </div>
    </div>

    <!-- Stat 4: Nilai Total Kargo -->
    <div class="bg-white rounded-2xl p-6 border border-slate-200/80 shadow-sm hover:shadow-md transition-all flex items-start justify-between">
        <div>
            <div class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-1">Total Nilai Kargo</div>
            <div class="text-2xl font-extrabold text-blue-700"><?= rupiah($totalRevenue) ?></div>
            <div class="text-xs text-slate-500 mt-1">Akumulasi tarif & handling</div>
        </div>
        <div class="w-12 h-12 rounded-xl bg-indigo-50 border border-indigo-100 text-indigo-600 flex items-center justify-center">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        </div>
    </div>
</div>

<!-- Aksi Cepat Operasional -->
<div class="bg-gradient-to-r from-blue-700 via-blue-600 to-indigo-700 rounded-2xl p-6 text-white shadow-md shadow-blue-500/10 mb-8 flex flex-col md:flex-row items-center justify-between gap-5">
    <div>
        <h3 class="text-lg font-bold">Operasional IDUKA Hari Ini</h3>
        <p class="text-sm text-blue-100 mt-1">Kelola pesanan freight kargo atau tambah master data armada & pelanggan baru.</p>
    </div>
    <div class="flex flex-wrap gap-3">
        <a href="transaksi_form.php" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-white text-blue-700 font-bold text-sm shadow hover:bg-blue-50 transition-all">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            <span>Input Booking Pengiriman</span>
        </a>
        <a href="kontainer_form.php" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-blue-800/60 hover:bg-blue-800 text-white border border-blue-400/40 font-semibold text-sm transition-all">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10l8 4"/></svg>
            <span>Tambah Kontainer</span>
        </a>
        <a href="pelanggan_form.php" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-blue-800/60 hover:bg-blue-800 text-white border border-blue-400/40 font-semibold text-sm transition-all">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/></svg>
            <span>Tambah Shipper</span>
        </a>
    </div>
</div>

<!-- Tabel Pengiriman Terbaru -->
<div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden">
    <div class="px-6 py-5 border-b border-slate-100 flex items-center justify-between">
        <div>
            <h3 class="text-base font-bold text-slate-900">5 Transaksi Pengiriman Terbaru</h3>
            <p class="text-xs text-slate-500 mt-0.5">Surat jalan terakhir yang diterbitkan oleh tim operasional</p>
        </div>
        <a href="transaksi.php" class="text-xs font-bold text-blue-600 hover:text-blue-800 flex items-center gap-1">
            <span>Lihat Semua Data</span>
            <span>&rarr;</span>
        </a>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-left text-sm text-slate-600">
            <thead class="bg-slate-50/80 text-[11px] font-bold text-slate-500 uppercase tracking-wider border-b border-slate-100">
                <tr>
                    <th class="px-6 py-4">No. Resi (Project Code)</th>
                    <th class="px-6 py-4">Perusahaan Shipper</th>
                    <th class="px-6 py-4">Kontainer Armada</th>
                    <th class="px-6 py-4">Rute Pelabuhan</th>
                    <th class="px-6 py-4">Estimasi Tiba</th>
                    <th class="px-6 py-4">Total Biaya</th>
                    <th class="px-6 py-4">Status</th>
                    <th class="px-6 py-4 text-center">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                <?php if (empty($recentShipments)): ?>
                    <tr>
                        <td colspan="8" class="px-6 py-10 text-center text-slate-400">Belum ada data transaksi pengiriman.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($recentShipments as $row): ?>
                        <tr class="hover:bg-slate-50/80 transition-colors">
                            <td class="px-6 py-4 font-mono font-bold text-blue-600">
                                <?= htmlspecialchars($row['no_resi']) ?>
                            </td>
                            <td class="px-6 py-4">
                                <div class="font-bold text-slate-900"><?= htmlspecialchars($row['nama_perusahaan']) ?></div>
                                <div class="text-xs text-slate-500"><?= htmlspecialchars($row['jenis_muatan']) ?> (<?= $row['berat_muatan_ton'] ?> Ton)</div>
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
                            <td class="px-6 py-4 text-xs font-medium text-slate-700">
                                <?= tanggalIndo($row['estimasi_tiba']) ?>
                            </td>
                            <td class="px-6 py-4 font-bold text-slate-900">
                                <?= rupiah($row['total_biaya']) ?>
                            </td>
                            <td class="px-6 py-4">
                                <?php
                                $badgeColor = 'bg-blue-50 text-blue-700 border-blue-200';
                                if ($row['status_pengiriman'] === 'Selesai' || $row['status_pengiriman'] === 'Tiba di Tujuan') {
                                    $badgeColor = 'bg-emerald-50 text-emerald-700 border-emerald-200';
                                } elseif ($row['status_pengiriman'] === 'Loading Kontainer') {
                                    $badgeColor = 'bg-amber-50 text-amber-700 border-amber-200';
                                }
                                ?>
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold border <?= $badgeColor ?>">
                                    <?= htmlspecialchars($row['status_pengiriman']) ?>
                                </span>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <a href="transaksi_detail.php?id=<?= $row['id'] ?>" class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg text-xs font-semibold bg-white border border-slate-200 text-slate-700 hover:bg-slate-50 shadow-sm transition-all">
                                    <svg class="w-3.5 h-3.5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                    <span>Detail</span>
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
