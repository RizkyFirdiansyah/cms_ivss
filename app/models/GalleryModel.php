<?php
require_once '../app/config/Database.php';

class GalleryModel
{
  private $conn;

  public function __construct()
  {
    $db = new Database();
    $this->conn = $db->getConnection();
  }

  // Read Data Gallery
  public function getGallery($limit, $offset, $search = '')
  {
    $query = "SELECT g.id, g.user_id, g.title, g.link, g.created_at, g.updated_at FROM gallery g";

    if ($search !== '') {
      $query .= " WHERE g.title ILIKE :search";
    }

    $query .= " ORDER BY g.id DESC LIMIT :limit OFFSET :offset";

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
      echo "DB Error (getGallery): " . $e->getMessage();
    }
  }

  // Count Gallery
  public function countGallery($search = '')
  {
    $query = "SELECT COUNT(*) AS total FROM gallery g";
    if ($search !== '') {
      $query .= " WHERE g.title ILIKE :search";
    }

    try {
      $stmt = $this->conn->prepare($query);
      if ($search !== '') {
        $stmt->bindValue(':search', '%' . $search . '%', PDO::PARAM_STR);
      }
      $stmt->execute();
      return (int)$stmt->fetch(PDO::FETCH_ASSOC)['total'];
    } catch (PDOException $e) {
      echo "DB Error (countGallery): " . $e->getMessage();
    }
  }

  // Insert/Update Gallery
  public function saveGallery($data)
  {
    try {
      if (!empty($data["id"])) {
        // UPDATE
        $query = "UPDATE gallery 
                  SET user_id = :user_id,
                      title = :title,
                      link = :link,
                      updated_at = NOW()
                  WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(":id", $data["id"], PDO::PARAM_INT);
      } else {
        // INSERT
        $query = "INSERT INTO gallery (user_id, title, link)
                  VALUES (:user_id, :title, :link)";
        $stmt = $this->conn->prepare($query);
      }

      $stmt->bindValue(':user_id', $data['user_id'], PDO::PARAM_INT);
      $stmt->bindValue(':title', $data['title'], PDO::PARAM_STR);
      $stmt->bindValue(':link', $data['link'], PDO::PARAM_STR);

      return $stmt->execute();
    } catch (PDOException $e) {
      error_log("DB Error (saveGallery): " . $e->getMessage());
      return false;
    }
  }

  // Get Gallery by ID
  public function getById($id)
  {
    $stmt = $this->conn->prepare("SELECT * FROM gallery WHERE id = :id LIMIT 1");
    $stmt->execute([":id" => $id]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
  }

  // Delete Gallery
  public function delete($id)
  {
    $stmt = $this->conn->prepare("DELETE FROM gallery WHERE id = :id");
    return $stmt->execute([":id" => $id]);
  }
}
