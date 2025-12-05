<?php
require_once __DIR__ . '/ApiBaseController.php';
require_once __DIR__ . '/../../app/models/AboutPageModel.php';
require_once __DIR__ . '/../../app/models/GalleryModel.php';

class AboutApiController extends ApiBaseController
{
  private $aboutPageModel;
  private $galleryModel;

  public function __construct()
  {
    // parent::__construct();
    $this->aboutPageModel = new AboutPageModel();
    $this->galleryModel = new GalleryModel();
  }

  public function index()
  {
    try {
      // Get all about page data
      $contents = $this->aboutPageModel->getAboutContents();
      $gallery = $this->galleryModel->getRecentGallery();

      // Prepare response
      $response = [
        'header' => [
          'title' => $contents['about_header_title']['value'] ?? 'Tentang LAB IVSS',
          'subtitle' => $contents['about_header_subtitle']['value'] ?? 'Mengenal lebih dekat laboratorium kami',
          'image_path' => $contents['about_header_image']['value'] ?? ''
        ],
        'profile' => [
          'title' => $contents['profile_title']['value'] ?? 'Profil Laboratorium',
          'description' => $contents['about_profile_description']['value'] ?? '',
          'image' => $contents['about_profile_image']['value'] ?? ''
        ],
        'vision_mission' => $this->getVisionMissionData($contents),
        'activities' => $this->getActivitiesData($contents),
        'gallery_preview' => $gallery,
        'meta' => [
          'generated_at' => date('c'),
          'version' => '1.0'
        ]
      ];

      return $this->sendSuccess($response, 'Complete about page data retrieved successfully');
    } catch (Exception $e) {
      return $this->sendError($e->getMessage(), 500);
    }
  }

  public function header()
  {
    try {
      $header = $this->aboutPageModel->getHeader();
      return $this->sendSuccess($header, 'About header retrieved successfully');
    } catch (Exception $e) {
      return $this->sendError($e->getMessage(), 500);
    }
  }

  public function profile()
  {
    try {
      $contents = $this->aboutPageModel->getAboutContents();

      $response = [
        'title' => $contents['profile_title']['value'] ?? 'Profil Laboratorium',
        'description' => $contents['about_profile_description']['value'] ?? '',
        'image' => $contents['about_profile_image']['value'] ?? ''
      ];

      return $this->sendSuccess($response, 'About profile retrieved successfully');
    } catch (Exception $e) {
      return $this->sendError($e->getMessage(), 500);
    }
  }

  public function visionMission()
  {
    try {
      $contents = $this->aboutPageModel->getAboutContents();
      // echo json_encode($contents);
      // var_dump($contents);
      $visionMissionData = $this->getVisionMissionData($contents);

      return $this->sendSuccess($visionMissionData, 'Vision and mission retrieved successfully');
    } catch (Exception $e) {
      return $this->sendError($e->getMessage(), 500);
    }
  }

  public function activities()
  {
    try {
      $contents = $this->aboutPageModel->getAboutContents();
      $activities = $this->getActivitiesData($contents);

      return $this->sendSuccess($activities, 'Activities retrieved successfully');
    } catch (Exception $e) {
      return $this->sendError($e->getMessage(), 500);
    }
  }

  public function gallery()
  {
    try {
      $gallery = $this->galleryModel->getRecentGallery();
      return $this->sendSuccess($gallery, 'Gallery images retrieved successfully');
    } catch (Exception $e) {
      return $this->sendError($e->getMessage(), 500);
    }
  }

  // Helper methods
  private function getVisionMissionData($contents)
  {
    // Get mission data
    $missionContent = $contents['about_mission_content']['value'] ?? '';
    // Parse mission content into points based on line breaks
    $missionPoints = $this->splitByLineBreaks($missionContent);

    // Combine with additional mission items from separate fields
    for ($i = 1; $i <= 5; $i++) {
      $missionItem = $contents["mission_item_{$i}"]['value'] ?? '';
      if (!empty($missionItem)) {
        $parsedPoints = $this->splitByLineBreaks($missionItem);
        $missionPoints = array_merge($missionPoints, $parsedPoints);
      }
    }

    $visionMissionData = [
      'vision' => [
        'title' => $contents['about_vision_title']['value'] ?? 'Visi',
        'content' => $contents['about_vision_content']['value'] ?? ''
      ],
      'mission' => [
        'title' => $contents['about_mission_title']['value'] ?? 'Misi',
        'content' => $missionPoints // Parsed points by line breaks
      ]
    ];

    return $visionMissionData;
  }

  private function splitByLineBreaks($content)
  {
    if (empty($content)) {
      return [];
    }

    // Normalize line endings to \n
    $normalized = str_replace(["\r\n", "\r"], "\n", $content);

    // Split by line breaks
    $points = explode("\n", $normalized);

    // Trim each point and filter out empty lines
    $points = array_map('trim', $points);
    $points = array_filter($points, function ($point) {
      return $point !== '' && $point !== null;
    });

    // Re-index array
    return array_values($points);
  }

  private function getActivitiesData($contents)
  {
    $activities = [];

    for ($i = 1; $i <= 3; $i++) {
      $title = $contents["activity_{$i}_title"]['value'] ?? '';
      $description = $contents["activity_{$i}_description"]['value'] ?? '';
      $image = $contents["activity_{$i}_image"]['value'] ?? '';

      if (!empty($title)) {
        $activities[] = [
          'id' => $i,
          'title' => $title,
          'description' => $description,
          'image' => $image,
          'order' => $i
        ];
      }
    }

    return $activities;
  }
}
