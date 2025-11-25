<?php
require_once '../app/models/SettingsModel.php';
require_once '../app/controllers/BaseController.php';

class SettingsController extends BaseController
{
  private $settings;

  public function __construct()
  {
    parent::__construct();
    parent::requireLogin();

    $this->settings = new SettingsModel();
  }

  public function index()
  {
    $page_title = 'Pengaturan Global';
    $page_breadcrumb = ['System', 'Pengaturan Global'];

    include '../app/views/settings.php';
  }

  // Get all settings
  public function getAll()
  {
    try {
      $settings = $this->settings->getAllSettings();

      $this->jsonResponse([
        'success' => true,
        'data' => $settings
      ]);
    } catch (Exception $e) {
      error_log("Get Settings Error: " . $e->getMessage());
      $this->jsonResponse([
        'success' => false,
        'message' => 'Gagal mengambil data pengaturan.'
      ], 500);
    }
  }

  // Get settings by group
  public function getByGroup()
  {
    $group = $_GET['group'] ?? '';

    if (empty($group)) {
      return $this->jsonResponse([
        'success' => false,
        'message' => 'Group pengaturan tidak valid.'
      ], 400);
    }

    try {
      $settings = $this->settings->getSettingsByGroup($group);

      $this->jsonResponse([
        'success' => true,
        'data' => $settings
      ]);
    } catch (Exception $e) {
      error_log("Get Settings By Group Error: " . $e->getMessage());
      $this->jsonResponse([
        'success' => false,
        'message' => 'Gagal mengambil data pengaturan.'
      ], 500);
    }
  }

  // Update settings
  public function update()
  {
    $user = $this->user;

    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
      return $this->jsonResponse([
        'success' => false,
        'message' => 'Method tidak diizinkan.'
      ], 405);
    }

    // Handle file uploads first - dapatkan file baru dan info file lama
    $uploadResult = $this->handleFileUploads();
    $uploadedFiles = $uploadResult['uploadedFiles'];
    $filesToDelete = $uploadResult['filesToDelete'];

    // Prepare settings data
    $settingsData = [];

    // Site Identity
    if (isset($_POST['site_name'])) {
      $settingsData['site_name'] = [
        'value' => trim($_POST['site_name']),
        'data_type' => 'string',
        'description' => 'Nama website/laboratorium'
      ];
    }

    if (isset($_POST['footer_description'])) {
      $settingsData['footer_description'] = [
        'value' => trim($_POST['footer_description']),
        'data_type' => 'text',
        'description' => 'Deskripsi laboratorium di footer'
      ];
    }

    if (isset($_POST['footer_copyright'])) {
      $settingsData['footer_copyright'] = [
        'value' => trim($_POST['footer_copyright']),
        'data_type' => 'string',
        'description' => 'Teks copyright'
      ];
    }

    // Contact Information
    if (isset($_POST['contact_address'])) {
      $settingsData['contact_address'] = [
        'value' => trim($_POST['contact_address']),
        'data_type' => 'string',
        'description' => 'Alamat lengkap'
      ];
    }

    if (isset($_POST['contact_email'])) {
      $settingsData['contact_email'] = [
        'value' => trim($_POST['contact_email']),
        'data_type' => 'string',
        'description' => 'Email utama'
      ];
    }

    if (isset($_POST['contact_phone'])) {
      $settingsData['contact_phone'] = [
        'value' => trim($_POST['contact_phone']),
        'data_type' => 'string',
        'description' => 'Nomor telepon'
      ];
    }

    // Social Media
    $socialFields = ['facebook', 'instagram', 'twitter', 'linkedin', 'youtube'];
    foreach ($socialFields as $platform) {
      $key = "social_{$platform}";
      if (isset($_POST[$key])) {
        $settingsData[$key] = [
          'value' => trim($_POST[$key]),
          'data_type' => 'string',
          'description' => "URL {$platform}"
        ];
      }
    }

    // Legal Links
    if (isset($_POST['legal_disclaimer'])) {
      $settingsData['legal_disclaimer'] = [
        'value' => trim($_POST['legal_disclaimer']),
        'data_type' => 'string',
        'description' => 'Link disclaimer'
      ];
    }

