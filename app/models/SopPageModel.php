<?php
require_once __DIR__ . '/../models/BasePageModel.php';

class SopPageModel extends BasePageModel
{
  public function __construct()
  {
    parent::__construct('sop', 'Standard Operating Procedure');
  }

  // Get all SOP contents including header and SOP items
  public function getSopContents()
  {
    return $this->getPageContents();
  }

  // Save multiple SOP contents
  public function saveMultipleSopContents($contents, $userId)
  {
    return $this->saveMultipleContents($contents, $userId);
  }

  // Get SOP content by key
  public function getSopContent($key)
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
      error_log("DB Error (getSopContent): " . $e->getMessage());
      return null;
    }
  }

  // Get SOP header data
  public function getSopHeader()
  {
    try {
      $contents = $this->getPageContents();

      return [
        'title' => $contents['sop_header_title']['value'] ?? 'Standard Operating Procedure',
        'subtitle' => $contents['sop_header_subtitle']['value'] ?? 'Prosedur operasional standar laboratorium',
        'image_path' => $contents['sop_header_image']['value'] ?? ''
      ];
    } catch (PDOException $e) {
      error_log("DB Error (getSopHeader): " . $e->getMessage());
      return [
        'title' => 'Standard Operating Procedure',
        'subtitle' => 'Prosedur operasional standar laboratorium',
        'image_path' => ''
      ];
    }
  }

  // Implement abstract method dari BasePageModel
  public function getHeader()
  {
    return $this->getSopHeader();
  }

  public function saveHeader($headerData, $userId)
  {
    $contents = [
      'sop_header_title' => ['type' => 'text', 'value' => $headerData['title'] ?? ''],
      'sop_header_subtitle' => ['type' => 'text', 'value' => $headerData['subtitle'] ?? ''],
      'sop_header_image' => ['type' => 'image', 'value' => $headerData['image_path'] ?? '']
    ];

    return $this->saveMultipleContents($contents, $userId);
  }
}
