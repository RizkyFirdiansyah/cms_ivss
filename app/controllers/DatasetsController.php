<?php
require_once '../app/models/DatasetsModel.php';
require_once '../app/controllers/BaseController.php';

class DatasetsController extends BaseController
{
  private $dataset;
  public function __construct()
  {
    parent::__construct();
    parent::requireLogin();
    $this->dataset = new DatasetsModel();
  }
  public function index()
  {
    $page_title = 'Datasets';
    $page_breadcrumb = ['Pages', 'Datasets'];

    include '../app/views/datasets.php';
  }

  // Get All Datasets 
  public function getList()
{
  $page   = isset($_GET['page']) ? (int)$_GET['page'] : 1;
  $limit  = isset($_GET['limit']) ? (int)$_GET['limit'] : 6;
  $search = isset($_GET['search']) ? trim($_GET['search']) : '';
  $filter = isset($_GET['filter']) ? trim($_GET['filter']) : '';
  
  $offset = ($page - 1) * $limit;

  // Tentukan user_id berdasarkan filter
  $user_id = null;
  if ($filter === 'my') {
    $user_id = $this->user['id'];
  }

  $current_user_id = ($filter === '') ? $this->user['id'] : null;

  $data = $this->dataset->getDatasets($limit, $offset, $search, $user_id, $current_user_id);
  $total = $this->dataset->countDatasets($search, $user_id);

  $response_data = [
    'success' => true,
    'data' => $data,
    'total' => $total,
    'current_user' => [
      'id' => $this->user['id'],
      'name' => $this->user['name'],
      'role' => $this->user['role']
    ]
  ];

  $this->jsonResponse($response_data);
}

  // Create New Dataset - Semua user
  public function create()
  {
    try {
      // Validasi input
      if (empty($_POST["title"])) {
        return $this->jsonResponse([
          "success" => false,
          "message" => "Judul dataset harus diisi."
        ], 400);
      }

      if (empty($_POST["link"])) {
        return $this->jsonResponse([
          "success" => false,
          "message" => "Link dataset harus diisi."
        ], 400);
      }

      // Validasi URL format
      if (!filter_var($_POST["link"], FILTER_VALIDATE_URL)) {
        return $this->jsonResponse([
          "success" => false,
          "message" => "Format link dataset tidak valid."
        ], 400);
      }

      // Otomatis gunakan user_id dari user yang login
      $user_id = $this->user['id'];

      $save = $this->dataset->saveDataset([
        "user_id" => $user_id,
        "title" => $_POST["title"] ?? '',
        "link" => $_POST["link"] ?? ''
      ]);

      if ($save) {
        $this->jsonResponse([
          "success" => true,
          "message" => "Dataset berhasil ditambahkan."
        ]);
      } else {
        $this->jsonResponse([
          "success" => false,
          "message" => "Gagal menambah dataset."
        ]);
      }
    } catch (Exception $e) {
      error_log("Create Dataset Exception: " . $e->getMessage());
      $this->jsonResponse([
        "success" => false,
        "message" => "Terjadi kesalahan sistem."
      ], 500);
    }
  }

  // Update Dataset - Hanya pemilik yang bisa edit dataset miliknya sendiri
  public function update()
  {
    try {
      $id = $_POST["id"] ?? null;

      if (!$id) {
        return $this->jsonResponse([
          "success" => false,
          "message" => "ID dataset tidak valid."
        ], 400);
      }

      // Validasi input
      if (empty($_POST["title"])) {
        return $this->jsonResponse([
          "success" => false,
          "message" => "Judul dataset harus diisi."
        ], 400);
      }

      if (empty($_POST["link"])) {
        return $this->jsonResponse([
          "success" => false,
          "message" => "Link dataset harus diisi."
        ], 400);
      }

      // Validasi URL format
      if (!filter_var($_POST["link"], FILTER_VALIDATE_URL)) {
        return $this->jsonResponse([
          "success" => false,
          "message" => "Format link dataset tidak valid."
        ], 400);
      }

      // Dapatkan data existing untuk referensi
      $existing = $this->dataset->getById($id);
      if (!$existing) {
        return $this->jsonResponse([
          "success" => false,
          "message" => "Dataset tidak ditemukan."
        ], 404);
      }

      // VALIDASI OWNERSHIP: 
      // - Hanya pemilik (user_id match) atau kepala lab yang bisa edit
      // - Admin TIDAK bisa edit kecuali mereka adalah pemiliknya
      $isOwner = $existing['user_id'] == $this->user['id'];
      $isKepalaLab = $this->user['role'] === 'kepala';
      
      if (!$isOwner && !$isKepalaLab) {
        return $this->jsonResponse([
          "success" => false,
          "message" => "Anda tidak memiliki akses untuk mengedit dataset ini. Hanya pemilik dan kepala lab yang dapat mengedit."
        ], 403);
      }

      // Simpan data ke database
      $save = $this->dataset->saveDataset([
        "id" => $id,
        "user_id" => $existing["user_id"], 
        "title" => $_POST["title"] ?? '',
        "link" => $_POST["link"] ?? ''
      ]);

      $this->jsonResponse([
        "success" => $save,
        "message" => $save ? "Dataset berhasil diperbarui." : "Gagal update dataset."
      ]);
    } catch (Exception $e) {
      error_log("Update Dataset Exception: " . $e->getMessage());
      $this->jsonResponse([
        "success" => false,
        "message" => "Terjadi kesalahan sistem."
      ], 500);
    }
  }

  // Delete Dataset - Hanya pemilik yang bisa hapus datasetnya sendiri
  public function delete()
  {
    try {
      $id = $_POST["id"] ?? null;

      if (!$id) {
        return $this->jsonResponse([
          "success" => false,
          "message" => "ID dataset tidak valid."
        ], 400);
      }

      // Validasi ownership
      $existing = $this->dataset->getById($id);
      if (!$existing) {
        return $this->jsonResponse([
          "success" => false,
          "message" => "Dataset tidak ditemukan."
        ], 404);
      }

      // VALIDASI OWNERSHIP:
      // - Hanya pemilik (user_id match) atau kepala lab yang bisa hapus
      // - Admin TIDAK bisa hapus kecuali mereka adalah pemiliknya
      $isOwner = $existing['user_id'] == $this->user['id'];
      $isKepalaLab = $this->user['role'] === 'kepala';
      
      if (!$isOwner && !$isKepalaLab) {
        return $this->jsonResponse([
          "success" => false,
          "message" => "Anda tidak memiliki akses untuk menghapus dataset ini. Hanya pemilik dan kepala lab yang dapat menghapus."
        ], 403);
      }

      $del = $this->dataset->delete($id);

      $this->jsonResponse([
        "success" => $del,
        "message" => $del ? "Dataset berhasil dihapus." : "Gagal menghapus dataset."
      ]);
    } catch (Exception $e) {
      error_log("Delete Dataset Exception: " . $e->getMessage());
      $this->jsonResponse([
        "success" => false,
        "message" => "Terjadi kesalahan sistem."
      ], 500);
    }
  }
}
?>