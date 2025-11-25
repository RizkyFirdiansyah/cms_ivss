<?php
require_once __DIR__ . '/../config/Database.php';

class AboutModel
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

  // Get all about contents
  public function getAboutContents()
  {
    try {
      $pageId = $this->getPageId('about');
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
      error_log("DB Error (getAboutContents): " . $e->getMessage());
      return [];
    }
  }

  // Save multiple about contents
  public function saveMultipleAboutContents($contents, $userId)
  {
    $this->conn->beginTransaction();

    try {
      $pageId = $this->getPageId('about');
      if (!$pageId) {
        $pageId = $this->createAboutPage();
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
      error_log("DB Error (saveMultipleAboutContents): " . $e->getMessage());
      return false;
    }
  }

  // Get about content
  public function getAboutContent($key)
  {
    try {
      $pageId = $this->getPageId('about');
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

  // Create about page if not exists
  private function createAboutPage()
  {
    try {
      $query = "INSERT INTO pages (name, slug) VALUES ('Tentang Kami', 'about') RETURNING id";
      $stmt = $this->conn->prepare($query);
      $stmt->execute();

      $result = $stmt->fetch(PDO::FETCH_ASSOC);
      return $result ? $result['id'] : null;
    } catch (PDOException $e) {
      error_log("DB Error (createAboutPage): " . $e->getMessage());
      return null;
    }
  }
}
