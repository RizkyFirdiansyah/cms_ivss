<?php

// Set response headers
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

// Handle preflight CORS request
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// Only allow GET and POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'GET' && $_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode([
        'success' => false,
        'data' => null,
        'message' => 'Method not allowed. Only GET and POST requests are supported.'
    ]);
    exit();
}

// Parse request URI
$requestUri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$scriptPath = dirname($_SERVER['SCRIPT_NAME']);
$path = str_replace($scriptPath, '', $requestUri);
$path = trim($path, '/');

// Split path into segments
$segments = array_filter(explode('/', $path));
$segments = array_values($segments); // Reindex array

// Root endpoint
if (empty($segments)) {
    http_response_code(200);
    echo json_encode([
        'success' => true,
        'data' => [
            'version' => '1.0',
            'name' => 'CMS IVSS REST API',
            'endpoints' => [
                'GET /api/settings' => 'Get all settings (structured)',
                'GET /api/settings/{key}' => 'Get specific setting by key',
                'GET /api/settings/site' => 'Get site identity settings',
                'GET /api/settings/footer' => 'Get footer settings',
                'GET /api/settings/contact' => 'Get contact settings',
                'GET /api/settings/social' => 'Get social media settings',
                'GET /api/settings/group?name={group}' => 'Get settings by group prefix',
                'GET /api/home' => 'Get all home page data',
                'GET /api/home/hero' => 'Get hero section data',
                'GET /api/home/profile' => 'Get profile section data',
                'GET /api/home/activities' => 'Get activities data',
                'GET /api/home/gallery' => 'Get gallery preview data',
                'GET /api/home/all' => 'Get all home page data (alias)',
                'GET /api/about' => 'Get all about page data',
                'GET /api/about/header' => 'Get about page header',
                'GET /api/about/profile' => 'Get about profile section',
                'GET /api/about/vision-mission' => 'Get vision and mission data',
                'GET /api/about/activities' => 'Get about activities',
                'GET /api/about/gallery' => 'Get gallery preview',
                'GET /api/users' => 'Get all users/members',
                'GET /api/users/{id}' => 'Get user details by ID',
                'GET /api/users/header' => 'Get member page header',
                'GET /api/facilities' => 'Get all facilities',
                'GET /api/facilities/{id}' => 'Get facility details by ID',
                'GET /api/facilities/header' => 'Get facility page header',
                'GET /api/sop' => 'Get all SOP data',
                'GET /api/sop/header' => 'Get SOP page header',
                'GET /api/sop/items' => 'Get all SOP items',
                'GET /api/gallery' => 'Get gallery items',
                'GET /api/gallery/{id}' => 'Get gallery item details by ID',
                'GET /api/news' => 'Get news with pagination and filters',
                'GET /api/news/{id}' => 'Get news details by ID',
                'GET /api/news/header' => 'Get news page header',
                'GET /api/news/filters' => 'Get available filter options',
                'GET /api/publications' => 'Get publications with pagination and filters',
                'GET /api/publications/{id}' => 'Get publication details by ID',
                'GET /api/publications/header' => 'Get publications page header',
                'GET /api/publications/filters' => 'Get available filter options',
                'GET /api/contact' => 'Get contact page header',
                'GET /api/contact/header' => 'Get contact page header (alias)',
                'POST /api/contact/submit' => 'Submit feedback form',
            ]
        ],
        'message' => 'Welcome to CMS IVSS REST API'
    ]);
    exit();
}

// Load base controller
require_once __DIR__ . '/controllers/ApiBaseController.php';

