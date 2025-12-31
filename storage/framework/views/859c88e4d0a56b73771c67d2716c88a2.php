

<?php $__env->startSection('title', isset($country) ? __('areasettings::country.edit_country') : __('areasettings::country.create_country')); ?>
<?php $__env->startSection('content'); ?>
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12">
                <?php if (isset($component)) { $__componentOriginale19f62b34dfe0bfdf95075badcb45bc2 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginale19f62b34dfe0bfdf95075badcb45bc2 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.breadcrumb','data' => ['items' => [
                    ['title' => trans('dashboard.title'), 'url' => route('admin.dashboard'), 'icon' => 'uil uil-estate'],
                    ['title' => __('areasettings::country.countries_management'), 'url' => route('admin.area-settings.countries.index')],
                    ['title' => isset($country) ? __('areasettings::country.edit_country') : __('areasettings::country.create_country')]
                ]]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('breadcrumb'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['items' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute([
                    ['title' => trans('dashboard.title'), 'url' => route('admin.dashboard'), 'icon' => 'uil uil-estate'],
                    ['title' => __('areasettings::country.countries_management'), 'url' => route('admin.area-settings.countries.index')],
                    ['title' => isset($country) ? __('areasettings::country.edit_country') : __('areasettings::country.create_country')]
                ])]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginale19f62b34dfe0bfdf95075badcb45bc2)): ?>
