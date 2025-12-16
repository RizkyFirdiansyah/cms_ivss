<?php
require_once __DIR__ . '/ApiBaseController.php';
require_once __DIR__ . '/../../app/models/RegistrationModel.php';

class RegistrationApiController extends ApiBaseController
{
  private $registrationModel;

  public function __construct()
  {
    $this->registrationModel = new RegistrationModel();
  }


  // Submit pendaftaran
  public function submit()
  {
    try {
      // Terima data JSON atau form-data
      $data = $this->getRequestData();

      // Validasi required fields (tanpa password)
      $required = ['name', 'email', 'nim', 'study_program_id'];
      foreach ($required as $field) {
        if (empty($data[$field])) {
          return $this->sendError("Field '$field' diperlukan", 400);
        }
      }

      // Validasi email
      if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
        return $this->sendError("Format email tidak valid", 400);
      }

      // Validasi NIM (contoh: minimal 8 digit)
      if (!preg_match('/^\d{8,}$/', $data['nim'])) {
        return $this->sendError("NIM harus minimal 8 digit angka", 400);
      }

      // Cek duplicate email/NIM
      $duplicateCheck = $this->registrationModel->checkDuplicateRegistration(
        $data['email'],
        $data['nim']
      );

      if ($duplicateCheck['email_exists']) {
        return $this->sendError("Email sudah terdaftar", 409);
      }

      if ($duplicateCheck['nim_exists']) {
        return $this->sendError("NIM sudah terdaftar", 409);
      }

      // Simpan sebagai pending (tanpa password)
      $userId = $this->registrationModel->insertRegistration($data, $data['nim']);

      if ($userId) {
        // Log success (opsional)
        error_log("Pendaftaran berhasil: {$data['email']} - {$data['nim']}");

        return $this->sendSuccess(
          ['user_id' => $userId],
          'Pendaftaran berhasil! Menunggu persetujuan dari Kepala Lab. Password akan dikirim via email setelah disetujui.',
          201 // 201 Created
        );
      } else {
        return $this->sendError("Gagal melakukan pendaftaran. Silakan coba lagi.", 500);
      }
    } catch (Exception $e) {
      error_log("Registration API Error: " . $e->getMessage());
      return $this->sendError("Terjadi kesalahan sistem", 500);
    }
  }


  // Get program studi
  public function getProgramStudi()
  {
    try {

      $programStudi = $this->registrationModel->getAllProgramStudi();
      $response = [
        'program_studi' => $programStudi,
        'meta' => [
          'generated_at' => date('c'),
          'version' => '1.0'
        ]
      ];

      return $this->sendSuccess($response, 'Program studi retrieved successfully');
    } catch (Exception $e) {
      return $this->sendError($e->getMessage(), 500);
    }
  }

  // Check duplicate
  public function check()
  {
    try {
      $email = isset($_GET['email']) ? trim($_GET['email']) : '';
      $nim = isset($_GET['nim']) ? trim($_GET['nim']) : '';

      if (empty($email) && empty($nim)) {
        return $this->sendError("Email atau NIM diperlukan", 400);
      }

      $result = $this->registrationModel->checkDuplicateRegistration($email, $nim);

      return $this->sendSuccess($result, 'Check duplicate completed');
    } catch (Exception $e) {
      return $this->sendError($e->getMessage(), 500);
    }
  }

  // Fungsi untuk mengambil data JSON atau form-data
  private function getRequestData()
  {
    // Support both JSON and form-data
    $contentType = isset($_SERVER['CONTENT_TYPE']) ? $_SERVER['CONTENT_TYPE'] : '';

    if (strpos($contentType, 'application/json') !== false) {
      $json = file_get_contents('php://input');
      $data = json_decode($json, true);
      return $data ?: [];
    } else {
      return $_POST;
    }
  }
}
