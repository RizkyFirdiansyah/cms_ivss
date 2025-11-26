<?php
require_once '../app/models/GalleryModel.php';
require_once '../app/controllers/BaseController.php';

class GalleryController extends BaseController
{
  private $gallery;

  public function __construct()
  {
    parent::__construct();
    parent::requireLogin();
    parent::requireRole('kepala');

    $this->gallery = new GalleryModel();
  }

  public function index()
  {
    $page_title = 'Galeri';
    $page_breadcrumb = ['Pages', 'Galeri'];

    include '../app/views/galeri.php';
  }

  // Get All Gallery Items
  public function list()
  {
    $page   = isset($_GET['page']) ? (int)$_GET['page'] : 1;
    $limit  = isset($_GET['limit']) ? (int)$_GET['limit'] : 5;
    $search = isset($_GET['search']) ? trim($_GET['search']) : '';

    $offset = ($page - 1) * $limit;

    $data = $this->gallery->getGallery($limit, $offset, $search);
    $total = $this->gallery->countGallery($search);

    $response_data = [
      'data' => $data,
      'total' => $total,
    ];

    $this->jsonResponse($response_data);
  }

  // Create New Gallery Item
  public function create()
  {
    $user = $this->user;
    try {
      $link = null;

      if (!empty($_FILES["link"]["name"])) {
        $link = $this->handleFileUpload($_FILES["link"]);
      }

      $save = $this->gallery->saveGallery([
        "user_id" => $user["id"],
        "title" => $_POST["title"],
        "link" => $link
      ]);

      $this->jsonResponse([
        "success" => $save,
        "message" => $save ? "Galeri berhasil ditambahkan." : "Gagal menambah galeri."
      ]);
    } catch (Exception $e) {
      $this->jsonResponse([
        "success" => false,
        "message" => $e->getMessage()
      ]);
    }
  }

  // Update Gallery Item
  public function update()
  {
    try {
      $id = $_POST["id"] ?? null;

      if (!$id) {
        return $this->jsonResponse(["success" => false, "message" => "ID galeri tidak valid."], 400);
      }

      // 1. Ambil data lama (untuk mendapatkan nama file lama)
      $existing = $this->gallery->getById($id);

      if (!$existing) {
        return $this->jsonResponse(["success" => false, "message" => "Galeri tidak ditemukan."], 404);
      }

      $link_old_name = $existing["link"];
      $link_new_name = $link_old_name;

      // 2. Cek apakah ada file baru yang diunggah
      if (!empty($_FILES["link"]["name"])) {
        // A. Upload file baru
        $link_new_name = $this->handleFileUpload($_FILES["link"]);

        if ($link_new_name === false) {
          return $this->jsonResponse(["success" => false, "message" => "Gagal mengunggah file baru. Format tidak didukung."], 400);
        }

        // B. HAPUS FILE LAMA dari server
        if ($link_old_name && $link_new_name !== $link_old_name) {
          $old_file_path = $this->deleteFileFromServer($link_old_name);
          if (file_exists($old_file_path)) {
            unlink($old_file_path);
          }
        }
      }

      // 3. Simpan data ke database
      $save = $this->gallery->saveGallery([
        "id" => $id,
        "user_id" => $existing["user_id"],
        "title" => $_POST["title"] ?? null,
        "link" => $link_new_name
      ]);

      $this->jsonResponse([
        "success" => $save,
        "message" => $save ? "Galeri berhasil diperbarui." : "Gagal update galeri."
      ]);
    } catch (Exception $e) {
      error_log("Update Galeri Exception: " . $e->getMessage());
      $this->jsonResponse([
        "success" => false,
        "message" => "Terjadi kesalahan sistem."
      ], 500);
    }
  }

  // Delete Gallery Item
  public function delete()
  {
    try {
      $id = $_POST["id"] ?? null;

      if (!$id) {
        return $this->jsonResponse(["success" => false, "message" => "ID galeri tidak valid."], 400);
      }

      // 1. Ambil data galeri (untuk mendapatkan nama file)
      $existing = $this->gallery->getById($id);

      if (!$existing) {
        return $this->jsonResponse(["success" => false, "message" => "Galeri tidak ditemukan."], 404);
      }

      $link_name = $existing["link"];

      // 2. Hapus data dari database
      $del = $this->gallery->delete($id);

      if ($del) {
        // 3. Hapus file fisik dari server
        if ($link_name) {
          $file_path = $this->deleteFileFromServer($link_name);
          if (file_exists($file_path)) {
            unlink($file_path);
          }
        }
      }

      $this->jsonResponse([
        "success" => $del,
        "message" => $del ? "Galeri berhasil dihapus." : "Gagal menghapus galeri."
      ]);
    } catch (Exception $e) {
      error_log("Delete Galeri Exception: " . $e->getMessage());
      $this->jsonResponse([
        "success" => false,
        "message" => "Terjadi kesalahan sistem."
      ], 500);
    }
  }

  // Helper Function - Handle File Upload
  private function handleFileUpload($file)
  {
    $upload_dir = 'uploads/gallery/';

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

  protected function deleteFileFromServer($file_name)
  {
    if (empty($file_name) || strpos($file_name, 'default') !== false) {
      // Jangan hapus jika kosong atau file default
      return true;
    }

    $file_path = __DIR__ . '/../../public/uploads/gallery/' . $file_name;

    // Cek apakah file ada dan bukan direktori
    if (file_exists($file_path) && is_file($file_path)) {
      if (unlink($file_path)) {
        error_log("File galeri berhasil dihapus: " . $file_path);
        return true;
      } else {
        // Gagal hapus karena masalah izin
        error_log("Gagal menghapus file galeri (Izin Ditolak): " . $file_path);
        return false;
      }
    }

    // File tidak ada di server atau DB menyimpan NULL/kosong, anggap berhasil
    return true;
  }
}
