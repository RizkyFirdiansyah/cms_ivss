<?php
require_once __DIR__ . '/../config/Database.php';

class BootcampModel
{
  private $conn;

  public function __construct()
  {
    $db = new Database();
    $this->conn = $db->getConnection();
  }

  // Read Data Bootcamp dengan Kategori dan filter user_id
  public function getBootcamps($limit, $offset, $search = '', $category_id = null, $user_id = null)
  {
    $query = "SELECT 
                b.*, 
                u.name as author_name,
                STRING_AGG(c.name, ', ') as categories,
                STRING_AGG(CAST(c.id AS VARCHAR), ',') as category_ids
              FROM bootcamp b
              LEFT JOIN users u ON b.created_by_user_id = u.id
              LEFT JOIN bootcamp_categories bc ON b.id = bc.bootcamp_id
              LEFT JOIN categories c ON bc.category_id = c.id";

    $whereConditions = [];
    $params = [];

    // Filter berdasarkan user_id (jika diberikan dan bukan null)
    if ($user_id !== null) {
      $whereConditions[] = "b.created_by_user_id = :user_id";
      $params[':user_id'] = $user_id;
    }

    // Filter search
    if ($search !== '') {
      $whereConditions[] = "(b.name ILIKE :search OR b.description ILIKE :search)";
      $params[':search'] = '%' . $search . '%';
    }

    // Filter kategori
    if ($category_id !== null && $category_id > 0) {
      $whereConditions[] = "bc.category_id = :category_id";
      $params[':category_id'] = $category_id;
    }

    // Gabungkan kondisi WHERE
    if (!empty($whereConditions)) {
      $query .= " WHERE " . implode(" AND ", $whereConditions);
    }

    $query .= " GROUP BY b.id, u.name
                ORDER BY b.created_at DESC 
                LIMIT :limit OFFSET :offset";

    try {
      $stmt = $this->conn->prepare($query);

      // Bind parameters
      foreach ($params as $key => $value) {
        if ($key === ':user_id') {
          $stmt->bindValue($key, $value, PDO::PARAM_INT);
        } else {
          $stmt->bindValue($key, $value, PDO::PARAM_STR);
        }
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
      error_log("DB Error (getBootcamps): " . $e->getMessage());
      return [];
    }
  }

  // Count Bootcamps
  public function countBootcamps($search = '', $category_id = null, $user_id = null)
  {
    $query = "SELECT COUNT(DISTINCT b.id) AS total 
              FROM bootcamp b
              LEFT JOIN bootcamp_categories bc ON b.id = bc.bootcamp_id
              LEFT JOIN categories c ON bc.category_id = c.id";

    $whereConditions = [];
    $params = [];

    // Filter berdasarkan user_id (jika diberikan dan bukan null)
    if ($user_id !== null) {
      $whereConditions[] = "b.created_by_user_id = :user_id";
      $params[':user_id'] = $user_id;
    }

    // Filter search
    if ($search !== '') {
      $whereConditions[] = "(b.name ILIKE :search OR b.description ILIKE :search)";
      $params[':search'] = '%' . $search . '%';
    }

    // Filter kategori
    if ($category_id !== null && $category_id > 0) {
      $whereConditions[] = "bc.category_id = :category_id";
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
        if ($key === ':user_id') {
          $stmt->bindValue($key, $value, PDO::PARAM_INT);
        } else {
          $stmt->bindValue($key, $value, PDO::PARAM_STR);
        }
      }

      $stmt->execute();
      return (int)$stmt->fetch(PDO::FETCH_ASSOC)['total'];
    } catch (PDOException $e) {
      error_log("DB Error (countBootcamps): " . $e->getMessage());
      return 0;
    }
  }