<?php $attributes = $__attributesOriginale19f62b34dfe0bfdf95075badcb45bc2; ?>
<?php unset($__attributesOriginale19f62b34dfe0bfdf95075badcb45bc2); ?>
<?php endif; ?>
<?php if (isset($__componentOriginale19f62b34dfe0bfdf95075badcb45bc2)): ?>
<?php $component = $__componentOriginale19f62b34dfe0bfdf95075badcb45bc2; ?>
<?php unset($__componentOriginale19f62b34dfe0bfdf95075badcb45bc2); ?>
<?php endif; ?>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-12">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white border-bottom py-20">
                        <h5 class="mb-0 fw-500 fw-bold">
                            <?php echo e(isset($country) ? __('areasettings::country.edit_country') : __('areasettings::country.create_country')); ?>

                        </h5>
                    </div>
                    <div class="card-body">
                        <!-- Alert Container -->
                        <div id="alertContainer"></div>

                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($errors->any()): ?>
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <strong><?php echo e(__('areasettings::country.validation_errors')); ?></strong>
                                <ul class="mb-0 mt-2">
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <li><?php echo e($error); ?></li>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                </ul>
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                        <!-- Alert Container -->
                        <div id="alertContainer"></div>

                        <form id="countryForm" method="POST" action="<?php echo e(isset($country) ? route('admin.area-settings.countries.update', $country->id) : route('admin.area-settings.countries.store')); ?>">
                            <?php echo csrf_field(); ?>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(isset($country)): ?>
                                <?php echo method_field('PUT'); ?>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            <div class="row">
                                <!-- Translation Fields -->
                                <?php if (isset($component)) { $__componentOriginal36fb2a0d5a2d77000f95bef7ddc5a9bb = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal36fb2a0d5a2d77000f95bef7ddc5a9bb = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.multilingual-input','data' => ['name' => 'name','oldPrefix' => 'translations','label' => 'Name','labelAr' => 'الاسم','placeholder' => 'name','placeholderAr' => 'الاسم','type' => 'text','languages' => $languages,'model' => $country ?? null]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('multilingual-input'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'name','oldPrefix' => 'translations','label' => 'Name','labelAr' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute('الاسم'),'placeholder' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute('name'),'placeholderAr' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute('الاسم'),'type' => 'text','languages' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($languages),'model' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($country ?? null)]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal36fb2a0d5a2d77000f95bef7ddc5a9bb)): ?>
<?php $attributes = $__attributesOriginal36fb2a0d5a2d77000f95bef7ddc5a9bb; ?>
<?php unset($__attributesOriginal36fb2a0d5a2d77000f95bef7ddc5a9bb); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal36fb2a0d5a2d77000f95bef7ddc5a9bb)): ?>
<?php $component = $__componentOriginal36fb2a0d5a2d77000f95bef7ddc5a9bb; ?>
<?php unset($__componentOriginal36fb2a0d5a2d77000f95bef7ddc5a9bb); ?>
<?php endif; ?>
                                <div class="col-md-6">
                                    <div class="form-group mb-3">
                                        <label for="code" class="form-label"><?php echo e(__('areasettings::country.country_code')); ?> <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control ih-medium ip-gray radius-xs b-light px-15" id="code" name="code" value="<?php echo e(old('code', isset($country) ? $country->code : '')); ?>" maxlength="3" placeholder="e.g., USA, SAU, EGY">
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['code'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                            <div class="text-danger small mt-1"><?php echo e($message); ?></div>
                                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group mb-3">
                                        <label for="phone_code" class="form-label"><?php echo e(__('areasettings::country.phone_code')); ?> <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control ih-medium ip-gray radius-xs b-light px-15" id="phone_code" name="phone_code" value="<?php echo e(old('phone_code', isset($country) ? $country->phone_code : '')); ?>" maxlength="10" placeholder="e.g., +1, +966, +20">
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['phone_code'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                            <div class="text-danger small mt-1"><?php echo e($message); ?></div>
                                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group mb-3">
                                        <label for="currency_id" class="form-label"><?php echo e(__('areasettings::country.currency')); ?> <span class="text-danger">*</span></label>
                                        <select class="form-control ih-medium ip-gray radius-xs b-light px-15" id="currency_id" name="currency_id">
                                            <option value=""><?php echo e(__('areasettings::country.select_currency')); ?></option>
                                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $currencies; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $currency): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <option value="<?php echo e($currency->id); ?>"
                                                    <?php echo e(old('currency_id', isset($country) ? $country->currency_id : '') == $currency->id ? 'selected' : ''); ?>>
                                                    <?php echo e($currency->getTranslation('name', app()->getLocale())); ?> (<?php echo e($currency->code); ?>)
                                                </option>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                        </select>
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['currency_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                            <div class="text-danger small mt-1"><?php echo e($message); ?></div>
                                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                    </div>
                                </div>

                                
                                <div class="col-md-6">
                                    <div class="form-group mb-25">
                                        <label class="il-gray fs-14 fw-500 mb-10 d-block">
                                            <?php echo e(__('areasettings::country.active')); ?>

                                        </label>
                                        <div class="dm-switch-wrap d-flex align-items-center">
                                            <div class="form-check form-switch form-switch-primary form-switch-md">
                                                <input type="hidden" name="active" value="0">
                                                <input type="checkbox"
                                                       class="form-check-input"
                                                       id="active"
                                                       name="active"
                                                       value="1"
                                                       <?php echo e(old('active', isset($country) ? $country->active : 1) == 1 ? 'checked' : ''); ?>>
                                                <label class="form-check-label" for="active"></label>
                                            </div>
                                        </div>
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['active'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                            <div class="text-danger fs-12 mt-1"><?php echo e($message); ?></div>
                                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                    </div>
                                </div>

                                
                                <div class="col-md-6">
                                    <div class="form-group mb-25">
                                        <label class="il-gray fs-14 fw-500 mb-10 d-block">
                                            <?php echo e(__('areasettings::country.default')); ?>

                                        </label>
                                        <div class="dm-switch-wrap d-flex align-items-center">
                                            <div class="form-check form-switch form-switch-success form-switch-md">
                                                <input type="hidden" name="default" value="0">
                                                <input type="checkbox"
                                                       class="form-check-input"
                                                       id="default"
                                                       name="default"
                                                       value="1"
                                                       <?php echo e(old('default', isset($country) ? $country->default : 0) == 1 ? 'checked' : ''); ?>>
                                                <label class="form-check-label" for="default"></label>
                                            </div>
                                            <span class="text-muted fs-12 ms-2"><?php echo e(__('areasettings::country.default_country_info')); ?></span>
                                        </div>
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['default'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                            <div class="text-danger fs-12 mt-1"><?php echo e($message); ?></div>
                                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                    </div>
                                </div>
                                <div class="col-md-6 mb-25">
                                    <?php if (isset($component)) { $__componentOriginaldbebdfa49a0907927fe266159631a348 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginaldbebdfa49a0907927fe266159631a348 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.image-upload','data' => ['id' => 'country_image','name' => 'image','label' => trans('areasettings::country.image'),'placeholder' => trans('areasettings::country.click_to_upload_image'),'recommendedSize' => trans('areasettings::country.recommended_size'),'existingImage' => isset($country) && $country->image ? $country->image->path : null,'aspectRatio' => 'square']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('image-upload'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['id' => 'country_image','name' => 'image','label' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(trans('areasettings::country.image')),'placeholder' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(trans('areasettings::country.click_to_upload_image')),'recommendedSize' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(trans('areasettings::country.recommended_size')),'existingImage' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(isset($country) && $country->image ? $country->image->path : null),'aspectRatio' => 'square']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginaldbebdfa49a0907927fe266159631a348)): ?>
<?php $attributes = $__attributesOriginaldbebdfa49a0907927fe266159631a348; ?>
<?php unset($__attributesOriginaldbebdfa49a0907927fe266159631a348); ?>
<?php endif; ?>
<?php if (isset($__componentOriginaldbebdfa49a0907927fe266159631a348)): ?>
<?php $component = $__componentOriginaldbebdfa49a0907927fe266159631a348; ?>
<?php unset($__componentOriginaldbebdfa49a0907927fe266159631a348); ?>
<?php endif; ?>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-group mt-4 d-flex align-items-center justify-content-end">
                                        <a href="<?php echo e(route('admin.area-settings.countries.index')); ?>" class="btn btn-light btn-default btn-squared text-capitalize">
                                            <i class="uil uil-arrow-left"></i> <?php echo e(__('areasettings::country.back_to_list')); ?>

                                        </a>
                                        <button type="submit" class="btn btn-primary btn-default btn-squared text-capitalize ms-2">
                                            <i class="uil uil-check"></i> <?php echo e(isset($country) ? __('areasettings::country.update_country') : __('areasettings::country.create_country')); ?>

                                        </button>
                                    </div>
                                </div>
                            </div>

                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // AJAX Form Submission
    const countryForm = document.getElementById('countryForm');
    const submitBtn = countryForm.querySelector('button[type="submit"]');
    const alertContainer = document.getElementById('alertContainer');
    let originalBtnHtml = ''; // Store outside to access in catch block

    countryForm.addEventListener('submit', function(e) {
        e.preventDefault();

        // Disable submit button and show loading
        submitBtn.disabled = true;
        originalBtnHtml = submitBtn.innerHTML; // Store original HTML
        submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span><?php echo e(__("common.processing") ?? "Processing..."); ?>';

        // Update loading text dynamically
        const loadingText = <?php echo json_encode(isset($country) ? trans('loading.updating') : trans('loading.creating'), 15, 512) ?>;
        const loadingSubtext = '<?php echo e(trans("loading.please_wait")); ?>';
        const overlay = document.getElementById('loadingOverlay');
        if (overlay) {
            overlay.querySelector('.loading-text').textContent = loadingText;
            overlay.querySelector('.loading-subtext').textContent = loadingSubtext;
        }

        // Show loading overlay
        LoadingOverlay.show();

        // Clear previous alerts
        alertContainer.innerHTML = '';

        // Remove previous validation errors
        document.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));
        document.querySelectorAll('.invalid-feedback').forEach(el => el.remove());

        // Start progress bar animation
        LoadingOverlay.animateProgressBar(30, 300).then(() => {
            // Prepare form data
            const formData = new FormData(countryForm);

            // Send AJAX request
            return fetch(countryForm.action, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json',
                }
            });
        })
        .then(response => {
            // Progress to 60%
            LoadingOverlay.animateProgressBar(60, 200);

            if (!response.ok) {
                return response.json().then(data => {
                    throw data;
                });
            }
            return response.json();
        })
        .then(data => {
            // Progress to 90%
            return LoadingOverlay.animateProgressBar(90, 200).then(() => data);
        })
        .then(data => {
            // Complete progress bar
            return LoadingOverlay.animateProgressBar(100, 200).then(() => {
                // Show success animation with dynamic message
                const successMessage = <?php echo json_encode(isset($country) ? trans('loading.updated_successfully') : trans('loading.created_successfully'), 15, 512) ?>;
                LoadingOverlay.showSuccess(
                    successMessage,
                    '<?php echo e(trans("loading.redirecting")); ?>'
                );

                // Show success alert
                showAlert('success', data.message || successMessage);

                // Redirect after 1.5 seconds
                setTimeout(() => {
                    window.location.href = data.redirect || '<?php echo e(route("admin.area-settings.countries.index")); ?>';
                }, 1500);
            });
        })
        .catch(error => {
            // Hide loading overlay and reset progress bar
            LoadingOverlay.hide();

            // Handle validation errors
            if (error.errors) {
                console.log('Validation errors received:', error.errors);
                Object.keys(error.errors).forEach(key => {
                    console.log('Processing error key:', key);

                    let input = null;
                    const possibleSelectors = [];

                    // Add original key
                    possibleSelectors.push(`[name="${key}"]`);

                    // If key contains dots (Laravel format: translations.0.name)
                    if (key.includes('.')) {
                        // Convert to bracket notation: translations[0][name]
                        const bracketKey = key.replace(/^([^.]+)\.(\d+)\.([^.]+)$/, '$1[$2][$3]');
                        possibleSelectors.push(`[name="${bracketKey}"]`);

                        // Also try with escaped brackets
                        const escapedBracketKey = bracketKey.replace(/\[/g, '\\\\[').replace(/\]/g, '\\\\]');
                        possibleSelectors.push(`[name="${escapedBracketKey}"]`);
                    }

                    // If key contains brackets, try escaping them
                    if (key.includes('[')) {
                        const escapedKey = key.replace(/\[/g, '\\\\[').replace(/\]/g, '\\\\]');
                        possibleSelectors.push(`[name="${escapedKey}"]`);
                    }

                    // Try each selector until we find the input
                    for (const selector of possibleSelectors) {
                        console.log('Trying selector:', selector);
                        try {
                            input = document.querySelector(selector);
                            if (input) {
                                console.log('Found input with selector:', selector);
                                break;
                            }
                        } catch (e) {
                            console.log('Invalid selector:', selector, e.message);
                        }
                    }

                    // If still not found, try to find by ID pattern (for translation fields)
                    if (!input && key.match(/^translations\.(\d+)\.name$/)) {
                        const languageId = key.match(/^translations\.(\d+)\.name$/)[1];
                        const idSelector = `#name_${languageId}`;
                        console.log('Trying ID selector:', idSelector);
                        input = document.querySelector(idSelector);
                        if (input) {
                            console.log('Found input with ID selector:', idSelector);
                        }
                    }

                    if (input) {
                        console.log('Found input for key:', key);
                        input.classList.add('is-invalid');

                        // Remove any existing feedback
                        const existingFeedback = input.parentNode.querySelector('.invalid-feedback');
                        if (existingFeedback) {
                            existingFeedback.remove();
                        }

                        const feedback = document.createElement('div');
                        feedback.className = 'invalid-feedback';
                        feedback.textContent = error.errors[key][0];
                        input.parentNode.appendChild(feedback);
                    } else {
                        console.log('Could not find input for error key:', key);
                        console.log('Available inputs:', Array.from(document.querySelectorAll('input, select, textarea')).map(el => el.name));
                    }
                });
                showAlert('danger', error.message || '<?php echo e(__("Please check the form for errors")); ?>');
            } else {
                showAlert('danger', error.message || '<?php echo e(__("An error occurred")); ?>');
            }

            // Re-enable submit button
            submitBtn.disabled = false;
            submitBtn.innerHTML = originalBtnHtml;
        });
    });

    // Show alert function
    function showAlert(type, message) {
        const alert = document.createElement('div');
        alert.className = `alert alert-${type} alert-dismissible fade show mb-20`;
        alert.innerHTML = `
            <i class="uil uil-${type === 'success' ? 'check-circle' : 'exclamation-triangle'}"></i> ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        `;
        document.getElementById('alertContainer').appendChild(alert);

        // Scroll to top to show alert
        window.scrollTo({ top: 0, behavior: 'smooth' });
    }

    // Auto-format phone code to start with +
    $('#phone_code').on('input', function() {
        let value = $(this).val();
        if (value && !value.startsWith('+')) {
            $(this).val('+' + value);
        }
    });

    // Auto-format country code to uppercase
    $('#code').on('input', function() {
        $(this).val($(this).val().toUpperCase());
    });

});
</script>
<?php $__env->stopPush(); ?>


<?php $__env->startPush('after-body'); ?>
    <?php if (isset($component)) { $__componentOriginal115e82920da0ed7c897ee494af74b9d8 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal115e82920da0ed7c897ee494af74b9d8 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.loading-overlay','data' => ['loadingText' => trans('loading.processing'),'loadingSubtext' => trans('loading.please_wait')]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('loading-overlay'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['loadingText' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(trans('loading.processing')),'loadingSubtext' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(trans('loading.please_wait'))]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal115e82920da0ed7c897ee494af74b9d8)): ?>
<?php $attributes = $__attributesOriginal115e82920da0ed7c897ee494af74b9d8; ?>
<?php unset($__attributesOriginal115e82920da0ed7c897ee494af74b9d8); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal115e82920da0ed7c897ee494af74b9d8)): ?>
<?php $component = $__componentOriginal115e82920da0ed7c897ee494af74b9d8; ?>
<?php unset($__componentOriginal115e82920da0ed7c897ee494af74b9d8); ?>
<?php endif; ?>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layout.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\eramo-multi-vendor\Modules/AreaSettings\resources/views/country/form.blade.php ENDPATH**/ ?>