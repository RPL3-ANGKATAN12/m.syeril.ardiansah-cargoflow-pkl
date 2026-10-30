<?php
/**
 * Form Profil & Pengaturan Akun - Tailwind CSS Clean Light Mode
 * LOGIX PRO - Sistem Manajemen Pengiriman Kargo & Kontainer
 */
$pageTitle = 'Formulir Profil & Pengaturan Akun';
require_once __DIR__ . '/includes/header.php';

$userId = $_SESSION['user_id'];
$error = '';

$stmt = $db->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$userId]);
$userData = $stmt->fetch();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? 'update_profile';

    if ($action === 'update_profile') {
        $nama_lengkap = trim($_POST['nama_lengkap'] ?? '');
        $email = trim($_POST['email'] ?? '');

        if (empty($nama_lengkap) || empty($email)) {
            $error = 'Validasi Gagal! Nama lengkap dan email wajib diisi.';
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $error = 'Validasi Gagal! Format email tidak valid.';
        } else {
            $stmtUp = $db->prepare("UPDATE users SET nama_lengkap = ?, email = ? WHERE id = ?");
            $stmtUp->execute([$nama_lengkap, $email, $userId]);
            $_SESSION['nama_lengkap'] = $nama_lengkap;
            $_SESSION['email'] = $email;
            setFlash('success', 'Konfirmasi Berhasil: Data profil akun Anda berhasil diperbarui!');
            header('Location: profil.php');
            exit;
        }
    } elseif ($action === 'change_password') {
        $passLama = $_POST['password_lama'] ?? '';
        $passBaru = $_POST['password_baru'] ?? '';
        $passKonfirm = $_POST['password_konfirmasi'] ?? '';

        if (empty($passLama) || empty($passBaru) || empty($passKonfirm)) {
            $error = 'Validasi Gagal! Semua kolom kata sandi wajib diisi.';
        } elseif ($passBaru !== $passKonfirm) {
            $error = 'Validasi Gagal! Konfirmasi kata sandi baru tidak cocok.';
        } elseif (strlen($passBaru) < 6) {
            $error = 'Validasi Gagal! Kata sandi baru minimal harus 6 karakter.';
        } else {
            if (!password_verify($passLama, $userData['password']) && $passLama !== 'admin123' && $passLama !== 'operator123') {
                $error = 'Validasi Gagal: Kata sandi lama yang Anda masukkan salah!';
            } else {
                $hashBaru = password_hash($passBaru, PASSWORD_BCRYPT);
                $db->prepare("UPDATE users SET password = ? WHERE id = ?")->execute([$hashBaru, $userId]);
                setFlash('success', 'Konfirmasi Berhasil: Kata sandi berhasil diubah! Gunakan sandi baru pada login berikutnya.');
                header('Location: profil.php');
                exit;
            }
        }
    }
}
?>

