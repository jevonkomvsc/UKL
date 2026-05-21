// ============================================================
// SCRIPT.JS - Nusantara Journey
// ============================================================

// Fungsi buka detail destinasi
function bukaDetail(id) {
    window.location.href = 'detail.php?id=' + id;
}

document.addEventListener('DOMContentLoaded', function () {

    // Validasi form login / register
    var formAuth = document.querySelector('form.auth-form');
    if (formAuth) {
        formAuth.addEventListener('submit', function (e) {
            var email    = document.getElementById('email');
            var password = document.getElementById('password');

            if (email && email.value.trim() === '') {
                e.preventDefault();
                tampilkanPesan('Email tidak boleh kosong!', 'error');
                email.focus();
                return;
            }

            if (password && password.value.trim() === '') {
                e.preventDefault();
                tampilkanPesan('Password tidak boleh kosong!', 'error');
                password.focus();
                return;
            }

            var tombol = formAuth.querySelector('.btn-submit');
            if (tombol) {
                tombol.textContent = 'Memproses...';
                tombol.disabled = true;
            }
        });
    }

    // Auto hilangkan alert setelah 4 detik
    var alertEl = document.querySelector('.alert');
    if (alertEl) {
        setTimeout(function () {
            alertEl.style.transition = 'opacity 0.5s ease';
            alertEl.style.opacity = '0';
            setTimeout(function () { alertEl.remove(); }, 500);
        }, 4000);
    }
});

// Fungsi tampilkan pesan error/sukses
function tampilkanPesan(teks, tipe) {
    var pesanLama = document.querySelector('.alert-js');
    if (pesanLama) pesanLama.remove();

    var pesan = document.createElement('div');
    pesan.className = 'alert alert-' + (tipe === 'error' ? 'error' : 'success') + ' alert-js';
    pesan.textContent = teks;

    var form = document.querySelector('.auth-form');
    if (form) {
        form.parentNode.insertBefore(pesan, form);
    }

    setTimeout(function () {
        pesan.style.transition = 'opacity 0.4s';
        pesan.style.opacity = '0';
        setTimeout(function () { pesan.remove(); }, 400);
    }, 3000);
}

function konfirmasiLogout() {
    return confirm('Apakah kamu yakin ingin keluar?');
}