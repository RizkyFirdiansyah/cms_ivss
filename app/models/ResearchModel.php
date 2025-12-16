<?php
require_once __DIR__ . '/../config/Database.php';

class ResearchModel
{
  private $conn;

  public function __construct()
  {
    $db = new Database();
    $this->conn = $db->getConnection();
  }

  // Read Data Research dengan filter role
  public function getResearch($limit, $offset, $search = '', $category_id = null, $status = '', $userId = null, $userRole = null)
  {
    $query = "SELECT DISTINCT 
                    r.*, 
                    u.name as creator_name,
                    STRING_AGG(DISTINCT c.name, ', ') as categories,
                    STRING_AGG(DISTINCT CAST(c.id AS VARCHAR), ',') as category_ids,
                    STRING_AGG(DISTINCT pu.name, ', ') as participants,
                    STRING_AGG(DISTINCT CAST(pu.id AS VARCHAR), ',') as participant_ids,
                    STRING_AGG(DISTINCT rp.role, ', ') as participant_roles,
                    CASE WHEN r.created_by_user_id = :current_user_id THEN 'creator' ELSE 'participant' END as user_role_in_research
                  FROM research r
                  LEFT JOIN users u ON r.created_by_user_id = u.id
                  LEFT JOIN research_categories rc ON r.id = rc.research_id
                  LEFT JOIN categories c ON rc.category_id = c.id
                  LEFT JOIN research_participants rp ON r.id = rp.research_id
                  LEFT JOIN users pu ON rp.user_id = pu.id";

    $whereConditions = [];
    $params = [':current_user_id' => $userId];

    // Filter berdasarkan role user
    if ($userRole === 'dosen') {
      $whereConditions[] = "(r.created_by_user_id = :current_user_id OR rp.user_id = :current_user_id)";
      error_log("Dosen Filter: user_id={$userId}, melihat research sebagai creator atau participant");
    } elseif ($userRole === 'mahasiswa') {
      $whereConditions[] = "rp.user_id = :current_user_id";
      error_log("Mahasiswa Filter: user_id={$userId}, hanya melihat research sebagai participant");
    }

    // Filter search
    if ($search !== '') {
      $whereConditions[] = "(r.title ILIKE :search OR r.description ILIKE :search)";
      $params[':search'] = '%' . $search . '%';
    }

    // Filter kategori
    if ($category_id !== null && $category_id > 0) {
      $whereConditions[] = "rc.category_id = :category_id";
      $params[':category_id'] = $category_id;
    }

    // Filter status
    if ($status !== '') {
      $whereConditions[] = "r.status = :status";
      $params[':status'] = $status;
    }

    // Gabungkan kondisi WHERE
    if (!empty($whereConditions)) {
      $query .= " WHERE " . implode(" AND ", $whereConditions);
    }

    $query .= " GROUP BY r.id, u.name, r.created_by_user_id
                    ORDER BY r.start_date DESC 
                    LIMIT :limit OFFSET :offset";

    try {
      $stmt = $this->conn->prepare($query);

      foreach ($params as $key => $value) {
        $stmt->bindValue($key, $value, PDO::PARAM_STR);
      }

      $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
      $stmt->bindValue(':offset', (int)$offset, PDO::PARAM_INT);

      $stmt->execute();
      $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

      foreach ($results as &$row) {
        $row = $this->processResearchData($row);
      }

      return $results;
    } catch (PDOException $e) {
      error_log("DB Error (getResearch): " . $e->getMessage());
      error_log("Query: " . $query);
      return [];
    }
  }

