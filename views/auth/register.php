<div class="min-h-[calc(100vh-16rem)] flex flex-col justify-center py-12 sm:px-6 lg:px-8">
    <div class="sm:mx-auto sm:w-full sm:max-w-md">
        <img class="mx-auto h-12 w-auto" src="/assets/images/logo.png" alt="<?= APP_NAME ?>">
        <h2 class="mt-6 text-center text-3xl font-extrabold text-gray-900">
            Create your account
        </h2>
        <p class="mt-2 text-center text-sm text-gray-600">
            Already have an account?
            <a href="/login" class="font-medium text-indigo-600 hover:text-indigo-500">
                Sign in
            </a>
        </p>
    </div>

    <div class="mt-8 sm:mx-auto sm:w-full sm:max-w-md">
        <div class="bg-white py-8 px-4 shadow sm:rounded-lg sm:px-10">
            <!-- Registration Form -->
            <form class="space-y-6" action="/register" method="POST" id="register-form">
                <input type="hidden" name="csrf_token" value="<?= $this->generateCSRFToken() ?>">

                <!-- Name Fields -->
                <div class="grid grid-cols-1 gap-y-6 gap-x-4 sm:grid-cols-2">
                    <div>
                        <label for="first_name" class="block text-sm font-medium text-gray-700">
                            First name
                        </label>
                        <div class="mt-1">
                            <input type="text" 
                                   name="first_name" 
                                   id="first_name" 
                                   autocomplete="given-name" 
                                   required
                                   value="<?= htmlspecialchars($data['first_name'] ?? '') ?>"
                                   class="appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                        </div>
                        <?php if (isset($errors['first_name'])): ?>
                            <p class="mt-2 text-sm text-red-600">
                                <?= htmlspecialchars($errors['first_name']) ?>
                            </p>
                        <?php endif; ?>
                    </div>

                    <div>
                        <label for="last_name" class="block text-sm font-medium text-gray-700">
                            Last name
                        </label>
                        <div class="mt-1">
                            <input type="text" 
                                   name="last_name" 
                                   id="last_name" 
                                   autocomplete="family-name" 
                                   required
                                   value="<?= htmlspecialchars($data['last_name'] ?? '') ?>"
                                   class="appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                        </div>
                        <?php if (isset($errors['last_name'])): ?>
                            <p class="mt-2 text-sm text-red-600">
                                <?= htmlspecialchars($errors['last_name']) ?>
                            </p>
                        <?php endif; ?>
                    </div>
                </div>

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
                    <div class="mt-1 relative">
                        <input id="password" 
                               name="password" 
                               type="password" 
                               required
                               class="appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                        <div class="absolute inset-y-0 right-0 pr-3 flex items-center">
                            <button type="button" 
                                    onclick="togglePasswordVisibility('password')"
                                    class="text-gray-400 hover:text-gray-500 focus:outline-none">
                                <i class="fas fa-eye"></i>
                            </button>
                        </div>
                    </div>
                    <?php if (isset($errors['password'])): ?>
                        <p class="mt-2 text-sm text-red-600">
                            <?= htmlspecialchars($errors['password']) ?>
                        </p>
                    <?php endif; ?>
                    <div class="mt-2">
                        <div class="text-sm text-gray-500">
                            Password must:
                            <ul class="mt-1 space-y-1">
                                <li id="length-check" class="flex items-center">
                                    <i class="far fa-circle text-xs mr-2"></i>
                                    Be at least 8 characters
                                </li>
                                <li id="uppercase-check" class="flex items-center">
                                    <i class="far fa-circle text-xs mr-2"></i>
                                    Include uppercase letter
                                </li>
                                <li id="number-check" class="flex items-center">
                                    <i class="far fa-circle text-xs mr-2"></i>
                                    Include number
                                </li>
                                <li id="special-check" class="flex items-center">
                                    <i class="far fa-circle text-xs mr-2"></i>
                                    Include special character
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- Confirm Password -->
                <div>
                    <label for="password_confirmation" class="block text-sm font-medium text-gray-700">
                        Confirm password
                    </label>
                    <div class="mt-1 relative">
                        <input id="password_confirmation" 
                               name="password_confirmation" 
                               type="password" 
                               required
                               class="appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                        <div class="absolute inset-y-0 right-0 pr-3 flex items-center">
                            <button type="button" 
                                    onclick="togglePasswordVisibility('password_confirmation')"
                                    class="text-gray-400 hover:text-gray-500 focus:outline-none">
                                <i class="fas fa-eye"></i>
                            </button>
                        </div>
                    </div>
                    <?php if (isset($errors['password_confirmation'])): ?>
                        <p class="mt-2 text-sm text-red-600">
                            <?= htmlspecialchars($errors['password_confirmation']) ?>
                        </p>
                    <?php endif; ?>
                </div>

                <!-- Terms and Privacy -->
                <div class="flex items-start">
                    <div class="flex items-center h-5">
                        <input id="terms" 
                               name="terms" 
                               type="checkbox" 
                               required
                               <?= isset($data['terms']) ? 'checked' : '' ?>
                               class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
                    </div>
                    <div class="ml-3 text-sm">
                        <label for="terms" class="font-medium text-gray-700">
                            I agree to the 
                            <a href="/terms" class="text-indigo-600 hover:text-indigo-500" target="_blank">
                                Terms of Service
                            </a> 
                            and 
                            <a href="/privacy" class="text-indigo-600 hover:text-indigo-500" target="_blank">
                                Privacy Policy
                            </a>
                        </label>
                    </div>
                </div>

                <!-- Submit Button -->
                <div>
                    <button type="submit" 
                            class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        Create account
                    </button>
                </div>
            </form>

            <!-- Social Registration -->
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
                            <span class="sr-only">Sign up with Google</span>
                        </a>
                    </div>

                    <div>
                        <a href="/auth/facebook/connect"
                           class="w-full inline-flex justify-center py-2 px-4 border border-gray-300 rounded-md shadow-sm bg-white text-sm font-medium text-gray-500 hover:bg-gray-50">
                            <i class="fab fa-facebook text-blue-600"></i>
                            <span class="sr-only">Sign up with Facebook</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('register-form');
    const password = document.getElementById('password');
    const confirmation = document.getElementById('password_confirmation');
    
    // Password validation
    password.addEventListener('input', validatePassword);
    
    // Form validation
    form.addEventListener('submit', function(e) {
        if (!validateForm()) {
            e.preventDefault();
        }
    });
});

