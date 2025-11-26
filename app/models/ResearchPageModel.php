<?php
require_once __DIR__ . '/../models/BasePageModel.php';

class ResearchPageModel extends BasePageModel
{
  public function __construct()
  {
    parent::__construct('research', 'Penelitian');
  }

  // Get all research page contents
  public function getResearchPageContents()
  {
    return $this->getPageContents();
  }

  // Save multiple research page contents
  public function saveMultipleResearchPageContents($contents, $userId)
  {
    return $this->saveMultipleContents($contents, $userId);
  }

  // Get research page content by key
  public function getResearchPageContent($key)
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
      error_log("DB Error (getResearchPageContent): " . $e->getMessage());
      return null;
    }
  }

  // Get research page header data
  public function getResearchPageHeader()
  {
    try {
      $contents = $this->getPageContents();

      return [
        'title' => $contents['research_header_title']['value'] ?? 'Penelitian',
        'subtitle' => $contents['research_header_subtitle']['value'] ?? 'Proyek dan kegiatan penelitian LAB IVSS',
        'image_path' => $contents['research_header_image']['value'] ?? ''
      ];
    } catch (PDOException $e) {
      error_log("DB Error (getResearchPageHeader): " . $e->getMessage());
      return [
        'title' => 'Penelitian',
        'subtitle' => 'Proyek dan kegiatan penelitian LAB IVSS',
        'image_path' => ''
      ];
    }
  }

  // Implement abstract method dari BasePageModel
  public function getHeader()
  {
    return $this->getResearchPageHeader();
  }

  public function saveHeader($headerData, $userId)
  {
    $contents = [
      'research_header_title' => ['type' => 'text', 'value' => $headerData['title'] ?? ''],
      'research_header_subtitle' => ['type' => 'text', 'value' => $headerData['subtitle'] ?? ''],
      'research_header_image' => ['type' => 'image', 'value' => $headerData['image_path'] ?? '']
    ];

    return $this->saveMultipleContents($contents, $userId);
  }
}