  // Count Research dengan filter role
  public function countResearch($search = '', $category_id = null, $status = '', $userId = null, $userRole = null)
  {
    $query = "SELECT COUNT(DISTINCT r.id) AS total 
                  FROM research r
                  LEFT JOIN users u ON r.created_by_user_id = u.id
                  LEFT JOIN research_categories rc ON r.id = rc.research_id
                  LEFT JOIN research_participants rp ON r.id = rp.research_id";

    $whereConditions = [];
    $params = [];

    // Filter berdasarkan role user 
    if ($userRole === 'dosen') {
      $whereConditions[] = "(r.created_by_user_id = :user_id OR rp.user_id = :user_id)";
      $params[':user_id'] = $userId;
    } elseif ($userRole === 'mahasiswa') {
      $whereConditions[] = "rp.user_id = :user_id";
      $params[':user_id'] = $userId;
    }

    // Filter search
    if ($search !== '') {
      $whereConditions[] = "(r.title ILIKE :search OR r.description ILIKE :search)";
      $params[':search'] = '%' . $search . '%';
    }

    // Filter kategori
    if ($category_id !== null && $category_id > 0) {
      $whereConditions[] = "rc.category_id = :category_id";
      $params[':category_id'] = $category_id;
    }

    // Filter status
    if ($status !== '') {
      $whereConditions[] = "r.status = :status";
      $params[':status'] = $status;
    }

    // Gabungkan kondisi WHERE
    if (!empty($whereConditions)) {
      $query .= " WHERE " . implode(" AND ", $whereConditions);
    }

    try {
      $stmt = $this->conn->prepare($query);

      foreach ($params as $key => $value) {
        $stmt->bindValue($key, $value, PDO::PARAM_STR);
      }

      $stmt->execute();
      $total = (int)$stmt->fetch(PDO::FETCH_ASSOC)['total'];

      error_log("Count untuk {$userRole} (ID: {$userId}): {$total} records");

      return $total;
    } catch (PDOException $e) {
      error_log("DB Error (countResearch): " . $e->getMessage());
      return 0;
    }
  }

  // Save Research dengan validasi max_participants
  public function saveResearch($data)
  {
    $this->conn->beginTransaction();

    try {
      // VALIDASI: Cek apakah creator mencoba menambahkan diri sendiri sebagai participant
      if (isset($data['participants']) && isset($data['created_by_user_id'])) {
        $creatorId = $data['created_by_user_id'];
        $participants = is_array($data['participants']) ? $data['participants'] : [];

        if (in_array($creatorId, $participants)) {
          throw new Exception("Creator cannot be added as participant");
        }
      }

      // VALIDASI: Cek max_participants jika update
      if (!empty($data["id"]) && isset($data['participants'])) {
        $currentCount = $this->countParticipants($data["id"]);
        $maxParticipants = $this->getMaxParticipants($data["id"]);
        $newParticipants = is_array($data['participants']) ? $data['participants'] : [];

        if (count($newParticipants) > $maxParticipants) {
          throw new Exception("Number of participants exceeds maximum limit of {$maxParticipants}");
        }
      }

      if (!empty($data["id"])) {
        $query = "UPDATE research 
                          SET title = :title,
                              status = :status,
                              description = :description,
                              budget = :budget,
                              max_participants = :max_participants,
                              start_date = :start_date,
                              finish_date = :finish_date
                          WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(":id", $data["id"], PDO::PARAM_INT);
      } else {
        $query = "INSERT INTO research (created_by_user_id, title, status, description, budget, max_participants, start_date, finish_date)
                          VALUES (:created_by_user_id, :title, :status, :description, :budget, :max_participants, :start_date, :finish_date)";
        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(':created_by_user_id', $data['created_by_user_id'], PDO::PARAM_INT);
      }

      $stmt->bindValue(':title', $data['title'] ?? '', PDO::PARAM_STR);
      $stmt->bindValue(':status', $data['status'] ?? 'ongoing', PDO::PARAM_STR);
      $stmt->bindValue(':description', $data['description'] ?? '', PDO::PARAM_STR);
      $stmt->bindValue(':budget', $data['budget'] ?? 0, PDO::PARAM_INT);
      $stmt->bindValue(':max_participants', $data['max_participants'] ?? 10, PDO::PARAM_INT);
      $stmt->bindValue(':start_date', $data['start_date'], PDO::PARAM_STR);

      if (!empty($data['finish_date'])) {
        $stmt->bindValue(':finish_date', $data['finish_date'], PDO::PARAM_STR);
      } else {
        $stmt->bindValue(':finish_date', null, PDO::PARAM_NULL);
      }

      $stmt->execute();

      // Get research ID (for new insert)
      $researchId = !empty($data["id"]) ? $data["id"] : $this->conn->lastInsertId();

      // Handle categories
      if (isset($data['categories'])) {
        $this->saveResearchCategories($researchId, $data['categories']);
      }

      // Handle participants
      if (isset($data['participants'])) {
        $this->saveResearchParticipants($researchId, $data['participants']);
      }

      $this->conn->commit();

      return $researchId;
    } catch (Exception $e) {
      $this->conn->rollBack();
      error_log("DB Error (saveResearch): " . $e->getMessage());
      throw $e;
    }
  }