    if (isset($_POST['legal_privacy_policy'])) {
      $settingsData['legal_privacy_policy'] = [
        'value' => trim($_POST['legal_privacy_policy']),
        'data_type' => 'string',
        'description' => 'Link privacy policy'
      ];
    }

    if (isset($_POST['legal_terms_of_use'])) {
      $settingsData['legal_terms_of_use'] = [
        'value' => trim($_POST['legal_terms_of_use']),
        'data_type' => 'string',
        'description' => 'Link terms of use'
      ];
    }

    // Add uploaded files to settings data
    foreach ($uploadedFiles as $key => $filename) {
      if ($filename !== false) {
        $settingsData[$key] = [
          'value' => $filename,
          'data_type' => 'string',
          'description' => $key === 'site_logo' ? 'Logo utama website' : 'Favicon website'
        ];
      }
    }

    try {
      $success = $this->settings->updateSettings($settingsData, $user['id']);

      if ($success) {
        // Hapus file lama hanya setelah sukses save ke database
        $this->deleteOldFiles($filesToDelete);

        $this->jsonResponse([
          'success' => true,
          'message' => 'Pengaturan berhasil diperbarui.'
        ]);
      } else {
        // Jika gagal save, hapus file yang baru diupload
        $this->rollbackUploadedFiles($uploadedFiles);

        $this->jsonResponse([
          'success' => false,
          'message' => 'Gagal memperbarui pengaturan.'
        ], 500);
      }
    } catch (Exception $e) {
      // Jika ada exception, hapus file yang baru diupload
      $this->rollbackUploadedFiles($uploadedFiles);

      error_log("Update Settings Error: " . $e->getMessage());
      $this->jsonResponse([
        'success' => false,
        'message' => 'Terjadi kesalahan sistem: ' . $e->getMessage()
      ], 500);
    }
  }

  // Handle file uploads for logo and favicon dengan management file lama yang aman
  private function handleFileUploads()
  {
    $uploadedFiles = [];
    $filesToDelete = [];

    // Handle site logo upload
    if (!empty($_FILES['site_logo']['name'])) {
      $logo = $this->handleFileUpload($_FILES['site_logo'], 'logo');
      if ($logo !== false) {
        $uploadedFiles['site_logo'] = $logo;

        // Simpan info file lama untuk dihapus nanti setelah sukses save
        $oldLogo = $this->settings->getSetting('site_logo');
        if ($oldLogo && $oldLogo !== '/assets/settings/logo.png' && strpos($oldLogo, 'default') === false) {
          $filesToDelete[] = [
            'path' => $oldLogo,
            'type' => 'logo'
          ];
        }
      }
    }

    // Handle favicon upload
    if (!empty($_FILES['site_favicon']['name'])) {
      $favicon = $this->handleFileUpload($_FILES['site_favicon'], 'favicon');
      if ($favicon !== false) {
        $uploadedFiles['site_favicon'] = $favicon;

        // Simpan info file lama untuk dihapus nanti setelah sukses save
        $oldFavicon = $this->settings->getSetting('site_favicon');
        if ($oldFavicon && $oldFavicon !== '/assets/settings/favicon.ico' && strpos($oldFavicon, 'default') === false) {
          $filesToDelete[] = [
            'path' => $oldFavicon,
            'type' => 'favicon'
          ];
        }
      }
    }

    return [
      'uploadedFiles' => $uploadedFiles,
      'filesToDelete' => $filesToDelete
    ];
  }

  // Handle single file upload
  private function handleFileUpload($file, $type = 'logo')
  {
    $upload_dir = "uploads/settings/";
    $upload_path_full = __DIR__ . '/../../public/' . $upload_dir;

    // Ensure directory exists
    if (!is_dir($upload_path_full)) {
      mkdir($upload_path_full, 0777, true);
    }

    // Allowed file types
    if ($type === 'logo') {
      $allowed_types = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'svg'];
      $max_size = 2 * 1024 * 1024; // 2MB
    } else { // favicon
      $allowed_types = ['ico', 'png', 'jpg', 'jpeg', 'gif'];
      $max_size = 1 * 1024 * 1024; // 1MB
    }

    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));

    // Validate file type
    if (!in_array($ext, $allowed_types)) {
      error_log("Invalid file type for {$type}: {$ext}");
      return false;
    }

    // Validate file size
    if ($file['size'] > $max_size) {
      error_log("File too large for {$type}: {$file['size']} bytes");
      return false;
    }

    // Generate unique filename
    $new_file_name = "{$type}_" . $this->user['id'] . '_' . time() . '.' . $ext;
    $target_path = $upload_path_full . $new_file_name;

    if (move_uploaded_file($file['tmp_name'], $target_path)) {
      return $new_file_name;
    }

    error_log("Failed to move uploaded file for {$type}");
    return false;
  }

  // Delete old files after successful database update
  private function deleteOldFiles($filesToDelete)
  {
    foreach ($filesToDelete as $fileInfo) {
      $this->deleteFileFromServer($fileInfo['path'], $fileInfo['type']);
    }
  }

  // Rollback uploaded files if operation fails
  private function rollbackUploadedFiles($uploadedFiles)
  {
    foreach ($uploadedFiles as $filename) {
      if ($filename !== false) {
        $this->deleteFileFromServer($filename, 'rollback');
      }
    }
  }

  // Delete file from server
  protected function deleteFileFromServer($filename, $type = 'logo')
  {
    if (empty($filename) || strpos($filename, 'default') !== false) {
      error_log("Delete File: Filename kosong atau default untuk {$type}");
      return true;
    }

    // Tambahkan path directory karena hanya menyimpan nama file
    $file_path = 'uploads/settings/' . $filename;
    $full_path = __DIR__ . '/../../public/' . $file_path;

    error_log("Delete File Attempt: {$type}");
    error_log("Filename: {$filename}");
    error_log("Full Path: {$full_path}");

    // Check if file exists and is a file
    if (file_exists($full_path) && is_file($full_path)) {
      if (unlink($full_path)) {
        error_log("✅ File {$type} berhasil dihapus: " . $full_path);
        return true;
      } else {
        error_log("❌ Gagal menghapus file {$type} (Izin Ditolak): " . $full_path);

        // Cek permissions
        error_log("File Permissions: " . substr(sprintf('%o', fileperms($full_path)), -4));
        error_log("Is Writable: " . (is_writable($full_path) ? 'Yes' : 'No'));

        return false;
      }
    } else {
      error_log("⚠️ File {$type} tidak ditemukan: " . $full_path);

      // Cek apakah directory exists
      $dir = dirname($full_path);
      error_log("Directory exists: " . (is_dir($dir) ? 'Yes' : 'No'));

      // Coba cari file dengan path alternatif
      $alternative_path = __DIR__ . '/../../public/uploads/settings/' . $filename;
      if (file_exists($alternative_path)) {
        error_log("File ditemukan di path alternatif: " . $alternative_path);
        if (unlink($alternative_path)) {
          error_log("✅ File {$type} berhasil dihapus dari path alternatif");
          return true;
        }
      }
    }

    return true;
  }

  // Get specific setting group for frontend
  public function getSiteConfig()
  {
    try {
      $siteIdentity = $this->settings->getSiteIdentity();
      $contactSettings = $this->settings->getContactSettings();
      $socialSettings = $this->settings->getSocialSettings();
      $footerSettings = $this->settings->getFooterSettings();

      $config = [
        'site' => array_column($siteIdentity, 'value', 'key_name'),
        'contact' => array_column($contactSettings, 'value', 'key_name'),
        'social' => array_column($socialSettings, 'value', 'key_name'),
        'footer' => array_column($footerSettings, 'value', 'key_name')
      ];

      $this->jsonResponse([
        'success' => true,
        'data' => $config
      ]);
    } catch (Exception $e) {
      error_log("Get Site Config Error: " . $e->getMessage());
      $this->jsonResponse([
        'success' => false,
        'message' => 'Gagal mengambil konfigurasi situs.'
      ], 500);
    }
  }
}
