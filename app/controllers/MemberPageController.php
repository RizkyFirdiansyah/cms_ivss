<?php
require_once '../app/controllers/BasePageController.php';
require_once '../app/models/MemberPageModel.php';
require_once '../app/models/UserModel.php';
require_once '../app/models/ProfileModel.php';

class MemberPageController extends BasePageController
{
  private $users;
  private $profile;

  public function __construct()
  {
    parent::__construct(new MemberPageModel(), 'uploads/member/');
    $this->users = new UserModel();
    $this->profile = new ProfileModel();
  }

  public function index()
  {
    $page_title = 'Manajemen Halaman Member';
    $page_breadcrumb = ['Pages', 'Member'];

    include '../app/views/member-page.php';
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

    try {
      // Handle file uploads first
      $uploadResult = $this->handleFileUploads();
      $uploadedFiles = $uploadResult['uploadedFiles'];
      $filesToDelete = $uploadResult['filesToDelete'];

      // Dapatkan data saat ini untuk mempertahankan gambar yang tidak diubah
      $currentHeader = $this->pageModel->getMemberHeader();

      // Prepare header data
      $headerData = [
        'title' => trim($_POST['member_header_title'] ?? ''),
        'subtitle' => trim($_POST['member_header_subtitle'] ?? ''),
        'image_path' => $uploadedFiles['member_header_image'] ?? ($_POST['old_member_header_image'] ?? $currentHeader['image_path'] ?? '')
      ];

      $success = $this->pageModel->saveMemberHeader($headerData, $user['id']);

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
      if (isset($uploadResult)) {
        $this->rollbackUploadedFiles($uploadResult['uploadedFiles']);
      }

      error_log("Update Member Error: " . $e->getMessage());
      $this->jsonResponse([
        'success' => false,
        'message' => 'Terjadi kesalahan sistem: ' . $e->getMessage()
      ], 500);
    }
  }

  // Handle file uploads
  protected function handleFileUploads()
  {
    $uploadedFiles = [];
    $filesToDelete = [];

    // Get current member header
    $currentHeader = $this->pageModel->getMemberHeader();

    // Header background image
    if (!empty($_FILES['member_header_image']['name'])) {
      $headerImage = $this->handleFileUpload($_FILES['member_header_image'], 'member_header');
      if ($headerImage !== false) {
        $uploadedFiles['member_header_image'] = $headerImage;

        // Simpan info file lama untuk dihapus nanti setelah sukses save
        $oldImage = $_POST['old_member_header_image'] ?? ($currentHeader['image_path'] ?? '');
        if (!empty($oldImage) && $oldImage !== $headerImage) {
          $filesToDelete[] = $oldImage;
        }
      }
    }

    return [
      'uploadedFiles' => $uploadedFiles,
      'filesToDelete' => $filesToDelete
    ];
  }
}
