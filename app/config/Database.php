<?php
require_once __DIR__ . '/../config/config.php';

class Database
{
  private $conn;

  public function __construct()
  {
    $this->connect();
  }

  private function connect()
  {
    try {
      $dsn = "pgsql:host=" . DB_HOST . ";port=" . DB_PORT . ";dbname=" . DB_NAME;
      $this->conn = new PDO($dsn, DB_USER, DB_PASS);
      $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
      $this->conn->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
      error_log("Database Connection Error: " . $e->getMessage());
      throw new Exception("Database connection failed. Please check configuration.");
    }
  }

  public function getConnection()
  {
    return $this->conn;
  }

  public function isConnected()
  {
    return $this->conn !== null;
  }
}
