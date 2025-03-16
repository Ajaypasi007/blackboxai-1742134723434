<div class="py-6">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Welcome Section -->
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-semibold text-gray-900">Welcome back, <?= htmlspecialchars($this->user['first_name']) ?>!</h1>
                <p class="mt-1 text-sm text-gray-500">
                    Here's what's happening with your social media accounts
                </p>
            </div>
            <div>
                <a href="/dashboard/posts/create" 
                   class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    <i class="fas fa-plus -ml-1 mr-2"></i>
                    Create Post
                </a>
            </div>
        </div>

        <!-- Key Metrics -->
        <div class="mt-6 grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4">
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
                                        <?= formatNumber($analytics['total_followers']) ?>
                                    </div>
                                    <?php if ($analytics['growth']['followers'] ?? 0 !== 0): ?>
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
                                        <?= formatNumber($analytics['total_engagement']) ?>
                                    </div>
                                    <?php if ($analytics['growth']['engagement'] ?? 0 !== 0): ?>
                                        <div class="ml-2 flex items-baseline text-sm font-semibold <?= $analytics['growth']['engagement'] > 0 ? 'text-green-600' : 'text-red-600' ?>">
                                            <i class="fas fa-arrow-<?= $analytics['growth']['engagement'] > 0 ? 'up' : 'down' ?> mr-0.5"></i>
                                            <?= abs(round($analytics['growth']['engagement'], 1)) ?>%
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
                                    <?= number_format($analytics['avg_engagement_rate'], 1) ?>%
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
                                    <?= formatNumber($analytics['total_posts']) ?>
                                </dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Connected Accounts -->
        <div class="mt-8">
            <div class="flex items-center justify-between">
                <h2 class="text-lg font-medium text-gray-900">Connected Accounts</h2>
                <a href="/dashboard/accounts" class="text-sm font-medium text-indigo-600 hover:text-indigo-500">
                    View all <span aria-hidden="true">&rarr;</span>
                </a>
            </div>
            <div class="mt-4 grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-3">
                <?php foreach ($accounts as $account): ?>
                    <div class="bg-white overflow-hidden shadow rounded-lg">
                        <div class="px-4 py-5 sm:p-6">
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    <i class="<?= getPlatformIcon($account['platform']) ?> <?= getPlatformColor($account['platform']) ?> text-2xl"></i>
                                </div>
                                <div class="ml-5 w-0 flex-1">
                                    <dl>
                                        <dt class="text-sm font-medium text-gray-500 truncate">
                                            <?= htmlspecialchars($account['account_name']) ?>
                                        </dt>
                                        <dd class="flex items-baseline">
                                            <div class="text-sm text-gray-900">
                                                <?= ucfirst($account['platform']) ?>
                                            </div>
                                            <?php if ($account['status'] === 'active'): ?>
                                                <div class="ml-2 flex items-center text-sm text-green-600">
                                                    <i class="fas fa-check-circle mr-1"></i>
                                                    Connected
                                                </div>
                                            <?php endif; ?>
                                        </dd>
                                    </dl>
                                </div>
                            </div>
                        </div>
                        <div class="bg-gray-50 px-4 py-4 sm:px-6">
                            <div class="text-sm">
                                <a href="/dashboard/accounts/<?= $account['id'] ?>/analytics" class="font-medium text-indigo-600 hover:text-indigo-500">
                                    View analytics <span aria-hidden="true">&rarr;</span>
                                </a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>

                <?php if (empty($accounts)): ?>
                    <div class="col-span-full">
                        <div class="text-center py-12 bg-white rounded-lg shadow">
                            <i class="fas fa-link text-gray-400 text-4xl mb-4"></i>
                            <h3 class="text-lg font-medium text-gray-900">No accounts connected</h3>
                            <p class="mt-2 text-sm text-gray-500">
                                Get started by connecting your social media accounts.
                            </p>
                            <div class="mt-6">
                                <a href="/dashboard/accounts" 
                                   class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700">
                                    <i class="fas fa-plus mr-2"></i>
                                    Connect Account
                                </a>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Recent Posts -->
        <div class="mt-8">
            <div class="flex items-center justify-between">
                <h2 class="text-lg font-medium text-gray-900">Recent Posts</h2>
                <a href="/dashboard/posts" class="text-sm font-medium text-indigo-600 hover:text-indigo-500">
                    View all <span aria-hidden="true">&rarr;</span>
                </a>
            </div>
            <div class="mt-4 bg-white shadow overflow-hidden sm:rounded-md">
                <ul role="list" class="divide-y divide-gray-200">
                    <?php foreach ($posts as $post): ?>
                        <li>
                            <div class="px-4 py-4 sm:px-6">
                                <div class="flex items-center justify-between">
                                    <div class="truncate">
                                        <div class="flex text-sm">
                                            <p class="font-medium text-indigo-600 truncate">
                                                <?= htmlspecialchars(truncateText($post['content'], 100)) ?>
                                            </p>
                                        </div>
                                        <div class="mt-2 flex">
                                            <div class="flex items-center text-sm text-gray-500">
                                                <i class="fas fa-calendar flex-shrink-0 mr-1.5 text-gray-400"></i>
                                                <p>
                                                    <?= formatDateTime($post['created_at']) ?>
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="ml-6 flex items-center">
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full <?= getStatusBadgeClass($post['status']) ?>">
                                            <?= ucfirst($post['status']) ?>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </li>
                    <?php endforeach; ?>

                    <?php if (empty($posts)): ?>
                        <li>
                            <div class="px-4 py-12 text-center">
                                <i class="fas fa-pencil-alt text-gray-400 text-4xl mb-4"></i>
                                <h3 class="text-lg font-medium text-gray-900">No posts yet</h3>
                                <p class="mt-2 text-sm text-gray-500">
                                    Create your first post to get started.
                                </p>
                                <div class="mt-6">
                                    <a href="/dashboard/posts/create" 
                                       class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700">
                                        <i class="fas fa-plus mr-2"></i>
                                        Create Post
                                    </a>
                                </div>
                            </div>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>

        <!-- Performance Chart -->
        <div class="mt-8">
            <div class="bg-white shadow rounded-lg">
                <div class="px-4 py-5 sm:p-6">
                    <h3 class="text-lg leading-6 font-medium text-gray-900">
                        Performance Overview
                    </h3>
                    <div class="mt-4" style="height: 300px;">
                        <canvas id="performanceChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize performance chart
    const ctx = document.getElementById('performanceChart').getContext('2d');
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: <?= json_encode(array_keys($analytics['timeline'])) ?>,
            datasets: <?= json_encode($analytics['timeline']['datasets']) ?>
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
</script>
