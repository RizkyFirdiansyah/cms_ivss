<?php
require_once '../app/controllers/BasePageController.php';
require_once '../app/models/HomePageModel.php';
require_once '../app/models/GalleryModel.php';

class HomePageController extends BasePageController
{
  private $gallery;

  public function __construct()
  {
    parent::__construct(new HomePageModel(), 'uploads/home/');
    $this->gallery = new GalleryModel();
  }

  public function index()
  {
    $page_title = 'Manajemen Home';
    $page_breadcrumb = ['Pages', 'Home'];

    include '../app/views/home-page.php';
  }

  // OVERRIDE: Get all home contents (bukan hanya header)
  public function getContents()
  {
    try {
      $contents = $this->pageModel->getHomeContents();

      // Structure data sesuai dengan kebutuhan views
      $structuredData = [
        'hero' => [
          'title' => $contents['hero_title']['value'] ?? 'Selamat Datang di LAB IVSS',
          'subtitle' => $contents['hero_subtitle']['value'] ?? 'Laboratorium Inovasi dan Visual Sistem Cerdas',
          'image_path' => $contents['hero_image']['value'] ?? ''
        ],
        'profile' => [
          'description' => $contents['profile_description']['value'] ?? ''
        ],
        'profile_images' => [
          'image_1' => $contents['profile_image_1']['value'] ?? '',
          'image_2' => $contents['profile_image_2']['value'] ?? '',
          'image_3' => $contents['profile_image_3']['value'] ?? ''
        ]
      ];

      $this->jsonResponse([
        'success' => true,
        'data' => $structuredData
      ]);
    } catch (Exception $e) {
      error_log("Get Home Contents Error: " . $e->getMessage());
      $this->jsonResponse([
        'success' => false,
        'message' => 'Gagal mengambil data konten home.'
      ], 500);
    }
  }

  // Get activities preview for home management
  public function getActivitiesPreview()
  {
    try {
      $activities = $this->pageModel->getActivitiesForHome();

      $this->jsonResponse([
        'success' => true,
        'data' => $activities
      ]);
    } catch (Exception $e) {
      error_log("Get Activities Preview Error: " . $e->getMessage());
      $this->jsonResponse([
        'success' => false,
        'message' => 'Gagal mengambil preview kegiatan.'
      ], 500);
    }
  }

  // Get gallery images for preview
  public function getGalleryImages()
  {
    try {
      $images = $this->gallery->getRecentGallery(10);

      $this->jsonResponse([
        'success' => true,
        'data' => $images
      ]);
    } catch (Exception $e) {
      error_log("Get Gallery Images Error: " . $e->getMessage());
      $this->jsonResponse([
        'success' => false,
        'message' => 'Gagal mengambil data galeri.'
      ], 500);
    }
  }

  // Update home contents - OVERRIDE method base
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

      // Hero Section
      $this->addContentIfSet($contentData, 'hero_title', $_POST['hero_title'] ?? '');
      $this->addContentIfSet($contentData, 'hero_subtitle', $_POST['hero_subtitle'] ?? '');

      // Profile Section
      $this->addContentIfSet($contentData, 'profile_description', $_POST['profile_description'] ?? '');

      // Add uploaded files to content data
      foreach ($uploadedFiles as $key => $filename) {
        if ($filename !== false) {
          $contentData[$key] = [
            'type' => 'text',
            'value' => $filename
          ];
        }
      }

      $success = $this->pageModel->saveMultipleHomeContents($contentData, $user['id']);

      if ($success) {
        // Hapus file lama hanya setelah sukses save ke database
        $this->deleteOldFiles($filesToDelete);

        $this->jsonResponse([
          'success' => true,
          'message' => 'Konten home berhasil diperbarui.'
        ]);
      } else {
        // Jika gagal save, hapus file yang baru diupload
        $this->rollbackUploadedFiles($uploadedFiles);

        $this->jsonResponse([
          'success' => false,
          'message' => 'Gagal memperbarui konten home.'
        ], 500);
      }
    } catch (Exception $e) {
      // Jika ada exception, hapus file yang baru diupload
      if (isset($uploadResult)) {
        $this->rollbackUploadedFiles($uploadResult['uploadedFiles']);
      }

      error_log("Update Home Error: " . $e->getMessage());
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
      'profile_description'
    ];

    return in_array($key, $textareaKeys) ? 'textarea' : 'text';
  }

  // Handle file uploads dengan management file lama yang aman
  protected function handleFileUploads()
  {
    $uploadedFiles = [];
    $filesToDelete = [];

    // Hero image
    if (!empty($_FILES['hero_image']['name'])) {
      $heroImage = $this->handleFileUpload($_FILES['hero_image'], 'hero');
      if ($heroImage !== false) {
        $uploadedFiles['hero_image'] = $heroImage;

        // Simpan info file lama untuk dihapus nanti setelah sukses save
        $oldHeroImage = $this->pageModel->getHomeContent('hero_image');
        if ($oldHeroImage && $oldHeroImage !== $heroImage) {
          $filesToDelete[] = $oldHeroImage;
        }
      }
    }

    // Profile images
    for ($i = 1; $i <= 3; $i++) {
      $fieldName = "profile_image_{$i}";
      if (!empty($_FILES[$fieldName]['name'])) {
        $profileImage = $this->handleFileUpload($_FILES[$fieldName], "profile_{$i}");
        if ($profileImage !== false) {
          $uploadedFiles[$fieldName] = $profileImage;

          // Simpan info file lama untuk dihapus nanti setelah sukses save
          $oldProfileImage = $this->pageModel->getHomeContent($fieldName);
          if ($oldProfileImage && $oldProfileImage !== $profileImage) {
            $filesToDelete[] = $oldProfileImage;
          }
        }
      }
    }

    return [
      'uploadedFiles' => $uploadedFiles,
      'filesToDelete' => $filesToDelete
    ];
  }
}
