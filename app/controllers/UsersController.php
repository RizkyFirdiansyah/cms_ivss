<?php
require_once '../app/models/UserModel.php';
require_once '../app/controllers/BaseController.php';

class UsersController extends BaseController
{

  private $userModel;

  public function __construct()
  {
    parent::__construct();
    parent::requireLogin();
    parent::requireRole('kepala');

    $this->userModel = new UserModel();
  }
  public function index()
  {
    $page_title = 'Manajemen Users';
    $page_breadcrumb = ['Pages', 'Manajemen Users'];

    include '../app/views/users.php';
  }

  // Get All Users
  public function getList()
  {
    $page   = isset($_GET['page']) ? (int)$_GET['page'] : 1;
    $limit  = isset($_GET['limit']) ? (int)$_GET['limit'] : 5;
    $search = isset($_GET['search']) ? trim($_GET['search']) : '';

    $offset = ($page - 1) * $limit;

    $data = $this->userModel->getUsers($limit, $offset, $search);
    $total = $this->userModel->countUsers($search);

    $response_data = [
      'data' => $data,
      'total' => $total,
    ];

    $this->jsonResponse($response_data);
  }

  // ADD New User
  public function create()
  {
    $data = $_POST;
    $success = $this->userModel->insertUser($data);

    $response_data = [
      'success' => $success,
      'message' => $success ? 'User berhasil ditambahkan.' : 'Gagal menambahkan user. Cek log error.'
    ];

    $this->jsonResponse($response_data);
  }

  // Update user (role/status/password)
  public function update()
  {
    $id = $_POST['id_user'];
    $role = $_POST['role'];
    $raw_password = $_POST['password'];
    $is_active = $_POST['is_active'];

    $password_hash = null;

    // Cek apakah password diisi/diubah
    if (!empty($raw_password)) {
      // Hash password hanya jika ada input
      $password_hash = password_hash($raw_password, PASSWORD_DEFAULT);
    }

    $success = $this->userModel->updateUser($id, $role, $is_active, $password_hash);

    $response_data = [
      'success' => $success,
      'message' =>  $success ? 'User berhasil diperbarui.' : 'Gagal memperbarui user. Cek log error.'
    ];

    $this->jsonResponse($response_data);
  }

  // Delete user
  public function delete()
  {
    $id = $_POST['id_user'];
    $success = $this->userModel->deleteUser($id);

    $response_data = [
      'success' => $success,
      'message' => $success ? 'User berhasil dihapus.' : 'Gagal menghapus user. Cek log error.'
    ];

    $this->jsonResponse($response_data);
  }
}
