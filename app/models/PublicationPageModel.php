<?php
require_once __DIR__ . '/../models/BasePageModel.php';

class PublicationPageModel extends BasePageModel
{
  public function __construct()
  {
    parent::__construct('publication', 'Publikasi');
  }

  // Get all publication page contents
  public function getPublicationPageContents()
  {
    return $this->getPageContents();
  }

  // Save multiple publication page contents
  public function saveMultiplePublicationPageContents($contents, $userId)
  {
    return $this->saveMultipleContents($contents, $userId);
  }

  // Get publication page content by key
  public function getPublicationPageContent($key)
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
      error_log("DB Error (getPublicationPageContent): " . $e->getMessage());
      return null;
    }
  }

  // Get publication page header data
  public function getPublicationPageHeader()
  {
    try {
      $contents = $this->getPageContents();

      return [
        'title' => $contents['publication_header_title']['value'] ?? 'Publikasi',
        'subtitle' => $contents['publication_header_subtitle']['value'] ?? 'Karya ilmiah dan hasil penelitian LAB IVSS',
        'image_path' => $contents['publication_header_image']['value'] ?? ''
      ];
    } catch (PDOException $e) {
      error_log("DB Error (getPublicationPageHeader): " . $e->getMessage());
      return [
        'title' => 'Publikasi',
        'subtitle' => 'Karya ilmiah dan hasil penelitian LAB IVSS',
        'image_path' => ''
      ];
    }
  }

  // Implement abstract method dari BasePageModel
  public function getHeader()
  {
    return $this->getPublicationPageHeader();
  }

  public function saveHeader($headerData, $userId)
  {
    $contents = [
      'publication_header_title' => ['type' => 'text', 'value' => $headerData['title'] ?? ''],
      'publication_header_subtitle' => ['type' => 'text', 'value' => $headerData['subtitle'] ?? ''],
      'publication_header_image' => ['type' => 'image', 'value' => $headerData['image_path'] ?? '']
    ];

    return $this->saveMultipleContents($contents, $userId);
  }
}
