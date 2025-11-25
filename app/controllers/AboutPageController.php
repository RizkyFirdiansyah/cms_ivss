<?php
require_once '../app/controllers/BasePageController.php';
require_once '../app/models/AboutPageModel.php';
require_once '../app/models/GalleryModel.php';

class AboutPageController extends BasePageController
{
  private $gallery;

  public function __construct()
  {
    parent::__construct(new AboutPageModel(), 'uploads/about/');
    $this->gallery = new GalleryModel();
  }

  public function index()
  {
    $page_title = 'Manajemen Tentang Kami';
    $page_breadcrumb = ['Pages', 'Tentang Kami'];

    include '../app/views/about-page.php';
  }

  // OVERRIDE: Get all about contents (bukan hanya header)
  public function getContents()
  {
    try {
      $contents = $this->pageModel->getAboutContents();

      // Structure data sesuai dengan kebutuhan views
      $structuredData = [
        'header' => [
          'title' => $contents['about_header_title']['value'] ?? 'Tentang LAB IVSS',
          'subtitle' => $contents['about_header_subtitle']['value'] ?? 'Mengenal lebih dekat laboratorium kami',
          'image_path' => $contents['about_header_image']['value'] ?? ''
        ],
        'profile' => [
          'description' => $contents['about_profile_description']['value'] ?? '',
          'image_path' => $contents['about_profile_image']['value'] ?? ''
        ],
        'vision_mission' => [
          'vision_title' => $contents['about_vision_title']['value'] ?? '',
          'vision_content' => $contents['about_vision_content']['value'] ?? '',
          'mission_title' => $contents['about_mission_title']['value'] ?? '',
          'mission_content' => $contents['about_mission_content']['value'] ?? ''
        ],
        'activities' => [
          'title' => $contents['activities_title']['value'] ?? '',
          'subtitle' => $contents['activities_subtitle']['value'] ?? '',
          'items' => []
        ],
        'gallery' => [
          'title' => $contents['gallery_title']['value'] ?? '',
          'subtitle' => $contents['gallery_subtitle']['value'] ?? ''
        ]
      ];

      // Add activity items
      for ($i = 1; $i <= 3; $i++) {
        $structuredData['activities']['items'][] = [
          'title' => $contents["activity_{$i}_title"]['value'] ?? '',
          'description' => $contents["activity_{$i}_description"]['value'] ?? '',
          'image_path' => $contents["activity_{$i}_image"]['value'] ?? ''
        ];
      }

      $this->jsonResponse([
        'success' => true,
        'data' => $structuredData
      ]);
    } catch (Exception $e) {
      error_log("Get About Contents Error: " . $e->getMessage());
      $this->jsonResponse([
        'success' => false,
        'message' => 'Gagal mengambil data konten tentang kami.'
      ], 500);
    }
  }

  // Get gallery images for preview - method khusus About
  public function getGallery()
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

  // Update about contents - OVERRIDE method base
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
      $this->addContentIfSet($contentData, 'about_header_title', $_POST['about_header_title'] ?? '');
      $this->addContentIfSet($contentData, 'about_header_subtitle', $_POST['about_header_subtitle'] ?? '');

      // Profile Section
      $this->addContentIfSet($contentData, 'about_profile_description', $_POST['about_profile_description'] ?? '');

      // Vision & Mission Section
      $this->addContentIfSet($contentData, 'about_vision_title', $_POST['about_vision_title'] ?? '');
      $this->addContentIfSet($contentData, 'about_vision_content', $_POST['about_vision_content'] ?? '');
      $this->addContentIfSet($contentData, 'about_mission_title', $_POST['about_mission_title'] ?? '');
      $this->addContentIfSet($contentData, 'about_mission_content', $_POST['about_mission_content'] ?? '');

      // Activities Section - SAVE AS GLOBAL ACTIVITIES
      $this->addContentIfSet($contentData, 'activities_title', $_POST['activities_title'] ?? '');
      $this->addContentIfSet($contentData, 'activities_subtitle', $_POST['activities_subtitle'] ?? '');

