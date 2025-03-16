<div class="py-6">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="flex items-center justify-between">
            <div>
                <div class="flex items-center">
                    <i class="<?= getPlatformIcon($account['platform']) ?> <?= getPlatformColor($account['platform']) ?> text-3xl mr-3"></i>
                    <div>
                        <h1 class="text-2xl font-semibold text-gray-900">
                            <?= htmlspecialchars($account['account_name']) ?>
                        </h1>
                        <p class="mt-1 text-sm text-gray-500">
                            Analytics for your <?= ucfirst($account['platform']) ?> account
                        </p>
                    </div>
                </div>
            </div>
            <div>
                <a href="/dashboard/accounts" 
                   class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    <i class="fas fa-arrow-left mr-2"></i>
                    Back to Accounts
                </a>
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

        <!-- Key Metrics -->
        <div class="mt-8 grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4">
            <!-- Followers -->
            <div class="bg-white overflow-hidden shadow rounded-lg">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <i class="fas fa-users text-blue-600 text-2xl"></i>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">
                                    Followers
                                </dt>
                                <dd class="flex items-baseline">
                                    <div class="text-2xl font-semibold text-gray-900">
                                        <?= formatNumber($analytics['current']['followers']) ?>
                                    </div>
                                    <?php if ($analytics['growth']['followers'] !== 0): ?>
                                        <div class="ml-2 flex items-baseline text-sm font-semibold <?= $analytics['growth']['followers'] > 0 ? 'text-green-600' : 'text-red-600' ?>">
                                            <i class="fas fa-arrow-<?= $analytics['growth']['followers'] > 0 ? 'up' : 'down' ?> mr-0.5"></i>
                                            <?= abs(round($analytics['growth']['followers'], 1)) ?>%
                                        </div>
                                    <?php endif; ?>
                                </dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Engagement Rate -->
            <div class="bg-white overflow-hidden shadow rounded-lg">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <i class="fas fa-heart text-pink-600 text-2xl"></i>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">
                                    Engagement Rate
                                </dt>
                                <dd class="flex items-baseline">
                                    <div class="text-2xl font-semibold text-gray-900">
                                        <?= number_format($analytics['average']['engagement_rate'], 1) ?>%
                                    </div>
                                    <?php if ($analytics['growth']['engagement_rate'] !== 0): ?>
                                        <div class="ml-2 flex items-baseline text-sm font-semibold <?= $analytics['growth']['engagement_rate'] > 0 ? 'text-green-600' : 'text-red-600' ?>">
                                            <i class="fas fa-arrow-<?= $analytics['growth']['engagement_rate'] > 0 ? 'up' : 'down' ?> mr-0.5"></i>
                                            <?= abs(round($analytics['growth']['engagement_rate'], 1)) ?>%
                                        </div>
                                    <?php endif; ?>
                                </dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Posts -->
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
                                    <?= formatNumber($analytics['total']['posts']) ?>
                                </dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Reach -->
            <div class="bg-white overflow-hidden shadow rounded-lg">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <i class="fas fa-eye text-green-600 text-2xl"></i>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">
                                    Average Reach
                                </dt>
                                <dd class="flex items-baseline">
                                    <div class="text-2xl font-semibold text-gray-900">
                                        <?= formatNumber($analytics['average']['reach']) ?>
                                    </div>
                                    <?php if ($analytics['growth']['reach'] !== 0): ?>
                                        <div class="ml-2 flex items-baseline text-sm font-semibold <?= $analytics['growth']['reach'] > 0 ? 'text-green-600' : 'text-red-600' ?>">
                                            <i class="fas fa-arrow-<?= $analytics['growth']['reach'] > 0 ? 'up' : 'down' ?> mr-0.5"></i>
                                            <?= abs(round($analytics['growth']['reach'], 1)) ?>%
                                        </div>
                                    <?php endif; ?>
                                </dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Growth Chart -->
        <div class="mt-8">
            <div class="bg-white shadow rounded-lg">
                <div class="px-4 py-5 sm:p-6">
                    <h3 class="text-lg leading-6 font-medium text-gray-900">
                        Growth Trends
                    </h3>
                    <div class="mt-4" style="height: 300px;">
                        <canvas id="growthChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Engagement Breakdown -->
        <div class="mt-8">
            <div class="bg-white shadow rounded-lg">
                <div class="px-4 py-5 sm:p-6">
                    <h3 class="text-lg leading-6 font-medium text-gray-900">
                        Engagement Breakdown
                    </h3>
                    <div class="mt-4 grid grid-cols-1 gap-5 sm:grid-cols-3">
                        <div class="bg-gray-50 rounded-lg p-5">
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    <i class="fas fa-thumbs-up text-blue-600 text-xl"></i>
                                </div>
                                <div class="ml-3">
                                    <h4 class="text-sm font-medium text-gray-900">Likes</h4>
                                    <p class="text-2xl font-semibold text-gray-900">
                                        <?= formatNumber($analytics['total']['likes']) ?>
                                    </p>
                                </div>
                            </div>
                        </div>
                        <div class="bg-gray-50 rounded-lg p-5">
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    <i class="fas fa-comment text-green-600 text-xl"></i>
                                </div>
                                <div class="ml-3">
                                    <h4 class="text-sm font-medium text-gray-900">Comments</h4>
                                    <p class="text-2xl font-semibold text-gray-900">
                                        <?= formatNumber($analytics['total']['comments']) ?>
                                    </p>
                                </div>
                            </div>
                        </div>
                        <div class="bg-gray-50 rounded-lg p-5">
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    <i class="fas fa-share text-purple-600 text-xl"></i>
                                </div>
                                <div class="ml-3">
                                    <h4 class="text-sm font-medium text-gray-900">Shares</h4>
                                    <p class="text-2xl font-semibold text-gray-900">
                                        <?= formatNumber($analytics['total']['shares']) ?>
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Best Performing Posts -->
        <div class="mt-8">
            <div class="bg-white shadow sm:rounded-lg">
                <div class="px-4 py-5 sm:p-6">
                    <h3 class="text-lg leading-6 font-medium text-gray-900">
                        Best Performing Posts
                    </h3>
                    <div class="mt-4 flow-root">
                        <div class="-mx-4 -my-2 overflow-x-auto sm:-mx-6 lg:-mx-8">
                            <div class="inline-block min-w-full py-2 align-middle sm:px-6 lg:px-8">
                                <table class="min-w-full divide-y divide-gray-300">
                                    <thead>
                                        <tr>
                                            <th scope="col" class="py-3.5 pl-4 pr-3 text-left text-sm font-semibold text-gray-900 sm:pl-0">Post</th>
                                            <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Date</th>
                                            <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Reach</th>
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
                                                    <?= formatDateTime($post['created_at']) ?>
                                                </td>
                                                <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">
                                                    <?= formatNumber($post['reach']) ?>
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

        <!-- Export Options -->
        <div class="mt-8 flex justify-end space-x-3">
            <button onclick="exportAnalytics('pdf')" 
                    class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                <i class="fas fa-file-pdf mr-2"></i>
                Export PDF
            </button>
            <button onclick="exportAnalytics('csv')" 
                    class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                <i class="fas fa-file-csv mr-2"></i>
                Export CSV
            </button>
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
            labels: <?= json_encode(array_keys($analytics['timeline'])) ?>,
            datasets: [
                {
                    label: 'Followers',
                    data: <?= json_encode(array_column($analytics['timeline'], 'followers')) ?>,
                    borderColor: '#3B82F6',
                    backgroundColor: '#93C5FD',
                    fill: false
                },
                {
                    label: 'Engagement Rate',
                    data: <?= json_encode(array_column($analytics['timeline'], 'engagement_rate')) ?>,
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
    const url = `/dashboard/accounts/<?= $account['id'] ?>/analytics/export?format=${format}&start_date=${document.getElementById('start_date').value}&end_date=${document.getElementById('end_date').value}`;
    
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
        a.download = `account-analytics-<?= $account['id'] ?>.${format}`;
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
