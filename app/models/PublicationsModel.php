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

  // Read Data Publications dengan Kategori 
  public function getPublications($limit, $offset, $search = '', $category_id = null)
  {
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

  // Count Publications 
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

  // Insert/Update Publication 
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

  // Save publication categories 
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

  // Get single publication by ID 
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

  // Delete Publication 
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

  // Helpers 
  // Refresh materialized view
  public function refreshMaterializedView()
  {
    try {
      // Pastikan ada unique index untuk CONCURRENTLY refresh
      // CREATE UNIQUE INDEX idx_mv_publications_id ON mv_publications(publication_id);
      $this->conn->exec("REFRESH MATERIALIZED VIEW mv_publications;");
      return true;
    } catch (PDOException $e) {
      error_log("DB Error (refreshMaterializedView): " . $e->getMessage());
      return false;
    }
  }

  // Get publications for API with filters
  public function getPublicationsForApi($limit = 10, $offset = 0, $search = '', $year = null, $categoryId = null)
  {
    $params = [];
    $where = [];

    if (!empty($search)) {
      $where[] = "(LOWER(title) LIKE LOWER(:search) OR LOWER(author_name) LIKE LOWER(:search))";
      $params[':search'] = "%$search%";
    }

    if (!empty($year)) {
      $where[] = "publication_year = :year";
      $params[':year'] = $year;
    }

    if (!empty($categoryId)) {
      $where[] = "categories_name::text LIKE :category_filter";
      $params[':category_filter'] = '%"id":' . $categoryId . '%';
    }

    // Build WHERE clause
    $whereSql = "";
    if (!empty($where)) {
      $whereSql = "WHERE " . implode(" AND ", $where);
    }

    $sql = "
      SELECT 
        publication_id as id,
        title,
        link,
        publication_year,
        author_name,
        categories_name
      FROM mv_publications
      $whereSql
      ORDER BY publication_year DESC, publication_id DESC
      LIMIT :limit OFFSET :offset
    ";

    try {
      $stmt = $this->conn->prepare($sql);

      // Bind parameters
      foreach ($params as $key => $value) {
        $stmt->bindValue($key, $value);
      }

      $stmt->bindValue(":limit", (int)$limit, PDO::PARAM_INT);
      $stmt->bindValue(":offset", (int)$offset, PDO::PARAM_INT);

      $stmt->execute();
      $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

      // Parse categories_name JSON
      foreach ($results as &$result) {
        if (isset($result['categories_name'])) {
          $result['categories'] = json_decode($result['categories_name'], true);
          unset($result['categories_name']);
        } else {
          $result['categories'] = [];
        }
      }

      return $results;
    } catch (PDOException $e) {
      error_log("DB Error (getPublicationsForApi): " . $e->getMessage());
      return [];
    }
  }

  // Count publications for pagination
  public function countPublicationsForApi($search = '', $year = null, $categoryId = null)
  {
    $params = [];
    $where = [];

    if (!empty($search)) {
      $where[] = "(LOWER(title) LIKE LOWER(:search) OR LOWER(author_name) LIKE LOWER(:search))";
      $params[':search'] = "%$search%";
    }

    if (!empty($year)) {
      $where[] = "publication_year = :year";
      $params[':year'] = $year;
    }

    if (!empty($categoryId)) {
      $where[] = "categories_name::text LIKE :category_filter";
      $params[':category_filter'] = '%"id":' . $categoryId . '%';
    }

    $whereSql = "";
    if (!empty($where)) {
      $whereSql = "WHERE " . implode(" AND ", $where);
    }

    $sql = "SELECT COUNT(*) as total FROM mv_publications $whereSql";

    try {
      $stmt = $this->conn->prepare($sql);

      foreach ($params as $key => $value) {
        $stmt->bindValue($key, $value);
      }

      $stmt->execute();
      $result = $stmt->fetch(PDO::FETCH_ASSOC);
      return (int)$result['total'];
    } catch (PDOException $e) {
      error_log("DB Error (countPublicationsForApi): " . $e->getMessage());
      return 0;
    }
  }

  // Get available years for filtering
  public function getAvailableYears()
  {
    $sql = "
      SELECT DISTINCT publication_year as year 
      FROM mv_publications 
      WHERE publication_year IS NOT NULL
      ORDER BY publication_year DESC
    ";

    try {
      $stmt = $this->conn->prepare($sql);
      $stmt->execute();

      $years = $stmt->fetchAll(PDO::FETCH_COLUMN);
      return array_map('intval', $years);
    } catch (PDOException $e) {
      error_log("DB Error (getAvailableYears): " . $e->getMessage());
      return [];
    }
  }
}
