<?php
require_once __DIR__ . '/ApiBaseController.php';
require_once __DIR__ . '/../../app/models/SettingsPageModel.php';

class SettingsApiController extends ApiBaseController
{
  private $settingsModel;

  public function __construct()
  {
    // parent::__construct();
    $this->settingsModel = new SettingsPageModel();
  }

  public function index()
  {
    try {
      // Get all settings
      $allSettings = $this->settingsModel->getAllSettings();

      // Structure settings into groups
      $structuredData = $this->structureSettings($allSettings);

      $response = [
        'settings' => $structuredData,
        'meta' => [
          'generated_at' => date('c'),
          'version' => '1.0',
          'total_keys' => count($allSettings)
        ]
      ];

      return $this->sendSuccess($response, 'All settings retrieved successfully');
    } catch (Exception $e) {
      return $this->sendError($e->getMessage(), 500);
    }
  }

  public function site()
  {
    try {
      // Get site identity settings
      $siteSettings = $this->settingsModel->getSiteIdentity();

      $response = $this->extractValues($siteSettings);

      return $this->sendSuccess($response, 'Site identity settings retrieved successfully');
    } catch (Exception $e) {
      return $this->sendError($e->getMessage(), 500);
    }
  }

  public function footer()
  {
    try {
      // Get footer settings
      $footerSettings = $this->settingsModel->getFooterSettings();

      $response = $this->extractValues($footerSettings);

      return $this->sendSuccess($response, 'Footer settings retrieved successfully');
    } catch (Exception $e) {
      return $this->sendError($e->getMessage(), 500);
    }
  }

  public function contact()
  {
    try {
      // Get contact settings
      $contactSettings = $this->settingsModel->getContactSettings();

      $response = $this->extractValues($contactSettings);

      return $this->sendSuccess($response, 'Contact settings retrieved successfully');
    } catch (Exception $e) {
      return $this->sendError($e->getMessage(), 500);
    }
  }

  public function social()
  {
    try {
      // Get social media settings
      $socialSettings = $this->settingsModel->getSocialSettings();

      $response = $this->extractValues($socialSettings);

      return $this->sendSuccess($response, 'Social media settings retrieved successfully');
    } catch (Exception $e) {
      return $this->sendError($e->getMessage(), 500);
    }
  }

  public function show($key)
  {
    try {
      // Validate key
      if (empty($key)) {
        throw new Exception('Setting key is required', 400);
      }

      // Get specific setting
      $value = $this->settingsModel->getSetting($key);

      if ($value === null) {
        return $this->sendError("Setting '{$key}' not found", 404);
      }

      $response = [
        'key' => $key,
        'value' => $value
      ];

      return $this->sendSuccess($response, 'Setting retrieved successfully');
    } catch (Exception $e) {
      return $this->sendError($e->getMessage(), 500);
    }
  }

  public function group($group)
  {
    try {
      // Validate group
      if (empty($group)) {
        throw new Exception('Group name is required', 400);
      }

      // Get settings by group
      $groupSettings = $this->settingsModel->getSettingsByGroup($group);

      if (empty($groupSettings)) {
        return $this->sendSuccess([], "No settings found for group '{$group}'");
      }

      $response = $this->extractValues($groupSettings);

      return $this->sendSuccess($response, "Settings for group '{$group}' retrieved successfully");
    } catch (Exception $e) {
      return $this->sendError($e->getMessage(), 500);
    }
  }

  // Helper method to structure settings
  private function structureSettings($allSettings)
  {
    $structured = [
      'site_identity' => [],
      'footer' => [],
      'contact' => [],
      'social' => [],
      'other' => []
    ];

    foreach ($allSettings as $key => $data) {
      $settingValue = [
        'value' => $data['value'],
        'type' => $data['data_type'] ?? 'string',
        'description' => $data['description'] ?? ''
      ];

      if (strpos($key, 'site_') === 0) {
        $simpleKey = str_replace('site_', '', $key);
        $structured['site_identity'][$simpleKey] = $settingValue;
      } elseif (strpos($key, 'footer_') === 0) {
        $simpleKey = str_replace('footer_', '', $key);
        $structured['footer'][$simpleKey] = $settingValue;
      } elseif (strpos($key, 'contact_') === 0) {
        $simpleKey = str_replace('contact_', '', $key);
        $structured['contact'][$simpleKey] = $settingValue;
      } elseif (strpos($key, 'social_') === 0) {
        $simpleKey = str_replace('social_', '', $key);
        $structured['social'][$simpleKey] = $settingValue;
      } else {
        $structured['other'][$key] = $settingValue;
      }
    }

    return $structured;
  }

  // Helper method to extract just values (for grouped responses)
  private function extractValues($settings)
  {
    $result = [];

    foreach ($settings as $key => $data) {
      $result[$key] = $data['value'];
    }

    return $result;
  }
}
