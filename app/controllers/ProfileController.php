<?php

require_once '../app/models/ProfileModel.php';
require_once '../app/controllers/BaseController.php';

class ProfileController extends BaseController
{

  private $id_user;

  public function __construct()
  {
    // Jalankan konstruktor dari BaseController untuk inisialisasi session, auth, dan user
    parent::__construct();
    parent::requireLogin();

    // Inisialisasi model khusus Profile (kalau ingin operasi tambahan)
    $this->profileModel = new ProfileModel();
    $this->id_user = $this->user['id'];
  }

  /**
   * Menampilkan halaman profil (GET Request)
   */
  public function index()
  {
    $user = $this->user;

    $foto_path = !empty($user['photo'])
      ? BASE_URL . '/public/uploads/' . htmlspecialchars($user['photo'])
      : BASE_URL . '/public/assets/img/default-avatar.png';

    $status_message = $_SESSION['status_message'] ?? null;
    $status_type = $_SESSION['status_type'] ?? null;
    unset($_SESSION['status_message'], $_SESSION['status_type']);

    $page_title = 'Profile';
    $page_breadcrumb = ['Pages', 'Profile'];

    include '../app/views/profile.php';
  }

  /**
   * Menangani submit form utama (POST Request ke BASE_URL/profile/update)
   */
  public function update()
  {
    // Set response selalu JSON
    header('Content-Type: application/json');

    // Validasi method
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
      echo json_encode([
        'success' => false,
        'message' => 'Metode request tidak valid.'
      ]);
      exit;
    }

    // Ambil data input
    $basic_data = [
      'name'    => trim($_POST['name'] ?? ''),
      'email'   => trim($_POST['email'] ?? ''),
      'address' => trim($_POST['address'] ?? '')
    ];

    // Validasi minimal name dan email
    if (empty($basic_data['name']) || empty($basic_data['email'])) {
      echo json_encode([
        'success' => false,
        'message' => 'Nama dan email tidak boleh kosong.'
      ]);
      exit;
    }

    // Upload foto jika ada
    if (!empty($_FILES['photo']) && $_FILES['photo']['error'] === 0) {
      $photo_path = $this->handleFileUpload($_FILES['photo']);

      if ($photo_path === false) {
        echo json_encode([
          'success' => false,
          'message' => 'Upload foto gagal atau format tidak sesuai.'
        ]);
        exit;
      }

      $basic_data['photo'] = $photo_path;
    }

    // Proses update database
    $success = $this->profileModel->updateBasicProfile($this->id_user, $basic_data);

    // Kirim output JSON
    echo json_encode([
      'success' => $success,
      'message' => $success
        ? 'Profil berhasil diperbarui.'
        : 'Gagal memperbarui profil. Cek log error.'
    ]);

    exit;
  }

  /**
   * ✅ ENDPOINT BARU: Menangani submit modal KELOLA Sosial Media (AJAX/POST)
   * Menggantikan add_sosmed() yang lama.
   */
  public function update_sosmed()
  {
    header('Content-Type: application/json');

    $old = $_POST['social_media'] ?? [];
    $new = $_POST['social_media_new'] ?? [];

    if (empty($old) && empty($new)) {
      echo json_encode([
        'success' => true,
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

      echo json_encode([
        'success' => $ok,
        'message' => $ok
          ? 'Media sosial berhasil diperbarui.'
          : 'Gagal memperbarui media sosial.'
      ]);
      exit;
    } catch (Exception $e) {
      echo json_encode([
        'success' => false,
        'message' => 'Error: ' . $e->getMessage()
      ]);
      exit;
    }
  }



  /**
   * ✅ ENDPOINT REVISI: Menangani submit modal List Dinamis (AJAX/POST)
   * Disinkronkan dengan Model yang mengharapkan $post_data lengkap.
   */
  public function update_list()
  {
    header('Content-Type: application/json');

    $post_data = $_POST;
    $list_type = $post_data['list_type'] ?? null;

    try {
      if ($list_type && in_array($list_type, ['pendidikan_sertifikat', 'keahlian_mk'])) {

        // Meneruskan SEMUA data POST ke Model
        $success = $this->profileModel->updateDynamicLists($this->id_user, $list_type, $post_data);

        if ($success) {
          echo json_encode([
            'success' => true,
            'message' => 'Data list dinamis berhasil diperbarui.'
          ]);
        } else {
          // Jalur ini umumnya tidak tercapai jika Model melempar Exception, 
          // tetapi dipertahankan sebagai fallback.
          echo json_encode([
            'success' => false,
            'message' => 'Gagal memperbarui data list dinamis (Unknown Error).'
          ]);
        }
      } else {
        echo json_encode([
          'success' => false,
          'message' => 'Tipe data list tidak valid.'
        ]);
      }
    } catch (Exception $e) {
      // Menangkap Exception (baik PDO maupun aplikasi) dari Model
      // dan menampilkan pesan error detail di modal AJAX.
      echo json_encode([
        'success' => false,
        'message' => 'TERJADI Kesalahan Kritis: ' . $e->getMessage()
      ]);
    }
    exit;
  }


  // Helper Functoin
  private function handleFileUpload($file)
  {
    $upload_dir = 'uploads/';

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

    $new_file_name = $this->id_user . '_' . time() . '.' . $ext;
    $target_path = $upload_path_full . $new_file_name;

    if (move_uploaded_file($file['tmp_name'], $target_path)) {
      return $new_file_name;
    }

    return false;
  }
}
