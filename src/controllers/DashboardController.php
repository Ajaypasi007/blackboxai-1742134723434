<?php
/**
 * Dashboard Controller
 * Handles dashboard functionality and views
 */
class DashboardController extends BaseController {
    private $userModel;
    private $postModel;
    private $socialAccountModel;
    private $analyticsModel;
    
    public function __construct() {
        parent::__construct();
        
        // Require authentication
        if (!$this->isAuthenticated()) {
            $this->redirect('/login');
        }
        
        $this->userModel = new User();
        $this->postModel = new Post();
        $this->socialAccountModel = new SocialAccount();
        $this->analyticsModel = new Analytics();
    }
    
    /**
     * Display dashboard overview
     */
    public function index() {
        try {
            // Get user's social accounts
            $accounts = $this->socialAccountModel->getUserAccounts($this->user['id']);
            
            // Get recent posts
            $posts = $this->postModel->getUserPosts($this->user['id'], [], 1, 5)['posts'];
            
            // Get analytics overview
            $analytics = $this->analyticsModel->getUserAnalytics(
                $this->user['id'],
                date('Y-m-d', strtotime('-30 days')),
                date('Y-m-d')
            );
            
            // Get notifications
            $notifications = $this->getNotifications();
            
            $this->render('dashboard/index', [
                'pageTitle' => 'Dashboard - ' . APP_NAME,
                'accounts' => $accounts,
                'posts' => $posts,
                'analytics' => $analytics,
                'notifications' => $notifications
            ]);
        } catch (Exception $e) {
            $this->logError($e->getMessage());
            $this->setFlash('error', 'Failed to load dashboard data');
            $this->redirect('/dashboard');
        }
    }
    
    /**
     * Display settings page
     */
    public function settings() {
        try {
            // Get user's subscription info
            $subscription = [
                'plan' => $this->user['subscription_plan'],
                'status' => $this->user['subscription_status'],
                'ends_at' => $this->user['subscription_ends_at']
            ];
            
            // Get notification preferences
            $preferences = $this->userModel->getNotificationPreferences($this->user['id']);
            
            // Get recent activity
            $activity = $this->userModel->getRecentActivity($this->user['id']);
            
            $this->render('dashboard/settings', [
                'pageTitle' => 'Settings - ' . APP_NAME,
                'subscription' => $subscription,
                'preferences' => $preferences,
                'activity' => $activity
            ]);
        } catch (Exception $e) {
            $this->logError($e->getMessage());
            $this->setFlash('error', 'Failed to load settings');
            $this->redirect('/dashboard');
        }
    }
    
    /**
     * Update profile settings
     */
    public function updateProfile() {
        if (!$this->validateCSRF()) {
            $this->setFlash('error', 'Invalid request');
            $this->redirect('/dashboard/settings');
        }
        
        $data = [
            'first_name' => filter_input(INPUT_POST, 'first_name', FILTER_SANITIZE_STRING),
            'last_name' => filter_input(INPUT_POST, 'last_name', FILTER_SANITIZE_STRING),
            'email' => filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL),
            'timezone' => filter_input(INPUT_POST, 'timezone', FILTER_SANITIZE_STRING)
        ];
        
