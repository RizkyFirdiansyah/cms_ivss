<?php
require_once __DIR__ . '/../models/BasePageModel.php';

class BootcampPageModel extends BasePageModel
{
  public function __construct()
  {
    parent::__construct('bootcamp', 'Bootcamp');
  }

  // Get all bootcamp page contents
  public function getBootcampPageContents()
  {
    return $this->getPageContents();
  }

  // Save multiple bootcamp page contents
  public function saveMultipleBootcampPageContents($contents, $userId)
  {
    return $this->saveMultipleContents($contents, $userId);
  }

  // Get bootcamp page content by key
  public function getBootcampPageContent($key)
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
      error_log("DB Error (getBootcampPageContent): " . $e->getMessage());
      return null;
    }
  }

  // Get bootcamp page header data
  public function getBootcampPageHeader()
  {
    try {
      $contents = $this->getPageContents();

      return [
        'title' => $contents['bootcamp_header_title']['value'] ?? 'Bootcamp',
        'subtitle' => $contents['bootcamp_header_subtitle']['value'] ?? 'Program pelatihan intensif dan workshop',
        'image_path' => $contents['bootcamp_header_image']['value'] ?? ''
      ];
    } catch (PDOException $e) {
      error_log("DB Error (getBootcampPageHeader): " . $e->getMessage());
      return [
        'title' => 'Bootcamp',
        'subtitle' => 'Program pelatihan intensif dan workshop',
        'image_path' => ''
      ];
    }
  }

  // Implement abstract method dari BasePageModel
  public function getHeader()
  {
    return $this->getBootcampPageHeader();
  }

  public function saveHeader($headerData, $userId)
  {
    $contents = [
      'bootcamp_header_title' => ['type' => 'text', 'value' => $headerData['title'] ?? ''],
      'bootcamp_header_subtitle' => ['type' => 'text', 'value' => $headerData['subtitle'] ?? ''],
      'bootcamp_header_image' => ['type' => 'image', 'value' => $headerData['image_path'] ?? '']
    ];

    return $this->saveMultipleContents($contents, $userId);
  }
}