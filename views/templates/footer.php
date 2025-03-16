</main>

    <!-- Footer -->
    <footer class="bg-white">
        <div class="max-w-7xl mx-auto py-12 px-4 sm:px-6 lg:px-8">
            <div class="xl:grid xl:grid-cols-3 xl:gap-8">
                <!-- Brand -->
                <div class="space-y-8 xl:col-span-1">
                    <img class="h-10" src="/assets/images/logo.png" alt="<?= APP_NAME ?>">
                    <p class="text-gray-500 text-base">
                        Manage your social media presence across all platforms from one central dashboard.
                    </p>
                    <div class="flex space-x-6">
                        <a href="#" class="text-gray-400 hover:text-gray-500">
                            <span class="sr-only">Facebook</span>
                            <i class="fab fa-facebook text-xl"></i>
                        </a>
                        <a href="#" class="text-gray-400 hover:text-gray-500">
                            <span class="sr-only">Twitter</span>
                            <i class="fab fa-twitter text-xl"></i>
                        </a>
                        <a href="#" class="text-gray-400 hover:text-gray-500">
                            <span class="sr-only">Instagram</span>
                            <i class="fab fa-instagram text-xl"></i>
                        </a>
                        <a href="#" class="text-gray-400 hover:text-gray-500">
                            <span class="sr-only">LinkedIn</span>
                            <i class="fab fa-linkedin text-xl"></i>
                        </a>
                    </div>
                </div>

                <!-- Navigation -->
                <div class="mt-12 grid grid-cols-2 gap-8 xl:mt-0 xl:col-span-2">
                    <div class="md:grid md:grid-cols-2 md:gap-8">
                        <div>
                            <h3 class="text-sm font-semibold text-gray-400 tracking-wider uppercase">
                                Product
                            </h3>
                            <ul class="mt-4 space-y-4">
                                <li>
                                    <a href="/features" class="text-base text-gray-500 hover:text-gray-900">
                                        Features
                                    </a>
                                </li>
                                <li>
                                    <a href="/pricing" class="text-base text-gray-500 hover:text-gray-900">
                                        Pricing
                                    </a>
                                </li>
                                <li>
                                    <a href="/security" class="text-base text-gray-500 hover:text-gray-900">
                                        Security
                                    </a>
                                </li>
                                <li>
                                    <a href="/integrations" class="text-base text-gray-500 hover:text-gray-900">
                                        Integrations
                                    </a>
                                </li>
                            </ul>
                        </div>
                        <div class="mt-12 md:mt-0">
                            <h3 class="text-sm font-semibold text-gray-400 tracking-wider uppercase">
                                Support
                            </h3>
                            <ul class="mt-4 space-y-4">
                                <li>
                                    <a href="/help" class="text-base text-gray-500 hover:text-gray-900">
                                        Help Center
                                    </a>
                                </li>
                                <li>
                                    <a href="/guides" class="text-base text-gray-500 hover:text-gray-900">
                                        Guides
                                    </a>
                                </li>
                                <li>
                                    <a href="/api" class="text-base text-gray-500 hover:text-gray-900">
                                        API Documentation
                                    </a>
                                </li>
                                <li>
                                    <a href="/status" class="text-base text-gray-500 hover:text-gray-900">
                                        Status
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </div>
                    <div class="md:grid md:grid-cols-2 md:gap-8">
                        <div>
                            <h3 class="text-sm font-semibold text-gray-400 tracking-wider uppercase">
                                Company
                            </h3>
                            <ul class="mt-4 space-y-4">
                                <li>
                                    <a href="/about" class="text-base text-gray-500 hover:text-gray-900">
                                        About
                                    </a>
                                </li>
                                <li>
                                    <a href="/blog" class="text-base text-gray-500 hover:text-gray-900">
                                        Blog
                                    </a>
                                </li>
                                <li>
                                    <a href="/careers" class="text-base text-gray-500 hover:text-gray-900">
                                        Careers
                                    </a>
                                </li>
                                <li>
                                    <a href="/contact" class="text-base text-gray-500 hover:text-gray-900">
                                        Contact
                                    </a>
                                </li>
                            </ul>
                        </div>
                        <div class="mt-12 md:mt-0">
                            <h3 class="text-sm font-semibold text-gray-400 tracking-wider uppercase">
                                Legal
                            </h3>
                            <ul class="mt-4 space-y-4">
                                <li>
                                    <a href="/privacy" class="text-base text-gray-500 hover:text-gray-900">
                                        Privacy
                                    </a>
                                </li>
                                <li>
                                    <a href="/terms" class="text-base text-gray-500 hover:text-gray-900">
                                        Terms
                                    </a>
                                </li>
                                <li>
                                    <a href="/cookies" class="text-base text-gray-500 hover:text-gray-900">
                                        Cookie Policy
                                    </a>
                                </li>
                                <li>
                                    <a href="/licenses" class="text-base text-gray-500 hover:text-gray-900">
                                        Licenses
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Bottom bar -->
            <div class="mt-12 border-t border-gray-200 pt-8">
                <p class="text-base text-gray-400 xl:text-center">
                    &copy; <?= date('Y') ?> <?= APP_NAME ?>. All rights reserved.
                </p>
            </div>
        </div>
    </footer>

    <!-- Initialize flash messages -->
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // Show flash messages
        document.querySelectorAll('.flash-message').forEach(function(element) {
            showNotification(
                element.dataset.type,
                element.dataset.message
            );
        });
        
        // Initialize mobile menu
        window.mobileMenuOpen = false;
    });
    </script>

</body>
</html>
