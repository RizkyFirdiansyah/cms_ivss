<?php
require_once '../app/models/GaleriPageModel.php';
require_once '../app/controllers/BaseController.php';

class GalleryPageController extends BaseController
{
  private $conn;
  private $galleryPage;

  public function __construct()
  {
    parent::__construct();
    parent::requireLogin();
    parent::requireRole('kepala');

    $db = new Database();
    $this->conn = $db->getConnection();

    $this->galleryPage = new GalleryPageModel();
  }

  public function index()
  {
    $page_title = 'Manajemen Halaman Galeri';
    $page_breadcrumb = ['Pages', 'Galeri'];

    include '../app/views/galeri-page.php';
  }

  // Get gallery page contents (header saja)
  public function getContents()
  {
    try {
      $header = $this->galleryPage->getGalleryHeader();

      $contents = [
        'header' => $header
      ];

      $this->jsonResponse([
        'success' => true,
        'data' => $contents
      ]);
    } catch (Exception $e) {
      error_log("Get Gallery Contents Error: " . $e->getMessage());
      $this->jsonResponse([
        'success' => false,
        'message' => 'Gagal mengambil data konten galeri.'
      ], 500);
    }
  }

  // Update gallery header
  public function update()
  {
    $user = $this->user;

    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
      return $this->jsonResponse([
        'success' => false,
        'message' => 'Method tidak diizinkan.'
      ], 405);
    }

    try {
      // Handle file upload
      $uploadResult = $this->handleHeaderFileUpload();
      $uploadedFiles = $uploadResult['uploadedFiles'];
      $filesToDelete = $uploadResult['filesToDelete'];

      // Prepare header data
      $headerData = [
        'title' => trim($_POST['gallery_header_title'] ?? ''),
        'subtitle' => trim($_POST['gallery_header_subtitle'] ?? ''),
        'image_path' => $uploadedFiles['gallery_header_image'] ?? ($_POST['old_gallery_header_image'] ?? '')
      ];

      $success = $this->galleryPage->saveGalleryHeader($headerData, $user['id']);

      if ($success) {
        // Hapus file lama hanya setelah sukses save ke database
        $this->deleteOldFiles($filesToDelete);

        $this->jsonResponse([
          'success' => true,
          'message' => 'Header galeri berhasil diperbarui.'
        ]);
      } else {
        // Jika gagal save, hapus file yang baru diupload
        $this->rollbackUploadedFiles($uploadedFiles);

        $this->jsonResponse([
          'success' => false,
          'message' => 'Gagal memperbarui header galeri.'
        ], 500);
      }
    } catch (Exception $e) {
      // Jika ada exception, hapus file yang baru diupload
      if (isset($uploadResult)) {
        $this->rollbackUploadedFiles($uploadResult['uploadedFiles']);
      }

      error_log("Update Gallery Header Error: " . $e->getMessage());
      $this->jsonResponse([
        'success' => false,
        'message' => 'Terjadi kesalahan sistem: ' . $e->getMessage()
      ], 500);
    }
  }

  // Handle file upload for header
  private function handleHeaderFileUpload()
  {
    $uploadedFiles = [];
    $filesToDelete = [];

    // Get current gallery header
    $currentHeader = $this->galleryPage->getGalleryHeader();

    // Header background image
    if (!empty($_FILES['gallery_header_image']['name'])) {
      $headerImage = $this->handleFileUpload($_FILES['gallery_header_image'], 'gallery_header');
      if ($headerImage !== false) {
        $uploadedFiles['gallery_header_image'] = $headerImage;

        // Simpan info file lama untuk dihapus nanti setelah sukses save
        $oldImage = $_POST['old_gallery_header_image'] ?? ($currentHeader['image_path'] ?? '');
        if (!empty($oldImage)) {
          $filesToDelete[] = [
            'path' => $oldImage,
            'type' => 'gallery_header'
          ];
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
    $upload_dir = 'uploads/gallery/';
    $upload_path_full = __DIR__ . '/../../public/' . $upload_dir;

    if (!is_dir($upload_path_full)) {
      mkdir($upload_path_full, 0777, true);
    }

    $allowed_types = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));

    if (!in_array($ext, $allowed_types)) {
      error_log("Invalid file type for {$prefix}: {$ext}");
      return false;
    }

    if ($file['size'] > 5 * 1024 * 1024) {
      error_log("File too large for {$prefix}: {$file['size']} bytes");
      return false;
    }

    // Gunakan user untuk ID user yang login
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

    $file_path = 'uploads/gallery/' . $filename;
    $full_path = __DIR__ . '/../../public/' . $file_path;

    if (file_exists($full_path) && is_file($full_path)) {
      if (unlink($full_path)) {
        error_log("✅ File {$type} berhasil dihapus: " . $full_path);
        return true;
      } else {
        error_log("❌ Gagal menghapus file {$type}: " . $full_path);
        return false;
      }
    } else {
      error_log("⚠️ File {$type} tidak ditemukan: " . $full_path);
    }

    return true;
  }
}
