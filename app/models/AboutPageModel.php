<?php
require_once __DIR__ . '/../models/BasePageModel.php';

class AboutPageModel extends BasePageModel
{
  public function __construct()
  {
    parent::__construct('about', 'Tentang Kami');
  }

  // Get all about contents
  public function getAboutContents()
  {
    return $this->getPageContents();
  }

  // Save multiple about contents
  public function saveMultipleAboutContents($contents, $userId)
  {
    return $this->saveMultipleContents($contents, $userId);
  }

  // Get about content
  public function getAboutContent($key)
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
      error_log("DB Error (getAboutContent): " . $e->getMessage());
      return null;
    }
  }

  // Get about header data
  public function getAboutHeader()
  {
    try {
      $contents = $this->getPageContents();

      return [
        'title' => $contents['about_header_title']['value'] ?? 'Tentang LAB IVSS',
        'subtitle' => $contents['about_header_subtitle']['value'] ?? 'Mengenal lebih dekat laboratorium kami',
        'image_path' => $contents['about_header_image']['value'] ?? ''
      ];
    } catch (PDOException $e) {
      error_log("DB Error (getAboutHeader): " . $e->getMessage());
      return [
        'title' => 'Tentang LAB IVSS',
        'subtitle' => 'Mengenal lebih dekat laboratorium kami',
        'image_path' => ''
      ];
    }
  }

  // Implement abstract method dari BasePageModel
  public function getHeader()
  {
    return $this->getAboutHeader();
  }

  public function saveHeader($headerData, $userId)
  {
    $contents = [
      'about_header_title' => ['type' => 'text', 'value' => $headerData['title'] ?? ''],
      'about_header_subtitle' => ['type' => 'text', 'value' => $headerData['subtitle'] ?? ''],
      'about_header_image' => ['type' => 'image', 'value' => $headerData['image_path'] ?? '']
    ];

    return $this->saveMultipleContents($contents, $userId);
  }
}