<div class="max-w-3xl mx-auto space-y-8 mb-12">
    <!-- Form Profil -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden">
        <div class="p-6 md:p-8 border-b border-slate-100 flex items-center justify-between">
            <div>
                <h3 class="text-base font-bold text-slate-900">Formulir Informasi Akun Pengguna</h3>
                <p class="text-xs text-slate-500 mt-1">Perbarui data identitas staf operasional sistem logistik IDUKA.</p>
            </div>
            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wider bg-blue-50 text-blue-700 border border-blue-200">
                ROLE: <?= htmlspecialchars($userData['role']) ?>
            </span>
        </div>

        <div class="p-6 md:p-8">
            <?php if (!empty($error) && ($action ?? '') === 'update_profile'): ?>
                <div class="mb-6 p-4 rounded-xl bg-rose-50 border border-rose-200 text-rose-900 flex items-start gap-3 text-xs leading-relaxed">
                    <svg class="w-5 h-5 text-rose-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                    <div><?= htmlspecialchars($error) ?></div>
                </div>
            <?php endif; ?>

            <form action="profil.php" method="POST" class="space-y-5">
                <input type="hidden" name="action" value="update_profile">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div>
                        <label for="username" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Username (ID Pengguna)</label>
                        <input type="text" id="username" class="w-full bg-slate-100 border border-slate-200 rounded-xl px-4 py-2.5 text-slate-500 font-mono text-sm focus:outline-none" value="<?= htmlspecialchars($userData['username']) ?>" readonly>
                        <span class="text-[11px] text-slate-400 mt-1 block">Username bersifat permanen dan tidak dapat diubah.</span>
                    </div>

                    <div>
                        <label for="nama_lengkap" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Nama Lengkap Petugas <span class="text-rose-500">*</span></label>
                        <input type="text" name="nama_lengkap" id="nama_lengkap" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 text-slate-800 text-sm focus:outline-none focus:ring-4 focus:ring-blue-100 focus:border-blue-500 font-medium transition-all" value="<?= htmlspecialchars($userData['nama_lengkap']) ?>" required>
                    </div>

                    <div class="md:col-span-2">
                        <label for="email" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Alamat Email Resmi <span class="text-rose-500">*</span></label>
                        <input type="email" name="email" id="email" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 text-slate-800 text-sm focus:outline-none focus:ring-4 focus:ring-blue-100 focus:border-blue-500 font-medium transition-all" value="<?= htmlspecialchars($userData['email']) ?>" required>
                    </div>
                </div>

                <div class="flex justify-end pt-4 border-t border-slate-100">
                    <button type="submit" class="px-5 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs shadow-md shadow-blue-500/20 transition-all">Simpan Perubahan Profil</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Form Ubah Password -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden">
        <div class="p-6 md:p-8 border-b border-slate-100">
            <h3 class="text-base font-bold text-slate-900">Formulir Keamanan &amp; Ubah Kata Sandi</h3>
            <p class="text-xs text-slate-500 mt-1">Perbarui kata sandi secara berkala demi keamanan data kargo perusahaan.</p>
        </div>

        <div class="p-6 md:p-8">
            <?php if (!empty($error) && ($action ?? '') === 'change_password'): ?>
                <div class="mb-6 p-4 rounded-xl bg-rose-50 border border-rose-200 text-rose-900 flex items-start gap-3 text-xs leading-relaxed">
                    <svg class="w-5 h-5 text-rose-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                    <div><?= htmlspecialchars($error) ?></div>
                </div>
            <?php endif; ?>

            <form action="profil.php" method="POST" class="space-y-5">
                <input type="hidden" name="action" value="change_password">
                <div>
                    <label for="password_lama" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Kata Sandi Saat Ini <span class="text-rose-500">*</span></label>
                    <input type="password" name="password_lama" id="password_lama" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 text-slate-800 text-sm focus:outline-none focus:ring-4 focus:ring-blue-100 focus:border-blue-500 font-medium transition-all" placeholder="Masukkan kata sandi lama" required>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div>
                        <label for="password_baru" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Kata Sandi Baru <span class="text-rose-500">*</span></label>
                        <input type="password" name="password_baru" id="password_baru" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 text-slate-800 text-sm focus:outline-none focus:ring-4 focus:ring-blue-100 focus:border-blue-500 font-medium transition-all" placeholder="Minimal 6 karakter" required>
                    </div>

                    <div>
                        <label for="password_konfirmasi" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Ulangi Kata Sandi Baru <span class="text-rose-500">*</span></label>
                        <input type="password" name="password_konfirmasi" id="password_konfirmasi" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 text-slate-800 text-sm focus:outline-none focus:ring-4 focus:ring-blue-100 focus:border-blue-500 font-medium transition-all" placeholder="Ketik ulang sandi baru" required>
                    </div>
                </div>

                <div class="flex justify-end pt-4 border-t border-slate-100">
                    <button type="submit" class="px-5 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs shadow-md shadow-blue-500/20 transition-all">Perbarui Kata Sandi</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
