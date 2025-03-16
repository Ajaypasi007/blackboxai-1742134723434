<!DOCTYPE html>
<html lang="en" class="h-full bg-gray-50">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle ?? APP_NAME) ?></title>
    
    <!-- Favicon -->
    <link rel="icon" type="image/png" href="/assets/images/favicon.png">
    
    <!-- Meta tags -->
    <meta name="description" content="Manage your social media presence across all platforms from one central dashboard.">
    <meta name="keywords" content="social media, management, analytics, scheduling, dashboard">
    
    <!-- Open Graph tags -->
    <meta property="og:title" content="<?= htmlspecialchars($pageTitle ?? APP_NAME) ?>">
    <meta property="og:description" content="Manage your social media presence across all platforms from one central dashboard.">
    <meta property="og:image" content="<?= APP_URL ?>/assets/images/og-image.png">
    <meta property="og:url" content="<?= APP_URL . $_SERVER['REQUEST_URI'] ?>">
    
    <!-- Twitter Card tags -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="<?= htmlspecialchars($pageTitle ?? APP_NAME) ?>">
    <meta name="twitter:description" content="Manage your social media presence across all platforms from one central dashboard.">
    <meta name="twitter:image" content="<?= APP_URL ?>/assets/images/twitter-card.png">
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Custom CSS -->
    <link rel="stylesheet" href="/assets/css/custom.css">
    
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    
    <!-- Axios -->
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    
    <!-- Alpine.js -->
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    
    <!-- Custom JavaScript -->
    <script src="/assets/js/custom.js"></script>
