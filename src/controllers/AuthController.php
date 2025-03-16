<?php
/**
 * Authentication Controller
 * Handles user authentication, registration, and password management
 */
class AuthController extends BaseController {
    private $userModel;
    
    public function __construct() {
        parent::__construct();
        $this->userModel = new User();
    }
    
    /**
     * Display login form
     */
    public function loginForm() {
        $this->render('auth/login');
    }
    
    /**
     * Handle login submission
     */
    public function login() {
        if (!$this->validateCSRF()) {
            $this->setFlash('error', 'Invalid request');
            $this->redirect('/login');
        }
        
        $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
        $password = $_POST['password'] ?? '';
        $rememberMe = isset($_POST['remember_me']);
        
        try {
            // Validate input
            $errors = $this->validate([
                'email' => $email,
                'password' => $password
            ], [
                'email' => 'required|email',
                'password' => 'required|min:8'
            ]);
            
            if (!empty($errors)) {
                $this->render('auth/login', [
                    'errors' => $errors,
                    'data' => ['email' => $email]
                ]);
                return;
            }
            
            // Check login attempts
            if ($this->isLoginBlocked($email)) {
                $this->setFlash('error', 'Too many login attempts. Please try again later.');
                $this->redirect('/login');
            }
            
            // Attempt login
            $user = $this->userModel->findByEmail($email);
            
            if (!$user || !password_verify($password, $user['password'])) {
                $this->incrementLoginAttempts($email);
                $this->setFlash('error', 'Invalid email or password');
                $this->redirect('/login');
            }
            
            // Check account status
            if ($user['status'] !== 'active') {
                switch ($user['status']) {
                    case 'pending':
                        $this->setFlash('warning', 'Please verify your email address to activate your account.');
                        break;
                    case 'suspended':
                        $this->setFlash('error', 'Your account has been suspended. Please contact support.');
                        break;
                    default:
                        $this->setFlash('error', 'Account is not active');
                }
                $this->redirect('/login');
            }
            
            // Clear login attempts
            $this->clearLoginAttempts($email);
            
            // Set session
            $_SESSION['user_id'] = $user['id'];
            
            // Set remember me cookie if requested
            if ($rememberMe) {
                $token = $this->generateRememberToken();
                $this->userModel->updateRememberToken($user['id'], $token);
                setcookie('remember_token', $token, time() + 30 * 24 * 60 * 60, '/', '', true, true);
            }
            
            // Log login
            $this->logAudit('login', 'user', $user['id'], [
                'ip' => getClientIP(),
                'user_agent' => getUserAgent()
            ]);
            
            // Update last login
            $this->userModel->updateLastLogin($user['id']);
            
            $this->redirect('/dashboard');
            
        } catch (Exception $e) {
            $this->logError($e->getMessage());
            $this->setFlash('error', 'An error occurred. Please try again.');
            $this->redirect('/login');
        }
    }
    
    /**
     * Display registration form
     */
    public function registerForm() {
        $this->render('auth/register');
    }
    
    /**
     * Handle registration submission
     */
    public function register() {
        if (!$this->validateCSRF()) {
            $this->setFlash('error', 'Invalid request');
            $this->redirect('/register');
        }
        
        $data = [
            'first_name' => filter_input(INPUT_POST, 'first_name', FILTER_SANITIZE_STRING),
            'last_name' => filter_input(INPUT_POST, 'last_name', FILTER_SANITIZE_STRING),
            'email' => filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL),
            'password' => $_POST['password'] ?? '',
            'password_confirmation' => $_POST['password_confirmation'] ?? '',
            'terms' => isset($_POST['terms'])
        ];
        
