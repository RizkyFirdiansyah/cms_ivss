<?php
require_once __DIR__ . '/ApiBaseController.php';
require_once __DIR__ . '/../../app/models/SopPageModel.php';

class SopApiController extends ApiBaseController
{
  private $sopModel;

  public function __construct()
  {
    // parent::__construct();
    $this->sopModel = new SopPageModel();
  }

  public function index()
  {
    try {
      // Get all SOP data
      $contents = $this->sopModel->getSopContents();
      $sopItems = $this->getSopItems($contents);

      // Prepare structured response
      $response = [
        'header' => [
          'title' => $contents['sop_header_title']['value'] ?? 'Standar Operasional Prosedur (SOP)',
          'subtitle' => $contents['sop_header_subtitle']['value'] ?? 'Panduan lengkap prosedur operasional laboratorium',
          'image_path' => $contents['sop_header_image']['value'] ?? ''
        ],
        'layanan' => [
          'location' => $contents['sop_layanan_location']['value'] ?? '',
          'email' => $contents['sop_layanan_email']['value'] ?? '',
          'hours' => $contents['sop_layanan_hours']['value'] ?? ''
        ],
        'sop_items' => $sopItems,
        'meta' => [
          'generated_at' => date('c'),
          'version' => '1.0'
        ]
      ];

      return $this->sendSuccess($response, 'Complete SOP page data retrieved successfully');
    } catch (Exception $e) {
      return $this->sendError($e->getMessage(), 500);
    }
  }

  public function header()
  {
    try {
      $headerData = $this->sopModel->getHeader();
      return $this->sendSuccess($headerData, 'SOP header retrieved successfully');
    } catch (Exception $e) {
      return $this->sendError($e->getMessage(), 500);
    }
  }

  public function items()
  {
    try {
      $contents = $this->sopModel->getSopContents();
      $sopItems = $this->getSopItems($contents);

      $response = [
        'items' => $sopItems,
        'total' => count($sopItems)
      ];

      return $this->sendSuccess($response, 'SOP items retrieved successfully');
    } catch (Exception $e) {
      return $this->sendError($e->getMessage(), 500);
    }
  }

  // Helper method to extract SOP items
  private function getSopItems($contents)
  {
    $sopItems = [];

    for ($i = 1; $i <= 20; $i++) {
      $title = $contents["sop_{$i}_title"]['value'] ?? '';

      if (!empty($title)) {
        $sopItems[] = [
          'id' => $i,
          'title' => $title,
          'description' => $contents["sop_{$i}_description"]['value'] ?? '',
          'document_link' => $contents["sop_{$i}_document_link"]['value'] ?? '',
        ];
      }
    }

    return $sopItems;
  }
}
