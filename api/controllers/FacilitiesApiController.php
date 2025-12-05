<?php
require_once __DIR__ . '/ApiBaseController.php';
require_once __DIR__ . '/../../app/models/FacilitiesModel.php';
require_once __DIR__ . '/../../app/models/FacilityPageModel.php';

class FacilitiesApiController extends ApiBaseController
{
  private $facilitiesModel;
  private $facilityPageModel;

  public function __construct()
  {
    // parent::__construct();
    $this->facilitiesModel = new FacilitiesModel();
    $this->facilityPageModel = new FacilityPageModel();
  }

  public function index()
  {
    try {
      // Get all facilities
      $facilities = $this->facilitiesModel->getAll();

      // Get page header
      $header = $this->facilityPageModel->getHeader();

      // Prepare response
      $response = [
        'header' => $header,
        'facilities' => $facilities,
        'total' => count($facilities)
      ];

      return $this->sendSuccess($response, 'Facilities retrieved successfully');
    } catch (Exception $e) {
      return $this->sendError($e->getMessage(), 500);
    }
  }

  public function show($id)
  {
    try {
      // Validate ID
      if (!is_numeric($id) || $id <= 0) {
        throw new Exception('Invalid facility ID', 400);
      }

      // Get facility detail
      $facility = $this->facilitiesModel->getById($id);
      if (!$facility) {
        return $this->sendError("Facility not found", 404);
      }

      // Get page header
      $header = $this->facilityPageModel->getHeader();

      // Prepare response
      $response = [
        'header' => $header,
        'facility' => $facility
      ];

      return $this->sendSuccess($response, 'Facility detail retrieved successfully');
    } catch (Exception $e) {
      return $this->sendError($e->getMessage(), 500);
    }
  }

  public function header()
  {
    try {
      $header = $this->facilityPageModel->getHeader();
      return $this->sendSuccess($header, "Facility page header retrieved successfully");
    } catch (Exception $e) {
      return $this->sendError($e->getMessage(), $e->getCode() ?: 500);
    }
  }
}
