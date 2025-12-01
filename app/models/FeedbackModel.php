<?php

class FeedbackModel
{
  private $conn;

  public function __construct()
  {
    $db = new Database();
    $this->conn = $db->getConnection();
  }

  // Get All Feedback
  public function getAllFeedback()
  {
    $query = "SELECT * FROM feedbacks ORDER BY created_at DESC";
    try {
      $stmt = $this->conn->prepare($query);
      $stmt->execute();
      return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
      echo "DB Error (getAllFeedback): " . $e->getMessage();
    }
  }

  // Read Data Feedback
  public function getFeedback($limit, $offset, $search = '')
  {
    $query = "SELECT * FROM feedbacks";

    if ($search !== '') {
      $query .= " WHERE name ILIKE :search OR email ILIKE :search OR content ILIKE :search";
    }

    $query .= " ORDER BY created_at DESC LIMIT :limit OFFSET :offset";

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
      echo "DB Error (getFeedback): " . $e->getMessage();
    }
  }

  // Count Feedback
  public function countFeedback($search = '')
  {
    $query = "SELECT COUNT(*) AS total FROM feedbacks";
    if ($search !== '') {
      $query .= " WHERE name LIKE :search OR email LIKE :search OR content LIKE :search";
    }

    try {
      $stmt = $this->conn->prepare($query);
      if ($search !== '') {
        $stmt->bindValue(':search', '%' . $search . '%', PDO::PARAM_STR);
      }
      $stmt->execute();
      return (int)$stmt->fetch(PDO::FETCH_ASSOC)['total'];
    } catch (PDOException $e) {
      echo "DB Error (countFeedback): " . $e->getMessage();
    }
  }

  // Insert New Feedback
  // public function insertFeedback($data)
  // {
  //   $query = "INSERT INTO feedbacks (name, email, content)
  //             VALUES (:name, :email, :content)";

  //   try {
  //     $stmt = $this->conn->prepare($query);
  //     $stmt->bindValue(':name', $data['name']);
  //     $stmt->bindValue(':email', $data['email']);
  //     $stmt->bindValue(':content', $data['content']);
  //     return $stmt->execute();
  //   } catch (PDOException $e) {
  //     error_log("DB Error (insertFeedback): " . $e->getMessage());
  //     return false;
  //   }
  // }

  // Delete Feedback
  public function deleteFeedback($id)
  {
    $query = "DELETE FROM feedbacks WHERE id = :id";
    try {
      $stmt = $this->conn->prepare($query);
      $stmt->bindValue(':id', $id);
      return $stmt->execute();
    } catch (PDOException $e) {
      error_log("DB Error (deleteFeedback): " . $e->getMessage());
      return false;
    }
  }

  // Get Feedback by ID
  public function getFeedbackById($id)
  {
    $query = "SELECT * FROM feedbacks WHERE id = :id LIMIT 1";
    try {
      $stmt = $this->conn->prepare($query);
      $stmt->bindParam(':id', $id);
      $stmt->execute();
      return $stmt->fetch(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
      echo "DB Error (getFeedbackById): " . $e->getMessage();
    }
  }
}
