<?php
/**
 * Base Controller
 * All controllers extend this class
 */
class BaseController {
    protected $db;
    protected $user;
    protected $flash;
    
    public function __construct() {
        $this->db = getDB();
        $this->user = $this->getAuthenticatedUser();
        $this->flash = $this->getFlashMessages();
    }
    
    /**
     * Get authenticated user
     * @return array|null User data
     */
    protected function getAuthenticatedUser() {
        if (isset($_SESSION['user_id'])) {
            try {
                $stmt = $this->db->prepare('SELECT * FROM users WHERE id = ? AND status = "active" LIMIT 1');
                $stmt->execute([$_SESSION['user_id']]);
                return $stmt->fetch();
            } catch (PDOException $e) {
                $this->logError($e->getMessage());
                return null;
            }
        }
        return null;
    }
    
    /**
     * Check if user is authenticated
     * @return bool
     */
    protected function isAuthenticated() {
        return !empty($this->user);
    }
    
    /**
     * Check if user has permission
     * @param string $permission Permission name
     * @return bool Has permission
     */
    protected function hasPermission($permission) {
        if (!$this->isAuthenticated()) {
            return false;
        }
        
        $permissions = getPlanFeatures($this->user['subscription_plan']);
        return isset($permissions[$permission]) && $permissions[$permission];
    }
    
    /**
     * Render view template
     * @param string $view View template path
     * @param array $data Data to pass to view
     */
    protected function render($view, $data = []) {
        // Make controller properties available to view
        $data['user'] = $this->user;
        $data['flash'] = $this->flash;
        
        // Extract data to make variables available in view
        extract($data);
        
        // Start output buffering
        ob_start();
        
        // Include header template
        include 'views/templates/header.php';
        
        // Include view template
        include "views/$view.php";
        
        // Include footer template
        include 'views/templates/footer.php';
        
        // Get buffered content and clean buffer
        $content = ob_get_clean();
        
        // Output content
        echo $content;
    }
    
    /**
     * Redirect to URL
     * @param string $url URL to redirect to
     */
    protected function redirect($url) {
        header("Location: $url");
        exit;
    }
    
    /**
     * Send JSON response
     * @param mixed $data Response data
     * @param int $status HTTP status code
     */
    protected function jsonResponse($data, $status = 200) {
        header('Content-Type: application/json');
        http_response_code($status);
        echo json_encode($data);
        exit;
    }
    
    /**
     * Set flash message
     * @param string $type Message type (success, error, warning, info)
     * @param string $message Message text
     */
    protected function setFlash($type, $message) {
        $_SESSION['flash'][$type][] = $message;
    }
    
    /**
     * Get flash messages
     * @return array Flash messages
     */
    protected function getFlashMessages() {
        $flash = $_SESSION['flash'] ?? [];
        unset($_SESSION['flash']);
        return $flash;
    }
    
    /**
     * Generate CSRF token
     * @return string CSRF token
     */
    protected function generateCSRFToken() {
        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['csrf_token'];
    }
    
    /**
     * Validate CSRF token
     * @return bool Valid token
     */
    protected function validateCSRF() {
        $token = $_POST['csrf_token'] ?? $_SERVER['HTTP_X_CSRF_TOKEN'] ?? null;
        return $token && hash_equals($_SESSION['csrf_token'], $token);
    }
    
