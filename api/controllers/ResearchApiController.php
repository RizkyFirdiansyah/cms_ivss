<?php
require_once __DIR__ . '/ApiBaseController.php';
require_once __DIR__ . '/../../app/models/ResearchModel.php';
require_once __DIR__ . '/../../app/models/ResearchPageModel.php';

class ResearchApiController extends ApiBaseController
{
  private $researchModel;
  private $researchPageModel;

  public function __construct()
  {
    $this->researchModel = new ResearchModel();
    $this->researchPageModel = new ResearchPageModel();
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

      // Perbaikan 1: Panggil method yang benar
      $items = $this->researchModel->getResearchForApi($perPage, $offset, $search, $categoryId, null, $year);
      $total = $this->researchModel->countResearchForApi($search, $categoryId, null, $year);

      // Get available years for filter dropdown
      $availableYears = $this->researchModel->getAvailableYears();

      // Prepare response
      $response = [
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

      return $this->sendSuccess($response, 'Research retrieved successfully');
    } catch (Exception $e) {
      return $this->sendError($e->getMessage(), $e->getCode() ?: 500);
    }
  }

  public function show($id)
  {
    try {
      // Validate ID
      if (!is_numeric($id) || $id <= 0) {
        throw new Exception('Invalid research ID', 400);
      }

      // Perbaikan 2: Panggil method getResearchByIdForApi, bukan getById
      $detail = $this->researchModel->getById($id);
      if (!$detail) {
        return $this->sendError("Research not found", 404);
      }

      // Get recent research
      $recent = $this->researchModel->getRecentResearchForApi(5);

      // Get related research (by category)
      $related = [];
      if (isset($detail['categories']) && !empty($detail['categories'])) {
        $firstCategoryId = $detail['categories'][0]['id'] ?? null;
        if ($firstCategoryId) {
          $related = $this->researchModel->getResearchForApi(3, 0, '', $firstCategoryId, null, null);
          // Remove current research from related
          $related = array_filter($related, function ($item) use ($id) {
            return $item['research_id'] != $id;
          });
          $related = array_slice(array_values($related), 0, 3);
        }
      }

      $response = [
        'detail' => $detail,
        'recent' => $recent,
        'related' => $related,
      ];

      return $this->sendSuccess($response, 'Research detail retrieved successfully');
    } catch (Exception $e) {
      return $this->sendError($e->getMessage(), 500);
    }
  }

  public function recent()
  {
    try {
      $limit = $this->getIntParam('limit', false) ?? 6;

      // Validate limit
      if ($limit < 1 || $limit > 20) {
        throw new Exception('Limit must be between 1 and 20', 400);
      }

      $recent = $this->researchModel->getRecentResearchForApi($limit);

      return $this->sendSuccess($recent, "Recent research retrieved successfully");
    } catch (Exception $e) {
      return $this->sendError($e->getMessage(), $e->getCode() ?: 500);
    }
  }

  public function filters()
  {
    try {
      $availableYears = $this->researchModel->getAvailableYears();

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
      $header = $this->researchPageModel->getHeader();
      return $this->sendSuccess($header, "Research header retrieved successfully");
    } catch (Exception $e) {
      return $this->sendError($e->getMessage(), $e->getCode() ?: 500);
    }
  }
}
