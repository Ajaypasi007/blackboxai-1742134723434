<?php
/**
 * Analytics Model
 * Handles social media analytics data and reporting
 */
class Analytics {
    private $db;
    
    public function __construct() {
        $this->db = getDB();
    }
    
    /**
     * Get account analytics
     * @param int $accountId Social account ID
     * @param string $startDate Start date (Y-m-d)
     * @param string $endDate End date (Y-m-d)
     * @return array Analytics data
     */
    public function getAccountAnalytics($accountId, $startDate, $endDate) {
        try {
            $stmt = $this->db->prepare('
                SELECT 
                    date,
                    metric_type,
                    metric_value
                FROM analytics
                WHERE social_account_id = ?
                AND date BETWEEN ? AND ?
                ORDER BY date ASC
            ');
            $stmt->execute([$accountId, $startDate, $endDate]);
            
            $metrics = [];
            while ($row = $stmt->fetch()) {
                $metrics[$row['date']][$row['metric_type']] = $row['metric_value'];
            }
            
            return $this->formatAnalyticsData($metrics);
        } catch (PDOException $e) {
            error_log('Failed to get account analytics: ' . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Get post analytics
     * @param int $postId Post ID
     * @return array Analytics data
     */
    public function getPostAnalytics($postId) {
        try {
            $stmt = $this->db->prepare('
                SELECT 
                    pp.social_account_id,
                    pp.platform_post_id,
                    pp.engagement_data,
                    sa.platform
                FROM post_platforms pp
                JOIN social_accounts sa ON pp.social_account_id = sa.id
                WHERE pp.post_id = ?
            ');
            $stmt->execute([$postId]);
            
            $analytics = [
                'total_impressions' => 0,
                'total_engagement' => 0,
                'total_shares' => 0,
                'total_comments' => 0,
                'platforms' => []
            ];
            
            while ($row = $stmt->fetch()) {
                $data = json_decode($row['engagement_data'], true) ?? [];
                
                $platformMetrics = [
                    'impressions' => $data['impressions'] ?? 0,
                    'engagement' => $data['engagement'] ?? 0,
                    'shares' => $data['shares'] ?? 0,
                    'comments' => $data['comments'] ?? 0
                ];
                
                $analytics['platforms'][$row['platform']] = $platformMetrics;
                
                // Update totals
                $analytics['total_impressions'] += $platformMetrics['impressions'];
                $analytics['total_engagement'] += $platformMetrics['engagement'];
                $analytics['total_shares'] += $platformMetrics['shares'];
                $analytics['total_comments'] += $platformMetrics['comments'];
            }
            
            return $analytics;
        } catch (PDOException $e) {
            error_log('Failed to get post analytics: ' . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Get user analytics overview
     * @param int $userId User ID
     * @param string $startDate Start date (Y-m-d)
     * @param string $endDate End date (Y-m-d)
     * @return array Analytics overview
     */
    public function getUserAnalytics($userId, $startDate, $endDate) {
        try {
            // Get user's social accounts
            $stmt = $this->db->prepare('
                SELECT id, platform 
                FROM social_accounts 
                WHERE user_id = ? 
                AND status = "active"
            ');
            $stmt->execute([$userId]);
            $accounts = $stmt->fetchAll();
            
            $overview = [
                'total_followers' => 0,
                'total_engagement' => 0,
                'avg_engagement_rate' => 0,
                'total_posts' => 0,
                'platforms' => [],
                'timeline' => [
                    'labels' => [],
                    'datasets' => []
                ]
            ];
            
            foreach ($accounts as $account) {
                $analytics = $this->getAccountAnalytics(
                    $account['id'],
                    $startDate,
                    $endDate
                );
                
                // Update platform-specific metrics
                $overview['platforms'][$account['platform']] = [
                    'followers' => $analytics['current']['followers'] ?? 0,
                    'engagement' => $analytics['total']['engagement'] ?? 0,
                    'engagement_rate' => $analytics['average']['engagement_rate'] ?? 0,
                    'posts' => $analytics['total']['posts'] ?? 0
                ];
                
                // Update totals
                $overview['total_followers'] += $analytics['current']['followers'] ?? 0;
                $overview['total_engagement'] += $analytics['total']['engagement'] ?? 0;
                $overview['total_posts'] += $analytics['total']['posts'] ?? 0;
                
                // Add to timeline data
                if (!empty($analytics['timeline'])) {
                    $overview['timeline']['labels'] = array_keys($analytics['timeline']);
                    $overview['timeline']['datasets'][] = [
                        'label' => ucfirst($account['platform']),
                        'data' => array_values($analytics['timeline'])
                    ];
                }
            }
            
            // Calculate overall engagement rate
            if ($overview['total_followers'] > 0) {
                $overview['avg_engagement_rate'] = 
                    ($overview['total_engagement'] / $overview['total_followers']) * 100;
            }
            
            return $overview;
        } catch (PDOException $e) {
            error_log('Failed to get user analytics: ' . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Record metric
     * @param int $accountId Social account ID
     * @param string $metricType Metric type
     * @param int $value Metric value
     * @param string $date Optional date (Y-m-d)
     * @return bool Success
     */
    public function recordMetric($accountId, $metricType, $value, $date = null) {
        try {
            $date = $date ?: date('Y-m-d');
            
            $stmt = $this->db->prepare('
                INSERT INTO analytics (
                    social_account_id,
                    date,
                    metric_type,
                    metric_value,
                    created_at
                ) VALUES (?, ?, ?, ?, NOW())
                ON DUPLICATE KEY UPDATE
                    metric_value = VALUES(metric_value)
            ');
            
            return $stmt->execute([$accountId, $date, $metricType, $value]);
        } catch (PDOException $e) {
            error_log('Failed to record metric: ' . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Get metric configuration
     * @param string $metric Metric name
     * @return array Metric configuration
     */
    public function getMetricConfig($metric) {
        global $ANALYTICS_METRICS;
        return $ANALYTICS_METRICS[$metric] ?? null;
    }
    
    /**
     * Format analytics data
     * @param array $metrics Raw metrics data
     * @return array Formatted analytics data
     */
    private function formatAnalyticsData($metrics) {
        $formatted = [
            'current' => [],
            'total' => [],
            'average' => [],
            'timeline' => [],
            'growth' => []
        ];
        
        if (empty($metrics)) {
            return $formatted;
        }
        
        // Get metric configurations
        global $ANALYTICS_METRICS;
        
        // Sort dates
        ksort($metrics);
        $dates = array_keys($metrics);
        $firstDate = reset($dates);
        $lastDate = end($dates);
        
        foreach ($ANALYTICS_METRICS as $metric => $config) {
            // Current value (latest date)
            $formatted['current'][$metric] = 
                $metrics[$lastDate][$metric] ?? 0;
            
            // Calculate total and average
            $total = 0;
            $count = 0;
            
            foreach ($metrics as $date => $values) {
                if (isset($values[$metric])) {
                    $total += $values[$metric];
                    $count++;
                    
                    // Add to timeline
                    $formatted['timeline'][$date][$metric] = $values[$metric];
                }
            }
            
            $formatted['total'][$metric] = $total;
            $formatted['average'][$metric] = $count > 0 ? $total / $count : 0;
            
            // Calculate growth
            if (isset($metrics[$firstDate][$metric]) && isset($metrics[$lastDate][$metric])) {
                $initial = $metrics[$firstDate][$metric];
                $final = $metrics[$lastDate][$metric];
                
                if ($initial > 0) {
                    $formatted['growth'][$metric] = 
                        (($final - $initial) / $initial) * 100;
                }
            }
        }
        
        return $formatted;
    }
    
    /**
     * Generate analytics report
     * @param int $userId User ID
     * @param array $options Report options
     * @return string Report path
     */
    public function generateReport($userId, $options = []) {
        try {
            $analytics = $this->getUserAnalytics(
                $userId,
                $options['start_date'] ?? date('Y-m-d', strtotime('-30 days')),
                $options['end_date'] ?? date('Y-m-d')
            );
            
            // Generate PDF report
            $pdf = new TCPDF();
            
            // Set document information
            $pdf->SetCreator(APP_NAME);
            $pdf->SetAuthor(APP_NAME);
            $pdf->SetTitle('Analytics Report');
            
            // Add content
            $pdf->AddPage();
            
            // ... Add report content ...
            
            // Save to file
            $filename = 'analytics-report-' . date('Y-m-d-His') . '.pdf';
            $path = UPLOAD_PATH . '/reports/' . $filename;
            $pdf->Output($path, 'F');
            
            return $path;
        } catch (Exception $e) {
            error_log('Failed to generate report: ' . $e->getMessage());
            throw new Exception('Failed to generate report');
        }
    }
}
?>
