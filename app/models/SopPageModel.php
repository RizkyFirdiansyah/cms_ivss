<?php
require_once __DIR__ . '/../config/Database.php';

class SopPageModel
{
  private $conn;

  public function __construct()
  {
    $db = new Database();
    $this->conn = $db->getConnection();
  }

  // Get page ID by slug
  public function getPageId($slug)
  {
    try {
      $query = "SELECT id FROM pages WHERE slug = :slug";
      $stmt = $this->conn->prepare($query);
      $stmt->bindValue(':slug', $slug, PDO::PARAM_STR);
      $stmt->execute();

      $result = $stmt->fetch(PDO::FETCH_ASSOC);
      return $result ? $result['id'] : null;
    } catch (PDOException $e) {
      error_log("DB Error (getPageId): " . $e->getMessage());
      return null;
    }
  }

  // Get SOP page contents
  public function getSopPageContents()
  {
    try {
      $pageId = $this->getPageId('sop');
      if (!$pageId) return [];

      $query = "SELECT content_key, content_type, content_value 
                FROM page_contents 
                WHERE page_id = :page_id 
                ORDER BY content_key";

      $stmt = $this->conn->prepare($query);
      $stmt->bindValue(':page_id', $pageId, PDO::PARAM_INT);
      $stmt->execute();

      $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

      // Convert to associative array
      $contents = [];
      foreach ($results as $row) {
        $contents[$row['content_key']] = [
          'type' => $row['content_type'],
          'value' => $row['content_value']
        ];
      }

      return $contents;
    } catch (PDOException $e) {
      error_log("DB Error (getSopPageContents): " . $e->getMessage());
      return [];
    }
  }

  // Save multiple SOP page contents
  public function saveMultipleSopContents($contents, $userId)
  {
    $this->conn->beginTransaction();

    try {
      $pageId = $this->getPageId('sop');
      if (!$pageId) {
        $pageId = $this->createSopPage();
        if (!$pageId) {
          $this->conn->rollBack();
          return false;
        }
      }

      $query = "INSERT INTO page_contents (page_id, user_id, content_key, content_type, content_value) 
                VALUES (:page_id, :user_id, :content_key, :content_type, :content_value)
                ON CONFLICT (page_id, content_key) 
                DO UPDATE SET 
                  content_value = EXCLUDED.content_value,
                  content_type = EXCLUDED.content_type,
                  user_id = EXCLUDED.user_id,
                  last_updated = CURRENT_TIMESTAMP";

      $stmt = $this->conn->prepare($query);

      foreach ($contents as $key => $data) {
        $stmt->bindValue(':page_id', $pageId, PDO::PARAM_INT);
        $stmt->bindValue(':user_id', $userId, PDO::PARAM_INT);
        $stmt->bindValue(':content_key', $key, PDO::PARAM_STR);
        $stmt->bindValue(':content_type', $data['type'] ?? 'text', PDO::PARAM_STR);
        $stmt->bindValue(':content_value', $data['value'] ?? '', PDO::PARAM_STR);
        $stmt->execute();
      }

      $this->conn->commit();
      return true;
    } catch (PDOException $e) {
      $this->conn->rollBack();
      error_log("DB Error (saveMultipleSopContents): " . $e->getMessage());
      return false;
    }
  }

  // Create SOP page if not exists
  private function createSopPage()
  {
    try {
      $query = "INSERT INTO pages (name, slug) VALUES ('Standard Operating Procedure', 'sop') RETURNING id";
      $stmt = $this->conn->prepare($query);
      $stmt->execute();

      $result = $stmt->fetch(PDO::FETCH_ASSOC);
      return $result ? $result['id'] : null;
    } catch (PDOException $e) {
      error_log("DB Error (createSopPage): " . $e->getMessage());
      return null;
    }
  }

  // Get specific SOP content value
  public function getSopContent($key)
  {
    try {
      $pageId = $this->getPageId('sop');
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

  // Get SOP header data (title, subtitle, image)
  public function getSopHeader()
  {
    try {
      $contents = $this->getSopPageContents();

      return [
        'title' => $contents['sop_header_title']['value'] ?? 'Standard Operating Procedure',
        'subtitle' => $contents['sop_header_subtitle']['value'] ?? '',
        'image_path' => $contents['sop_header_image']['value'] ?? ''
      ];
    } catch (PDOException $e) {
      error_log("DB Error (getSopHeader): " . $e->getMessage());
      return [
        'title' => 'Standard Operating Procedure',
        'subtitle' => '',
        'image_path' => ''
      ];
    }
  }

  // Get main SOP content (image, title, description)
  public function getSopMainContent()
  {
    try {
      $contents = $this->getSopPageContents();

      return [
        'title' => $contents['sop_main_title']['value'] ?? '',
        'content' => $contents['sop_main_content']['value'] ?? '',
        'image_path' => $contents['sop_main_image']['value'] ?? ''
      ];
    } catch (PDOException $e) {
      error_log("DB Error (getSopMainContent): " . $e->getMessage());
      return [
        'title' => '',
        'content' => '',
        'image_path' => ''
      ];
    }
  }

  // Save SOP header data
  public function saveSopHeader($headerData, $userId)
  {
    $contents = [
      'sop_header_title' => ['type' => 'text', 'value' => $headerData['title'] ?? ''],
      'sop_header_subtitle' => ['type' => 'text', 'value' => $headerData['subtitle'] ?? ''],
      'sop_header_image' => ['type' => 'image', 'value' => $headerData['image_path'] ?? '']
    ];

    return $this->saveMultipleSopContents($contents, $userId);
  }

  // Save SOP main content
  public function saveSopMainContent($mainData, $userId)
  {
    $contents = [
      'sop_main_title' => ['type' => 'text', 'value' => $mainData['title'] ?? ''],
      'sop_main_content' => ['type' => 'text', 'value' => $mainData['content'] ?? ''],
      'sop_main_image' => ['type' => 'image', 'value' => $mainData['image_path'] ?? '']
    ];

    return $this->saveMultipleSopContents($contents, $userId);
  }

  // Save all SOP contents at once (untuk update-all)
  public function saveAllSopContents($headerData, $mainData, $userId)
  {
    $contents = [
      // Header section
      'sop_header_title' => ['type' => 'text', 'value' => $headerData['title'] ?? ''],
      'sop_header_subtitle' => ['type' => 'text', 'value' => $headerData['subtitle'] ?? ''],
      'sop_header_image' => ['type' => 'image', 'value' => $headerData['image_path'] ?? ''],

      // Main content section
      'sop_main_title' => ['type' => 'text', 'value' => $mainData['title'] ?? ''],
      'sop_main_content' => ['type' => 'text', 'value' => $mainData['content'] ?? ''],
      'sop_main_image' => ['type' => 'image', 'value' => $mainData['image_path'] ?? '']
    ];

    return $this->saveMultipleSopContents($contents, $userId);
  }
}
