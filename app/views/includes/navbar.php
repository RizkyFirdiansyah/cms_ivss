<?php
// Parameter dinamis dari halaman
$page_title = $page_title ?? 'Dashboard';
$page_breadcrumb = $page_breadcrumb ?? ['Pages', $page_title];
?>
<nav class="navbar navbar-main navbar-expand-lg px-0 mx-4 shadow-none border-radius-xl" id="navbarBlur" data-scroll="false">
  <div class="container-fluid py-1 px-3">
    <nav aria-label="breadcrumb">
      <ol class="breadcrumb bg-transparent mb-0 pb-0 pt-1 px-0 me-sm-6 me-5">
        <?php foreach ($page_breadcrumb as $i => $crumb): ?>
          <?php if ($i === array_key_last($page_breadcrumb)): ?>
            <li class="breadcrumb-item text-sm text-white active" aria-current="page"><?= htmlspecialchars($crumb) ?></li>
          <?php else: ?>
            <li class="breadcrumb-item text-sm">
              <a class="opacity-5 text-white" href="javascript:;"><?= htmlspecialchars($crumb) ?></a>
            </li>
          <?php endif; ?>
        <?php endforeach; ?>
      </ol>
      <h6 class="font-weight-bolder text-white mb-0"><?= htmlspecialchars($page_title) ?></h6>
    </nav>

    <div class="collapse navbar-collapse mt-sm-0 mt-2 me-md-0 me-sm-4" id="navbar">
      <div class="ms-md-auto pe-md-3 d-flex align-items-center">
      </div>
      <ul class="navbar-nav justify-content-end">
        <li class="nav-item dropdown pe-2 d-flex align-items-center">
          <a href="javascript:;" class="nav-link text-white p-0" id="dropdownMenuButton" data-bs-toggle="dropdown" aria-expanded="false">
            <i class="fa fa-bell cursor-pointer"></i>
          </a>
          <ul class="dropdown-menu dropdown-menu-end px-2 py-3 me-sm-n4" aria-labelledby="dropdownMenuButton">
            <li><a class="dropdown-item border-radius-md" href="#">Tidak ada notifikasi</a></li>
          </ul>
        </li>
        <li class="nav-item d-xl-none ps-3 d-flex align-items-center">
          <a href="javascript:;" class="nav-link text-white p-0" id="iconNavbarSidenav">
            <div class="sidenav-toggler-inner">
              <i class="sidenav-toggler-line bg-white"></i>
              <i class="sidenav-toggler-line bg-white"></i>
              <i class="sidenav-toggler-line bg-white"></i>
            </div>
          </a>
        </li>
      </ul>
    </div>
  </div>
</nav>
<!-- <li class="nav-item d-flex align-items-center">

 <span class="text-white ms-2"><?= htmlspecialchars($user['nama'] ?? '') ?></span>
</li> -->