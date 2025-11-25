<?php
require_once __DIR__ . '/../config/Database.php';

abstract class BasePageModel
{
  protected $conn;
  protected $slug;
  protected $pageName;

  public function __construct($slug, $pageName)
  {
    $db = new Database();
    $this->conn = $db->getConnection();
    $this->slug = $slug;
    $this->pageName = $pageName;
  }

  // Common methods yang sama untuk semua halaman
  public function getPageId()
  {
    try {
      $query = "SELECT id FROM pages WHERE slug = :slug";
      $stmt = $this->conn->prepare($query);
      $stmt->bindValue(':slug', $this->slug, PDO::PARAM_STR);
      $stmt->execute();

      $result = $stmt->fetch(PDO::FETCH_ASSOC);
      return $result ? $result['id'] : null;
    } catch (PDOException $e) {
      error_log("DB Error (getPageId for {$this->slug}): " . $e->getMessage());
      return null;
    }
  }

  public function getPageContents()
  {
    try {
      $pageId = $this->getPageId();
      if (!$pageId) return [];

      $query = "SELECT content_key, content_type, content_value 
                FROM page_contents 
                WHERE page_id = :page_id 
                ORDER BY content_key";

      $stmt = $this->conn->prepare($query);
      $stmt->bindValue(':page_id', $pageId, PDO::PARAM_INT);
      $stmt->execute();

      $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

      $contents = [];
      foreach ($results as $row) {
        $contents[$row['content_key']] = [
          'type' => $row['content_type'],
          'value' => $row['content_value']
        ];
      }

      return $contents;
    } catch (PDOException $e) {
      error_log("DB Error (getPageContents for {$this->slug}): " . $e->getMessage());
      return [];
    }
  }

  public function saveMultipleContents($contents, $userId)
  {
    $this->conn->beginTransaction();

    try {
      $pageId = $this->getPageId();
      if (!$pageId) {
        $pageId = $this->createPage();
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
      error_log("DB Error (saveMultipleContents for {$this->slug}): " . $e->getMessage());
      return false;
    }
  }

  private function createPage()
  {
    try {
      $query = "INSERT INTO pages (name, slug) VALUES (:name, :slug) RETURNING id";
      $stmt = $this->conn->prepare($query);
      $stmt->bindValue(':name', $this->pageName, PDO::PARAM_STR);
      $stmt->bindValue(':slug', $this->slug, PDO::PARAM_STR);
      $stmt->execute();

      $result = $stmt->fetch(PDO::FETCH_ASSOC);
      return $result ? $result['id'] : null;
    } catch (PDOException $e) {
      error_log("DB Error (createPage for {$this->slug}): " . $e->getMessage());
      return null;
    }
  }

  // Abstract methods - harus diimplement oleh child class
  abstract public function getHeader();
  abstract public function saveHeader($headerData, $userId);
}
