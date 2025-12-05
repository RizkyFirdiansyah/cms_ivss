<?php
require_once __DIR__ . '/ApiBaseController.php';
require_once __DIR__ . '/../../app/models/ContactPageModel.php';
require_once __DIR__ . '/../../app/models/FeedbackModel.php';

class ContactApiController extends ApiBaseController
{
  private $contactPageModel;
  private $feedbackModel;

  public function __construct()
  {
    // parent::__construct();
    $this->contactPageModel = new ContactPageModel();
    $this->feedbackModel = new FeedbackModel();
  }

  // GET: Get contact page header
  public function index()
  {
    try {
      // Get contact page header
      $header = $this->contactPageModel->getHeader();

      // Prepare response
      $response = [
        'header' => $header,
        'meta' => [
          'generated_at' => date('c'),
          'version' => '1.0'
        ]
      ];

      return $this->sendSuccess($response, 'Contact page header retrieved successfully');
    } catch (Exception $e) {
      return $this->sendError($e->getMessage(), 500);
    }
  }

  // GET: Get header only (alias untuk index)
  public function header()
  {
    try {
      $header = $this->contactPageModel->getHeader();
      return $this->sendSuccess($header, "Contact page header retrieved successfully");
    } catch (Exception $e) {
      return $this->sendError($e->getMessage(), $e->getCode() ?: 500);
    }
  }

  // POST: Submit feedback
  // POST: Submit feedback
  public function submit()
  {
    // Check if it's POST request
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
      return $this->sendError('Method not allowed. Only POST requests are accepted.', 405);
    }

    try {
      // Debug: Log all POST data
      error_log("Full POST data: " . print_r($_POST, true));

      // Get and validate input directly from $_POST
      $name = $_POST['name'] ?? '';
      $email = $_POST['email'] ?? '';
      $content = $_POST['content'] ?? '';

      // Debug: Log individual values
      error_log("Name: $name, Email: $email, Content: $content");

      // Basic validation
      if (empty($name)) {
        throw new Exception('Name is required', 400);
      }

      if (empty($email)) {
        throw new Exception('Email is required', 400);
      }

      if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        throw new Exception('Please enter a valid email address', 400);
      }

      if (empty($content)) {
        throw new Exception('Message content is required', 400);
      }

      // Trim and sanitize
      $name = trim($name);
      $email = trim($email);
      $content = trim($content);

      // Validate length
      if (strlen($name) > 100) {
        throw new Exception('Name cannot exceed 100 characters', 400);
      }

      if (strlen($email) > 100) {
        throw new Exception('Email cannot exceed 100 characters', 400);
      }

      // Prepare feedback data
      $feedbackData = [
        'name' => $name,
        'email' => $email,
        'content' => $content
      ];

      // Insert feedback
      $result = $this->feedbackModel->insertFeedback($feedbackData);

      if ($result) {
        return $this->sendSuccess([], 'Thank you for your feedback!');
      } else {
        return $this->sendError('Failed to submit feedback. Please try again later.', 500);
      }
    } catch (Exception $e) {
      return $this->sendError($e->getMessage(), $e->getCode() ?: 500);
    }
  }
}
