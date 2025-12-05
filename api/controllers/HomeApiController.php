<?php
require_once __DIR__ . '/ApiBaseController.php';
require_once __DIR__ . '/../../app/models/HomePageModel.php';
require_once __DIR__ . '/../../app/models/GalleryModel.php';

class HomeApiController extends ApiBaseController
{
  private $homePageModel;
  private $galleryModel;

  public function __construct()
  {
    // parent::__construct();`
    $this->homePageModel = new HomePageModel();
    $this->galleryModel = new GalleryModel();
  }

  public function index()
  {
    try {
      // Get all home page data
      $contents = $this->homePageModel->getHomeContents();
      $activities = $this->homePageModel->getActivitiesForHome();
      $gallery = $this->galleryModel->getRecentGallery();

      // Prepare response
      $response = [
        'hero' => [
          'title' => $contents['hero_title']['value'] ?? 'Selamat Datang di LAB IVSS',
          'subtitle' => $contents['hero_subtitle']['value'] ?? 'Laboratorium Inovasi dan Visual Sistem Cerdas',
          'image_path' => $contents['hero_image']['value'] ?? ''
        ],
        'profile' => [
          'description' => $contents['profile_description']['value'] ?? '',
          'images' => [
            'image_1' => $contents['profile_image_1']['value'] ?? '',
            'image_2' => $contents['profile_image_2']['value'] ?? '',
            'image_3' => $contents['profile_image_3']['value'] ?? ''
          ]
        ],
        'activities' => $activities,
        'gallery_preview' => $gallery,
        'meta' => [
          'generated_at' => date('c'),
          'version' => '1.0'
        ]
      ];

      return $this->sendSuccess($response, 'Home page data retrieved successfully');
    } catch (Exception $e) {
      return $this->sendError($e->getMessage(), 500);
    }
  }

  public function hero()
  {
    try {
      // Get hero section data
      $contents = $this->homePageModel->getHomeContents();

      $response = [
        'title' => $contents['hero_title']['value'] ?? 'Selamat Datang di LAB IVSS',
        'subtitle' => $contents['hero_subtitle']['value'] ?? 'Laboratorium Inovasi dan Visual Sistem Cerdas',
        'image_path' => $contents['hero_image']['value'] ?? ''
      ];

      return $this->sendSuccess($response, 'Hero section retrieved successfully');
    } catch (Exception $e) {
      return $this->sendError($e->getMessage(), 500);
    }
  }

  public function profile()
  {
    try {
      // Get profile section data
      $contents = $this->homePageModel->getHomeContents();

      $response = [
        'description' => $contents['profile_description']['value'] ?? '',
        'images' => [
          'image_1' => $contents['profile_image_1']['value'] ?? '',
          'image_2' => $contents['profile_image_2']['value'] ?? '',
          'image_3' => $contents['profile_image_3']['value'] ?? ''
        ]
      ];

      return $this->sendSuccess($response, 'Profile section retrieved successfully');
    } catch (Exception $e) {
      return $this->sendError($e->getMessage(), 500);
    }
  }

  public function activities()
  {
    try {
      // Get activities data
      $activities = $this->homePageModel->getActivitiesForHome();

      return $this->sendSuccess($activities, 'Activities retrieved successfully');
    } catch (Exception $e) {
      return $this->sendError($e->getMessage(), 500);
    }
  }

  public function gallery()
  {
    try {
      // Get gallery data
      $gallery = $this->galleryModel->getRecentGallery();

      return $this->sendSuccess($gallery, 'Gallery images retrieved successfully');
    } catch (Exception $e) {
      return $this->sendError($e->getMessage(), 500);
    }
  }

  // Optional: Get all sections in one endpoint (kept for backward compatibility)
  public function all()
  {
    return $this->index();
  }
}
