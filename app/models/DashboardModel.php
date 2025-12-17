<?php
require_once __DIR__ . '/../config/Database.php';

class DashboardModel
{
  private $conn;

  public function __construct()
  {
    $db = new Database();
    $this->conn = $db->getConnection();
  }

  // Get dashboard totals for kepala
  public function getKepalaTotals()
  {
    $query = "SELECT * FROM vw_dashboard_totals LIMIT 1";

    try {
      $stmt = $this->conn->prepare($query);
      $stmt->execute();
      return $stmt->fetch(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
      error_log("DB Error (DashboardModel::getKepalaTotals): " . $e->getMessage());
      return $this->getDefaultTotals();
    }
  }

  // Get dashboard totals for dosen 
  public function getDosenTotals($userId)
  {
    if (!$userId) return [];

    try {
      $query = "SELECT * FROM fn_get_dosen_totals(:user_id)";

      $stmt = $this->conn->prepare($query);
      $stmt->execute([':user_id' => $userId]);
      $result = $stmt->fetch(PDO::FETCH_ASSOC);

      if ($result) {
        return [
          'my_publications' => $result['my_publications'] ?? 0,
          'my_datasets' => $result['my_datasets'] ?? 0,
          'my_research' => $result['my_research'] ?? 0
        ];
      }

      return [];
    } catch (PDOException $e) {
      error_log("DB Error (DashboardModel::getDosenTotals): " . $e->getMessage());
      return [];
    }
  }

  // Get dashboard totals for mahasiswa
  public function getMahasiswaTotals($userId)
  {
    if (!$userId) return [];

    try {
      $query = "SELECT * FROM fn_get_mahasiswa_totals(:user_id)";

      $stmt = $this->conn->prepare($query);
      $stmt->execute([':user_id' => $userId]);
      $result = $stmt->fetch(PDO::FETCH_ASSOC);

      $researchJoined = $this->getResearchJoinedCount($userId);

      if ($result) {
        return [
          'my_datasets' => $result['my_datasets'],
          'research_joined' => $researchJoined
        ];
      }

      return [
        'my_datasets' => 0,
        'research_joined' => $researchJoined
      ];
    } catch (PDOException $e) {
      error_log("DB Error (DashboardModel::getMahasiswaTotals): " . $e->getMessage());
      return [
        'my_datasets' => 0,
        'research_joined' => 0
      ];
    }
  }

  // Get research joined count
  private function getResearchJoinedCount($userId)
  {
    try {
      $query = "SELECT COUNT(DISTINCT research_id) as research_joined 
                FROM research_participants 
                WHERE user_id = :user_id";

      $stmt = $this->conn->prepare($query);
      $stmt->execute([':user_id' => $userId]);
      $result = $stmt->fetch(PDO::FETCH_ASSOC);

      return $result['research_joined'] ?? 0;
    } catch (PDOException $e) {
      error_log("DB Error (DashboardModel::getResearchJoinedCount): " . $e->getMessage());
      return 0;
    }
  }

  // Get role-based totals
  public function getRoleBasedTotals($role, $userId = null)
  {
    switch ($role) {
      case 'kepala':
        return $this->getKepalaTotals();

      case 'dosen':
        return $this->getDosenTotals($userId);

      case 'mahasiswa':
        return $this->getMahasiswaTotals($userId);

      default:
        return $this->getDefaultTotals();
    }
  }

  // Get recent datasets
  public function getRecentDatasets($limit = 5)
  {
    try {
      $query = "SELECT 
                    id,
                    title,
                    link,
                    updated_at,
                    (SELECT name FROM users WHERE id = datasets.user_id) as creator_name
                  FROM datasets 
                  ORDER BY updated_at DESC 
                  LIMIT :limit";

      $stmt = $this->conn->prepare($query);
      $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
      $stmt->execute();
      return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
      error_log("DB Error (DashboardModel::getRecentDatasets): " . $e->getMessage());
      return [];
    }
  }

  // Get recent feedback
  public function getRecentFeedback($limit = 5)
  {
    $query = "SELECT id, name, email, content, created_at 
              FROM feedbacks 
              ORDER BY created_at DESC 
              LIMIT :limit";

    try {
      $stmt = $this->conn->prepare($query);
      $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
      $stmt->execute();
      return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
      error_log("DB Error (DashboardModel::getRecentFeedback): " . $e->getMessage());
      return [];
    }
  }

  // Get default totals
  private function getDefaultTotals()
  {
    return [
      'total_users' => 0,
      'total_publications' => 0,
      'total_datasets' => 0,
      'total_research' => 0,
      'total_gallery' => 0,
      'total_feedback' => 0,
      'total_facilities' => 0
    ];
  }
}
