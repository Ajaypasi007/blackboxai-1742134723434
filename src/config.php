<?php
/**
 * Application Configuration
 */

// Application settings
define('APP_NAME', 'Social Media Manager');
define('APP_ENV', getenv('APP_ENV') ?: 'development');
define('APP_URL', getenv('APP_URL') ?: 'http://localhost:8000');
define('APP_DEBUG', getenv('APP_DEBUG') ?: true);
define('APP_TIMEZONE', 'UTC');

// Database configuration
define('DB_HOST', getenv('DB_HOST') ?: 'localhost');
define('DB_NAME', getenv('DB_NAME') ?: 'social_media_manager');
define('DB_USER', getenv('DB_USER') ?: 'root');
define('DB_PASS', getenv('DB_PASS') ?: '');
define('DB_CHARSET', 'utf8mb4');

// Security settings
define('SESSION_LIFETIME', 7200); // 2 hours
define('CSRF_LIFETIME', 3600); // 1 hour
define('MAX_LOGIN_ATTEMPTS', 5);
define('LOGIN_LOCKOUT_TIME', 900); // 15 minutes
define('PASSWORD_MIN_LENGTH', 8);
define('TOKEN_LENGTH', 64);

// File upload settings
define('MAX_UPLOAD_SIZE', 10 * 1024 * 1024); // 10MB
define('ALLOWED_IMAGE_TYPES', ['image/jpeg', 'image/png', 'image/gif']);
define('ALLOWED_VIDEO_TYPES', ['video/mp4', 'video/quicktime']);
define('UPLOAD_PATH', __DIR__ . '/../public/uploads');

// Cache settings
define('CACHE_DRIVER', 'file'); // file, redis, memcached
define('CACHE_PREFIX', 'smm_');
define('CACHE_LIFETIME', 3600); // 1 hour

// Email settings
define('MAIL_DRIVER', getenv('MAIL_DRIVER') ?: 'smtp');
define('MAIL_HOST', getenv('MAIL_HOST') ?: 'smtp.mailtrap.io');
define('MAIL_PORT', getenv('MAIL_PORT') ?: 2525);
define('MAIL_USERNAME', getenv('MAIL_USERNAME') ?: '');
define('MAIL_PASSWORD', getenv('MAIL_PASSWORD') ?: '');
define('MAIL_ENCRYPTION', getenv('MAIL_ENCRYPTION') ?: 'tls');
define('MAIL_FROM_ADDRESS', getenv('MAIL_FROM_ADDRESS') ?: 'noreply@example.com');
define('MAIL_FROM_NAME', getenv('MAIL_FROM_NAME') ?: APP_NAME);

// Social media API credentials
$API_CREDENTIALS = [
    'facebook' => [
        'client_id' => getenv('FACEBOOK_CLIENT_ID'),
        'client_secret' => getenv('FACEBOOK_CLIENT_SECRET'),
        'redirect_uri' => APP_URL . '/auth/facebook/callback',
        'scopes' => ['email', 'pages_show_list', 'pages_read_engagement', 'pages_manage_posts'],
        'authUrl' => 'https://www.facebook.com/v12.0/dialog/oauth'
    ],
    'twitter' => [
        'client_id' => getenv('TWITTER_CLIENT_ID'),
        'client_secret' => getenv('TWITTER_CLIENT_SECRET'),
        'redirect_uri' => APP_URL . '/auth/twitter/callback',
        'scopes' => ['tweet.read', 'tweet.write', 'users.read'],
        'authUrl' => 'https://twitter.com/i/oauth2/authorize'
    ],
    'instagram' => [
        'client_id' => getenv('INSTAGRAM_CLIENT_ID'),
        'client_secret' => getenv('INSTAGRAM_CLIENT_SECRET'),
        'redirect_uri' => APP_URL . '/auth/instagram/callback',
        'scopes' => ['basic', 'comments', 'relationships'],
        'authUrl' => 'https://api.instagram.com/oauth/authorize'
    ],
    'linkedin' => [
        'client_id' => getenv('LINKEDIN_CLIENT_ID'),
        'client_secret' => getenv('LINKEDIN_CLIENT_SECRET'),
        'redirect_uri' => APP_URL . '/auth/linkedin/callback',
        'scopes' => ['r_liteprofile', 'r_organization_social', 'w_organization_social'],
        'authUrl' => 'https://www.linkedin.com/oauth/v2/authorization'
    ]
];

