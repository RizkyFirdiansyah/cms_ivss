<?php
require_once __DIR__ . '/../config/Database.php';

class RegistrationModel
{
  private $conn;

  public function __construct()
  {
    $db = new Database();
    $this->conn = $db->getConnection();
  }

  // Get semua pendaftaran pending
  public function getPendingRegistrations($limit, $offset, $search = '')
  {
    $query = "SELECT 
                u.id,
                u.name as name_user,
                u.email,
                u.created_at,
                u.study_program_id,
                sp.name as name_ps,
                m.nim
              FROM users u
              LEFT JOIN study_programs sp ON u.study_program_id = sp.id
              LEFT JOIN mahasiswa m ON u.id = m.user_id
              WHERE u.role = 'mahasiswa' 
              AND u.is_active = 'pending'";

    if ($search !== '') {
      $query .= " AND (u.name ILIKE :search OR u.email ILIKE :search OR m.nim ILIKE :search)";
    }

    $query .= " ORDER BY u.created_at DESC LIMIT :limit OFFSET :offset";

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
      error_log("DB Error (getPendingRegistrations): " . $e->getMessage());
      return [];
    }
  }

  // Count total pendaftaran pending
  public function countPendingRegistrations($search = '')
  {
    $query = "SELECT COUNT(*) as total
              FROM users u
              LEFT JOIN mahasiswa m ON u.id = m.user_id
              WHERE u.role = 'mahasiswa' 
              AND u.is_active = 'pending'";

    if ($search !== '') {
      $query .= " AND (u.name LIKE :search OR u.email LIKE :search OR m.nim LIKE :search)";
    }

    try {
      $stmt = $this->conn->prepare($query);
      if ($search !== '') {
        $stmt->bindValue(':search', '%' . $search . '%', PDO::PARAM_STR);
      }
      $stmt->execute();
      return (int)$stmt->fetch(PDO::FETCH_ASSOC)['total'];
    } catch (PDOException $e) {
      error_log("DB Error (countPendingRegistrations): " . $e->getMessage());
      return 0;
    }
  }

  // Get detail pendaftaran by ID
  public function getPendingRegistrationDetail($id)
  {
    $query = "SELECT 
                u.id,
                u.name as name_user,
                u.email,
                u.created_at,
                u.study_program_id,
                sp.name as name_ps,
                m.nim
              FROM users u
              LEFT JOIN study_programs sp ON u.study_program_id = sp.id
              LEFT JOIN mahasiswa m ON u.id = m.user_id
              WHERE u.id = :id 
              AND u.role = 'mahasiswa' 
              AND u.is_active = 'pending'";

    try {
      $stmt = $this->conn->prepare($query);
      $stmt->bindValue(':id', $id, PDO::PARAM_INT);
      $stmt->execute();
      return $stmt->fetch(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
      error_log("DB Error (getPendingRegistrationDetail): " . $e->getMessage());
      return false;
    }
  }

  // Get count pending untuk badge
  public function getPendingCount()
  {
    $query = "SELECT COUNT(*) as count 
              FROM users 
              WHERE role = 'mahasiswa' 
              AND is_active = 'pending'";

    try {
      $stmt = $this->conn->prepare($query);
      $stmt->execute();
      return (int)$stmt->fetch(PDO::FETCH_ASSOC)['count'];
    } catch (PDOException $e) {
      error_log("DB Error (getPendingCount): " . $e->getMessage());
      return 0;
    }
  }

  // Insert pendaftaran
  public function insertRegistration($userData, $nim)
  {
    $this->conn->beginTransaction();

    try {
      // 1. Insert ke tabel users dengan password NULL (pending)
      $queryUser = "INSERT INTO users (name, email, password, study_program_id, role, is_active, created_at)
                    VALUES (:name, :email, NULL, :study_program_id, 'mahasiswa', 'pending', NOW())";

      $stmtUser = $this->conn->prepare($queryUser);
      $stmtUser->bindValue(':name', $userData['name']);
      $stmtUser->bindValue(':email', $userData['email']);
      $stmtUser->bindValue(':study_program_id', $userData['study_program_id']);

      if (!$stmtUser->execute()) {
        throw new Exception("Gagal menyimpan data user");
      }

      $userId = $this->conn->lastInsertId();

      // 2. Insert ke tabel mahasiswa
      $queryMahasiswa = "INSERT INTO mahasiswa (nim, user_id) VALUES (:nim, :user_id)";
      $stmtMahasiswa = $this->conn->prepare($queryMahasiswa);
      $stmtMahasiswa->bindValue(':nim', $nim);
      $stmtMahasiswa->bindValue(':user_id', $userId);

      if (!$stmtMahasiswa->execute()) {
        throw new Exception("Gagal menyimpan data mahasiswa");
      }

      $this->conn->commit();
      return $userId;
    } catch (Exception $e) {
      $this->conn->rollBack();
      error_log("DB Error (insertRegistration): " . $e->getMessage());
      return false;
    }
  }

  // Approve pendaftaran
  public function approveRegistration($id)
  {
    $this->conn->beginTransaction();

    try {
      // 1. Get user data untuk email
      $queryGet = "SELECT u.email, u.name FROM users u WHERE u.id = :id";
      $stmtGet = $this->conn->prepare($queryGet);
      $stmtGet->bindValue(':id', $id, PDO::PARAM_INT);
      $stmtGet->execute();
      $userData = $stmtGet->fetch(PDO::FETCH_ASSOC);

      if (!$userData) {
        throw new Exception("User tidak ditemukan");
      }

      // 2. Generate password otomatis
      $generatedPassword = $this->generateRandomPassword();
      $hashedPassword = password_hash($generatedPassword, PASSWORD_DEFAULT);

      // 3. Update status jadi aktif DAN set password
      $queryUpdate = "UPDATE users SET is_active = 'aktif', password = :password WHERE id = :id";
      $stmtUpdate = $this->conn->prepare($queryUpdate);
      $stmtUpdate->bindValue(':password', $hashedPassword);
      $stmtUpdate->bindValue(':id', $id, PDO::PARAM_INT);

      if (!$stmtUpdate->execute()) {
        throw new Exception("Gagal update status user");
      }

      $this->conn->commit();

      return [
        'email' => $userData['email'],
        'name' => $userData['name'],
        'generated_password' => $generatedPassword
      ];
    } catch (Exception $e) {
      $this->conn->rollBack();
      error_log("DB Error (approveRegistration): " . $e->getMessage());
      return false;
    }
  }

  // Reject pendaftaran - hapus user jika ditolak
  public function rejectRegistration($id)
  {
    $this->conn->beginTransaction();

    try {
      // 1. Get user data untuk email
      $queryGet = "SELECT u.email, u.name FROM users u WHERE u.id = :id";
      $stmtGet = $this->conn->prepare($queryGet);
      $stmtGet->bindValue(':id', $id, PDO::PARAM_INT);
      $stmtGet->execute();
      $userData = $stmtGet->fetch(PDO::FETCH_ASSOC);

      if (!$userData) {
        throw new Exception("User tidak ditemukan");
      }

      // 2. Hapus dari tabel mahasiswa terlebih dahulu (foreign key constraint)
      $queryDeleteMahasiswa = "DELETE FROM mahasiswa WHERE user_id = :id";
      $stmtDeleteMahasiswa = $this->conn->prepare($queryDeleteMahasiswa);
      $stmtDeleteMahasiswa->bindValue(':id', $id, PDO::PARAM_INT);

      if (!$stmtDeleteMahasiswa->execute()) {
        throw new Exception("Gagal menghapus data mahasiswa");
      }

      // 3. Hapus dari tabel users
      $queryDeleteUser = "DELETE FROM users WHERE id = :id";
      $stmtDeleteUser = $this->conn->prepare($queryDeleteUser);
      $stmtDeleteUser->bindValue(':id', $id, PDO::PARAM_INT);

      if (!$stmtDeleteUser->execute()) {
        throw new Exception("Gagal menghapus data user");
      }

      $this->conn->commit();
      return $userData;
    } catch (Exception $e) {
      $this->conn->rollBack();
      error_log("DB Error (rejectRegistration): " . $e->getMessage());
      return false;
    }
  }

  // Helper function
  // Gett all program studi
  public function getAllProgramStudi()
  {
    $query = "SELECT id, name FROM study_programs;
";
    try {
      $stmt = $this->conn->prepare($query);
      $stmt->execute();
      return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
      error_log("DB Error (getAllProgramStudi): " . $e->getMessage());
      return false;
    }
  }

  // Check duplicate email/NIM
  public function checkDuplicateRegistration($email, $nim)
  {
    $query = "SELECT 
                (SELECT COUNT(*) FROM users WHERE email = :email) as email_count,
                (SELECT COUNT(*) FROM mahasiswa WHERE nim = :nim) as nim_count";

    try {
      $stmt = $this->conn->prepare($query);
      $stmt->bindValue(':email', $email);
      $stmt->bindValue(':nim', $nim);
      $stmt->execute();

      $result = $stmt->fetch(PDO::FETCH_ASSOC);
      return [
        'email_exists' => $result['email_count'] > 0,
        'nim_exists' => $result['nim_count'] > 0
      ];
    } catch (PDOException $e) {
      error_log("DB Error (checkDuplicateRegistration): " . $e->getMessage());
      return ['email_exists' => false, 'nim_exists' => false];
    }
  }

  // Generate random password untuk approval  
  private function generateRandomPassword($length = 8)
  {
    $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%^&*';
    $password = '';
    for ($i = 0; $i < $length; $i++) {
      $password .= $chars[random_int(0, strlen($chars) - 1)];
    }
    return $password;
  }
}
