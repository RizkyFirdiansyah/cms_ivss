<?php
require_once __DIR__ . '/../config/Database.php';

class MemberPageModel
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

  // Get member page contents
  public function getMemberPageContents()
  {
    try {
      $pageId = $this->getPageId('member');
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
      error_log("DB Error (getMemberPageContents): " . $e->getMessage());
      return [];
    }
  }

  // Save multiple member page contents
  public function saveMultipleMemberContents($contents, $userId)
  {
    $this->conn->beginTransaction();

    try {
      $pageId = $this->getPageId('member');
      if (!$pageId) {
        $pageId = $this->createMemberPage();
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
      error_log("DB Error (saveMultipleMemberContents): " . $e->getMessage());
      return false;
    }
  }

  // Create member page if not exists
  private function createMemberPage()
  {
    try {
      $query = "INSERT INTO pages (name, slug) VALUES ('Member', 'member') RETURNING id";
      $stmt = $this->conn->prepare($query);
      $stmt->execute();

      $result = $stmt->fetch(PDO::FETCH_ASSOC);
      return $result ? $result['id'] : null;
    } catch (PDOException $e) {
      error_log("DB Error (createMemberPage): " . $e->getMessage());
      return null;
    }
  }

  // Get specific content value
  public function getMemberContent($key)
  {
    try {
      $pageId = $this->getPageId('member');
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
}
