<div class="min-h-[calc(100vh-16rem)] bg-white px-4 py-16 sm:px-6 sm:py-24 md:grid md:place-items-center lg:px-8">
    <div class="max-w-max mx-auto">
        <main class="sm:flex">
            <p class="text-4xl font-extrabold text-indigo-600 sm:text-5xl">404</p>
            <div class="sm:ml-6">
                <div class="sm:border-l sm:border-gray-200 sm:pl-6">
                    <h1 class="text-4xl font-extrabold text-gray-900 tracking-tight sm:text-5xl">Page not found</h1>
                    <p class="mt-1 text-base text-gray-500">Please check the URL in the address bar and try again.</p>
                </div>
                <div class="mt-10 flex space-x-3 sm:border-l sm:border-transparent sm:pl-6">
                    <a href="/" 
                       class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        Go back home
                    </a>
                    <a href="/help" 
                       class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-indigo-700 bg-indigo-100 hover:bg-indigo-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        Visit help center
                    </a>
                </div>
            </div>
        </main>
    </div>

    <!-- Suggested Links -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-16">
        <h2 class="text-lg font-medium text-gray-900">Popular pages you might be looking for:</h2>
        <div class="mt-6 grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
            <a href="/features" 
               class="relative rounded-lg border border-gray-300 bg-white px-6 py-5 shadow-sm flex items-center space-x-3 hover:border-gray-400 focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-indigo-500">
                <div class="flex-shrink-0">
                    <i class="fas fa-star text-indigo-600 text-2xl"></i>
                </div>
                <div class="flex-1 min-w-0">
                    <span class="absolute inset-0" aria-hidden="true"></span>
                    <p class="text-sm font-medium text-gray-900">Features</p>
                    <p class="text-sm text-gray-500">Explore our features</p>
                </div>
            </a>

            <a href="/dashboard" 
               class="relative rounded-lg border border-gray-300 bg-white px-6 py-5 shadow-sm flex items-center space-x-3 hover:border-gray-400 focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-indigo-500">
                <div class="flex-shrink-0">
                    <i class="fas fa-chart-line text-indigo-600 text-2xl"></i>
                </div>
                <div class="flex-1 min-w-0">
                    <span class="absolute inset-0" aria-hidden="true"></span>
                    <p class="text-sm font-medium text-gray-900">Dashboard</p>
                    <p class="text-sm text-gray-500">Go to your dashboard</p>
                </div>
            </a>

            <a href="/pricing" 
               class="relative rounded-lg border border-gray-300 bg-white px-6 py-5 shadow-sm flex items-center space-x-3 hover:border-gray-400 focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-indigo-500">
                <div class="flex-shrink-0">
                    <i class="fas fa-tag text-indigo-600 text-2xl"></i>
                </div>
                <div class="flex-1 min-w-0">
                    <span class="absolute inset-0" aria-hidden="true"></span>
                    <p class="text-sm font-medium text-gray-900">Pricing</p>
                    <p class="text-sm text-gray-500">View our plans</p>
                </div>
            </a>

            <a href="/contact" 
               class="relative rounded-lg border border-gray-300 bg-white px-6 py-5 shadow-sm flex items-center space-x-3 hover:border-gray-400 focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-indigo-500">
                <div class="flex-shrink-0">
                    <i class="fas fa-envelope text-indigo-600 text-2xl"></i>
                </div>
                <div class="flex-1 min-w-0">
                    <span class="absolute inset-0" aria-hidden="true"></span>
                    <p class="text-sm font-medium text-gray-900">Contact</p>
                    <p class="text-sm text-gray-500">Get in touch</p>
                </div>
            </a>
        </div>
    </div>

    <!-- Search Box -->
    <div class="max-w-xl mx-auto px-4 sm:px-6 lg:px-8 mt-12">
        <form action="/search" method="get" class="mt-6">
            <label for="search" class="sr-only">Search</label>
            <div class="relative rounded-md shadow-sm">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <i class="fas fa-search text-gray-400"></i>
                </div>
                <input type="text" 
                       name="q" 
                       id="search" 
                       class="focus:ring-indigo-500 focus:border-indigo-500 block w-full pl-10 sm:text-sm border-gray-300 rounded-md" 
                       placeholder="Search our website...">
                <div class="absolute inset-y-0 right-0 flex py-1.5 pr-1.5">
                    <kbd class="inline-flex items-center border border-gray-200 rounded px-2 text-sm font-sans font-medium text-gray-400">
                        ⏎
                    </kbd>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Report broken link -->
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12 text-center">
    <p class="text-base text-gray-500">
        Found a broken link? 
        <a href="/contact" class="font-medium text-indigo-600 hover:text-indigo-500">
            Let us know <span aria-hidden="true">&rarr;</span>
        </a>
    </p>
</div>
