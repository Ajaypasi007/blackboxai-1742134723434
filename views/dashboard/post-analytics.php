<div class="py-6">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-semibold text-gray-900">Post Analytics</h1>
                <p class="mt-1 text-sm text-gray-500">
                    Performance metrics for your post
                </p>
            </div>
            <div>
                <a href="/dashboard/posts" 
                   class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    <i class="fas fa-arrow-left mr-2"></i>
                    Back to Posts
                </a>
            </div>
        </div>

        <!-- Post Content -->
        <div class="mt-6 bg-white shadow sm:rounded-lg">
            <div class="px-4 py-5 sm:p-6">
                <div class="flex items-start space-x-4">
                    <!-- Post Text -->
                    <div class="flex-1">
                        <p class="text-sm text-gray-900">
                            <?= htmlspecialchars($post['content']) ?>
                        </p>
                        <div class="mt-2 flex items-center text-sm text-gray-500">
                            <i class="fas fa-calendar mr-1.5"></i>
                            <?= formatDateTime($post['created_at']) ?>
                            <?php if ($post['scheduled_time']): ?>
                                <span class="mx-2">&middot;</span>
                                <i class="fas fa-clock mr-1.5"></i>
                                Scheduled for <?= formatDateTime($post['scheduled_time']) ?>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Status -->
                    <div>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium <?= getStatusBadgeClass($post['status']) ?>">
                            <?= ucfirst($post['status']) ?>
                        </span>
                    </div>
                </div>

                <!-- Media Preview -->
                <?php if (!empty($post['media_urls'])): ?>
                    <div class="mt-4 grid grid-cols-2 gap-4 sm:grid-cols-3 lg:grid-cols-4">
                        <?php foreach ($post['media_urls'] as $url): ?>
                            <?php if (strpos($url, '.mp4') !== false): ?>
                                <video class="h-24 w-full object-cover rounded-lg">
                                    <source src="<?= $url ?>" type="video/mp4">
                                </video>
                            <?php else: ?>
                                <img src="<?= $url ?>" 
                                     alt="" 
                                     class="h-24 w-full object-cover rounded-lg">
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Overall Performance -->
        <div class="mt-8">
            <h2 class="text-lg font-medium text-gray-900">Overall Performance</h2>
            <div class="mt-4 grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4">
                <!-- Total Impressions -->
                <div class="bg-white overflow-hidden shadow rounded-lg">
                    <div class="p-5">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <i class="fas fa-eye text-blue-600 text-2xl"></i>
                            </div>
                            <div class="ml-5 w-0 flex-1">
                                <dl>
                                    <dt class="text-sm font-medium text-gray-500 truncate">
                                        Total Impressions
                                    </dt>
                                    <dd class="text-2xl font-semibold text-gray-900">
                                        <?= formatNumber($analytics['total_impressions']) ?>
                                    </dd>
                                </dl>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Total Engagement -->
                <div class="bg-white overflow-hidden shadow rounded-lg">
                    <div class="p-5">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <i class="fas fa-heart text-pink-600 text-2xl"></i>
                            </div>
                            <div class="ml-5 w-0 flex-1">
                                <dl>
                                    <dt class="text-sm font-medium text-gray-500 truncate">
                                        Total Engagement
                                    </dt>
                                    <dd class="text-2xl font-semibold text-gray-900">
                                        <?= formatNumber($analytics['total_engagement']) ?>
                                    </dd>
                                </dl>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Total Shares -->
                <div class="bg-white overflow-hidden shadow rounded-lg">
                    <div class="p-5">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <i class="fas fa-share-alt text-green-600 text-2xl"></i>
                            </div>
                            <div class="ml-5 w-0 flex-1">
                                <dl>
                                    <dt class="text-sm font-medium text-gray-500 truncate">
                                        Total Shares
                                    </dt>
                                    <dd class="text-2xl font-semibold text-gray-900">
                                        <?= formatNumber($analytics['total_shares']) ?>
                                    </dd>
                                </dl>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Total Comments -->
                <div class="bg-white overflow-hidden shadow rounded-lg">
                    <div class="p-5">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <i class="fas fa-comment text-purple-600 text-2xl"></i>
                            </div>
                            <div class="ml-5 w-0 flex-1">
                                <dl>
                                    <dt class="text-sm font-medium text-gray-500 truncate">
                                        Total Comments
                                    </dt>
                                    <dd class="text-2xl font-semibold text-gray-900">
                                        <?= formatNumber($analytics['total_comments']) ?>
                                    </dd>
                                </dl>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Platform Performance -->
        <div class="mt-8">
            <h2 class="text-lg font-medium text-gray-900">Platform Performance</h2>
            <div class="mt-4 grid grid-cols-1 gap-5 sm:grid-cols-2">
                <?php foreach ($analytics['platforms'] as $platform => $metrics): ?>
                    <div class="bg-white overflow-hidden shadow rounded-lg">
                        <div class="px-4 py-5 sm:p-6">
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    <i class="<?= getPlatformIcon($platform) ?> <?= getPlatformColor($platform) ?> text-2xl"></i>
                                </div>
                                <div class="ml-3">
                                    <h3 class="text-lg font-medium text-gray-900">
                                        <?= ucfirst($platform) ?>
                                    </h3>
                                </div>
                            </div>

                            <div class="mt-4 grid grid-cols-2 gap-4">
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">
                                        Impressions
                                    </dt>
                                    <dd class="mt-1 text-xl font-semibold text-gray-900">
                                        <?= formatNumber($metrics['impressions']) ?>
                                    </dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">
                                        Engagement
                                    </dt>
                                    <dd class="mt-1 text-xl font-semibold text-gray-900">
                                        <?= formatNumber($metrics['engagement']) ?>
                                    </dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">
                                        Shares
                                    </dt>
                                    <dd class="mt-1 text-xl font-semibold text-gray-900">
                                        <?= formatNumber($metrics['shares']) ?>
                                    </dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">
                                        Comments
                                    </dt>
                                    <dd class="mt-1 text-xl font-semibold text-gray-900">
                                        <?= formatNumber($metrics['comments']) ?>
                                    </dd>
                                </div>
                            </div>

                            <?php if (isset($post['platforms'][$platform]['post_id'])): ?>
                                <div class="mt-4">
                                    <a href="<?= $post['platforms'][$platform]['url'] ?>" 
                                       target="_blank"
                                       class="text-sm font-medium text-indigo-600 hover:text-indigo-500">
                                        View on <?= ucfirst($platform) ?> <span aria-hidden="true">&rarr;</span>
                                    </a>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- Engagement Timeline -->
        <div class="mt-8">
            <div class="bg-white shadow rounded-lg">
                <div class="px-4 py-5 sm:p-6">
                    <h3 class="text-lg leading-6 font-medium text-gray-900">
                        Engagement Timeline
                    </h3>
                    <div class="mt-4" style="height: 300px;">
                        <canvas id="engagementChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Export Options -->
        <div class="mt-8 flex justify-end">
            <button onclick="exportAnalytics('pdf')" 
                    class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                <i class="fas fa-file-pdf mr-2"></i>
                Export PDF
            </button>
            <button onclick="exportAnalytics('csv')" 
                    class="ml-3 inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                <i class="fas fa-file-csv mr-2"></i>
                Export CSV
            </button>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize engagement chart
    const ctx = document.getElementById('engagementChart').getContext('2d');
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: <?= json_encode(array_keys($analytics['timeline'])) ?>,
            datasets: [
                {
                    label: 'Impressions',
                    data: <?= json_encode(array_column($analytics['timeline'], 'impressions')) ?>,
                    borderColor: '#3B82F6',
                    backgroundColor: '#93C5FD',
                    fill: false
                },
                {
                    label: 'Engagement',
                    data: <?= json_encode(array_column($analytics['timeline'], 'engagement')) ?>,
                    borderColor: '#EC4899',
                    backgroundColor: '#F9A8D4',
                    fill: false
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true
                }
            },
            plugins: {
                legend: {
                    position: 'bottom'
                }
            }
        }
    });
});

// Export analytics
function exportAnalytics(format) {
    const url = `/dashboard/posts/analytics/<?= $post['id'] ?>/export?format=${format}`;
    
    fetch(url, {
        headers: {
            'X-CSRF-Token': '<?= $this->generateCSRFToken() ?>'
        }
    })
    .then(response => response.blob())
    .then(blob => {
        const url = window.URL.createObjectURL(blob);
        const a = document.createElement('a');
        a.href = url;
        a.download = `post-analytics-<?= $post['id'] ?>.${format}`;
        document.body.appendChild(a);
        a.click();
        window.URL.revokeObjectURL(url);
        a.remove();
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification('error', 'Failed to export analytics');
    });
}
</script>
