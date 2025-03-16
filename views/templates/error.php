<div class="min-h-[calc(100vh-16rem)] bg-white px-4 py-16 sm:px-6 sm:py-24 md:grid md:place-items-center lg:px-8">
    <div class="max-w-max mx-auto">
        <main class="sm:flex">
            <p class="text-4xl font-extrabold text-red-600 sm:text-5xl">500</p>
            <div class="sm:ml-6">
                <div class="sm:border-l sm:border-gray-200 sm:pl-6">
                    <h1 class="text-4xl font-extrabold text-gray-900 tracking-tight sm:text-5xl">Something went wrong</h1>
                    <p class="mt-1 text-base text-gray-500">
                        <?php if (APP_DEBUG): ?>
                            <?= htmlspecialchars($error->getMessage()) ?>
                        <?php else: ?>
                            We're experiencing some technical difficulties. Please try again later.
                        <?php endif; ?>
                    </p>
                </div>
                <div class="mt-10 flex space-x-3 sm:border-l sm:border-transparent sm:pl-6">
                    <button onclick="window.location.reload()" 
                            class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                        <i class="fas fa-redo mr-2"></i>
                        Try again
                    </button>
                    <a href="/" 
                       class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-red-700 bg-red-100 hover:bg-red-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                        Go back home
                    </a>
                </div>
            </div>
        </main>
    </div>

    <!-- Error Details (Debug Mode) -->
    <?php if (APP_DEBUG && isset($error)): ?>
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-16">
            <div class="bg-white shadow overflow-hidden sm:rounded-lg">
                <div class="px-4 py-5 sm:px-6">
                    <h3 class="text-lg leading-6 font-medium text-gray-900">
                        Error Details
                    </h3>
                    <p class="mt-1 max-w-2xl text-sm text-gray-500">
                        Technical information about the error.
                    </p>
                </div>
                <div class="border-t border-gray-200 px-4 py-5 sm:px-6">
                    <dl class="grid grid-cols-1 gap-x-4 gap-y-8 sm:grid-cols-2">
                        <div class="sm:col-span-1">
                            <dt class="text-sm font-medium text-gray-500">
                                Error Type
                            </dt>
                            <dd class="mt-1 text-sm text-gray-900">
                                <?= get_class($error) ?>
                            </dd>
                        </div>
                        <div class="sm:col-span-1">
                            <dt class="text-sm font-medium text-gray-500">
                                Error Code
                            </dt>
                            <dd class="mt-1 text-sm text-gray-900">
                                <?= $error->getCode() ?>
                            </dd>
                        </div>
                        <div class="sm:col-span-2">
                            <dt class="text-sm font-medium text-gray-500">
                                Message
                            </dt>
                            <dd class="mt-1 text-sm text-gray-900">
                                <?= htmlspecialchars($error->getMessage()) ?>
                            </dd>
                        </div>
                        <div class="sm:col-span-2">
                            <dt class="text-sm font-medium text-gray-500">
                                File
                            </dt>
                            <dd class="mt-1 text-sm text-gray-900">
                                <?= $error->getFile() ?> (line <?= $error->getLine() ?>)
                            </dd>
                        </div>
                        <div class="sm:col-span-2">
                            <dt class="text-sm font-medium text-gray-500">
                                Stack Trace
                            </dt>
                            <dd class="mt-1 text-sm text-gray-900">
                                <div class="bg-gray-50 p-4 rounded-md overflow-auto max-h-96">
                                    <pre class="text-xs"><?= htmlspecialchars($error->getTraceAsString()) ?></pre>
                                </div>
                            </dd>
                        </div>
                    </dl>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <!-- Support Options -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-16">
        <h2 class="text-lg font-medium text-gray-900">Need help?</h2>
        <div class="mt-6 grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
            <a href="/help" 
               class="relative rounded-lg border border-gray-300 bg-white px-6 py-5 shadow-sm flex items-center space-x-3 hover:border-gray-400 focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-red-500">
                <div class="flex-shrink-0">
                    <i class="fas fa-question-circle text-red-600 text-2xl"></i>
                </div>
                <div class="flex-1 min-w-0">
                    <span class="absolute inset-0" aria-hidden="true"></span>
                    <p class="text-sm font-medium text-gray-900">Help Center</p>
                    <p class="text-sm text-gray-500">Browse our documentation</p>
                </div>
            </a>

            <a href="/contact" 
               class="relative rounded-lg border border-gray-300 bg-white px-6 py-5 shadow-sm flex items-center space-x-3 hover:border-gray-400 focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-red-500">
                <div class="flex-shrink-0">
                    <i class="fas fa-envelope text-red-600 text-2xl"></i>
                </div>
                <div class="flex-1 min-w-0">
                    <span class="absolute inset-0" aria-hidden="true"></span>
                    <p class="text-sm font-medium text-gray-900">Contact Support</p>
                    <p class="text-sm text-gray-500">Get in touch with our team</p>
                </div>
            </a>

            <a href="/status" 
               class="relative rounded-lg border border-gray-300 bg-white px-6 py-5 shadow-sm flex items-center space-x-3 hover:border-gray-400 focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-red-500">
                <div class="flex-shrink-0">
                    <i class="fas fa-server text-red-600 text-2xl"></i>
                </div>
                <div class="flex-1 min-w-0">
                    <span class="absolute inset-0" aria-hidden="true"></span>
                    <p class="text-sm font-medium text-gray-900">System Status</p>
                    <p class="text-sm text-gray-500">Check service status</p>
                </div>
            </a>
        </div>
    </div>
</div>

<!-- Report Error -->
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12 text-center">
    <p class="text-base text-gray-500">
        If this error persists, please 
        <a href="/contact" class="font-medium text-red-600 hover:text-red-500">
            report it to our support team <span aria-hidden="true">&rarr;</span>
        </a>
    </p>
</div>

<!-- Error Tracking (Production Only) -->
<?php if (!APP_DEBUG): ?>
    <script>
    // Send error details to our monitoring service
    const errorDetails = {
        url: window.location.href,
        timestamp: new Date().toISOString(),
        userAgent: navigator.userAgent,
        errorId: '<?= $errorId ?? uniqid() ?>'
    };

    // Log error to monitoring service
    fetch('/api/log-error', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify(errorDetails)
    }).catch(console.error);
    </script>
<?php endif; ?>
