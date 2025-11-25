<?php
require_once '../app/models/AboutPageModel.php';
require_once '../app/models/GalleryModel.php';
require_once '../app/controllers/BaseController.php';

class AboutPageController extends BaseController
{
  private $about;
  private $gallery;

  public function __construct()
  {
    parent::__construct();
    parent::requireLogin();
    parent::requireRole('kepala');

    $this->about = new AboutPageModel();
    $this->gallery = new GalleryModel();
  }

  public function index()
  {
    $page_title = 'Manajemen Tentang Kami';
    $page_breadcrumb = ['Pages', 'Tentang Kami'];

    include '../app/views/about-page.php';
  }

  // Get about contents
  public function getContents()
  {
    try {
      $contents = $this->about->getAboutContents();

      $this->jsonResponse([
        'success' => true,
        'data' => $contents
      ]);
    } catch (Exception $e) {
      error_log("Get About Contents Error: " . $e->getMessage());
      $this->jsonResponse([
        'success' => false,
        'message' => 'Gagal mengambil data konten tentang kami.'
      ], 500);
    }
  }

  // Update about contents dengan file management yang aman
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

    // Header Section
    $this->addContentIfSet($contentData, 'about_header_title', $_POST['about_header_title'] ?? '');
    $this->addContentIfSet($contentData, 'about_header_subtitle', $_POST['about_header_subtitle'] ?? '');

    // Profile Section
    $this->addContentIfSet($contentData, 'about_profile_description', $_POST['about_profile_description'] ?? '');

    // Vision & Mission Section
    $this->addContentIfSet($contentData, 'about_vision_title', $_POST['about_vision_title'] ?? '');
    $this->addContentIfSet($contentData, 'about_vision_content', $_POST['about_vision_content'] ?? '');
    $this->addContentIfSet($contentData, 'about_mission_title', $_POST['about_mission_title'] ?? '');
    $this->addContentIfSet($contentData, 'about_mission_content', $_POST['about_mission_content'] ?? '');

    // Activities Section - SAVE AS GLOBAL ACTIVITIES
    $this->addContentIfSet($contentData, 'activities_title', $_POST['activities_title'] ?? '');
    $this->addContentIfSet($contentData, 'activities_subtitle', $_POST['activities_subtitle'] ?? '');

    // Activities items - SAVE AS GLOBAL (used by both home and about)
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
      $success = $this->about->saveMultipleAboutContents($contentData, $user['id']);

      if ($success) {
        // Hapus file lama hanya setelah sukses save ke database
        $this->deleteOldFiles($filesToDelete);

        $this->jsonResponse([
          'success' => true,
          'message' => 'Konten tentang kami dan kegiatan berhasil diperbarui.'
        ]);
      } else {
        // Jika gagal save, hapus file yang baru diupload
        $this->rollbackUploadedFiles($uploadedFiles);

        $this->jsonResponse([
          'success' => false,
          'message' => 'Gagal memperbarui konten.'
        ], 500);
      }
    } catch (Exception $e) {
      // Jika ada exception, hapus file yang baru diupload
      $this->rollbackUploadedFiles($uploadedFiles);

      error_log("Update About Error: " . $e->getMessage());
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

  // Helper method to add content if set
  private function addContentIfSet(&$contentData, $key, $value)
  {
    if (isset($value)) {
      $contentData[$key] = [
        'type' => $this->getContentType($key),
        'value' => trim($value)
      ];
    }
  }

  // Determine content type based on key
  private function getContentType($key)
  {
    $textareaKeys = [
      'about_profile_description',
      'about_vision_content',
      'about_mission_content',
      'activity_1_description',
      'activity_2_description',
      'activity_3_description'
    ];

    return in_array($key, $textareaKeys) ? 'textarea' : 'text';
  }

  // Handle file uploads dengan management file lama yang aman
  private function handleFileUploads()
  {
    $uploadedFiles = [];
    $filesToDelete = [];

    // Header image
    if (!empty($_FILES['about_header_image']['name'])) {
      $headerImage = $this->handleFileUpload($_FILES['about_header_image'], 'about_header');
      if ($headerImage !== false) {
        $uploadedFiles['about_header_image'] = $headerImage;

        // Simpan info file lama untuk dihapus nanti setelah sukses save
        $oldHeaderImage = $this->about->getAboutContent('about_header_image');
        if ($oldHeaderImage) {
          $filesToDelete[] = [
            'path' => $oldHeaderImage,
            'type' => 'about_header'
          ];
        }
      }
    }

    // Profile image
    if (!empty($_FILES['about_profile_image']['name'])) {
      $profileImage = $this->handleFileUpload($_FILES['about_profile_image'], 'about_profile');
      if ($profileImage !== false) {
        $uploadedFiles['about_profile_image'] = $profileImage;

        // Simpan info file lama untuk dihapus nanti setelah sukses save
        $oldProfileImage = $this->about->getAboutContent('about_profile_image');
        if ($oldProfileImage) {
          $filesToDelete[] = [
            'path' => $oldProfileImage,
            'type' => 'about_profile'
          ];
        }
      }
    }

    // Activity images
    for ($i = 1; $i <= 3; $i++) {
      if (!empty($_FILES["activity_{$i}_image"]['name'])) {
        $activityImage = $this->handleFileUpload($_FILES["activity_{$i}_image"], "activity_{$i}");
        if ($activityImage !== false) {
          $uploadedFiles["activity_{$i}_image"] = $activityImage;

          // Simpan info file lama untuk dihapus nanti setelah sukses save
          $oldActivityImage = $this->about->getAboutContent("activity_{$i}_image");
          if ($oldActivityImage) {
            $filesToDelete[] = [
              'path' => $oldActivityImage,
              'type' => "activity_{$i}"
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

  // Handle single file upload - TAMBAHKAN PARAMETER $prefix
  private function handleFileUpload($file, $prefix = '')
  {
    $upload_dir = 'uploads/about/';
    $upload_path_full = __DIR__ . '/../../public/' . $upload_dir;

    // Pastikan folder ada
    if (!is_dir($upload_path_full)) {
      mkdir($upload_path_full, 0777, true);
    }

    $allowed_types = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));

    if (!in_array($ext, $allowed_types)) {
      error_log("Invalid file type for {$prefix}: {$ext}");
      return false;
    }

    // Validate file size (max 5MB)
    if ($file['size'] > 5 * 1024 * 1024) {
      error_log("File too large for {$prefix}: {$file['size']} bytes");
      return false;
    }

    // Generate unique filename dengan prefix
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

  // Delete file from server - PERBAIKI PATH HANDLING
  private function deleteFileFromServer($filename, $type = 'general')
  {
    if (empty($filename)) {
      error_log("Delete File: Filename kosong untuk {$type}");
      return true;
    }

    // Tambahkan path directory karena hanya menyimpan nama file
    $file_path = 'uploads/about/' . $filename;
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
      $alternative_path = __DIR__ . '/../../public/uploads/about/' . $filename;
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
