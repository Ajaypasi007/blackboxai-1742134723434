<div class="py-6">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-semibold text-gray-900">Analytics Overview</h1>
                <p class="mt-1 text-sm text-gray-500">
                    Performance metrics across all your social media accounts
                </p>
            </div>
            <div>
                <button onclick="exportAnalytics('pdf')" 
                        class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    <i class="fas fa-download mr-2"></i>
                    Export Report
                </button>
            </div>
        </div>

        <!-- Date Range Selector -->
        <div class="mt-6 bg-white shadow px-4 py-5 sm:rounded-lg sm:p-6">
            <form id="date-range-form" class="space-y-6 sm:space-y-0 sm:flex sm:items-center">
                <div class="sm:flex-1 sm:flex sm:items-center sm:space-x-4">
                    <div class="sm:w-1/3">
                        <label for="start_date" class="block text-sm font-medium text-gray-700">Start Date</label>
                        <input type="date" 
                               name="start_date" 
                               id="start_date"
                               value="<?= $startDate ?>"
                               max="<?= date('Y-m-d') ?>"
                               class="mt-1 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                    </div>
                    <div class="sm:w-1/3">
                        <label for="end_date" class="block text-sm font-medium text-gray-700">End Date</label>
                        <input type="date" 
                               name="end_date" 
                               id="end_date"
                               value="<?= $endDate ?>"
                               max="<?= date('Y-m-d') ?>"
                               class="mt-1 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                    </div>
                    <div class="sm:w-1/3">
                        <label for="quick_range" class="block text-sm font-medium text-gray-700">Quick Select</label>
                        <select id="quick_range" 
                                class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md">
                            <option value="">Custom Range</option>
                            <option value="7">Last 7 Days</option>
                            <option value="30">Last 30 Days</option>
                            <option value="90">Last 90 Days</option>
                        </select>
                    </div>
                </div>
                <div class="sm:ml-4">
                    <button type="submit" 
                            class="w-full sm:w-auto flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        Update
                    </button>
                </div>
            </form>
        </div>

        <!-- Overall Performance -->
        <div class="mt-8 grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4">
            <!-- Total Followers -->
            <div class="bg-white overflow-hidden shadow rounded-lg">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <i class="fas fa-users text-blue-600 text-2xl"></i>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">
                                    Total Followers
                                </dt>
                                <dd class="flex items-baseline">
                                    <div class="text-2xl font-semibold text-gray-900">
                                        <?= formatNumber($overview['total_followers']) ?>
                                    </div>
                                    <?php if ($overview['growth']['followers'] !== 0): ?>
                                        <div class="ml-2 flex items-baseline text-sm font-semibold <?= $overview['growth']['followers'] > 0 ? 'text-green-600' : 'text-red-600' ?>">
                                            <i class="fas fa-arrow-<?= $overview['growth']['followers'] > 0 ? 'up' : 'down' ?> mr-0.5"></i>
                                            <?= abs(round($overview['growth']['followers'], 1)) ?>%
                                        </div>
                                    <?php endif; ?>
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
                                <dd class="flex items-baseline">
                                    <div class="text-2xl font-semibold text-gray-900">
                                        <?= formatNumber($overview['total_engagement']) ?>
                                    </div>
                                    <?php if ($overview['growth']['engagement'] !== 0): ?>
                                        <div class="ml-2 flex items-baseline text-sm font-semibold <?= $overview['growth']['engagement'] > 0 ? 'text-green-600' : 'text-red-600' ?>">
                                            <i class="fas fa-arrow-<?= $overview['growth']['engagement'] > 0 ? 'up' : 'down' ?> mr-0.5"></i>
                                            <?= abs(round($overview['growth']['engagement'], 1)) ?>%
                                        </div>
                                    <?php endif; ?>
                                </dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Average Engagement Rate -->
            <div class="bg-white overflow-hidden shadow rounded-lg">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <i class="fas fa-chart-line text-green-600 text-2xl"></i>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">
                                    Avg. Engagement Rate
                                </dt>
                                <dd class="text-2xl font-semibold text-gray-900">
                                    <?= number_format($overview['avg_engagement_rate'], 1) ?>%
                                </dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Total Posts -->
            <div class="bg-white overflow-hidden shadow rounded-lg">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <i class="fas fa-pencil-alt text-purple-600 text-2xl"></i>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">
                                    Total Posts
                                </dt>
                                <dd class="text-2xl font-semibold text-gray-900">
                                    <?= formatNumber($overview['total_posts']) ?>
                                </dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Platform Performance -->
        <div class="mt-8">
            <h2 class="text-lg font-medium text-gray-900">Platform Performance</h2>
            <div class="mt-4 grid grid-cols-1 gap-5 sm:grid-cols-2">
                <?php foreach ($overview['platforms'] as $platform => $metrics): ?>
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
                                        Followers
                                    </dt>
                                    <dd class="mt-1 text-xl font-semibold text-gray-900">
                                        <?= formatNumber($metrics['followers']) ?>
                                    </dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">
                                        Engagement Rate
                                    </dt>
                                    <dd class="mt-1 text-xl font-semibold text-gray-900">
                                        <?= number_format($metrics['engagement_rate'], 1) ?>%
                                    </dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">
                                        Posts
                                    </dt>
                                    <dd class="mt-1 text-xl font-semibold text-gray-900">
                                        <?= formatNumber($metrics['posts']) ?>
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
                            </div>

                            <div class="mt-4">
                                <a href="/dashboard/accounts/<?= $metrics['account_id'] ?>/analytics" 
                                   class="text-sm font-medium text-indigo-600 hover:text-indigo-500">
                                    View detailed analytics <span aria-hidden="true">&rarr;</span>
                                </a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- Growth Timeline -->
        <div class="mt-8">
            <div class="bg-white shadow rounded-lg">
                <div class="px-4 py-5 sm:p-6">
                    <h3 class="text-lg leading-6 font-medium text-gray-900">
                        Growth Timeline
                    </h3>
                    <div class="mt-4" style="height: 300px;">
                        <canvas id="growthChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Best Performing Posts -->
        <div class="mt-8">
            <div class="bg-white shadow sm:rounded-lg">
                <div class="px-4 py-5 sm:p-6">
                    <h3 class="text-lg leading-6 font-medium text-gray-900">
                        Top Performing Posts
                    </h3>
                    <div class="mt-4 flow-root">
                        <div class="-mx-4 -my-2 overflow-x-auto sm:-mx-6 lg:-mx-8">
                            <div class="inline-block min-w-full py-2 align-middle sm:px-6 lg:px-8">
                                <table class="min-w-full divide-y divide-gray-300">
                                    <thead>
                                        <tr>
                                            <th scope="col" class="py-3.5 pl-4 pr-3 text-left text-sm font-semibold text-gray-900 sm:pl-0">Post</th>
                                            <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Platform</th>
                                            <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Date</th>
                                            <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Engagement</th>
                                            <th scope="col" class="relative py-3.5 pl-3 pr-4 sm:pr-0">
                                                <span class="sr-only">View</span>
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-gray-200">
                                        <?php foreach ($topPosts as $post): ?>
                                            <tr>
                                                <td class="whitespace-nowrap py-4 pl-4 pr-3 text-sm sm:pl-0">
                                                    <div class="flex items-center">
                                                        <?php if (!empty($post['media_urls'])): ?>
                                                            <div class="h-10 w-10 flex-shrink-0">
                                                                <img class="h-10 w-10 rounded-full object-cover" 
                                                                     src="<?= $post['media_urls'][0] ?>" 
                                                                     alt="">
                                                            </div>
                                                        <?php endif; ?>
                                                        <div class="<?= !empty($post['media_urls']) ? 'ml-4' : '' ?>">
                                                            <div class="font-medium text-gray-900">
                                                                <?= htmlspecialchars(truncateText($post['content'], 50)) ?>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">
                                                    <div class="flex items-center">
                                                        <i class="<?= getPlatformIcon($post['platform']) ?> <?= getPlatformColor($post['platform']) ?> mr-2"></i>
                                                        <?= ucfirst($post['platform']) ?>
                                                    </div>
                                                </td>
                                                <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">
                                                    <?= formatDateTime($post['created_at']) ?>
                                                </td>
                                                <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">
                                                    <?= formatNumber($post['engagement']) ?>
                                                </td>
                                                <td class="relative whitespace-nowrap py-4 pl-3 pr-4 text-right text-sm font-medium sm:pr-0">
                                                    <a href="/dashboard/posts/analytics/<?= $post['id'] ?>" 
                                                       class="text-indigo-600 hover:text-indigo-900">
                                                        View<span class="sr-only">, post</span>
                                                    </a>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize growth chart
    const ctx = document.getElementById('growthChart').getContext('2d');
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: <?= json_encode(array_keys($overview['timeline']['labels'])) ?>,
            datasets: <?= json_encode($overview['timeline']['datasets']) ?>
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

    // Date range form handling
    const form = document.getElementById('date-range-form');
    const startDate = document.getElementById('start_date');
    const endDate = document.getElementById('end_date');
    const quickRange = document.getElementById('quick_range');

    quickRange.addEventListener('change', function() {
        if (this.value) {
            const days = parseInt(this.value);
            endDate.value = new Date().toISOString().split('T')[0];
            startDate.value = new Date(Date.now() - days * 24 * 60 * 60 * 1000).toISOString().split('T')[0];
            form.submit();
        }
    });

    form.addEventListener('submit', function(e) {
        e.preventDefault();
        const start = new Date(startDate.value);
        const end = new Date(endDate.value);

        if (start > end) {
            showNotification('error', 'Start date cannot be after end date');
            return;
        }

        this.submit();
    });
});

// Export analytics
function exportAnalytics(format) {
    const url = `/dashboard/analytics/export?format=${format}&start_date=${document.getElementById('start_date').value}&end_date=${document.getElementById('end_date').value}`;
    
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
        a.download = `analytics-report.${format}`;
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
