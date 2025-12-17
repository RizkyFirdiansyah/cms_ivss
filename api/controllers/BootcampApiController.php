<?php
require_once __DIR__ . '/ApiBaseController.php';
require_once __DIR__ . '/../../app/models/BootcampModel.php';
require_once __DIR__ . '/../../app/models/BootcampPageModel.php';

class BootcampApiController extends ApiBaseController
{
  private $bootcampModel;
  private $bootcampPageModel;

  public function __construct()
  {
    $this->bootcampModel = new BootcampModel();
    $this->bootcampPageModel = new BootcampPageModel();
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

      // Get bootcamps from model
      $items = $this->bootcampModel->getBootcampsForApi($perPage, $offset, $search, $categoryId);
      $total = $this->bootcampModel->countBootcampsForApi($search, $categoryId);

      // Get available years for filter dropdown
      $availableYears = $this->bootcampModel->getAvailableYears();

      // Format items for response
      $formattedItems = array_map(function($item) {
        return [
          'id' => $item['id'] ?? null,
          'name' => $item['name'] ?? '',
          'description' => $item['description'] ?? '',
          'photo' => $item['photo'] ?? null,
          'link' => $item['link'] ?? '',
          'created_at' => $item['created_at'] ?? null,
          'created_at_formatted' => $item['created_at_formatted'] ?? null,
          'created_at_readable' => $item['created_at_readable'] ?? null,
          'categories' => $item['categories'] ?? [],
          'author_name' => $item['author_name'] ?? 'Admin'
        ];
      }, $items);

      // Prepare response
      $response = [
        'items' => $formattedItems,
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
          'total_pages' => $total > 0 ? ceil($total / $perPage) : 0,
          'has_next' => $page < ceil($total / $perPage),
          'has_prev' => $page > 1
        ]
      ];