        try {
            // Validate input
            $errors = $this->validate($data, [
                'first_name' => 'required|max:50',
                'last_name' => 'required|max:50',
                'email' => 'required|email|unique:users,email',
                'password' => 'required|min:8',
                'terms' => 'required'
            ]);
            
            // Additional password validation
            if (!preg_match('/[A-Z]/', $data['password'])) {
                $errors['password'] = 'Password must contain at least one uppercase letter';
            } elseif (!preg_match('/[0-9]/', $data['password'])) {
                $errors['password'] = 'Password must contain at least one number';
            } elseif (!preg_match('/[^A-Za-z0-9]/', $data['password'])) {
                $errors['password'] = 'Password must contain at least one special character';
            }
            
            // Check password confirmation
            if ($data['password'] !== $data['password_confirmation']) {
                $errors['password_confirmation'] = 'Passwords do not match';
            }
            
            if (!empty($errors)) {
                $this->render('auth/register', [
                    'errors' => $errors,
                    'data' => $data
                ]);
                return;
            }
            
            // Create user
            $userId = $this->userModel->create([
                'first_name' => $data['first_name'],
                'last_name' => $data['last_name'],
                'email' => $data['email'],
                'password' => password_hash($data['password'], PASSWORD_DEFAULT),
                'verification_token' => bin2hex(random_bytes(32)),
                'status' => 'pending'
            ]);
            
            // Send verification email
            $this->sendVerificationEmail($userId);
            
            // Log registration
            $this->logAudit('register', 'user', $userId, [
                'ip' => getClientIP(),
                'user_agent' => getUserAgent()
            ]);
            
            $this->setFlash('success', 'Registration successful! Please check your email to verify your account.');
            $this->redirect('/login');
            
        } catch (Exception $e) {
            $this->logError($e->getMessage());
            $this->setFlash('error', 'An error occurred. Please try again.');
            $this->redirect('/register');
        }
    }
    
    /**
     * Handle logout
     */
    public function logout() {
        if (!$this->validateCSRF()) {
            $this->setFlash('error', 'Invalid request');
            $this->redirect('/');
        }
        
        // Clear session
        session_destroy();
        
        // Clear remember me cookie
        setcookie('remember_token', '', time() - 3600, '/', '', true, true);
        
        // Log logout
        if ($this->user) {
            $this->logAudit('logout', 'user', $this->user['id']);
        }
        
        $this->redirect('/');
    }
    
    /**
     * Display forgot password form
     */
    public function forgotPasswordForm() {
        $this->render('auth/forgot-password');
    }
    
    /**
     * Handle forgot password submission
     */
    public function forgotPassword() {
        if (!$this->validateCSRF()) {
            $this->setFlash('error', 'Invalid request');
            $this->redirect('/forgot-password');
        }
        
        $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
        
        try {
            $user = $this->userModel->findByEmail($email);
            
            if ($user) {
                // Generate reset token
                $token = bin2hex(random_bytes(32));
                $expires = date('Y-m-d H:i:s', strtotime('+1 hour'));
                
                // Save token
                $stmt = $this->db->prepare('
                    INSERT INTO password_reset_tokens (
                        user_id, 
                        token, 
                        expires_at, 
                        created_at
                    ) VALUES (?, ?, ?, NOW())
                ');
                $stmt->execute([$user['id'], $token, $expires]);
                
                // Send reset email
                $this->sendPasswordResetEmail($user['id'], $token);
                
                // Log password reset request
                $this->logAudit('password_reset_request', 'user', $user['id']);
            }
            
            // Always show success to prevent email enumeration
            $this->setFlash('success', 'If an account exists with that email, you will receive password reset instructions.');
            $this->redirect('/login');
            
        } catch (Exception $e) {
            $this->logError($e->getMessage());
            $this->setFlash('error', 'An error occurred. Please try again.');
            $this->redirect('/forgot-password');
        }
    }
    
    /**
     * Display reset password form
     */
    public function resetPasswordForm($token) {
        try {
            // Validate token
            $stmt = $this->db->prepare('
                SELECT user_id 
                FROM password_reset_tokens 
                WHERE token = ? 
                AND expires_at > NOW() 
                AND used = 0 
                LIMIT 1
            ');
            $stmt->execute([$token]);
            $result = $stmt->fetch();
            
            if (!$result) {
                $this->setFlash('error', 'Invalid or expired password reset token');
                $this->redirect('/login');
            }
            
            $this->render('auth/reset-password', ['token' => $token]);
            
        } catch (Exception $e) {
            $this->logError($e->getMessage());
            $this->setFlash('error', 'An error occurred. Please try again.');
            $this->redirect('/login');
        }
    }
    
    /**
     * Handle reset password submission
     */
    public function resetPassword() {
        if (!$this->validateCSRF()) {
            $this->setFlash('error', 'Invalid request');
            $this->redirect('/login');
        }
        
        $token = $_POST['token'] ?? '';
        $password = $_POST['password'] ?? '';
        $confirmation = $_POST['password_confirmation'] ?? '';
        
        try {
            // Validate token
            $stmt = $this->db->prepare('
                SELECT user_id 
                FROM password_reset_tokens 
                WHERE token = ? 
                AND expires_at > NOW() 
                AND used = 0 
                LIMIT 1
            ');
            $stmt->execute([$token]);
            $result = $stmt->fetch();
            
            if (!$result) {
                $this->setFlash('error', 'Invalid or expired password reset token');
                $this->redirect('/login');
            }
            
            // Validate password
            $errors = $this->validate([
                'password' => $password
            ], [
                'password' => 'required|min:8'
            ]);
            
            if ($password !== $confirmation) {
                $errors['password_confirmation'] = 'Passwords do not match';
            }
            
            if (!empty($errors)) {
                $this->render('auth/reset-password', [
                    'token' => $token,
                    'errors' => $errors
                ]);
                return;
            }
            
            // Update password
            $this->userModel->updatePassword($result['user_id'], $password);
            
            // Mark token as used
            $stmt = $this->db->prepare('
                UPDATE password_reset_tokens 
                SET used = 1 
                WHERE token = ?
            ');
            $stmt->execute([$token]);
            
            // Log password reset
            $this->logAudit('password_reset', 'user', $result['user_id']);
            
            $this->setFlash('success', 'Password has been reset successfully. Please log in with your new password.');
            $this->redirect('/login');
            
        } catch (Exception $e) {
            $this->logError($e->getMessage());
            $this->setFlash('error', 'An error occurred. Please try again.');
            $this->redirect('/login');
        }
    }
    
    /**
     * Handle email verification
     */
    public function verifyEmail($token) {
        try {
            $user = $this->userModel->findByVerificationToken($token);
            
            if (!$user) {
                $this->setFlash('error', 'Invalid verification token');
                $this->redirect('/login');
            }
            
            // Activate account
            $this->userModel->activate($user['id']);
            
            // Log verification
            $this->logAudit('email_verified', 'user', $user['id']);
            
            $this->setFlash('success', 'Email verified successfully. You can now log in.');
            $this->redirect('/login');
            
        } catch (Exception $e) {
            $this->logError($e->getMessage());
            $this->setFlash('error', 'An error occurred. Please try again.');
            $this->redirect('/login');
        }
    }
    
    /**
     * Handle social authentication
     */
    public function socialConnect($platform) {
        try {
            $config = $GLOBALS['API_CREDENTIALS'][$platform] ?? null;
            
            if (!$config) {
                throw new Exception('Invalid platform');
            }
            
            // Generate state token
            $state = bin2hex(random_bytes(16));
            $_SESSION['oauth_state'] = $state;
            
            // Build authorization URL
            $params = [
                'client_id' => $config['client_id'],
                'redirect_uri' => $config['redirect_uri'],
                'scope' => implode(' ', $config['scopes']),
                'state' => $state,
                'response_type' => 'code'
            ];
            
            $url = $config['authUrl'] . '?' . http_build_query($params);
            
            $this->redirect($url);
            
        } catch (Exception $e) {
            $this->logError($e->getMessage());
            $this->setFlash('error', 'Authentication failed. Please try again.');
            $this->redirect('/login');
        }
    }
    
    /**
     * Handle social authentication callback
     */
    public function socialCallback($platform) {
        try {
            $config = $GLOBALS['API_CREDENTIALS'][$platform] ?? null;
            
            if (!$config) {
                throw new Exception('Invalid platform');
            }
            
            // Verify state
            $state = $_GET['state'] ?? '';
            if (!$state || $state !== $_SESSION['oauth_state']) {
                throw new Exception('Invalid state');
            }
            
            // Get access token
            $code = $_GET['code'] ?? '';
            if (!$code) {
                throw new Exception('Authorization code missing');
            }
            
            $token = $this->getAccessToken($platform, $code);
            
            // Get user info
            $userInfo = $this->getSocialUserInfo($platform, $token);
            
            // Find or create user
            $user = $this->userModel->findByEmail($userInfo['email']);
            
            if (!$user) {
                // Create new user
                $userId = $this->userModel->create([
                    'first_name' => $userInfo['first_name'],
                    'last_name' => $userInfo['last_name'],
                    'email' => $userInfo['email'],
                    'password' => null,
                    'status' => 'active'
                ]);
                
                $user = $this->userModel->find($userId);
            }
            
            // Link social account
            $this->linkSocialAccount($user['id'], $platform, $userInfo['id'], $token);
            
            // Set session
            $_SESSION['user_id'] = $user['id'];
            
            // Log social login
            $this->logAudit('social_login', 'user', $user['id'], [
                'platform' => $platform
            ]);
            
            $this->redirect('/dashboard');
            
        } catch (Exception $e) {
            $this->logError($e->getMessage());
            $this->setFlash('error', 'Authentication failed. Please try again.');
            $this->redirect('/login');
        }
    }
    
    /**
     * Check if login is blocked
     */
    private function isLoginBlocked($email) {
        try {
            $stmt = $this->db->prepare('
                SELECT COUNT(*) 
                FROM login_attempts 
                WHERE email = ? 
                AND created_at > DATE_SUB(NOW(), INTERVAL 15 MINUTE)
            ');
            $stmt->execute([$email]);
            return $stmt->fetchColumn() >= MAX_LOGIN_ATTEMPTS;
        } catch (PDOException $e) {
            $this->logError($e->getMessage());
            return false;
        }
    }
    
    /**
     * Increment login attempts
     */
    private function incrementLoginAttempts($email) {
        try {
            $stmt = $this->db->prepare('
                INSERT INTO login_attempts (
                    email, 
                    ip_address, 
                    created_at
                ) VALUES (?, ?, NOW())
            ');
            $stmt->execute([$email, getClientIP()]);
        } catch (PDOException $e) {
            $this->logError($e->getMessage());
        }
    }
    
    /**
     * Clear login attempts
     */
    private function clearLoginAttempts($email) {
        try {
            $stmt = $this->db->prepare('
                DELETE FROM login_attempts 
                WHERE email = ?
            ');
            $stmt->execute([$email]);
        } catch (PDOException $e) {
            $this->logError($e->getMessage());
        }
    }
    
    /**
     * Generate remember token
     */
    private function generateRememberToken() {
        return bin2hex(random_bytes(32));
    }
    
    /**
     * Send verification email
     */
    private function sendVerificationEmail($userId) {
        try {
            $user = $this->userModel->find($userId);
            $verificationUrl = APP_URL . '/verify-email/' . $user['verification_token'];
            
            $subject = 'Verify your email address';
            $body = "
                <h1>Welcome to " . APP_NAME . "!</h1>
                <p>Please click the link below to verify your email address:</p>
                <p><a href=\"$verificationUrl\">$verificationUrl</a></p>
                <p>This link will expire in 24 hours.</p>
            ";
            
            sendEmail($user['email'], $subject, $body);
            
        } catch (Exception $e) {
            $this->logError($e->getMessage());
        }
    }
    
    /**
     * Send password reset email
     */
    private function sendPasswordResetEmail($userId, $token) {
        try {
            $user = $this->userModel->find($userId);
            $resetUrl = APP_URL . '/reset-password/' . $token;
            
            $subject = 'Reset your password';
            $body = "
                <h1>Password Reset Request</h1>
                <p>You recently requested to reset your password. Click the link below to proceed:</p>
                <p><a href=\"$resetUrl\">$resetUrl</a></p>
                <p>This link will expire in 1 hour.</p>
                <p>If you didn't request this, please ignore this email.</p>
            ";
            
            sendEmail($user['email'], $subject, $body);
            
        } catch (Exception $e) {
            $this->logError($e->getMessage());
        }
    }
}
?>
