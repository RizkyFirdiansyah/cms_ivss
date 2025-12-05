<?php
require_once __DIR__ . '/ApiBaseController.php';
require_once __DIR__ . '/../../app/models/UserModel.php';
require_once __DIR__ . '/../../app/models/ProfileModel.php';
require_once __DIR__ . '/../../app/models/MemberPageModel.php';

class UsersApiController extends ApiBaseController
{
  private $userModel;
  private $profileModel;
  private $memberPageModel;

  public function __construct()
  {
    // parent::__construct();
    $this->userModel = new UserModel();
    $this->profileModel = new ProfileModel();
    $this->memberPageModel = new MemberPageModel();
  }

  public function index()
  {
    try {
      // Get all members with social media data
      $members = $this->userModel->getAllUsersWithSosmed();

      // Get page header
      $header = $this->memberPageModel->getHeader();

      // Prepare response
      $response = [
        'header' => $header,
        'members' => $members,
        'total' => count($members)
      ];

      return $this->sendSuccess($response, 'Members retrieved successfully');
    } catch (Exception $e) {
      return $this->sendError($e->getMessage(), 500);
    }
  }

  public function show($id)
  {
    try {
      // Validate ID
      if (!is_numeric($id) || $id <= 0) {
        throw new Exception('Invalid member ID', 400);
      }

      // Get member detail
      $member = $this->profileModel->getProfileById($id);

      // Get page header
      $header = $this->memberPageModel->getHeader();

      // Prepare response
      $response = [
        'header' => $header,
        'member' => $member
      ];

      return $this->sendSuccess($response, 'Member detail retrieved successfully');
    } catch (Exception $e) {
      return $this->sendError($e->getMessage(), 500);
    }
  }

  public function header()
  {
    try {
      $header = $this->memberPageModel->getHeader();
      return $this->sendSuccess($header, "Member page header retrieved successfully");
    } catch (Exception $e) {
      return $this->sendError($e->getMessage(), $e->getCode() ?: 500);
    }
  }

  public function active()
  {
    try {
      // Get all active members (you might need to add filter logic)
      $members = $this->userModel->getAllUsersWithSosmed();

      // Filter active members if needed (assuming there's an 'is_active' field)
      $activeMembers = array_filter($members, function ($member) {
        return isset($member['is_active']) ? $member['is_active'] == 1 : true;
      });

      // Re-index array
      $activeMembers = array_values($activeMembers);

      // Get page header
      $header = $this->memberPageModel->getHeader();

      // Prepare response
      $response = [
        'header' => $header,
        'members' => $activeMembers,
        'total' => count($activeMembers)
      ];

      return $this->sendSuccess($response, 'Active members retrieved successfully');
    } catch (Exception $e) {
      return $this->sendError($e->getMessage(), 500);
    }
  }
}
