<?php
require_once '../app/models/CategoryModel.php';
require_once '../app/controllers/BaseController.php';

class CategoryController extends BaseController
{
  private $categoryModel;

  public function __construct()
  {
    parent::__construct();
    parent::requireLogin();
    parent::requireRoles(['kepala', 'dosen', 'mahasiswa']);

    $this->categoryModel = new CategoryModel();
  }

  public function index()
  {
    $page_title = 'Kategori';
    $page_breadcrumb = ['Pages', 'Kategori'];

    include '../app/views/kategori.php';
  }

  // Get All Categories
  public function getList()
  {
    $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
    $limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 5;
    $search = isset($_GET['search']) ? trim($_GET['search']) : '';

    $offset = ($page - 1) * $limit;

    $data = $this->categoryModel->getCategories($limit, $offset, $search);
    $total = $this->categoryModel->countCategories($search);

    $this->jsonResponse([
      'success' => true,
      'data' => $data,
      'total' => $total
    ]);
  }

  // Create New Category
  public function create()
  {
    $name = trim($_POST['name'] ?? '');

    if (empty($name)) {
      return $this->jsonResponse([
        "success" => false,
        "message" => "Nama kategori tidak boleh kosong."
      ], 400);
    }

    if (strlen($name) < 2) {
      return $this->jsonResponse([
        "success" => false,
        "message" => "Nama kategori minimal 2 karakter."
      ], 400);
    }

    if ($this->categoryModel->exists($name)) {
      return $this->jsonResponse([
        "success" => false,
        "message" => "Kategori '$name' sudah ada."
      ], 409);
    }

    $categoryId = $this->categoryModel->create($name);

    if ($categoryId) {
      $this->jsonResponse([
        "success" => true,
        "message" => "Kategori berhasil ditambahkan.",
        "id" => $categoryId
      ]);
    } else {
      $this->jsonResponse([
        "success" => false,
        "message" => "Gagal menambah kategori."
      ], 500);
    }
  }

  // Get All Categoty
  public function getAll()
  {
    $categories = $this->categoryModel->getAll();
    $this->jsonResponse([
      'success' => true,
      'data' => $categories
    ]);
  }

  // Update Category
  public function update()
  {
    $id = $_POST['id'] ?? null;
    $name = trim($_POST['name'] ?? '');

    // Validasi ID
    if (!$id || !is_numeric($id)) {
      return $this->jsonResponse([
        "success" => false,
        "message" => "ID kategori tidak valid."
      ], 400);
    }

    $id = (int)$id;

    // Validasi kategori
    if (!$this->categoryModel->existsById($id)) {
      return $this->jsonResponse([
        "success" => false,
        "message" => "Kategori tidak ditemukan."
      ], 404);
    }

    if (empty($name)) {
      return $this->jsonResponse([
        "success" => false,
        "message" => "Nama kategori tidak boleh kosong."
      ], 400);
    }

    if (strlen($name) < 2) {
      return $this->jsonResponse([
        "success" => false,
        "message" => "Nama kategori minimal 2 karakter."
      ], 400);
    }

    // Validasi nama kategori
    $existingCategory = $this->categoryModel->getById($id);
    if ($existingCategory && $existingCategory['name'] !== $name) {
      if ($this->categoryModel->exists($name)) {
        return $this->jsonResponse([
          "success" => false,
          "message" => "Kategori '$name' sudah ada."
        ], 409);
      }
    }

    $result = $this->categoryModel->update($id, $name);

    $this->jsonResponse([
      "success" => $result,
      "message" => $result ? "Kategori berhasil diperbarui." : "Gagal update kategori."
    ]);
  }

  // Delete Category
  public function delete()
  {
    $id = $_POST['id'] ?? null;

    // Validasi ID
    if (!$id || !is_numeric($id)) {
      return $this->jsonResponse([
        "success" => false,
        "message" => "ID kategori tidak valid."
      ], 400);
    }

    $id = (int)$id;

    // Validasi kategori
    if (!$this->categoryModel->existsById($id)) {
      return $this->jsonResponse([
        "success" => false,
        "message" => "Kategori tidak ditemukan."
      ], 404);
    }

    $result = $this->categoryModel->delete($id);

    $this->jsonResponse([
      "success" => $result,
      "message" => $result ? "Kategori berhasil dihapus." : "Gagal menghapus kategori."
    ]);
  }
}
