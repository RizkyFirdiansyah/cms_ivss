<?php
require_once __DIR__ . '/../config/Database.php';

class NewsModel
{
  private $conn;

  public function __construct()
  {
    $db = new Database();
    $this->conn = $db->getConnection();
  }

  // Read Data News 
  public function getNews($limit, $offset, $search = '', $category_id = null)
  {
    $query = "SELECT 
                n.*, 
                u.name as author_name,
                STRING_AGG(c.name, ', ') as categories,
                STRING_AGG(CAST(c.id AS VARCHAR), ',') as category_ids
              FROM news n
              LEFT JOIN users u ON n.user_id = u.id
              LEFT JOIN news_categories nc ON n.id = nc.news_id
              LEFT JOIN categories c ON nc.category_id = c.id";

    $whereConditions = [];
    $params = [];

    // Filter search
    if ($search !== '') {
      $whereConditions[] = "(n.title ILIKE :search OR n.content ILIKE :search)";
      $params[':search'] = '%' . $search . '%';
    }

    // Filter kategori
    if ($category_id !== null && $category_id > 0) {
      $whereConditions[] = "nc.category_id = :category_id";
      $params[':category_id'] = $category_id;
    }

    // Gabungkan kondisi WHERE
    if (!empty($whereConditions)) {
      $query .= " WHERE " . implode(" AND ", $whereConditions);
    }

    $query .= " GROUP BY n.id, u.name
                ORDER BY n.created_at DESC 
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

      // Process category data untuk semua results
      foreach ($results as &$row) {
        $row = $this->processCategoryData($row);
      }

      return $results;
    } catch (PDOException $e) {
      error_log("DB Error (getNews): " . $e->getMessage());
      return [];
    }
  }

  // Count News
  public function countNews($search = '', $category_id = null)
  {
    $query = "SELECT COUNT(DISTINCT n.id) AS total 
              FROM news n
              LEFT JOIN news_categories nc ON n.id = nc.news_id
              LEFT JOIN categories c ON nc.category_id = c.id";

    $whereConditions = [];
    $params = [];

    // Filter search
    if ($search !== '') {
      $whereConditions[] = "(n.title ILIKE :search OR n.content ILIKE :search)";
      $params[':search'] = '%' . $search . '%';
    }

    // Filter kategori
    if ($category_id !== null && $category_id > 0) {
      $whereConditions[] = "nc.category_id = :category_id";
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
      error_log("DB Error (countNews): " . $e->getMessage());
      return 0;
    }
  }

  // Insert/Update News 
  public function saveNews($data)
  {
    $this->conn->beginTransaction();

    try {
      if (!empty($data["id"])) {
        // UPDATE News
        $query = "UPDATE news 
                  SET title = :title,
                      content = :content,
                      photo = :photo,
                      updated_at = CURRENT_TIMESTAMP
                  WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(":id", $data["id"], PDO::PARAM_INT);
      } else {
        // INSERT News
        $query = "INSERT INTO news (user_id, title, content, photo)
                  VALUES (:user_id, :title, :content, :photo)";
        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(':user_id', $data['user_id'] ?? 1, PDO::PARAM_INT); // Default user_id
      }

      $stmt->bindValue(':title', $data['title'] ?? '', PDO::PARAM_STR);
      $stmt->bindValue(':content', $data['content'] ?? '', PDO::PARAM_STR);
      $stmt->bindValue(':photo', $data['photo'] ?? null, PDO::PARAM_STR);

      $stmt->execute();

      // Get news ID (for new insert)
      $newsId = !empty($data["id"]) ? $data["id"] : $this->conn->lastInsertId();

      // Handle categories 
      if (isset($data['categories'])) {
        $this->saveNewsCategories($newsId, $data['categories']);
      }

      $this->conn->commit();
      return $newsId;
    } catch (PDOException $e) {
      $this->conn->rollBack();
      error_log("DB Error (saveNews): " . $e->getMessage());
      return false;
    }
  }

  // Save news categories
  private function saveNewsCategories($newsId, $categories)
  {

    // Validasi input
    if (!is_array($categories)) {
      $categories = [];
    }

    // Filter dan sanitize category IDs
    $categories = array_filter($categories, function ($catId) {
      return is_numeric($catId) && $catId > 0;
    });

    $query = "DELETE FROM news_categories WHERE news_id = :news_id";

    // Delete existing categories
    $deleteStmt = $this->conn->prepare($query);
    $deleteStmt->bindValue(':news_id', $newsId, PDO::PARAM_INT);
    $deleteStmt->execute();

    // Insert new categories hanya jika ada
    if (!empty($categories)) {
      $query = "INSERT INTO news_categories (news_id, category_id) VALUES (:news_id, :category_id)";
      $insertStmt = $this->conn->prepare($query);

      foreach ($categories as $categoryId) {
        $insertStmt->bindValue(':news_id', $newsId, PDO::PARAM_INT);
        $insertStmt->bindValue(':category_id', (int)$categoryId, PDO::PARAM_INT);
        $insertStmt->execute();
      }
    }
  }

  // Get single news by ID 
  public function getById($id)
  {
    try {
      $query = "SELECT 
                  n.*, 
                  u.name as author_name,
                  STRING_AGG(c.name, ', ') as categories,
                  STRING_AGG(CAST(c.id AS VARCHAR), ',') as category_ids
                FROM news n
                LEFT JOIN users u ON n.user_id = u.id
                LEFT JOIN news_categories nc ON n.id = nc.news_id
                LEFT JOIN categories c ON nc.category_id = c.id
                WHERE n.id = :id
                GROUP BY n.id, u.name";

      $stmt = $this->conn->prepare($query);
      $stmt->execute([":id" => $id]);
      $result = $stmt->fetch(PDO::FETCH_ASSOC);

      return $result ? $this->processCategoryData($result) : null;
    } catch (PDOException $e) {
      error_log("DB Error (getById): " . $e->getMessage());
      return null;
    }
  }

  // Delete News
  public function delete($id)
  {
    $query = "DELETE FROM news WHERE id = :id";
    try {
      $stmt = $this->conn->prepare($query);
      $stmt->bindValue(':id', $id, PDO::PARAM_INT);
      return $stmt->execute();
    } catch (PDOException $e) {
      error_log("DB Error (delete): " . $e->getMessage());
      return false;
    }
  }

  // Get recent news (future)
  public function getRecentNews($limit = 5)
  {
    try {
      $query = "SELECT 
                  n.id, 
                  n.title, 
                  n.content, 
                  n.photo, 
                  n.created_at,
                  u.name as author_name,
                  STRING_AGG(c.name, ', ') as categories,
                  STRING_AGG(CAST(c.id AS VARCHAR), ',') as category_ids
                FROM news n
                LEFT JOIN users u ON n.user_id = u.id
                LEFT JOIN news_categories nc ON n.id = nc.news_id
                LEFT JOIN categories c ON nc.category_id = c.id
                GROUP BY n.id, u.name
                ORDER BY n.created_at DESC 
                LIMIT :limit";

      $stmt = $this->conn->prepare($query);
      $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
      $stmt->execute();

      $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

      // Process category data untuk semua results
      foreach ($results as &$row) {
        $row = $this->processCategoryData($row);
      }

      return $results;
    } catch (PDOException $e) {
      error_log("DB Error (getRecentNews): " . $e->getMessage());
      return [];
    }
  }

  // Get news by category (future)
  public function getNewsByCategory($categoryId, $limit = 10, $offset = 0)
  {
    try {
      $query = "SELECT 
                  n.id, 
                  n.title, 
                  n.content, 
                  n.photo, 
                  n.created_at,
                  u.name as author_name,
                  STRING_AGG(c.name, ', ') as categories,
                  STRING_AGG(CAST(c.id AS VARCHAR), ',') as category_ids
                FROM news n
                LEFT JOIN users u ON n.user_id = u.id
                LEFT JOIN news_categories nc ON n.id = nc.news_id
                LEFT JOIN categories c ON nc.category_id = c.id
                WHERE nc.category_id = :category_id
                GROUP BY n.id, u.name
                ORDER BY n.created_at DESC 
                LIMIT :limit OFFSET :offset";

      $stmt = $this->conn->prepare($query);
      $stmt->bindValue(':category_id', $categoryId, PDO::PARAM_INT);
      $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
      $stmt->bindValue(':offset', (int)$offset, PDO::PARAM_INT);
      $stmt->execute();

      $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

      // Process category data untuk semua results
      foreach ($results as &$row) {
        $row = $this->processCategoryData($row);
      }

      return $results;
    } catch (PDOException $e) {
      error_log("DB Error (getNewsByCategory): " . $e->getMessage());
      return [];
    }
  }

  // Tambahkan method ini di NewsModel untuk count news by category
  public function countNewsByCategory($categoryId)
  {
    try {
      $query = "SELECT COUNT(DISTINCT n.id) AS total 
                  FROM news n
                  LEFT JOIN news_categories nc ON n.id = nc.news_id
                  WHERE nc.category_id = :category_id";

      $stmt = $this->conn->prepare($query);
      $stmt->bindValue(':category_id', $categoryId, PDO::PARAM_INT);
      $stmt->execute();

      return (int)$stmt->fetch(PDO::FETCH_ASSOC)['total'];
    } catch (PDOException $e) {
      error_log("DB Error (countNewsByCategory): " . $e->getMessage());
      return 0;
    }
  }

  // Helper function
  // Process category data
  private function processCategoryData($row)
  {
    // Process category_ids
    if (!empty($row['category_ids'])) {
      if (is_string($row['category_ids'])) {
        $row['category_ids'] = explode(',', $row['category_ids']);
        // Convert to integers
        $row['category_ids'] = array_map('intval', $row['category_ids']);
      }
    } else {
      $row['category_ids'] = [];
    }

    // Process categories names
    if (empty($row['categories'])) {
      $row['categories'] = '';
    }

    return $row;
  }
}