      return $this->sendSuccess($response, 'Bootcamps retrieved successfully');
    } catch (Exception $e) {
      return $this->sendError($e->getMessage(), $e->getCode() ?: 500);
    }
  }

  public function show($id)
  {
    try {
      // Validate ID
      if (!is_numeric($id) || $id <= 0) {
        throw new Exception('Invalid bootcamp ID', 400);
      }

      // Get bootcamp detail
      $detail = $this->bootcampModel->getById($id);
      if (!$detail) {
        return $this->sendError("Bootcamp not found", 404);
      }

      // Format categories
      $categories = [];
      if (!empty($detail['categories'])) {
        if (is_string($detail['categories'])) {
          $categoryNames = explode(', ', $detail['categories']);
          $categoryIds = $detail['category_ids'] ?? [];
          if (is_string($categoryIds)) {
            $categoryIds = explode(',', $categoryIds);
          }
          
          for ($i = 0; $i < count($categoryNames); $i++) {
            $categories[] = [
              'id' => $categoryIds[$i] ?? null,
              'name' => $categoryNames[$i] ?? ''
            ];
          }
        }
      }

      // Format detail
      $formattedDetail = [
        'id' => $detail['id'],
        'name' => $detail['name'],
        'description' => $detail['description'],
        'photo' => $detail['photo'] ?? null,
        'link' => $detail['link'],
        'created_by_user_id' => $detail['created_by_user_id'],
        'author_name' => $detail['author_name'] ?? 'Admin',
        'created_at' => $detail['created_at'],
        'created_at_formatted' => isset($detail['created_at']) ? date('Y-m-d', strtotime($detail['created_at'])) : null,
        'created_at_readable' => isset($detail['created_at']) ? date('d F Y', strtotime($detail['created_at'])) : null,
        'categories' => $categories
      ];

      // Get recent bootcamps
      $recent = $this->bootcampModel->getRecentBootcampsForApi(5);

      // Format recent bootcamps
      $formattedRecent = array_map(function($item) {
        return [
          'id' => $item['id'] ?? null,
          'name' => $item['name'] ?? '',
          'description' => $item['description'] ?? '',
          'photo' => $item['photo'] ?? null,
          'created_at_formatted' => $item['formatted_date'] ?? null,
          'author_name' => 'Admin'
        ];
      }, $recent);

      // Get related bootcamps (by category)
      $related = [];
      if (!empty($categories)) {
        $firstCategoryId = $categories[0]['id'] ?? null;
        if ($firstCategoryId) {
          $related = $this->bootcampModel->getBootcampsForApi(3, 0, '', $firstCategoryId);
          // Remove current bootcamp from related
          $related = array_filter($related, function ($item) use ($id) {
            return ($item['id'] ?? null) != $id;
          });
          $related = array_slice(array_values($related), 0, 3);
        }
      }

      // Format related bootcamps
      $formattedRelated = array_map(function($item) {
        return [
          'id' => $item['id'] ?? null,
          'name' => $item['name'] ?? '',
          'description' => $item['description'] ?? '',
          'photo' => $item['photo'] ?? null,
          'created_at_formatted' => $item['formatted_date'] ?? null
        ];
      }, $related);

      $response = [
        'detail' => $formattedDetail,
        'recent' => $formattedRecent,
        'related' => $formattedRelated,
      ];

      return $this->sendSuccess($response, 'Bootcamp detail retrieved successfully');
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

      $recent = $this->bootcampModel->getRecentBootcampsForApi($limit);

      // Format recent bootcamps
      $formattedRecent = array_map(function($item) {
        return [
          'id' => $item['id'] ?? null,
          'name' => $item['name'] ?? '',
          'description' => $item['description'] ?? '',
          'photo' => $item['photo'] ?? null,
          'link' => $item['link'] ?? '',
          'created_at' => $item['created_at'] ?? null,
          'created_at_formatted' => $item['formatted_date'] ?? null,
          'created_at_readable' => isset($item['created_at']) ? date('d F Y', strtotime($item['created_at'])) : null,
          'year' => $item['year'] ?? null,
          'month' => $item['month'] ?? null,
          'day' => $item['day'] ?? null,
          'author_name' => 'Admin'
        ];
      }, $recent);

      return $this->sendSuccess($formattedRecent, "Recent bootcamps retrieved successfully");
    } catch (Exception $e) {
      return $this->sendError($e->getMessage(), $e->getCode() ?: 500);
    }
  }

  public function featured()
  {
    try {
      $limit = $this->getIntParam('limit', false) ?? 3;

      // Validate limit
      if ($limit < 1 || $limit > 10) {
        throw new Exception('Limit must be between 1 and 10', 400);
      }

      // Get featured bootcamps (most recent)
      $featured = $this->bootcampModel->getRecentBootcampsForApi($limit);

      // Format featured bootcamps
      $formattedFeatured = array_map(function($item) {
        return [
          'id' => $item['id'] ?? null,
          'name' => $item['name'] ?? '',
          'description' => $item['description'] ?? '',
          'photo' => $item['photo'] ?? null,
          'link' => $item['link'] ?? '',
          'created_at_formatted' => $item['formatted_date'] ?? null,
          'excerpt' => $this->truncateText($item['description'] ?? '', 100)
        ];
      }, $featured);

      return $this->sendSuccess($formattedFeatured, "Featured bootcamps retrieved successfully");
    } catch (Exception $e) {
      return $this->sendError($e->getMessage(), $e->getCode() ?: 500);
    }
  }

  public function filters()
  {
    try {
      $availableYears = $this->bootcampModel->getAvailableYears();

      $response = [
        'years' => $availableYears,
      ];

      return $this->sendSuccess($response, 'Filter options retrieved successfully');
    } catch (Exception $e) {
      return $this->sendError($e->getMessage(), 500);
    }
  }

  public function categories()
  {
    try {
      // This method would typically get categories from CategoryModel
      // For now, we'll return an empty array or implement if needed
      $response = [
        'categories' => []
      ];

      return $this->sendSuccess($response, 'Bootcamp categories retrieved successfully');
    } catch (Exception $e) {
      return $this->sendError($e->getMessage(), 500);
    }
  }

  public function header()
  {
    try {
      $header = $this->bootcampPageModel->getHeader();
      
      // Format header response
      $formattedHeader = [
        'title' => $header['title'] ?? 'Bootcamp',
        'subtitle' => $header['subtitle'] ?? 'Program pelatihan intensif dan workshop',
        'image_path' => $header['image_path'] ?? ''
      ];

      return $this->sendSuccess($formattedHeader, "Bootcamp header retrieved successfully");
    } catch (Exception $e) {
      return $this->sendError($e->getMessage(), $e->getCode() ?: 500);
    }
  }

  public function search()
  {
    try {
      $query = $this->getParam('q') ?? '';
      $limit = $this->getIntParam('limit', false) ?? 10;

      if (empty($query)) {
        return $this->sendSuccess([], 'No search query provided');
      }

      // Validate limit
      if ($limit < 1 || $limit > 50) {
        throw new Exception('Limit must be between 1 and 50', 400);
      }

      $results = $this->bootcampModel->getBootcampsForApi($limit, 0, $query);

      // Format search results
      $formattedResults = array_map(function($item) {
        return [
          'id' => $item['id'] ?? null,
          'name' => $item['name'] ?? '',
          'description' => $this->truncateText($item['description'] ?? '', 150),
          'photo' => $item['photo'] ?? null,
          'link' => $item['link'] ?? '',
          'created_at_formatted' => $item['formatted_date'] ?? null,
          'type' => 'bootcamp'
        ];
      }, $results);

      $response = [
        'query' => $query,
        'results' => $formattedResults,
        'total' => count($formattedResults)
      ];

      return $this->sendSuccess($response, 'Search completed successfully');
    } catch (Exception $e) {
      return $this->sendError($e->getMessage(), $e->getCode() ?: 500);
    }
  }

  public function stats()
  {
    try {
      // Get total count
      $total = $this->bootcampModel->countBootcampsForApi();

      // Get count by year
      $availableYears = $this->bootcampModel->getAvailableYears();
      $byYear = [];
      
      foreach ($availableYears as $year) {
        $count = $this->bootcampModel->countBootcampsForApi('', null);
        // Note: You may need to implement countByYear method in BootcampModel
        $byYear[] = [
          'year' => $year,
          'count' => $count
        ];
      }

      $response = [
        'total_bootcamps' => $total,
        'by_year' => $byYear,
        'last_updated' => date('Y-m-d H:i:s')
      ];

      return $this->sendSuccess($response, 'Bootcamp statistics retrieved successfully');
    } catch (Exception $e) {
      return $this->sendError($e->getMessage(), 500);
    }
  }

  // Helper method to truncate text
  private function truncateText($text, $length = 100)
  {
    if (strlen($text) <= $length) {
      return $text;
    }
    
    $truncated = substr($text, 0, $length);
    $truncated = substr($truncated, 0, strrpos($truncated, ' '));
    
    return $truncated . '...';
  }
}