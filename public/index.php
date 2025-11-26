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
require_once '../app/controllers/NewsController.php';
require_once '../app/controllers/CategoryController.php';
require_once '../app/controllers/GalleryController.php';
require_once '../app/controllers/PublicationsController.php';
require_once '../app/controllers/SettingsPageController.php';
require_once '../app/controllers/HomePageController.php';
require_once '../app/controllers/AboutPageController.php';
require_once '../app/controllers/MemberPageController.php';
require_once '../app/controllers/FacilityPageController.php';
require_once '../app/controllers/SopPageController.php';
require_once '../app/controllers/GaleriPageController.php';
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

  case 'fasilitas/getDetail':
    (new FacilitiesController())->getDetail();
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

  case 'berita':
    (new NewsController())->index();
    break;

  case 'berita/list':
    (new NewsController())->getList();
    break;

  case 'berita/getDetail':
    (new NewsController())->getDetail();

  case 'berita/create':
    (new NewsController())->create();
    break;

  case 'berita/update':
    (new NewsController())->update();
    break;

  case  'berita/delete':
    (new NewsController())->delete();
    break;

  case 'kategori':
    (new CategoryController())->index();
    break;

  case 'kategori/list':
    (new CategoryController())->getList();
    break;

  case 'kategori/getAll':
    (new CategoryController())->getAll();
    break;

  case 'kategori/create':
    (new CategoryController())->create();
    break;

  case 'kategori/update':
    (new CategoryController())->update();
    break;

  case 'kategori/delete':
    (new CategoryController())->delete();
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

  case 'publikasi/categories':
    (new PublicationsController())->getCategories();
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

  case 'settings':
    (new SettingsPageController())->index();
    break;

  case 'settings/read':
    (new SettingsPageController())->getAll();
    break;

  case 'settings/update':
    (new SettingsPageController())->update();
    break;

  case 'home':
    (new HomePageController())->index();
    break;

  case 'home/read':
    (new HomePageController())->getContents();
    break;

  case 'home/update':
    (new HomePageController())->update();
    break;

  case 'home/gallery':
    (new HomePageController())->getGalleryImages();
    break;

  case 'home/activities-preview':
    (new HomePageController())->getActivitiesPreview();
    break;

  case 'about':
    (new AboutPageController())->index();
    break;

  case 'about/read':
    (new AboutPageController())->getContents();
    break;

  case 'about/update':
    (new AboutPageController())->update();
    break;

  case 'about/gallery':
    (new AboutPageController())->getGalleryImages();
    break;

  case 'member':
    (new MemberPageController())->index();
    break;

  case 'member/read':
    (new MemberPageController())->getContents();
    break;

  case 'member/active-members':
    (new MemberPageController())->getActiveMembers();
    break;

  case 'member/update':
    (new MemberPageController())->update();
    break;

  case 'fasilitas-page':
    (new FacilityPageController())->index();
    break;

  case 'fasilitas-page/read':
    (new FacilityPageController())->getContents();
    break;

  case 'fasilitas-page/allFasilitas':
    (new FacilityPageController())->getAllFacilities();
    break;

  case 'fasilitas-page/update':
    (new FacilityPageController())->update();

  case 'sop-page':
    (new SopPageController())->index();
    break;

  case 'sop-page/read':
    (new SopPageController())->getContents();
    break;

  case 'sop-page/update':
    (new SopPageController())->update();
    break;

  case 'galeri-page':
    (new GalleryPageController())->index();
    break;

  case 'galeri-page/read':
    (new GalleryPageController())->getContents();
    break;

  case 'galeri-page/update':
    (new GalleryPageController())->update();

  default:
    echo "404 - Halaman tidak ditemukan";
    break;
}
