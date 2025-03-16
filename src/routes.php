<?php
/**
 * Application Routes
 */

return [
    // Public routes
    'GET' => [
        '/' => ['HomeController', 'index'],
        '/features' => ['HomeController', 'features'],
        '/pricing' => ['HomeController', 'pricing'],
        '/about' => ['HomeController', 'about'],
        '/contact' => ['HomeController', 'contact'],
        '/blog' => ['HomeController', 'blog'],
        '/help' => ['HomeController', 'help'],
        '/terms' => ['HomeController', 'terms'],
        '/privacy' => ['HomeController', 'privacy'],
        
        // Authentication routes
        '/login' => ['AuthController', 'loginForm'],
        '/register' => ['AuthController', 'registerForm'],
        '/forgot-password' => ['AuthController', 'forgotPasswordForm'],
        '/reset-password/{token}' => ['AuthController', 'resetPasswordForm'],
        '/verify-email/{token}' => ['AuthController', 'verifyEmail'],
        
        // Social authentication routes
        '/auth/{platform}/connect' => ['AuthController', 'socialConnect'],
        '/auth/{platform}/callback' => ['AuthController', 'socialCallback'],
        
        // Dashboard routes
        '/dashboard' => ['DashboardController', 'index'],
        '/dashboard/settings' => ['DashboardController', 'settings'],
        '/dashboard/notifications' => ['DashboardController', 'notifications'],
        
        // Posts routes
        '/dashboard/posts' => ['PostController', 'index'],
        '/dashboard/posts/create' => ['PostController', 'createForm'],
        '/dashboard/posts/edit/{id}' => ['PostController', 'editForm'],
        '/dashboard/posts/analytics/{id}' => ['PostController', 'analytics'],
        
        // Accounts routes
        '/dashboard/accounts' => ['AccountController', 'index'],
        '/dashboard/accounts/{id}/analytics' => ['AccountController', 'analytics'],
        
        // Analytics routes
        '/dashboard/analytics' => ['AnalyticsController', 'index'],
        '/dashboard/analytics/export' => ['AnalyticsController', 'export']
    ],
    
    'POST' => [
        // Authentication actions
        '/login' => ['AuthController', 'login'],
        '/register' => ['AuthController', 'register'],
        '/logout' => ['AuthController', 'logout'],
        '/forgot-password' => ['AuthController', 'forgotPassword'],
        '/reset-password' => ['AuthController', 'resetPassword'],
        '/contact' => ['HomeController', 'submitContact'],
        
        // Dashboard actions
        '/dashboard/settings/profile' => ['DashboardController', 'updateProfile'],
        '/dashboard/settings/password' => ['DashboardController', 'updatePassword'],
        '/dashboard/settings/notifications' => ['DashboardController', 'updateNotifications'],
        '/dashboard/settings/delete-account' => ['DashboardController', 'deleteAccount'],
        
        // Posts actions
        '/dashboard/posts/create' => ['PostController', 'create'],
        '/dashboard/posts/update/{id}' => ['PostController', 'update'],
        '/dashboard/posts/delete/{id}' => ['PostController', 'delete'],
        '/dashboard/posts/schedule/{id}' => ['PostController', 'schedule'],
        '/dashboard/posts/publish/{id}' => ['PostController', 'publish'],
        '/dashboard/posts/save-draft' => ['PostController', 'saveDraft'],
        '/dashboard/posts/hashtag-suggestions' => ['PostController', 'getHashtagSuggestions'],
        
        // Accounts actions
        '/dashboard/accounts/{id}/refresh-token' => ['AccountController', 'refreshToken'],
        '/dashboard/accounts/{id}/disconnect' => ['AccountController', 'disconnect'],
        
        // Notifications actions
        '/dashboard/notifications/mark-read/{id}' => ['DashboardController', 'markNotificationRead'],
        '/dashboard/notifications/mark-all-read' => ['DashboardController', 'markAllNotificationsRead'],
        '/dashboard/notifications/clear' => ['DashboardController', 'clearNotifications'],
        
        // Analytics actions
        '/dashboard/analytics/download' => ['AnalyticsController', 'downloadReport']
    ],
    
    // API routes (for AJAX requests)
    'API' => [
        'GET' => [
            '/api/posts/{id}' => ['ApiController', 'getPost'],
            '/api/analytics/data' => ['ApiController', 'getAnalyticsData'],
            '/api/notifications' => ['ApiController', 'getNotifications']
        ],
        'POST' => [
            '/api/posts/{id}/status' => ['ApiController', 'updatePostStatus'],
            '/api/upload/media' => ['ApiController', 'uploadMedia']
        ]
    ]
];

/**
 * Route middleware configurations
 */
$routeMiddleware = [
    // Authentication middleware
    'auth' => [
        'pattern' => '#^/dashboard#',
        'handler' => function($request) {
            if (!isAuthenticated()) {
                redirect('/login');
            }
        }
    ],
    
    // Guest middleware (for non-authenticated users)
    'guest' => [
        'pattern' => '#^/(login|register|forgot-password|reset-password)#',
        'handler' => function($request) {
            if (isAuthenticated()) {
                redirect('/dashboard');
            }
        }
    ],
    
    // CSRF protection middleware
    'csrf' => [
        'pattern' => '#^/(dashboard|api)#',
        'handler' => function($request) {
            if (!validateCSRF()) {
                throw new Exception('Invalid CSRF token');
            }
        }
    ],
    
    // API authentication middleware
    'api_auth' => [
        'pattern' => '#^/api#',
        'handler' => function($request) {
            if (!validateApiToken()) {
                throw new Exception('Invalid API token');
            }
        }
    ],
    
    // Subscription check middleware
    'subscription' => [
        'pattern' => '#^/dashboard/(posts/create|accounts/connect)#',
        'handler' => function($request) {
            if (!checkSubscriptionLimits()) {
                redirect('/dashboard/settings/subscription');
            }
        }
    ]
];

/**
 * Global middleware (applied to all routes)
 */
$globalMiddleware = [
    // Security headers
    function($request) {
        header('X-Frame-Options: DENY');
        header('X-XSS-Protection: 1; mode=block');
        header('X-Content-Type-Options: nosniff');
        header('Referrer-Policy: strict-origin-when-cross-origin');
        if (APP_ENV === 'production') {
            header('Strict-Transport-Security: max-age=31536000; includeSubDomains');
        }
    },
    
    // Maintenance mode check
    function($request) {
        if (file_exists(STORAGE_PATH . '/down') && !isMaintenanceExempt()) {
            include VIEW_PATH . '/maintenance.php';
            exit;
        }
    },
    
    // Request logging
    function($request) {
        if (APP_DEBUG) {
            error_log(sprintf(
                "[%s] %s %s",
                date('Y-m-d H:i:s'),
                $_SERVER['REQUEST_METHOD'],
                $_SERVER['REQUEST_URI']
            ));
        }
    }
];

/**
 * Error handlers
 */
$errorHandlers = [
    '404' => function($request) {
        header('HTTP/1.0 404 Not Found');
        return ['ErrorController', 'notFound'];
    },
    '500' => function($request) {
        header('HTTP/1.0 500 Internal Server Error');
        return ['ErrorController', 'serverError'];
    }
];
?>
