<?php
/**
 * Helper Functions
 */

/**
 * Get database connection
 * @return PDO Database connection
 */
function getDB() {
    static $db = null;
    
    if ($db === null) {
        try {
            $dsn = sprintf(
                'mysql:host=%s;dbname=%s;charset=%s',
                DB_HOST,
                DB_NAME,
                DB_CHARSET
            );
            
            $db = new PDO($dsn, DB_USER, DB_PASS, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false
            ]);
        } catch (PDOException $e) {
            error_log('Database connection failed: ' . $e->getMessage());
            throw new Exception('Database connection failed');
        }
    }
    
    return $db;
}

/**
 * Format number for display
 * @param int $number Number to format
 * @return string Formatted number
 */
function formatNumber($number) {
    if ($number >= 1000000) {
        return round($number / 1000000, 1) . 'M';
    }
    if ($number >= 1000) {
        return round($number / 1000, 1) . 'K';
    }
    return number_format($number);
}

/**
 * Format date/time
 * @param string $datetime Date/time string
 * @param string $format Format string
 * @return string Formatted date/time
 */
function formatDateTime($datetime, $format = 'M j, Y g:i A') {
    return date($format, strtotime($datetime));
}

/**
 * Format date
 * @param string $date Date string
 * @param string $format Format string
 * @return string Formatted date
 */
function formatDate($date, $format = 'M j, Y') {
    return date($format, strtotime($date));
}

/**
 * Get time ago string
 * @param string $datetime Date/time string
 * @return string Time ago
 */
function getTimeAgo($datetime) {
    $time = strtotime($datetime);
    $now = time();
    $diff = $now - $time;
    
    if ($diff < 60) {
        return 'just now';
    }
    
    $intervals = [
        31536000 => 'year',
        2592000 => 'month',
        604800 => 'week',
        86400 => 'day',
        3600 => 'hour',
        60 => 'minute'
    ];
    
    foreach ($intervals as $seconds => $label) {
        $interval = floor($diff / $seconds);
        if ($interval > 0) {
            return $interval . ' ' . $label . ($interval > 1 ? 's' : '') . ' ago';
        }
    }
}

/**
 * Get client IP address
 * @return string IP address
 */
function getClientIP() {
    $headers = [
        'HTTP_CLIENT_IP',
        'HTTP_X_FORWARDED_FOR',
        'HTTP_X_FORWARDED',
        'HTTP_X_CLUSTER_CLIENT_IP',
        'HTTP_FORWARDED_FOR',
        'HTTP_FORWARDED',
        'REMOTE_ADDR'
    ];
    
    foreach ($headers as $header) {
        if (isset($_SERVER[$header])) {
            $ips = explode(',', $_SERVER[$header]);
            $ip = trim($ips[0]);
            if (filter_var($ip, FILTER_VALIDATE_IP)) {
                return $ip;
            }
        }
    }
    
    return '0.0.0.0';
}

/**
 * Get user agent string
 * @return string User agent
 */
function getUserAgent() {
    return $_SERVER['HTTP_USER_AGENT'] ?? 'Unknown';
}

/**
 * Truncate text
 * @param string $text Text to truncate
 * @param int $length Maximum length
 * @param string $append String to append
 * @return string Truncated text
 */
function truncateText($text, $length = 100, $append = '...') {
    if (mb_strlen($text) <= $length) {
        return $text;
    }
    return mb_substr($text, 0, $length - mb_strlen($append)) . $append;
}

/**
 * Get platform icon class
 * @param string $platform Platform name
 * @return string Icon class
 */
function getPlatformIcon($platform) {
    $icons = [
        'facebook' => 'fab fa-facebook',
        'twitter' => 'fab fa-twitter',
        'instagram' => 'fab fa-instagram',
        'linkedin' => 'fab fa-linkedin'
    ];
    return $icons[$platform] ?? 'fas fa-globe';
}

/**
 * Get platform color class
 * @param string $platform Platform name
 * @return string Color class
 */
function getPlatformColor($platform) {
    $colors = [
        'facebook' => 'text-blue-600',
        'twitter' => 'text-blue-400',
        'instagram' => 'text-pink-600',
        'linkedin' => 'text-blue-700'
    ];
    return $colors[$platform] ?? 'text-gray-600';
}

/**
 * Get platform background class
 * @param string $platform Platform name
 * @return string Background class
 */
function getPlatformBgClass($platform) {
    $classes = [
        'facebook' => 'bg-blue-100 text-blue-800',
        'twitter' => 'bg-blue-100 text-blue-800',
        'instagram' => 'bg-pink-100 text-pink-800',
        'linkedin' => 'bg-blue-100 text-blue-800'
    ];
    return $classes[$platform] ?? 'bg-gray-100 text-gray-800';
}

/**
 * Get status badge class
 * @param string $status Status
 * @return string Badge class
 */