  // Insert/Update Bootcamp 
  public function saveBootcamp($data)
  {
    $this->conn->beginTransaction();

    try {
      if (!empty($data["id"])) {
        // UPDATE Bootcamp
        $query = "UPDATE bootcamp 
                  SET name = :name,
                      description = :description,
                      photo = :photo,
                      link = :link
                  WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(":id", $data["id"], PDO::PARAM_INT);
      } else {
        // INSERT Bootcamp
        $query = "INSERT INTO bootcamp (created_by_user_id, name, description, photo, link)
                  VALUES (:created_by_user_id, :name, :description, :photo, :link)";
        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(':created_by_user_id', $data['created_by_user_id'], PDO::PARAM_INT);
      }

      $stmt->bindValue(':name', $data['name'] ?? '', PDO::PARAM_STR);
      $stmt->bindValue(':description', $data['description'] ?? '', PDO::PARAM_STR);
      $stmt->bindValue(':photo', $data['photo'] ?? null, PDO::PARAM_STR);
      $stmt->bindValue(':link', $data['link'] ?? '', PDO::PARAM_STR);

      $stmt->execute();

      // Get bootcamp ID (for new insert)
      $bootcampId = !empty($data["id"]) ? $data["id"] : $this->conn->lastInsertId();

      // Handle categories 
      if (isset($data['categories'])) {
        $this->saveBootcampCategories($bootcampId, $data['categories']);
      }

      $this->conn->commit();
      return $bootcampId;
    } catch (PDOException $e) {
      $this->conn->rollBack();
      error_log("DB Error (saveBootcamp): " . $e->getMessage());
      return false;
    }
  }

  // Save bootcamp categories
  private function saveBootcampCategories($bootcampId, $categories)
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
    $deleteStmt = $this->conn->prepare("DELETE FROM bootcamp_categories WHERE bootcamp_id = :bootcamp_id");
    $deleteStmt->bindValue(':bootcamp_id', $bootcampId, PDO::PARAM_INT);
    $deleteStmt->execute();