  // Get single research by ID
  public function getById($id)
  {
    try {
      $query = "SELECT 
                        r.*, 
                        u.name as creator_name,
                        STRING_AGG(DISTINCT c.name, ', ') as categories,
                        STRING_AGG(DISTINCT CAST(c.id AS VARCHAR), ',') as category_ids,
                        STRING_AGG(DISTINCT pu.name, ', ') as participants,
                        STRING_AGG(DISTINCT CAST(pu.id AS VARCHAR), ',') as participant_ids,
                        STRING_AGG(DISTINCT rp.role, ', ') as participant_roles
                      FROM research r
                      LEFT JOIN users u ON r.created_by_user_id = u.id
                      LEFT JOIN research_categories rc ON r.id = rc.research_id
                      LEFT JOIN categories c ON rc.category_id = c.id
                      LEFT JOIN research_participants rp ON r.id = rp.research_id
                      LEFT JOIN users pu ON rp.user_id = pu.id
                      WHERE r.id = :id
                      GROUP BY r.id, u.name";

      $stmt = $this->conn->prepare($query);
      $stmt->execute([":id" => $id]);
      $result = $stmt->fetch(PDO::FETCH_ASSOC);

      if (!$result) {
        return null;
      }

      return $this->processResearchData($result);
    } catch (PDOException $e) {
      error_log("DB Error (getById): " . $e->getMessage());
      return null;
    }
  }

  // Delete Research
  public function delete($id)
  {
    $this->conn->beginTransaction();

    try {
      // Delete from research_categories
      $stmt1 = $this->conn->prepare("DELETE FROM research_categories WHERE research_id = :id");
      $stmt1->execute([":id" => $id]);

      // Delete from research_participants
      $stmt2 = $this->conn->prepare("DELETE FROM research_participants WHERE research_id = :id");
      $stmt2->execute([":id" => $id]);

      // Delete from research
      $stmt3 = $this->conn->prepare("DELETE FROM research WHERE id = :id");
      $result = $stmt3->execute([":id" => $id]);

      $this->conn->commit();
      return $result;
    } catch (PDOException $e) {
      $this->conn->rollBack();
      error_log("DB Error (delete): " . $e->getMessage());
      return false;
    }
  }

  // Check if user can edit research (creator only)
  public function canUserEdit($researchId, $userId, $userRole)
  {
    if ($userRole === 'kepala_lab' || $userRole === 'admin') {
      return true;
    }

    $stmt = $this->conn->prepare("SELECT created_by_user_id FROM research WHERE id = :id");
    $stmt->execute([":id" => $researchId]);
    $research = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$research) {
      return false;
    }

