<?php
require_once '../app/models/PublicationsModel.php';
require_once '../app/models/CategoryModel.php';
require_once '../app/controllers/BaseController.php';

class PublicationsController extends BaseController
{
  private $publication;
  private $category;

  public function __construct()
  {
    parent::__construct();
    parent::requireLogin();
    // Ubah pengecekan role
    $allowedRoles = ['kepala', 'dosen', 'mahasiswa'];
    if (!in_array($this->user['role'], $allowedRoles)) {
      header('HTTP/1.0 403 Forbidden');
      echo "Access denied";
      exit;
    }

    $this->publication = new PublicationsModel();
    $this->category = new CategoryModel();
  }

  public function index()
  {
    $page_title = 'Publikasi';
    $page_breadcrumb = ['Pages', 'Publikasi'];

    include '../app/views/publications.php';
  }

  // Get All Publications dengan filter berdasarkan role user
  public function getList()
  {
    $page   = isset($_GET['page']) ? (int)$_GET['page'] : 1;
    $limit  = isset($_GET['limit']) ? (int)$_GET['limit'] : 6;
    $search = isset($_GET['search']) ? trim($_GET['search']) : '';
    $category_id = isset($_GET['category_id']) ? (int)$_GET['category_id'] : null;
    
    // Debug
    error_log("=== DEBUG PUBLIKASI ===");
    error_log("User ID: " . ($this->user['id'] ?? 'NULL'));
    error_log("User Role: " . ($this->user['role'] ?? 'NULL'));
    error_log("Is Admin: " . ($this->isAdmin() ? 'YES' : 'NO'));
    
    // Tentukan user_id berdasarkan role
    $user_id = null;
    if (!$this->isAdmin()) {
      $user_id = $this->user['id']; // Filter hanya data user ini
    }
    
    error_log("Filter User ID: " . ($user_id ?? 'NULL (show all for admin)'));

    $offset = ($page - 1) * $limit;

    $data = $this->publication->getPublications($limit, $offset, $search, $category_id, $user_id);
    $total = $this->publication->countPublications($search, $category_id, $user_id);

    $response_data = [
      'success' => true,
      'data' => $data,
      'total' => $total,
      'is_admin' => $this->isAdmin(),
      'debug_info' => [
        'user_id' => $this->user['id'] ?? null,
        'user_role' => $this->user['role'] ?? null,
        'filter_applied' => $user_id !== null ? 'user_only' : 'all_users'
      ]
    ];

    $this->jsonResponse($response_data);
  }

  // Get All Categories (dropdown)
  public function getCategories()
  {
    $categories = $this->category->getAll();
    $this->jsonResponse([
      'success' => true,
      'data' => $categories
    ]);
  }

  // Create New Publication
  public function create()
  {
    $user = $this->user;

    try {
      // Handle categories array 
      $categoryIds = [];
      if (!empty($_POST["categories"])) {
        // Parse JSON string jika categories dikirim sebagai string
        if (is_string($_POST["categories"])) {
          $categoryIds = json_decode($_POST["categories"], true) ?: [];
        } else {
          $categoryIds = $_POST["categories"];
        }

        // Validasi IDs integer
        $categoryIds = array_map('intval', $categoryIds);
        $categoryIds = array_filter($categoryIds);
      }

      $save = $this->publication->savePublication([
        "user_id" => $user["id"],
        "title" => $_POST["title"] ?? '',
        "link" => $_POST["link"] ?? '',
        "publication_year" => $_POST["publication_year"] ?? date('Y'),
        "categories" => $categoryIds  // Array of category IDs
      ]);

      if ($save) {
        $this->jsonResponse([
          "success" => true,
          "message" => "Publikasi berhasil ditambahkan.",
          "id" => $save
        ]);
        $this->publication->refreshMaterializedView();
      } else {
        $this->jsonResponse([
          "success" => false,
          "message" => "Gagal menambah publikasi."
        ]);
      }
    } catch (Exception $e) {
      error_log("Create Publication Exception: " . $e->getMessage());
      $this->jsonResponse([
        "success" => false,
        "message" => "Terjadi kesalahan sistem."
      ], 500);
    }
  }

