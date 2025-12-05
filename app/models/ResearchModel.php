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

  // Read Data Research dengan Kategori dan Participants
  // Read Data Research dengan Kategori dan Participants
  // Read Data Research dengan Kategori dan Participants - DIMODIFIKASI
  // Read Data Research dengan Kategori dan Participants - DIMODIFIKASI untuk mahasiswa
  // Read Data Research dengan Kategori dan Participants - DIMODIFIKASI untuk mahasiswa
  // Read Data Research dengan Kategori dan Participants - DIPERBAIKI untuk dosen
  // Alternatif untuk dosen - PASTIKAN hanya research yang dibuat oleh dosen itu sendiri
  // Read Data Research dengan Kategori dan Participants - VERSI FIXED
// Read Data Research dengan Kategori dan Participants - PERBAIKAN UNTUK DOSEN
public function getResearch($limit, $offset, $search = '', $category_id = null, $status = '', $userId = null, $userRole = null)
{
    $query = "SELECT DISTINCT 
                r.*, 
                u.name as creator_name,
                STRING_AGG(DISTINCT c.name, ', ') as categories,
                STRING_AGG(DISTINCT CAST(c.id AS VARCHAR), ',') as category_ids,
                STRING_AGG(DISTINCT pu.name, ', ') as participants,
                STRING_AGG(DISTINCT CAST(pu.id AS VARCHAR), ',') as participant_ids
              FROM research r
              LEFT JOIN users u ON r.created_by_user_id = u.id
              LEFT JOIN research_categories rc ON r.id = rc.research_id
              LEFT JOIN categories c ON rc.category_id = c.id
              LEFT JOIN research_participants rp ON r.id = rp.research_id
              LEFT JOIN users pu ON rp.user_id = pu.id";
    
    $whereConditions = [];
    $params = [];
    
    // Filter berdasarkan role user
    if ($userRole === 'dosen') {
        // DOSEN bisa melihat:
        // 1. Research yang DIA BUAT SENDIRI (created_by_user_id = user_id)
        // 2. Research dimana DIA BERPARTISIPASI (sebagai participant)
        $whereConditions[] = "(r.created_by_user_id = :user_id 
                              OR rp.user_id = :user_id)";
        $params[':user_id'] = $userId;
        
        // Debug log
        error_log("Dosen Filter: user_id={$userId}, melihat research yang dibuat atau diikuti");
    } elseif ($userRole === 'mahasiswa') {
        // MAHASISWA bisa melihat:
        // 1. Research yang DIA BUAT SENDIRI
        // 2. Research dimana DIA BERPARTISIPASI
        // 3. Research yang dibuat oleh DOSEN
        $whereConditions[] = "(r.created_by_user_id = :user_id 
                              OR rp.user_id = :user_id 
                              OR u.role = 'dosen')";
        $params[':user_id'] = $userId;
        
        // Debug log
        error_log("Mahasiswa Filter: user_id={$userId}, melihat research yang dibuat, diikuti, atau oleh dosen");
    }
    // Jika kepala lab (admin), tidak ada filter tambahan
    
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
    
    $query .= " GROUP BY r.id, u.name
                ORDER BY r.start_date DESC 
                LIMIT :limit OFFSET :offset";
    
    // Debug: Log query untuk dosen
    if ($userRole === 'dosen') {
        $debugQuery = str_replace(array_keys($params), array_values($params), $query);
        error_log("Query untuk Dosen: " . $debugQuery);
    }
    
    try {
        $stmt = $this->conn->prepare($query);
        
        // Bind parameters
        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value, PDO::PARAM_STR);
        }
        
        $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', (int)$offset, PDO::PARAM_INT);
        
        $stmt->execute();
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Debug log hasil
        error_log("Hasil query untuk {$userRole} (ID: {$userId}): " . count($results) . " records");
        
        // Process data untuk semua results
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

