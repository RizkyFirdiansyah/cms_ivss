<?php
require_once __DIR__ . '/ApiBaseController.php';
require_once __DIR__ . '/../../app/models/GalleryModel.php';
require_once __DIR__ . '/../../app/models/GalleryPageModel.php';

class GalleryApiController extends ApiBaseController
{
  private $galleryModel;
  private $galleryPageModel;

  public function __construct()
  {
    $this->galleryModel = new GalleryModel();
    $this->galleryPageModel = new GalleryPageModel();
  }

  public function index()
  {
    try {
      // Get all gallery items
      $items = $this->galleryModel->getAll();

      // Get page header
      $header = $this->galleryPageModel->getHeader();

      // Prepare response
      $response = [
        'header' => $header,
        'items' => $items,
        'total' => count($items)
      ];

      return $this->sendSuccess($response, 'Gallery items retrieved successfully');
    } catch (Exception $e) {
      return $this->sendError($e->getMessage(), 500);
    }
  }

  public function show($id)
  {
    try {
      // Validate ID
      if (!is_numeric($id) || $id <= 0) {
        throw new Exception('Invalid gallery item ID', 400);
      }

      // Get gallery item detail
      $item = $this->galleryModel->getById($id);
      if (!$item) {
        return $this->sendError("Gallery item not found", 404);
      }

      // Get page header
      $header = $this->galleryPageModel->getHeader();

      // Prepare response
      $response = [
        'header' => $header,
        'item' => $item
      ];

      return $this->sendSuccess($response, 'Gallery item detail retrieved successfully');
    } catch (Exception $e) {
      return $this->sendError($e->getMessage(), 500);
    }
  }

  public function header()
  {
    try {
      $header = $this->galleryPageModel->getHeader();
      return $this->sendSuccess($header, "Gallery page header retrieved successfully");
    } catch (Exception $e) {
      return $this->sendError($e->getMessage(), $e->getCode() ?: 500);
    }
  }
}
