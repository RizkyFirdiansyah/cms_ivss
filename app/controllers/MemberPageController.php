<?php
require_once '../app/models/UserModel.php';
require_once '../app/models/ProfileModel.php';
require_once '../app/models/MemberPageModel.php';
require_once '../app/controllers/BaseController.php';

class MemberPageController extends BaseController
{
  private $conn;
  private $profile;
  private $memberPage;
  private $users;

  public function __construct()
  {
    parent::__construct();
    parent::requireLogin();

    $db = new Database();
    $this->conn = $db->getConnection();

    $this->users = new UserModel();
    $this->profile = new ProfileModel();
    $this->memberPage = new MemberPageModel();
  }

  public function index()
  {
    $page_title = 'Manajemen Halaman Member';
    $page_breadcrumb = ['Pages', 'Member'];

    include '../app/views/member-page.php';
  }

  // Get member page contents (header section)
  public function getContents()
  {
    try {
      $contents = $this->getMemberPageContents();

      $this->jsonResponse([
        'success' => true,
        'data' => $contents
      ]);
    } catch (Exception $e) {
      error_log("Get Member Contents Error: " . $e->getMessage());
      $this->jsonResponse([
        'success' => false,
        'message' => 'Gagal mengambil data konten member.'
      ], 500);
    }
  }

  // Get active members for display
  public function getActiveMembers()
  {
    try {
      // Ambil semua users dari model
      $users = $this->users->getAllUsers();

      // Filter hanya users yang aktif (is_active = 'aktif')
      $activeMembers = array_filter($users, function ($user) {
        return isset($user['is_active']) && $user['is_active'] === 'aktif';
      });

      // Reset array keys
      $activeMembers = array_values($activeMembers);

      $this->jsonResponse([
        'success' => true,
        'data' => $activeMembers
      ]);
    } catch (Exception $e) {
      error_log("Get Active Members Error: " . $e->getMessage());
      $this->jsonResponse([
        'success' => false,
        'message' => 'Gagal mengambil data member.'
      ], 500);
    }
  }
  // Update member page contents
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
    $uploadResult = $this->handleFileUploads();
    $uploadedFiles = $uploadResult['uploadedFiles'];
    $filesToDelete = $uploadResult['filesToDelete'];

    // Prepare content data
    $contentData = [];

    // Header Section
    if (isset($_POST['member_header_title'])) {
      $contentData['member_header_title'] = [
        'type' => 'text',
        'value' => trim($_POST['member_header_title'])
      ];
    }

    if (isset($_POST['member_header_subtitle'])) {
      $contentData['member_header_subtitle'] = [
        'type' => 'text',
        'value' => trim($_POST['member_header_subtitle'])
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
      $success = $this->saveMemberPageContents($contentData, $user['id']);

      if ($success) {
        // Hapus file lama hanya setelah sukses save ke database
        $this->deleteOldFiles($filesToDelete);

        $this->jsonResponse([
          'success' => true,
          'message' => 'Konten halaman member berhasil diperbarui.'
        ]);
      } else {
        // Jika gagal save, hapus file yang baru diupload
        $this->rollbackUploadedFiles($uploadedFiles);

        $this->jsonResponse([
          'success' => false,
          'message' => 'Gagal memperbarui konten member.'
        ], 500);
      }
    } catch (Exception $e) {
      // Jika ada exception, hapus file yang baru diupload
      $this->rollbackUploadedFiles($uploadedFiles);

      error_log("Update Member Error: " . $e->getMessage());
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

    // Get current member page contents
    $currentContents = $this->getMemberPageContents();

    // Header background image
    if (!empty($_FILES['member_header_image']['name'])) {
      $headerImage = $this->handleFileUpload($_FILES['member_header_image'], 'member_header');
      if ($headerImage !== false) {
        $uploadedFiles['member_header_image'] = $headerImage;

        // Simpan info file lama untuk dihapus nanti setelah sukses save
        $oldImage = $_POST['old_member_header_image'] ?? ($currentContents['member_header_image']['value'] ?? '');
        if (!empty($oldImage)) {
          $filesToDelete[] = [
            'path' => $oldImage,
            'type' => 'member_header'
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
    $upload_dir = 'uploads/member/';
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

    $new_file_name = $prefix . '_' . $this->user['id'] . '_' . time() . '.' . $ext;
    $target_path = $upload_path_full . $new_file_name;

    if (move_uploaded_file($file['tmp_name'], $target_path)) {
      return $new_file_name;
    }

    error_log("Failed to move uploaded file for {$prefix}");
    return false;
  }

  // Get member page contents from database
  private function getMemberPageContents()
  {
    return $this->memberPage->getMemberPageContents();
  }

  // Save member page contents to database
  private function saveMemberPageContents($contents, $userId)
  {
    return $this->memberPage->saveMultipleMemberContents($contents, $userId);
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

    $file_path = 'uploads/member/' . $filename;
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
