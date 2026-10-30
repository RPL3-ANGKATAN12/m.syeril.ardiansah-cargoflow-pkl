<?php
/**
 * Beranda Publik & Tracking Pengiriman - Tailwind CSS Clean Light Mode
 * LOGIX PRO - Sistem Manajemen Pengiriman Kargo & Kontainer
 */
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/config/auth.php';

$db = getDB();
$trackingResult = null;
$searchedResi = '';

if (!empty($_GET['resi'])) {
    $searchedResi = trim($_GET['resi']);
    $stmt = $db->prepare("
        SELECT t.*, p.nama_perusahaan, k.nomor_kontainer, k.tipe_kontainer 
        FROM transaksi_pengiriman t
        JOIN pelanggan p ON t.id_pelanggan = p.id
        JOIN kontainer k ON t.id_kontainer = k.id
        WHERE t.no_resi = ?
    ");
    $stmt->execute([$searchedResi]);
    $trackingResult = $stmt->fetch();

    if ($trackingResult) {
        $stmtHistory = $db->prepare("SELECT * FROM riwayat_status WHERE id_transaksi = ? ORDER BY waktu DESC");
        $stmtHistory->execute([$trackingResult['id']]);
        $trackingResult['history'] = $stmtHistory->fetchAll();
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= APP_NAME ?> - Beranda Sistem Pengiriman Logistik & Kargo IDUKA</title>
    <!-- Google Font: Plus Jakarta Sans -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <!-- Tailwind CSS v3 via CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['"Plus Jakarta Sans"', 'system-ui', 'sans-serif'],
                    }
                }
            }
        }
    </script>
    <link rel="icon" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='%232563eb'><path d='M3 3h18v18H3V3zm16 16V5H5v14h14zM7 7h10v2H7V7zm0 4h10v2H7v-2zm0 4h7v2H7v-2z'/></svg>">