// Subscription plans
$SUBSCRIPTION_PLANS = [
    'free' => [
        'name' => 'Free',
        'price' => 0,
        'billing_period' => 'monthly',
        'features' => [
            'social_accounts' => 2,
            'scheduled_posts' => 10,
            'analytics_history' => 30, // days
            'team_members' => 1
        ]
    ],
    'pro' => [
        'name' => 'Professional',
        'price' => 29,
        'billing_period' => 'monthly',
        'features' => [
            'social_accounts' => 10,
            'scheduled_posts' => 100,
            'analytics_history' => 90, // days
            'team_members' => 3,
            'approval_workflow' => true,
            'custom_analytics' => true
        ]
    ],
    'business' => [
        'name' => 'Business',
        'price' => 99,
        'billing_period' => 'monthly',
        'features' => [
            'social_accounts' => -1, // unlimited
            'scheduled_posts' => -1, // unlimited
            'analytics_history' => 365, // days
            'team_members' => 10,
            'approval_workflow' => true,
            'custom_analytics' => true,
            'api_access' => true,
            'priority_support' => true
        ]
    ]
];

// Analytics settings
$ANALYTICS_METRICS = [
    'followers' => [
        'name' => 'Followers',
        'description' => 'Total number of followers',
        'type' => 'number',
        'aggregation' => 'last' // last, sum, average
    ],
    'engagement' => [
        'name' => 'Engagement',
        'description' => 'Total likes, comments, and shares',
        'type' => 'number',
        'aggregation' => 'sum'
    ],
    'impressions' => [
        'name' => 'Impressions',
        'description' => 'Number of times content was displayed',
        'type' => 'number',
        'aggregation' => 'sum'
    ],
    'clicks' => [
        'name' => 'Clicks',
        'description' => 'Number of clicks on links',
        'type' => 'number',
        'aggregation' => 'sum'
    ],
    'engagement_rate' => [
        'name' => 'Engagement Rate',
        'description' => 'Engagement as percentage of followers',
        'type' => 'percentage',
        'aggregation' => 'average'
    ]
];

// Platform-specific settings
$PLATFORM_SETTINGS = [
    'facebook' => [
        'max_text_length' => 63206,
        'max_images' => 10,
        'max_video_length' => 240, // minutes
        'supported_formats' => [
            'image' => ['jpg', 'jpeg', 'png', 'gif'],
            'video' => ['mp4', 'mov']
        ]
    ],
    'twitter' => [
        'max_text_length' => 280,
        'max_images' => 4,
        'max_video_length' => 140, // seconds
        'supported_formats' => [
            'image' => ['jpg', 'jpeg', 'png', 'gif'],
            'video' => ['mp4']
        ]
    ],
    'instagram' => [
        'max_text_length' => 2200,
        'max_images' => 10,
        'max_video_length' => 60, // seconds
        'supported_formats' => [
            'image' => ['jpg', 'jpeg'],
            'video' => ['mp4']
        ]
    ],
    'linkedin' => [
        'max_text_length' => 3000,
        'max_images' => 9,
        'max_video_length' => 10, // minutes
        'supported_formats' => [
            'image' => ['jpg', 'jpeg', 'png', 'gif'],
            'video' => ['mp4']
        ]
    ]
];

// Error reporting
if (APP_DEBUG) {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
} else {
    error_reporting(0);
    ini_set('display_errors', 0);
}

// Set timezone
date_default_timezone_set(APP_TIMEZONE);

// Initialize session
session_start([
    'cookie_lifetime' => SESSION_LIFETIME,
    'cookie_httponly' => true,
    'cookie_secure' => APP_ENV === 'production',
    'cookie_samesite' => 'Lax'
]);

/**
 * Get feature limit for a subscription plan
 * @param string $plan Plan name
 * @param string $feature Feature name
 * @return mixed Feature limit
 */
function getPlanFeatures($plan) {
    global $SUBSCRIPTION_PLANS;
    return isset($SUBSCRIPTION_PLANS[$plan]) ? $SUBSCRIPTION_PLANS[$plan]['features'] : [];
}

/**
 * Get platform-specific settings
 * @param string $platform Platform name
 * @return array Platform settings
 */
function getPlatformSettings($platform) {
    global $PLATFORM_SETTINGS;
    return isset($PLATFORM_SETTINGS[$platform]) ? $PLATFORM_SETTINGS[$platform] : [];
}

/**
 * Get analytics metric configuration
 * @param string $metric Metric name
 * @return array Metric configuration
 */
function getAnalyticsMetric($metric) {
    global $ANALYTICS_METRICS;
    return isset($ANALYTICS_METRICS[$metric]) ? $ANALYTICS_METRICS[$metric] : null;
}
?>
