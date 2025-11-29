<?php
if (session_status() === PHP_SESSION_NONE) {
  session_start();
}

$role = $_SESSION['role'] ?? 'guest';
?>

<div class="min-height-300 bg-gradient position-absolute w-100" style="background-color: #5AB2FF;"></div>
<aside class="sidenav bg-white navbar navbar-vertical navbar-expand-xs border-0 border-radius-xl my-3 fixed-start ms-4 " id="sidenav-main">
  <div class="sidenav-header">
    <i class="fas fa-times p-3 cursor-pointer text-secondary opacity-5 position-absolute end-0 top-0 d-none d-xl-none" aria-hidden="true" id="iconSidenav"></i>
    <a class="navbar-brand m-0" href="<?= BASE_URL ?>/dashboard">
      <img src="<?= BASE_URL ?>/assets/img/logo-ct-dark.png" width="26px" height="26px" class="navbar-brand-img h-100" alt="main_logo">
      <span class="ms font-weight-bold">Lab Dashboard</span>
    </a>
  </div>
  <hr class="horizontal dark mt-0">
  <div class="navbar-collapse w-auto h-auto" id="sidenav-collapse-main">
    <ul class="navbar-nav">

      <!-- MENU UTAMA -->
      <li class="nav-item">
        <a class="nav-link" href="<?= BASE_URL ?>/dashboard">
          <i class="ni ni-tv-2 text-dark text-sm opacity-10"></i>
          <span class="nav-link-text">Dashboard</span>
        </a>
      </li>
      <!-- MENU UNTUK KEPALA LAB -->
      <?php if ($role === 'kepala'): ?>

        <!-- GROUP: Manajemen Sistem -->
        <li class="nav-item mt-3">
          <h6 class="ps-4 ms-2 text-uppercase text-xs font-weight-bolder opacity-6">Manajemen Sistem</h6>
        </li>

        <li class="nav-item"><a class="nav-link" href="<?= BASE_URL ?>/users"><i class="ni ni-single-02"></i> Users</a></li>
        <li class="nav-item"><a class="nav-link" href="<?= BASE_URL ?>/fasilitas"><i class="ni ni-archive-2"></i> Inventori / Fasilitas</a></li>
        <li class="nav-item"><a class="nav-link" href="<?= BASE_URL ?>/berita"><i class="ni ni-paper-diploma"></i> Berita</a></li>
        <li class="nav-item"><a class="nav-link" href="<?= BASE_URL ?>/publikasi"><i class="ni ni-books"></i> Publikasi</a></li>
        <li class="nav-item"><a class="nav-link" href="<?= BASE_URL ?>/penelitian"><i class="ni ni-atom"></i> Research</a></li>
        <li class="nav-item"><a class="nav-link" href="<?= BASE_URL ?>/datasets"><i class="ni ni-folder-17"></i> Dataset</a></li>
        <li class="nav-item"><a class="nav-link" href="<?= BASE_URL ?>/galeri"><i class="ni ni-image"></i> Galeri</a></li>
        <li class="nav-item"><a class="nav-link" href="<?= BASE_URL ?>/kategori"><i class="ni ni-tag"></i> Kategori</a></li>


        <!-- GROUP: Web Profile Management -->
        <li class="nav-item mt-3">
          <h6 class="ps-4 ms-2 text-uppercase text-xs font-weight-bolder opacity-6">Manejemen Interface</h6>
        </li>

        <!-- DROPDOWN INTERFACE -->
        <li class="nav-item">
          <a data-bs-toggle="collapse" href="#interfaceMenu" class="nav-link" aria-controls="interfaceMenu" role="button" aria-expanded="false">
            <i class="ni ni-tv-2"></i>
            <span class="nav-link-text">Kelola Halaman</span>
          </a>

          <div class="collapse" id="interfaceMenu">
            <ul class="nav ms-2">
              <li class="nav-item"><a class="nav-link" href="<?= BASE_URL ?>/settings"><i class="ni ni-settings-gear-65"></i> Pengaturan Umum</a></li>
              <li class="nav-item"><a class="nav-link" href="<?= BASE_URL ?>/home"><i class="ni ni-shop"></i> Halaman Beranda</a></li>
              <li class="nav-item"><a class="nav-link" href="<?= BASE_URL ?>/about"><i class="ni ni-single-copy-04"></i> Halaman Tentang Kami</a></li>
              <li class="nav-item"><a class="nav-link" href="<?= BASE_URL ?>/member"><i class="ni ni-badge"></i> Halaman Member</a></li>
              <li class="nav-item"><a class="nav-link" href="<?= BASE_URL ?>/fasilitas-page"><i class="ni ni-building"></i> Halaman Fasilitas</a></li>
              <li class="nav-item"><a class="nav-link" href="<?= BASE_URL ?>/sop-page"><i class="ni ni-collection"></i> Halaman SOP</a></li>
              <li class="nav-item"><a class="nav-link" href="<?= BASE_URL ?>/galeri-page"><i class="ni ni-album-2"></i> Halaman Galeri</a></li>
              <li class="nav-item"><a class="nav-link" href="<?= BASE_URL ?>/berita-page"><i class="ni ni-notification-70"></i> Halaman Berita</a></li>
              <li class="nav-item"><a class="nav-link" href="<?= BASE_URL ?>/publikasi-page"><i class="ni ni-books"></i> Halaman Publikasi</a></li>
              <li class="nav-item"><a class="nav-link" href="<?= BASE_URL ?>/penelitian-page"><i class="ni ni-zoom-split-in"></i> Halaman Penelitian</a>
              </li>
            </ul>
          </div>
        </li>



      <?php elseif ($role === 'dosen'): ?>
        <!-- MENU KHUSUS DOSEN -->
        <li class="nav-item"><a class="nav-link" href="<?= BASE_URL ?>/bimbingan"><i class="ni ni-hat-3"></i> Mahasiswa Bimbingan</a></li>
        <li class="nav-item"><a class="nav-link" href="<?= BASE_URL ?>/berita"><i class="ni ni-paper-diploma"></i> Berita</a></li>
        <li class="nav-item"><a class="nav-link" href="<?= BASE_URL ?>/publikasi"><i class="ni ni-books"></i> Publikasi</a></li>
        <li class="nav-item"><a class="nav-link" href="<?= BASE_URL ?>/datasets"><i class="ni ni-folder-17"></i> Dataset</a></li>

      <?php elseif ($role === 'mahasiswa'): ?>
        <!-- MENU KHUSUS MAHASISWA -->
        <li class="nav-item"><a class="nav-link" href="<?= BASE_URL ?>/berita"><i class="ni ni-paper-diploma"></i> Berita</a></li>
        <li class="nav-item"><a class="nav-link" href="<?= BASE_URL ?>/publikasi"><i class="ni ni-books"></i> Publikasi Saya</a></li>
        <li class="nav-item"><a class="nav-link" href="<?= BASE_URL ?>/bimbingan"><i class="ni ni-hat-3"></i> Bimbingan</a></li>
        <li class="nav-item"><a class="nav-link" href="<?= BASE_URL ?>/datasets"><i class="ni ni-folder-17"></i> Dataset</a></li>
      <?php endif; ?>

      <!-- MENU AKUN -->
      <li class="nav-item mt-3">
        <h6 class="ps-4 ms-2 text-uppercase text-xs font-weight-bolder opacity-6">Account</h6>
      </li>
      <li class="nav-item"><a class="nav-link" href="<?= BASE_URL ?>/profile"><i class="ni ni-circle-08"></i> Profile</a></li>
      <li id="logout" class="nav-item"><a class="nav-link text-danger" href="<?= BASE_URL ?>/logout"><i class="ni ni-user-run"></i> Logout</a></li>
      <script>
        const logout = document.querySelector('#logout');
        logout.addEventListener('click', function() {
          const confirmLogout = confirm('Apakah Anda yakin ingin logout?');
          if (confirmLogout) {
            window.location.href = '<?= BASE_URL ?>/logout';
          }
        });
      </script>
    </ul>
  </div>
</aside>