function validatePassword() {
    const password = document.getElementById('password').value;
    
    // Update password requirement checks
    updateCheck('length-check', password.length >= 8);
    updateCheck('uppercase-check', /[A-Z]/.test(password));
    updateCheck('number-check', /[0-9]/.test(password));
    updateCheck('special-check', /[^A-Za-z0-9]/.test(password));
}

function updateCheck(id, valid) {
    const element = document.getElementById(id);
    const icon = element.querySelector('i');
    
    if (valid) {
        icon.className = 'fas fa-check-circle text-xs mr-2 text-green-500';
        element.classList.add('text-green-500');
    } else {
        icon.className = 'far fa-circle text-xs mr-2';
        element.classList.remove('text-green-500');
    }
}

function validateForm() {
    const password = document.getElementById('password').value;
    const confirmation = document.getElementById('password_confirmation').value;
    let valid = true;
    
    // Password requirements
    if (password.length < 8 || 
        !/[A-Z]/.test(password) || 
        !/[0-9]/.test(password) || 
        !/[^A-Za-z0-9]/.test(password)) {
        showError('password', 'Password does not meet requirements');
        valid = false;
    } else {
        clearError('password');
    }
    
    // Password confirmation
    if (password !== confirmation) {
        showError('password_confirmation', 'Passwords do not match');
        valid = false;
    } else {
        clearError('password_confirmation');
    }
    
    return valid;
}

function showError(fieldId, message) {
    const field = document.getElementById(fieldId);
    clearError(fieldId);
    
    field.classList.add('border-red-300', 'focus:border-red-500', 'focus:ring-red-500');
    
    const error = document.createElement('p');
    error.className = 'mt-2 text-sm text-red-600';
    error.textContent = message;
    field.parentElement.appendChild(error);
}

function clearError(fieldId) {
    const field = document.getElementById(fieldId);
    field.classList.remove('border-red-300', 'focus:border-red-500', 'focus:ring-red-500');
    
    const error = field.parentElement.querySelector('.text-red-600');
    if (error) {
        error.remove();
    }
}

function togglePasswordVisibility(fieldId) {
    const field = document.getElementById(fieldId);
    const button = field.parentElement.querySelector('button');
    const icon = button.querySelector('i');
    
    if (field.type === 'password') {
        field.type = 'text';
        icon.className = 'fas fa-eye-slash';
    } else {
        field.type = 'password';
        icon.className = 'fas fa-eye';
    }
}
</script>
