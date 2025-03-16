<div class="py-6">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <h1 class="text-2xl font-semibold text-gray-900">Settings</h1>

        <!-- Profile Settings -->
        <div class="mt-6">
            <div class="bg-white shadow px-4 py-5 sm:rounded-lg sm:p-6">
                <div class="md:grid md:grid-cols-3 md:gap-6">
                    <div class="md:col-span-1">
                        <h3 class="text-lg font-medium leading-6 text-gray-900">Profile</h3>
                        <p class="mt-1 text-sm text-gray-500">
                            Update your personal information and email preferences.
                        </p>
                    </div>
                    <div class="mt-5 md:mt-0 md:col-span-2">
                        <form action="/dashboard/settings/profile" method="POST">
                            <input type="hidden" name="csrf_token" value="<?= $this->generateCSRFToken() ?>">
                            
                            <div class="grid grid-cols-6 gap-6">
                                <div class="col-span-6 sm:col-span-3">
                                    <label for="first_name" class="block text-sm font-medium text-gray-700">First name</label>
                                    <input type="text" 
                                           name="first_name" 
                                           id="first_name" 
                                           value="<?= htmlspecialchars($this->user['first_name']) ?>"
                                           class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                                </div>

                                <div class="col-span-6 sm:col-span-3">
                                    <label for="last_name" class="block text-sm font-medium text-gray-700">Last name</label>
                                    <input type="text" 
                                           name="last_name" 
                                           id="last_name" 
                                           value="<?= htmlspecialchars($this->user['last_name']) ?>"
                                           class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                                </div>

                                <div class="col-span-6 sm:col-span-4">
                                    <label for="email" class="block text-sm font-medium text-gray-700">Email address</label>
                                    <input type="email" 
                                           name="email" 
                                           id="email" 
                                           value="<?= htmlspecialchars($this->user['email']) ?>"
                                           class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                                </div>

                                <div class="col-span-6 sm:col-span-3">
                                    <label for="timezone" class="block text-sm font-medium text-gray-700">Timezone</label>
                                    <select id="timezone" 
                                            name="timezone" 
                                            class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                        <?php foreach (timezone_identifiers_list() as $tz): ?>
                                            <option value="<?= $tz ?>" <?= $this->user['timezone'] === $tz ? 'selected' : '' ?>>
                                                <?= $tz ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>

                            <div class="mt-6">
                                <button type="submit" 
                                        class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                    Save changes
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Notification Settings -->
        <div class="mt-10">
            <div class="bg-white shadow px-4 py-5 sm:rounded-lg sm:p-6">
                <div class="md:grid md:grid-cols-3 md:gap-6">
                    <div class="md:col-span-1">
                        <h3 class="text-lg font-medium leading-6 text-gray-900">Notifications</h3>
                        <p class="mt-1 text-sm text-gray-500">
                            Decide which communications you'd like to receive.
                        </p>
                    </div>
                    <div class="mt-5 md:mt-0 md:col-span-2">
                        <form action="/dashboard/settings/notifications" method="POST">
                            <input type="hidden" name="csrf_token" value="<?= $this->generateCSRFToken() ?>">
                            
                            <div class="space-y-6">
                                <div class="flex items-start">
                                    <div class="flex items-center h-5">
                                        <input id="email_notifications" 
                                               name="email_notifications" 
                                               type="checkbox"
                                               <?= $preferences['email'] ? 'checked' : '' ?>
                                               class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300 rounded">
                                    </div>
                                    <div class="ml-3 text-sm">
                                        <label for="email_notifications" class="font-medium text-gray-700">Email notifications</label>
                                        <p class="text-gray-500">Receive email notifications about your account activity.</p>
                                    </div>
                                </div>

                                <div class="flex items-start">
                                    <div class="flex items-center h-5">
                                        <input id="desktop_notifications" 
                                               name="desktop_notifications" 
                                               type="checkbox"
                                               <?= $preferences['desktop'] ? 'checked' : '' ?>
                                               class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300 rounded">
                                    </div>
                                    <div class="ml-3 text-sm">
                                        <label for="desktop_notifications" class="font-medium text-gray-700">Desktop notifications</label>
                                        <p class="text-gray-500">Receive desktop notifications when you're online.</p>
                                    </div>
                                </div>
                            </div>

                            <div class="mt-6">
                                <button type="submit" 
                                        class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                    Save preferences
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Subscription Settings -->
        <div class="mt-10">
            <div class="bg-white shadow px-4 py-5 sm:rounded-lg sm:p-6">
                <div class="md:grid md:grid-cols-3 md:gap-6">
                    <div class="md:col-span-1">
                        <h3 class="text-lg font-medium leading-6 text-gray-900">Subscription</h3>
                        <p class="mt-1 text-sm text-gray-500">
                            Manage your subscription and billing information.
                        </p>
                    </div>
                    <div class="mt-5 md:mt-0 md:col-span-2">
                        <div class="rounded-md bg-gray-50 p-4">
                            <div class="flex">
                                <div class="flex-shrink-0">
                                    <i class="fas fa-info-circle text-blue-400"></i>
                                </div>
                                <div class="ml-3">
                                    <h3 class="text-sm font-medium text-blue-800">Current Plan</h3>
                                    <div class="mt-2 text-sm text-blue-700">
                                        <p>You are currently on the <strong><?= ucfirst($subscription['plan']) ?></strong> plan.</p>
                                        <?php if ($subscription['ends_at']): ?>
                                            <p class="mt-1">Your subscription will end on <?= formatDate($subscription['ends_at']) ?>.</p>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="mt-6">
                            <a href="/pricing" 
                               class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                View Plans
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Activity -->
        <div class="mt-10">
            <div class="bg-white shadow px-4 py-5 sm:rounded-lg sm:p-6">
                <div class="md:grid md:grid-cols-3 md:gap-6">
                    <div class="md:col-span-1">
                        <h3 class="text-lg font-medium leading-6 text-gray-900">Recent Activity</h3>
                        <p class="mt-1 text-sm text-gray-500">
                            View your recent account activity.
                        </p>
                    </div>
                    <div class="mt-5 md:mt-0 md:col-span-2">
                        <div class="flow-root">
                            <ul role="list" class="-mb-8">
                                <?php foreach ($activity as $item): ?>
                                    <li>
                                        <div class="relative pb-8">
                                            <span class="absolute top-4 left-4 -ml-px h-full w-0.5 bg-gray-200" aria-hidden="true"></span>
                                            <div class="relative flex space-x-3">
                                                <div>
                                                    <span class="h-8 w-8 rounded-full bg-gray-400 flex items-center justify-center ring-8 ring-white">
                                                        <i class="fas fa-<?= $item['action'] === 'login' ? 'sign-in-alt' : 'user-edit' ?> text-white"></i>
                                                    </span>
                                                </div>
                                                <div class="min-w-0 flex-1 pt-1.5 flex justify-between space-x-4">
                                                    <div>
                                                        <p class="text-sm text-gray-500">
                                                            <?= ucfirst($item['action']) ?> 
                                                            <span class="font-medium text-gray-900">
                                                                from <?= $item['ip_address'] ?>
                                                            </span>
                                                        </p>
                                                    </div>
                                                    <div class="text-right text-sm whitespace-nowrap text-gray-500">
                                                        <time datetime="<?= $item['created_at'] ?>">
                                                            <?= getTimeAgo($item['created_at']) ?>
                                                        </time>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Danger Zone -->
        <div class="mt-10">
            <div class="bg-white shadow px-4 py-5 sm:rounded-lg sm:p-6">
                <div class="md:grid md:grid-cols-3 md:gap-6">
                    <div class="md:col-span-1">
                        <h3 class="text-lg font-medium leading-6 text-gray-900">Delete Account</h3>
                        <p class="mt-1 text-sm text-gray-500">
                            Permanently delete your account and all associated data.
                        </p>
                    </div>
                    <div class="mt-5 md:mt-0 md:col-span-2">
                        <div class="rounded-md bg-red-50 p-4">
                            <div class="flex">
                                <div class="flex-shrink-0">
                                    <i class="fas fa-exclamation-triangle text-red-400"></i>
                                </div>
                                <div class="ml-3">
                                    <h3 class="text-sm font-medium text-red-800">Warning</h3>
                                    <div class="mt-2 text-sm text-red-700">
                                        <p>
                                            This action cannot be undone. This will permanently delete your account and remove all associated data from our servers.
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <form action="/dashboard/settings/delete-account" 
                              method="POST" 
                              class="mt-6" 
                              onsubmit="return confirm('Are you sure you want to delete your account? This action cannot be undone.');">
                            <input type="hidden" name="csrf_token" value="<?= $this->generateCSRFToken() ?>">
                            
                            <div>
                                <label for="password" class="block text-sm font-medium text-gray-700">
                                    Confirm your password
                                </label>
                                <div class="mt-1">
                                    <input type="password" 
                                           name="password" 
                                           id="password" 
                                           required
                                           class="appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-red-500 focus:border-red-500 sm:text-sm">
                                </div>
                            </div>

                            <div class="mt-6">
                                <button type="submit" 
                                        class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                                    Delete account
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
