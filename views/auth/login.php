<div class="min-h-[calc(100vh-16rem)] flex flex-col justify-center py-12 sm:px-6 lg:px-8">
    <div class="sm:mx-auto sm:w-full sm:max-w-md">
        <img class="mx-auto h-12 w-auto" src="/assets/images/logo.png" alt="<?= APP_NAME ?>">
        <h2 class="mt-6 text-center text-3xl font-extrabold text-gray-900">
            Sign in to your account
        </h2>
        <p class="mt-2 text-center text-sm text-gray-600">
            Or
            <a href="/register" class="font-medium text-indigo-600 hover:text-indigo-500">
                create a new account
            </a>
        </p>
    </div>

    <div class="mt-8 sm:mx-auto sm:w-full sm:max-w-md">
        <div class="bg-white py-8 px-4 shadow sm:rounded-lg sm:px-10">
            <!-- Login Form -->
            <form class="space-y-6" action="/login" method="POST">
                <input type="hidden" name="csrf_token" value="<?= $this->generateCSRFToken() ?>">
                
                <!-- Email -->
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700">
                        Email address
                    </label>
                    <div class="mt-1">
                        <input id="email" 
                               name="email" 
                               type="email" 
                               autocomplete="email" 
                               required 
                               value="<?= htmlspecialchars($data['email'] ?? '') ?>"
                               class="appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                    </div>
                    <?php if (isset($errors['email'])): ?>
                        <p class="mt-2 text-sm text-red-600">
                            <?= htmlspecialchars($errors['email']) ?>
                        </p>
                    <?php endif; ?>
                </div>

                <!-- Password -->
                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700">
                        Password
                    </label>
                    <div class="mt-1">
                        <input id="password" 
                               name="password" 
                               type="password" 
                               autocomplete="current-password" 
                               required
                               class="appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                    </div>
                    <?php if (isset($errors['password'])): ?>
                        <p class="mt-2 text-sm text-red-600">
                            <?= htmlspecialchars($errors['password']) ?>
                        </p>
                    <?php endif; ?>
                </div>

                <!-- Remember Me -->
                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <input id="remember_me" 
                               name="remember_me" 
                               type="checkbox"
                               <?= isset($data['remember_me']) ? 'checked' : '' ?>
                               class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
                        <label for="remember_me" class="ml-2 block text-sm text-gray-900">
                            Remember me
                        </label>
                    </div>

                    <div class="text-sm">
                        <a href="/forgot-password" class="font-medium text-indigo-600 hover:text-indigo-500">
                            Forgot your password?
                        </a>
                    </div>
                </div>

                <!-- Submit Button -->
                <div>
                    <button type="submit" 
                            class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        Sign in
                    </button>
                </div>
            </form>

            <!-- Social Login -->
            <div class="mt-6">
                <div class="relative">
                    <div class="absolute inset-0 flex items-center">
                        <div class="w-full border-t border-gray-300"></div>
                    </div>
                    <div class="relative flex justify-center text-sm">
                        <span class="px-2 bg-white text-gray-500">
                            Or continue with
                        </span>
                    </div>
                </div>

                <div class="mt-6 grid grid-cols-2 gap-3">
                    <div>
                        <a href="/auth/google/connect"
                           class="w-full inline-flex justify-center py-2 px-4 border border-gray-300 rounded-md shadow-sm bg-white text-sm font-medium text-gray-500 hover:bg-gray-50">
                            <i class="fab fa-google text-red-600"></i>
                            <span class="sr-only">Sign in with Google</span>
                        </a>
                    </div>

                    <div>
                        <a href="/auth/facebook/connect"
                           class="w-full inline-flex justify-center py-2 px-4 border border-gray-300 rounded-md shadow-sm bg-white text-sm font-medium text-gray-500 hover:bg-gray-50">
                            <i class="fab fa-facebook text-blue-600"></i>
                            <span class="sr-only">Sign in with Facebook</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Help Links -->
    <div class="mt-8 text-center">
        <p class="text-sm text-gray-500">
            Having trouble signing in? 
            <a href="/help/login" class="font-medium text-indigo-600 hover:text-indigo-500">
                View login help
            </a>
        </p>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Focus email field on page load
    document.getElementById('email').focus();

    // Show/hide password functionality
    const togglePassword = document.createElement('button');
    togglePassword.type = 'button';
    togglePassword.className = 'absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-gray-500';
    togglePassword.innerHTML = '<i class="fas fa-eye"></i>';
    togglePassword.addEventListener('click', function() {
        const password = document.getElementById('password');
        const type = password.getAttribute('type') === 'password' ? 'text' : 'password';
        password.setAttribute('type', type);
        this.innerHTML = type === 'password' ? '<i class="fas fa-eye"></i>' : '<i class="fas fa-eye-slash"></i>';
    });

    const passwordWrapper = document.getElementById('password').parentElement;
    passwordWrapper.style.position = 'relative';
    passwordWrapper.appendChild(togglePassword);

    // Form validation
    const form = document.querySelector('form');
    form.addEventListener('submit', function(e) {
        let valid = true;
        const email = document.getElementById('email');
        const password = document.getElementById('password');

        // Simple email validation
        if (!email.value.match(/^[^\s@]+@[^\s@]+\.[^\s@]+$/)) {
            showFieldError(email, 'Please enter a valid email address');
            valid = false;
        } else {
            clearFieldError(email);
        }

        // Password validation
        if (password.value.length < 8) {
            showFieldError(password, 'Password must be at least 8 characters');
            valid = false;
        } else {
            clearFieldError(password);
        }

        if (!valid) {
            e.preventDefault();
        }
    });
});

function showFieldError(field, message) {
    clearFieldError(field);
    field.classList.add('border-red-300');
    field.classList.add('focus:border-red-500');
    field.classList.add('focus:ring-red-500');
    
    const error = document.createElement('p');
    error.className = 'mt-2 text-sm text-red-600';
    error.textContent = message;
    field.parentElement.appendChild(error);
}

function clearFieldError(field) {
    field.classList.remove('border-red-300', 'focus:border-red-500', 'focus:ring-red-500');
    const error = field.parentElement.querySelector('.text-red-600');
    if (error) {
        error.remove();
    }
}
</script>