    /**
     * Validate input data
     * @param array $data Input data
     * @param array $rules Validation rules
     * @return array Validation errors
     */
    protected function validate($data, $rules) {
        $errors = [];
        
        foreach ($rules as $field => $rule) {
            $value = $data[$field] ?? null;
            $ruleList = explode('|', $rule);
            
            foreach ($ruleList as $r) {
                $params = [];
                
                if (strpos($r, ':') !== false) {
                    list($r, $param) = explode(':', $r);
                    $params = explode(',', $param);
                }
                
                switch ($r) {
                    case 'required':
                        if (empty($value)) {
                            $errors[$field] = 'This field is required';
                        }
                        break;
                        
                    case 'email':
                        if ($value && !filter_var($value, FILTER_VALIDATE_EMAIL)) {
                            $errors[$field] = 'Invalid email address';
                        }
                        break;
                        
                    case 'min':
                        if (strlen($value) < $params[0]) {
                            $errors[$field] = "Must be at least {$params[0]} characters";
                        }
                        break;
                        
                    case 'max':
                        if (strlen($value) > $params[0]) {
                            $errors[$field] = "Must not exceed {$params[0]} characters";
                        }
                        break;
                        
                    case 'match':
                        if ($value !== $data[$params[0]]) {
                            $errors[$field] = "Does not match {$params[0]}";
                        }
                        break;
                        
                    case 'unique':
                        try {
                            $stmt = $this->db->prepare("SELECT 1 FROM {$params[0]} WHERE {$params[1]} = ?");
                            $stmt->execute([$value]);
                            if ($stmt->fetch()) {
                                $errors[$field] = 'Already exists';
                            }
                        } catch (PDOException $e) {
                            $this->logError($e->getMessage());
                            $errors[$field] = 'Validation error';
                        }
                        break;
                }
                
                if (isset($errors[$field])) {
                    break;
                }
            }
        }
        
        return $errors;
    }
    
    /**
     * Log error message
     * @param string $message Error message
     * @param string $level Error level
     */
    protected function logError($message, $level = 'error') {
        $logFile = __DIR__ . '/../logs/' . date('Y-m-d') . '.log';
        $logMessage = sprintf(
            "[%s] %s: %s\n",
            date('Y-m-d H:i:s'),
            strtoupper($level),
            $message
        );
        
        error_log($logMessage, 3, $logFile);
    }
    
    /**
     * Log audit event
     * @param string $action Action name
     * @param string $entityType Entity type
     * @param int $entityId Entity ID
     * @param array $metadata Additional metadata
     */
    protected function logAudit($action, $entityType, $entityId, $metadata = []) {
        try {
            $stmt = $this->db->prepare('
                INSERT INTO audit_logs (
                    user_id,
                    action,
                    entity_type,
                    entity_id,
                    metadata,
                    ip_address,
                    user_agent,
                    created_at
                ) VALUES (?, ?, ?, ?, ?, ?, ?, NOW())
            ');
            
            $stmt->execute([
                $this->user['id'] ?? null,
                $action,
                $entityType,
                $entityId,
                json_encode($metadata),
                $_SERVER['REMOTE_ADDR'] ?? null,
                $_SERVER['HTTP_USER_AGENT'] ?? null
            ]);
        } catch (PDOException $e) {
            $this->logError($e->getMessage());
        }
    }
    
    /**
     * Check subscription limits
     * @param string $feature Feature to check
     * @return bool Within limits
     */
    protected function checkSubscriptionLimits($feature = null) {
        if (!$this->isAuthenticated()) {
            return false;
        }
        
        $plan = getPlanFeatures($this->user['subscription_plan']);
        
        if ($feature) {
            return $plan[$feature] === -1 || $this->getFeatureUsage($feature) < $plan[$feature];
        }
        
        foreach ($plan as $feature => $limit) {
            if ($limit !== -1 && $this->getFeatureUsage($feature) >= $limit) {
                return false;
            }
        }
        
        return true;
    }
    
    /**
     * Get feature usage
     * @param string $feature Feature name
     * @return int Current usage
     */
    protected function getFeatureUsage($feature) {
        try {
            switch ($feature) {
                case 'social_accounts':
                    $stmt = $this->db->prepare('
                        SELECT COUNT(*) FROM social_accounts 
                        WHERE user_id = ? AND status = "active"
                    ');
                    break;
                    
                case 'scheduled_posts':
                    $stmt = $this->db->prepare('
                        SELECT COUNT(*) FROM posts 
                        WHERE user_id = ? AND status = "scheduled"
                    ');
                    break;
                    
                case 'team_members':
                    $stmt = $this->db->prepare('
                        SELECT COUNT(*) FROM team_members 
                        WHERE team_id = (
                            SELECT team_id FROM team_members 
                            WHERE user_id = ? AND role = "owner"
                        )
                    ');
                    break;
                    
                default:
                    return 0;
            }
            
            $stmt->execute([$this->user['id']]);
            return (int) $stmt->fetchColumn();
        } catch (PDOException $e) {
            $this->logError($e->getMessage());
            return 0;
        }
    }
}
?>
