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

    // Simpan data user yang sedang login
    if (isset($_SESSION['id'])) {
      $this->user = $this->profileModel->getProfileById($_SESSION['id']);
    }
  }

  // Wajib Login
  protected function requireLogin()
  {
    if (!isset($_SESSION['id'])) {
      $this->redirectTo('login', 'Anda harus login untuk mengakses halaman ini.', 'error');
    }
  }

  // Validasi satu role
  protected function requireRole($role)
  {

    if (!isset($_SESSION['role']) || $_SESSION['role'] !== $role) {
      // header('Location: ' . BASE_URL . '/unauthorized');
      echo "404 - Halaman tidak ditemukan";
      exit;
    }
  }

  // Validasi beberapa role
  protected function requireRoles(array $roles)
  {

    $userRole = $_SESSION['role'] ?? null;

    if (!in_array($userRole, $roles)) {
      // header('Location: ' . BASE_URL . '/unauthorized');
      echo "404 - Halaman tidak ditemukan";
      exit;
    }
  }

  // Redirect
  protected function redirectTo($path, $message = null, $type = 'info')
  {
    if ($message) {
      $_SESSION['status_message'] = $message;
      $_SESSION['status_type'] = $type;
    }

    header("Location: " . $this->baseUrl() . '/' . $path);
    exit;
  }

  // Render view
  protected function render($viewPath, $data = [])
  {
    extract($data);
    include '../app/views/' . $viewPath;
  }

  // Base URL
  protected function baseUrl()
  {
    return BASE_URL ?? "http://localhost/mvc-pbl/public";
  }

  // JSON Response
  protected function jsonResponse(array $data, $http_code = 200)
  {
    http_response_code($http_code);
    header('Content-Type: application/json');
    echo json_encode($data);
    exit;
  }
}
