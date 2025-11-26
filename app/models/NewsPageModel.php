
<?php
require_once __DIR__ . '/../models/BasePageModel.php';

class NewsPageModel extends BasePageModel
{
  public function __construct()
  {
    parent::__construct('news', 'Berita');
  }

  // Get all news page contents
  public function getNewsPageContents()
  {
    return $this->getPageContents();
  }

  // Save multiple news page contents
  public function saveMultipleNewsPageContents($contents, $userId)
  {
    return $this->saveMultipleContents($contents, $userId);
  }

  // Get news page content by key
  public function getNewsPageContent($key)
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
      error_log("DB Error (getNewsPageContent): " . $e->getMessage());
      return null;
    }
  }

  // Get news page header data
  public function getNewsPageHeader()
  {
    try {
      $contents = $this->getPageContents();

      return [
        'title' => $contents['news_header_title']['value'] ?? 'Berita & Artikel',
        'subtitle' => $contents['news_header_subtitle']['value'] ?? 'Informasi terbaru dari LAB IVSS',
        'image_path' => $contents['news_header_image']['value'] ?? ''
      ];
    } catch (PDOException $e) {
      error_log("DB Error (getNewsPageHeader): " . $e->getMessage());
      return [
        'title' => 'Berita & Artikel',
        'subtitle' => 'Informasi terbaru dari LAB IVSS',
        'image_path' => ''
      ];
    }
  }

  // Implement abstract method dari BasePageModel
  public function getHeader()
  {
    return $this->getNewsPageHeader();
  }

  public function saveHeader($headerData, $userId)
  {
    $contents = [
      'news_header_title' => ['type' => 'text', 'value' => $headerData['title'] ?? ''],
      'news_header_subtitle' => ['type' => 'text', 'value' => $headerData['subtitle'] ?? ''],
      'news_header_image' => ['type' => 'image', 'value' => $headerData['image_path'] ?? '']
    ];

    return $this->saveMultipleContents($contents, $userId);
  }
}
