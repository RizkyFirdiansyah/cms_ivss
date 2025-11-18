<?php
require_once __DIR__ . '/../config/config.php';

class Database
{
 public $conn;

 public function getConnection()
 {
  $this->conn = null;

  try {
   $dsn = "pgsql:host=" . DB_HOST . ";port=" . DB_PORT . ";dbname=" . DB_NAME;
   $this->conn = new PDO($dsn, DB_USER, DB_PASS);
   $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
  } catch (PDOException $exception) {
   echo "Error koneksi database: " . $exception->getMessage();
  }

  return $this->conn;
 }
}
