<?php

/**
 * ApiBaseController - Base controller for all read-only REST API endpoints
 *
 * Provides standardized JSON response handling and error management
 * All API responses follow the structure: { success: bool, data: any, message: string }
 */
class ApiBaseController
{
  /**
   * Send successful JSON response
   *
   * @param mixed $data Response data payload
   * @param string $message Optional message (default: "OK")
   * @param int $statusCode HTTP status code (default: 200)
   */
  protected function sendSuccess($data = null, $message = 'OK', $statusCode = 200)
  {
    $response = [
      'success' => true,
      'data' => $data,
      'message' => $message
    ];

    http_response_code($statusCode);
    header('Content-Type: application/json');
    echo json_encode($response, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);
    exit();
  }

  /**
   * Send error JSON response
   *
   * @param string $message Error message
   * @param int $statusCode HTTP status code (default: 500)
   * @param mixed $data Optional additional data
   */
  protected function sendError($message, $statusCode = 500, $data = null)
  {
    $response = [
      'success' => false,
      'data' => $data,
      'message' => $message
    ];

    http_response_code($statusCode);
    header('Content-Type: application/json');
    echo json_encode($response, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);
    exit();
  }

  /**
   * Validate required GET parameter
   *
   * @param string $key Parameter name
   * @return mixed Parameter value
   * @throws Exception If parameter is missing or invalid
   */
  protected function getRequiredParam($key)
  {
    if (!isset($_GET[$key]) || trim($_GET[$key]) === '') {
      throw new Exception("Required parameter '{$key}' is missing", 400);
    }
    return trim($_GET[$key]);
  }

  /**
   * Get optional GET parameter with default value
   *
   * @param string $key Parameter name
   * @param mixed $default Default value if not provided
   * @return mixed Parameter value or default
   */
  protected function getParam($key, $default = null)
  {
    return isset($_GET[$key]) ? trim($_GET[$key]) : $default;
  }

  /**
   * Validate and convert parameter to integer
   *
   * @param string $key Parameter name
   * @param bool $required Whether parameter is required
   * @return int|null Validated integer or null
   * @throws Exception If parameter is not a valid integer
   */
  protected function getIntParam($key, $required = false)
  {
    $value = $this->getParam($key);

    if ($value === null) {
      if ($required) {
        throw new Exception("Required parameter '{$key}' is missing", 400);
      }
      return null;
    }

    if (!ctype_digit((string)$value)) {
      throw new Exception("Parameter '{$key}' must be a valid integer", 400);
    }

    return (int)$value;
  }

  protected function getBoolParam($key, $required = false)
  {
    $value = $this->getParam($key);

    if ($value === null) {
      if ($required) {
        throw new Exception("Required parameter '{$key}' is missing", 400);
      }
      return null;
    }

    $lowerValue = strtolower($value);
    if (in_array($lowerValue, ['1', 'true', 'yes'], true)) {
      return true;
    } elseif (in_array($lowerValue, ['0', 'false', 'no'], true)) {
      return false;
    } else {
      throw new Exception("Parameter '{$key}' must be a valid boolean", 400);
    }
  }

  /**
   * Log error message
   *
   * @param string $message Error message
   * @param string $context Optional context for debugging
   */
  protected function logError($message, $context = 'API Error')
  {
    error_log("[{$context}] {$message}");
  }

  /**
   * Format database exception to user-friendly message
   *
   * @param Exception $e Exception object
   * @return string Formatted message
   */
  protected function formatException($e)
  {
    $this->logError($e->getMessage());
    return 'An error occurred while processing your request. Please try again later.';
  }
}
