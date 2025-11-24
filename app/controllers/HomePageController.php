<?php
require_once '../app/models/HomeModel.php';
require_once '../app/models/GalleryModel.php';
require_once '../app/controllers/BaseController.php';

class HomePageController extends BaseController
{
  private $home;
  private $gallery;

  public function __construct()
  {
    parent::__construct();
    parent::requireLogin();

    $this->home = new HomeModel();
    $this->gallery = new GalleryModel();
  }

  public function index()
  {
    $page_title = 'Manajemen Home';
    $page_breadcrumb = ['Pages', 'Home'];

    // Get home contents for form
    $homeContents = $this->home->getHomeContents();

    include '../app/views/home-page.php';
  }

  // Get home contents for editing
  public function getContents()
  {
    try {
      $contents = $this->home->getHomeContents();

      $this->jsonResponse([
        'success' => true,
        'data' => $contents
      ]);
    } catch (Exception $e) {
      error_log("Get Home Contents Error: " . $e->getMessage());
      $this->jsonResponse([
        'success' => false,
        'message' => 'Gagal mengambil data konten home.'
      ], 500);
    }
  }

  // Update home contents
  public function update()
  {
    $user = $this->user;

    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
      return $this->jsonResponse([
        'success' => false,
        'message' => 'Method tidak diizinkan.'
      ], 405);
    }

    // Handle file uploads first
    $uploadedFiles = $this->handleFileUploads();

    // Prepare content data
    $contentData = [];

    // Hero Section
    $this->addContentIfSet($contentData, 'hero_title', $_POST['hero_title'] ?? '');
    $this->addContentIfSet($contentData, 'hero_subtitle', $_POST['hero_subtitle'] ?? '');

    // Profile Section
    $this->addContentIfSet($contentData, 'profile_description', $_POST['profile_description'] ?? '');

    // Activities Section
    $this->addContentIfSet($contentData, 'activity_title', $_POST['activity_title'] ?? '');

    // Activities items
    for ($i = 1; $i <= 3; $i++) {
      $this->addContentIfSet($contentData, "activity_{$i}_title", $_POST["activity_{$i}_title"] ?? '');
      $this->addContentIfSet($contentData, "activity_{$i}_description", $_POST["activity_{$i}_description"] ?? '');
    }

    // Gallery Section
    $this->addContentIfSet($contentData, 'gallery_title', $_POST['gallery_title'] ?? '');
    $this->addContentIfSet($contentData, 'gallery_subtitle', $_POST['gallery_subtitle'] ?? '');

    // Add uploaded files to content data
    foreach ($uploadedFiles as $key => $filename) {
      if ($filename !== false) {
        $contentData[$key] = [
          'type' => 'text',
          'value' => $filename
        ];
      }
    }

    try {
      $success = $this->home->saveMultipleHomeContents($contentData, $user['id']);

      if ($success) {
        $this->jsonResponse([
          'success' => true,
          'message' => 'Konten home berhasil diperbarui.'
        ]);
      } else {
        $this->jsonResponse([
          'success' => false,
          'message' => 'Gagal memperbarui konten home.'
        ], 500);
      }
    } catch (Exception $e) {
      error_log("Update Home Error: " . $e->getMessage());
      $this->jsonResponse([
        'success' => false,
        'message' => 'Terjadi kesalahan sistem: ' . $e->getMessage()
      ], 500);
    }
  }

  // Get gallery images for slider (from GalleryModel)
  public function getGalleryImages()
  {
    try {
      $images = $this->gallery->getRecentGallery(10); // Get 10 recent images

      $this->jsonResponse([
        'success' => true,
        'data' => $images
      ]);
    } catch (Exception $e) {
      error_log("Get Gallery Images Error: " . $e->getMessage());
      $this->jsonResponse([
        'success' => false,
        'message' => 'Gagal mengambil data galeri.'
      ], 500);
    }
  }

  // Initialize default contents
  public function initializeDefaults()
  {
    $user = $this->user;

    try {
      $success = $this->home->initializeDefaultContents($user['id']);

      if ($success) {
        $this->jsonResponse([
          'success' => true,
          'message' => 'Konten default berhasil diinisialisasi.'
        ]);
      } else {
        $this->jsonResponse([
          'success' => false,
          'message' => 'Gagal menginisialisasi konten default.'
        ], 500);
      }
    } catch (Exception $e) {
      error_log("Initialize Home Error: " . $e->getMessage());
      $this->jsonResponse([
        'success' => false,
        'message' => 'Terjadi kesalahan sistem: ' . $e->getMessage()
      ], 500);
    }
  }

  // Helper method to add content if set
  private function addContentIfSet(&$contentData, $key, $value)
  {
    if (isset($value)) {
      $contentData[$key] = [
        'type' => in_array($key, ['profile_description', 'activity_1_description', 'activity_2_description', 'activity_3_description']) ? 'textarea' : 'text',
        'value' => trim($value)
      ];
    }
  }

  // Handle file uploads
  private function handleFileUploads()
  {
    $uploadedFiles = [];

    // Hero image
    if (!empty($_FILES['hero_image']['name'])) {
      $heroImage = $this->handleFileUpload($_FILES['hero_image'], 'hero');
      if ($heroImage !== false) {
        $uploadedFiles['hero_image'] = $heroImage;
      }
    }

    // Profile images
    for ($i = 1; $i <= 3; $i++) {
      if (!empty($_FILES["profile_image_{$i}"]['name'])) {
        $profileImage = $this->handleFileUpload($_FILES["profile_image_{$i}"], "profile_{$i}");
        if ($profileImage !== false) {
          $uploadedFiles["profile_image_{$i}"] = $profileImage;
        }
      }
    }

    // Activity images
    for ($i = 1; $i <= 3; $i++) {
      if (!empty($_FILES["activity_{$i}_image"]['name'])) {
        $activityImage = $this->handleFileUpload($_FILES["activity_{$i}_image"], "activity_{$i}");
        if ($activityImage !== false) {
          $uploadedFiles["activity_{$i}_image"] = $activityImage;
        }
      }
    }

    return $uploadedFiles;
  }

  // Handle single file upload
  private function handleFileUpload($file, $type = 'general')
  {
    $upload_dir = "uploads/home/";
    $upload_path_full = __DIR__ . '/../../public/' . $upload_dir;

    // Ensure directory exists
    if (!is_dir($upload_path_full)) {
      mkdir($upload_path_full, 0777, true);
    }

    $allowed_types = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
    $max_size = 2 * 1024 * 1024; // 2MB

    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));

    // Validate file type
    if (!in_array($ext, $allowed_types)) {
      error_log("Invalid file type for {$type}: {$ext}");
      return false;
    }

    // Validate file size
    if ($file['size'] > $max_size) {
      error_log("File too large for {$type}: {$file['size']} bytes");
      return false;
    }

    // Generate unique filename
    $new_file_name = "{$type}_" . $this->user['id'] . '_' . time() . '.' . $ext;
    $target_path = $upload_path_full . $new_file_name;

    if (move_uploaded_file($file['tmp_name'], $target_path)) {
      return $upload_dir . $new_file_name;
    }

    error_log("Failed to move uploaded file for {$type}");
    return false;
  }
}
