<?php
require_once '../app/models/BootcampModel.php';
require_once '../app/models/CategoryModel.php';
require_once '../app/controllers/BaseController.php';

class BootcampController extends BaseController
{
  private $bootcamp;
  private $category;

  public function __construct()
  {
    parent::__construct();
    parent::requireLogin();
    parent::requireRole('kepala');

    $this->bootcamp = new BootcampModel();
    $this->category = new CategoryModel();
  }

  public function index()
  {
    $page_title = 'Bootcamp';
    $page_breadcrumb = ['Pages', 'Bootcamp'];

    include '../app/views/bootcamp.php';
  }

  // Get All Bootcamp dengan filter berdasarkan role user
  public function getList()
  {
    $page   = isset($_GET['page']) ? (int)$_GET['page'] : 1;
    $limit  = isset($_GET['limit']) ? (int)$_GET['limit'] : 6;
    $search = isset($_GET['search']) ? trim($_GET['search']) : '';
    $category_id = isset($_GET['category_id']) ? (int)$_GET['category_id'] : null;
    
    // Tentukan user_id berdasarkan role
    $user_id = null;
    if (!$this->isAdmin()) {
      $user_id = $this->user['id']; // Filter hanya data user ini
    }

    $offset = ($page - 1) * $limit;

    $data = $this->bootcamp->getBootcamps($limit, $offset, $search, $category_id, $user_id);
    $total = $this->bootcamp->countBootcamps($search, $category_id, $user_id);

    $response_data = [
      'success' => true,
      'data' => $data,
      'total' => $total,
      'is_admin' => $this->isAdmin()
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

  // Create New Bootcamp
  public function create()
  {
    $user = $this->user;
    $photo = null;

    if (!empty($_FILES["photo"]["name"])) {
      $photo = $this->handleFileUpload($_FILES["photo"]);
      if ($photo === false) {
        return $this->jsonResponse([
          "success" => false,
          "message" => "Gagal mengunggah foto. Format tidak didukung."
        ], 400);
      }
    }

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

    $save = $this->bootcamp->saveBootcamp([
      "created_by_user_id" => $user["id"],
      "name" => $_POST["name"] ?? '',
      "description" => $_POST["description"] ?? '',
      "photo" => $photo,
      "link" => $_POST["link"] ?? '',
      "categories" => $categoryIds
    ]);

    $this->jsonResponse([
      "success" => (bool)$save,
      "message" => $save ? "Bootcamp berhasil ditambahkan." : "Gagal menambah bootcamp.",
      "id" => $save
    ]);
  }

  // Update Bootcamp dengan validasi kepemilikan
  public function update()
  {
    try {
      $id = $_POST["id"] ?? null;

      if (!$id) {
        return $this->jsonResponse([
          "success" => false,
          "message" => "ID bootcamp tidak valid."
        ], 400);
      }

      // Ambil data bootcamp 
      $existing = $this->bootcamp->getById($id);

      if (!$existing) {
        return $this->jsonResponse([
          "success" => false,
          "message" => "Bootcamp tidak ditemukan."
        ], 404);
      }

      // Validasi kepemilikan untuk non-admin
      if (!$this->isAdmin() && $existing['created_by_user_id'] != $this->user['id']) {
        return $this->jsonResponse([
          "success" => false,
          "message" => "Anda tidak memiliki izin untuk mengedit bootcamp ini."
        ], 403);
      }

      $photo_old_name = $existing["photo"];
      $photo_new_name = $photo_old_name;

      // Cek apakah ada file baru yang diunggah
      if (!empty($_FILES["photo"]["name"])) {
        $photo_new_name = $this->handleFileUpload($_FILES["photo"]);

        if ($photo_new_name === false) {
          return $this->jsonResponse([
            "success" => false,
            "message" => "Gagal mengunggah foto baru. Format tidak didukung."
          ], 400);
        }

        // Hapus foto lama jika ada
        if ($photo_old_name && $photo_new_name !== $photo_old_name) {
          $this->deleteFileFromServer($photo_old_name);
        }
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

      // Simpan data ke database
      $save = $this->bootcamp->saveBootcamp([
        "id" => $id,
        "name" => $_POST["name"] ?? '',
        "description" => $_POST["description"] ?? '',
        "photo" => $photo_new_name,
        "link" => $_POST["link"] ?? '',
        "categories" => $categoryIds
      ]);

      $this->jsonResponse([
        "success" => (bool)$save,
        "message" => $save ? "Bootcamp berhasil diperbarui." : "Gagal update bootcamp."
      ]);
    } catch (Exception $e) {
      error_log("Update Bootcamp Exception: " . $e->getMessage());
      $this->jsonResponse([
        "success" => false,
        "message" => "Terjadi kesalahan sistem."
      ], 500);
    }
  }

  // Delete Bootcamp dengan validasi kepemilikan
  public function delete()
  {
    try {
      $id = $_POST["id"] ?? null;

      if (!$id) {
        return $this->jsonResponse([
          "success" => false,
          "message" => "ID bootcamp tidak valid."
        ], 400);
      }

      // Ambil data bootcamp 
      $existing = $this->bootcamp->getById($id);

      if (!$existing) {
        return $this->jsonResponse([
          "success" => false,
          "message" => "Bootcamp tidak ditemukan."
        ], 404);
      }

      // Validasi kepemilikan untuk non-admin
      if (!$this->isAdmin() && $existing['created_by_user_id'] != $this->user['id']) {
        return $this->jsonResponse([
          "success" => false,
          "message" => "Anda tidak memiliki izin untuk menghapus bootcamp ini."
        ], 403);
      }

      $photo_name = $existing["photo"];

      // Hapus data dari database
      $del = $this->bootcamp->delete($id);

      if ($del) {
        // Hapus foto jika ada
        if ($photo_name) {
          $this->deleteFileFromServer($photo_name);
        }
      }

      $this->jsonResponse([
        "success" => $del,
        "message" => $del ? "Bootcamp berhasil dihapus." : "Gagal menghapus bootcamp."
      ]);
    } catch (Exception $e) {
      error_log("Delete Bootcamp Exception: " . $e->getMessage());
      $this->jsonResponse([
        "success" => false,
        "message" => "Terjadi kesalahan sistem."
      ], 500);
    }
  }

  // Get Single Bootcamp by ID dengan validasi akses
  public function getDetail()
  {
    $id = $_GET['id'] ?? null;

    if (!$id) {
      return $this->jsonResponse([
        "success" => false,
        "message" => "ID bootcamp tidak valid."
      ], 400);
    }

    $bootcamp = $this->bootcamp->getById($id);

    if (!$bootcamp) {
      return $this->jsonResponse([
        "success" => false,
        "message" => "Bootcamp tidak ditemukan."
      ], 404);
    }

    // Validasi akses untuk non-admin
    if (!$this->isAdmin() && $bootcamp['created_by_user_id'] != $this->user['id']) {
      return $this->jsonResponse([
        "success" => false,
        "message" => "Anda tidak memiliki akses ke bootcamp ini."
      ], 403);
    }

    $this->jsonResponse([
      "success" => true,
      "data" => $bootcamp
    ]);
  }

  // Helper method untuk mengecek apakah user adalah admin (kepala lab)
  private function isAdmin()
  {
    return isset($this->user['role']) && $this->user['role'] === 'kepala';
  }

  // Handle File Upload
  private function handleFileUpload($file)
  {
    $upload_dir = 'uploads/bootcamp/';
    $upload_path_full = __DIR__ . '/../../public/' . $upload_dir;

    // Pastikan folder ada
    if (!is_dir($upload_path_full)) {
      mkdir($upload_path_full, 0777, true);
    }

    $allowed_types = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));

    if (!in_array($ext, $allowed_types)) {
      return false;
    }

    $new_file_name = $this->user['id'] . '_' . time() . '.' . $ext;
    $target_path = $upload_path_full . $new_file_name;

    if (move_uploaded_file($file['tmp_name'], $target_path)) {
      return $new_file_name;
    }

    return false;
  }

  // Delete File From Server
  protected function deleteFileFromServer($file_name)
  {
    if (empty($file_name) || strpos($file_name, 'default') !== false) {
      return true;
    }

    $file_path = __DIR__ . '/../../public/uploads/bootcamp/' . $file_name;

    if (file_exists($file_path) && is_file($file_path)) {
      if (unlink($file_path)) {
        error_log("File bootcamp berhasil dihapus: " . $file_path);
        return true;
      } else {
        error_log("Gagal menghapus file bootcamp: " . $file_path);
        return false;
      }
    }

    return true;
  }
}