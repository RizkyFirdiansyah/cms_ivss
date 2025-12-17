<?php
require_once '../app/models/DashboardModel.php';
require_once '../app/controllers/BaseController.php';

class DashboardController extends BaseController
{
  private $dashboardModel;

  public function __construct()
  {
    parent::__construct();
    parent::requireLogin();
    parent::requireRoles(['kepala', 'dosen', 'mahasiswa']);

    $this->dashboardModel = new DashboardModel();
  }

  public function index()
  {
    $page_title = 'Dashboard';
    $page_breadcrumb = ['Dashboard'];

    // Include dashboard view
    include '../app/views/dashboard.php';
  }

  // Get dashboard data by role
  public function getDashboardData()
  {
    try {
      $userRole = $_SESSION['role'] ?? 'kepala';
      $userId = $_SESSION['id'] ?? null;

      $totals = $this->dashboardModel->getRoleBasedTotals($userRole, $userId);

      $recentDatasets = $this->dashboardModel->getRecentDatasets(5);

      $additionalData = [];

      if ($userRole === 'kepala') {
        $additionalData['recent_feedback'] = $this->dashboardModel->getRecentFeedback(5);
      }

      $response = [
        'success' => true,
        'role' => $userRole,
        'user_id' => $userId,
        'totals' => $totals,
        'recent_datasets' => $recentDatasets,
        'timestamp' => date('Y-m-d H:i:s')
      ];

      // Merge additional data jika ada
      if (!empty($additionalData)) {
        $response = array_merge($response, $additionalData);
      }

      $this->jsonResponse($response);
    } catch (Exception $e) {
      error_log("Error in DashboardController::getDashboardData: " . $e->getMessage());

      $this->jsonResponse([
        'success' => false,
        'message' => 'Gagal memuat data dashboard',
        'error' => $e->getMessage()
      ], 500);
    }
  }

  // Get recent datasets
  public function getRecentDatasets()
  {
    try {
      $limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 5;

      $recentDatasets = $this->dashboardModel->getRecentDatasets($limit);

      $this->jsonResponse([
        'success' => true,
        'recent_datasets' => $recentDatasets
      ]);
    } catch (Exception $e) {
      $this->jsonResponse([
        'success' => false,
        'message' => 'Gagal memuat dataset terbaru'
      ], 500);
    }
  }

  // Get recent feedback
  public function getRecentFeedback()
  {
    try {
      $limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 5;
      $recentFeedback = $this->dashboardModel->getRecentFeedback($limit);

      $this->jsonResponse([
        'success' => true,
        'recent_feedback' => $recentFeedback
      ]);
    } catch (Exception $e) {
      $this->jsonResponse([
        'success' => false,
        'message' => 'Gagal memuat feedback terbaru'
      ], 500);
    }
  }

  // Get role-based totals
  public function getTotals()
  {
    try {
      $userRole = $_SESSION['role'] ?? 'kepala';
      $userId = $_SESSION['id'] ?? null;

      $totals = $this->dashboardModel->getRoleBasedTotals($userRole, $userId);

      $this->jsonResponse([
        'success' => true,
        'totals' => $totals,
        'timestamp' => date('Y-m-d H:i:s')
      ]);
    } catch (Exception $e) {
      $this->jsonResponse([
        'success' => false,
        'message' => 'Gagal memuat totals'
      ], 500);
    }
  }

  // Get role summary
  public function getRoleSummary()
  {
    try {
      $userRole = $_SESSION['role'] ?? 'kepala';
      $userId = $_SESSION['id'] ?? null;

      // Get totals
      $totals = $this->dashboardModel->getRoleBasedTotals($userRole, $userId);

      // Get recent datasets
      $recentDatasets = $this->dashboardModel->getRecentDatasets($userRole, $userId, 3);

      // Initialize summary
      $summary = [
        'role' => $userRole,
        'totals_count' => count($totals),
        'datasets_count' => count($recentDatasets),
        'has_feedback' => false
      ];

      if ($userRole === 'kepala') {
        $feedback = $this->dashboardModel->getRecentFeedback(1);
        $summary['has_feedback'] = !empty($feedback);
        $summary['feedback_count'] = count($feedback);
      }

      $this->jsonResponse([
        'success' => true,
        'summary' => $summary,
        'timestamp' => date('Y-m-d H:i:s')
      ]);
    } catch (Exception $e) {
      $this->jsonResponse([
        'success' => false,
        'message' => 'Gagal memuat summary role'
      ], 500);
    }
  }
}
