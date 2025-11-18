<?php

class UserModel
{
  private $conn;

  public function __construct()
  {
    $db = new Database();
    $this->conn = $db->getConnection();
  }

  public function getUserByEmail($email)
  {
    $query = "SELECT * FROM users WHERE email = :email LIMIT 1";
    $stmt = $this->conn->prepare($query);
    $stmt->bindParam(':email', $email);
    $stmt->execute();

    return $stmt->fetch(PDO::FETCH_ASSOC);
  }

  // public function getAllUsers()
  // {
  //   $query = "SELECT u.nama, u.email, u.status, u.foto, u.role,  p.nama_ps FROM users u LEFT JOIN program_studi p ON u.id_ps = p.id_ps ORDER BY id_user;";
  //   $stmt = $this->conn->prepare($query);
  //   $stmt->execute();

  //   return $stmt->fetchAll(PDO::FETCH_ASSOC);
  // }

  // public function updateUser($id_user, $role, $status)
  // {
  //   $query = "UPDATE users SET role = :role, status = :status WHERE id_user = :id_user";
  //   $stmt = $this->conn->prepare($query);
  //   $stmt->bindParam(':role', $role);
  //   $stmt->bindParam(':status', $status);
  //   $stmt->bindParam(':id_user', $id_user);
  //   return $stmt->execute();
  // }

  public function getUsers($limit, $offset, $search = '')
  {
    $query = "SELECT u.id, u.name, u.email, u.status, u.photo, u.role,  p.name FROM users u LEFT JOIN study_programs p ON u.id_ps = p.id_ps";
    if ($search !== '') {
      $query .= " WHERE name LIKE :search OR email LIKE :search";
    }
    $query .= " ORDER BY id LIMIT :limit OFFSET :offset";

    $stmt = $this->conn->prepare($query);

    if ($search !== '') {
      $stmt->bindValue(':search', '%' . $search . '%', PDO::PARAM_STR);
    }
    $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
    $stmt->bindValue(':offset', (int)$offset, PDO::PARAM_INT);
    $stmt->execute();

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
  }

  public function countUsers($search = '')
  {
    $query = "SELECT COUNT(*) AS total FROM users";
    if ($search !== '') {
      $query .= " WHERE nama LIKE :search OR email LIKE :search";
    }

    $stmt = $this->conn->prepare($query);
    if ($search !== '') {
      $stmt->bindValue(':search', '%' . $search . '%', PDO::PARAM_STR);
    }
    $stmt->execute();

    return (int)$stmt->fetch(PDO::FETCH_ASSOC)['total'];
  }

  public function insertUser($data)
  {
    $query = "INSERT INTO users (nama, email, password, id_ps, role, status)
                  VALUES (:nama, :email, :password, :id_ps, :role, :status)";
    $stmt = $this->conn->prepare($query);
    $stmt->bindValue(':nama', $data['nama']);
    $stmt->bindValue(':email', $data['email']);
    $stmt->bindValue(':password', password_hash($data['password'], PASSWORD_DEFAULT));
    $stmt->bindValue(':id_ps', $data['id_ps']);
    $stmt->bindValue(':role', $data['role']);
    $stmt->bindValue(':status', $data['status']);
    return $stmt->execute();
  }

  public function updateUser($id, $role, $password, $status)
  {
    $stmt = $this->conn->prepare("UPDATE users SET role = :role, status = :status, password = :password WHERE id_user = :id_user");
    $stmt->bindValue(':id_user', $id);
    $stmt->bindValue(':role', $role);
    $stmt->bindValue(':password', password_hash($password, PASSWORD_DEFAULT));
    $stmt->bindValue(':status', $status);
    return $stmt->execute();
  }

  public function deleteUser($id)
  {
    $stmt = $this->conn->prepare("DELETE FROM users WHERE id_user = :id_user");
    $stmt->bindValue(':id_user', $id);
    return $stmt->execute();
  }
}
