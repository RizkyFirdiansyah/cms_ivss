<?php
require_once '../app/config/Database.php';

class PublicationsModel
{
  private $conn;

  public function __construct()
  {
    $db = new Database();
    $this->conn = $db->getConnection();
  }

  // Read Data Publications
  public function getPublications($limit, $offset, $search = '')
  {
    $query = "SELECT p.id, p.user_id, p.title, p.link, p.publication_year, p.last_updated 
              FROM publications p";

    if ($search !== '') {
      $query .= " WHERE p.title ILIKE :search";
    }

    $query .= " ORDER BY p.publication_year DESC, p.id DESC LIMIT :limit OFFSET :offset";

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
      echo "DB Error (getPublications): " . $e->getMessage();
    }
  }

  // Count Publications
  public function countPublications($search = '')
  {
    $query = "SELECT COUNT(*) AS total FROM publications p";
    if ($search !== '') {
      $query .= " WHERE p.title ILIKE :search";
    }

    try {
      $stmt = $this->conn->prepare($query);
      if ($search !== '') {
        $stmt->bindValue(':search', '%' . $search . '%', PDO::PARAM_STR);
      }
      $stmt->execute();
      return (int)$stmt->fetch(PDO::FETCH_ASSOC)['total'];
    } catch (PDOException $e) {
      echo "DB Error (countPublications): " . $e->getMessage();
    }
  }

  // Insert/Update Publication
  public function savePublication($data)
  {
    try {
      if (!empty($data["id"])) {
        // UPDATE
        $query = "UPDATE publications 
                  SET user_id = :user_id,
                      title = :title,
                      link = :link,
                      publication_year = :publication_year,
                      last_updated = CURRENT_TIMESTAMP
                  WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(":id", $data["id"], PDO::PARAM_INT);
      } else {
        // INSERT
        $query = "INSERT INTO publications (user_id, title, link, publication_year)
                  VALUES (:user_id, :title, :link, :publication_year)";
        $stmt = $this->conn->prepare($query);
      }

      $stmt->bindValue(':user_id', $data['user_id'], PDO::PARAM_INT);
      $stmt->bindValue(':title', $data['title'], PDO::PARAM_STR);
      $stmt->bindValue(':link', $data['link'], PDO::PARAM_STR);
      $stmt->bindValue(':publication_year', $data['publication_year'], PDO::PARAM_INT);

      return $stmt->execute();
    } catch (PDOException $e) {
      error_log("DB Error (savePublication): " . $e->getMessage());
      return false;
    }
  }

  // Get Publication by ID
  public function getById($id)
  {
    $stmt = $this->conn->prepare("SELECT * FROM publications WHERE id = :id LIMIT 1");
    $stmt->execute([":id" => $id]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
  }

  // Delete Publication
  public function delete($id)
  {
    $stmt = $this->conn->prepare("DELETE FROM publications WHERE id = :id");
    return $stmt->execute([":id" => $id]);
  }
}