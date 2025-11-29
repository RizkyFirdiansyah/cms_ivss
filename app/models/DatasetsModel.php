<?php
require_once '../app/config/Database.php';

class DatasetsModel
{
  private $conn;

  public function __construct()
  {
    $db = new Database();
    $this->conn = $db->getConnection();
  }

  // Read Data Datasets
  public function getDatasets($limit, $offset, $search = '', $user_id = null, $current_user_id = null)
{
  $query = "SELECT d.id, d.user_id, d.title, d.link, d.created_at, d.updated_at, u.name as author_name 
            FROM datasets d
            LEFT JOIN users u ON d.user_id = u.id";

  $whereConditions = [];
  $params = [];

  if ($search !== '') {
    $whereConditions[] = "d.title ILIKE :search";
    $params[':search'] = '%' . $search . '%';
  }

  if ($user_id !== null && $user_id > 0) {
    $whereConditions[] = "d.user_id = :user_id";
    $params[':user_id'] = $user_id;
  }

  if (!empty($whereConditions)) {
    $query .= " WHERE " . implode(" AND ", $whereConditions);
  }

  // Prioritaskan dataset milik user sendiri di urutan atas
  if ($current_user_id) {
    $query .= " ORDER BY 
                CASE WHEN d.user_id = :current_user_id THEN 0 ELSE 1 END,
                d.created_at DESC";
    $params[':current_user_id'] = $current_user_id;
  } else {
    $query .= " ORDER BY d.created_at DESC";
  }

  $query .= " LIMIT :limit OFFSET :offset";

  try {
    $stmt = $this->conn->prepare($query);
    
    // Bind search and user filter parameters
    foreach ($params as $key => $value) {
      $stmt->bindValue($key, $value, PDO::PARAM_STR);
    }
    
    $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
    $stmt->bindValue(':offset', (int)$offset, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
  } catch (PDOException $e) {
    error_log("DB Error (getDatasets): " . $e->getMessage());
    return [];
  }
}

  // Count Datasets
  public function countDatasets($search = '', $user_id = null)
{
  $query = "SELECT COUNT(*) AS total FROM datasets d";
  
  $whereConditions = [];
  $params = [];

  if ($search !== '') {
    $whereConditions[] = "d.title ILIKE :search";
    $params[':search'] = '%' . $search . '%';
  }

  if ($user_id !== null && $user_id > 0) {
    $whereConditions[] = "d.user_id = :user_id";
    $params[':user_id'] = $user_id;
  }

  if (!empty($whereConditions)) {
    $query .= " WHERE " . implode(" AND ", $whereConditions);
  }

  try {
    $stmt = $this->conn->prepare($query);
    
    // Bind search and user filter parameters
    foreach ($params as $key => $value) {
      $stmt->bindValue($key, $value, PDO::PARAM_STR);
    }
    
    $stmt->execute();
    return (int)$stmt->fetch(PDO::FETCH_ASSOC)['total'];
  } catch (PDOException $e) {
    error_log("DB Error (countDatasets): " . $e->getMessage());
    return 0;
  }
}

  // Insert/Update Dataset
  public function saveDataset($data)
  {
    try {
      if (!empty($data["id"])) {
        // UPDATE
        $query = "UPDATE datasets 
                  SET user_id = :user_id,
                      title = :title,
                      link = :link,
                      updated_at = NOW()
                  WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(":id", $data["id"], PDO::PARAM_INT);
      } else {
        // INSERT
        $query = "INSERT INTO datasets (user_id, title, link)
                  VALUES (:user_id, :title, :link)";
        $stmt = $this->conn->prepare($query);
      }

      $stmt->bindValue(':user_id', $data['user_id'], PDO::PARAM_INT);
      $stmt->bindValue(':title', $data['title'], PDO::PARAM_STR);
      $stmt->bindValue(':link', $data['link'], PDO::PARAM_STR);

      $result = $stmt->execute();
      
      if ($result && empty($data["id"])) {
        return $this->conn->lastInsertId();
      }
      
      return $result;
    } catch (PDOException $e) {
      error_log("DB Error (saveDataset): " . $e->getMessage());
      return false;
    }
  }

  // Get Dataset by ID
  public function getById($id)
  {
    $query = "SELECT d.*, u.name as author_name 
              FROM datasets d
              LEFT JOIN users u ON d.user_id = u.id
              WHERE d.id = :id LIMIT 1";
              
    try {
      $stmt = $this->conn->prepare($query);
      $stmt->execute([":id" => $id]);
      return $stmt->fetch(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
      error_log("DB Error (getById): " . $e->getMessage());
      return null;
    }
  }

  // Delete Dataset
  public function delete($id)
  {
    try {
      $stmt = $this->conn->prepare("DELETE FROM datasets WHERE id = :id");
      return $stmt->execute([":id" => $id]);
    } catch (PDOException $e) {
      error_log("DB Error (delete): " . $e->getMessage());
      return false;
    }
  }

  // Get datasets by user ID
  public function getByUserId($user_id, $limit = 10, $offset = 0)
  {
    $query = "SELECT d.*, u.name as author_name 
              FROM datasets d
              LEFT JOIN users u ON d.user_id = u.id
              WHERE d.user_id = :user_id
              ORDER BY d.created_at DESC 
              LIMIT :limit OFFSET :offset";

    try {
      $stmt = $this->conn->prepare($query);
      $stmt->bindValue(':user_id', $user_id, PDO::PARAM_INT);
      $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
      $stmt->bindValue(':offset', (int)$offset, PDO::PARAM_INT);
      $stmt->execute();
      return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
      error_log("DB Error (getByUserId): " . $e->getMessage());
      return [];
    }
  }

  // Get recent datasets
  public function getRecentDatasets($limit = 10)
  {
    $query = "SELECT d.*, u.name as author_name 
              FROM datasets d
              LEFT JOIN users u ON d.user_id = u.id
              ORDER BY d.created_at DESC 
              LIMIT :limit";
              
    try {
      $stmt = $this->conn->prepare($query);
      $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
      $stmt->execute();
      return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
      error_log("DB Error (getRecentDatasets): " . $e->getMessage());
      return [];
    }
  }
}
?>