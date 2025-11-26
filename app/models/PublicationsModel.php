<?php
require_once __DIR__ . '/../config/Database.php';

class PublicationsModel
{
  private $conn;

  public function __construct()
  {
    $db = new Database();
    $this->conn = $db->getConnection();
  }

  // Read Data Publications dengan Kategori - PERBAIKAN
  public function getPublications($limit, $offset, $search = '', $category_id = null)
  {
    // Query dasar tanpa GROUP BY untuk menghindari masalah STRING_AGG
    $query = "SELECT DISTINCT p.*, u.name as author_name
              FROM publications p
              LEFT JOIN users u ON p.user_id = u.id
              LEFT JOIN publication_categories pc ON p.id = pc.publication_id
              LEFT JOIN categories c ON pc.category_id = c.id";

    $whereConditions = [];
    $params = [];

    // Filter search
    if ($search !== '') {
      $whereConditions[] = "p.title ILIKE :search";
      $params[':search'] = '%' . $search . '%';
    }

    // Filter kategori
    if ($category_id !== null && $category_id > 0) {
      $whereConditions[] = "pc.category_id = :category_id";
      $params[':category_id'] = $category_id;
    }

    // Gabungkan kondisi WHERE
    if (!empty($whereConditions)) {
      $query .= " WHERE " . implode(" AND ", $whereConditions);
    }

    $query .= " ORDER BY p.publication_year DESC, p.last_updated DESC 
                LIMIT :limit OFFSET :offset";

    try {
      $stmt = $this->conn->prepare($query);

      // Bind parameters
      foreach ($params as $key => $value) {
        $stmt->bindValue($key, $value, PDO::PARAM_STR);
      }

      $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
      $stmt->bindValue(':offset', (int)$offset, PDO::PARAM_INT);

      $stmt->execute();

      $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

      // Get categories untuk setiap publication secara terpisah
      foreach ($results as &$publication) {
        $publication['categories'] = $this->getPublicationCategories($publication['id']);
        $publication['category_ids'] = $this->getPublicationCategoryIds($publication['id']);
      }

      return $results;
    } catch (PDOException $e) {
      error_log("DB Error (getPublications): " . $e->getMessage());
      return [];
    }
  }

  // Get categories names untuk sebuah publication
  private function getPublicationCategories($publication_id)
  {
    $query = "SELECT c.name 
              FROM categories c
              JOIN publication_categories pc ON c.id = pc.category_id
              WHERE pc.publication_id = :publication_id";
    
    try {
      $stmt = $this->conn->prepare($query);
      $stmt->execute([':publication_id' => $publication_id]);
      $categories = $stmt->fetchAll(PDO::FETCH_COLUMN, 0);
      return implode(', ', $categories);
    } catch (PDOException $e) {
      error_log("DB Error (getPublicationCategories): " . $e->getMessage());
      return '';
    }
  }

  // Get category IDs untuk sebuah publication
  private function getPublicationCategoryIds($publication_id)
  {
    $query = "SELECT c.id 
              FROM categories c
              JOIN publication_categories pc ON c.id = pc.category_id
              WHERE pc.publication_id = :publication_id";
    
    try {
      $stmt = $this->conn->prepare($query);
      $stmt->execute([':publication_id' => $publication_id]);
      return $stmt->fetchAll(PDO::FETCH_COLUMN, 0);
    } catch (PDOException $e) {
      error_log("DB Error (getPublicationCategoryIds): " . $e->getMessage());
      return [];
    }
  }

  // Count Publications - PERBAIKAN
  public function countPublications($search = '', $category_id = null)
  {
    $query = "SELECT COUNT(DISTINCT p.id) AS total 
              FROM publications p
              LEFT JOIN publication_categories pc ON p.id = pc.publication_id
              LEFT JOIN categories c ON pc.category_id = c.id";

    $whereConditions = [];
    $params = [];

    // Filter search
    if ($search !== '') {
      $whereConditions[] = "p.title ILIKE :search";
      $params[':search'] = '%' . $search . '%';
    }

    // Filter kategori
    if ($category_id !== null && $category_id > 0) {
      $whereConditions[] = "pc.category_id = :category_id";
      $params[':category_id'] = $category_id;
    }

    // Gabungkan kondisi WHERE
    if (!empty($whereConditions)) {
      $query .= " WHERE " . implode(" AND ", $whereConditions);
    }

    try {
      $stmt = $this->conn->prepare($query);

      // Bind parameters
      foreach ($params as $key => $value) {
        $stmt->bindValue($key, $value, PDO::PARAM_STR);
      }

      $stmt->execute();
      return (int)$stmt->fetch(PDO::FETCH_ASSOC)['total'];
    } catch (PDOException $e) {
      error_log("DB Error (countPublications): " . $e->getMessage());
      return 0;
    }
  }

