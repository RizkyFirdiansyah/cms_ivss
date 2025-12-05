<?php
require_once __DIR__ . '/../models/BasePageModel.php';
require_once __DIR__ . '/../models/AboutPageModel.php';

class HomePageModel extends BasePageModel
{
  public function __construct()
  {
    parent::__construct('home', 'Home');
  }

  // Get all home contents
  public function getHomeContents()
  {
    return $this->getPageContents();
  }

  // Save multiple home contents
  public function saveMultipleHomeContents($contents, $userId)
  {
    return $this->saveMultipleContents($contents, $userId);
  }

  // Get home content by key
  public function getHomeContent($key)
  {
    try {
      $pageId = $this->getPageId();
      if (!$pageId) return null;

      $query = "SELECT content_value FROM page_contents 
                WHERE page_id = :page_id AND content_key = :content_key";

      $stmt = $this->conn->prepare($query);
      $stmt->bindValue(':page_id', $pageId, PDO::PARAM_INT);
      $stmt->bindValue(':content_key', $key, PDO::PARAM_STR);
      $stmt->execute();

      $result = $stmt->fetch(PDO::FETCH_ASSOC);
      return $result ? $result['content_value'] : null;
    } catch (PDOException $e) {
      error_log("DB Error (getHomeContent): " . $e->getMessage());
      return null;
    }
  }

  // Get activities for home (from about page)
  public function getActivitiesForHome()
  {
    try {
      $activities = [];

      // Get about page contents
      $aboutModel = new AboutPageModel();
      $aboutContents = $aboutModel->getAboutContents();

      for ($i = 1; $i <= 3; $i++) {
        $title = $aboutContents["activity_{$i}_title"]['value'] ?? '';
        $description = $aboutContents["activity_{$i}_description"]['value'] ?? '';
        $image = $aboutContents["activity_{$i}_image"]['value'] ?? '';

        // Only include activities that have at least a title
        if (!empty($title)) {
          $activities[] = [
            'title' => $title,
            'description' => $description,
            'image' => $image,
            'order' => $i
          ];
        }
      }

      return $activities;
    } catch (PDOException $e) {
      error_log("DB Error (getActivitiesForHome): " . $e->getMessage());
      return [];
    }
  }

  // Get home header data
  public function getHomeHeader()
  {
    try {
      $contents = $this->getPageContents();

      return [
        'title' => $contents['hero_title']['value'] ?? 'Selamat Datang di LAB IVSS',
        'subtitle' => $contents['hero_subtitle']['value'] ?? 'Laboratorium Inovasi dan Visual Sistem Cerdas',
        'image_path' => $contents['hero_image']['value'] ?? ''
      ];
    } catch (PDOException $e) {
      error_log("DB Error (getHomeHeader): " . $e->getMessage());
      return [
        'title' => 'Selamat Datang di LAB IVSS',
        'subtitle' => 'Laboratorium Inovasi dan Visual Sistem Cerdas',
        'image_path' => ''
      ];
    }
  }

  // Implement abstract method dari BasePageModel
  public function getHeader()
  {
    return $this->getHomeHeader();
  }

  public function saveHeader($headerData, $userId)
  {
    $contents = [
      'hero_title' => ['type' => 'text', 'value' => $headerData['title'] ?? ''],
      'hero_subtitle' => ['type' => 'text', 'value' => $headerData['subtitle'] ?? ''],
      'hero_image' => ['type' => 'image', 'value' => $headerData['image_path'] ?? '']
    ];

    return $this->saveMultipleContents($contents, $userId);
  }
}
