<?php
require_once '../app/models/RegistrationModel.php';
require_once '../app/models/UserModel.php';
require_once '../app/controllers/BaseController.php';

class RegistrationController extends BaseController
{
  private $registrationModel;
  private $userModel;

  public function __construct()
  {
    parent::__construct();
    parent::requireLogin();
    parent::requireRole('kepala');

    $this->registrationModel = new RegistrationModel();
    $this->userModel = new UserModel();
  }


  // Submit pendaftaran
  public function submit()
  {
    header('Content-Type: application/json');

    $data = json_decode(file_get_contents('php://input'), true);

    if (!isset($data['name'], $data['email'], $data['password'], $data['nim'], $data['study_program_id'])) {
      echo json_encode([
        'success' => false,
        'message' => 'Data tidak lengkap'
      ]);
      return;
    }

    // 1. Cek duplicate email/NIM
    $duplicateCheck = $this->registrationModel->checkDuplicateRegistration(
      $data['email'],
      $data['nim']
    );

    if ($duplicateCheck['email_exists']) {
      echo json_encode([
        'success' => false,
        'message' => 'Email sudah terdaftar'
      ]);
      return;
    }

    if ($duplicateCheck['nim_exists']) {
      echo json_encode([
        'success' => false,
        'message' => 'NIM sudah terdaftar'
      ]);
      return;
    }

    // 2. Simpan sebagai pending
    $userId = $this->registrationModel->insertRegistration($data, $data['nim']);

    if ($userId) {
      echo json_encode([
        'success' => true,
        'message' => 'Pendaftaran berhasil! Menunggu persetujuan dari Kepala Lab.',
        'user_id' => $userId
      ]);
    } else {
      echo json_encode([
        'success' => false,
        'message' => 'Gagal melakukan pendaftaran. Silakan coba lagi.'
      ]);
    }
  }

  // Get daftar pendaftaran pending
  public function getPending()
  {
    $page   = isset($_GET['page']) ? (int)$_GET['page'] : 1;
    $limit  = isset($_GET['limit']) ? (int)$_GET['limit'] : 5;
    $search = isset($_GET['search']) ? trim($_GET['search']) : '';

    $offset = ($page - 1) * $limit;

    $data = $this->registrationModel->getPendingRegistrations($limit, $offset, $search);
    $total = $this->registrationModel->countPendingRegistrations($search);

    $response_data = [
      'success' => true,
      'data' => $data,
      'total' => $total,
    ];

    $this->jsonResponse($response_data);
  }

  // Get detail pendaftaran
  public function getDetail()
  {
    $id = $_GET['id'] ?? null;

    $data = $this->registrationModel->getPendingRegistrationDetail($id);

    if ($data) {
      $response_data = [
        'success' => true,
        'data' => $data
      ];
    } else {
      $response_data = [
        'success' => false,
        'message' => 'Data pendaftaran tidak ditemukan'
      ];
    }

    $this->jsonResponse($response_data);
  }

  // Approve pendaftaran
  public function approve()
  {
    $id = $_POST['id'];

    if (!isset($id)) {
      $this->jsonResponse([
        'success' => false,
        'message' => 'ID tidak valid'
      ]);
      return;
    }

    $userData = $this->registrationModel->approveRegistration($id);

    if ($userData) {


      // Kirim email ke mahasiswa
      $emailSent = $this->sendApprovalEmail(
        $userData['email'],
        $userData['name'],
        $userData['generated_password']
      );

      $response_data = [
        'success' => true,
        'message' => 'Pendaftaran disetujui. ' .
          ($emailSent ? 'Email telah dikirim ke mahasiswa.' : 'Email gagal dikirim.'),
        'email_sent' => $emailSent
      ];
    } else {
      $response_data = [
        'success' => false,
        'message' => 'Gagal menyetujui pendaftaran'
      ];
    }

    $this->userModel->refreshMaterializedViews();


    $this->jsonResponse($response_data);
  }

  // Reject pendaftaran
  public function reject()
  {
    $id = $_POST['id'];

    if (!isset($id)) {
      $this->jsonResponse([
        'success' => false,
        'message' => 'ID tidak valid'
      ]);
      return;
    }

    $userData = $this->registrationModel->rejectRegistration($id);

    if ($userData) {
      // Kirim email penolakan
      $emailSent = $this->sendRejectionEmail(
        $userData['email'],
        $userData['name']
      );

      $response_data = [
        'success' => true,
        'message' => 'Pendaftaran ditolak. ' .
          ($emailSent ? 'Email telah dikirim ke mahasiswa.' : 'Email gagal dikirim.'),
        'email_sent' => $emailSent
      ];
    } else {
      $response_data = [
        'success' => false,
        'message' => 'Gagal menolak pendaftaran'
      ];
    }

    $this->jsonResponse($response_data);
  }

  // Helper function
  // Kirim email approval ke mahasiswa
  private function sendApprovalEmail($toEmail, $name, $password)
  {
    require_once __DIR__ . '/../libs/SimpleMailer.php';
    return SimpleMailer::sendApproval($toEmail, $name, $password);
  }

  // Kirim email penolakan ke mahasiswa
  private function sendRejectionEmail($toEmail, $name)
  {
    require_once __DIR__ . '/../libs/SimpleMailer.php';
    return SimpleMailer::sendRejection($toEmail, $name);
  }

  // Get count pending untuk badge
  public function getPendingCount()
  {
    $count = $this->registrationModel->getPendingCount();

    $response_data = [
      'success' => true,
      'count' => $count
    ];

    $this->jsonResponse($response_data);
  }
}
