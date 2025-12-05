<?php
require_once __DIR__ . '/ApiBaseController.php';
require_once __DIR__ . '/../../app/models/GalleryModel.php';

/**
 * GalleryApiController - REST API endpoints for gallery management (read-only)
 *
 * Endpoints:
 * - GET /api/gallery - List all gallery items
 * - GET /api/gallery?page=1&per_page=12 - Paginated gallery
 * - GET /api/gallery/{id} - Get gallery item detail
 */
class GalleryApiController extends ApiBaseController
{
  private $galleryModel;

  public function __construct()
  {
    $this->galleryModel = new GalleryModel();
  }

  /**
   * GET /api/gallery
   * List gallery items with pagination
   */
  public function index()
  {
    try {
      $page = $this->getIntParam('page', false) ?? 1;
      $perPage = $this->getIntParam('per_page', false) ?? 12;

      // Validate pagination parameters
      if ($page < 1 || $perPage < 1 || $perPage > 100) {
        throw new Exception('Invalid pagination parameters', 400);
      }

      $offset = ($page - 1) * $perPage;

      // Fetch gallery items
      $items = $this->galleryModel->getGallery($perPage, $offset, '');

      if (!is_array($items)) {
        throw new Exception('Failed to fetch gallery items', 500);
      }

      // Get total count
      $total = $this->galleryModel->countGallery('');

      $response = [
        'items' => $items,
        'pagination' => [
          'page' => $page,
          'per_page' => $perPage,
          'total' => $total,
          'pages' => ceil($total / $perPage)
        ]
      ];

      $this->sendSuccess($response, 'Gallery items retrieved successfully');
    } catch (Exception $e) {
      $code = $e->getCode() ?: 500;
      $this->sendError($e->getMessage(), $code);
    }
  }

  /**
   * GET /api/gallery/{id}
   * Get specific gallery item details
   */
  public function show($id)
  {
    try {
      if (!is_numeric($id) || $id < 1) {
        throw new Exception('Invalid gallery item ID', 400);
      }

      $item = $this->galleryModel->getById((int)$id);

      if (!$item) {
        throw new Exception('Gallery item not found', 404);
      }

      $this->sendSuccess($item, 'Gallery item retrieved successfully');
    } catch (Exception $e) {
      $code = $e->getCode() ?: 500;
      $this->sendError($e->getMessage(), $code);
    }
  }
}
