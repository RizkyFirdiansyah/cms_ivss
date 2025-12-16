<?php
require_once '../app/models/ResearchModel.php';
require_once '../app/models/CategoryModel.php';
require_once '../app/models/UserModel.php';
require_once '../app/controllers/BaseController.php';

class ResearchController extends BaseController
{
  private $research;
  private $category;
  private $userModel;

  public function __construct()
  {
    parent::__construct();
    parent::requireLogin();

    $this->research = new ResearchModel();
    $this->category = new CategoryModel();
    $this->userModel = new UserModel();
  }

  public function index()
  {
    $page_title = 'Research';
    $page_breadcrumb = ['Pages', 'Research'];

    include '../app/views/research.php';
  }

  // Get All Research dengan role-based filtering
  public function getList()
  {
    try {
      $page   = isset($_GET['page']) ? (int)$_GET['page'] : 1;
      $limit  = isset($_GET['limit']) ? (int)$_GET['limit'] : 6;
      $search = isset($_GET['search']) ? trim($_GET['search']) : '';
      $category_id = isset($_GET['category_id']) ? (int)$_GET['category_id'] : null;
      $status = isset($_GET['status']) ? trim($_GET['status']) : '';

      $offset = ($page - 1) * $limit;

      // Get current user data
      $currentUser = $this->user;
      $userId = $currentUser['id'];
      $userRole = $currentUser['role'];

      // Untuk mahasiswa, tidak perlu filter search/category jika tidak ada research
      if ($userRole === 'mahasiswa' && empty($search) && empty($category_id) && empty($status)) {
        // Tidak perlu log khusus
      }

      // Get data dengan filter role
      $data = $this->research->getResearch($limit, $offset, $search, $category_id, $status, $userId, $userRole);
      $total = $this->research->countResearch($search, $category_id, $status, $userId, $userRole);

      $response_data = [
        'success' => true,
        'data' => $data,
        'total' => $total,
        'user_role' => $userRole
      ];

      $this->jsonResponse($response_data);
    } catch (Exception $e) {
      error_log("Research List Error: " . $e->getMessage());
      $this->jsonResponse([
        'success' => false,
        'message' => 'Error: ' . $e->getMessage(),
        'data' => [],
        'total' => 0
      ]);
    }
  }

  // Get All Categories
  public function getCategories()
  {
    try {
      $categories = $this->category->getAll();
      $this->jsonResponse([
        'success' => true,
        'data' => $categories
      ]);
    } catch (Exception $e) {
      error_log("Research Categories Error: " . $e->getMessage());
      $this->jsonResponse([
        'success' => false,
        'data' => []
      ]);
    }
  }

  // Get All Users untuk dropdown participants
  public function getUsers()
  {
    try {
      // Dapatkan semua user kecuali yang sedang login
      $currentUser = $this->user;
      $users = $this->userModel->getAllUsersExcept($currentUser['id']);

      $this->jsonResponse([
        'success' => true,
        'data' => $users
      ]);
    } catch (Exception $e) {
      error_log("Research Users Error: " . $e->getMessage());
      $this->jsonResponse([
        'success' => false,
        'data' => []
      ]);
    }
  }

  // Create New Research dengan validasi role
  public function create()
  {
    $currentUser = $this->user;

    // Validasi role: mahasiswa tidak bisa membuat research
    if ($currentUser['role'] === 'mahasiswa') {
      $this->jsonResponse([
        "success" => false,
        "message" => "Maaf, mahasiswa tidak dapat membuat penelitian."
      ], 403);
      return;
    }

    try {
      // Parse categories
      $categoryIds = [];
      if (!empty($_POST["categories"])) {
        if (is_string($_POST["categories"])) {
          $categoryIds = json_decode($_POST["categories"], true);
        } else {
          $categoryIds = $_POST["categories"];
        }
        $categoryIds = array_map('intval', $categoryIds);
        $categoryIds = array_filter($categoryIds);
      }

      // Parse participants
      $participantIds = [];
      if (!empty($_POST["participants"])) {
        if (is_string($_POST["participants"])) {
          $participantIds = json_decode($_POST["participants"], true);
        } else {
          $participantIds = $_POST["participants"];
        }
        $participantIds = array_map('intval', $participantIds);
        $participantIds = array_filter($participantIds);
      }

      // Parse data
      $start_date = !empty($_POST["start_date"]) ? $_POST["start_date"] : date('Y-m-d');
      $finish_date = !empty($_POST["finish_date"]) ? $_POST["finish_date"] : null;
      $budget = !empty($_POST["budget"]) ? (int)$_POST["budget"] : 0;
      $max_participants = !empty($_POST["max_participants"]) ? (int)$_POST["max_participants"] : 10;

      $save = $this->research->saveResearch([
        "created_by_user_id" => $currentUser["id"],
        "title" => $_POST["title"] ?? '',
        "status" => $_POST["status"] ?? 'ongoing',
        "description" => $_POST["description"] ?? '',
        "budget" => $budget,
        "max_participants" => $max_participants,
        "start_date" => $start_date,
        "finish_date" => $finish_date,
        "categories" => $categoryIds,
        "participants" => $participantIds
      ]);

      if ($save) {
        $this->jsonResponse([
          "success" => true,
          "message" => "Research berhasil ditambahkan.",
          "id" => $save
        ]);
      } else {
        $this->jsonResponse([
          "success" => false,
          "message" => "Gagal menambah research."
        ]);
      }
    } catch (Exception $e) {
      error_log("Create Research Exception: " . $e->getMessage());
      $this->jsonResponse([
        "success" => false,
        "message" => $e->getMessage()
      ], 500);
    }
  }

