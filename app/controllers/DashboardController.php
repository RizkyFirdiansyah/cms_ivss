<?php
require_once 'BaseController.php';

class DashboardController extends BaseController
{
  public function __construct()
  {
    parent::__construct(); // ✅ Penting: jalankan inisialisasi dari BaseController
    parent::requireLogin();
  }

  public function index()
  {
    $page_title = 'Dashboard';
    $page_breadcrumb = ['Pages', 'Dashboard'];

    // ✅ Sekarang data user bisa diakses langsung
    $user = $this->user;

    include '../app/views/dashboard.php';
  }
}
