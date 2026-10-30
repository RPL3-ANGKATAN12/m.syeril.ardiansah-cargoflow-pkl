<?php
/**
 * Halaman Khusus Pengujian "Konfirmasi Gagal" - Tailwind CSS Clean Light Mode
 * LOGIX PRO - Sistem Manajemen Pengiriman Kargo & Kontainer
 */
$pageTitle = 'Showcase Form: Konfirmasi Gagal';
require_once __DIR__ . '/includes/header.php';

$scenario = $_GET['scenario'] ?? 'overload';
?>

<!-- Banner Keterangan Tugas Ummi -->
<div class="mb-6 p-5 rounded-2xl bg-rose-50 border border-rose-200/80 shadow-sm">
    <span class="inline-block px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-rose-100 text-rose-800 uppercase tracking-wider mb-1">
        TUGAS PROJEK - SOAL NOMOR 2
    </span>
    <h4 class="text-base font-bold text-rose-900">Form Bukti: Konfirmasi Gagal (Validation & Error Handling)</h4>
    <p class="text-xs text-rose-700 mt-1 leading-relaxed">
        Halaman ini mendemonstrasikan sistem penanganan kesalahan (*error handling* & *validation failure*) pada form PHP saat data tidak memenuhi kriteria keamanan atau aturan bisnis logistik.
    </p>
</div>

<!-- Tab Pemilihan Skenario Gagal -->
<div class="flex items-center gap-2 mb-6 flex-wrap">
    <a href="konfirmasi_gagal.php?scenario=overload" class="px-4 py-2 rounded-xl text-xs font-bold transition-all <?= $scenario === 'overload' ? 'bg-rose-600 text-white shadow-md shadow-rose-500/20' : 'bg-white border border-slate-200 text-slate-700 hover:bg-slate-50' ?>">
        Skenario 1: Transaksi Overload Tonase
    </a>
    <a href="konfirmasi_gagal.php?scenario=duplicate" class="px-4 py-2 rounded-xl text-xs font-bold transition-all <?= $scenario === 'duplicate' ? 'bg-rose-600 text-white shadow-md shadow-rose-500/20' : 'bg-white border border-slate-200 text-slate-700 hover:bg-slate-50' ?>">
        Skenario 2: Duplikasi Nomor Kontainer Master
    </a>
    <a href="konfirmasi_gagal.php?scenario=auth" class="px-4 py-2 rounded-xl text-xs font-bold transition-all <?= $scenario === 'auth' ? 'bg-rose-600 text-white shadow-md shadow-rose-500/20' : 'bg-white border border-slate-200 text-slate-700 hover:bg-slate-50' ?>">
        Skenario 3: Otentikasi Gagal (Login Invalid)
    </a>
</div>

