<?php
require_once '../app/controllers/BasePageController.php';
require_once '../app/models/FacilityPageModel.php';

class FacilityPageController extends BasePageController
{
  private $facilities;

  public function __construct()
  {
    parent::__construct(new FacilityPageModel(), 'uploads/facility/');
    $this->facilities = new FacilitiesModel();
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
      $header = $this->pageModel->getFacilityHeader();

      $contents = [
        'header' => $header
      ];

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

  // Update facility page contents
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
      $currentHeader = $this->pageModel->getFacilityHeader();

      // Prepare header data
      $headerData = [
        'title' => trim($_POST['facility_header_title'] ?? ''),
        'subtitle' => trim($_POST['facility_header_subtitle'] ?? ''),
        'image_path' => $uploadedFiles['facility_header_image'] ?? ($_POST['old_facility_header_image'] ?? $currentHeader['image_path'] ?? '')
      ];

      $success = $this->pageModel->saveFacilityHeader($headerData, $user['id']);

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
      if (isset($uploadResult)) {
        $this->rollbackUploadedFiles($uploadResult['uploadedFiles']);
      }

      error_log("Update Facility Error: " . $e->getMessage());
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

    // Get current facility header
    $currentHeader = $this->pageModel->getFacilityHeader();

    // Header background image
    if (!empty($_FILES['facility_header_image']['name'])) {
      $headerImage = $this->handleFileUpload($_FILES['facility_header_image'], 'facility_header');
      if ($headerImage !== false) {
        $uploadedFiles['facility_header_image'] = $headerImage;

        // Simpan info file lama untuk dihapus nanti setelah sukses save
        $oldImage = $_POST['old_facility_header_image'] ?? ($currentHeader['image_path'] ?? '');
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