function getStatusBadgeClass($status) {
    $classes = [
        'draft' => 'bg-gray-100 text-gray-800',
        'scheduled' => 'bg-blue-100 text-blue-800',
        'published' => 'bg-green-100 text-green-800',
        'failed' => 'bg-red-100 text-red-800',
        'pending' => 'bg-yellow-100 text-yellow-800'
    ];
    return $classes[$status] ?? 'bg-gray-100 text-gray-800';
}

/**
 * Generate random string
 * @param int $length String length
 * @return string Random string
 */
function generateRandomString($length = 32) {
    return bin2hex(random_bytes($length / 2));
}

/**
 * Sanitize filename
 * @param string $filename Filename to sanitize
 * @return string Sanitized filename
 */
function sanitizeFilename($filename) {
    // Remove any path components
    $filename = basename($filename);
    
    // Replace spaces with underscores
    $filename = str_replace(' ', '_', $filename);
    
    // Remove any non-alphanumeric characters except dots and underscores
    $filename = preg_replace('/[^A-Za-z0-9._-]/', '', $filename);
    
    // Ensure safe extension
    $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
    $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif', 'pdf', 'doc', 'docx'];
    
    if (!in_array($ext, $allowedExtensions)) {
        $filename .= '.txt';
    }
    
    return $filename;
}

/**
 * Get file mime type
 * @param string $path File path
 * @return string Mime type
 */
function getFileMimeType($path) {
    if (function_exists('finfo_file')) {
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mimeType = finfo_file($finfo, $path);
        finfo_close($finfo);
        return $mimeType;
    }
    
    if (function_exists('mime_content_type')) {
        return mime_content_type($path);
    }
    
    // Fallback to extension-based detection
    $ext = strtolower(pathinfo($path, PATHINFO_EXTENSION));
    $mimeTypes = [
        'jpg' => 'image/jpeg',
        'jpeg' => 'image/jpeg',
        'png' => 'image/png',
        'gif' => 'image/gif',
        'pdf' => 'application/pdf',
        'doc' => 'application/msword',
        'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'
    ];
    
    return $mimeTypes[$ext] ?? 'application/octet-stream';
}

/**
 * Send email
 * @param string $to Recipient email
 * @param string $subject Email subject
 * @param string $body Email body
 * @param array $attachments Email attachments
 * @return bool Success
 */
function sendEmail($to, $subject, $body, $attachments = []) {
    try {
        $mail = new PHPMailer\PHPMailer\PHPMailer(true);
        
        // Server settings
        $mail->isSMTP();
        $mail->Host = MAIL_HOST;
        $mail->SMTPAuth = true;
        $mail->Username = MAIL_USERNAME;
        $mail->Password = MAIL_PASSWORD;
        $mail->SMTPSecure = MAIL_ENCRYPTION;
        $mail->Port = MAIL_PORT;
        
        // Recipients
        $mail->setFrom(MAIL_FROM_ADDRESS, MAIL_FROM_NAME);
        $mail->addAddress($to);
        
        // Content
        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body = $body;
        
        // Attachments
        foreach ($attachments as $attachment) {
            $mail->addAttachment($attachment);
        }
        
        return $mail->send();
    } catch (Exception $e) {
        error_log('Email sending failed: ' . $e->getMessage());
        return false;
    }
}

/**
 * Generate slug from string
 * @param string $string String to convert
 * @return string Slug
 */
function generateSlug($string) {
    // Convert to lowercase
    $string = strtolower($string);
    
    // Replace non-alphanumeric characters with hyphens
    $string = preg_replace('/[^a-z0-9-]/', '-', $string);
    
    // Remove multiple consecutive hyphens
    $string = preg_replace('/-+/', '-', $string);
    
    // Remove leading and trailing hyphens
    return trim($string, '-');
}

/**
 * Format file size
 * @param int $bytes Size in bytes
 * @return string Formatted size
 */
function formatFileSize($bytes) {
    $units = ['B', 'KB', 'MB', 'GB', 'TB'];
    $i = 0;
    
    while ($bytes >= 1024 && $i < count($units) - 1) {
        $bytes /= 1024;
        $i++;
    }
    
    return round($bytes, 2) . ' ' . $units[$i];
}

/**
 * Check if string contains HTML
 * @param string $string String to check
 * @return bool Contains HTML
 */
function containsHTML($string) {
    return $string !== strip_tags($string);
}

/**
 * Convert HTML to plain text
 * @param string $html HTML string
 * @return string Plain text
 */
function htmlToText($html) {
    // Remove style/script tags and their contents
    $html = preg_replace('/<(style|script)[^>]*?>.*?<\/\1>/si', '', $html);
    
    // Convert breaks to newlines
    $html = preg_replace('/<br[^>]*>/i', "\n", $html);
    
    // Convert paragraphs to double newlines
    $html = preg_replace('/<\/p>/i', "\n\n", $html);
    
    // Strip remaining tags
    $text = strip_tags($html);
    
    // Decode HTML entities
    $text = html_entity_decode($text, ENT_QUOTES | ENT_HTML5, 'UTF-8');
    
    // Remove extra whitespace
    $text = preg_replace('/\s+/', ' ', $text);
    
    return trim($text);
}
?>
