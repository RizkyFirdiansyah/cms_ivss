<?php
require_once __DIR__ . '/../models/BasePageModel.php';

class GaleriPageModel extends BasePageModel
{
  public function __construct()
  {
    parent::__construct('gallery', 'Galeri');
  }

  // Get gallery header data (title, subtitle, image)
  public function getGalleryHeader()
  {
    try {
      $contents = $this->getPageContents();

      return [
        'title' => $contents['gallery_header_title']['value'] ?? 'Galeri',
        'subtitle' => $contents['gallery_header_subtitle']['value'] ?? 'Koleksi dokumentasi kegiatan laboratorium',
        'image_path' => $contents['gallery_header_image']['value'] ?? ''
      ];
    } catch (PDOException $e) {
      error_log("DB Error (getGalleryHeader): " . $e->getMessage());
      return [
        'title' => 'Galeri',
        'subtitle' => 'Koleksi dokumentasi kegiatan laboratorium',
        'image_path' => ''
      ];
    }
  }

  // Save gallery header data
  public function saveGalleryHeader($headerData, $userId)
  {
    $contents = [
      'gallery_header_title' => ['type' => 'text', 'value' => $headerData['title'] ?? ''],
      'gallery_header_subtitle' => ['type' => 'text', 'value' => $headerData['subtitle'] ?? ''],
      'gallery_header_image' => ['type' => 'image', 'value' => $headerData['image_path'] ?? '']
    ];

    return $this->saveMultipleContents($contents, $userId);
  }

  // Implement abstract method dari BasePageModel
  public function getHeader()
  {
    return $this->getGalleryHeader();
  }

  public function saveHeader($headerData, $userId)
  {
    return $this->saveGalleryHeader($headerData, $userId);
  }
}
