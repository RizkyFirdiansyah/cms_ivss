<?php
require_once '../app/controllers/BasePageController.php';
require_once '../app/models/MemberPageModel.php';
require_once '../app/models/UserModel.php';

class MemberPageController extends BasePageController
{
  private $users;

  public function __construct()
  {
    parent::__construct(new MemberPageModel(), 'uploads/member/');
    $this->users = new UserModel();
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
      $users = $this->users->getAllUsersWithSosmed();
      $this->jsonResponse([
        'success' => true,
        'data' => $users
      ]);
    } catch (Exception $e) {
      error_log("Get Active Members Error: " . $e->getMessage());
      $this->jsonResponse([
        'success' => false,
        'message' => 'Gagal mengambil data member.'
      ], 500);
    }
  }

  // Get member page contents
  public function getContents()
  {
    try {
      $contents = $this->pageModel->getMemberContents();

      // Structure data sesuai dengan kebutuhan views
      $structuredData = [
        'header' => [
          'title' => $contents['member_header_title']['value'] ?? 'Anggota Laboratorium',
          'subtitle' => $contents['member_header_subtitle']['value'] ?? 'Tim peneliti dan staff laboratorium',
          'image_path' => $contents['member_header_image']['value'] ?? ''
        ]
      ];

      $this->jsonResponse([
        'success' => true,
        'data' => $structuredData
      ]);
    } catch (Exception $e) {
      error_log("Get Member Page Contents Error: " . $e->getMessage());
      $this->jsonResponse([
        'success' => false,
        'message' => 'Gagal mengambil data konten halaman member.'
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
      $uploadResult = $this->handleFileUploads();
      $uploadedFiles = $uploadResult['uploadedFiles'];
      $filesToDelete = $uploadResult['filesToDelete'];

      // Prepare content data
      $contentData = [];

      // Header Section
      $this->addContentIfSet($contentData, 'member_header_title', $_POST['member_header_title'] ?? '');
      $this->addContentIfSet($contentData, 'member_header_subtitle', $_POST['member_header_subtitle'] ?? '');

      // Add uploaded files to content data
      foreach ($uploadedFiles as $key => $filename) {
        if ($filename !== false) {
          $contentData[$key] = [
            'type' => 'image',
            'value' => $filename
          ];
        }
      }

      // Tambahkan existing images jika tidak ada upload baru
      if (!isset($contentData['member_header_image']) && isset($_POST['old_member_header_image'])) {
        $contentData['member_header_image'] = [
          'type' => 'image',
          'value' => trim($_POST['old_member_header_image'])
        ];
      }

      $success = $this->pageModel->saveMultipleMemberContents($contentData, $user['id']);

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
          'message' => 'Gagal memperbarui konten halaman member.'
        ], 500);
      }
    } catch (Exception $e) {
      // Jika ada exception, hapus file yang baru diupload
      if (isset($uploadResult)) {
        $this->rollbackUploadedFiles($uploadResult['uploadedFiles']);
      }

      error_log("Update Member Page Error: " . $e->getMessage());
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
        'type' => $this->getContentType($key),
        'value' => trim($value)
      ];
    }
  }

  // Determine content type based on key
  private function getContentType($key)
  {
    // Check if key contains 'image' for image type
    if (strpos($key, 'image') !== false) {
      return 'image';
    }
    return 'text';
  }

  // Handle file uploads dengan management file lama yang aman
  protected function handleFileUploads()
  {
    $uploadedFiles = [];
    $filesToDelete = [];

    // Member header image
    if (!empty($_FILES['member_header_image']['name'])) {
      $headerImage = $this->handleFileUpload($_FILES['member_header_image'], 'member_header');
      if ($headerImage !== false) {
        $uploadedFiles['member_header_image'] = $headerImage;

        // Simpan info file lama untuk dihapus nanti setelah sukses save
        $oldHeaderImage = $this->pageModel->getMemberContent('member_header_image');
        if ($oldHeaderImage && $oldHeaderImage !== $headerImage) {
          $filesToDelete[] = $oldHeaderImage;
        }
      }
    }

    return [
      'uploadedFiles' => $uploadedFiles,
      'filesToDelete' => $filesToDelete
    ];
  }

  // Get member content by key
  public function getContent($key)
  {
    try {
      $content = $this->pageModel->getMemberContent($key);
      $this->jsonResponse([
        'success' => true,
        'data' => $content
      ]);
    } catch (Exception $e) {
      error_log("Get Member Content Error: " . $e->getMessage());
      $this->jsonResponse([
        'success' => false,
        'message' => 'Gagal mengambil konten member.'
      ], 500);
    }
  }
}
