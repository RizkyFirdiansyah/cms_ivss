<?php
require_once 'BaseController.php';

class DashboardController extends BaseController
{
  public function __construct()
  {
    // Inisialisasi BaseController
    parent::__construct();
    parent::requireLogin();
    parent::requireRoles(['kepala', 'dosen', 'mahasiswa']);
  }

  public function index()
  {
    $page_title = 'Dashboard';
    $page_breadcrumb = ['Pages', 'Dashboard'];

    $user = $this->user;

    include '../app/views/dashboard.php';
  }
}
