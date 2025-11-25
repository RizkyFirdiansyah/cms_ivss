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

    $this->publication = new PublicationsModel();
    $this->category = new CategoryModel();
  }

  public function index()
  {
    $page_title = 'Publikasi';
    $page_breadcrumb = ['Pages', 'Publikasi'];

    include '../app/views/publications.php';
  }

  // Get All Publications
  public function getList()
  {
    $page   = isset($_GET['page']) ? (int)$_GET['page'] : 1;
    $limit  = isset($_GET['limit']) ? (int)$_GET['limit'] : 6;
    $search = isset($_GET['search']) ? trim($_GET['search']) : '';
    $category_id = isset($_GET['category_id']) ? (int)$_GET['category_id'] : null;

    $offset = ($page - 1) * $limit;

    $data = $this->publication->getPublications($limit, $offset, $search, $category_id);
    $total = $this->publication->countPublications($search, $category_id);

    $response_data = [
      'success' => true,
      'data' => $data,
      'total' => $total,
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
        $categoryIds = is_array($_POST["categories"]) ? $_POST["categories"] : [];

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

  // Update Publication
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

      // Validasi categories
      $categoryIds = [];
      if (!empty($_POST["categories"])) {
        $categoryIds = is_array($_POST["categories"]) ? $_POST["categories"] : [];

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

  // Delete Publication
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

      $del = $this->publication->delete($id);

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

  // Get Single Publication by ID
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

    $this->jsonResponse([
      "success" => true,
      "data" => $publication
    ]);
  }
}
