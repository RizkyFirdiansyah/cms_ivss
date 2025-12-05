<?php
require_once '../app/controllers/BasePageController.php';
require_once '../app/models/SopPageModel.php';

class SopPageController extends BasePageController
{
  public function __construct()
  {
    parent::__construct(new SopPageModel(), 'uploads/sop/');
  }

  public function index()
  {
    $page_title = 'Manajemen Halaman SOP';
    $page_breadcrumb = ['Pages', 'SOP'];

    include '../app/views/sop-page.php';
  }

  // OVERRIDE: Get all SOP contents (header dan SOP items)
  public function getContents()
  {
    try {
      $contents = $this->pageModel->getSopContents();

      // Structure data sesuai dengan kebutuhan views
      $structuredData = [
        'header' => [
          'title' => $contents['sop_header_title']['value'] ?? 'Standard Operating Procedure',
          'subtitle' => $contents['sop_header_subtitle']['value'] ?? 'Prosedur operasional standar laboratorium',
          'image_path' => $contents['sop_header_image']['value'] ?? ''
        ],
        'layanan' => [
          'location' => $contents['sop_layanan_location']['value'] ?? '',
          'email' => $contents['sop_layanan_email']['value'] ?? '',
          'hours' => $contents['sop_layanan_hours']['value'] ?? ''
        ],
        'sop_items' => []
      ];

      // Add SOP items
      for ($i = 1; $i <= 3; $i++) {
        $structuredData['sop_items'][] = [
          'title' => $contents["sop_{$i}_title"]['value'] ?? '',
          'description' => $contents["sop_{$i}_description"]['value'] ?? '',
          'document_link' => $contents["sop_{$i}_document_link"]['value'] ?? ''
        ];
      }

      $this->jsonResponse([
        'success' => true,
        'data' => $structuredData
      ]);
    } catch (Exception $e) {
      error_log("Get SOP Contents Error: " . $e->getMessage());
      $this->jsonResponse([
        'success' => false,
        'message' => 'Gagal mengambil data konten SOP.'
      ], 500);
    }
  }

  // Update SOP contents - OVERRIDE method base
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
      $this->addContentIfSet($contentData, 'sop_header_title', $_POST['sop_header_title'] ?? '');
      $this->addContentIfSet($contentData, 'sop_header_subtitle', $_POST['sop_header_subtitle'] ?? '');

      // Layanan Section
      $this->addContentIfSet($contentData, 'sop_layanan_location', $_POST['sop_layanan_location'] ?? '');
      $this->addContentIfSet($contentData, 'sop_layanan_email', $_POST['sop_layanan_email'] ?? '');
      $this->addContentIfSet($contentData, 'sop_layanan_hours', $_POST['sop_layanan_hours'] ?? '');

      // SOP Items - 3 items
      for ($i = 1; $i <= 3; $i++) {
        $this->addContentIfSet($contentData, "sop_{$i}_title", $_POST["sop_{$i}_title"] ?? '');
        $this->addContentIfSet($contentData, "sop_{$i}_description", $_POST["sop_{$i}_description"] ?? '');
        $this->addContentIfSet($contentData, "sop_{$i}_document_link", $_POST["sop_{$i}_document_link"] ?? '');
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

      $success = $this->pageModel->saveMultipleSopContents($contentData, $user['id']);

      if ($success) {
        // Hapus file lama hanya setelah sukses save ke database
        $this->deleteOldFiles($filesToDelete);

        $this->jsonResponse([
          'success' => true,
          'message' => 'Konten SOP berhasil diperbarui.'
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

      error_log("Update SOP Error: " . $e->getMessage());
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
    $textareaKeys = [
      'sop_1_description',
      'sop_2_description',
      'sop_3_description'
    ];

    return in_array($key, $textareaKeys) ? 'textarea' : 'text';
  }

  // Handle file uploads dengan management file lama yang aman
  protected function handleFileUploads()
  {
    $uploadedFiles = [];
    $filesToDelete = [];

    // Header image
    if (!empty($_FILES['sop_header_image']['name'])) {
      $headerImage = $this->handleFileUpload($_FILES['sop_header_image'], 'sop_header');
      if ($headerImage !== false) {
        $uploadedFiles['sop_header_image'] = $headerImage;

        // Simpan info file lama untuk dihapus nanti setelah sukses save
        $oldHeaderImage = $this->pageModel->getSopContent('sop_header_image');
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