// Count Research - PERBAIKAN UNTUK DOSEN
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
        // DOSEN: research yang dibuat atau diikuti
        $whereConditions[] = "(r.created_by_user_id = :user_id OR rp.user_id = :user_id)";
        $params[':user_id'] = $userId;
    } elseif ($userRole === 'mahasiswa') {
        // MAHASISWA: research yang dibuat, diikuti, atau oleh dosen
        $whereConditions[] = "(r.created_by_user_id = :user_id 
                              OR rp.user_id = :user_id 
                              OR u.role = 'dosen')";
        $params[':user_id'] = $userId;
    }
    // Jika kepala lab (admin), tidak ada filter tambahan
    
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
        
        // Bind parameters
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
  public function saveResearch($data)
  {
    $this->conn->beginTransaction();

    try {
      if (!empty($data["id"])) {
        // UPDATE Research
        $query = "UPDATE research 
                      SET title = :title,
                          status = :status,
                          description = :description,
                          budget = :budget,
                          start_date = :start_date,
                          finish_date = :finish_date
                      WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(":id", $data["id"], PDO::PARAM_INT);
      } else {
        // INSERT Research
        $query = "INSERT INTO research (created_by_user_id, title, status, description, budget, start_date, finish_date)
                      VALUES (:created_by_user_id, :title, :status, :description, :budget, :start_date, :finish_date)";
        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(':created_by_user_id', $data['created_by_user_id'], PDO::PARAM_INT);
      }

      $stmt->bindValue(':title', $data['title'] ?? '', PDO::PARAM_STR);
      $stmt->bindValue(':status', $data['status'] ?? 'ongoing', PDO::PARAM_STR);
      $stmt->bindValue(':description', $data['description'] ?? '', PDO::PARAM_STR);
      $stmt->bindValue(':budget', $data['budget'] ?? 0, PDO::PARAM_INT);
      $stmt->bindValue(':start_date', $data['start_date'], PDO::PARAM_STR);

      // Handle NULL finish_date
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

      // DEBUG: Log success
      error_log("Research saved successfully. ID: " . $researchId);

      return $researchId;
    } catch (PDOException $e) {
      $this->conn->rollBack();
      error_log("DB Error (saveResearch): " . $e->getMessage());
      error_log("Data causing error: " . print_r($data, true));
      return false;
    }
  }

  // Save research categories
  private function saveResearchCategories($researchId, $categories)
  {
    // Validasi input
    if (!is_array($categories)) {
      $categories = [];
    }

    // Filter dan sanitize category IDs
    $categories = array_filter($categories, function ($catId) {
      return is_numeric($catId) && $catId > 0;
    });

    // Delete existing categories
    $deleteStmt = $this->conn->prepare("DELETE FROM research_categories WHERE research_id = :research_id");
    $deleteStmt->bindValue(':research_id', $researchId, PDO::PARAM_INT);
    $deleteStmt->execute();

    // Insert new categories hanya jika ada
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
    // Validasi input
    if (!is_array($participants)) {
      $participants = [];
    }

    // Filter dan sanitize participant IDs
    $participants = array_filter($participants, function ($userId) {
      return is_numeric($userId) && $userId > 0;
    });

    // Delete existing participants
    $deleteStmt = $this->conn->prepare("DELETE FROM research_participants WHERE research_id = :research_id");
    $deleteStmt->bindValue(':research_id', $researchId, PDO::PARAM_INT);
    $deleteStmt->execute();

    // Insert new participants hanya jika ada
    if (!empty($participants)) {
      $insertStmt = $this->conn->prepare("INSERT INTO research_participants (research_id, user_id) VALUES (:research_id, :user_id)");

      foreach ($participants as $userId) {
        $insertStmt->bindValue(':research_id', $researchId, PDO::PARAM_INT);
        $insertStmt->bindValue(':user_id', (int)$userId, PDO::PARAM_INT);
        $insertStmt->execute();
      }
    }
  }

  // Get single research by ID
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
                    STRING_AGG(DISTINCT CAST(pu.id AS VARCHAR), ',') as participant_ids
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

      // DEBUG: Log hasil query
      error_log("getById Result for ID {$id}: " . print_r($result, true));

      if (!$result) {
        error_log("Research with ID {$id} not found");
        return null;
      }

      return $this->processResearchData($result);
    } catch (PDOException $e) {
      error_log("DB Error (getById for ID {$id}): " . $e->getMessage());
      error_log("SQL Query: " . $query);
      return null;
    }
  }

  // Delete Research
  public function delete($id)
  {
    $this->conn->beginTransaction();

    try {
      // Delete from research_categories first
      $stmt1 = $this->conn->prepare("DELETE FROM research_categories WHERE research_id = :id");
      $stmt1->execute([":id" => $id]);

      // Delete from research_participants
      $stmt2 = $this->conn->prepare("DELETE FROM research_participants WHERE research_id = :id");
      $stmt2->execute([":id" => $id]);

      // Then delete from research
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

  // Helper function: Process research data
  private function processResearchData($row)
  {
    // Process category_ids
    if (!empty($row['category_ids'])) {
      if (is_string($row['category_ids'])) {
        $row['category_ids'] = explode(',', $row['category_ids']);
        // Convert to integers
        $row['category_ids'] = array_map('intval', $row['category_ids']);
      }
    } else {
      $row['category_ids'] = [];
    }

    // Process participant_ids
    if (!empty($row['participant_ids'])) {
      if (is_string($row['participant_ids'])) {
        $row['participant_ids'] = explode(',', $row['participant_ids']);
        // Convert to integers
        $row['participant_ids'] = array_map('intval', $row['participant_ids']);
      }
    } else {
      $row['participant_ids'] = [];
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
}
