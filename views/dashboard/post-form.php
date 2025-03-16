<div class="py-6">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-semibold text-gray-900">
                    <?= isset($post) ? 'Edit Post' : 'Create New Post' ?>
                </h1>
                <p class="mt-1 text-sm text-gray-500">
                    Compose and schedule your social media content
                </p>
            </div>
            <div>
                <a href="/dashboard/posts" 
                   class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    <i class="fas fa-arrow-left mr-2"></i>
                    Back to Posts
                </a>
            </div>
        </div>

        <form id="post-form" 
              action="<?= isset($post) ? "/dashboard/posts/update/{$post['id']}" : '/dashboard/posts/create' ?>" 
              method="POST" 
              enctype="multipart/form-data" 
              class="mt-6">
            
            <input type="hidden" name="csrf_token" value="<?= $this->generateCSRFToken() ?>">

            <!-- Content Editor -->
            <div class="bg-white shadow sm:rounded-lg">
                <div class="px-4 py-5 sm:p-6">
                    <div class="grid grid-cols-1 gap-6">
                        <!-- Platform Selection -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700">
                                Select Platforms
                            </label>
                            <div class="mt-2 grid grid-cols-1 gap-3 sm:grid-cols-2 lg:grid-cols-4">
                                <?php foreach ($accounts as $account): ?>
                                    <label class="relative flex items-center px-4 py-3 border rounded-lg shadow-sm cursor-pointer hover:border-gray-400 <?= in_array($account['id'], $selectedAccounts ?? []) ? 'border-indigo-500 ring-2 ring-indigo-500' : 'border-gray-300' ?>">
                                        <input type="checkbox" 
                                               name="platforms[]" 
                                               value="<?= $account['id'] ?>"
                                               <?= in_array($account['id'], $selectedAccounts ?? []) ? 'checked' : '' ?>
                                               class="hidden">
                                        <div class="flex items-center">
                                            <div class="flex-shrink-0">
                                                <i class="<?= getPlatformIcon($account['platform']) ?> <?= getPlatformColor($account['platform']) ?> text-xl"></i>
                                            </div>
                                            <div class="ml-3">
                                                <span class="block text-sm font-medium text-gray-900">
                                                    <?= htmlspecialchars($account['account_name']) ?>
                                                </span>
                                                <span class="block text-xs text-gray-500">
                                                    <?= ucfirst($account['platform']) ?>
                                                </span>
                                            </div>
                                        </div>
                                    </label>
                                <?php endforeach; ?>
                            </div>
                        </div>

                        <!-- Content Editor -->
                        <div>
                            <label for="content" class="block text-sm font-medium text-gray-700">
                                Content
                            </label>
                            <div class="mt-1">
                                <textarea id="content" 
                                          name="content" 
                                          rows="5"
                                          class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md"
                                          placeholder="What's on your mind?"><?= htmlspecialchars($post['content'] ?? '') ?></textarea>
                            </div>
                            <div class="mt-2 flex justify-between items-center">
                                <div class="text-sm text-gray-500">
                                    <span id="character-count">0</span> characters
                                </div>
                                <button type="button" 
                                        onclick="insertEmoji()"
                                        class="inline-flex items-center px-3 py-1 border border-gray-300 shadow-sm text-sm leading-4 font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                    <i class="far fa-smile mr-2"></i>
                                    Add Emoji
                                </button>
                            </div>
                        </div>

                        <!-- Media Upload -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700">
                                Media
                            </label>
                            <div class="mt-2 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-md">
                                <div class="space-y-1 text-center">
                                    <i class="fas fa-image text-gray-400 text-3xl mb-3"></i>
                                    <div class="flex text-sm text-gray-600">
                                        <label for="media" class="relative cursor-pointer bg-white rounded-md font-medium text-indigo-600 hover:text-indigo-500 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-indigo-500">
                                            <span>Upload files</span>
                                            <input id="media" 
                                                   name="media[]" 
                                                   type="file" 
                                                   multiple 
                                                   accept="image/*,video/*" 
                                                   class="sr-only">
                                        </label>
                                        <p class="pl-1">or drag and drop</p>
                                    </div>
                                    <p class="text-xs text-gray-500">
                                        PNG, JPG, GIF up to 10MB
                                    </p>
                                </div>
                            </div>
                            <!-- Media Preview -->
                            <div id="media-preview" class="mt-4 grid grid-cols-2 gap-4 sm:grid-cols-3 lg:grid-cols-4">
                                <?php if (!empty($post['media_urls'])): ?>
                                    <?php foreach ($post['media_urls'] as $index => $url): ?>
                                        <div class="relative">
                                            <?php if (strpos($url, '.mp4') !== false): ?>
                                                <video class="h-24 w-full object-cover rounded-lg">
                                                    <source src="<?= $url ?>" type="video/mp4">
                                                </video>
                                            <?php else: ?>
                                                <img src="<?= $url ?>" 
                                                     alt="" 
                                                     class="h-24 w-full object-cover rounded-lg">
                                            <?php endif; ?>
                                            <button type="button" 
                                                    onclick="removeMedia(<?= $index ?>)"
                                                    class="absolute top-0 right-0 -mt-2 -mr-2 p-1 rounded-full bg-red-600 text-white hover:bg-red-700 focus:outline-none">
                                                <span class="sr-only">Remove</span>
                                                <i class="fas fa-times"></i>
                                            </button>
                                        </div>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </div>
                        </div>

                        <!-- Scheduling -->
                        <div>
                            <div class="flex items-center">
                                <input type="checkbox" 
                                       id="schedule" 
                                       name="schedule"
                                       <?= isset($post) && $post['scheduled_time'] ? 'checked' : '' ?>
                                       class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
                                <label for="schedule" class="ml-2 block text-sm font-medium text-gray-700">
                                    Schedule for later
                                </label>
                            </div>
                            <div id="schedule-options" class="mt-3 <?= isset($post) && $post['scheduled_time'] ? '' : 'hidden' ?>">
                                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                                    <div>
                                        <label for="schedule_date" class="block text-sm font-medium text-gray-700">
                                            Date
                                        </label>
                                        <input type="date" 
                                               id="schedule_date" 
                                               name="schedule_date"
                                               value="<?= isset($post) && $post['scheduled_time'] ? date('Y-m-d', strtotime($post['scheduled_time'])) : '' ?>"
                                               min="<?= date('Y-m-d') ?>"
                                               class="mt-1 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                                    </div>
                                    <div>
                                        <label for="schedule_time" class="block text-sm font-medium text-gray-700">
                                            Time
                                        </label>
                                        <input type="time" 
                                               id="schedule_time" 
                                               name="schedule_time"
                                               value="<?= isset($post) && $post['scheduled_time'] ? date('H:i', strtotime($post['scheduled_time'])) : '' ?>"
                                               class="mt-1 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Advanced Options -->
                        <div>
                            <div class="flex items-center">
                                <input type="checkbox" 
                                       id="advanced" 
                                       name="advanced"
                                       class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
                                <label for="advanced" class="ml-2 block text-sm font-medium text-gray-700">
                                    Show advanced options
                                </label>
                            </div>
                            <div id="advanced-options" class="mt-3 hidden">
                                <div class="space-y-4">
                                    <!-- Approval Required -->
                                    <div class="flex items-center">
                                        <input type="checkbox" 
                                               id="approval_required" 
                                               name="approval_required"
                                               <?= isset($post) && $post['approval_required'] ? 'checked' : '' ?>
                                               class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
                                        <label for="approval_required" class="ml-2 block text-sm text-gray-700">
                                            Require approval before publishing
                                        </label>
                                    </div>

                                    <!-- Custom Link Settings -->
                                    <div>
                                        <label for="link_settings" class="block text-sm font-medium text-gray-700">
                                            Link Settings
                                        </label>
                                        <select id="link_settings" 
                                                name="link_settings"
                                                class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md">
                                            <option value="default">Default link preview</option>
                                            <option value="custom">Custom link preview</option>
                                            <option value="none">No link preview</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Form Actions -->
                <div class="px-4 py-3 bg-gray-50 text-right sm:px-6 space-x-3">
                    <button type="submit" 
                            name="action" 
                            value="draft"
                            class="inline-flex justify-center py-2 px-4 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        Save as draft
                    </button>
                    <button type="submit" 
                            name="action" 
                            value="publish"
                            class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        <?= isset($post) && $post['scheduled_time'] ? 'Schedule' : 'Publish now' ?>
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Platform selection
    document.querySelectorAll('input[name="platforms[]"]').forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            const label = this.closest('label');
            if (this.checked) {
                label.classList.add('border-indigo-500', 'ring-2', 'ring-indigo-500');
                label.classList.remove('border-gray-300');
            } else {
                label.classList.remove('border-indigo-500', 'ring-2', 'ring-indigo-500');
                label.classList.add('border-gray-300');
            }
        });
    });

    // Character counter
    const content = document.getElementById('content');
    const charCount = document.getElementById('character-count');
    
    function updateCharCount() {
        charCount.textContent = content.value.length;
    }
    
    content.addEventListener('input', updateCharCount);
    updateCharCount();

    // Schedule toggle
    const scheduleCheckbox = document.getElementById('schedule');
    const scheduleOptions = document.getElementById('schedule-options');
    
    scheduleCheckbox.addEventListener('change', function() {
        scheduleOptions.classList.toggle('hidden', !this.checked);
    });

    // Advanced options toggle
    const advancedCheckbox = document.getElementById('advanced');
    const advancedOptions = document.getElementById('advanced-options');
    
    advancedCheckbox.addEventListener('change', function() {
        advancedOptions.classList.toggle('hidden', !this.checked);
    });

    // Media upload preview
    const mediaInput = document.getElementById('media');
    const mediaPreview = document.getElementById('media-preview');
    
    mediaInput.addEventListener('change', function() {
        Array.from(this.files).forEach(file => {
            const reader = new FileReader();
            reader.onload = function(e) {
                const preview = document.createElement('div');
                preview.className = 'relative';
                
                if (file.type.startsWith('video/')) {
                    preview.innerHTML = `
                        <video class="h-24 w-full object-cover rounded-lg">
                            <source src="${e.target.result}" type="${file.type}">
                        </video>
                        <button type="button" 
                                onclick="this.closest('.relative').remove()"
                                class="absolute top-0 right-0 -mt-2 -mr-2 p-1 rounded-full bg-red-600 text-white hover:bg-red-700 focus:outline-none">
                            <span class="sr-only">Remove</span>
                            <i class="fas fa-times"></i>
                        </button>
                    `;
                } else {
                    preview.innerHTML = `
                        <img src="${e.target.result}" 
                             alt="" 
                             class="h-24 w-full object-cover rounded-lg">
                        <button type="button" 
                                onclick="this.closest('.relative').remove()"
                                class="absolute top-0 right-0 -mt-2 -mr-2 p-1 rounded-full bg-red-600 text-white hover:bg-red-700 focus:outline-none">
                            <span class="sr-only">Remove</span>
                            <i class="fas fa-times"></i>
                        </button>
                    `;
                }
                
                mediaPreview.appendChild(preview);
            };
            reader.readAsDataURL(file);
        });
    });

    // Form validation
    const form = document.getElementById('post-form');
    form.addEventListener('submit', function(e) {
        let valid = true;
        const errors = [];

        // Check platform selection
        const platforms = document.querySelectorAll('input[name="platforms[]"]:checked');
        if (platforms.length === 0) {
            errors.push('Please select at least one platform');
            valid = false;
        }

        // Check content
        if (content.value.trim() === '') {
            errors.push('Please enter some content');
            valid = false;
        }

        // Check scheduling
        if (scheduleCheckbox.checked) {
            const date = document.getElementById('schedule_date').value;
            const time = document.getElementById('schedule_time').value;
            
            if (!date || !time) {
                errors.push('Please set both date and time for scheduling');
                valid = false;
            }
        }

        if (!valid) {
            e.preventDefault();
            showNotification('error', errors.join('<br>'));
        }
    });
});

// Remove media
function removeMedia(index) {
    // Send AJAX request to remove media
    fetch(`/dashboard/posts/remove-media/${index}`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-Token': '<?= $this->generateCSRFToken() ?>'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Remove preview
            document.querySelector(`#media-preview div:nth-child(${index + 1})`).remove();
            showNotification('success', 'Media removed successfully');
        } else {
            throw new Error(data.message || 'Failed to remove media');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification('error', error.message);
    });
}

// Insert emoji
function insertEmoji() {
    // Create emoji picker
    const picker = new EmojiPicker({
        onSelect: emoji => {
            const content = document.getElementById('content');
            const start = content.selectionStart;
            const end = content.selectionEnd;
            content.value = content.value.substring(0, start) + emoji + content.value.substring(end);
            content.dispatchEvent(new Event('input'));
        }
    });
    
    picker.show();
}
</script>