  // Update Publication dengan validasi kepemilikan
  public function update()
  {
    try {
      $id = $_POST["id"] ?? null;

      if (!$id) {
        return $this->jsonResponse([
          "success" => false,
          "message" => "ID publikasi tidak valid."
        ], 400);
      }

      // Validasi kepemilikan untuk non-admin
      if (!$this->isAdmin()) {
        $publication = $this->publication->getById($id);
        if (!$publication || $publication['user_id'] != $this->user['id']) {
          return $this->jsonResponse([
            "success" => false,
            "message" => "Anda tidak memiliki izin untuk mengedit publikasi ini."
          ], 403);
        }
      }

      // Validasi categories
      $categoryIds = [];
      if (!empty($_POST["categories"])) {
        // Parse JSON string jika categories dikirim sebagai string
        if (is_string($_POST["categories"])) {
          $categoryIds = json_decode($_POST["categories"], true) ?: [];
        } else {
          $categoryIds = $_POST["categories"];
        }

        // Pastikan IDs adalah integer
        $categoryIds = array_map('intval', $categoryIds);
        $categoryIds = array_filter($categoryIds);
      }

      // Simpan data ke database
      $save = $this->publication->savePublication([
        "id" => $id,
        "user_id" => $this->user["id"],
        "title" => $_POST["title"] ?? '',
        "link" => $_POST["link"] ?? '',
        "publication_year" => $_POST["publication_year"] ?? date('Y'),
        "categories" => $categoryIds
      ]);

      $this->publication->refreshMaterializedView();

      $this->jsonResponse([
        "success" => (bool)$save,
        "message" => $save ? "Publikasi berhasil diperbarui." : "Gagal update publikasi."
      ]);
    } catch (Exception $e) {
      error_log("Update Publication Exception: " . $e->getMessage());
      $this->jsonResponse([
        "success" => false,
        "message" => "Terjadi kesalahan sistem."
      ], 500);
    }
  }

  // Delete Publication dengan validasi kepemilikan
  public function delete()
  {
    try {
      $id = $_POST["id"] ?? null;

      if (!$id) {
        return $this->jsonResponse([
          "success" => false,
          "message" => "ID publikasi tidak valid."
        ], 400);
      }

      // Validasi kepemilikan untuk non-admin
      if (!$this->isAdmin()) {
        $publication = $this->publication->getById($id);
        if (!$publication || $publication['user_id'] != $this->user['id']) {
          return $this->jsonResponse([
            "success" => false,
            "message" => "Anda tidak memiliki izin untuk menghapus publikasi ini."
          ], 403);
        }
      }

      $del = $this->publication->delete($id);
      $this->publication->refreshMaterializedView();

      $this->jsonResponse([
        "success" => $del,
        "message" => $del ? "Publikasi berhasil dihapus." : "Gagal menghapus publikasi."
      ]);
    } catch (Exception $e) {
      error_log("Delete Publication Exception: " . $e->getMessage());
      $this->jsonResponse([
        "success" => false,
        "message" => "Terjadi kesalahan sistem."
      ], 500);
    }
  }

  // Get Single Publication by ID dengan validasi akses
  public function getDetail()
  {
    $id = $_GET['id'] ?? null;

    if (!$id) {
      return $this->jsonResponse([
        "success" => false,
        "message" => "ID publikasi tidak valid."
      ], 400);
    }

    $publication = $this->publication->getById($id);

    if (!$publication) {
      return $this->jsonResponse([
        "success" => false,
        "message" => "Publikasi tidak ditemukan."
      ], 404);
    }

    // Validasi akses untuk non-admin
    if (!$this->isAdmin() && $publication['user_id'] != $this->user['id']) {
      return $this->jsonResponse([
        "success" => false,
        "message" => "Anda tidak memiliki akses ke publikasi ini."
      ], 403);
    }

    $this->jsonResponse([
      "success" => true,
      "data" => $publication
    ]);
  }

  // Helper method untuk mengecek apakah user adalah admin (kepala lab)
  private function isAdmin()
  {
    // Karena database menggunakan single role string, bukan array
    return isset($this->user['role']) && $this->user['role'] === 'kepala';
  }
}