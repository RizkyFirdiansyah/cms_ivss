<?php
require_once '../app/models/SopPageModel.php';
require_once '../app/controllers/BaseController.php';

class SopPageController extends BaseController
{
  private $conn;
  private $sopPage;

  public function __construct()
  {
    parent::__construct();
    parent::requireLogin();
    parent::requireRole('kepala');

    $db = new Database();
    $this->conn = $db->getConnection();

    $this->sopPage = new SopPageModel();
  }

  public function index()
  {
    $page_title = 'Manajemen Halaman SOP';
    $page_breadcrumb = ['Pages', 'SOP'];

    include '../app/views/sop-page.php';
  }

  // Get SOP page contents (header dan main content)
  public function getContents()
  {
    try {
      $header = $this->sopPage->getSopHeader();
      $mainContent = $this->sopPage->getSopMainContent();

      $contents = [
        'header' => $header,
        'main_content' => $mainContent
      ];

      $this->jsonResponse([
        'success' => true,
        'data' => $contents
      ]);
    } catch (Exception $e) {
      error_log("Get SOP Contents Error: " . $e->getMessage());
      $this->jsonResponse([
        'success' => false,
        'message' => 'Gagal mengambil data konten SOP.'
      ], 500);
    }
  }

  // Update all SOP contents at once (sesuai dengan views)
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
      // Handle file uploads untuk semua section
      $uploadResult = $this->handleAllFileUploads();
      $uploadedFiles = $uploadResult['uploadedFiles'];
      $filesToDelete = $uploadResult['filesToDelete'];

      // Dapatkan data saat ini untuk mempertahankan gambar yang tidak diubah
      $currentHeader = $this->sopPage->getSopHeader();
      $currentMain = $this->sopPage->getSopMainContent();

      // Prepare header data - pertahankan gambar lama jika tidak ada upload baru
      $headerData = [
        'title' => trim($_POST['sop_header_title'] ?? ''),
        'subtitle' => trim($_POST['sop_header_subtitle'] ?? ''),
        'image_path' => $uploadedFiles['sop_header_image'] ?? ($_POST['old_sop_header_image'] ?? $currentHeader['image_path'] ?? '')
      ];

      // Prepare main content data - pertahankan gambar lama jika tidak ada upload baru
      $mainData = [
        'title' => trim($_POST['sop_main_title'] ?? ''),
        'content' => trim($_POST['sop_main_content'] ?? ''),
        'image_path' => $uploadedFiles['sop_main_image'] ?? ($_POST['old_sop_main_image'] ?? $currentMain['image_path'] ?? '')
      ];

      // Gunakan method yang menyimpan semua data sekaligus
      $success = $this->sopPage->saveAllSopContents($headerData, $mainData, $user['id']);

      if ($success) {
        // Hapus file lama hanya setelah sukses save ke database
        $this->deleteOldFiles($filesToDelete);

        $this->jsonResponse([
          'success' => true,
          'message' => 'Semua konten SOP berhasil diperbarui.'
        ]);
      } else {
        // Jika gagal save, hapus file yang baru diupload
        $this->rollbackUploadedFiles($uploadedFiles);

        $this->jsonResponse([
          'success' => false,
          'message' => 'Gagal memperbarui konten SOP.'
        ], 500);
      }
    } catch (Exception $e) {
      // Jika ada exception, hapus file yang baru diupload
      if (isset($uploadResult)) {
        $this->rollbackUploadedFiles($uploadResult['uploadedFiles']);
      }

      error_log("Update All SOP Contents Error: " . $e->getMessage());
      $this->jsonResponse([
        'success' => false,
        'message' => 'Terjadi kesalahan sistem: ' . $e->getMessage()
      ], 500);
    }
  }

  // Handle file uploads for all sections
  private function handleAllFileUploads()
  {
    $uploadedFiles = [];
    $filesToDelete = [];

    // Get current data untuk semua section
    $currentHeader = $this->sopPage->getSopHeader();
    $currentMain = $this->sopPage->getSopMainContent();

    // Header background image
    if (!empty($_FILES['sop_header_image']['name'])) {
      $headerImage = $this->handleFileUpload($_FILES['sop_header_image'], 'sop_header');
      if ($headerImage !== false) {
        $uploadedFiles['sop_header_image'] = $headerImage;

        // Simpan info file lama untuk dihapus nanti setelah sukses save
        $oldImage = $_POST['old_sop_header_image'] ?? ($currentHeader['image_path'] ?? '');
        if (!empty($oldImage) && $oldImage !== $headerImage) {
          $filesToDelete[] = [
            'path' => $oldImage,
            'type' => 'sop_header'
          ];
        }
      }
    }

    // Main content image
    if (!empty($_FILES['sop_main_image']['name'])) {
      $mainImage = $this->handleFileUpload($_FILES['sop_main_image'], 'sop_main');
      if ($mainImage !== false) {
        $uploadedFiles['sop_main_image'] = $mainImage;

        // Simpan info file lama untuk dihapus nanti setelah sukses save
        $oldImage = $_POST['old_sop_main_image'] ?? ($currentMain['image_path'] ?? '');
        if (!empty($oldImage) && $oldImage !== $mainImage) {
          $filesToDelete[] = [
            'path' => $oldImage,
            'type' => 'sop_main'
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
    $upload_dir = 'uploads/sop/';
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

    $file_path = 'uploads/sop/' . $filename;
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
