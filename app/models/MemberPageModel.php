<?php
require_once __DIR__ . '/../models/BasePageModel.php';

class MemberPageModel extends BasePageModel
{
  public function __construct()
  {
    parent::__construct('member', 'Member');
  }

  // Get member header data (title, subtitle, image)
  public function getMemberHeader()
  {
    try {
      $contents = $this->getPageContents();

      return [
        'title' => $contents['member_header_title']['value'] ?? 'Anggota Laboratorium',
        'subtitle' => $contents['member_header_subtitle']['value'] ?? 'Tim peneliti dan staff laboratorium',
        'image_path' => $contents['member_header_image']['value'] ?? ''
      ];
    } catch (PDOException $e) {
      error_log("DB Error (getMemberHeader): " . $e->getMessage());
      return [
        'title' => 'Anggota Laboratorium',
        'subtitle' => 'Tim peneliti dan staff laboratorium',
        'image_path' => ''
      ];
    }
  }

  // Save member header data
  public function saveMemberHeader($headerData, $userId)
  {
    $contents = [
      'member_header_title' => ['type' => 'text', 'value' => $headerData['title'] ?? ''],
      'member_header_subtitle' => ['type' => 'text', 'value' => $headerData['subtitle'] ?? ''],
      'member_header_image' => ['type' => 'image', 'value' => $headerData['image_path'] ?? '']
    ];

    return $this->saveMultipleContents($contents, $userId);
  }

  // Get specific content value
  public function getMemberContent($key)
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
      error_log("DB Error (getMemberContent): " . $e->getMessage());
      return null;
    }
  }

  // Implement abstract method dari BasePageModel
  public function getHeader()
  {
    return $this->getMemberHeader();
  }

  public function saveHeader($headerData, $userId)
  {
    return $this->saveMemberHeader($headerData, $userId);
  }
}