  // Insert/Update Publication - TETAP SAMA
  public function savePublication($data)
  {
    $this->conn->beginTransaction();

    try {
      if (!empty($data["id"])) {
        // UPDATE Publication
        $query = "UPDATE publications 
                  SET title = :title,
                      link = :link,
                      publication_year = :publication_year,
                      last_updated = CURRENT_TIMESTAMP
                  WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(":id", $data["id"], PDO::PARAM_INT);
      } else {
        // INSERT Publication
        $query = "INSERT INTO publications (user_id, title, link, publication_year)
                  VALUES (:user_id, :title, :link, :publication_year)";
        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(':user_id', $data['user_id'], PDO::PARAM_INT);
      }

      $stmt->bindValue(':title', $data['title'], PDO::PARAM_STR);
      $stmt->bindValue(':link', $data['link'], PDO::PARAM_STR);
      $stmt->bindValue(':publication_year', $data['publication_year'], PDO::PARAM_INT);

      $stmt->execute();

      // Get publication ID (for new insert)
      $publicationId = !empty($data["id"]) ? $data["id"] : $this->conn->lastInsertId();

      // Handle categories 
      if (isset($data['categories'])) {
        $this->savePublicationCategories($publicationId, $data['categories']);
      }

      $this->conn->commit();
      return $publicationId;
    } catch (PDOException $e) {
      $this->conn->rollBack();
      error_log("DB Error (savePublication): " . $e->getMessage());
      return false;
    }
  }

  // Save publication categories - TETAP SAMA
  private function savePublicationCategories($publicationId, $categories)
  {
    // Validasi input
    if (!is_array($categories)) {
      $categories = [];
    }

    // Filter dan sanitize category IDs
    $categories = array_filter($categories, function ($catId) {
      return is_numeric($catId) && $catId > 0;
    });

    // Delete existing categories
    $deleteStmt = $this->conn->prepare("DELETE FROM publication_categories WHERE publication_id = :publication_id");
    $deleteStmt->bindValue(':publication_id', $publicationId, PDO::PARAM_INT);
    $deleteStmt->execute();

    // Insert new categories hanya jika ada
    if (!empty($categories)) {
      $insertStmt = $this->conn->prepare("INSERT INTO publication_categories (publication_id, category_id) VALUES (:publication_id, :category_id)");
      
      foreach ($categories as $categoryId) {
        $insertStmt->bindValue(':publication_id', $publicationId, PDO::PARAM_INT);
        $insertStmt->bindValue(':category_id', (int)$categoryId, PDO::PARAM_INT);
        $insertStmt->execute();
      }
    }
  }

  // Get single publication by ID - PERBAIKAN
  public function getById($id)
  {
    try {
      $query = "SELECT p.*, u.name as author_name
                FROM publications p
                LEFT JOIN users u ON p.user_id = u.id
                WHERE p.id = :id";

      $stmt = $this->conn->prepare($query);
      $stmt->execute([":id" => $id]);
      $result = $stmt->fetch(PDO::FETCH_ASSOC);

      if ($result) {
        // Get categories separately
        $result['categories'] = $this->getPublicationCategories($id);
        $result['category_ids'] = $this->getPublicationCategoryIds($id);
      }

      return $result;
    } catch (PDOException $e) {
      error_log("DB Error (getById): " . $e->getMessage());
      return null;
    }
  }

  // Delete Publication - TETAP SAMA
  public function delete($id)
  {
    $this->conn->beginTransaction();
    
    try {
      // Delete from publication_categories first
      $stmt1 = $this->conn->prepare("DELETE FROM publication_categories WHERE publication_id = :id");
      $stmt1->execute([":id" => $id]);
      
      // Then delete from publications
      $stmt2 = $this->conn->prepare("DELETE FROM publications WHERE id = :id");
      $result = $stmt2->execute([":id" => $id]);
      
      $this->conn->commit();
      return $result;
    } catch (PDOException $e) {
      $this->conn->rollBack();
      error_log("DB Error (delete): " . $e->getMessage());
      return false;
    }
  }
}