<?php
require_once __DIR__ . '/../models/BasePageModel.php';

class ContactPageModel extends BasePageModel
{
  public function __construct()
  {
    parent::__construct('contact', 'Kontak & Layanan');
  }

  // Get all Contact page contents
  public function getContactPageContents()
  {
    return $this->getPageContents();
  }

  // Save multiple Contact page contents
  public function saveMultipleContactPageContents($contents, $userId)
  {
    return $this->saveMultipleContents($contents, $userId);
  }

  // Get Contact page content by key
  public function getContactPageContent($key)
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
      error_log("DB Error (getContactPageContent): " . $e->getMessage());
      return null;
    }
  }

  // Get Contact page header data
  public function getContactPageHeader()
  {
    try {
      $contents = $this->getPageContents();

      return [
        'title' => $contents['contact_header_title']['value'] ?? 'Kontak & Layanan',
        'subtitle' => $contents['contact_header_subtitle']['value'] ?? 'Hubungi kami untuk informasi lebih lanjut',
        'image_path' => $contents['contact_header_image']['value'] ?? ''
      ];
    } catch (PDOException $e) {
      error_log("DB Error (getContactPageHeader): " . $e->getMessage());
      return [
        'title' => 'Kontak & Layanan',
        'subtitle' => 'Hubungi kami untuk informasi lebih lanjut',
        'image_path' => ''
      ];
    }
  }

  // Implement abstract method dari BasePageModel
  public function getHeader()
  {
    return $this->getContactPageHeader();
  }

  public function saveHeader($headerData, $userId)
  {
    $contents = [
      'contact_header_title' => ['type' => 'text', 'value' => $headerData['title'] ?? ''],
      'contact_header_subtitle' => ['type' => 'text', 'value' => $headerData['subtitle'] ?? ''],
      'contact_header_image' => ['type' => 'image', 'value' => $headerData['image_path'] ?? '']
    ];

    return $this->saveMultipleContents($contents, $userId);
  }
}
