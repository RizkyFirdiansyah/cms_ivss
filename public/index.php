<?php
if (session_status() === PHP_SESSION_NONE) {
  session_start();
}

require_once '../app/config/Database.php';
require_once '../app/controllers/AuthController.php';
require_once '../app/controllers/ProfileController.php';
require_once '../app/controllers/DashboardController.php';
require_once '../app/controllers/UsersController.php';
require_once '../app/controllers/FacilitiesController.php';
require_once '../app/controllers/GalleryController.php';
require_once '../app/controllers/PublicationsController.php';
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

  case 'fasilitas':
    (new FacilitiesController())->index();
    break;

  case 'fasilitas/list':
    (new FacilitiesController())->getList();
    break;

  case 'fasilitas/create':
    (new FacilitiesController())->create();
    break;

  case 'fasilitas/update':
    (new FacilitiesController())->update();
    break;

  case 'fasilitas/delete':
    (new FacilitiesController())->delete();
    break;

  case 'galeri':
    (new GalleryController())->index();
    break;

  case 'galeri/list':
    (new GalleryController())->list();
    break;

  case 'galeri/create':
    (new GalleryController())->create();
    break;

  case 'galeri/update':
    (new GalleryController())->update();
    break;

  case 'galeri/delete':
    (new GalleryController())->delete();
    break;

  case 'publikasi':
    (new PublicationsController())->index();
    break;

  case 'publikasi/list':
    (new PublicationsController())->getList();
    break;

  case 'publikasi/create':
    (new PublicationsController())->create();
    break;

  case 'publikasi/update':
    (new PublicationsController())->update();
    break;

  case 'publikasi/delete':
    (new PublicationsController())->delete();
    break;

  

  default:
    echo "404 - Halaman tidak ditemukan";
    break;
}
