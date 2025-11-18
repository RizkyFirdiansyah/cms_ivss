<?php
if (session_status() === PHP_SESSION_NONE) {
  session_start();
}

require_once '../app/config/Database.php';
require_once '../app/controllers/AuthController.php';
require_once '../app/controllers/ProfileController.php';
require_once '../app/controllers/DashboardController.php';
require_once '../app/controllers/UsersController.php';
require_once '../app/models/UserModel.php';

// Ambil parameter URL (misalnya: login, logout, dashboard)
$url = isset($_GET['url']) ? $_GET['url'] : 'login';

$auth = new AuthController();

switch ($url) {
  case 'login':
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
      $auth->login();
    } else {
      $auth->loginForm();
    }
    break;

  case 'logout':
    $auth->logout();
    break;

  case 'dashboard':
    (new DashboardController())->index();
    break;

  case 'users':
    (new UsersController())->index();
    break;

  case 'user/list':
    (new UsersController())->getList();
    break;

  case 'user/create':
    (new UsersController())->create();
    break;

  case 'user/update':
    (new UsersController())->update();
    break;

  case 'user/delete':
    (new UsersController())->delete();
    break;

  case 'profile':
    (new ProfileController())->index();
    break;

  case 'profile/update':
    (new ProfileController())->update();
    break;

  case 'profile/add-sosmed':
    (new ProfileController())->update_sosmed();
    break;

  case 'profile/update-list':
    (new ProfileController())->update_list();
    break;

  default:
    echo "404 - Halaman tidak ditemukan";
    break;
}
