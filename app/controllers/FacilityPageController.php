<?php
require_once '../app/models/FacilitiesModel.php';
require_once '../app/models/FacilityPageModel.php';
require_once '../app/controllers/BaseController.php';

class FacilityPageController extends BaseController
{
  private $conn;
  private $facilities;
  private $facilityPage;

  public function __construct()
  {
    parent::__construct();
    parent::requireLogin();

    $db = new Database();
    $this->conn = $db->getConnection();

    $this->facilities = new FacilitiesModel();
    $this->facilityPage = new FacilityPageModel();
  }

  public function index()
  {
    $page_title = 'Manajemen Halaman Fasilitas';
    $page_breadcrumb = ['Pages', 'Fasilitas'];

    include '../app/views/fasilitas-page.php';
  }

  // Get facility page contents (header section)
  public function getContents()
  {
    try {
      $contents = $this->getFacilityPageContents();

      $this->jsonResponse([
        'success' => true,
        'data' => $contents
      ]);
    } catch (Exception $e) {
      error_log("Get Facility Contents Error: " . $e->getMessage());
      $this->jsonResponse([
        'success' => false,
        'message' => 'Gagal mengambil data konten fasilitas.'
      ], 500);
    }
  }

  // Get all facilities for display
  public function getAllFacilities()
  {
    try {
      $facilities = $this->facilities->getAll();

      $this->jsonResponse([
        'success' => true,
        'data' => $facilities,
        'message' => "Halo sukses"
      ]);
    } catch (Exception $e) {
      error_log("Get Facilities Error: " . $e->getMessage());
      $this->jsonResponse([
        'success' => false,
        'message' => 'Gagal mengambil data fasilitas.'
      ], 500);
    }
  }

  // Update facility page contents
  public function update()
  {
    $user = $this->user; // Gunakan user dari BaseController

    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
      return $this->jsonResponse([
        'success' => false,
        'message' => 'Method tidak diizinkan.'
      ], 405);
    }

    // Handle file uploads first
    $uploadResult = $this->handleFileUploads();
    $uploadedFiles = $uploadResult['uploadedFiles'];
    $filesToDelete = $uploadResult['filesToDelete'];

    // Prepare content data
    $contentData = [];

    // Header Section
    if (isset($_POST['facility_header_title'])) {
      $contentData['facility_header_title'] = [
        'type' => 'text',
        'value' => trim($_POST['facility_header_title'])
      ];
    }

    if (isset($_POST['facility_header_subtitle'])) {
      $contentData['facility_header_subtitle'] = [
        'type' => 'text',
        'value' => trim($_POST['facility_header_subtitle'])
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
      $success = $this->saveFacilityPageContents($contentData, $user['id']);

      if ($success) {
        // Hapus file lama hanya setelah sukses save ke database
        $this->deleteOldFiles($filesToDelete);

        $this->jsonResponse([
          'success' => true,
          'message' => 'Konten halaman fasilitas berhasil diperbarui.'
        ]);
      } else {
        // Jika gagal save, hapus file yang baru diupload
        $this->rollbackUploadedFiles($uploadedFiles);

        $this->jsonResponse([
          'success' => false,
          'message' => 'Gagal memperbarui konten fasilitas.'
        ], 500);
      }
    } catch (Exception $e) {
      // Jika ada exception, hapus file yang baru diupload
      $this->rollbackUploadedFiles($uploadedFiles);

      error_log("Update Facility Error: " . $e->getMessage());
      $this->jsonResponse([
        'success' => false,
        'message' => 'Terjadi kesalahan sistem: ' . $e->getMessage()
      ], 500);
    }
  }

  // Handle file uploads
  private function handleFileUploads()
  {
    $uploadedFiles = [];
    $filesToDelete = [];

    // Get current facility page contents
    $currentContents = $this->getFacilityPageContents();

    // Header background image
    if (!empty($_FILES['facility_header_image']['name'])) {
      $headerImage = $this->handleFileUpload($_FILES['facility_header_image'], 'facility_header');
      if ($headerImage !== false) {
        $uploadedFiles['facility_header_image'] = $headerImage;

        // Simpan info file lama untuk dihapus nanti setelah sukses save
        $oldImage = $_POST['old_facility_header_image'] ?? ($currentContents['facility_header_image']['value'] ?? '');
        if (!empty($oldImage)) {
          $filesToDelete[] = [
            'path' => $oldImage,
            'type' => 'facility_header'
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
    $upload_dir = 'uploads/facility/';
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

  // Get facility page contents from database
  private function getFacilityPageContents()
  {
    return $this->facilityPage->getFacilityPageContents();
  }

  // Save facility page contents to database
  private function saveFacilityPageContents($contents, $userId)
  {
    return $this->facilityPage->saveMultipleFacilityContents($contents, $userId);
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

    $file_path = 'uploads/facility/' . $filename;
    $full_path = __DIR__ . '/../../public/' . $file_path;

    error_log("Delete File Attempt: {$type}");
    error_log("Filename: {$filename}");
    error_log("Full Path: {$full_path}");

    if (file_exists($full_path) && is_file($full_path)) {
      if (unlink($full_path)) {
        error_log("✅ File {$type} berhasil dihapus: " . $full_path);
        return true;
      } else {
        error_log("❌ Gagal menghapus file {$type} (Izin Ditolak): " . $full_path);
        return false;
      }
    } else {
      error_log("⚠️ File {$type} tidak ditemukan: " . $full_path);
    }

    return true;
  }
}
