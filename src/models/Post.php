<?php
/**
 * Post Model
 * Handles social media post operations
 */
class Post {
    private $db;
    
    public function __construct() {
        $this->db = getDB();
    }
    
    /**
     * Find post by ID
     * @param int $id Post ID
     * @return array|null Post data
     */
    public function find($id) {
        try {
            $stmt = $this->db->prepare('
                SELECT p.*, 
                       GROUP_CONCAT(pp.social_account_id) as platform_ids,
                       GROUP_CONCAT(pp.status) as platform_statuses,
                       GROUP_CONCAT(pp.platform_post_id) as platform_post_ids
                FROM posts p
                LEFT JOIN post_platforms pp ON p.id = pp.post_id
                WHERE p.id = ?
                GROUP BY p.id
                LIMIT 1
            ');
            $stmt->execute([$id]);
            $post = $stmt->fetch();
            
            if ($post) {
                $post['platforms'] = $this->formatPlatformData($post);
                $post['media_urls'] = json_decode($post['media_urls'], true) ?? [];
            }
            
            return $post;
        } catch (PDOException $e) {
            error_log('Failed to find post: ' . $e->getMessage());
            throw new Exception('Failed to find post');
        }
    }
    
    /**
     * Get user's posts
     * @param int $userId User ID
     * @param array $filters Optional filters
     * @param int $page Page number
     * @param int $perPage Items per page
     * @return array Posts and pagination data
     */
    public function getUserPosts($userId, $filters = [], $page = 1, $perPage = 10) {
        try {
            $where = ['p.user_id = ?'];
            $params = [$userId];
            
            // Apply filters
            if (!empty($filters['status'])) {
                $where[] = 'p.status = ?';
                $params[] = $filters['status'];
            }
            
            if (!empty($filters['platform'])) {
                $where[] = 'EXISTS (
                    SELECT 1 FROM post_platforms pp2 
                    JOIN social_accounts sa ON pp2.social_account_id = sa.id 
                    WHERE pp2.post_id = p.id AND sa.platform = ?
                )';
                $params[] = $filters['platform'];
            }
            
            if (!empty($filters['date_range'])) {
                switch ($filters['date_range']) {
                    case 'today':
                        $where[] = 'DATE(p.created_at) = CURDATE()';
                        break;
                    case 'week':
                        $where[] = 'p.created_at >= DATE_SUB(NOW(), INTERVAL 1 WEEK)';
                        break;
                    case 'month':
                        $where[] = 'p.created_at >= DATE_SUB(NOW(), INTERVAL 1 MONTH)';
                        break;
                }
            }
            
            if (!empty($filters['search'])) {
                $where[] = 'p.content LIKE ?';
                $params[] = '%' . $filters['search'] . '%';
            }
            
            // Calculate pagination
            $offset = ($page - 1) * $perPage;
            $params[] = $perPage;
            $params[] = $offset;
            
            // Get total count
            $countStmt = $this->db->prepare('
                SELECT COUNT(DISTINCT p.id) 
                FROM posts p
                WHERE ' . implode(' AND ', $where)
            );
            $countStmt->execute(array_slice($params, 0, -2));
            $total = $countStmt->fetchColumn();
            
            // Get posts
            $stmt = $this->db->prepare('
                SELECT p.*, 
                       GROUP_CONCAT(pp.social_account_id) as platform_ids,
                       GROUP_CONCAT(pp.status) as platform_statuses,
                       GROUP_CONCAT(pp.platform_post_id) as platform_post_ids
                FROM posts p
                LEFT JOIN post_platforms pp ON p.id = pp.post_id
                WHERE ' . implode(' AND ', $where) . '
                GROUP BY p.id
                ORDER BY p.created_at DESC
                LIMIT ? OFFSET ?
            ');
            $stmt->execute($params);
            
            $posts = [];
            while ($post = $stmt->fetch()) {
                $post['platforms'] = $this->formatPlatformData($post);
                $post['media_urls'] = json_decode($post['media_urls'], true) ?? [];
                $posts[] = $post;
            }
            
            return [
                'posts' => $posts,
                'total' => $total,
                'pages' => ceil($total / $perPage),
                'current_page' => $page,
                'per_page' => $perPage
            ];
        } catch (PDOException $e) {
            error_log('Failed to get user posts: ' . $e->getMessage());
            throw new Exception('Failed to get user posts');
        }
    }
    
    /**
     * Create new post
     * @param array $data Post data
     * @return int Post ID
     */
    public function create($data) {
        try {
            $this->db->beginTransaction();
            
            // Insert post
            $stmt = $this->db->prepare('
                INSERT INTO posts (
                    user_id,
                    content,
                    media_urls,
                    scheduled_time,
                    status,
                    approval_required,
                    created_at
                ) VALUES (?, ?, ?, ?, ?, ?, NOW())
            ');
            
            $stmt->execute([
                $data['user_id'],
                $data['content'],
                json_encode($data['media_urls'] ?? []),
                $data['scheduled_time'] ?? null,
                $data['status'] ?? 'draft',
                $data['approval_required'] ?? false
            ]);
            
            $postId = $this->db->lastInsertId();
            
            // Insert platform associations
            if (!empty($data['platforms'])) {
                $platformStmt = $this->db->prepare('
                    INSERT INTO post_platforms (
                        post_id,
                        social_account_id,
                        created_at
                    ) VALUES (?, ?, NOW())
                ');
                
                foreach ($data['platforms'] as $accountId) {
                    $platformStmt->execute([$postId, $accountId]);
                }
            }
            
            $this->db->commit();
            return $postId;
        } catch (PDOException $e) {
            $this->db->rollBack();
            error_log('Failed to create post: ' . $e->getMessage());
            throw new Exception('Failed to create post');
        }
    }
    
    /**
     * Update post
     * @param int $id Post ID
     * @param array $data Post data
     * @return bool Success
     */
    public function update($id, $data) {
        try {
            $this->db->beginTransaction();
            
            // Update post
            $fields = [];
            $values = [];
            
            foreach ($data as $field => $value) {
                if ($field === 'platforms') continue;
                if ($field === 'media_urls') {
                    $value = json_encode($value);
                }
                $fields[] = "$field = ?";
                $values[] = $value;
            }
            
            $values[] = $id;
            
            $stmt = $this->db->prepare('
                UPDATE posts 
                SET ' . implode(', ', $fields) . ' 
                WHERE id = ?
            ');
            
            $stmt->execute($values);
            
            // Update platforms if provided
            if (isset($data['platforms'])) {
                // Remove existing platforms
                $stmt = $this->db->prepare('
                    DELETE FROM post_platforms 
                    WHERE post_id = ?
                ');
                $stmt->execute([$id]);
                
                // Insert new platforms
                if (!empty($data['platforms'])) {
                    $stmt = $this->db->prepare('
                        INSERT INTO post_platforms (
                            post_id,
                            social_account_id,
                            created_at
                        ) VALUES (?, ?, NOW())
                    ');
                    
                    foreach ($data['platforms'] as $accountId) {
                        $stmt->execute([$id, $accountId]);
                    }
                }
            }
            
            $this->db->commit();
            return true;
        } catch (PDOException $e) {
            $this->db->rollBack();
            error_log('Failed to update post: ' . $e->getMessage());
            throw new Exception('Failed to update post');
        }
    }
    
    /**
     * Delete post
     * @param int $id Post ID
     * @return bool Success
     */
    public function delete($id) {
        try {
            $this->db->beginTransaction();
            
            // Delete platform associations
            $stmt = $this->db->prepare('
                DELETE FROM post_platforms 
                WHERE post_id = ?
            ');
            $stmt->execute([$id]);
            
            // Delete post
            $stmt = $this->db->prepare('
                DELETE FROM posts 
                WHERE id = ?
            ');
            $stmt->execute([$id]);
            
            $this->db->commit();
            return true;
        } catch (PDOException $e) {
            $this->db->rollBack();
            error_log('Failed to delete post: ' . $e->getMessage());
            throw new Exception('Failed to delete post');
        }
    }
    
    /**
     * Update post status
     * @param int $id Post ID
     * @param string $status New status
     * @param string $platformPostId Optional platform post ID
     * @param array $engagementData Optional engagement data
     * @return bool Success
     */
    public function updateStatus($id, $status, $platformPostId = null, $engagementData = null) {
        try {
            $this->db->beginTransaction();
            
            // Update post status
            $stmt = $this->db->prepare('
                UPDATE posts 
                SET status = ?,
                    published_at = CASE WHEN ? = "published" THEN NOW() ELSE published_at END
                WHERE id = ?
            ');
            $stmt->execute([$status, $status, $id]);
            
            // Update platform status if provided
            if ($platformPostId) {
                $stmt = $this->db->prepare('
                    UPDATE post_platforms 
                    SET status = ?,
                        platform_post_id = ?,
                        engagement_data = ?
                    WHERE post_id = ?
                ');
                $stmt->execute([
                    $status,
                    $platformPostId,
                    $engagementData ? json_encode($engagementData) : null,
                    $id
                ]);
            }
            
            $this->db->commit();
            return true;
        } catch (PDOException $e) {
            $this->db->rollBack();
            error_log('Failed to update post status: ' . $e->getMessage());
            throw new Exception('Failed to update post status');
        }
    }
    
    /**
     * Get scheduled posts
     * @return array Posts
     */
    public function getScheduledPosts() {
        try {
            $stmt = $this->db->prepare('
                SELECT p.*, 
                       GROUP_CONCAT(pp.social_account_id) as platform_ids
                FROM posts p
                LEFT JOIN post_platforms pp ON p.id = pp.post_id
                WHERE p.status = "scheduled"
                AND p.scheduled_time <= NOW()
                GROUP BY p.id
            ');
            $stmt->execute();
            
            $posts = [];
            while ($post = $stmt->fetch()) {
                $post['platforms'] = explode(',', $post['platform_ids']);
                $post['media_urls'] = json_decode($post['media_urls'], true) ?? [];
                $posts[] = $post;
            }
            
            return $posts;
        } catch (PDOException $e) {
            error_log('Failed to get scheduled posts: ' . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Format platform data from database result
     * @param array $post Post data
     * @return array Formatted platform data
     */
    private function formatPlatformData($post) {
        if (empty($post['platform_ids'])) {
            return [];
        }
        
        $platformIds = explode(',', $post['platform_ids']);
        $statuses = explode(',', $post['platform_statuses']);
        $postIds = explode(',', $post['platform_post_ids']);
        
        $platforms = [];
        foreach ($platformIds as $i => $id) {
            $platforms[] = [
                'account_id' => $id,
                'status' => $statuses[$i] ?? 'pending',
                'post_id' => $postIds[$i] ?? null
            ];
        }
        
        return $platforms;
    }
}
?>
