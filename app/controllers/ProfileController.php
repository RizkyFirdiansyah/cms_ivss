<?php

require_once '../app/models/ProfileModel.php';
require_once '../app/controllers/BaseController.php';

class ProfileController extends BaseController
{

  private $id_user;
  protected $upload_dir = __DIR__ . '/../../public/uploads/profile/';


  public function __construct()
  {
    parent::__construct();
    parent::requireLogin();

    $this->profileModel = new ProfileModel();
    $this->id_user = $this->user['id'];
  }

  // GET halaman profile
  public function index()
  {
    $user = $this->user;

    $foto_path = !empty($user['photo'])
      ? BASE_URL . '/public/uploads/profile/' . htmlspecialchars($user['photo'])
      : BASE_URL . '/public/assets/img/default-avatar.png';

    $status_message = $_SESSION['status_message'] ?? null;
    $status_type = $_SESSION['status_type'] ?? null;
    unset($_SESSION['status_message'], $_SESSION['status_type']);

    $page_title = 'Profile';
    $page_breadcrumb = ['Pages', 'Profile'];

    include '../app/views/profile.php';
  }


  // Update FORM BASIC
  public function update()
  {
    // Set JSON
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
      exit;
    }

    $basic_data = [
      'name'    => trim($_POST['name'] ?? ''),
      'email'   => trim($_POST['email'] ?? ''),
      'address' => trim($_POST['address'] ?? '')
    ];

    if (empty($basic_data['name']) || empty($basic_data['email'])) {
      exit;
    }

    $old_photo_to_delete = null;
    $photo_updated = false;

    // Upload foto 
    if (!empty($_FILES['photo']) && $_FILES['photo']['error'] === 0) {
      $photo_path = $this->handleFileUpload($_FILES['photo']);

      if ($photo_path === false) {
        $this->jsonResponse([
          'success' => false,
          'message' => 'Upload foto gagal atau format tidak sesuai.'
        ]);
        exit;
      }

      $basic_data['photo'] = $photo_path;
      $photo_updated = true;
    }

    $old_photo_or_success = $this->profileModel->updateBasicProfile($this->id_user, $basic_data);
    $success = ($photo_updated) ? ($old_photo_or_success !== false) : $old_photo_or_success;

    // Hapus Foto lama
    if ($success) {
      if ($photo_updated && $old_photo_or_success && $old_photo_or_success != 'default_photo.jpg') {
        $this->deleteFileFromServer($old_photo_or_success);
      }

      $this->jsonResponse([
        'success' => true,
        'message' => 'Profil berhasil diperbarui.'
      ]);
    } else {
      if ($photo_updated) {
        $this->deleteFileFromServer($basic_data['photo']);
      }

      $this->jsonResponse([
        'success' => false,
        'message' => 'Gagal memperbarui profil. Cek log error.'
      ]);
    }
    exit;
  }

  // Update FORM SOSMED
  public function update_sosmed()
  {
    $old = $_POST['social_media'] ?? [];
    $new = $_POST['social_media_new'] ?? [];

    if (empty($old) && empty($new)) {
      $this->jsonResponse([
        'success' => false,
        'message' => 'Tidak ada perubahan.'
      ]);
      exit;
    }

    try {
      $ok = $this->profileModel->updateSosialMedia(
        $this->id_user,
        $old,
        $new
      );

      $this->jsonResponse([
        'success' => $ok,
        'message' => $ok
          ? 'Media sosial berhasil diperbarui.'
          : 'Gagal memperbarui media sosial.'
      ]);
      exit;
    } catch (Exception $e) {
      $this->jsonResponse([
        'success' => false,
        'message' => 'Error: ' . $e->getMessage()
      ]);
      exit;
    }
  }

  // Update FORM LIST DINAMIS
  public function update_list()
  {
    $post_data = $_POST;
    $list_type = $post_data['list_type'] ?? null;

    try {
      if ($list_type && in_array($list_type, ['pendidikan_sertifikat', 'keahlian_mk'])) {

        $success = $this->profileModel->updateDynamicLists($this->id_user, $list_type, $post_data);

        if ($success) {
          $this->jsonResponse([
            'success' => true,
            'message' => 'Data list dinamis berhasil diperbarui.'
          ]);
        } else {
          $this->jsonResponse([
            'success' => false,
            'message' => 'Gagal memperbarui data list dinamis (Unknown Error).'
          ]);
        }
      } else {
        $this->jsonResponse([
          'success' => false,
          'message' => 'Tipe data list tidak valid.'
        ]);
      }
    } catch (Exception $e) {
      $this->jsonResponse([
        'success' => false,
        'message' => 'TERJADI Kesalahan Kritis: ' . $e->getMessage()
      ]);
    }
    exit;
  }


  // Helper Functoin
  // Handle File Upload
  private function handleFileUpload($file)
  {
    $upload_dir = 'uploads/profile/';
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

    $new_file_name = $this->id_user . '_' . time() . '.' . $ext;
    $target_path = $upload_path_full . $new_file_name;

    if (move_uploaded_file($file['tmp_name'], $target_path)) {
      return $new_file_name;
    }

    return false;
  }

  // Hapus File lama
  protected function deleteFileFromServer($file_name)
  {

    if (empty($file_name) || strpos($file_name, 'default') !== false) {
      // Jangan hapus jika kosong atau file default
      return true;
    }

    $file_path = $this->upload_dir . $file_name;

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

    // File tidak ada di server atau DB menyimpan NULL/kosong, anggap berhasil
    return true;
  }
}
