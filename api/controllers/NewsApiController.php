<?php
require_once __DIR__ . '/ApiBaseController.php';
require_once __DIR__ . '/../../app/models/NewsModel.php';
require_once __DIR__ . '/../../app/models/NewsPageModel.php';

class NewsApiController extends ApiBaseController
{
  private $newsModel;
  private $newsPageModel;

  public function __construct()
  {
    $this->newsModel = new NewsModel();
    $this->newsPageModel = new NewsPageModel();
  }

  public function index()
  {
    try {
      // Get pagination parameters
      $page = $this->getIntParam('page', false) ?? 1;
      $perPage = $this->getIntParam('per_page', false) ?? 10;

      // Get filter parameters
      $categoryId = $this->getIntParam('category_id', false);
      $search = $this->getParam('search') ?? '';
      $year = $this->getIntParam('year', false);

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
      $items = $this->newsModel->getNewsForApi($perPage, $offset, $search, $categoryId, $year);
      $total = $this->newsModel->countNewsForApi($search, $categoryId, $year);

      // Get header
      $header = $this->newsPageModel->getHeader();

      // Get available years for filter dropdown
      $availableYears = $this->newsModel->getAvailableYears();

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

      return $this->sendSuccess($response, 'News retrieved successfully');
    } catch (Exception $e) {
      return $this->sendError($e->getMessage(), $e->getCode() ?: 500);
    }
  }

  public function show($id)
  {
    try {
      // Validate ID
      if (!is_numeric($id) || $id <= 0) {
        throw new Exception('Invalid news ID', 400);
      }

      // Get detail
      $detail = $this->newsModel->getById($id);
      if (!$detail) {
        return $this->sendError("News not found", 404);
      }

      // Get header
      $header = $this->newsPageModel->getHeader();

      // Get recent news
      $recent = $this->newsModel->getRecentNewsForApi(5);
      $response = [
        'header' => $header,
        'detail' => $detail,
        'recent' => $recent,
      ];

      return $this->sendSuccess($response, 'News detail retrieved successfully');
    } catch (Exception $e) {
      return $this->sendError($e->getMessage(), 500);
    }
  }

  public function filters()
  {
    try {
      $availableYears = $this->newsModel->getAvailableYears();

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
      $header = $this->newsPageModel->getHeader();
      return $this->sendSuccess($header, "News header retrieved successfully");
    } catch (Exception $e) {
      return $this->sendError($e->getMessage(), $e->getCode() ?: 500);
    }
  }
}
