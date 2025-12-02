<?php
require_once '../app/models/FeedbackModel.php';
require_once '../app/controllers/BaseController.php';

class FeedbackController extends BaseController
{
  private $feedbackModel;

  public function __construct()
  {
    parent::__construct();
    $this->feedbackModel = new FeedbackModel();
  }

  // Halaman utama feedback 
  public function index()
  {
    parent::requireLogin();
    parent::requireRole('kepala');

    $page_title = 'Manajemen Feedback';
    $page_breadcrumb = ['Pages', 'Manajemen Feedback'];

    include '../app/views/feedback.php';
  }

  // Get List Feedback
  public function getList()
  {
    parent::requireLogin();
    parent::requireRole('kepala');

    $page   = isset($_GET['page']) ? (int)$_GET['page'] : 1;
    $limit  = isset($_GET['limit']) ? (int)$_GET['limit'] : 5;
    $search = isset($_GET['search']) ? trim($_GET['search']) : '';

    $offset = ($page - 1) * $limit;

    $data = $this->feedbackModel->getFeedback($limit, $offset, $search);
    $total = $this->feedbackModel->countFeedback($search);

    $response_data = [
      'data' => $data,
      'total' => $total,
    ];

    $this->jsonResponse($response_data);
  }

  // API untuk menerima feedback dari web profile (tanpa auth)
  // public function create()
  // {
  //   // Cek method
  //   if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
  //     $this->jsonResponse([
  //       'success' => false,
  //       'message' => 'Method not allowed'
  //     ], 405);
  //     return;
  //   }

  //   // Get data dari input
  //   $input = json_decode(file_get_contents('php://input'), true);

  //   // Jika tidak ada data JSON, coba dari form data
  //   if (empty($input)) {
  //     $input = $_POST;
  //   }

  //   // Validasi required fields
  //   $required = ['name', 'email', 'content'];
  //   foreach ($required as $field) {
  //     if (empty($input[$field])) {
  //       $this->jsonResponse([
  //         'success' => false,
  //         'message' => "Field $field is required"
  //       ], 400);
  //       return;
  //     }
  //   }

  //   // Sanitize data
  //   $data = [
  //     'name' => trim(strip_tags($input['name'])),
  //     'email' => filter_var(trim($input['email']), FILTER_SANITIZE_EMAIL),
  //     'content' => trim(strip_tags($input['content']))
  //   ];

  //   // Validasi email
  //   if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
  //     $this->jsonResponse([
  //       'success' => false,
  //       'message' => 'Invalid email format'
  //     ], 400);
  //     return;
  //   }

  //   // Insert feedback
  //   $success = $this->feedbackModel->insertFeedback($data);

  //   $response_data = [
  //     'success' => $success,
  //     'message' => $success ? 'Feedback berhasil dikirim. Terima kasih!' : 'Gagal mengirim feedback. Silakan coba lagi.'
  //   ];

  //   $this->jsonResponse($response_data);
  // }

  // Delete feedback 
  public function delete()
  {
    $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;

    if ($id === 0) {
      $this->jsonResponse([
        'success' => false,
        'message' => 'ID feedback tidak valid'
      ], 400);
      return;
    }

    $success = $this->feedbackModel->deleteFeedback($id);

    $response_data = [
      'success' => $success,
      'message' => $success ? 'Feedback berhasil dihapus.' : 'Gagal menghapus feedback. Cek log error.'
    ];

    $this->jsonResponse($response_data);
  }

  // Get detail feedback 
  public function detail()
  {
    $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

    if ($id === 0) {
      $this->jsonResponse([
        'success' => false,
        'message' => 'ID feedback tidak valid'
      ], 400);
      return;
    }

    $feedback = $this->feedbackModel->getFeedbackById($id);

    if (!$feedback) {
      $this->jsonResponse([
        'success' => false,
        'message' => 'Feedback tidak ditemukan'
      ], 404);
      return;
    }

    $this->jsonResponse([
      'success' => true,
      'data' => $feedback
    ]);
  }
}
