<?php
require_once __DIR__ . '/../config/Database.php';

class GalleryPageModel
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

  // Get gallery page contents
  public function getGalleryPageContents()
  {
    try {
      $pageId = $this->getPageId('gallery');
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
      error_log("DB Error (getGalleryPageContents): " . $e->getMessage());
      return [];
    }
  }

  // Save multiple gallery page contents
  public function saveMultipleGalleryContents($contents, $userId)
  {
    $this->conn->beginTransaction();

    try {
      $pageId = $this->getPageId('gallery');
      if (!$pageId) {
        $pageId = $this->createGalleryPage();
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
      error_log("DB Error (saveMultipleGalleryContents): " . $e->getMessage());
      return false;
    }
  }

  // Create gallery page if not exists
  private function createGalleryPage()
  {
    try {
      $query = "INSERT INTO pages (name, slug) VALUES ('Galeri', 'gallery') RETURNING id";
      $stmt = $this->conn->prepare($query);
      $stmt->execute();

      $result = $stmt->fetch(PDO::FETCH_ASSOC);
      return $result ? $result['id'] : null;
    } catch (PDOException $e) {
      error_log("DB Error (createGalleryPage): " . $e->getMessage());
      return null;
    }
  }

  // Get gallery header data (title, subtitle, image)
  public function getGalleryHeader()
  {
    try {
      $contents = $this->getGalleryPageContents();

      return [
        'title' => $contents['gallery_header_title']['value'] ?? 'Galeri',
        'subtitle' => $contents['gallery_header_subtitle']['value'] ?? 'Koleksi dokumentasi kegiatan laboratorium',
        'image_path' => $contents['gallery_header_image']['value'] ?? ''
      ];
    } catch (PDOException $e) {
      error_log("DB Error (getGalleryHeader): " . $e->getMessage());
      return [
        'title' => 'Galeri',
        'subtitle' => 'Koleksi dokumentasi kegiatan laboratorium',
        'image_path' => ''
      ];
    }
  }

  // Save gallery header data
  public function saveGalleryHeader($headerData, $userId)
  {
    $contents = [
      'gallery_header_title' => ['type' => 'text', 'value' => $headerData['title'] ?? ''],
      'gallery_header_subtitle' => ['type' => 'text', 'value' => $headerData['subtitle'] ?? ''],
      'gallery_header_image' => ['type' => 'image', 'value' => $headerData['image_path'] ?? '']
    ];

    return $this->saveMultipleGalleryContents($contents, $userId);
  }
}
