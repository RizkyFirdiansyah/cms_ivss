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

    $this->userModel = new UserModel();
  }
  public function index()
  {
    $page_title = 'Manajemen Users';
    $page_breadcrumb = ['Pages', 'Manajemen Users'];

    // $user = $this->userModel->getAllUsers();

    include '../app/views/users.php';
  }

  public function getList()
  {
    $page   = isset($_GET['page']) ? (int)$_GET['page'] : 1;
    $limit  = isset($_GET['limit']) ? (int)$_GET['limit'] : 5;
    $search = isset($_GET['search']) ? trim($_GET['search']) : '';

    $offset = ($page - 1) * $limit;

    $data = $this->userModel->getUsers($limit, $offset, $search);
    $total = $this->userModel->countUsers($search);

    header('Content-Type: application/json');
    echo json_encode([
      'data' => $data,
      'total' => $total,
    ]);
    exit;
  }

  // Tambah user baru
  public function create()
  {
    $data = $_POST;
    $success = $this->userModel->insertUser($data);

    echo json_encode(['success' => $success]);
  }

  // Update role/status
  public function update()
  {
    $id = $_POST['id'];
    $role = $_POST['role'];
    $password = $_POST['password'];
    $status = $_POST['status'];

    $success = $this->userModel->updateUser($id, $role, $password, $status);
    echo json_encode(['success' => $success]);
  }

  // Hapus user
  public function delete()
  {
    $id = $_POST['id'];
    $success = $this->userModel->deleteUser($id);
    echo json_encode(['success' => $success]);
  }
}
