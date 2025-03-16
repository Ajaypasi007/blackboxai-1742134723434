<div class="py-6">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-semibold text-gray-900">Notifications</h1>
                <p class="mt-1 text-sm text-gray-500">
                    Stay updated with your account activity
                </p>
            </div>
            <div>
                <button onclick="markAllAsRead()" 
                        class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    <i class="fas fa-check-double mr-2"></i>
                    Mark all as read
                </button>
            </div>
        </div>

        <!-- Notifications List -->
        <div class="mt-6 bg-white shadow overflow-hidden sm:rounded-md">
            <ul role="list" class="divide-y divide-gray-200" id="notifications-list">
                <?php if (empty($notifications)): ?>
                    <li class="px-4 py-12 text-center">
                        <i class="fas fa-bell text-gray-400 text-4xl mb-4"></i>
                        <h3 class="text-lg font-medium text-gray-900">No notifications</h3>
                        <p class="mt-2 text-sm text-gray-500">
                            You're all caught up! Check back later for new notifications.
                        </p>
                    </li>
                <?php else: ?>
                    <?php foreach ($notifications as $notification): ?>
                        <li id="notification-<?= $notification['id'] ?>" 
                            class="notification-item <?= !$notification['read_at'] ? 'bg-blue-50' : '' ?>">
                            <div class="px-4 py-4 sm:px-6">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center">
                                        <!-- Notification Icon -->
                                        <div class="flex-shrink-0">
                                            <?php
                                            $iconClass = 'text-gray-400';
                                            switch ($notification['type']) {
                                                case 'success':
                                                    $icon = 'check-circle';
                                                    $iconClass = 'text-green-400';
                                                    break;
                                                case 'warning':
                                                    $icon = 'exclamation-triangle';
                                                    $iconClass = 'text-yellow-400';
                                                    break;
                                                case 'error':
                                                    $icon = 'exclamation-circle';
                                                    $iconClass = 'text-red-400';
                                                    break;
                                                default:
                                                    $icon = 'info-circle';
                                                    $iconClass = 'text-blue-400';
                                            }
                                            ?>
                                            <i class="fas fa-<?= $icon ?> text-xl <?= $iconClass ?>"></i>
                                        </div>

                                        <!-- Notification Content -->
                                        <div class="ml-4 flex-1">
                                            <div class="text-sm font-medium text-gray-900">
                                                <?= htmlspecialchars($notification['title']) ?>
                                            </div>
                                            <div class="mt-1 text-sm text-gray-500">
                                                <?= htmlspecialchars($notification['message']) ?>
                                            </div>
                                            <div class="mt-2 flex items-center text-xs text-gray-500">
                                                <i class="fas fa-clock mr-1.5"></i>
                                                <span><?= getTimeAgo($notification['created_at']) ?></span>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Action Buttons -->
                                    <div class="ml-6 flex items-center space-x-4">
                                        <?php if ($notification['action_url']): ?>
                                            <a href="<?= $notification['action_url'] ?>" 
                                               class="inline-flex items-center px-3 py-1 border border-transparent text-sm leading-5 font-medium rounded-md text-indigo-700 bg-indigo-100 hover:bg-indigo-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                                <?= htmlspecialchars($notification['action_text'] ?? 'View') ?>
                                            </a>
                                        <?php endif; ?>

                                        <?php if (!$notification['read_at']): ?>
                                            <button onclick="markAsRead(<?= $notification['id'] ?>)" 
                                                    class="text-gray-400 hover:text-gray-500 focus:outline-none">
                                                <span class="sr-only">Mark as read</span>
                                                <i class="fas fa-check"></i>
                                            </button>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </li>
                    <?php endforeach; ?>
                <?php endif; ?>
            </ul>
        </div>

        <!-- Notification Preferences Link -->
        <div class="mt-6 text-center">
            <a href="/dashboard/settings#notifications" class="text-sm font-medium text-indigo-600 hover:text-indigo-500">
                Manage notification preferences <span aria-hidden="true">&rarr;</span>
            </a>
        </div>
    </div>
</div>

<script>
// Mark single notification as read
async function markAsRead(id) {
    try {
        const response = await fetch(`/dashboard/notifications/mark-read/${id}`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-Token': '<?= $this->generateCSRFToken() ?>'
            }
        });

        if (!response.ok) {
            throw new Error('Failed to mark notification as read');
        }

        // Update UI
        const notification = document.getElementById(`notification-${id}`);
        notification.classList.remove('bg-blue-50');
        
        // Remove mark as read button
        const button = notification.querySelector('button');
        if (button) {
            button.remove();
        }

        showNotification('success', 'Notification marked as read');
    } catch (error) {
        console.error('Error:', error);
        showNotification('error', 'Failed to mark notification as read');
    }
}

// Mark all notifications as read
async function markAllAsRead() {
    try {
        const response = await fetch('/dashboard/notifications/mark-all-read', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-Token': '<?= $this->generateCSRFToken() ?>'
            }
        });

        if (!response.ok) {
            throw new Error('Failed to mark all notifications as read');
        }

        // Update UI
        document.querySelectorAll('.notification-item').forEach(notification => {
            notification.classList.remove('bg-blue-50');
            const button = notification.querySelector('button');
            if (button) {
                button.remove();
            }
        });

        showNotification('success', 'All notifications marked as read');
    } catch (error) {
        console.error('Error:', error);
        showNotification('error', 'Failed to mark all notifications as read');
    }
}

// Show notification
function showNotification(type, message) {
    const notification = document.createElement('div');
    notification.className = `fixed top-4 right-4 p-4 rounded-lg shadow-lg transform transition-all duration-300 ease-in-out ${
        type === 'success' ? 'bg-green-50 text-green-800' : 'bg-red-50 text-red-800'
    }`;
    
    notification.innerHTML = `
        <div class="flex items-center">
            <div class="flex-shrink-0">
                <i class="fas fa-${type === 'success' ? 'check' : 'exclamation'}-circle"></i>
            </div>
            <div class="ml-3">
                <p class="text-sm font-medium">
                    ${message}
                </p>
            </div>
            <div class="ml-4">
                <button class="text-${type === 'success' ? 'green' : 'red'}-500 hover:text-${type === 'success' ? 'green' : 'red'}-600 focus:outline-none">
                    <span class="sr-only">Close</span>
                    <i class="fas fa-times"></i>
                </button>
            </div>
        </div>
    `;

    document.body.appendChild(notification);

    // Add click handler to close button
    notification.querySelector('button').addEventListener('click', () => {
        notification.remove();
    });

    // Auto-remove after 5 seconds
    setTimeout(() => {
        notification.remove();
    }, 5000);
}
</script>
