<?php
require_once '../app/models/FacilitiesModel.php';
require_once '../app/controllers/BaseController.php';

class FacilitiesController extends BaseController
{

  private $facility;

  public function __construct()
  {
    parent::__construct();
    parent::requireLogin();
    parent::requireRole('kepala');

    $this->facility = new FacilitiesModel();
  }
  public function index()
  {
    $page_title = 'Fasilitas';
    $page_breadcrumb = ['Pages', 'Fasilitas'];

    include '../app/views/facility.php';
  }

  // Get All Facilities
  public function getList()
  {
    $page   = isset($_GET['page']) ? (int)$_GET['page'] : 1;
    $limit  = isset($_GET['limit']) ? (int)$_GET['limit'] : 5;
    $search = isset($_GET['search']) ? trim($_GET['search']) : '';

    $offset = ($page - 1) * $limit;

    $data = $this->facility->getFacility($limit, $offset, $search);
    $total = $this->facility->countFacility($search);

    $response_data = [
      'data' => $data,
      'total' => $total,
    ];

    $this->jsonResponse($response_data);
  }

  // Create New Facility
  public function create()
  {
    $user = $this->user;
    try {
      $photo = null;

      if (!empty($_FILES["photo"]["name"])) {
        $photo = $this->handleFileUpload($_FILES["photo"]);
      }

      $save = $this->facility->saveFacility([
        "user_id" => $user["id"],
        "name" => $_POST["name"],
        "description" => $_POST["description"],
        "photo" => $photo
      ]);

      $this->jsonResponse([
        "success" => $save,
        "message" => $save ? "Fasilitas berhasil ditambahkan." : "Gagal menambah fasilitas."
      ]);
    } catch (Exception $e) {
      $this->jsonResponse([
        "success" => false,
        "message" => $e->getMessage()
      ]);
    }
  }

  // Update Facility
  public function update()
  {
    $id = $_POST["id"] ?? null;

    if (!$id) {
      return $this->jsonResponse(["success" => false, "message" => "ID fasilitas tidak valid."], 400);
    }

    // Ambil data fasilitas 
    $existing = $this->facility->getById($id);

    if (!$existing) {
      return $this->jsonResponse(["success" => false, "message" => "Fasilitas tidak ditemukan."], 404);
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
        $old_file_path = $this->deleteFileFromServer($photo_old_name);
        if (file_exists($old_file_path)) {
          unlink($old_file_path);
        }
      }
    }

    // Simpan data ke database
    $save = $this->facility->saveFacility([
      "id" => $id,
      "name" => $_POST["name"] ?? null,
      "description" => $_POST["description"] ?? null,
      "photo" => $photo_new_name
    ]);

    $this->jsonResponse([
      "success" => $save,
      "message" => $save ? "Fasilitas berhasil diperbarui." : "Gagal update fasilitas."
    ]);
  }

  // Delete Facility
  public function delete()
  {
    $id = $_POST["id"] ?? null;

    if (!$id) {
      return $this->jsonResponse(["success" => false, "message" => "ID fasilitas tidak valid."], 400);
    }

    // Ambil data fasilitas
    $existing = $this->facility->getById($id);

    if (!$existing) {
      return $this->jsonResponse(["success" => false, "message" => "Fasilitas tidak ditemukan."], 404);
    }

    $photo_name = $existing["photo"];

    // Hapus data dari database
    $del = $this->facility->delete($id);

    if ($del) {
      // Hapus foto jika ada
      if ($photo_name) {
        $this->deleteFileFromServer($photo_name);
      }
    }

    $this->jsonResponse([
      "success" => $del,
      "message" => $del ? "Fasilitas berhasil dihapus." : "Gagal menghapus fasilitas."
    ]);
  }

  // Get Single Facility by ID 
  public function getDetail()
  {
    $id = $_GET['id'] ?? null;

    if (!$id) {
      return $this->jsonResponse(["success" => false, "message" => "ID facility tidak valid."], 400);
    }

    $facility = $this->facility->getById($id);

    if (!$facility) {
      return $this->jsonResponse(["success" => false, "message" => "Facility tidak ditemukan."], 404);
    }

    $this->jsonResponse([
      "success" => true,
      "data" => $facility
    ]);
  }

  // Helper Functoin
  // Handle File Upload
  private function handleFileUpload($file)
  {
    $upload_dir = 'uploads/facility/';

    // FIX PATH
    $upload_path_full = __DIR__ . '/../../public/' . $upload_dir;

    // Pastikan folder ada
    if (!is_dir($upload_path_full)) {
      mkdir($upload_path_full, 0777, true);
    }

    $allowed_types = ['jpg', 'jpeg', 'png', 'gif'];
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

    $file_path = __DIR__ . '/../../public/uploads/facility/' . $file_name;

    // Cek apakah file ada dan bukan direktori
    if (file_exists($file_path) && is_file($file_path)) {
      if (unlink($file_path)) {
        error_log("File lama berhasil dihapus: " . $file_path);
        return true;
      } else {
        // Gagal hapus karena masalah izin
        error_log("Gagal menghapus file lama (Izin Ditolak): " . $file_path);
        return false;
      }
    }

    return true;
  }
}