    // Insert new categories hanya jika ada
    if (!empty($categories)) {
      $query = "INSERT INTO bootcamp_categories (bootcamp_id, category_id) VALUES (:bootcamp_id, :category_id)";
      $insertStmt = $this->conn->prepare($query);

      foreach ($categories as $categoryId) {
        $insertStmt->bindValue(':bootcamp_id', $bootcampId, PDO::PARAM_INT);
        $insertStmt->bindValue(':category_id', (int)$categoryId, PDO::PARAM_INT);
        $insertStmt->execute();
      }
    }
  }

  // Get single bootcamp by ID 
  public function getById($id)
  {
    try {
      $query = "SELECT 
                  b.*, 
                  u.name as author_name,
                  STRING_AGG(c.name, ', ') as categories,
                  STRING_AGG(CAST(c.id AS VARCHAR), ',') as category_ids
                FROM bootcamp b
                LEFT JOIN users u ON b.created_by_user_id = u.id
                LEFT JOIN bootcamp_categories bc ON b.id = bc.bootcamp_id
                LEFT JOIN categories c ON bc.category_id = c.id
                WHERE b.id = :id
                GROUP BY b.id, u.name";

      $stmt = $this->conn->prepare($query);
      $stmt->execute([":id" => $id]);
      $result = $stmt->fetch(PDO::FETCH_ASSOC);

      return $result ? $this->processCategoryData($result) : null;
    } catch (PDOException $e) {
      error_log("DB Error (getById): " . $e->getMessage());
      return null;
    }
  }

  // Delete Bootcamp
  public function delete($id)
  {
    $this->conn->beginTransaction();

    try {
      // Delete from bootcamp_categories first
      $stmt1 = $this->conn->prepare("DELETE FROM bootcamp_categories WHERE bootcamp_id = :id");
      $stmt1->execute([":id" => $id]);

      // Then delete from bootcamp
      $stmt2 = $this->conn->prepare("DELETE FROM bootcamp WHERE id = :id");
      $result = $stmt2->execute([":id" => $id]);

      $this->conn->commit();
      return $result;
    } catch (PDOException $e) {
      $this->conn->rollBack();
      error_log("DB Error (delete): " . $e->getMessage());
      return false;
    }
  }

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

  // Get bootcamps for API with filters
  public function getBootcampsForApi($limit = 10, $offset = 0, $search = '', $categoryId = null)
  {
    $params = [];
    $where = [];

    if (!empty($search)) {
      $where[] = "(LOWER(name) LIKE LOWER(:search) OR LOWER(description) LIKE LOWER(:search))";
      $params[':search'] = "%$search%";
    }

    if (!empty($categoryId)) {
      $where[] = "category_id = :category_id";
      $params[':category_id'] = $categoryId;
    }

    // Build WHERE
    $whereSql = "";
    if (!empty($where)) {
      $whereSql = "WHERE " . implode(" AND ", $where);
    }

    $sql = "
      SELECT *, 
      TO_CHAR(created_at, 'YYYY-MM-DD') as formatted_date,
      EXTRACT(YEAR FROM created_at) as year,
      EXTRACT(MONTH FROM created_at) as month,
      EXTRACT(DAY FROM created_at) as day
      FROM bootcamp
      $whereSql
      ORDER BY created_at DESC
      LIMIT :limit OFFSET :offset
    ";

    $stmt = $this->conn->prepare($sql);

    foreach ($params as $key => $value) {
      $stmt->bindValue($key, $value);
    }

    $stmt->bindValue(":limit", (int)$limit, PDO::PARAM_INT);
    $stmt->bindValue(":offset", (int)$offset, PDO::PARAM_INT);

    $stmt->execute();
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Format date for each result
    foreach ($results as &$result) {
      if (isset($result['created_at'])) {
        $date = new DateTime($result['created_at']);
        $result['created_at_formatted'] = $date->format('Y-m-d');
        $result['created_at_readable'] = $date->format('d F Y');
      }
    }

    return $results;
  }

  // Get recent bootcamps for API
  public function getRecentBootcampsForApi($limit = 5)
  {
    $sql = "SELECT *, 
            TO_CHAR(created_at, 'YYYY-MM-DD') as formatted_date,
            EXTRACT(YEAR FROM created_at) as year,
            EXTRACT(MONTH FROM created_at) as month,
            EXTRACT(DAY FROM created_at) as day
            FROM bootcamp
            ORDER BY created_at DESC
            LIMIT :limit";
    $stmt = $this->conn->prepare($sql);
    $stmt->bindValue(":limit", (int)$limit, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
  }

  // Count bootcamps for pagination
  public function countBootcampsForApi($search = '', $categoryId = null)
  {
    $params = [];
    $where = [];

    if (!empty($search)) {
      $where[] = "(LOWER(name) LIKE LOWER(:search) OR LOWER(description) LIKE LOWER(:search))";
      $params[':search'] = "%$search%";
    }

    if (!empty($categoryId)) {
      $where[] = "category_id = :category_id";
      $params[':category_id'] = $categoryId;
    }

    // Build WHERE
    $whereSql = "";
    if (!empty($where)) {
      $whereSql = "WHERE " . implode(" AND ", $where);
    }

    $sql = "SELECT COUNT(*) as total FROM bootcamp $whereSql";
    $stmt = $this->conn->prepare($sql);

    foreach ($params as $key => $value) {
      $stmt->bindValue($key, $value);
    }

    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    return (int)$result['total'];
  }

  // Get available years for filtering
  public function getAvailableYears()
  {
    $sql = "
      SELECT DISTINCT EXTRACT(YEAR FROM created_at) as year 
      FROM bootcamp 
      ORDER BY year DESC
    ";

    $stmt = $this->conn->prepare($sql);
    $stmt->execute();

    $years = $stmt->fetchAll(PDO::FETCH_COLUMN);
    return array_map('intval', $years);
  }
}