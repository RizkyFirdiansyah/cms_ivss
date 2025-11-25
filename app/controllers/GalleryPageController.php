<?php
require_once '../app/controllers/BasePageController.php';
require_once '../app/models/GalleryPageModel.php';

class GalleryPageController extends BasePageController
{
  public function __construct()
  {
    parent::__construct(new GaleriPageModel(), 'uploads/gallery/');
  }

  public function index()
  {
    $page_title = 'Manajemen Halaman Galeri';
    $page_breadcrumb = ['Pages', 'Galeri'];

    include '../app/views/galeri-page.php';
  }

  // Update gallery header
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
      // Handle file upload
      $uploadResult = $this->handleFileUploads();
      $uploadedFiles = $uploadResult['uploadedFiles'];
      $filesToDelete = $uploadResult['filesToDelete'];

      // Dapatkan data saat ini untuk mempertahankan gambar yang tidak diubah
      $currentHeader = $this->pageModel->getGalleryHeader();

      // Prepare header data - pertahankan gambar lama jika tidak ada upload baru
      $headerData = [
        'title' => trim($_POST['gallery_header_title'] ?? ''),
        'subtitle' => trim($_POST['gallery_header_subtitle'] ?? ''),
        'image_path' => $uploadedFiles['gallery_header_image'] ?? ($_POST['old_gallery_header_image'] ?? $currentHeader['image_path'] ?? '')
      ];

      $success = $this->pageModel->saveGalleryHeader($headerData, $user['id']);

      if ($success) {
        // Hapus file lama hanya setelah sukses save ke database
        $this->deleteOldFiles($filesToDelete);

        $this->jsonResponse([
          'success' => true,
          'message' => 'Header galeri berhasil diperbarui.'
        ]);
      } else {
        // Jika gagal save, hapus file yang baru diupload
        $this->rollbackUploadedFiles($uploadedFiles);

        $this->jsonResponse([
          'success' => false,
          'message' => 'Gagal memperbarui header galeri.'
        ], 500);
      }
    } catch (Exception $e) {
      // Jika ada exception, hapus file yang baru diupload
      if (isset($uploadResult)) {
        $this->rollbackUploadedFiles($uploadResult['uploadedFiles']);
      }

      error_log("Update Gallery Header Error: " . $e->getMessage());
      $this->jsonResponse([
        'success' => false,
        'message' => 'Terjadi kesalahan sistem: ' . $e->getMessage()
      ], 500);
    }
  }

  // Handle file upload for header
  protected function handleFileUploads()
  {
    $uploadedFiles = [];
    $filesToDelete = [];

    // Get current gallery header
    $currentHeader = $this->pageModel->getGalleryHeader();

    // Header background image
    if (!empty($_FILES['gallery_header_image']['name'])) {
      $headerImage = $this->handleFileUpload($_FILES['gallery_header_image'], 'gallery_header');
      if ($headerImage !== false) {
        $uploadedFiles['gallery_header_image'] = $headerImage;

        // Simpan info file lama untuk dihapus nanti setelah sukses save
        $oldImage = $_POST['old_gallery_header_image'] ?? ($currentHeader['image_path'] ?? '');
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
