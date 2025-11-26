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

  // Get SOP page contents (header dan main content) - OVERRIDE
  public function getContents()
  {
    try {
      $header = $this->pageModel->getSopHeader();
      $mainContent = $this->pageModel->getSopMainContent();

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

  // Update all SOP contents at once
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
      $uploadResult = $this->handleFileUploads();
      $uploadedFiles = $uploadResult['uploadedFiles'];
      $filesToDelete = $uploadResult['filesToDelete'];

      // Dapatkan data saat ini untuk mempertahankan gambar yang tidak diubah
      $currentHeader = $this->pageModel->getSopHeader();
      $currentMain = $this->pageModel->getSopMainContent();

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
      $success = $this->pageModel->saveAllSopContents($headerData, $mainData, $user['id']);

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
  protected function handleFileUploads()
  {
    $uploadedFiles = [];
    $filesToDelete = [];

    // Get current data untuk semua section
    $currentHeader = $this->pageModel->getSopHeader();
    $currentMain = $this->pageModel->getSopMainContent();

    // Header background image
    if (!empty($_FILES['sop_header_image']['name'])) {
      $headerImage = $this->handleFileUpload($_FILES['sop_header_image'], 'sop_header');
      if ($headerImage !== false) {
        $uploadedFiles['sop_header_image'] = $headerImage;

        // Simpan info file lama untuk dihapus nanti setelah sukses save
        $oldImage = $_POST['old_sop_header_image'] ?? ($currentHeader['image_path'] ?? '');
        if (!empty($oldImage) && $oldImage !== $headerImage) {
          $filesToDelete[] = $oldImage;
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
