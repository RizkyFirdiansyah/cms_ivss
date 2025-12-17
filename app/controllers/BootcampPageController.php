<?php
require_once '../app/controllers/BasePageController.php';
require_once '../app/models/BootcampPageModel.php';

class BootcampPageController extends BasePageController
{
  public function __construct()
  {
    parent::__construct(new BootcampPageModel(), 'uploads/bootcamp-page/');
  }

  public function index()
  {
    $page_title = 'Manajemen Halaman Bootcamp';
    $page_breadcrumb = ['Pages', 'Bootcamp'];

    include '../app/views/bootcamp-page.php';
  }

  // OVERRIDE: Get all bootcamp page contents
  public function getContents()
  {
    try {
      $contents = $this->pageModel->getBootcampPageContents();

      // Structure data sesuai dengan kebutuhan views
      $structuredData = [
        'header' => [
          'title' => $contents['bootcamp_header_title']['value'] ?? 'Bootcamp',
          'subtitle' => $contents['bootcamp_header_subtitle']['value'] ?? 'Program pelatihan intensif dan workshop',
          'image_path' => $contents['bootcamp_header_image']['value'] ?? ''
        ]
      ];

      $this->jsonResponse([
        'success' => true,
        'data' => $structuredData
      ]);
    } catch (Exception $e) {
      error_log("Get Bootcamp Page Contents Error: " . $e->getMessage());
      $this->jsonResponse([
        'success' => false,
        'message' => 'Gagal mengambil data konten halaman bootcamp.'
      ], 500);
    }
  }

  // Update bootcamp page contents - OVERRIDE method base
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
      // Handle file uploads first - dapatkan file baru dan info file lama
      $uploadResult = $this->handleFileUploads();
      $uploadedFiles = $uploadResult['uploadedFiles'];
      $filesToDelete = $uploadResult['filesToDelete'];

      // Prepare content data
      $contentData = [];

      // Header Section
      $this->addContentIfSet($contentData, 'bootcamp_header_title', $_POST['bootcamp_header_title'] ?? '');
      $this->addContentIfSet($contentData, 'bootcamp_header_subtitle', $_POST['bootcamp_header_subtitle'] ?? '');

      // Add uploaded files to content data
      foreach ($uploadedFiles as $key => $filename) {
        if ($filename !== false) {
          $contentData[$key] = [
            'type' => 'image',
            'value' => $filename
          ];
        }
      }

      $success = $this->pageModel->saveMultipleBootcampPageContents($contentData, $user['id']);

      if ($success) {
        // Hapus file lama hanya setelah sukses save ke database
        $this->deleteOldFiles($filesToDelete);

        $this->jsonResponse([
          'success' => true,
          'message' => 'Konten halaman bootcamp berhasil diperbarui.'
        ]);
      } else {
        // Jika gagal save, hapus file yang baru diupload
        $this->rollbackUploadedFiles($uploadedFiles);

        $this->jsonResponse([
          'success' => false,
          'message' => 'Gagal memperbarui konten halaman bootcamp.'
        ], 500);
      }
    } catch (Exception $e) {
      // Jika ada exception, hapus file yang baru diupload
      if (isset($uploadResult)) {
        $this->rollbackUploadedFiles($uploadResult['uploadedFiles']);
      }

      error_log("Update Bootcamp Page Error: " . $e->getMessage());
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
    // Semua field untuk bootcamp page adalah text kecuali image
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

    // Header image
    if (!empty($_FILES['bootcamp_header_image']['name'])) {
      $headerImage = $this->handleFileUpload($_FILES['bootcamp_header_image'], 'bootcamp_header');
      if ($headerImage !== false) {
        $uploadedFiles['bootcamp_header_image'] = $headerImage;

        // Simpan info file lama untuk dihapus nanti setelah sukses save
        $oldHeaderImage = $this->pageModel->getBootcampPageContent('bootcamp_header_image');
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
}