      // Activities items - SAVE AS GLOBAL (used by both home and about)
      for ($i = 1; $i <= 3; $i++) {
        $this->addContentIfSet($contentData, "activity_{$i}_title", $_POST["activity_{$i}_title"] ?? '');
        $this->addContentIfSet($contentData, "activity_{$i}_description", $_POST["activity_{$i}_description"] ?? '');
      }

      // Gallery Section
      $this->addContentIfSet($contentData, 'gallery_title', $_POST['gallery_title'] ?? '');
      $this->addContentIfSet($contentData, 'gallery_subtitle', $_POST['gallery_subtitle'] ?? '');

      // Add uploaded files to content data
      foreach ($uploadedFiles as $key => $filename) {
        if ($filename !== false) {
          $contentData[$key] = [
            'type' => 'text',
            'value' => $filename
          ];
        }
      }

      $success = $this->pageModel->saveMultipleAboutContents($contentData, $user['id']);

      if ($success) {
        // Hapus file lama hanya setelah sukses save ke database
        $this->deleteOldFiles($filesToDelete);

        $this->jsonResponse([
          'success' => true,
          'message' => 'Konten tentang kami dan kegiatan berhasil diperbarui.'
        ]);
      } else {
        // Jika gagal save, hapus file yang baru diupload
        $this->rollbackUploadedFiles($uploadedFiles);

        $this->jsonResponse([
          'success' => false,
          'message' => 'Gagal memperbarui konten.'
        ], 500);
      }
    } catch (Exception $e) {
      // Jika ada exception, hapus file yang baru diupload
      if (isset($uploadResult)) {
        $this->rollbackUploadedFiles($uploadResult['uploadedFiles']);
      }

      error_log("Update About Error: " . $e->getMessage());
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
      'about_profile_description',
      'about_vision_content',
      'about_mission_content',
      'activity_1_description',
      'activity_2_description',
      'activity_3_description'
    ];

    return in_array($key, $textareaKeys) ? 'textarea' : 'text';
  }

  // Handle file uploads dengan management file lama yang aman
  protected function handleFileUploads()
  {
    $uploadedFiles = [];
    $filesToDelete = [];

    // Header image
    if (!empty($_FILES['about_header_image']['name'])) {
      $headerImage = $this->handleFileUpload($_FILES['about_header_image'], 'about_header');
      if ($headerImage !== false) {
        $uploadedFiles['about_header_image'] = $headerImage;

        // Simpan info file lama untuk dihapus nanti setelah sukses save
        $oldHeaderImage = $this->pageModel->getAboutContent('about_header_image');
        if ($oldHeaderImage && $oldHeaderImage !== $headerImage) {
          $filesToDelete[] = $oldHeaderImage;
        }
      }
    }

    // Profile image
    if (!empty($_FILES['about_profile_image']['name'])) {
      $profileImage = $this->handleFileUpload($_FILES['about_profile_image'], 'about_profile');
      if ($profileImage !== false) {
        $uploadedFiles['about_profile_image'] = $profileImage;

        // Simpan info file lama untuk dihapus nanti setelah sukses save
        $oldProfileImage = $this->pageModel->getAboutContent('about_profile_image');
        if ($oldProfileImage && $oldProfileImage !== $profileImage) {
          $filesToDelete[] = $oldProfileImage;
        }
      }
    }

    // Activity images
    for ($i = 1; $i <= 3; $i++) {
      if (!empty($_FILES["activity_{$i}_image"]['name'])) {
        $activityImage = $this->handleFileUpload($_FILES["activity_{$i}_image"], "activity_{$i}");
        if ($activityImage !== false) {
          $uploadedFiles["activity_{$i}_image"] = $activityImage;

          // Simpan info file lama untuk dihapus nanti setelah sukses save
          $oldActivityImage = $this->pageModel->getAboutContent("activity_{$i}_image");
          if ($oldActivityImage && $oldActivityImage !== $activityImage) {
            $filesToDelete[] = $oldActivityImage;
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
