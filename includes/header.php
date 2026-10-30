<?php
/**
 * Header Internal Portal - Tailwind CSS Clean Light Mode
 * LOGIX PRO
 */
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/auth.php';

checkAuth();
$user = currentUser();
$db = getDB();
$driverName = $db->getAttribute(PDO::ATTR_DRIVER_NAME);
$pageTitle = $pageTitle ?? 'Dashboard Operasional';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle) ?> - <?= APP_NAME ?></title>
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
                    },
                    colors: {
                        brand: {
                            50: '#eff6ff',
                            100: '#dbeafe',
                            200: '#bfdbfe',
                            500: '#3b82f6',
                            600: '#2563eb',
                            700: '#1d4ed8',
                            800: '#1e40af',
                            900: '#1e3a8a',
                        }
                    }
                }
            }
        }
    </script>
    <link rel="icon" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='%232563eb'><path d='M3 3h18v18H3V3zm16 16V5H5v14h14zM7 7h10v2H7V7zm0 4h10v2H7v-2zm0 4h7v2H7v-2z'/></svg>">
    <style>
        @media print {
            .no-print { display: none !important; }
            body { background: white !important; }
        }
    </style>
</head>
<body class="bg-slate-50 text-slate-800 font-sans antialiased min-h-screen flex">

    <!-- Sidebar Navigation -->
    <aside id="appSidebar" class="w-64 bg-white border-r border-slate-200/80 flex flex-col fixed inset-y-0 left-0 z-50 transition-transform duration-300 md:translate-x-0 -translate-x-full">
        <!-- Logo Brand -->
        <div class="h-16 flex items-center px-6 border-b border-slate-100 gap-3">
            <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-blue-700 via-blue-600 to-indigo-600 flex items-center justify-center text-white font-extrabold text-lg shadow-md shadow-blue-500/20">
                LX
            </div>
            <div>
                <h1 class="text-base font-bold text-slate-900 leading-tight">LOGIX PRO</h1>
                <span class="text-[10px] font-bold text-blue-600 uppercase tracking-wider">Logistics PKL</span>
            </div>
        </div>

        <!-- Navigation Menu Links -->
        <div class="flex-1 overflow-y-auto py-5 px-4 space-y-6">
            <div>
                <div class="text-[11px] font-bold text-slate-400 uppercase tracking-wider px-3 mb-2">Menu Utama</div>
                <ul class="space-y-1">
                    <li>
                        <a href="dashboard.php" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-semibold transition-all <?= basename($_SERVER['PHP_SELF']) == 'dashboard.php' ? 'bg-blue-50 text-blue-700 border border-blue-200/60 shadow-sm' : 'text-slate-600 hover:bg-slate-100/70 hover:text-slate-900' ?>">
                            <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                            <span>Dashboard Beranda</span>
                        </a>
                    </li>
                </ul>
            </div>

            <div>
                <div class="text-[11px] font-bold text-slate-400 uppercase tracking-wider px-3 mb-2">Data Master</div>
                <ul class="space-y-1">
                    <li>
                        <a href="kontainer.php" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-semibold transition-all <?= in_array(basename($_SERVER['PHP_SELF']), ['kontainer.php', 'kontainer_form.php']) ? 'bg-blue-50 text-blue-700 border border-blue-200/60 shadow-sm' : 'text-slate-600 hover:bg-slate-100/70 hover:text-slate-900' ?>">
                            <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10l8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                            <span>Data Kontainer</span>
                        </a>
                    </li>
                    <li>
                        <a href="pelanggan.php" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-semibold transition-all <?= in_array(basename($_SERVER['PHP_SELF']), ['pelanggan.php', 'pelanggan_form.php']) ? 'bg-blue-50 text-blue-700 border border-blue-200/60 shadow-sm' : 'text-slate-600 hover:bg-slate-100/70 hover:text-slate-900' ?>">
                            <svg class="w-5 h-5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                            <span>Data Pelanggan</span>
                        </a>
                    </li>
                </ul>
            </div>

            <div>
                <div class="text-[11px] font-bold text-slate-400 uppercase tracking-wider px-3 mb-2">Data Transaksi</div>
                <ul class="space-y-1">
                    <li>
                        <a href="transaksi.php" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-semibold transition-all <?= in_array(basename($_SERVER['PHP_SELF']), ['transaksi.php', 'transaksi_detail.php']) ? 'bg-blue-50 text-blue-700 border border-blue-200/60 shadow-sm' : 'text-slate-600 hover:bg-slate-100/70 hover:text-slate-900' ?>">
                            <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/></svg>
                            <span>Daftar Pengiriman</span>
                        </a>
                    </li>
                    <li>
                        <a href="transaksi_form.php" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-semibold transition-all <?= basename($_SERVER['PHP_SELF']) == 'transaksi_form.php' ? 'bg-blue-50 text-blue-700 border border-blue-200/60 shadow-sm' : 'text-slate-600 hover:bg-slate-100/70 hover:text-slate-900' ?>">
                            <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v3m0 0v3m0-3h3m-3 0H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            <span>Input Booking Baru</span>
                        </a>
                    </li>
                </ul>
            </div>

            <div>
                <div class="text-[11px] font-bold text-slate-400 uppercase tracking-wider px-3 mb-2">Pengujian Tugas Ummi</div>
                <ul class="space-y-1">
                    <li>
                        <a href="konfirmasi_berhasil.php" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-semibold transition-all <?= basename($_SERVER['PHP_SELF']) == 'konfirmasi_berhasil.php' ? 'bg-emerald-50 text-emerald-800 border border-emerald-200/80 shadow-sm' : 'text-slate-600 hover:bg-slate-100/70 hover:text-slate-900' ?>">
                            <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            <span>Showcase SS Berhasil</span>
                        </a>
                    </li>
                    <li>
                        <a href="konfirmasi_gagal.php" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-semibold transition-all <?= basename($_SERVER['PHP_SELF']) == 'konfirmasi_gagal.php' ? 'bg-rose-50 text-rose-800 border border-rose-200/80 shadow-sm' : 'text-slate-600 hover:bg-slate-100/70 hover:text-slate-900' ?>">
                            <svg class="w-5 h-5 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            <span>Showcase SS Gagal</span>
                        </a>
                    </li>
                    <li>
                        <a href="profil.php" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-semibold transition-all <?= basename($_SERVER['PHP_SELF']) == 'profil.php' ? 'bg-blue-50 text-blue-700 border border-blue-200/60 shadow-sm' : 'text-slate-600 hover:bg-slate-100/70 hover:text-slate-900' ?>">
                            <svg class="w-5 h-5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                            <span>Form Profil (Lainnya)</span>
                        </a>
                    </li>
                </ul>
            </div>
        </div>

        <!-- Sidebar Footer User Card -->
        <div class="p-4 border-t border-slate-100 bg-slate-50/60">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-3 min-w-0">
                    <div class="w-9 h-9 rounded-full bg-gradient-to-tr from-blue-600 to-indigo-600 text-white font-bold flex items-center justify-center text-sm shadow-sm flex-shrink-0">
                        <?= strtoupper(substr($user['nama_lengkap'], 0, 1)) ?>
                    </div>
                    <div class="min-w-0">
                        <div class="text-xs font-bold text-slate-800 truncate"><?= htmlspecialchars($user['nama_lengkap']) ?></div>
                        <div class="text-[11px] text-slate-500 capitalize"><?= htmlspecialchars($user['role']) ?></div>
                    </div>
                </div>
                <a href="logout.php" title="Keluar" class="text-slate-400 hover:text-rose-600 p-1.5 rounded-lg hover:bg-rose-50 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                </a>
            </div>
        </div>
    </aside>

    <!-- Main Wrapper -->
    <div class="flex-1 md:ml-64 flex flex-col min-w-0">
        <!-- Top Sticky Header -->
        <header class="h-16 bg-white/95 backdrop-blur-md border-b border-slate-200/80 sticky top-0 z-40 flex items-center justify-between px-6 md:px-8">
            <div class="flex items-center gap-3">
                <button type="button" id="menuToggle" class="md:hidden text-slate-600 p-1.5 hover:bg-slate-100 rounded-lg">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                </button>
                <div>
                    <h2 class="text-base font-bold text-slate-900"><?= htmlspecialchars($pageTitle) ?></h2>
                    <p class="text-xs text-slate-500 hidden sm:block"><?= APP_COMPANY ?></p>
                </div>
            </div>

            <div class="flex items-center gap-3">
                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200/80">
                    <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                    <?= strtoupper($driverName) ?> Engine Ready
                </span>
                <a href="index.php" target="_blank" class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold text-slate-700 bg-white border border-slate-200 hover:bg-slate-50 rounded-lg shadow-sm transition-all">
                    <svg class="w-3.5 h-3.5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                    <span class="hidden sm:inline">Web Publik</span>
                </a>
            </div>
        </header>

        <!-- Content Body -->
        <main class="flex-1 p-6 md:p-8 max-w-7xl w-full mx-auto">
            <?php $flash = getFlash(); if ($flash): ?>
                <?php if ($flash['type'] === 'success'): ?>
                    <div class="mb-6 p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-900 flex items-center gap-3 text-sm shadow-sm">
                        <svg class="w-5 h-5 text-emerald-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <div class="font-medium"><?= htmlspecialchars($flash['message']) ?></div>
                    </div>
                <?php else: ?>
                    <div class="mb-6 p-4 rounded-xl bg-rose-50 border border-rose-200 text-rose-900 flex items-center gap-3 text-sm shadow-sm">
                        <svg class="w-5 h-5 text-rose-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                        <div class="font-medium"><?= htmlspecialchars($flash['message']) ?></div>
                    </div>
                <?php endif; ?>
            <?php endif; ?>
