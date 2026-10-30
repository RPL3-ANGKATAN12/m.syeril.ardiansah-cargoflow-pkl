/**
 * Interactive Scripts for LOGIX PRO
 */

document.addEventListener('DOMContentLoaded', function () {
    // 1. Hitung Otomatis Total Biaya Transaksi
    const freightInput = document.getElementById('tarif_freight');
    const handlingInput = document.getElementById('biaya_handling');
    const totalInput = document.getElementById('total_biaya');
    const beratInput = document.getElementById('berat_muatan_ton');

    function calculateTotal() {
        if (!freightInput || !handlingInput || !totalInput) return;
        const freight = parseFloat(freightInput.value) || 0;
        const handling = parseFloat(handlingInput.value) || 0;
        const total = freight + handling;
        totalInput.value = total;

        const previewText = document.getElementById('total_biaya_preview');
        if (previewText) {
            previewText.textContent = 'Rp ' + total.toLocaleString('id-ID');
        }
    }

    if (freightInput) freightInput.addEventListener('input', calculateTotal);
    if (handlingInput) handlingInput.addEventListener('input', calculateTotal);

    // 2. Isi Otomatis Akun Demo Login
    window.fillDemo = function (role) {
        const u = document.getElementById('username');
        const p = document.getElementById('password');
        if (!u || !p) return;

        if (role === 'admin') {
            u.value = 'admin';
            p.value = 'admin123';
        } else if (role === 'operator') {
            u.value = 'operator';
            p.value = 'operator123';
        }
    };

    // 3. Konfirmasi Dialog Hapus Data
    window.confirmDelete = function (url, itemName) {
        if (confirm(`Apakah Anda yakin ingin menghapus "${itemName}"?\nData yang terhapus tidak dapat dikembalikan.`)) {
            window.location.href = url;
        }
    };

    // 4. Toggle Mobile Sidebar
    const menuToggle = document.getElementById('menuToggle');
    const sidebar = document.querySelector('.app-sidebar');
    if (menuToggle && sidebar) {
        menuToggle.addEventListener('click', function () {
            sidebar.classList.toggle('open');
        });
    }

    // 5. Cek Kapasitas Kontainer Saat Dipilih
    const kontainerSelect = document.getElementById('id_kontainer');
    if (kontainerSelect && beratInput) {
        kontainerSelect.addEventListener('change', function () {
            const selectedOption = this.options[this.selectedIndex];
            const maxKapasitas = parseFloat(selectedOption.getAttribute('data-kapasitas')) || 999;
            const warningEl = document.getElementById('kapasitas_warning');

            if (warningEl) {
                const berat = parseFloat(beratInput.value) || 0;
                if (berat > maxKapasitas) {
                    warningEl.style.display = 'block';
                    warningEl.textContent = `Peringatan: Berat muatan (${berat} Ton) melebihi kapasitas kontainer (${maxKapasitas} Ton)!`;
                } else {
                    warningEl.style.display = 'none';
                }
            }
        });

        beratInput.addEventListener('input', function () {
            kontainerSelect.dispatchEvent(new Event('change'));
        });
    }
});
