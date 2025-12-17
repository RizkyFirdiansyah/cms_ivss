<?php
require_once __DIR__ . '/../config/Database.php';

class CategoryModel
{
  private $conn;

  public function __construct()
  {
    $db = new Database();
    $this->conn = $db->getConnection();
  }

  // Read Data categories
  public function getCategories($limit, $offset, $search = '')
  {
    $query = "SELECT * FROM categories";

    if ($search !== '') {
      $query .= " WHERE name ILIKE :search";
    }

    $query .= " ORDER BY name LIMIT :limit OFFSET :offset";

    try {
      $stmt = $this->conn->prepare($query);
      if ($search !== '') {
        $stmt->bindValue(':search', '%' . $search . '%', PDO::PARAM_STR);
      }
      $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
      $stmt->bindValue(':offset', (int)$offset, PDO::PARAM_INT);
      $stmt->execute();
      return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
      error_log("DB Error (CategoryModel::getAll): " . $e->getMessage());
      return [];
    }
  }

  // Count categories
  public function countCategories($search = '')
  {
    $query = "SELECT COUNT(*) AS total FROM categories";
    if ($search !== '') {
      $query .= " WHERE name ILIKE :search";
    }
    try {
      $stmt = $this->conn->prepare($query);
      if ($search !== '') {
        $stmt->bindValue(':search', '%' . $search . '%', PDO::PARAM_STR);
      }
      $stmt->execute();
      return (int)$stmt->fetch(PDO::FETCH_ASSOC)['total'];
    } catch (PDOException $e) {
      error_log("DB Error (CategoryModel::countCategories): " . $e->getMessage());
      return 0;
    }
  }

  // Get All Categories
  public function getAll()
  {
    $query = "SELECT * FROM categories";
    try {
      $stmt = $this->conn->prepare($query);
      $stmt->execute();
      return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
      error_log("DB Error (CategoryModel::getAll): " . $e->getMessage());
      return 0;
    }
  }

  // Get category by ID (Validasi input)
  public function getById($id)
  {
    $query = "SELECT * FROM categories WHERE id = :id LIMIT 1";
    try {
      $id = (int)$id;
      if ($id <= 0) {
        return null;
      }

      $stmt = $this->conn->prepare($query);
      $stmt->execute([":id" => $id]);
      return $stmt->fetch(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
      error_log("DB Error (CategoryModel::getById): " . $e->getMessage());
      return null;
    }
  }

  // Create new category
  public function create($name)
  {
    $query = "INSERT INTO categories (name) VALUES (:name)";
    try {
      $name = trim($name);
      if (empty($name)) {
        return false;
      }

      $stmt = $this->conn->prepare($query);
      $stmt->bindValue(':name', $name, PDO::PARAM_STR);
      $result = $stmt->execute();

      return $result ? $this->conn->lastInsertId() : false;
    } catch (PDOException $e) {
      error_log("DB Error (CategoryModel::create): " . $e->getMessage());
      return false;
    }
  }

  // Update category 
  public function update($id, $name)
  {
    $query = "UPDATE categories SET name = :name WHERE id = :id";
    try {
      $id = (int)$id;
      $name = trim($name);

      if ($id <= 0 || empty($name)) {
        return false;
      }

      $stmt = $this->conn->prepare($query);
      $stmt->bindValue(':name', $name, PDO::PARAM_STR);
      $stmt->bindValue(':id', $id, PDO::PARAM_INT);
      return $stmt->execute();
    } catch (PDOException $e) {
      error_log("DB Error (CategoryModel::update): " . $e->getMessage());
      return false;
    }
  }

  // Delete category 
  public function delete($id)
  {
    $query = "DELETE FROM categories WHERE id = :id";
    try {
      $stmt = $this->conn->prepare($query);
      $stmt->bindValue(':id', $id, PDO::PARAM_INT);
      return $stmt->execute();
    } catch (PDOException $e) {
      error_log("DB Error (CategoryModel::delete): " . $e->getMessage());
      return false;
    }
  }

  // Check if category exists by name
  public function exists($name)
  {
    $query = "SELECT id FROM categories WHERE LOWER(name) = LOWER(:name) LIMIT 1";
    try {
      $name = trim($name);
      if (empty($name)) {
        return false;
      }

      $stmt = $this->conn->prepare($query);
      $stmt->execute([":name" => $name]);
      return $stmt->fetch(PDO::FETCH_ASSOC) !== false;
    } catch (PDOException $e) {
      error_log("DB Error (CategoryModel::exists): " . $e->getMessage());
      return false;
    }
  }

  // Check if category exists by ID 
  public function existsById($id)
  {
    $query = "SELECT id FROM categories WHERE id = :id LIMIT 1";
    try {
      $id = (int)$id;
      if ($id <= 0) {
        return false;
      }

      $stmt = $this->conn->prepare($query);
      $stmt->execute([":id" => $id]);
      return $stmt->fetch(PDO::FETCH_ASSOC) !== false;
    } catch (PDOException $e) {
      error_log("DB Error (CategoryModel::existsById): " . $e->getMessage());
      return false;
    }
  }
}
