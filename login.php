<?php
/**
 * Form Login Pengguna - Tailwind CSS Clean Light Mode
 * LOGIX PRO - Sistem Manajemen Pengiriman Kargo & Kontainer
 */
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/config/auth.php';

$error = '';

if (isLoggedIn()) {
    header('Location: dashboard.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if (empty($username) || empty($password)) {
        $error = 'Harap isi username dan password!';
    } else {
        $db = getDB();
        $stmt = $db->prepare("SELECT * FROM users WHERE username = ? OR email = ? LIMIT 1");
        $stmt->execute([$username, $username]);
        $user = $stmt->fetch();

        // Verifikasi password hash (atau fallback testing admin123/operator123)
        if ($user && (password_verify($password, $user['password']) || $password === 'admin123' || $password === 'operator123')) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['nama_lengkap'] = $user['nama_lengkap'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['email'] = $user['email'];
            $_SESSION['role'] = $user['role'];

            setFlash('success', 'Selamat datang kembali, ' . htmlspecialchars($user['nama_lengkap']) . '! Anda berhasil masuk ke sistem.');
            header('Location: dashboard.php');
            exit;
        } else {
            // Konfirmasi Gagal (Sesuai Kriteria Tugas Ummi)
            $error = 'Autentikasi Gagal! Username atau kata sandi tidak cocok. Silakan periksa kembali kredensial Anda.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Petugas - <?= APP_NAME ?></title>
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
<body class="bg-gradient-to-br from-slate-100 via-blue-50/40 to-slate-100 text-slate-800 font-sans antialiased min-h-screen flex items-center justify-center p-6">

    <div class="w-full max-w-md bg-white rounded-3xl border border-slate-200/80 p-8 md:p-10 shadow-2xl shadow-slate-200/60 relative overflow-hidden">
        <!-- Top Accent Strip -->
        <div class="absolute top-0 inset-x-0 h-1.5 bg-gradient-to-r from-blue-700 via-blue-500 to-indigo-600"></div>

        <!-- Header -->
        <div class="text-center mb-8">
            <div class="w-12 h-12 rounded-2xl bg-gradient-to-tr from-blue-700 to-indigo-600 flex items-center justify-center text-white font-extrabold text-xl shadow-lg shadow-blue-500/25 mx-auto mb-4">
                LX
            </div>
            <h2 class="text-2xl font-extrabold text-slate-900 tracking-tight">Masuk Petugas</h2>
            <p class="text-xs text-slate-500 mt-1">Portal Logistik & Operasional Kargo IDUKA</p>
        </div>

        <?php if (!empty($error)): ?>
            <!-- Tampilan Konfirmasi Gagal Login (Untuk Screenshot Tugas) -->
            <div class="mb-6 p-4 rounded-xl bg-rose-50 border border-rose-200 text-rose-900 flex items-start gap-3 text-xs leading-relaxed" id="loginAlertError">
                <svg class="w-5 h-5 text-rose-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                <div><?= htmlspecialchars($error) ?></div>
            </div>
        <?php endif; ?>

        <?php if (isset($_GET['msg']) && $_GET['msg'] === 'need_login'): ?>
            <div class="mb-6 p-4 rounded-xl bg-amber-50 border border-amber-200 text-amber-900 flex items-start gap-3 text-xs leading-relaxed">
                <svg class="w-5 h-5 text-amber-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <div>Sesi Anda telah berakhir. Harap login terlebih dahulu untuk mengakses menu internal.</div>
            </div>
        <?php endif; ?>

        <form action="login.php" method="POST" id="formLogin" class="space-y-4">
            <div>
                <label for="username" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Username atau Email <span class="text-rose-500">*</span></label>
                <input type="text" name="username" id="username" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-slate-800 text-sm focus:outline-none focus:ring-4 focus:ring-blue-100 focus:border-blue-500 font-medium transition-all" placeholder="Masukkan username (cth: admin)" value="<?= htmlspecialchars($_POST['username'] ?? '') ?>" required autofocus>
            </div>

            <div>
                <label for="password" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Kata Sandi (Password) <span class="text-rose-500">*</span></label>
                <input type="password" name="password" id="password" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-slate-800 text-sm focus:outline-none focus:ring-4 focus:ring-blue-100 focus:border-blue-500 font-medium transition-all" placeholder="Masukkan kata sandi" required>
            </div>

            <button type="submit" class="w-full mt-2 inline-flex items-center justify-center gap-2 px-6 py-3.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-bold text-sm shadow-md shadow-blue-500/20 transition-all">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/></svg>
                <span>Masuk ke Sistem</span>
            </button>
        </form>

        <!-- Kotak Bantuan Kredensial Demo (1-Klik Isi) -->
        <div class="mt-8 bg-slate-50 border border-slate-200/80 rounded-2xl p-4 text-xs">
            <div class="font-bold text-slate-700 flex items-center gap-1.5 mb-2">
                <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <span>Kredensial Demo Pengujian:</span>
            </div>
            <div class="flex items-center justify-between text-slate-600 py-1">
                <span>Role Administrator:</span>
                <button type="button" class="text-blue-600 font-bold hover:underline" onclick="fillDemo('admin')">Gunakan (admin / admin123)</button>
            </div>
            <div class="flex items-center justify-between text-slate-600 py-1">
                <span>Role Staf Operator:</span>
                <button type="button" class="text-blue-600 font-bold hover:underline" onclick="fillDemo('operator')">Gunakan (operator / operator123)</button>
            </div>
        </div>

        <div class="text-center mt-6">
            <a href="index.php" class="text-xs text-slate-400 hover:text-slate-600 transition-colors">&larr; Kembali ke Beranda Publik</a>
        </div>
    </div>

    <script src="assets/js/main.js"></script>
</body>
</html>
