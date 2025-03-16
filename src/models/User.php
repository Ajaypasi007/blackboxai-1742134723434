<?php
/**
 * User Model
 * Handles user-related database operations
 */
class User {
    private $db;
    
    public function __construct() {
        $this->db = getDB();
    }
    
    /**
     * Find user by ID
     * @param int $id User ID
     * @return array|null User data
     */
    public function find($id) {
        try {
            $stmt = $this->db->prepare('
                SELECT * FROM users 
                WHERE id = ? 
                AND deleted_at IS NULL 
                LIMIT 1
            ');
            $stmt->execute([$id]);
            return $stmt->fetch();
        } catch (PDOException $e) {
            error_log('Failed to find user: ' . $e->getMessage());
            throw new Exception('Failed to find user');
        }
    }
    
    /**
     * Find user by email
     * @param string $email Email address
     * @return array|null User data
     */
    public function findByEmail($email) {
        try {
            $stmt = $this->db->prepare('
                SELECT * FROM users 
                WHERE email = ? 
                AND deleted_at IS NULL 
                LIMIT 1
            ');
            $stmt->execute([$email]);
            return $stmt->fetch();
        } catch (PDOException $e) {
            error_log('Failed to find user by email: ' . $e->getMessage());
            throw new Exception('Failed to find user');
        }
    }
    
    /**
     * Find user by verification token
     * @param string $token Verification token
     * @return array|null User data
     */
    public function findByVerificationToken($token) {
        try {
            $stmt = $this->db->prepare('
                SELECT * FROM users 
                WHERE verification_token = ? 
                AND status = "pending" 
                AND deleted_at IS NULL 
                LIMIT 1
            ');
            $stmt->execute([$token]);
            return $stmt->fetch();
        } catch (PDOException $e) {
            error_log('Failed to find user by verification token: ' . $e->getMessage());
            throw new Exception('Failed to find user');
        }
    }
    
    /**
     * Create new user
     * @param array $data User data
     * @return int User ID
     */
    public function create($data) {
        try {
            $stmt = $this->db->prepare('
                INSERT INTO users (
                    first_name,
                    last_name,
                    email,
                    password,
                    verification_token,
                    status,
                    created_at
                ) VALUES (?, ?, ?, ?, ?, ?, NOW())
            ');
            
            $stmt->execute([
                $data['first_name'],
                $data['last_name'],
                $data['email'],
                $data['password'],
                $data['verification_token'] ?? null,
                $data['status'] ?? 'pending'
            ]);
            
            return $this->db->lastInsertId();
        } catch (PDOException $e) {
            error_log('Failed to create user: ' . $e->getMessage());
            throw new Exception('Failed to create user');
        }
    }
    
    /**
     * Update user
     * @param int $id User ID
     * @param array $data User data
     * @return bool Success
     */
    public function update($id, $data) {
        try {
            $fields = [];
            $values = [];
            
            foreach ($data as $field => $value) {
                $fields[] = "$field = ?";
                $values[] = $value;
            }
            
            $values[] = $id;
            
            $stmt = $this->db->prepare('
                UPDATE users 
                SET ' . implode(', ', $fields) . ' 
                WHERE id = ?
            ');
            
            return $stmt->execute($values);
        } catch (PDOException $e) {
            error_log('Failed to update user: ' . $e->getMessage());
            throw new Exception('Failed to update user');
        }
    }
    
    /**
     * Update user password
     * @param int $id User ID
     * @param string $password New password
     * @return bool Success
     */
    public function updatePassword($id, $password) {
        try {
            $stmt = $this->db->prepare('
                UPDATE users 
                SET password = ? 
                WHERE id = ?
            ');
            return $stmt->execute([
                password_hash($password, PASSWORD_DEFAULT),
                $id
            ]);
        } catch (PDOException $e) {
            error_log('Failed to update password: ' . $e->getMessage());
            throw new Exception('Failed to update password');
        }
    }
    
    /**
     * Update remember token
     * @param int $id User ID
     * @param string $token Remember token
     * @return bool Success
     */
    public function updateRememberToken($id, $token) {
        try {
            $stmt = $this->db->prepare('
                UPDATE users 
                SET remember_token = ? 
                WHERE id = ?
            ');
            return $stmt->execute([$token, $id]);
        } catch (PDOException $e) {
            error_log('Failed to update remember token: ' . $e->getMessage());
            throw new Exception('Failed to update remember token');
        }
    }
    
    /**
     * Update last login timestamp
     * @param int $id User ID
     * @return bool Success
     */
    public function updateLastLogin($id) {
        try {
            $stmt = $this->db->prepare('
                UPDATE users 
                SET last_login = NOW() 
                WHERE id = ?
            ');
            return $stmt->execute([$id]);
        } catch (PDOException $e) {
            error_log('Failed to update last login: ' . $e->getMessage());
            throw new Exception('Failed to update last login');
        }
    }
    
    /**
     * Activate user account
     * @param int $id User ID
     * @return bool Success
     */
    public function activate($id) {
        try {
            $stmt = $this->db->prepare('
                UPDATE users 
                SET status = "active", 
                    verification_token = NULL 
                WHERE id = ?
            ');
            return $stmt->execute([$id]);
        } catch (PDOException $e) {
            error_log('Failed to activate user: ' . $e->getMessage());
            throw new Exception('Failed to activate user');
        }
    }
    
    /**
     * Soft delete user
     * @param int $id User ID
     * @return bool Success
     */
    public function delete($id) {
        try {
            $stmt = $this->db->prepare('
                UPDATE users 
                SET status = "deleted", 
                    deleted_at = NOW() 
                WHERE id = ?
            ');
            return $stmt->execute([$id]);
        } catch (PDOException $e) {
            error_log('Failed to delete user: ' . $e->getMessage());
            throw new Exception('Failed to delete user');
        }
    }
    
    /**
     * Get user's subscription features
     * @param int $id User ID
     * @return array Features
     */
    public function getFeatures($id) {
        try {
            $user = $this->find($id);
            return getPlanFeatures($user['subscription_plan']);
        } catch (Exception $e) {
            error_log('Failed to get user features: ' . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Check if user has specific feature
     * @param int $id User ID
     * @param string $feature Feature name
     * @return bool Has feature
     */
    public function hasFeature($id, $feature) {
        $features = $this->getFeatures($id);
        return isset($features[$feature]) && $features[$feature];
    }
    
    /**
     * Get user's connected social accounts
     * @param int $id User ID
     * @return array Social accounts
     */
    public function getSocialAccounts($id) {
        try {
            $stmt = $this->db->prepare('
                SELECT * FROM social_accounts 
                WHERE user_id = ? 
                AND status = "active"
            ');
            $stmt->execute([$id]);
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log('Failed to get social accounts: ' . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Get user's notification preferences
     * @param int $id User ID
     * @return array Preferences
     */
    public function getNotificationPreferences($id) {
        try {
            $user = $this->find($id);
            return [
                'email' => $user['email_notifications'],
                'desktop' => $user['desktop_notifications']
            ];
        } catch (Exception $e) {
            error_log('Failed to get notification preferences: ' . $e->getMessage());
            return [
                'email' => true,
                'desktop' => true
            ];
        }
    }
    
    /**
     * Update notification preferences
     * @param int $id User ID
     * @param array $preferences Preferences
     * @return bool Success
     */
    public function updateNotificationPreferences($id, $preferences) {
        try {
            return $this->update($id, [
                'email_notifications' => $preferences['email'] ?? true,
                'desktop_notifications' => $preferences['desktop'] ?? true
            ]);
        } catch (Exception $e) {
            error_log('Failed to update notification preferences: ' . $e->getMessage());
            throw new Exception('Failed to update notification preferences');
        }
    }
    
    /**
     * Get user's recent activity
     * @param int $id User ID
     * @param int $limit Limit
     * @return array Activity logs
     */
    public function getRecentActivity($id, $limit = 10) {
        try {
            $stmt = $this->db->prepare('
                SELECT * FROM audit_logs 
                WHERE user_id = ? 
                ORDER BY created_at DESC 
                LIMIT ?
            ');
            $stmt->execute([$id, $limit]);
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log('Failed to get recent activity: ' . $e->getMessage());
            return [];
        }
    }
}
?>