</head>
<body class="bg-slate-50 text-slate-800 font-sans antialiased min-h-screen flex flex-col justify-between">

    <!-- Public Navigation Bar -->
    <nav class="h-20 bg-white/90 backdrop-blur-md border-b border-slate-200/80 sticky top-0 z-50 px-6 md:px-12 flex items-center justify-between">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-blue-700 to-indigo-600 flex items-center justify-center text-white font-extrabold text-lg shadow-md shadow-blue-500/20">
                LX
            </div>
            <div>
                <strong class="text-lg font-bold text-slate-900 tracking-tight"><?= APP_NAME ?></strong>
                <div class="text-[10px] text-blue-600 uppercase font-bold tracking-wider">Freight Forwarding IDUKA</div>
            </div>
        </div>

        <div>
            <?php if (isLoggedIn()): ?>
                <a href="dashboard.php" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-semibold text-sm shadow-md shadow-blue-500/20 transition-all">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                    <span>Masuk Dashboard</span>
                </a>
            <?php else: ?>
                <a href="login.php" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-semibold text-sm shadow-md shadow-blue-500/20 transition-all">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/></svg>
                    <span>Login Petugas / Admin</span>
                </a>
            <?php endif; ?>
        </div>
    </nav>

    <!-- Hero Header -->
    <header class="py-16 md:py-20 px-6 text-center max-w-4xl mx-auto">
        <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-blue-50 border border-blue-200/80 text-blue-700 text-xs font-bold uppercase tracking-wider mb-6">
            <span class="w-2 h-2 rounded-full bg-blue-600 animate-pulse"></span>
            Studi Kasus IDUKA PKL: J3LOG & Indobox Cargo
        </div>

        <h1 class="text-4xl md:text-5xl font-extrabold text-slate-900 leading-tight mb-4 tracking-tight">
            Ekosistem Pengiriman Logistik &amp; Manajemen Kontainer
        </h1>

        <p class="text-base md:text-lg text-slate-600 leading-relaxed mb-10 max-w-2xl mx-auto">
            Aplikasi pengelolaan operasional kargo terpadu: CRUD Data Master Kontainer, Data Pelanggan Shipper, serta Manajemen Transaksi Pengiriman Antar-Pelabuhan.
        </p>

        <!-- Tracking Form Box -->
        <div class="bg-white rounded-2xl border border-slate-200/80 p-6 md:p-8 shadow-xl shadow-slate-200/50 max-w-2xl mx-auto text-left mb-10">
            <div class="text-xs font-bold uppercase tracking-wider text-blue-600 mb-2">
                Cek Status Resi Pengiriman Real-Time
            </div>
            <form action="index.php" method="GET" class="flex flex-col sm:flex-row gap-3">
                <input type="text" name="resi" class="flex-1 bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-slate-800 placeholder-slate-400 text-sm focus:outline-none focus:ring-4 focus:ring-blue-100 focus:border-blue-500 font-medium transition-all" placeholder="Contoh: J3LOG-2610-0001" value="<?= htmlspecialchars($searchedResi) ?>" required>
                <button type="submit" class="inline-flex items-center justify-center gap-2 px-6 py-3 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-bold text-sm shadow-md shadow-blue-500/20 transition-all">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    <span>Lacak Kargo</span>
                </button>
            </form>
            <div class="text-xs text-slate-400 mt-3">
                Resi demo tersedia: 
                <a href="index.php?resi=J3LOG-2610-0001" class="text-blue-600 font-semibold hover:underline">J3LOG-2610-0001</a> atau 
                <a href="index.php?resi=J3LOG-2610-0002" class="text-blue-600 font-semibold hover:underline">J3LOG-2610-0002</a>
            </div>
        </div>

        <!-- Tracking Result Card -->
        <?php if (!empty($searchedResi)): ?>
            <?php if ($trackingResult): ?>
                <div class="bg-white rounded-2xl border border-slate-200/80 shadow-lg p-6 md:p-8 max-w-2xl mx-auto text-left mb-12">
                    <div class="flex items-center justify-between border-b border-slate-100 pb-4 mb-6">
                        <div>
                            <span class="text-xs font-bold text-blue-600 uppercase tracking-wider">Hasil Pelacakan</span>
                            <h3 class="text-xl font-extrabold text-slate-900 font-mono mt-0.5"><?= htmlspecialchars($trackingResult['no_resi']) ?></h3>
                        </div>
                        <span class="inline-flex items-center px-3 py-1.5 rounded-full text-xs font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                            <?= htmlspecialchars($trackingResult['status_pengiriman']) ?>
                        </span>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm mb-6">
                        <div>
                            <span class="text-xs text-slate-400 uppercase font-semibold">Pengirim / Shipper:</span>
                            <div class="font-bold text-slate-900"><?= htmlspecialchars($trackingResult['nama_perusahaan']) ?></div>
                        </div>
                        <div>
                            <span class="text-xs text-slate-400 uppercase font-semibold">Kontainer:</span>
                            <div class="font-bold text-slate-900"><?= htmlspecialchars($trackingResult['nomor_kontainer']) ?> (<?= htmlspecialchars($trackingResult['tipe_kontainer']) ?>)</div>
                        </div>
                        <div>
                            <span class="text-xs text-slate-400 uppercase font-semibold">Rute Pelayaran:</span>
                            <div class="text-slate-700 font-medium"><?= htmlspecialchars($trackingResult['pelabuhan_asal']) ?> &rarr; <?= htmlspecialchars($trackingResult['pelabuhan_tujuan']) ?></div>
                        </div>
                        <div>
                            <span class="text-xs text-slate-400 uppercase font-semibold">Estimasi Tiba:</span>
                            <div class="text-slate-700 font-medium"><?= tanggalIndo($trackingResult['estimasi_tiba']) ?></div>
                        </div>
                    </div>

                    <div class="border-t border-slate-100 pt-5">
                        <h4 class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-4">Riwayat Perjalanan (Tracking Timeline)</h4>
                        <?php if (!empty($trackingResult['history'])): ?>
                            <ul class="space-y-4 border-l-2 border-blue-500 pl-4 ml-2">
                                <?php foreach ($trackingResult['history'] as $h): ?>
                                    <li class="relative">
                                        <span class="absolute -left-[21px] top-1.5 w-2.5 h-2.5 rounded-full bg-blue-600 border-2 border-white"></span>
                                        <div class="text-xs text-slate-400"><?= date('d M Y H:i', strtotime($h['waktu'])) ?> - <?= htmlspecialchars($h['lokasi']) ?></div>
                                        <strong class="text-sm text-slate-900 font-bold"><?= htmlspecialchars($h['status']) ?></strong>
                                        <div class="text-xs text-slate-600 mt-0.5"><?= htmlspecialchars($h['keterangan']) ?></div>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        <?php else: ?>
                            <p class="text-xs text-slate-400">Belum ada riwayat pergerakan kargo.</p>
                        <?php endif; ?>
                    </div>
                </div>
            <?php else: ?>
                <div class="bg-rose-50 border border-rose-200 text-rose-900 rounded-2xl p-6 max-w-2xl mx-auto text-left mb-12 flex items-center gap-3">
                    <svg class="w-6 h-6 text-rose-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                    <div>
                        <strong class="font-bold">Nomor Resi Tidak Ditemukan!</strong>
                        <div class="text-sm mt-0.5">Resi "<?= htmlspecialchars($searchedResi) ?>" belum terdaftar di sistem pengiriman.</div>
                    </div>
                </div>
            <?php endif; ?>
        <?php endif; ?>
    </header>

    <!-- Showcase Features Grid -->
    <section class="max-w-6xl mx-auto px-6 mb-16 w-full">
        <h2 class="text-center text-2xl font-extrabold text-slate-900 mb-8">Struktur Fitur Sistem Projek UKK</h2>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="bg-white rounded-2xl p-6 border border-slate-200/80 shadow-sm hover:shadow-md transition-all">
                <div class="w-12 h-12 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center font-bold text-lg mb-4">01</div>
                <h3 class="text-base font-bold text-slate-900 mb-2">Data Master Kontainer</h3>
                <p class="text-xs text-slate-600 leading-relaxed">Pengelolaan armada: Dry FCL 20ft/40ft, Reefer pendingin, status ketersediaan, kapasitas tonase, dan lokasi depo penumpukan.</p>
            </div>

            <div class="bg-white rounded-2xl p-6 border border-slate-200/80 shadow-sm hover:shadow-md transition-all">
                <div class="w-12 h-12 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center font-bold text-lg mb-4">02</div>
                <h3 class="text-base font-bold text-slate-900 mb-2">Data Master Shipper</h3>
                <p class="text-xs text-slate-600 leading-relaxed">Manajemen data mitra logistik, kontak PIC operasional, nomor telepon, dan alamat gudang penjemputan barang kargo.</p>
            </div>

            <div class="bg-white rounded-2xl p-6 border border-slate-200/80 shadow-sm hover:shadow-md transition-all">
                <div class="w-12 h-12 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center font-bold text-lg mb-4">03</div>
                <h3 class="text-base font-bold text-slate-900 mb-2">Data Transaksi Pengiriman</h3>
                <p class="text-xs text-slate-600 leading-relaxed">Generator nomor surat jalan otomatis J3LOG-YYMM-XXXX, kalkulasi tarif freight, jadwal pelayaran, dan status pengiriman.</p>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="text-center py-6 border-t border-slate-200 text-slate-400 text-xs bg-white">
        &copy; <?= date('Y') ?> LOGIX PRO - Tugas Projek PKL IDUKA Kelas XII RPL Persiapan UKK.
    </footer>

</body>
</html>
