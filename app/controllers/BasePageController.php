<?php
require_once '../app/controllers/BaseController.php';

abstract class BasePageController extends BaseController
{
  protected $conn;
  protected $pageModel;
  protected $uploadDir;

  public function __construct($pageModel, $uploadDir)
  {
    parent::__construct();
    parent::requireLogin();
    parent::requireRole('kepala');

    $db = new Database();
    $this->conn = $db->getConnection();
    $this->pageModel = $pageModel;
    $this->uploadDir = $uploadDir;
  }

  // Common methods untuk semua controller
  public function getContents()
  {
    try {
      $header = $this->pageModel->getHeader();

      $contents = [
        'header' => $header
      ];

      $this->jsonResponse([
        'success' => true,
        'data' => $contents
      ]);
    } catch (Exception $e) {
      error_log("Get Contents Error: " . $e->getMessage());
      $this->jsonResponse([
        'success' => false,
        'message' => 'Gagal mengambil data konten.'
      ], 500);
    }
  }

  protected function handleFileUpload($file, $prefix = '')
  {
    $upload_path_full = __DIR__ . '/../../public/' . $this->uploadDir;

    if (!is_dir($upload_path_full)) {
      mkdir($upload_path_full, 0777, true);
    }

    $allowed_types = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));

    if (!in_array($ext, $allowed_types)) {
      error_log("Invalid file type for {$prefix}: {$ext}");
      return false;
    }

    if ($file['size'] > 5 * 1024 * 1024) {
      error_log("File too large for {$prefix}: {$file['size']} bytes");
      return false;
    }

    $new_file_name = $prefix . '_' . $this->user['id'] . '_' . time() . '.' . $ext;
    $target_path = $upload_path_full . $new_file_name;

    if (move_uploaded_file($file['tmp_name'], $target_path)) {
      return $new_file_name;
    }

    error_log("Failed to move uploaded file for {$prefix}");
    return false;
  }

  protected function deleteFileFromServer($filename)
  {
    if (empty($filename)) {
      return true;
    }

    $file_path = $this->uploadDir . $filename;
    $full_path = __DIR__ . '/../../public/' . $file_path;

    if (file_exists($full_path) && is_file($full_path)) {
      return unlink($full_path);
    }

    return true;
  }

  protected function deleteOldFiles($filesToDelete)
  {
    foreach ($filesToDelete as $filename) {
      $this->deleteFileFromServer($filename);
    }
  }

  protected function rollbackUploadedFiles($uploadedFiles)
  {
    foreach ($uploadedFiles as $filename) {
      if ($filename !== false) {
        $this->deleteFileFromServer($filename);
      }
    }
  }

  // Abstract methods - harus diimplement oleh child class
  abstract public function update();
  abstract protected function handleFileUploads();
}
