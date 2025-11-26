<?php
require_once '../app/controllers/BasePageController.php';
require_once '../app/models/ResearchPageModel.php';

class ResearchPageController extends BasePageController
{
  public function __construct()
  {
    parent::__construct(new ResearchPageModel(), 'uploads/research/');
  }

  public function index()
  {
    $page_title = 'Manajemen Halaman Penelitian';
    $page_breadcrumb = ['Pages', 'Penelitian'];

    include '../app/views/penelitian-page.php';
  }

  // OVERRIDE: Get all research page contents (bukan hanya header)
  public function getContents()
  {
    try {
      $contents = $this->pageModel->getResearchPageContents();

      // Structure data sesuai dengan kebutuhan views
      $structuredData = [
        'header' => [
          'title' => $contents['research_header_title']['value'] ?? 'Penelitian',
          'subtitle' => $contents['research_header_subtitle']['value'] ?? 'Proyek dan kegiatan penelitian LAB IVSS',
          'image_path' => $contents['research_header_image']['value'] ?? ''
        ]
      ];

      $this->jsonResponse([
        'success' => true,
        'data' => $structuredData
      ]);
    } catch (Exception $e) {
      error_log("Get Research Page Contents Error: " . $e->getMessage());
      $this->jsonResponse([
        'success' => false,
        'message' => 'Gagal mengambil data konten halaman penelitian.'
      ], 500);
    }
  }

  // Update research page contents - OVERRIDE method base
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
      $this->addContentIfSet($contentData, 'research_header_title', $_POST['research_header_title'] ?? '');
      $this->addContentIfSet($contentData, 'research_header_subtitle', $_POST['research_header_subtitle'] ?? '');

      // Add uploaded files to content data
      foreach ($uploadedFiles as $key => $filename) {
        if ($filename !== false) {
          $contentData[$key] = [
            'type' => 'image',
            'value' => $filename
          ];
        }
      }

      $success = $this->pageModel->saveMultipleResearchPageContents($contentData, $user['id']);

      if ($success) {
        // Hapus file lama hanya setelah sukses save ke database
        $this->deleteOldFiles($filesToDelete);

        $this->jsonResponse([
          'success' => true,
          'message' => 'Konten halaman penelitian berhasil diperbarui.'
        ]);
      } else {
        // Jika gagal save, hapus file yang baru diupload
        $this->rollbackUploadedFiles($uploadedFiles);

        $this->jsonResponse([
          'success' => false,
          'message' => 'Gagal memperbarui konten halaman penelitian.'
        ], 500);
      }
    } catch (Exception $e) {
      // Jika ada exception, hapus file yang baru diupload
      if (isset($uploadResult)) {
        $this->rollbackUploadedFiles($uploadResult['uploadedFiles']);
      }

      error_log("Update Research Page Error: " . $e->getMessage());
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
    // Semua field untuk research page adalah text
    return 'text';
  }

  // Handle file uploads dengan management file lama yang aman
  protected function handleFileUploads()
  {
    $uploadedFiles = [];
    $filesToDelete = [];

    // Header image
    if (!empty($_FILES['research_header_image']['name'])) {
      $headerImage = $this->handleFileUpload($_FILES['research_header_image'], 'research_header');
      if ($headerImage !== false) {
        $uploadedFiles['research_header_image'] = $headerImage;

        // Simpan info file lama untuk dihapus nanti setelah sukses save
        $oldHeaderImage = $this->pageModel->getResearchPageContent('research_header_image');
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