<div class="max-w-3xl mx-auto bg-white rounded-3xl border border-slate-200/80 shadow-sm overflow-hidden mb-12">
    <div class="p-6 md:p-8 border-b border-slate-100 flex items-center justify-between bg-rose-50/50">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-rose-100 text-rose-600 flex items-center justify-center">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
            </div>
            <div>
                <h3 class="text-base font-bold text-rose-900">Respons Sistem: Konfirmasi Validasi Gagal</h3>
                <p class="text-xs text-rose-600">Terdeteksi pelanggaran aturan bisnis atau masukan data tidak sah</p>
            </div>
        </div>
        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-rose-100 text-rose-800 border border-rose-200">
            STATUS: REJECTED
        </span>
    </div>

    <div class="p-6 md:p-8">
        <?php if ($scenario === 'overload'): ?>
            <!-- Skenario 1: Overload Kargo Transaksi -->
            <div class="mb-6 p-5 rounded-2xl bg-rose-50 border border-rose-200 text-rose-900 flex items-start gap-4">
                <svg class="w-6 h-6 text-rose-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <div class="text-xs leading-relaxed">
                    <strong class="text-sm font-bold text-rose-950 block mb-1">Validasi Transaksi Ditolak: Bahaya Overload Kapasitas!</strong>
                    Berat muatan yang dimasukkan (<strong>32.50 Ton</strong>) melebihi kapasitas beban maksimal kontainer <strong>JLOG-U260101</strong> (Maksimal: <strong>21.50 Ton</strong>). Sesuai regulasi keselamatan pelayaran internasional (SOLAS VGM), pengiriman tidak dapat diproses.
                </div>
            </div>

            <div class="bg-slate-50 border border-slate-200/80 rounded-2xl p-5 mb-6 text-xs space-y-2">
                <div class="font-bold text-slate-800 uppercase tracking-wider mb-2">Parameter Audit &amp; Pengujian:</div>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div><span class="text-slate-400">Kontainer Dipilih:</span> <strong class="text-slate-800">JLOG-U260101 (Dry FCL 20ft)</strong></div>
                    <div><span class="text-slate-400">Batas Kapasitas:</span> <strong class="text-slate-800">21.50 Ton</strong></div>
                    <div><span class="text-slate-400">Berat Diinputkan:</span> <span class="text-rose-600 font-bold">32.50 Ton (+11.00 Ton Over)</span></div>
                    <div><span class="text-slate-400">Tindakan Sistem:</span> <code class="px-2 py-0.5 bg-rose-100 text-rose-800 rounded font-mono">ROLLBACK &amp; PREVENT_INSERT</code></div>
                </div>
            </div>

        <?php elseif ($scenario === 'duplicate'): ?>
            <!-- Skenario 2: Duplikasi Kontainer -->
            <div class="mb-6 p-5 rounded-2xl bg-rose-50 border border-rose-200 text-rose-900 flex items-start gap-4">
                <svg class="w-6 h-6 text-rose-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <div class="text-xs leading-relaxed">
                    <strong class="text-sm font-bold text-rose-950 block mb-1">Validasi Data Master Gagal: Duplikasi Nomor Kontainer!</strong>
                    Nomor kontainer <strong>'JLOG-U260101'</strong> sudah terdaftar pada basis data armada aktif di Depo Tanjung Priok. Nomor unit kontainer bersifat unik (*UNIQUE constraint*) dan tidak boleh diduplikasi.
                </div>
            </div>

            <div class="bg-slate-50 border border-slate-200/80 rounded-2xl p-5 mb-6 text-xs space-y-2">
                <div class="font-bold text-slate-800 uppercase tracking-wider mb-2">Parameter Audit &amp; Pengujian:</div>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div><span class="text-slate-400">Nomor Input:</span> <strong class="font-mono text-slate-800">JLOG-U260101</strong></div>
                    <div><span class="text-slate-400">Status Tabel:</span> Duplicate Entry 'JLOG-U260101' for key 'nomor_kontainer'</div>
                    <div class="sm:col-span-2"><span class="text-slate-400">Tindakan Sistem:</span> Menampilkan pesan peringatan merah dan mempertahankan isi input form.</div>
                </div>
            </div>

        <?php else: ?>
            <!-- Skenario 3: Login Invalid -->
            <div class="mb-6 p-5 rounded-2xl bg-rose-50 border border-rose-200 text-rose-900 flex items-start gap-4">
                <svg class="w-6 h-6 text-rose-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <div class="text-xs leading-relaxed">
                    <strong class="text-sm font-bold text-rose-950 block mb-1">Autentikasi Gagal: Kredensial Tidak Cocok!</strong>
                    Kombinasi username atau kata sandi yang Anda masukkan salah. Sistem keamanan memblokir akses dan mencatat percobaan masuk yang tidak sah.
                </div>
            </div>

            <div class="bg-slate-50 border border-slate-200/80 rounded-2xl p-5 mb-6 text-xs space-y-2">
                <div class="font-bold text-slate-800 uppercase tracking-wider mb-2">Parameter Audit &amp; Pengujian:</div>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div><span class="text-slate-400">Username Diuji:</span> administrator_palsu</div>
                    <div><span class="text-slate-400">Password Diuji:</span> ********** (Salah)</div>
                    <div class="sm:col-span-2"><span class="text-slate-400">Respon Sistem:</span> <code class="px-2 py-0.5 bg-rose-100 text-rose-800 rounded font-mono">password_verify() === FALSE</code></div>
                </div>
            </div>
        <?php endif; ?>

        <!-- Rekomendasi Solusi -->
        <div class="border-t border-slate-100 pt-6 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div class="text-xs text-slate-500">
                Screenshot halaman ini untuk melengkapi berkas <strong>"Form Konfirmasi Gagal"</strong> pada tugas Ummi.
            </div>
            <div class="flex items-center gap-2.5">
                <a href="transaksi_form.php" class="inline-flex items-center gap-1.5 px-4 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs shadow-md shadow-blue-500/20 transition-all">Coba Form Transaksi</a>
                <a href="kontainer_form.php" class="inline-flex items-center gap-1.5 px-4 py-2.5 rounded-xl bg-white border border-slate-200 text-slate-700 hover:bg-slate-50 font-semibold text-xs shadow-sm transition-all">Coba Form Master</a>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
