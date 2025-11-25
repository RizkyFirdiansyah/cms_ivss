<?php
require_once '../app/models/PublicationsModel.php';
require_once '../app/controllers/BaseController.php';

class PublicationsController extends BaseController
{
  private $publication;

  public function __construct()
  {
    parent::__construct();
    parent::requireLogin();
    parent::requireRole('kepala');

    $this->publication = new PublicationsModel();
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
    $limit  = isset($_GET['limit']) ? (int)$_GET['limit'] : 5;
    $search = isset($_GET['search']) ? trim($_GET['search']) : '';

    $offset = ($page - 1) * $limit;

    $data = $this->publication->getPublications($limit, $offset, $search);
    $total = $this->publication->countPublications($search);

    $response_data = [
      'data' => $data,
      'total' => $total,
    ];

    $this->jsonResponse($response_data);
  }

  // Create New Publication
  public function create()
  {
    $user = $this->user;
    try {
      $link = null;

      $save = $this->publication->savePublication([
        "user_id" => $user["id"],
        "title" => $_POST["title"],
        "link" => $_POST["link"],
        "publication_year" => $_POST["publication_year"]
      ]);

      $this->jsonResponse([
        "success" => $save,
        "message" => $save ? "Publikasi berhasil ditambahkan." : "Gagal menambah publikasi."
      ]);
    } catch (Exception $e) {
      $this->jsonResponse([
        "success" => false,
        "message" => $e->getMessage()
      ]);
    }
  }

  // Update Publication
  public function update()
  {
    try {
      $id = $_POST["id"] ?? null;

      if (!$id) {
        return $this->jsonResponse(["success" => false, "message" => "ID publikasi tidak valid."], 400);
      }

      // 1. Ambil data lama
      $existing = $this->publication->getById($id);

      if (!$existing) {
        return $this->jsonResponse(["success" => false, "message" => "Publikasi tidak ditemukan."], 404);
      }

      $link_old_name = $existing["link"];
      $link_new_name = $link_old_name;

      // 2. Cek apakah ada file baru yang diunggah
      if (!empty($_FILES["link"]["name"])) {
        // A. Upload file baru
        if ($link_new_name === false) {
          return $this->jsonResponse(["success" => false, "message" => "Gagal mengunggah file baru. Format tidak didukung."], 400);
        }

        // B. HAPUS FILE LAMA dari server (jika file upload, bukan URL)
        if ($link_old_name && $link_new_name !== $link_old_name && !filter_var($link_old_name, FILTER_VALIDATE_URL)) {
          $old_file_path = $this->deleteFileFromServer($link_old_name);
          if (file_exists($old_file_path)) {
            unlink($old_file_path);
          }
        }
      } elseif (!empty($_POST["link_url"])) {
        $link_new_name = $_POST["link_url"];

        // Hapus file lama jika sebelumnya adalah file upload
        if ($link_old_name && !filter_var($link_old_name, FILTER_VALIDATE_URL)) {
          $old_file_path = $this->deleteFileFromServer($link_old_name);
          if (file_exists($old_file_path)) {
            unlink($old_file_path);
          }
        }
      }

      // 3. Simpan data ke database
      $save = $this->publication->savePublication([
        "id" => $id,
        "user_id" => $existing["user_id"],
        "title" => $_POST["title"] ?? null,
        "link" => $link_new_name,
        "publication_year" => $_POST["publication_year"] ?? null
      ]);

      $this->jsonResponse([
        "success" => $save,
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
        return $this->jsonResponse(["success" => false, "message" => "ID publikasi tidak valid."], 400);
      }

      // 1. Ambil data publikasi
      $existing = $this->publication->getById($id);

      if (!$existing) {
        return $this->jsonResponse(["success" => false, "message" => "Publikasi tidak ditemukan."], 404);
      }

      $link_name = $existing["link"];

      // 2. Hapus data dari database
      $del = $this->publication->delete($id);

      if ($del) {
        // 3. Hapus file fisik dari server (jika file upload, bukan URL)
        if ($link_name && !filter_var($link_name, FILTER_VALIDATE_URL)) {
          $file_path = $this->deleteFileFromServer($link_name);
          if (file_exists($file_path)) {
            unlink($file_path);
          }
        }
      }

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

  protected function deleteFileFromServer($file_name)
  {
    if (empty($file_name) || strpos($file_name, 'default') !== false) {
      return true;
    }

    $file_path = __DIR__ . '/../../public/uploads/publications/' . $file_name;

    if (file_exists($file_path) && is_file($file_path)) {
      if (unlink($file_path)) {
        error_log("File publikasi berhasil dihapus: " . $file_path);
        return true;
      } else {
        error_log("Gagal menghapus file publikasi (Izin Ditolak): " . $file_path);
        return false;
      }
    }

    return true;
  }
}
