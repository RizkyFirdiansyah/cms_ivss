<?php
require_once __DIR__ . '/../config/Database.php';

class FacilitiesModel
{
  private $conn;

  public function __construct()
  {
    $db = new Database();
    $this->conn = $db->getConnection();
  }

  // Read Data Facility
  public function getFacility($limit, $offset, $search = '')
  {
    $query = "SELECT f.id, f.name, f.description, f.photo FROM facilities f";

    if ($search !== '') {
      $query .= " WHERE f.name ILIKE :search";
    }

    $query .= " ORDER BY f.id LIMIT :limit OFFSET :offset";

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
      error_log("DB Error (getFacility): " . $e->getMessage());
      return [];
    }
  }

  // Count Facility
  public function countFacility($search = '')
  {
    $query = "SELECT COUNT(*) AS total FROM facilities f";
    if ($search !== '') {
      $query .= " WHERE f.name ILIKE :search";
    }

    try {
      $stmt = $this->conn->prepare($query);
      if ($search !== '') {
        $stmt->bindValue(':search', '%' . $search . '%', PDO::PARAM_STR);
      }
      $stmt->execute();
      return (int)$stmt->fetch(PDO::FETCH_ASSOC)['total'];
    } catch (PDOException $e) {
      error_log("DB Error (countFacility): " . $e->getMessage());
      return 0;
    }
  }

  // Insert/Update New facility
  public function saveFacility($data)
  {
    try {
      if (!empty($data["id"])) {
        // UPDATE
        $query = "UPDATE facilities SET name = :name, description = :description, photo = :photo WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(":id", $data["id"]);
      } else {
        // INSERT
        $query = "INSERT INTO facilities (user_id, name, description, photo) VALUES (:user_id, :name, :description, :photo)";
        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(':user_id', $data['user_id'] ?? null, PDO::PARAM_INT);
      }

      $stmt->bindValue(':name', $data['name'] ?? null, PDO::PARAM_STR);
      $stmt->bindValue(':description', $data['description'] ?? null, PDO::PARAM_STR);
      $stmt->bindValue(':photo', $data['photo'] ?? null, PDO::PARAM_STR);

      return $stmt->execute();
    } catch (PDOException $e) {
      error_log("DB Error (saveFacility): " . $e->getMessage());
      return false;
    }
  }

  // Get single facility by ID 
  public function getById($id)
  {
    try {
      $query = "SELECT * FROM facilities WHERE id = :id LIMIT 1";
      $stmt = $this->conn->prepare($query);
      $stmt->execute([":id" => $id]);
      return $stmt->fetch(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
      error_log("DB Error (getById): " . $e->getMessage());
      return null;
    }
  }

  // Delete Facility
  public function delete($id)
  {
    try {
      $query = "DELETE FROM facilities WHERE id = :id";
      $stmt = $this->conn->prepare($query);
      return $stmt->execute([":id" => $id]);
    } catch (PDOException $e) {
      error_log("DB Error (delete): " . $e->getMessage());
      return false;
    }
  }

  // Get all (untuk API)
  public function getAll()
  {
    try {
      $query = "SELECT * FROM facilities ORDER BY id";
      $stmt = $this->conn->prepare($query);
      $stmt->execute();
      return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
      error_log("DB Error (getAll): " . $e->getMessage());
      return [];
    }
  }
}