  // Update Research dengan validasi permission
  public function update()
  {
    try {
      $id = $_POST["id"] ?? null;

      if (!$id) {
        return $this->jsonResponse([
          "success" => false,
          "message" => "ID research tidak valid."
        ], 400);
      }

      // Validasi permission
      $currentUser = $this->user;
      $canEdit = $this->research->canUserEdit($id, $currentUser['id'], $currentUser['role']);

      if (!$canEdit) {
        return $this->jsonResponse([
          "success" => false,
          "message" => "Maaf, Anda tidak memiliki izin untuk mengedit penelitian ini."
        ], 403);
      }

      // Ambil data existing
      $existingResearch = $this->research->getById($id);
      if (!$existingResearch) {
        return $this->jsonResponse([
          "success" => false,
          "message" => "Research tidak ditemukan."
        ], 404);
      }

      // Parse categories
      $categoryIds = [];
      if (!empty($_POST["categories"])) {
        if (is_string($_POST["categories"])) {
          $categoryIds = json_decode($_POST["categories"], true);
        } else {
          $categoryIds = $_POST["categories"];
        }
        $categoryIds = array_map('intval', $categoryIds);
        $categoryIds = array_filter($categoryIds);
      }

      // Parse participants
      $participantIds = [];
      if (!empty($_POST["participants"])) {
        if (is_string($_POST["participants"])) {
          $participantIds = json_decode($_POST["participants"], true);
        } else {
          $participantIds = $_POST["participants"];
        }
        $participantIds = array_map('intval', $participantIds);
        $participantIds = array_filter($participantIds);
      }

      // Parse data - gunakan existing jika tidak ada input baru
      $start_date = $_POST["start_date"] ?? $existingResearch['start_date'];
      $finish_date = $_POST["finish_date"] ?? $existingResearch['finish_date'];
      $budget = !empty($_POST["budget"]) ? (int)$_POST["budget"] : $existingResearch['budget'];
      $max_participants = !empty($_POST["max_participants"]) ? (int)$_POST["max_participants"] : $existingResearch['max_participants'];

      // Simpan data
      $save = $this->research->saveResearch([
        "id" => $id,
        "created_by_user_id" => $existingResearch['created_by_user_id'],
        "title" => $_POST["title"] ?? $existingResearch['title'],
        "status" => $_POST["status"] ?? $existingResearch['status'],
        "description" => $_POST["description"] ?? $existingResearch['description'],
        "budget" => $budget,
        "max_participants" => $max_participants,
        "start_date" => $start_date,
        "finish_date" => $finish_date,
        "categories" => $categoryIds,
        "participants" => $participantIds
      ]);

      $this->jsonResponse([
        "success" => (bool)$save,
        "message" => $save ? "Research berhasil diperbarui." : "Gagal update research."
      ]);
    } catch (Exception $e) {
      error_log("Update Research Exception: " . $e->getMessage());
      $this->jsonResponse([
        "success" => false,
        "message" => $e->getMessage()
      ], 500);
    }
  }

  // Delete Research dengan validasi permission
  public function delete()
  {
    try {
      $id = $_POST["id"] ?? null;

      if (!$id) {
        return $this->jsonResponse([
          "success" => false,
          "message" => "ID research tidak valid."
        ], 400);
      }

      // Validasi permission
      $currentUser = $this->user;
      $canEdit = $this->research->canUserEdit($id, $currentUser['id'], $currentUser['role']);

      if (!$canEdit) {
        return $this->jsonResponse([
          "success" => false,
          "message" => "Maaf, Anda tidak memiliki izin untuk menghapus penelitian ini."
        ], 403);
      }

      $del = $this->research->delete($id);

      $this->jsonResponse([
        "success" => $del,
        "message" => $del ? "Research berhasil dihapus." : "Gagal menghapus research."
      ]);
    } catch (Exception $e) {
      error_log("Delete Research Exception: " . $e->getMessage());
      $this->jsonResponse([
        "success" => false,
        "message" => "Terjadi kesalahan sistem."
      ], 500);
    }
  }

  // Get Single Research by ID untuk Edit
  public function getDetail()
  {
    $id = $_GET['id'] ?? null;

    if (!$id) {
      return $this->jsonResponse([
        "success" => false,
        "message" => "ID research tidak valid."
      ], 400);
    }

    $research = $this->research->getById($id);

    if (!$research) {
      return $this->jsonResponse([
        "success" => false,
        "message" => "Research tidak ditemukan."
      ], 404);
    }

    $this->jsonResponse([
      "success" => true,
      "data" => $research
    ]);
  }
}
