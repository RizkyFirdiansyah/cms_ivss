<?php
// Set headers untuk API
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET');
header('Access-Control-Allow-Headers: Content-Type');

// Handle preflight request
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
  http_response_code(200);
  exit();
}

try {
  // Include model - path sudah terbukti benar dari test
  require_once __DIR__ . '/../models/FacilitiesModel.php';

  // Inisialisasi model
  $facilityModel = new FacilitiesModel();

  // Hanya handle GET request
  if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    http_response_code(405);
    echo json_encode([
      'status' => 'error',
      'message' => 'Method not allowed. Only GET requests are accepted.'
    ]);
    exit();
  }

  // Get query parameters
  $id = isset($_GET['id']) ? (int)$_GET['id'] : null;
  $limit = isset($_GET['limit']) ? (int)$_GET['limit'] : null;
  $offset = isset($_GET['offset']) ? (int)$_GET['offset'] : 0;
  $search = isset($_GET['search']) ? trim($_GET['search']) : '';

  $response = [];

  // Get specific facility by ID
  if ($id) {
    $facility = $facilityModel->getById($id);

    if ($facility) {
      http_response_code(200);
      $response = [
        'status' => 'success',
        'data' => $facility,
        'message' => 'Facility retrieved successfully'
      ];
    } else {
      http_response_code(404);
      $response = [
        'status' => 'error',
        'message' => 'Facility not found'
      ];
    }
  }
  // Get paginated facilities
  elseif ($limit !== null) {
    $facilities = $facilityModel->getFacility($limit, $offset, $search);
    $total = $facilityModel->countFacility($search);

    http_response_code(200);
    $response = [
      'status' => 'success',
      'data' => $facilities,
      'pagination' => [
        'total' => $total,
        'limit' => $limit,
        'offset' => $offset,
        'has_more' => ($offset + $limit) < $total
      ],
      'message' => 'Facilities retrieved successfully'
    ];
  }
  // Get all facilities (default)
  else {
    $facilities = $facilityModel->getAll();

    http_response_code(200);
    $response = [
      'status' => 'success',
      'data' => $facilities,
      'total' => count($facilities),
      'message' => 'All facilities retrieved successfully'
    ];
  }

  // Send JSON response
  echo json_encode($response, JSON_PRETTY_PRINT);
} catch (Exception $e) {
  // Log error untuk debugging
  error_log("API Error: " . $e->getMessage());

  http_response_code(500);
  echo json_encode([
    'status' => 'error',
    'message' => 'Internal server error',
    'error' => $e->getMessage() // Hanya untuk development
  ]);
}
