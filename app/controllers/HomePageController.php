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

  // Get activities preview for home management
  public function getActivitiesPreview()
  {
    try {
      $activities = $this->home->getActivitiesForHome();

      $this->jsonResponse([
        'success' => true,
        'data' => $activities
      ]);
    } catch (Exception $e) {
      error_log("Get Activities Preview Error: " . $e->getMessage());
      $this->jsonResponse([
        'success' => false,
        'message' => 'Gagal mengambil preview kegiatan.'
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

    // Handle file uploads first - dapatkan file baru dan info file lama
    $uploadResult = $this->handleFileUploads();
    $uploadedFiles = $uploadResult['uploadedFiles'];
    $filesToDelete = $uploadResult['filesToDelete'];

    // Prepare content data
    $contentData = [];

    // Hero Section
    if (isset($_POST['hero_title'])) {
      $contentData['hero_title'] = [
        'type' => 'text',
        'value' => trim($_POST['hero_title'])
      ];
    }

    if (isset($_POST['hero_subtitle'])) {
      $contentData['hero_subtitle'] = [
        'type' => 'text',
        'value' => trim($_POST['hero_subtitle'])
      ];
    }

    // Profile Section
    if (isset($_POST['profile_description'])) {
      $contentData['profile_description'] = [
        'type' => 'textarea',
        'value' => trim($_POST['profile_description'])
      ];
    }

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
        // Hapus file lama hanya setelah sukses save ke database
        $this->deleteOldFiles($filesToDelete);

        $this->jsonResponse([
          'success' => true,
          'message' => 'Konten home berhasil diperbarui.'
        ]);
      } else {
        // Jika gagal save, hapus file yang baru diupload
        $this->rollbackUploadedFiles($uploadedFiles);

        $this->jsonResponse([
          'success' => false,
          'message' => 'Gagal memperbarui konten home.'
        ], 500);
      }
    } catch (Exception $e) {
      // Jika ada exception, hapus file yang baru diupload
      $this->rollbackUploadedFiles($uploadedFiles);

      error_log("Update Home Error: " . $e->getMessage());
      $this->jsonResponse([
        'success' => false,
        'message' => 'Terjadi kesalahan sistem: ' . $e->getMessage()
      ], 500);
    }
  }

  // Get gallery images for preview
  public function getGalleryImages()
  {
    try {
      $images = $this->gallery->getRecentGallery(10);

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

  // Handle file uploads dengan management file lama yang aman
  private function handleFileUploads()
  {
    $uploadedFiles = [];
    $filesToDelete = [];

    // Get current home contents to check existing images
    $currentContents = $this->home->getHomeContents();

    // Hero image
    if (!empty($_FILES['hero_image']['name'])) {
      $heroImage = $this->handleFileUpload($_FILES['hero_image'], 'hero');
      if ($heroImage !== false) {
        $uploadedFiles['hero_image'] = $heroImage;

        // Simpan info file lama untuk dihapus nanti setelah sukses save
        $oldImage = $_POST['old_hero_image'] ?? ($currentContents['hero_image']['value'] ?? '');
        if (!empty($oldImage)) {
          $filesToDelete[] = [
            'path' => $oldImage,
            'type' => 'hero_image'
          ];
        }
      }
    }

    // Profile images
    for ($i = 1; $i <= 3; $i++) {
      $fieldName = "profile_image_{$i}";
      if (!empty($_FILES[$fieldName]['name'])) {
        $profileImage = $this->handleFileUpload($_FILES[$fieldName], "profile_{$i}");
        if ($profileImage !== false) {
          $uploadedFiles[$fieldName] = $profileImage;

          // Simpan info file lama untuk dihapus nanti setelah sukses save
          $oldImage = $_POST["old_{$fieldName}"] ?? ($currentContents[$fieldName]['value'] ?? '');
          if (!empty($oldImage)) {
            $filesToDelete[] = [
              'path' => $oldImage,
              'type' => $fieldName
            ];
          }
        }
      }
    }

    return [
      'uploadedFiles' => $uploadedFiles,
      'filesToDelete' => $filesToDelete
    ];
  }

  // Handle single file upload
  private function handleFileUpload($file, $prefix = '')
  {
    $upload_dir = 'uploads/home/';
    $upload_path_full = __DIR__ . '/../../public/' . $upload_dir;

    // Pastikan folder ada
    if (!is_dir($upload_path_full)) {
      mkdir($upload_path_full, 0777, true);
    }

    $allowed_types = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));

    // Validate file type
    if (!in_array($ext, $allowed_types)) {
      error_log("Invalid file type for {$prefix}: {$ext}");
      return false;
    }

    // Validate file size (max 5MB)
    if ($file['size'] > 5 * 1024 * 1024) {
      error_log("File too large for {$prefix}: {$file['size']} bytes");
      return false;
    }

    // Generate unique filename
    $new_file_name = $prefix . '_' . $this->user['id'] . '_' . time() . '.' . $ext;
    $target_path = $upload_path_full . $new_file_name;

    if (move_uploaded_file($file['tmp_name'], $target_path)) {
      return $new_file_name;
    }

    error_log("Failed to move uploaded file for {$prefix}");
    return false;
  }

  // Delete old files after successful database update
  private function deleteOldFiles($filesToDelete)
  {
    foreach ($filesToDelete as $fileInfo) {
      $this->deleteFileFromServer($fileInfo['path'], $fileInfo['type']);
    }
  }

  // Rollback uploaded files if operation fails
  private function rollbackUploadedFiles($uploadedFiles)
  {
    foreach ($uploadedFiles as $filename) {
      if ($filename !== false) {
        $this->deleteFileFromServer($filename, 'rollback');
      }
    }
  }

  // Delete file from server
  private function deleteFileFromServer($filename, $type = 'general')
  {
    if (empty($filename)) {
      error_log("Delete File: Filename kosong untuk {$type}");
      return true;
    }

    // Tambahkan path directory karena hanya menyimpan nama file
    $file_path = 'uploads/home/' . $filename;
    $full_path = __DIR__ . '/../../public/' . $file_path;

    error_log("Delete File Attempt: {$type}");
    error_log("Filename: {$filename}");
    error_log("Full Path: {$full_path}");

    // Check if file exists and is a file
    if (file_exists($full_path) && is_file($full_path)) {
      if (unlink($full_path)) {
        error_log("✅ File {$type} berhasil dihapus: " . $full_path);
        return true;
      } else {
        error_log("❌ Gagal menghapus file {$type} (Izin Ditolak): " . $full_path);

        // Cek permissions
        error_log("File Permissions: " . substr(sprintf('%o', fileperms($full_path)), -4));
        error_log("Is Writable: " . (is_writable($full_path) ? 'Yes' : 'No'));

        return false;
      }
    } else {
      error_log("⚠️ File {$type} tidak ditemukan: " . $full_path);

      // Cek apakah directory exists
      $dir = dirname($full_path);
      error_log("Directory exists: " . (is_dir($dir) ? 'Yes' : 'No'));

      // Coba cari file dengan path alternatif
      $alternative_path = __DIR__ . '/../../public/uploads/home/' . $filename;
      if (file_exists($alternative_path)) {
        error_log("File ditemukan di path alternatif: " . $alternative_path);
        if (unlink($alternative_path)) {
          error_log("✅ File {$type} berhasil dihapus dari path alternatif");
          return true;
        }
      }
    }

    return true;
  }
}
