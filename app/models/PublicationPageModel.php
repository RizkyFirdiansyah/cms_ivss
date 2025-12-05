<?php
require_once __DIR__ . '/../models/BasePageModel.php';

class PublicationPageModel extends BasePageModel
{
  public function __construct()
  {
    parent::__construct('publication', 'Publikasi');
  }

  // Get all publication page contents
  public function getPublicationPageContents()
  {
    return $this->getPageContents();
  }

  // Save multiple publication page contents
  public function saveMultiplePublicationPageContents($contents, $userId)
  {
    return $this->saveMultipleContents($contents, $userId);
  }

  // Get publication page header data
  public function getPublicationPageHeader()
  {
    try {
      $contents = $this->getPageContents();

      return [
        'title' => $contents['publication_header_title']['value'] ?? 'Publikasi',
        'subtitle' => $contents['publication_header_subtitle']['value'] ?? 'Karya ilmiah dan hasil penelitian LAB IVSS',
        'image_path' => $contents['publication_header_image']['value'] ?? ''
      ];
    } catch (PDOException $e) {
      error_log("DB Error (getPublicationPageHeader): " . $e->getMessage());
      return [
        'title' => 'Publikasi',
        'subtitle' => 'Karya ilmiah dan hasil penelitian LAB IVSS',
        'image_path' => ''
      ];
    }
  }

  // Implement abstract method dari BasePageModel
  public function getHeader()
  {
    return $this->getPublicationPageHeader();
  }

  public function saveHeader($headerData, $userId)
  {
    $contents = [
      'publication_header_title' => ['type' => 'text', 'value' => $headerData['title'] ?? ''],
      'publication_header_subtitle' => ['type' => 'text', 'value' => $headerData['subtitle'] ?? ''],
      'publication_header_image' => ['type' => 'image', 'value' => $headerData['image_path'] ?? '']
    ];

    return $this->saveMultipleContents($contents, $userId);
  }
}
