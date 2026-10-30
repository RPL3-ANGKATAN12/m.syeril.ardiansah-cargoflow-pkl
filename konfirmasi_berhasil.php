<?php
/**
 * Halaman Khusus Pengujian "Konfirmasi Berhasil" - Tailwind CSS Clean Light Mode
 * LOGIX PRO - Sistem Manajemen Pengiriman Kargo & Kontainer
 */
$pageTitle = 'Showcase Form: Konfirmasi Berhasil';
require_once __DIR__ . '/includes/header.php';

$id = (int)($_GET['id'] ?? 1);
$stmt = $db->prepare("
    SELECT t.*, p.nama_perusahaan, k.nomor_kontainer, k.tipe_kontainer
    FROM transaksi_pengiriman t
    JOIN pelanggan p ON t.id_pelanggan = p.id
    JOIN kontainer k ON t.id_kontainer = k.id
    WHERE t.id = ?
");
$stmt->execute([$id]);
$trx = $stmt->fetch();
if (!$trx) {
    $stmtFallback = $db->query("
        SELECT t.*, p.nama_perusahaan, k.nomor_kontainer, k.tipe_kontainer
        FROM transaksi_pengiriman t
        JOIN pelanggan p ON t.id_pelanggan = p.id
        JOIN kontainer k ON t.id_kontainer = k.id
        ORDER BY t.id DESC LIMIT 1
    ");
    $trx = $stmtFallback->fetch();
}
?>

<!-- Banner Keterangan Tugas Ummi -->
<div class="mb-6 p-5 rounded-2xl bg-emerald-50 border border-emerald-200/80 shadow-sm">
    <span class="inline-block px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-emerald-100 text-emerald-800 uppercase tracking-wider mb-1">
        TUGAS PROJEK - SOAL NOMOR 2
    </span>
    <h4 class="text-base font-bold text-emerald-900">Form Bukti: Konfirmasi Berhasil (Success Confirmation)</h4>
    <p class="text-xs text-emerald-700 mt-1 leading-relaxed">
        Halaman ini menampilkan respons antarmuka sistem ketika transaksi kargo berhasil diproses dan tersimpan ke basis data.
    </p>
</div>

<div class="max-w-2xl mx-auto bg-white rounded-3xl border border-slate-200/80 shadow-xl p-8 md:p-12 text-center mb-12">
    <!-- Emerald Circle Icon -->
    <div class="w-20 h-20 rounded-full bg-emerald-50 border-2 border-emerald-500 text-emerald-600 flex items-center justify-center mx-auto mb-6 shadow-lg shadow-emerald-500/10">
        <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
    </div>

    <h2 class="text-2xl font-black text-slate-900 tracking-tight mb-2">SURAT JALAN BERHASIL DITERBITKAN!</h2>
    <p class="text-xs md:text-sm text-slate-500 max-w-md mx-auto mb-8 leading-relaxed">
        Transaksi pemesanan pengiriman kontainer telah sukses tercatat. Seluruh data kargo, alokasi armada kontainer, dan tarif pelayaran telah disinkronkan ke sistem basis data.
    </p>

    <!-- Ringkasan Box -->
    <div class="bg-slate-50 rounded-2xl border border-slate-200/80 p-5 text-left text-xs space-y-3 mb-8 max-w-lg mx-auto">
        <div class="flex items-center justify-between border-b border-slate-200 pb-2.5">
            <span class="text-slate-400 uppercase font-bold tracking-wider">Nomor Resi Resmi</span>
            <strong class="font-mono text-blue-700 text-sm"><?= htmlspecialchars($trx['no_resi']) ?></strong>
        </div>
        <div class="flex items-center justify-between border-b border-slate-200 pb-2.5">
            <span class="text-slate-400 uppercase font-bold tracking-wider">Shipper (Pengirim)</span>
            <strong class="text-slate-900"><?= htmlspecialchars($trx['nama_perusahaan']) ?></strong>
        </div>
        <div class="flex items-center justify-between border-b border-slate-200 pb-2.5">
            <span class="text-slate-400 uppercase font-bold tracking-wider">Kontainer Armada</span>
            <span class="text-slate-700 font-semibold"><?= htmlspecialchars($trx['nomor_kontainer']) ?> (<?= htmlspecialchars($trx['tipe_kontainer']) ?>)</span>
        </div>
        <div class="flex items-center justify-between border-b border-slate-200 pb-2.5">
            <span class="text-slate-400 uppercase font-bold tracking-wider">Rute Pelayaran</span>
            <span class="text-slate-700 font-medium"><?= htmlspecialchars($trx['pelabuhan_asal']) ?> &rarr; <?= htmlspecialchars($trx['pelabuhan_tujuan']) ?></span>
        </div>
        <div class="flex items-center justify-between pt-1">
            <span class="text-slate-500 uppercase font-extrabold tracking-wider">Total Biaya Freight</span>
            <strong class="text-emerald-700 font-extrabold text-base"><?= rupiah($trx['total_biaya']) ?></strong>
        </div>
    </div>

    <!-- Actions -->
    <div class="flex flex-wrap items-center justify-center gap-3">
        <a href="transaksi_detail.php?id=<?= $trx['id'] ?>" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs shadow-md shadow-blue-500/20 transition-all">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
            <span>Lihat Lembar Surat Jalan</span>
        </a>
        <a href="transaksi_form.php" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-white border border-slate-200 text-slate-700 hover:bg-slate-50 font-bold text-xs shadow-sm transition-all">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            <span>Input Booking Lainnya</span>
        </a>
        <a href="dashboard.php" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold text-xs transition-all">Kembali ke Dashboard</a>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
