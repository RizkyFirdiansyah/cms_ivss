<?php
require_once __DIR__ . '/../config/Database.php';

class SettingsModel
{
  private $conn;

  public function __construct()
  {
    $db = new Database();
    $this->conn = $db->getConnection();
  }

  // Get all settings
  public function getAllSettings()
  {
    try {
      $query = "SELECT key_name, value, data_type, description 
                FROM settings 
                ORDER BY key_name";

      $stmt = $this->conn->prepare($query);
      $stmt->execute();

      $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

      // Convert to associative array with key_name as key
      $settings = [];
      foreach ($results as $row) {
        $settings[$row['key_name']] = [
          'value' => $row['value'],
          'data_type' => $row['data_type'],
          'description' => $row['description']
        ];
      }

      return $settings;
    } catch (PDOException $e) {
      error_log("DB Error (getAllSettings): " . $e->getMessage());
      return [];
    }
  }

  // Get single setting by key
  public function getSetting($key)
  {
    try {
      $query = "SELECT value, data_type, description 
                FROM settings 
                WHERE key_name = :key_name";

      $stmt = $this->conn->prepare($query);
      $stmt->bindValue(':key_name', $key, PDO::PARAM_STR);
      $stmt->execute();

      $result = $stmt->fetch(PDO::FETCH_ASSOC);

      return $result ? $result['value'] : null;
    } catch (PDOException $e) {
      error_log("DB Error (getSetting): " . $e->getMessage());
      return null;
    }
  }

  // Update multiple settings
  public function updateSettings($settingsData, $userId)
  {
    $this->conn->beginTransaction();

    try {
      $query = "INSERT INTO settings (key_name, value, data_type, description, user_id) 
                VALUES (:key_name, :value, :data_type, :description, :user_id)
                ON CONFLICT (key_name) 
                DO UPDATE SET 
                  value = EXCLUDED.value,
                  data_type = EXCLUDED.data_type,
                  description = EXCLUDED.description,
                  user_id = EXCLUDED.user_id,
                  last_updated = CURRENT_TIMESTAMP";

      $stmt = $this->conn->prepare($query);

      foreach ($settingsData as $key => $data) {
        $stmt->bindValue(':key_name', $key, PDO::PARAM_STR);
        $stmt->bindValue(':value', $data['value'] ?? '', PDO::PARAM_STR);
        $stmt->bindValue(':data_type', $data['data_type'] ?? 'string', PDO::PARAM_STR);
        $stmt->bindValue(':description', $data['description'] ?? '', PDO::PARAM_STR);
        $stmt->bindValue(':user_id', $userId, PDO::PARAM_INT);
        $stmt->execute();
      }

      $this->conn->commit();
      return true;
    } catch (PDOException $e) {
      $this->conn->rollBack();
      error_log("DB Error (updateSettings): " . $e->getMessage());
      return false;
    }
  }

  // Update single setting
  public function updateSetting($key, $value, $userId, $dataType = 'string', $description = '')
  {
    try {
      $query = "INSERT INTO settings (key_name, value, data_type, description, user_id) 
                VALUES (:key_name, :value, :data_type, :description, :user_id)
                ON CONFLICT (key_name) 
                DO UPDATE SET 
                  value = EXCLUDED.value,
                  user_id = EXCLUDED.user_id,
                  last_updated = CURRENT_TIMESTAMP";

      $stmt = $this->conn->prepare($query);
      $stmt->bindValue(':key_name', $key, PDO::PARAM_STR);
      $stmt->bindValue(':value', $value, PDO::PARAM_STR);
      $stmt->bindValue(':data_type', $dataType, PDO::PARAM_STR);
      $stmt->bindValue(':description', $description, PDO::PARAM_STR);
      $stmt->bindValue(':user_id', $userId, PDO::PARAM_INT);

      return $stmt->execute();
    } catch (PDOException $e) {
      error_log("DB Error (updateSetting): " . $e->getMessage());
      return false;
    }
  }

  // Delete setting
  public function deleteSetting($key)
  {
    try {
      $query = "DELETE FROM settings WHERE key_name = :key_name";

      $stmt = $this->conn->prepare($query);
      $stmt->bindValue(':key_name', $key, PDO::PARAM_STR);

      return $stmt->execute();
    } catch (PDOException $e) {
      error_log("DB Error (deleteSetting): " . $e->getMessage());
      return false;
    }
  }

  // Get settings by group (prefix)
  public function getSettingsByGroup($prefix)
  {
    try {
      $query = "SELECT key_name, value, data_type, description 
                FROM settings 
                WHERE key_name LIKE :prefix
                ORDER BY key_name";

      $stmt = $this->conn->prepare($query);
      $stmt->bindValue(':prefix', $prefix . '%', PDO::PARAM_STR);
      $stmt->execute();

      $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

      $settings = [];
      foreach ($results as $row) {
        $settings[$row['key_name']] = [
          'value' => $row['value'],
          'data_type' => $row['data_type'],
          'description' => $row['description']
        ];
      }

      return $settings;
    } catch (PDOException $e) {
      error_log("DB Error (getSettingsByGroup): " . $e->getMessage());
      return [];
    }
  }

  // Get site identity settings
  public function getSiteIdentity()
  {
    return $this->getSettingsByGroup('site_');
  }

  // Get footer settings
  public function getFooterSettings()
  {
    return $this->getSettingsByGroup('footer_');
  }

  // Get contact settings
  public function getContactSettings()
  {
    return $this->getSettingsByGroup('contact_');
  }

  // Get social media settings
  public function getSocialSettings()
  {
    return $this->getSettingsByGroup('social_');
  }
}