// Route requests
try {
    $resource = $segments[0];
    $resourceId = $segments[1] ?? null;

    switch ($resource) {

        // Dalam switch case untuk resource
        case 'settings':
            require_once __DIR__ . '/controllers/SettingsApiController.php';
            $controller = new SettingsApiController();

            if ($resourceId === 'site') {
                $controller->site();
                break;
            } elseif ($resourceId === 'footer') {
                $controller->footer();
                break;
            } elseif ($resourceId === 'contact') {
                $controller->contact();
                break;
            } elseif ($resourceId === 'social') {
                $controller->social();
                break;
            } elseif ($resourceId === 'group') {
                // Get group name from query parameter
                $group = $_GET['name'] ?? null;
                if ($group) {
                    $controller->group($group);
                } else {
                    http_response_code(400);
                    echo json_encode([
                        'success' => false,
                        'data' => null,
                        'message' => 'Group name is required for settings/group endpoint'
                    ]);
                }
                break;
            } else {
                $controller->index();
            }

        case 'home':
            require_once __DIR__ . '/controllers/HomeApiController.php';
            $controller = new HomeApiController();

            if ($resourceId === 'hero') {
                $controller->hero();
                break;
            } elseif ($resourceId === 'profile') {
                $controller->profile();
                break;
            } elseif ($resourceId === 'activities') {
                $controller->activities();
                break;
            } elseif ($resourceId === 'gallery') {
                $controller->gallery();
                break;
            } elseif ($resourceId === 'all') {
                $controller->all();
                break;
            } else {
                $controller->index();
            }
            break;

        case 'about':
            require_once __DIR__ . '/controllers/AboutApiController.php';
            $controller = new AboutApiController();

            if ($resourceId === 'header') {
                $controller->header();
                break;
            } elseif ($resourceId === 'profile') {
                $controller->profile();
                break;
            } elseif ($resourceId === 'vision-mission') {
                $controller->visionMission();
                break;
            } elseif ($resourceId === 'activities') {
                $controller->activities();
                break;
            } elseif ($resourceId === 'gallery') {
                $controller->gallery();
                break;
            } else {
                $controller->index();
            }
            break;

        case 'users':
            require_once __DIR__ . '/controllers/UsersApiController.php';
            $controller = new UsersApiController();

            if ($resourceId === 'header') {
                $controller->header();
                break;
            } else if ($resourceId) {
                $controller->show($resourceId);
            } else {
                $controller->index();
            }
            break;

        case 'facilities':
            require_once __DIR__ . '/controllers/FacilitiesApiController.php';
            $controller = new FacilitiesApiController();

            if ($resourceId === 'header') {
                $controller->header();
                break;
            } else {
                if ($resourceId) {
                    $controller->show($resourceId);
                } else {
                    $controller->index();
                }
            }
            break;

        case 'sop':
            require_once __DIR__ . '/controllers/SopApiController.php';
            $controller = new SopApiController();

            if ($resourceId === 'header') {
                $controller->header();
                break;
            } elseif ($resourceId === 'items') {
                $controller->items();
                break;
            } else {
                $controller->index();
            }
            break;

        case 'gallery':
            require_once __DIR__ . '/controllers/GalleryApiController.php';
            $controller = new GalleryApiController();

            if ($resourceId === 'header') {
                $controller->header();
                break;
            } elseif ($resourceId) {
                $controller->show($resourceId);
            } else {
                $controller->index();
            }
            break;

        case 'news':
            require_once __DIR__ . '/controllers/NewsApiController.php';
            $controller = new NewsApiController();

            if ($resourceId === 'header') {
                $controller->header();
                break;
            } else if ($resourceId === 'filters') {
                $controller->filters();
                break;
            } else if (is_numeric($resourceId)) {
                $controller->show((int)$resourceId);
                break;
            } else {
                $controller->index();
            }
            break;

        case 'publications':
            require_once __DIR__ . '/controllers/PublicationsApiController.php';
            $controller = new PublicationsApiController();

            if ($resourceId === 'header') {
                $controller->header();
                break;
            } else if ($resourceId === 'filters') {
                $controller->filters();
                break;
            } else {
                $controller->index();
            }
            break;

        case 'research':
            require_once __DIR__ . '/controllers/ResearchApiController.php';
            $controller = new ResearchApiController();
            if ($resourceId === 'header') {
                $controller->header();
                break;
            } else if ($resourceId === 'filters') {
                $controller->filters();
                break;
            } else if (is_numeric($resourceId)) {
                $controller->show((int)$resourceId);
                break;
            } else {
                $controller->index();
            }
            break;

                case 'bootcamp':
            require_once __DIR__ . '/controllers/BootcampApiController.php';
            $controller = new BootcampApiController();
            if ($resourceId === 'header') {
                $controller->header();
                break;
            } else if ($resourceId === 'filters') {
                $controller->filters();
                break;
            } else if (is_numeric($resourceId)) {
                $controller->show((int)$resourceId);
                break;
            } else {
                $controller->index();
            }
            break;
        

        case 'contact':
            require_once __DIR__ . '/controllers/ContactApiController.php';
            $controller = new ContactApiController();

            if ($resourceId === 'submit') {
                $controller->submit();
            } elseif ($resourceId === 'header') {
                $controller->header();
            } else {
                $controller->index();
            }
            break;

        case 'register':
            require_once __DIR__ . '/controllers/RegistrationApiController.php';
            $controller = new RegistrationApiController();

            // Check HTTP method untuk menentukan endpoint
            if ($resourceId === 'submit') {
                $controller->submit();
            } elseif ($resourceId === 'check') {
                $controller->check();
            } elseif ($resourceId === 'program-studi') {
                $controller->getProgramStudi();
            } else {
                // Default response atau error
                http_response_code(404);
                echo json_encode([
                    'success' => false,
                    'message' => 'Endpoint tidak ditemukan'
                ]);
            }
            break;

        default:
            http_response_code(404);
            echo json_encode([
                'success' => false,
                'data' => null,
                'message' => "Resource '{$resource}' not found"
            ]);
            break;
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'data' => null,
        'message' => 'An unexpected error occurred: ' . $e->getMessage()
    ]);
}
