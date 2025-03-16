<?php
/**
 * Social Account Model
 * Handles social media account connections and operations
 */
class SocialAccount {
    private $db;
    
    public function __construct() {
        $this->db = getDB();
    }
    
    /**
     * Find account by ID
     * @param int $id Account ID
     * @return array|null Account data
     */
    public function find($id) {
        try {
            $stmt = $this->db->prepare('
                SELECT * FROM social_accounts 
                WHERE id = ? 
                LIMIT 1
            ');
            $stmt->execute([$id]);
            return $stmt->fetch();
        } catch (PDOException $e) {
            error_log('Failed to find social account: ' . $e->getMessage());
            throw new Exception('Failed to find social account');
        }
    }
    
    /**
     * Find account by platform and account ID
     * @param string $platform Platform name
     * @param string $accountId Platform account ID
     * @return array|null Account data
     */
    public function findByPlatformAccount($platform, $accountId) {
        try {
            $stmt = $this->db->prepare('
                SELECT * FROM social_accounts 
                WHERE platform = ? 
                AND account_id = ? 
                LIMIT 1
            ');
            $stmt->execute([$platform, $accountId]);
            return $stmt->fetch();
        } catch (PDOException $e) {
            error_log('Failed to find social account: ' . $e->getMessage());
            throw new Exception('Failed to find social account');
        }
    }
    
    /**
     * Get user's accounts
     * @param int $userId User ID
     * @param string $platform Optional platform filter
     * @return array Accounts
     */
    public function getUserAccounts($userId, $platform = null) {
        try {
            $sql = 'SELECT * FROM social_accounts WHERE user_id = ?';
            $params = [$userId];
            
            if ($platform) {
                $sql .= ' AND platform = ?';
                $params[] = $platform;
            }
            
            $sql .= ' ORDER BY platform, account_name';
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute($params);
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log('Failed to get user accounts: ' . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Create new social account connection
     * @param array $data Account data
     * @return int Account ID
     */
    public function create($data) {
        try {
            $stmt = $this->db->prepare('
                INSERT INTO social_accounts (
                    user_id,
                    platform,
                    account_id,
                    account_name,
                    access_token,
                    refresh_token,
                    token_expires,
                    status,
                    profile_url,
                    created_at
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())
            ');
            
            $stmt->execute([
                $data['user_id'],
                $data['platform'],
                $data['account_id'],
                $data['account_name'],
                $data['access_token'],
                $data['refresh_token'] ?? null,
                $data['token_expires'] ?? null,
                $data['status'] ?? 'active',
                $data['profile_url'] ?? null
            ]);
            
            return $this->db->lastInsertId();
        } catch (PDOException $e) {
            error_log('Failed to create social account: ' . $e->getMessage());
            throw new Exception('Failed to create social account');
        }
    }
    
    /**
     * Update social account
     * @param int $id Account ID
     * @param array $data Account data
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
                UPDATE social_accounts 
                SET ' . implode(', ', $fields) . ' 
                WHERE id = ?
            ');
            
            return $stmt->execute($values);
        } catch (PDOException $e) {
            error_log('Failed to update social account: ' . $e->getMessage());
            throw new Exception('Failed to update social account');
        }
    }
    
    /**
     * Update access token
     * @param int $id Account ID
     * @param string $accessToken New access token
     * @param string $refreshToken Optional refresh token
     * @param string $expires Optional expiration timestamp
     * @return bool Success
     */
    public function updateToken($id, $accessToken, $refreshToken = null, $expires = null) {
        try {
            $data = [
                'access_token' => $accessToken,
                'refresh_token' => $refreshToken,
                'token_expires' => $expires
            ];
            
            return $this->update($id, $data);
        } catch (Exception $e) {
            error_log('Failed to update token: ' . $e->getMessage());
            throw new Exception('Failed to update token');
        }
    }
    
    /**
     * Disconnect social account
     * @param int $id Account ID
     * @return bool Success
     */
    public function disconnect($id) {
        try {
            $stmt = $this->db->prepare('
                UPDATE social_accounts 
                SET status = "inactive" 
                WHERE id = ?
            ');
            return $stmt->execute([$id]);
        } catch (PDOException $e) {
            error_log('Failed to disconnect account: ' . $e->getMessage());
            throw new Exception('Failed to disconnect account');
        }
    }
    
    /**
     * Check if token needs refresh
     * @param int $id Account ID
     * @return bool Needs refresh
     */
    public function needsTokenRefresh($id) {
        try {
            $account = $this->find($id);
            
            if (!$account || !$account['token_expires']) {
                return false;
            }
            
            // Check if token expires in less than 1 hour
            $expires = strtotime($account['token_expires']);
            return $expires - time() < 3600;
        } catch (Exception $e) {
            error_log('Failed to check token refresh: ' . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Get account stats
     * @param int $id Account ID
     * @return array Stats
     */
    public function getStats($id) {
        try {
            $stmt = $this->db->prepare('
                SELECT 
                    metric_type,
                    metric_value
                FROM analytics 
                WHERE social_account_id = ? 
                AND date = CURDATE()
            ');
            $stmt->execute([$id]);
            
            $stats = [];
            while ($row = $stmt->fetch()) {
                $stats[$row['metric_type']] = $row['metric_value'];
            }
            
            return $stats;
        } catch (PDOException $e) {
            error_log('Failed to get account stats: ' . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Get account engagement rate
     * @param int $id Account ID
     * @return float Engagement rate
     */
    public function getEngagementRate($id) {
        try {
            $stats = $this->getStats($id);
            
            if (empty($stats['followers']) || empty($stats['engagement'])) {
                return 0;
            }
            
            return ($stats['engagement'] / $stats['followers']) * 100;
        } catch (Exception $e) {
            error_log('Failed to calculate engagement rate: ' . $e->getMessage());
            return 0;
        }
    }
    
    /**
     * Get account growth
     * @param int $id Account ID
     * @param int $days Number of days
     * @return array Growth data
     */
    public function getGrowth($id, $days = 30) {
        try {
            $stmt = $this->db->prepare('
                SELECT 
                    date,
                    metric_type,
                    metric_value
                FROM analytics 
                WHERE social_account_id = ? 
                AND date >= DATE_SUB(CURDATE(), INTERVAL ? DAY)
                ORDER BY date ASC
            ');
            $stmt->execute([$id, $days]);
            
            $growth = [];
            while ($row = $stmt->fetch()) {
                $growth[$row['date']][$row['metric_type']] = $row['metric_value'];
            }
            
            return $growth;
        } catch (PDOException $e) {
            error_log('Failed to get account growth: ' . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Get platform settings
     * @param string $platform Platform name
     * @return array Settings
     */
    public function getPlatformSettings($platform) {
        global $PLATFORM_SETTINGS;
        return $PLATFORM_SETTINGS[$platform] ?? [];
    }
}
?>
