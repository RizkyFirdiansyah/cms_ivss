<?php
require_once __DIR__ . '/../models/BasePageModel.php';

class SopPageModel extends BasePageModel
{
  public function __construct()
  {
    parent::__construct('sop', 'Standard Operating Procedure');
  }

  // Tetap pertahankan method khusus SOP
  public function getSopHeader()
  {
    try {
      $contents = $this->getPageContents();

      return [
        'title' => $contents['sop_header_title']['value'] ?? 'Standard Operating Procedure',
        'subtitle' => $contents['sop_header_subtitle']['value'] ?? '',
        'image_path' => $contents['sop_header_image']['value'] ?? ''
      ];
    } catch (PDOException $e) {
      error_log("DB Error (getSopHeader): " . $e->getMessage());
      return [
        'title' => 'Standard Operating Procedure',
        'subtitle' => '',
        'image_path' => ''
      ];
    }
  }

  public function getSopMainContent()
  {
    try {
      $contents = $this->getPageContents();

      return [
        'title' => $contents['sop_main_title']['value'] ?? '',
        'content' => $contents['sop_main_content']['value'] ?? '',
        'image_path' => $contents['sop_main_image']['value'] ?? ''
      ];
    } catch (PDOException $e) {
      error_log("DB Error (getSopMainContent): " . $e->getMessage());
      return [
        'title' => '',
        'content' => '',
        'image_path' => ''
      ];
    }
  }

  public function saveAllSopContents($headerData, $mainData, $userId)
  {
    $contents = [
      // Header section
      'sop_header_title' => ['type' => 'text', 'value' => $headerData['title'] ?? ''],
      'sop_header_subtitle' => ['type' => 'text', 'value' => $headerData['subtitle'] ?? ''],
      'sop_header_image' => ['type' => 'image', 'value' => $headerData['image_path'] ?? ''],

      // Main content section
      'sop_main_title' => ['type' => 'text', 'value' => $mainData['title'] ?? ''],
      'sop_main_content' => ['type' => 'text', 'value' => $mainData['content'] ?? ''],
      'sop_main_image' => ['type' => 'image', 'value' => $mainData['image_path'] ?? '']
    ];

    return $this->saveMultipleContents($contents, $userId);
  }

  // Implement abstract method dari BasePageModel
  public function getHeader()
  {
    return $this->getSopHeader();
  }

  public function saveHeader($headerData, $userId)
  {
    // Untuk kompatibilitas dengan base controller sederhana
    $contents = [
      'sop_header_title' => ['type' => 'text', 'value' => $headerData['title'] ?? ''],
      'sop_header_subtitle' => ['type' => 'text', 'value' => $headerData['subtitle'] ?? ''],
      'sop_header_image' => ['type' => 'image', 'value' => $headerData['image_path'] ?? '']
    ];

    return $this->saveMultipleContents($contents, $userId);
  }
}
