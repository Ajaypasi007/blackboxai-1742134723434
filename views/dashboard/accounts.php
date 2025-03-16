<div class="py-6">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-semibold text-gray-900">Social Accounts</h1>
                <p class="mt-1 text-sm text-gray-500">
                    Manage your connected social media accounts
                </p>
            </div>
            <div>
                <button onclick="showConnectModal()" 
                        class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    <i class="fas fa-plus -ml-1 mr-2"></i>
                    Connect Account
                </button>
            </div>
        </div>

        <!-- Connected Accounts -->
        <div class="mt-6">
            <?php if (empty($accounts)): ?>
                <div class="text-center py-12 bg-white shadow rounded-lg">
                    <i class="fas fa-link text-gray-400 text-4xl mb-4"></i>
                    <h3 class="text-lg font-medium text-gray-900">No accounts connected</h3>
                    <p class="mt-2 text-sm text-gray-500">
                        Get started by connecting your social media accounts
                    </p>
                    <div class="mt-6">
                        <button onclick="showConnectModal()" 
                                class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700">
                            <i class="fas fa-plus mr-2"></i>
                            Connect Account
                        </button>
                    </div>
                </div>
            <?php else: ?>
                <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-3">
                    <?php foreach ($accounts as $account): ?>
                        <div class="bg-white overflow-hidden shadow rounded-lg divide-y divide-gray-200">
                            <div class="px-4 py-5 sm:px-6">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0">
                                            <i class="<?= getPlatformIcon($account['platform']) ?> <?= getPlatformColor($account['platform']) ?> text-2xl"></i>
                                        </div>
                                        <div class="ml-4">
                                            <h3 class="text-lg font-medium text-gray-900">
                                                <?= htmlspecialchars($account['account_name']) ?>
                                            </h3>
                                            <p class="text-sm text-gray-500">
                                                <?= ucfirst($account['platform']) ?>
                                            </p>
                                        </div>
                                    </div>
                                    <div class="flex items-center">
                                        <?php if ($account['status'] === 'active'): ?>
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                Connected
                                            </span>
                                        <?php else: ?>
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                                <?= ucfirst($account['status']) ?>
                                            </span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                            <div class="px-4 py-4 sm:px-6">
                                <!-- Account Stats -->
                                <dl class="grid grid-cols-2 gap-4">
                                    <div>
                                        <dt class="text-sm font-medium text-gray-500">
                                            Followers
                                        </dt>
                                        <dd class="mt-1 text-lg font-semibold text-gray-900">
                                            <?= formatNumber($account['stats']['followers'] ?? 0) ?>
                                        </dd>
                                    </div>
                                    <div>
                                        <dt class="text-sm font-medium text-gray-500">
                                            Engagement Rate
                                        </dt>
                                        <dd class="mt-1 text-lg font-semibold text-gray-900">
                                            <?= number_format($account['stats']['engagement_rate'] ?? 0, 1) ?>%
                                        </dd>
                                    </div>
                                </dl>
                            </div>
                            <div class="px-4 py-4 sm:px-6 bg-gray-50">
                                <div class="flex justify-between">
                                    <a href="/dashboard/accounts/<?= $account['id'] ?>/analytics" 
                                       class="text-sm font-medium text-indigo-600 hover:text-indigo-500">
                                        View analytics
                                    </a>
                                    <div class="flex space-x-3">
                                        <button onclick="refreshToken(<?= $account['id'] ?>)" 
                                                class="text-sm font-medium text-gray-500 hover:text-gray-700">
                                            <i class="fas fa-sync-alt"></i>
                                            <span class="sr-only">Refresh token</span>
                                        </button>
                                        <button onclick="disconnectAccount(<?= $account['id'] ?>)" 
                                                class="text-sm font-medium text-red-600 hover:text-red-700">
                                            Disconnect
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Connect Account Modal -->
<div id="connect-modal" 
     class="fixed z-10 inset-0 overflow-y-auto hidden" 
     aria-labelledby="modal-title" 
     role="dialog" 
     aria-modal="true">
    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" 
             aria-hidden="true"></div>

        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" 
              aria-hidden="true">&#8203;</span>

        <div class="inline-block align-bottom bg-white rounded-lg px-4 pt-5 pb-4 text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full sm:p-6">
            <div>
                <div class="mt-3 text-center sm:mt-5">
                    <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">
                        Connect Social Account
                    </h3>
                    <div class="mt-2">
                        <p class="text-sm text-gray-500">
                            Choose a platform to connect your social media account
                        </p>
                    </div>
                </div>
            </div>

            <div class="mt-5 sm:mt-6 grid grid-cols-1 gap-3">
                <a href="/auth/facebook/connect" 
                   class="inline-flex justify-center items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    <i class="fab fa-facebook text-blue-600 text-lg mr-3"></i>
                    Connect Facebook
                </a>
                <a href="/auth/twitter/connect" 
                   class="inline-flex justify-center items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    <i class="fab fa-twitter text-blue-400 text-lg mr-3"></i>
                    Connect Twitter
                </a>
                <a href="/auth/instagram/connect" 
                   class="inline-flex justify-center items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    <i class="fab fa-instagram text-pink-600 text-lg mr-3"></i>
                    Connect Instagram
                </a>
                <a href="/auth/linkedin/connect" 
                   class="inline-flex justify-center items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    <i class="fab fa-linkedin text-blue-700 text-lg mr-3"></i>
                    Connect LinkedIn
                </a>
                <button type="button" 
                        onclick="hideConnectModal()"
                        class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:col-start-1 sm:text-sm">
                    Cancel
                </button>
            </div>
        </div>
    </div>
</div>

<script>
// Modal handling
function showConnectModal() {
    document.getElementById('connect-modal').classList.remove('hidden');
}

function hideConnectModal() {
    document.getElementById('connect-modal').classList.add('hidden');
}

// Refresh token
async function refreshToken(accountId) {
    try {
        const response = await fetch(`/dashboard/accounts/${accountId}/refresh-token`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-Token': '<?= $this->generateCSRFToken() ?>'
            }
        });

        const data = await response.json();
        
        if (data.success) {
            showNotification('success', 'Token refreshed successfully');
        } else {
            throw new Error(data.message || 'Failed to refresh token');
        }
    } catch (error) {
        console.error('Error:', error);
        showNotification('error', error.message);
    }
}

// Disconnect account
async function disconnectAccount(accountId) {
    if (confirm('Are you sure you want to disconnect this account? Any scheduled posts for this account will be cancelled.')) {
        try {
            const response = await fetch(`/dashboard/accounts/${accountId}/disconnect`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-Token': '<?= $this->generateCSRFToken() ?>'
                }
            });

            const data = await response.json();
            
            if (data.success) {
                showNotification('success', 'Account disconnected successfully');
                location.reload();
            } else {
                throw new Error(data.message || 'Failed to disconnect account');
            }
        } catch (error) {
            console.error('Error:', error);
            showNotification('error', error.message);
        }
    }
}
</script>