</head>
<body class="h-full">
    <!-- Notification Container -->
    <div id="notification-container" class="fixed top-4 right-4 z-50 space-y-2"></div>

    <?php if ($this->isAuthenticated()): ?>
        <!-- Dashboard Layout -->
        <div class="min-h-full">
            <!-- Navigation -->
            <nav class="bg-white shadow">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    <div class="flex justify-between h-16">
                        <div class="flex">
                            <!-- Logo -->
                            <div class="flex-shrink-0 flex items-center">
                                <a href="/dashboard">
                                    <img class="h-8 w-auto" src="/assets/images/logo.png" alt="<?= APP_NAME ?>">
                                </a>
                            </div>

                            <!-- Navigation Links -->
                            <div class="hidden sm:ml-6 sm:flex sm:space-x-8">
                                <a href="/dashboard" 
                                   class="<?= $_SERVER['REQUEST_URI'] === '/dashboard' ? 'border-indigo-500 text-gray-900' : 'border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700' ?> inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium">
                                    Dashboard
                                </a>
                                <a href="/dashboard/posts" 
                                   class="<?= strpos($_SERVER['REQUEST_URI'], '/dashboard/posts') === 0 ? 'border-indigo-500 text-gray-900' : 'border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700' ?> inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium">
                                    Posts
                                </a>
                                <a href="/dashboard/accounts" 
                                   class="<?= strpos($_SERVER['REQUEST_URI'], '/dashboard/accounts') === 0 ? 'border-indigo-500 text-gray-900' : 'border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700' ?> inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium">
                                    Accounts
                                </a>
                                <a href="/dashboard/analytics" 
                                   class="<?= strpos($_SERVER['REQUEST_URI'], '/dashboard/analytics') === 0 ? 'border-indigo-500 text-gray-900' : 'border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700' ?> inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium">
                                    Analytics
                                </a>
                            </div>
                        </div>

                        <!-- Right side navigation -->
                        <div class="hidden sm:ml-6 sm:flex sm:items-center">
                            <!-- Notifications -->
                            <div class="ml-3 relative" x-data="{ open: false }">
                                <button @click="open = !open" 
                                        class="bg-white p-1 rounded-full text-gray-400 hover:text-gray-500 focus:outline-none">
                                    <span class="sr-only">View notifications</span>
                                    <i class="fas fa-bell"></i>
                                    <?php if (!empty($unreadNotifications)): ?>
                                        <span class="absolute top-0 right-0 block h-2 w-2 rounded-full bg-red-400"></span>
                                    <?php endif; ?>
                                </button>

                                <!-- Notifications dropdown -->
                                <div x-show="open" 
                                     @click.away="open = false"
                                     class="origin-top-right absolute right-0 mt-2 w-80 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 divide-y divide-gray-100">
                                    <div class="py-1">
                                        <?php if (empty($notifications)): ?>
                                            <div class="px-4 py-2 text-sm text-gray-500">
                                                No new notifications
                                            </div>
                                        <?php else: ?>
                                            <?php foreach ($notifications as $notification): ?>
                                                <a href="<?= $notification['action_url'] ?>" 
                                                   class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                                    <p class="font-medium"><?= htmlspecialchars($notification['title']) ?></p>
                                                    <p class="text-gray-500"><?= htmlspecialchars($notification['message']) ?></p>
                                                    <p class="text-xs text-gray-400 mt-1"><?= getTimeAgo($notification['created_at']) ?></p>
                                                </a>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>

                            <!-- Profile dropdown -->
                            <div class="ml-3 relative" x-data="{ open: false }">
                                <button @click="open = !open" 
                                        class="bg-white rounded-full flex text-sm focus:outline-none">
                                    <span class="sr-only">Open user menu</span>
                                    <img class="h-8 w-8 rounded-full" 
                                         src="https://ui-avatars.com/api/?name=<?= urlencode($this->user['first_name'] . ' ' . $this->user['last_name']) ?>&background=6366f1&color=fff" 
                                         alt="">
                                </button>

                                <!-- Profile menu -->
                                <div x-show="open" 
                                     @click.away="open = false"
                                     class="origin-top-right absolute right-0 mt-2 w-48 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5">
                                    <div class="py-1">
                                        <a href="/dashboard/settings" 
                                           class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                            Settings
                                        </a>
                                        <a href="/help" 
                                           class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                            Help
                                        </a>
                                        <form action="/logout" method="post" class="block">
                                            <input type="hidden" name="csrf_token" value="<?= $this->generateCSRFToken() ?>">
                                            <button type="submit" 
                                                    class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                                Sign out
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Mobile menu button -->
                        <div class="flex items-center sm:hidden">
                            <button type="button" 
                                    @click="mobileMenuOpen = !mobileMenuOpen"
                                    class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none">
                                <span class="sr-only">Open main menu</span>
                                <i class="fas fa-bars"></i>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Mobile menu -->
                <div x-show="mobileMenuOpen" class="sm:hidden">
                    <div class="pt-2 pb-3 space-y-1">
                        <a href="/dashboard" 
                           class="<?= $_SERVER['REQUEST_URI'] === '/dashboard' ? 'bg-indigo-50 border-indigo-500 text-indigo-700' : 'border-transparent text-gray-500 hover:bg-gray-50 hover:border-gray-300 hover:text-gray-700' ?> block pl-3 pr-4 py-2 border-l-4 text-base font-medium">
                            Dashboard
                        </a>
                        <a href="/dashboard/posts" 
                           class="<?= strpos($_SERVER['REQUEST_URI'], '/dashboard/posts') === 0 ? 'bg-indigo-50 border-indigo-500 text-indigo-700' : 'border-transparent text-gray-500 hover:bg-gray-50 hover:border-gray-300 hover:text-gray-700' ?> block pl-3 pr-4 py-2 border-l-4 text-base font-medium">
                            Posts
                        </a>
                        <a href="/dashboard/accounts" 
                           class="<?= strpos($_SERVER['REQUEST_URI'], '/dashboard/accounts') === 0 ? 'bg-indigo-50 border-indigo-500 text-indigo-700' : 'border-transparent text-gray-500 hover:bg-gray-50 hover:border-gray-300 hover:text-gray-700' ?> block pl-3 pr-4 py-2 border-l-4 text-base font-medium">
                            Accounts
                        </a>
                        <a href="/dashboard/analytics" 
                           class="<?= strpos($_SERVER['REQUEST_URI'], '/dashboard/analytics') === 0 ? 'bg-indigo-50 border-indigo-500 text-indigo-700' : 'border-transparent text-gray-500 hover:bg-gray-50 hover:border-gray-300 hover:text-gray-700' ?> block pl-3 pr-4 py-2 border-l-4 text-base font-medium">
                            Analytics
                        </a>
                    </div>
                </div>
            </nav>
        </div>
    <?php else: ?>
        <!-- Public Navigation -->
        <nav class="bg-white shadow">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between h-16">
                    <div class="flex">
                        <!-- Logo -->
                        <div class="flex-shrink-0 flex items-center">
                            <a href="/">
                                <img class="h-8 w-auto" src="/assets/images/logo.png" alt="<?= APP_NAME ?>">
                            </a>
                        </div>

                        <!-- Navigation Links -->
                        <div class="hidden sm:ml-6 sm:flex sm:space-x-8">
                            <a href="/features" 
                               class="<?= $_SERVER['REQUEST_URI'] === '/features' ? 'border-indigo-500 text-gray-900' : 'border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700' ?> inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium">
                                Features
                            </a>
                            <a href="/pricing" 
                               class="<?= $_SERVER['REQUEST_URI'] === '/pricing' ? 'border-indigo-500 text-gray-900' : 'border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700' ?> inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium">
                                Pricing
                            </a>
                            <a href="/about" 
                               class="<?= $_SERVER['REQUEST_URI'] === '/about' ? 'border-indigo-500 text-gray-900' : 'border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700' ?> inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium">
                                About
                            </a>
                            <a href="/contact" 
                               class="<?= $_SERVER['REQUEST_URI'] === '/contact' ? 'border-indigo-500 text-gray-900' : 'border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700' ?> inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium">
                                Contact
                            </a>
                        </div>
                    </div>

                    <!-- Right side navigation -->
                    <div class="hidden sm:ml-6 sm:flex sm:items-center">
                        <a href="/login" 
                           class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-indigo-600 bg-white hover:bg-gray-50">
                            Sign in
                        </a>
                        <a href="/register" 
                           class="ml-3 inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700">
                            Sign up
                        </a>
                    </div>

                    <!-- Mobile menu button -->
                    <div class="flex items-center sm:hidden">
                        <button type="button" 
                                @click="mobileMenuOpen = !mobileMenuOpen"
                                class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none">
                            <span class="sr-only">Open main menu</span>
                            <i class="fas fa-bars"></i>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Mobile menu -->
            <div x-show="mobileMenuOpen" class="sm:hidden">
                <div class="pt-2 pb-3 space-y-1">
                    <a href="/features" 
                       class="<?= $_SERVER['REQUEST_URI'] === '/features' ? 'bg-indigo-50 border-indigo-500 text-indigo-700' : 'border-transparent text-gray-500 hover:bg-gray-50 hover:border-gray-300 hover:text-gray-700' ?> block pl-3 pr-4 py-2 border-l-4 text-base font-medium">
                        Features
                    </a>
                    <a href="/pricing" 
                       class="<?= $_SERVER['REQUEST_URI'] === '/pricing' ? 'bg-indigo-50 border-indigo-500 text-indigo-700' : 'border-transparent text-gray-500 hover:bg-gray-50 hover:border-gray-300 hover:text-gray-700' ?> block pl-3 pr-4 py-2 border-l-4 text-base font-medium">
                        Pricing
                    </a>
                    <a href="/about" 
                       class="<?= $_SERVER['REQUEST_URI'] === '/about' ? 'bg-indigo-50 border-indigo-500 text-indigo-700' : 'border-transparent text-gray-500 hover:bg-gray-50 hover:border-gray-300 hover:text-gray-700' ?> block pl-3 pr-4 py-2 border-l-4 text-base font-medium">
                        About
                    </a>
                    <a href="/contact" 
                       class="<?= $_SERVER['REQUEST_URI'] === '/contact' ? 'bg-indigo-50 border-indigo-500 text-indigo-700' : 'border-transparent text-gray-500 hover:bg-gray-50 hover:border-gray-300 hover:text-gray-700' ?> block pl-3 pr-4 py-2 border-l-4 text-base font-medium">
                        Contact
                    </a>
                </div>
                <div class="pt-4 pb-3 border-t border-gray-200">
                    <div class="space-y-1">
                        <a href="/login" 
                           class="block px-4 py-2 text-base font-medium text-gray-500 hover:text-gray-800 hover:bg-gray-100">
                            Sign in
                        </a>
                        <a href="/register" 
                           class="block px-4 py-2 text-base font-medium text-gray-500 hover:text-gray-800 hover:bg-gray-100">
                            Sign up
                        </a>
                    </div>
                </div>
            </div>
        </nav>
    <?php endif; ?>

    <!-- Flash Messages -->
    <?php if (!empty($flash)): ?>
        <?php foreach ($flash as $type => $messages): ?>
            <?php foreach ($messages as $message): ?>
                <div class="flash-message" 
                     data-type="<?= $type ?>" 
                     data-message="<?= htmlspecialchars($message) ?>">
                </div>
            <?php endforeach; ?>
        <?php endforeach; ?>
    <?php endif; ?>

    <!-- Main Content -->
    <main>
