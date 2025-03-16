<div class="py-6">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-semibold text-gray-900">Posts</h1>
                <p class="mt-1 text-sm text-gray-500">
                    Manage your social media content
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

        <!-- Filters -->
        <div class="mt-6 bg-white shadow px-4 py-5 sm:rounded-lg sm:p-6">
            <form id="filter-form" class="space-y-6 sm:space-y-0 sm:flex sm:items-center sm:space-x-4">
                <!-- Search -->
                <div class="flex-1">
                    <label for="search" class="sr-only">Search posts</label>
                    <div class="relative rounded-md shadow-sm">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fas fa-search text-gray-400"></i>
                        </div>
                        <input type="text" 
                               name="search" 
                               id="search"
                               value="<?= htmlspecialchars($filters['search'] ?? '') ?>"
                               class="focus:ring-indigo-500 focus:border-indigo-500 block w-full pl-10 sm:text-sm border-gray-300 rounded-md" 
                               placeholder="Search posts...">
                    </div>
                </div>

                <!-- Status Filter -->
                <div class="sm:w-40">
                    <label for="status" class="sr-only">Status</label>
                    <select id="status" 
                            name="status"
                            class="block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md">
                        <option value="">All Status</option>
                        <option value="draft" <?= ($filters['status'] ?? '') === 'draft' ? 'selected' : '' ?>>Draft</option>
                        <option value="scheduled" <?= ($filters['status'] ?? '') === 'scheduled' ? 'selected' : '' ?>>Scheduled</option>
                        <option value="published" <?= ($filters['status'] ?? '') === 'published' ? 'selected' : '' ?>>Published</option>
                        <option value="failed" <?= ($filters['status'] ?? '') === 'failed' ? 'selected' : '' ?>>Failed</option>
                    </select>
                </div>

                <!-- Platform Filter -->
                <div class="sm:w-40">
                    <label for="platform" class="sr-only">Platform</label>
                    <select id="platform" 
                            name="platform"
                            class="block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md">
                        <option value="">All Platforms</option>
                        <option value="facebook" <?= ($filters['platform'] ?? '') === 'facebook' ? 'selected' : '' ?>>Facebook</option>
                        <option value="twitter" <?= ($filters['platform'] ?? '') === 'twitter' ? 'selected' : '' ?>>Twitter</option>
                        <option value="instagram" <?= ($filters['platform'] ?? '') === 'instagram' ? 'selected' : '' ?>>Instagram</option>
                        <option value="linkedin" <?= ($filters['platform'] ?? '') === 'linkedin' ? 'selected' : '' ?>>LinkedIn</option>
                    </select>
                </div>

                <!-- Date Range Filter -->
                <div class="sm:w-48">
                    <label for="date_range" class="sr-only">Date Range</label>
                    <select id="date_range" 
                            name="date_range"
                            class="block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md">
                        <option value="">All Time</option>
                        <option value="today" <?= ($filters['date_range'] ?? '') === 'today' ? 'selected' : '' ?>>Today</option>
                        <option value="week" <?= ($filters['date_range'] ?? '') === 'week' ? 'selected' : '' ?>>This Week</option>
                        <option value="month" <?= ($filters['date_range'] ?? '') === 'month' ? 'selected' : '' ?>>This Month</option>
                    </select>
                </div>
            </form>
        </div>

        <!-- Posts List -->
        <div class="mt-6 bg-white shadow overflow-hidden sm:rounded-md">
            <?php if (empty($posts)): ?>
                <div class="px-4 py-12 text-center">
                    <i class="fas fa-pencil-alt text-gray-400 text-4xl mb-4"></i>
                    <h3 class="text-lg font-medium text-gray-900">No posts found</h3>
                    <p class="mt-2 text-sm text-gray-500">
                        Get started by creating your first post
                    </p>
                    <div class="mt-6">
                        <a href="/dashboard/posts/create" 
                           class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700">
                            <i class="fas fa-plus mr-2"></i>
                            Create Post
                        </a>
                    </div>
                </div>
            <?php else: ?>
                <ul role="list" class="divide-y divide-gray-200">
                    <?php foreach ($posts as $post): ?>
                        <li>
                            <div class="px-4 py-4 sm:px-6 hover:bg-gray-50">
                                <div class="flex items-center justify-between">
                                    <div class="flex-1 min-w-0">
                                        <div class="flex items-center">
                                            <!-- Post Content -->
                                            <div class="flex-1">
                                                <p class="text-sm font-medium text-gray-900 truncate">
                                                    <?= htmlspecialchars(truncateText($post['content'], 100)) ?>
                                                </p>
                                                <div class="mt-2 flex items-center text-sm text-gray-500">
                                                    <!-- Platforms -->
                                                    <div class="flex items-center space-x-2">
                                                        <?php foreach ($post['platforms'] as $platform): ?>
                                                            <i class="<?= getPlatformIcon($platform['platform']) ?> <?= getPlatformColor($platform['platform']) ?>"></i>
                                                        <?php endforeach; ?>
                                                    </div>
                                                    <span class="mx-2">&middot;</span>
                                                    <!-- Date -->
                                                    <i class="fas fa-calendar mr-1.5"></i>
                                                    <?= formatDateTime($post['created_at']) ?>
                                                    <?php if ($post['scheduled_time']): ?>
                                                        <span class="mx-2">&middot;</span>
                                                        <i class="fas fa-clock mr-1.5"></i>
                                                        Scheduled for <?= formatDateTime($post['scheduled_time']) ?>
                                                    <?php endif; ?>
                                                </div>
                                            </div>

                                            <!-- Status Badge -->
                                            <div class="ml-6">
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium <?= getStatusBadgeClass($post['status']) ?>">
                                                    <?= ucfirst($post['status']) ?>
                                                </span>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Actions -->
                                    <div class="ml-6 flex items-center space-x-4">
                                        <a href="/dashboard/posts/analytics/<?= $post['id'] ?>" 
                                           class="text-gray-400 hover:text-gray-500">
                                            <span class="sr-only">Analytics</span>
                                            <i class="fas fa-chart-bar"></i>
                                        </a>
                                        <a href="/dashboard/posts/edit/<?= $post['id'] ?>" 
                                           class="text-gray-400 hover:text-gray-500">
                                            <span class="sr-only">Edit</span>
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <button onclick="deletePost(<?= $post['id'] ?>)" 
                                                class="text-gray-400 hover:text-red-500">
                                            <span class="sr-only">Delete</span>
                                            <i class="fas fa-trash"></i>
                                        </button>
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
                        </li>
                    <?php endforeach; ?>
                </ul>

                <!-- Pagination -->
                <?php if ($pages > 1): ?>
                    <nav class="bg-white px-4 py-3 flex items-center justify-between border-t border-gray-200 sm:px-6">
                        <div class="hidden sm:block">
                            <p class="text-sm text-gray-700">
                                Showing
                                <span class="font-medium"><?= ($current_page - 1) * $per_page + 1 ?></span>
                                to
                                <span class="font-medium"><?= min($current_page * $per_page, $total) ?></span>
                                of
                                <span class="font-medium"><?= $total ?></span>
                                results
                            </p>
                        </div>
                        <div class="flex-1 flex justify-between sm:justify-end">
                            <?php if ($current_page > 1): ?>
                                <a href="?page=<?= $current_page - 1 ?>" 
                                   class="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                                    Previous
                                </a>
                            <?php endif; ?>
                            <?php if ($current_page < $pages): ?>
                                <a href="?page=<?= $current_page + 1 ?>" 
                                   class="ml-3 relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                                    Next
                                </a>
                            <?php endif; ?>
                        </div>
                    </nav>
                <?php endif; ?>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Filter form handling
    const filterForm = document.getElementById('filter-form');
    const inputs = filterForm.querySelectorAll('input, select');
    
    inputs.forEach(input => {
        input.addEventListener('change', () => filterForm.submit());
    });

    // Search input debouncing
    const searchInput = document.getElementById('search');
    let timeout = null;
    
    searchInput.addEventListener('input', function() {
        clearTimeout(timeout);
        timeout = setTimeout(() => filterForm.submit(), 500);
    });
});

// Delete post
function deletePost(id) {
    if (confirm('Are you sure you want to delete this post? This action cannot be undone.')) {
        fetch(`/dashboard/posts/delete/${id}`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-Token': '<?= $this->generateCSRFToken() ?>'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Remove post from list
                const post = document.querySelector(`[data-post-id="${id}"]`);
                post.remove();
                
                showNotification('success', 'Post deleted successfully');
                
                // Reload if no posts left
                if (document.querySelectorAll('[data-post-id]').length === 0) {
                    location.reload();
                }
            } else {
                throw new Error(data.message || 'Failed to delete post');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showNotification('error', error.message);
        });
    }
}
</script>
