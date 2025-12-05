<?php
require_once __DIR__ . '/ApiBaseController.php';
require_once __DIR__ . '/../../app/models/PublicationsModel.php';
require_once __DIR__ . '/../../app/models/PublicationPageModel.php';

class PublicationsApiController extends ApiBaseController
{
  private $publicationsModel;
  private $publicationPageModel;

  public function __construct()
  {
    // parent::__construct();
    $this->publicationsModel = new PublicationsModel();
    $this->publicationPageModel = new PublicationPageModel();
  }

  public function index()
  {
    try {
      // Get pagination parameters
      $page = $this->getIntParam('page', false) ?? 1;
      $perPage = $this->getIntParam('per_page', false) ?? 10;

      // Get filter parameters
      $search = $this->getParam('search') ?? '';
      $year = $this->getIntParam('year', false);
      $categoryId = $this->getIntParam('category_id', false);

      // Validate pagination
      if ($page < 1) {
        throw new Exception('Page must be greater than 0', 400);
      }

      if ($perPage < 1 || $perPage > 100) {
        throw new Exception('Per page must be between 1 and 100', 400);
      }

      // Calculate offset
      $offset = ($page - 1) * $perPage;

      // Get data with filters
      $items = $this->publicationsModel->getPublicationsForApi($perPage, $offset, $search, $year, $categoryId);
      $total = $this->publicationsModel->countPublicationsForApi($search, $year, $categoryId);

      // Get header
      $header = $this->publicationPageModel->getHeader();

      // Get available years for filter dropdown
      $availableYears = $this->publicationsModel->getAvailableYears();

      // Prepare response
      $response = [
        'header' => $header,
        'items' => $items,
        'filters' => [
          'available_years' => $availableYears,
          'current_year' => $year,
          'current_category' => $categoryId,
          'current_search' => $search
        ],
        'pagination' => [
          'page' => $page,
          'per_page' => $perPage,
          'total' => $total,
          'total_pages' => ceil($total / $perPage),
          'has_next' => ($page < ceil($total / $perPage)),
          'has_prev' => ($page > 1)
        ]
      ];

      return $this->sendSuccess($response, 'Publications retrieved successfully');
    } catch (Exception $e) {
      return $this->sendError($e->getMessage(), $e->getCode() ?: 500);
    }
  }

  public function filters()
  {
    try {
      $availableYears = $this->publicationsModel->getAvailableYears();

      $response = [
        'years' => $availableYears,
      ];

      return $this->sendSuccess($response, 'Filter options retrieved successfully');
    } catch (Exception $e) {
      return $this->sendError($e->getMessage(), 500);
    }
  }

  public function header()
  {
    try {
      $header = $this->publicationPageModel->getHeader();
      return $this->sendSuccess($header, "Publications header retrieved successfully");
    } catch (Exception $e) {
      return $this->sendError($e->getMessage(), $e->getCode() ?: 500);
    }
  }
}
