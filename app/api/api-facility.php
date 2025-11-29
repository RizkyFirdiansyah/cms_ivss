<?php
// Set headers untuk API
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

// Handle preflight request
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
  http_response_code(200);
  exit();
}

try {
  // Include models
  require_once __DIR__ . '/../models/FacilityPageModel.php';
  require_once __DIR__ . '/../models/FacilitiesModel.php';

  // Inisialisasi models
  $facilityPageModel = new FacilityPageModel();
  $facilitiesModel = new FacilitiesModel();

  // Get request method
  $method = $_SERVER['REQUEST_METHOD'];

  // Get query parameters
  $id = isset($_GET['id']) ? (int)$_GET['id'] : null;
  $action = isset($_GET['action']) ? $_GET['action'] : '';
  $limit = isset($_GET['limit']) ? (int)$_GET['limit'] : null;
  $offset = isset($_GET['offset']) ? (int)$_GET['offset'] : 0;
  $search = isset($_GET['search']) ? trim($_GET['search']) : '';

  switch ($method) {
    case 'GET':
      handleGetRequest($facilityPageModel, $facilitiesModel, $action, $id, $limit, $offset, $search);
      break;

    default:
      http_response_code(405);
      echo json_encode([
        'status' => 'error',
        'message' => 'Method not allowed'
      ]);
  }
} catch (Exception $e) {
  error_log("API Error: " . $e->getMessage());
  http_response_code(500);
  echo json_encode([
    'status' => 'error',
    'message' => 'Internal server error',
    'error' => $e->getMessage() // Hanya untuk development
  ]);
}

function handleGetRequest($facilityPageModel, $facilitiesModel, $action, $id, $limit, $offset, $search)
{
  switch ($action) {
    case 'header':
      // Get header content untuk halaman fasilitas
      $header = $facilityPageModel->getHeader();
      http_response_code(200);
      echo json_encode([
        'status' => 'success',
        'data' => $header,
        'message' => 'Facility header retrieved successfully'
      ]);
      break;

    case 'page-content':
      // Get semua konten halaman fasilitas
      $pageContents = $facilityPageModel->getPageContents();
      http_response_code(200);
      echo json_encode([
        'status' => 'success',
        'data' => $pageContents,
        'message' => 'Facility page contents retrieved successfully'
      ]);
      break;

    case 'all':
      // Get header + semua fasilitas
      $header = $facilityPageModel->getHeader();
      $facilities = $facilitiesModel->getAll();

      http_response_code(200);
      echo json_encode([
        'status' => 'success',
        'data' => [
          'header' => $header,
          'facilities' => $facilities
        ],
        'total_facilities' => count($facilities),
        'message' => 'Complete facility page data retrieved successfully'
      ]);
      break;

    case 'paginated':
      // Get header + fasilitas dengan pagination
      $header = $facilityPageModel->getHeader();
      $facilities = $facilitiesModel->getFacility($limit, $offset, $search);
      $total = $facilitiesModel->countFacility($search);

      http_response_code(200);
      echo json_encode([
        'status' => 'success',
        'data' => [
          'header' => $header,
          'facilities' => $facilities
        ],
        'pagination' => [
          'total' => $total,
          'limit' => $limit,
          'offset' => $offset,
          'has_more' => ($offset + $limit) < $total
        ],
        'message' => 'Paginated facility data retrieved successfully'
      ]);
      break;

    default:
      // Default: handle individual facility atau semua facilities
      if ($id) {
        // Get specific facility by ID
        $facility = $facilitiesModel->getById($id);
        if ($facility) {
          http_response_code(200);
          echo json_encode([
            'status' => 'success',
            'data' => $facility,
            'message' => 'Facility retrieved successfully'
          ]);
        } else {
          http_response_code(404);
          echo json_encode([
            'status' => 'error',
            'message' => 'Facility not found'
          ]);
        }
      } elseif ($limit !== null) {
        // Get paginated facilities
        $facilities = $facilitiesModel->getFacility($limit, $offset, $search);
        $total = $facilitiesModel->countFacility($search);

        http_response_code(200);
        echo json_encode([
          'status' => 'success',
          'data' => $facilities,
          'pagination' => [
            'total' => $total,
            'limit' => $limit,
            'offset' => $offset,
            'has_more' => ($offset + $limit) < $total
          ],
          'message' => 'Facilities retrieved successfully'
        ]);
      } else {
        // Get all facilities
        $facilities = $facilitiesModel->getAll();
        http_response_code(200);
        echo json_encode([
          'status' => 'success',
          'data' => $facilities,
          'total' => count($facilities),
          'message' => 'All facilities retrieved successfully'
        ]);
      }
  }
}
