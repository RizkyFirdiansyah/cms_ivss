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

  // Get All Research
  // Get All Research
// Get All Research
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
        
        // Debug log
        error_log("Research List Params: user_id={$userId}, role={$userRole}, search={$search}, category_id={$category_id}, status={$status}");
        
        // Tambahkan parameter user_id dan user_role
        $data = $this->research->getResearch($limit, $offset, $search, $category_id, $status, $userId, $userRole);
        $total = $this->research->countResearch($search, $category_id, $status, $userId, $userRole);
        
        $response_data = [
            'success' => true,
            'data' => $data,
            'total' => $total,
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
  // Get All Categories (dropdown)
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

  // Get All Users (for participants dropdown)
  public function getUsers()
  {
    try {
      $users = $this->userModel->getAllActiveUsers();
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

  // Create New Research
  public function create()
  {
    $currentUser = $this->user;

    try {
      // Handle categories array
      $categoryIds = [];
      if (!empty($_POST["categories"])) {
        if (is_string($_POST["categories"])) {
          $categoryIds = json_decode($_POST["categories"], true);
        } else {
          $categoryIds = $_POST["categories"];
        }

        // Validasi IDs integer
        $categoryIds = array_map('intval', $categoryIds);
        $categoryIds = array_filter($categoryIds);
      }

      // Handle participants array
      $participantIds = [];
      if (!empty($_POST["participants"])) {
        if (is_string($_POST["participants"])) {
          $participantIds = json_decode($_POST["participants"], true);
        } else {
          $participantIds = $_POST["participants"];
        }

        // Validasi IDs integer
        $participantIds = array_map('intval', $participantIds);
        $participantIds = array_filter($participantIds);
      }

      // Parse dates
      $start_date = !empty($_POST["start_date"]) ? $_POST["start_date"] : date('Y-m-d');
      $finish_date = !empty($_POST["finish_date"]) ? $_POST["finish_date"] : null;

      // Parse budget
      $budget = !empty($_POST["budget"]) ? (int)$_POST["budget"] : 0;

      $save = $this->research->saveResearch([
        "created_by_user_id" => $currentUser["id"],
        "title" => $_POST["title"] ?? '',
        "status" => $_POST["status"] ?? 'ongoing',
        "description" => $_POST["description"] ?? '',
        "budget" => $budget,
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
        "message" => "Terjadi kesalahan sistem."
      ], 500);
    }
  }

  // Update Research - PERBAIKAN: Ambil data existing dulu
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

      // Ambil data research yang existing
      $existingResearch = $this->research->getById($id);
      if (!$existingResearch) {
        return $this->jsonResponse([
          "success" => false,
          "message" => "Research tidak ditemukan."
        ], 404);
      }

      // Validasi categories
      $categoryIds = [];
      if (!empty($_POST["categories"])) {
        if (is_string($_POST["categories"])) {
          $categoryIds = json_decode($_POST["categories"], true);
        } else {
          $categoryIds = $_POST["categories"];
        }

        // Pastikan IDs adalah integer
        $categoryIds = array_map('intval', $categoryIds);
        $categoryIds = array_filter($categoryIds);
      }

      // Validasi participants
      $participantIds = [];
      if (!empty($_POST["participants"])) {
        if (is_string($_POST["participants"])) {
          $participantIds = json_decode($_POST["participants"], true);
        } else {
          $participantIds = $_POST["participants"];
        }

        // Pastikan IDs adalah integer
        $participantIds = array_map('intval', $participantIds);
        $participantIds = array_filter($participantIds);
      }

      // Parse dates - gunakan existing value jika tidak ada input baru
      $start_date = $_POST["start_date"] ?? $existingResearch['start_date'];
      $finish_date = $_POST["finish_date"] ?? $existingResearch['finish_date'];

      // Parse budget - gunakan existing value jika tidak ada input baru
      $budget = !empty($_POST["budget"]) ? (int)$_POST["budget"] : $existingResearch['budget'];

      // Simpan data ke database
      $save = $this->research->saveResearch([
        "id" => $id,
        "title" => $_POST["title"] ?? $existingResearch['title'],
        "status" => $_POST["status"] ?? $existingResearch['status'],
        "description" => $_POST["description"] ?? $existingResearch['description'],
        "budget" => $budget,
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
        "message" => "Terjadi kesalahan sistem."
      ], 500);
    }
  }

  // Delete Research
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