    return $research['created_by_user_id'] == $userId;
  }

  // Helper function
  // Count current participants
  private function countParticipants($researchId)
  {
    $stmt = $this->conn->prepare("SELECT COUNT(*) FROM research_participants WHERE research_id = :research_id");
    $stmt->bindValue(':research_id', $researchId, PDO::PARAM_INT);
    $stmt->execute();
    return (int)$stmt->fetchColumn();
  }

  // Get max participants
  private function getMaxParticipants($researchId)
  {
    $stmt = $this->conn->prepare("SELECT max_participants FROM research WHERE id = :id");
    $stmt->bindValue(':id', $researchId, PDO::PARAM_INT);
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    return $result ? (int)$result['max_participants'] : 10;
  }

  // Save research categories
  private function saveResearchCategories($researchId, $categories)
  {
    if (!is_array($categories)) {
      $categories = [];
    }

    $categories = array_filter($categories, function ($catId) {
      return is_numeric($catId) && $catId > 0;
    });

    // Delete existing categories
    $deleteStmt = $this->conn->prepare("DELETE FROM research_categories WHERE research_id = :research_id");
    $deleteStmt->bindValue(':research_id', $researchId, PDO::PARAM_INT);
    $deleteStmt->execute();

    // Insert new categories
    if (!empty($categories)) {
      $insertStmt = $this->conn->prepare("INSERT INTO research_categories (research_id, category_id) VALUES (:research_id, :category_id)");

      foreach ($categories as $categoryId) {
        $insertStmt->bindValue(':research_id', $researchId, PDO::PARAM_INT);
        $insertStmt->bindValue(':category_id', (int)$categoryId, PDO::PARAM_INT);
        $insertStmt->execute();
      }
    }
  }

  // Save research participants
  private function saveResearchParticipants($researchId, $participants)
  {
    if (!is_array($participants)) {
      $participants = [];
    }

    $participants = array_filter($participants, function ($userId) {
      return is_numeric($userId) && $userId > 0;
    });

    // Delete existing participants
    $deleteStmt = $this->conn->prepare("DELETE FROM research_participants WHERE research_id = :research_id");
    $deleteStmt->bindValue(':research_id', $researchId, PDO::PARAM_INT);
    $deleteStmt->execute();

    // Insert new participants
    if (!empty($participants)) {
      $insertStmt = $this->conn->prepare("INSERT INTO research_participants (research_id, user_id, role) VALUES (:research_id, :user_id, 'participant')");

      foreach ($participants as $userId) {
        $insertStmt->bindValue(':research_id', $researchId, PDO::PARAM_INT);
        $insertStmt->bindValue(':user_id', (int)$userId, PDO::PARAM_INT);
        $insertStmt->execute();
      }
    }
  }

  // Process research data
  private function processResearchData($row)
  {
    // Process category_ids
    if (!empty($row['category_ids'])) {
      if (is_string($row['category_ids'])) {
        $row['category_ids'] = explode(',', $row['category_ids']);
        $row['category_ids'] = array_map('intval', $row['category_ids']);
      }
    } else {
      $row['category_ids'] = [];
    }

    // Process participant_ids
    if (!empty($row['participant_ids'])) {
      if (is_string($row['participant_ids'])) {
        $row['participant_ids'] = explode(',', $row['participant_ids']);
        $row['participant_ids'] = array_map('intval', $row['participant_ids']);
      }
    } else {
      $row['participant_ids'] = [];
    }

    // Process participant_roles
    if (!empty($row['participant_roles'])) {
      if (is_string($row['participant_roles'])) {
        $row['participant_roles'] = explode(', ', $row['participant_roles']);
      }
    } else {
      $row['participant_roles'] = [];
    }

    // Process categories names
    if (empty($row['categories'])) {
      $row['categories'] = '';
    }

    // Process participants names
    if (empty($row['participants'])) {
      $row['participants'] = '';
    }

    // Format dates
    if (!empty($row['start_date'])) {
      $row['formatted_start_date'] = date('d/m/Y', strtotime($row['start_date']));
    }

    if (!empty($row['finish_date'])) {
      $row['formatted_finish_date'] = date('d/m/Y', strtotime($row['finish_date']));
    }

    // Format budget
    if (!empty($row['budget'])) {
      $row['formatted_budget'] = 'Rp ' . number_format($row['budget'], 0, ',', '.');
    }

    return $row;
  }

  // Model untuk API Research menggunakan Materialized View
  public function getResearchForApi($limit = 10, $offset = 0, $search = '', $categoryId = null, $year = null)
  {
    $params = [];
    $where = [];

    if (!empty($search)) {
      $where[] = "(LOWER(title) LIKE LOWER(:search) OR LOWER(description) LIKE LOWER(:search))";
      $params[':search'] = "%$search%";
    }

    if (!empty($year)) {
      $where[] = "EXTRACT(YEAR FROM start_date) = :year";
      $params[':year'] = $year;
    }

    if (!empty($categoryId)) {
      $where[] = "EXISTS (
            SELECT 1 FROM jsonb_array_elements(categories_name) AS cat
            WHERE (cat->>'id')::int = :category_id
        )";
      $params[':category_id'] = $categoryId;
    }

    // Hapus filter status

    // Build WHERE
    $whereSql = "";
    if (!empty($where)) {
      $whereSql = "WHERE " . implode(" AND ", $where);
    }

    $sql = "
        SELECT *, 
        TO_CHAR(start_date, 'YYYY-MM-DD') as formatted_start_date,
        TO_CHAR(finish_date, 'YYYY-MM-DD') as formatted_finish_date,
        EXTRACT(YEAR FROM start_date) as start_year,
        EXTRACT(MONTH FROM start_date) as start_month,
        EXTRACT(DAY FROM start_date) as start_day
        FROM mv_research
        $whereSql
        ORDER BY start_date DESC
        LIMIT :limit OFFSET :offset
    ";

    $stmt = $this->conn->prepare($sql);

    foreach ($params as $key => $value) {
      $stmt->bindValue($key, $value);
    }

    $stmt->bindValue(":limit", (int)$limit, PDO::PARAM_INT);
    $stmt->bindValue(":offset", (int)$offset, PDO::PARAM_INT);

    $stmt->execute();
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Decode JSONB categories_name untuk setiap hasil
    foreach ($results as &$result) {
      if (isset($result['categories_name'])) {
        $result['categories'] = json_decode($result['categories_name'], true);
        unset($result['categories_name']);
      }

      // Format tanggal jika diperlukan
      if (isset($result['start_date'])) {
        $date = new DateTime($result['start_date']);
        $result['start_date_formatted'] = $date->format('Y-m-d');
        $result['start_date_readable'] = $date->format('d F Y');
      }

      if (isset($result['finish_date'])) {
        $date = new DateTime($result['finish_date']);
        $result['finish_date_formatted'] = $date->format('Y-m-d');
        $result['finish_date_readable'] = $date->format('d F Y');
      }

      // Hitung durasi (jika start_date dan finish_date ada)
      if (isset($result['start_date']) && isset($result['finish_date'])) {
        $start = new DateTime($result['start_date']);
        $finish = new DateTime($result['finish_date']);
        $interval = $start->diff($finish);
        $result['duration_months'] = $interval->m + ($interval->y * 12);
        $result['duration_days'] = $interval->days;
      }
    }

    return $results;
  }

  // Get recent research for API
  public function getRecentResearchForApi($limit = 5)
  {
    $sql = "
        SELECT *, 
        TO_CHAR(start_date, 'YYYY-MM-DD') as formatted_start_date,
        TO_CHAR(finish_date, 'YYYY-MM-DD') as formatted_finish_date
        FROM mv_research
        ORDER BY start_date DESC
        LIMIT :limit
    ";

    $stmt = $this->conn->prepare($sql);
    $stmt->bindValue(":limit", (int)$limit, PDO::PARAM_INT);
    $stmt->execute();

    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Decode categories
    foreach ($results as &$result) {
      if (isset($result['categories_name'])) {
        $result['categories'] = json_decode($result['categories_name'], true);
        unset($result['categories_name']);
      }
    }

    return $results;
  }

  // Count research for pagination
  public function countResearchForApi($search = '', $categoryId = null, $year = null)
  {
    $params = [];
    $where = [];

    if (!empty($search)) {
      $where[] = "(LOWER(title) LIKE LOWER(:search) 
                     OR LOWER(description) LIKE LOWER(:search))";
      $params[':search'] = "%$search%";
    }

    if (!empty($year)) {
      $where[] = "EXTRACT(YEAR FROM start_date) = :year";
      $params[':year'] = $year;
    }

    if (!empty($categoryId)) {
      $where[] = "EXISTS (
            SELECT 1 FROM jsonb_array_elements(categories_name) AS cat
            WHERE (cat->>'id')::int = :category_id
        )";
      $params[':category_id'] = $categoryId;
    }

    // Hapus filter status

    // Build WHERE
    $whereSql = "";
    if (!empty($where)) {
      $whereSql = "WHERE " . implode(" AND ", $where);
    }

    $sql = "SELECT COUNT(*) as total FROM mv_research $whereSql";
    $stmt = $this->conn->prepare($sql);

    foreach ($params as $key => $value) {
      $stmt->bindValue($key, $value);
    }

    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    return (int)$result['total'];
  }

  // Get available years for filtering (berdasarkan start_date)
  public function getAvailableYears()
  {
    $sql = "
        SELECT DISTINCT EXTRACT(YEAR FROM start_date) as year 
        FROM mv_research 
        ORDER BY year DESC
    ";

    $stmt = $this->conn->prepare($sql);
    $stmt->execute();

    $years = $stmt->fetchAll(PDO::FETCH_COLUMN);
    return array_map('intval', $years);
  }
}
