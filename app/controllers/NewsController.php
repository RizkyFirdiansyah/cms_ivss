<?php
require_once '../app/models/NewsModel.php';
require_once '../app/models/CategoryModel.php';
require_once '../app/controllers/BaseController.php';

class NewsController extends BaseController
{
  private $news;
  private $category;

  public function __construct()
  {
    parent::__construct();
    parent::requireLogin();
    parent::requireRole('kepala');

    $this->news = new NewsModel();
    $this->category = new CategoryModel();
  }

  public function index()
  {
    $page_title = 'Berita';
    $page_breadcrumb = ['Pages', 'Berita'];

    include '../app/views/news.php';
  }

  // Get All News
  public function getList()
  {
    $page   = isset($_GET['page']) ? (int)$_GET['page'] : 1;
    $limit  = isset($_GET['limit']) ? (int)$_GET['limit'] : 6;
    $search = isset($_GET['search']) ? trim($_GET['search']) : '';
    $category_id = isset($_GET['category_id']) ? (int)$_GET['category_id'] : null;

    $offset = ($page - 1) * $limit;

    // **PERBAIKAN: Tambah parameter category_id**
    $data = $this->news->getNews($limit, $offset, $search, $category_id);
    $total = $this->news->countNews($search, $category_id);

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
    $this->jsonResponse(['data' => $categories]);
  }

  // Create New News 
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

    $save = $this->news->saveNews([
      "user_id" => $user["id"],
      "title" => $_POST["title"] ?? '',
      "content" => $_POST["content"] ?? '',
      "photo" => $photo,
      "categories" => $categoryIds
    ]);

    $this->news->refreshMaterializedViews();

    $this->jsonResponse([
      "success" => (bool)$save,
      "message" => $save ? "Berita berhasil ditambahkan." : "Gagal menambah berita.",
      "id" => $save
    ]);
  }

  // Update News
  public function update()
  {
    $id = $_POST["id"] ?? null;

    if (!$id) {
      return $this->jsonResponse(["success" => false, "message" => "ID berita tidak valid."], 400);
    }

    // Ambil data berita 
    $existing = $this->news->getById($id);

    if (!$existing) {
      return $this->jsonResponse(["success" => false, "message" => "Berita tidak ditemukan."], 404);
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

    error_log("Received Category IDs: " . print_r($categoryIds, true));

    // Simpan data ke database
    $save = $this->news->saveNews([
      "id" => $id,
      "title" => $_POST["title"] ?? '',
      "content" => $_POST["content"] ?? '',
      "photo" => $photo_new_name,
      "categories" => $categoryIds
    ]);

    $this->news->refreshMaterializedViews();

    $this->jsonResponse([
      "success" => (bool)$save,
      "message" => $save ? "Berita berhasil diperbarui." : "Gagal update berita."
    ]);
  }

  // Delete News
  public function delete()
  {
    $id = $_POST["id"] ?? null;

    if (!$id) {
      return $this->jsonResponse(["success" => false, "message" => "ID berita tidak valid."], 400);
    }

    // Ambil data berita 
    $existing = $this->news->getById($id);

    if (!$existing) {
      return $this->jsonResponse(["success" => false, "message" => "Berita tidak ditemukan."], 404);
    }

    $photo_name = $existing["photo"];

    // Hapus data dari database
    $del = $this->news->delete($id);

    if ($del) {
      // Hapus foto jika ada
      if ($photo_name) {
        $this->deleteFileFromServer($photo_name);
      }
    }

    $this->news->refreshMaterializedViews();

    $this->jsonResponse([
      "success" => $del,
      "message" => $del ? "Berita berhasil dihapus." : "Gagal menghapus berita."
    ]);
  }

  // Get Single News by ID 
  public function getDetail()
  {
    $id = $_GET['id'] ?? null;

    if (!$id) {
      return $this->jsonResponse(["success" => false, "message" => "ID berita tidak valid."], 400);
    }

    $news = $this->news->getById($id);

    if (!$news) {
      return $this->jsonResponse(["success" => false, "message" => "Berita tidak ditemukan."], 404);
    }

    $this->jsonResponse([
      "success" => true,
      "data" => $news
    ]);
  }

  // Helper Function
  // Handle File UploadS
  private function handleFileUpload($file)
  {
    $upload_dir = 'uploads/news/';

    // FIX PATH
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
      // Jangan hapus jika kosong atau file default
      return true;
    }

    $file_path = __DIR__ . '/../../public/uploads/news/' . $file_name;

    // Cek apakah file ada dan bukan direktori
    if (file_exists($file_path) && is_file($file_path)) {
      if (unlink($file_path)) {
        error_log("File berita berhasil dihapus: " . $file_path);
        return true;
      } else {
        // Gagal hapus karena masalah izin
        error_log("Gagal menghapus file berita (Izin Ditolak): " . $file_path);
        return false;
      }
    }

    return true;
  }
}
