<?php
require_once __DIR__ . '/../config/Database.php';
class UserModel
{
  private $conn;

  public function __construct()
  {
    $db = new Database();
    $this->conn = $db->getConnection();
  }

  // Read Data by Email (auth)
  public function getUserByEmail($email)
  {
    $query = "SELECT * FROM users WHERE email = :email LIMIT 1";
    try {
      $stmt = $this->conn->prepare($query);
      $stmt->bindParam(':email', $email);
      $stmt->execute();
      return $stmt->fetch(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
      echo "DB Error (getUserByEmail): " . $e->getMessage();
    }
  }

  // Read Data Users
  public function getUsers($limit, $offset, $search = '')
  {
    $query = "SELECT u.id, u.name AS name_user, u.email, u.is_active, u.photo, u.role, p.name AS name_ps 
              FROM users u 
              LEFT JOIN study_programs p ON u.study_program_id = p.id";

    if ($search !== '') {
      $query .= " WHERE u.name ILIKE :search OR u.email ILIKE :search";
    }

    $query .= " ORDER BY u.id LIMIT :limit OFFSET :offset";

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
      echo "DB Error (getUsers): " . $e->getMessage();
    }
  }

  // Count Users
  public function countUsers($search = '')
  {
    $query = "SELECT COUNT(*) AS total FROM users u";
    if ($search !== '') {
      $query .= " WHERE u.name LIKE :search OR u.email LIKE :search";
    }

    try {
      $stmt = $this->conn->prepare($query);
      if ($search !== '') {
        $stmt->bindValue(':search', '%' . $search . '%', PDO::PARAM_STR);
      }
      $stmt->execute();
      return (int)$stmt->fetch(PDO::FETCH_ASSOC)['total'];
    } catch (PDOException $e) {
      echo "DB Error (countUsers): " . $e->getMessage();
    }
  }

  // Insert New Users
  public function insertUser($data)
  {
    $query = "INSERT INTO users (name, email, password, study_program_id, role, is_active)
              VALUES (:name, :email, :password, :study_program_id, :role, :is_active)";

    try {
      $stmt = $this->conn->prepare($query);
      $stmt->bindValue(':name', $data['name']);
      $stmt->bindValue(':email', $data['email']);
      $stmt->bindValue(':password', password_hash($data['password'], PASSWORD_DEFAULT));
      $stmt->bindValue(':study_program_id', $data['study_program_id']);
      $stmt->bindValue(':role', $data['role']);
      $stmt->bindValue(':is_active', $data['is_active']);
      return $stmt->execute();
    } catch (PDOException $e) {
      error_log("DB Error (insertUser): " . $e->getMessage());
      return false;
    }
  }

  // Update User
  public function updateUser($id, $role, $is_active, $password_hash = null)
  {
    $query = "UPDATE users SET role = :role, is_active = :is_active";

    if ($password_hash !== null) {
      $query .= ", password = :password";
    }

    $query .= " WHERE id = :user_id";

    try {
      $stmt = $this->conn->prepare($query);
      $stmt->bindValue(':user_id', $id);
      $stmt->bindValue(':role', $role);
      $stmt->bindValue(':is_active', $is_active);
      if ($password_hash !== null) {
        $stmt->bindValue(':password', $password_hash);
      }
      return $stmt->execute();
    } catch (PDOException $e) {
      error_log("DB Error (updateUser): " . $e->getMessage());
      return false;
    }
  }

  // get all User
  public function getAllUsers()
  {
    $query = "SELECT id, name FROM users ORDER BY name ASC";
    try {
      $stmt = $this->conn->prepare($query);
      $stmt->execute();
      return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
      error_log("DB Error (getAllUsers): " . $e->getMessage());
      return false;
    }
  }


  // Delete User
  public function deleteUser($id)
  {
    $query = "DELETE FROM users WHERE id = :id_user";
    try {
      $stmt = $this->conn->prepare($query);
      $stmt->bindValue(':id_user', $id);
      return $stmt->execute();
    } catch (PDOException $e) {
      error_log("DB Error (deleteUser): " . $e->getMessage());
      return false;
    }
  }

  // Refresh Materialized Views
  public function refreshMaterializedViews()
  {
    try {
      $this->conn->exec("REFRESH MATERIALIZED VIEW mv_user_social_media;");
      return true;
    } catch (PDOException $e) {
      error_log("DB Error (refreshMaterializedViews): " . $e->getMessage());
      return false;
    }
  }

  // Get All users with sosmed for API
  public function getAllUsersWithSosmed()
  {
    $query = "SELECT * FROM mv_user_social_media";
    try {
      $stmt = $this->conn->prepare($query);
      $stmt->execute();
      return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
      error_log("DB Error (getAllUsersWithSosmed): " . $e->getMessage());
      return false;
    }
  }

  // Get User by ID for API
  public function getUserById($id)
  {
    $query = "SELECT * FROM users WHERE id = :id_user";
    try {
      $stmt = $this->conn->prepare($query);
      $stmt->bindValue(':id_user', $id);
      $stmt->execute();
      return $stmt->fetch(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
      error_log("DB Error (getUserById): " . $e->getMessage());
      return false;
    }
  }

  // Method untuk mendapatkan semua user kecuali yang dikecualikan
  public function getAllUsersExcept($excludeUserId)
  {
    try {
      $query = "SELECT 
                        id, 
                        name, 
                        email, 
                        role,
                        CONCAT(name, ' (', role, ')') as display_name
                      FROM users 
                      WHERE id != :exclude_id 
                      AND is_active = 'aktif'
                      ORDER BY role DESC, name ASC";

      $stmt = $this->conn->prepare($query);
      $stmt->bindValue(':exclude_id', $excludeUserId, PDO::PARAM_INT);
      $stmt->execute();

      return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
      error_log("DB Error (getAllUsersExcept): " . $e->getMessage());
      return [];
    }
  }
}