        try {
            // Validate input
            $errors = $this->validate($data, [
                'first_name' => 'required|max:50',
                'last_name' => 'required|max:50',
                'email' => 'required|email',
                'timezone' => 'required'
            ]);
            
            if (!empty($errors)) {
                $this->setFlash('error', 'Invalid input');
                $this->redirect('/dashboard/settings');
            }
            
            // Update user
            $this->userModel->update($this->user['id'], $data);
            
            $this->setFlash('success', 'Profile updated successfully');
            $this->redirect('/dashboard/settings');
        } catch (Exception $e) {
            $this->logError($e->getMessage());
            $this->setFlash('error', 'Failed to update profile');
            $this->redirect('/dashboard/settings');
        }
    }
    
    /**
     * Update notification preferences
     */
    public function updateNotifications() {
        if (!$this->validateCSRF()) {
            $this->setFlash('error', 'Invalid request');
            $this->redirect('/dashboard/settings');
        }
        
        $preferences = [
            'email' => isset($_POST['email_notifications']),
            'desktop' => isset($_POST['desktop_notifications'])
        ];
        
        try {
            $this->userModel->updateNotificationPreferences($this->user['id'], $preferences);
            
            $this->setFlash('success', 'Notification preferences updated');
            $this->redirect('/dashboard/settings');
        } catch (Exception $e) {
            $this->logError($e->getMessage());
            $this->setFlash('error', 'Failed to update preferences');
            $this->redirect('/dashboard/settings');
        }
    }
    
    /**
     * Display notifications page
     */
    public function notifications() {
        try {
            $notifications = $this->getNotifications(50); // Get last 50 notifications
            
            $this->render('dashboard/notifications', [
                'pageTitle' => 'Notifications - ' . APP_NAME,
                'notifications' => $notifications
            ]);
        } catch (Exception $e) {
            $this->logError($e->getMessage());
            $this->setFlash('error', 'Failed to load notifications');
            $this->redirect('/dashboard');
        }
    }
    
    /**
     * Mark notification as read
     */
    public function markNotificationRead($id) {
        try {
            $stmt = $this->db->prepare('
                UPDATE notifications 
                SET read_at = NOW() 
                WHERE id = ? AND user_id = ?
            ');
            $stmt->execute([$id, $this->user['id']]);
            
            if ($this->isAjaxRequest()) {
                $this->jsonResponse(['success' => true]);
            } else {
                $this->redirect('/dashboard/notifications');
            }
        } catch (Exception $e) {
            $this->logError($e->getMessage());
            if ($this->isAjaxRequest()) {
                $this->jsonResponse(['error' => 'Failed to mark notification as read'], 500);
            } else {
                $this->setFlash('error', 'Failed to mark notification as read');
                $this->redirect('/dashboard/notifications');
            }
        }
    }
    
    /**
     * Mark all notifications as read
     */
    public function markAllNotificationsRead() {
        try {
            $stmt = $this->db->prepare('
                UPDATE notifications 
                SET read_at = NOW() 
                WHERE user_id = ? AND read_at IS NULL
            ');
            $stmt->execute([$this->user['id']]);
            
            if ($this->isAjaxRequest()) {
                $this->jsonResponse(['success' => true]);
            } else {
                $this->redirect('/dashboard/notifications');
            }
        } catch (Exception $e) {
            $this->logError($e->getMessage());
            if ($this->isAjaxRequest()) {
                $this->jsonResponse(['error' => 'Failed to mark notifications as read'], 500);
            } else {
                $this->setFlash('error', 'Failed to mark notifications as read');
                $this->redirect('/dashboard/notifications');
            }
        }
    }
    
    /**
     * Get user's notifications
     * @param int $limit Optional limit
     * @return array Notifications
     */
    private function getNotifications($limit = 10) {
        try {
            $stmt = $this->db->prepare('
                SELECT * FROM notifications 
                WHERE user_id = ? 
                ORDER BY created_at DESC 
                LIMIT ?
            ');
            $stmt->execute([$this->user['id'], $limit]);
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            $this->logError($e->getMessage());
            return [];
        }
    }
    
    /**
     * Delete account
     */
    public function deleteAccount() {
        if (!$this->validateCSRF()) {
            $this->setFlash('error', 'Invalid request');
            $this->redirect('/dashboard/settings');
        }
        
        $password = $_POST['password'] ?? '';
        
        try {
            // Verify password
            if (!password_verify($password, $this->user['password'])) {
                $this->setFlash('error', 'Invalid password');
                $this->redirect('/dashboard/settings');
            }
            
            // Delete user
            $this->userModel->delete($this->user['id']);
            
            // Log out
            session_destroy();
            
            $this->setFlash('success', 'Your account has been deleted');
            $this->redirect('/');
        } catch (Exception $e) {
            $this->logError($e->getMessage());
            $this->setFlash('error', 'Failed to delete account');
            $this->redirect('/dashboard/settings');
        }
    }
}
?>
