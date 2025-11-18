<?php
require_once '../app/models/ProfileModel.php';

class BaseController
{
  protected $user = null;
  protected $profileModel;

  public function __construct()
  {
    if (session_status() === PHP_SESSION_NONE) {
      session_start();
    }

    // Inisialisasi model umum (boleh digunakan semua controller)
    $this->profileModel = new ProfileModel();

    // Jika user sudah login, simpan datanya untuk penggunaan global
    if (isset($_SESSION['id'])) {
      $this->user = $this->profileModel->getProfileById($_SESSION['id']);
    }
  }

  /**
   * Fungsi wajib login, panggil di controller yang butuh proteksi.
   */
  protected function requireLogin()
  {
    if (!isset($_SESSION['id'])) {
      $this->redirectTo('login', 'Anda harus login untuk mengakses halaman ini.', 'error');
    }
  }

  /**
   * Fungsi redirect dengan pesan opsional (flash message)
   */
  protected function redirectTo($path, $message = null, $type = 'info')
  {
    if ($message) {
      $_SESSION['status_message'] = $message;
      $_SESSION['status_type'] = $type;
    }

    header("Location: " . $this->baseUrl() . '/' . $path);
    exit;
  }

  /**
   * Helper untuk merender view utama
   */
  protected function render($viewPath, $data = [])
  {
    extract($data);
    include '../app/views/' . $viewPath;
  }

  /**
   * Helper untuk mendapatkan base URL
   */
  protected function baseUrl()
  {
    return BASE_URL ?? "http://localhost/mvc-pbl/public";
  }
}
