<?php
/**
 * Application Entry Point
 */

// Set error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Define root path
define('ROOT_PATH', dirname(__DIR__));

// Load configuration
require_once ROOT_PATH . '/src/config.php';

// Load helper functions
require_once ROOT_PATH . '/src/helpers/functions.php';

// Autoload classes
spl_autoload_register(function ($class) {
    // Convert class name to file path
    $paths = [
        ROOT_PATH . '/src/controllers/' . $class . '.php',
        ROOT_PATH . '/src/models/' . $class . '.php',
        ROOT_PATH . '/src/services/' . $class . '.php'
    ];
    
    foreach ($paths as $path) {
        if (file_exists($path)) {
            require_once $path;
            return;
        }
    }
});

// Load routes
$routes = require_once ROOT_PATH . '/src/routes.php';

try {
    // Get request method and path
    $method = $_SERVER['REQUEST_METHOD'];
    $path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
    
    // Remove trailing slash
    $path = rtrim($path, '/');
    
    // Default to home page
    if ($path === '') {
        $path = '/';
    }
    
    // Handle API requests
    if (strpos($path, '/api/') === 0) {
        if (!isset($routes['API'][$method][$path])) {
            throw new Exception('API endpoint not found', 404);
        }
        
        // Validate API token
        if (!validateApiToken()) {
            throw new Exception('Invalid API token', 401);
        }
        
        $route = $routes['API'][$method][$path];
    } else {
        // Handle regular requests
        if (!isset($routes[$method][$path])) {
            // Check for dynamic routes
            $matched = false;
            foreach ($routes[$method] as $routePath => $routeHandler) {
                $pattern = preg_replace('/\{[^}]+\}/', '([^/]+)', $routePath);
                $pattern = str_replace('/', '\/', $pattern);
                if (preg_match('/^' . $pattern . '$/', $path, $matches)) {
                    array_shift($matches); // Remove full match
                    $route = $routeHandler;
                    $params = $matches;
                    $matched = true;
                    break;
                }
            }
            
            if (!$matched) {
                throw new Exception('Page not found', 404);
            }
        } else {
            $route = $routes[$method][$path];
            $params = [];
        }
    }
    
    // Apply global middleware
    foreach ($globalMiddleware as $middleware) {
        $middleware($_REQUEST);
    }
    
    // Apply route middleware
    foreach ($routeMiddleware as $middleware) {
        if (preg_match($middleware['pattern'], $path)) {
            $middleware['handler']($_REQUEST);
        }
    }
    
    // Execute route handler
    list($controller, $action) = $route;
    $controller = new $controller();
    
    if (!method_exists($controller, $action)) {
        throw new Exception('Action not found', 404);
    }
    
    // Call controller action with parameters
    $response = call_user_func_array([$controller, $action], $params);
    
    // Handle response
    if (is_array($response)) {
        header('Content-Type: application/json');
        echo json_encode($response);
    }
    
} catch (Exception $e) {
    $status = $e->getCode() ?: 500;
    
    // Log error
    if ($status === 500) {
        error_log($e->getMessage());
    }
    
    // Handle error based on request type
    if (strpos($path, '/api/') === 0) {
        // API error response
        header('Content-Type: application/json');
        http_response_code($status);
        echo json_encode([
            'error' => [
                'code' => $status,
                'message' => $e->getMessage()
            ]
        ]);
    } else {
        // Web error response
        if (isset($errorHandlers[$status])) {
            list($controller, $action) = $errorHandlers[$status]($_REQUEST);
            $controller = new $controller();
            $controller->$action();
        } else {
            // Default error page
            http_response_code($status);
            include ROOT_PATH . '/views/templates/error.php';
        }
    }
}

/**
 * Validate API token
 * @return bool Valid token
 */
function validateApiToken() {
    $token = $_SERVER['HTTP_X_API_TOKEN'] ?? null;
    
    if (!$token) {
        return false;
    }
    
    try {
        $db = getDB();
        $stmt = $db->prepare('
            SELECT 1 FROM api_tokens 
            WHERE token = ? 
            AND expires_at > NOW() 
            AND active = 1 
            LIMIT 1
        ');
        $stmt->execute([$token]);
        return (bool) $stmt->fetch();
    } catch (PDOException $e) {
        error_log('API token validation failed: ' . $e->getMessage());
        return false;
    }
}

/**
 * Get client preferred language
 * @return string Language code
 */
function getClientLanguage() {
    $languages = [];
    
    if (isset($_SERVER['HTTP_ACCEPT_LANGUAGE'])) {
        // Parse Accept-Language header
        preg_match_all(
            '/([a-z]{2,8}(-[a-z]{2,8})?)\s*(;\s*q\s*=\s*(1|0\.[0-9]+))?/i',
            $_SERVER['HTTP_ACCEPT_LANGUAGE'],
            $matches
        );
        
        if (count($matches[1])) {
            // Create array with priorities
            $languages = array_combine($matches[1], $matches[4]);
            
            // Set default priority (1) for languages without q value
            foreach ($languages as $lang => $val) {
                if ($val === '') {
                    $languages[$lang] = 1;
                }
            }
            
            // Sort by priority
            arsort($languages, SORT_NUMERIC);
            
            // Get first language
            $languages = array_keys($languages);
        }
    }
    
    // Default to English if no preference found
    return $languages[0] ?? 'en';
}

/**
 * Check if request is AJAX
 * @return bool Is AJAX request
 */
function isAjaxRequest() {
    return !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && 
           strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
}

/**
 * Check if request is from mobile device
 * @return bool Is mobile device
 */
function isMobileDevice() {
    return preg_match(
        '/(android|bb\d+|meego).+mobile|avantgo|bada\/|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od)|iris|kindle|lge |maemo|midp|mmp|mobile.+firefox|netfront|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\/|plucker|pocket|psp|series(4|6)0|symbian|treo|up\.(browser|link)|vodafone|wap|windows ce|xda|xiino/i',
        $_SERVER['HTTP_USER_AGENT']
    );
}
?>
