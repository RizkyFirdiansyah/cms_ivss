<?php
require_once __DIR__ . '/../models/BasePageModel.php';

class FacilityPageModel extends BasePageModel
{
  public function __construct()
  {
    parent::__construct('facility', 'Fasilitas');
  }

  // Get facility header data (title, subtitle, image)
  public function getFacilityHeader()
  {
    try {
      $contents = $this->getPageContents();

      return [
        'title' => $contents['facility_header_title']['value'] ?? 'Fasilitas',
        'subtitle' => $contents['facility_header_subtitle']['value'] ?? 'Fasilitas laboratorium yang tersedia',
        'image_path' => $contents['facility_header_image']['value'] ?? ''
      ];
    } catch (PDOException $e) {
      error_log("DB Error (getFacilityHeader): " . $e->getMessage());
      return [
        'title' => 'Fasilitas',
        'subtitle' => 'Fasilitas laboratorium yang tersedia',
        'image_path' => ''
      ];
    }
  }

  // Save facility header data
  public function saveFacilityHeader($headerData, $userId)
  {
    $contents = [
      'facility_header_title' => ['type' => 'text', 'value' => $headerData['title'] ?? ''],
      'facility_header_subtitle' => ['type' => 'text', 'value' => $headerData['subtitle'] ?? ''],
      'facility_header_image' => ['type' => 'image', 'value' => $headerData['image_path'] ?? '']
    ];

    return $this->saveMultipleContents($contents, $userId);
  }

  // Implement abstract method dari BasePageModel
  public function getHeader()
  {
    return $this->getFacilityHeader();
  }

  public function saveHeader($headerData, $userId)
  {
    return $this->saveFacilityHeader($headerData, $userId);
  }
}
