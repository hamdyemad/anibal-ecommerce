

<?php $__env->startSection('title', trans('admin.my_profile')); ?>

<?php $__env->startPush('styles'); ?>
    <style>
        .fs-15.color-dark {
            line-height: 1.6;
        }

        .fs-15.color-dark strong {
            color: #2c3e50;
            font-weight: 600;
        }

        .card-holder .card-header h3 {
            font-size: 18px;
            font-weight: 600;
            color: #2c3e50;
        }

        .card-header-gradient {
            background: linear-gradient(90deg, #1e40af 0%, #db2777 100%) !important;
            color: white !important;
            border-radius: 10px 10px 0 0 !important;
        }

        .card-header-gradient h3,
        .card-header-gradient h5 {
            color: white !important;
        }

        .box-items-translations {
            padding: 15px;
            border: 1px solid #f1f2f6;
            border-radius: 10px;
            margin-bottom: 20px;
        }

        .field-icon {
            position: absolute;
            right: 15px;
            top: 50%;
            transform: translateY(-50%);
            cursor: pointer;
            color: #adb5bd;
            z-index: 10;
        }

        [dir="rtl"] .field-icon {
            right: auto;
            left: 15px;
        }
    </style>
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12">
                <?php if (isset($component)) { $__componentOriginale19f62b34dfe0bfdf95075badcb45bc2 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginale19f62b34dfe0bfdf95075badcb45bc2 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.breadcrumb','data' => ['items' => [
                    [
                        'title' => trans('dashboard.title'),
                        'url' => route('admin.dashboard'),
                        'icon' => 'uil uil-estate',
                    ],
                    ['title' => trans('admin.my_profile')],
                ]]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('breadcrumb'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['items' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute([
                    [
                        'title' => trans('dashboard.title'),
                        'url' => route('admin.dashboard'),
                        'icon' => 'uil uil-estate',
                    ],
                    ['title' => trans('admin.my_profile')],
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
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-white border-bottom py-20">
                        <h5 class="mb-0 fw-500"><?php echo e(trans('admin.my_profile')); ?></h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-lg-12">
                                <form action="<?php echo e(route('admin.profile.update')); ?>" method="POST" id="profileForm"
                                    enctype="multipart/form-data">
                                    <?php echo csrf_field(); ?>
                                    <?php echo method_field('PUT'); ?>

                                    <div class="card card-holder overflow-hidden border-0 shadow-sm mb-4">
                                        <div class="card-header card-header-gradient">
                                            <h3 class="mb-0">
                                                <i class="uil uil-user me-1"></i><?php echo e(trans('admin.profile_information')); ?>

                                            </h3>
                                        </div>
                                        <div class="card-body">
                                            <div class="row">
                                                
                                                <div class="col-md-12 mb-30">
                                                    <div class="d-flex justify-content-center">
                                                        <div style="width: 200px;">
                                                            <?php if (isset($component)) { $__componentOriginaldbebdfa49a0907927fe266159631a348 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginaldbebdfa49a0907927fe266159631a348 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.image-upload','data' => ['id' => 'profile_image_input','name' => 'image','label' => '','existingImage' => $user->image,'placeholder' => trans('admin.click_to_upload_image')]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('image-upload'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['id' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute('profile_image_input'),'name' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute('image'),'label' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(''),'existingImage' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($user->image),'placeholder' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(trans('admin.click_to_upload_image'))]); ?>
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
                                                    </div>
                                                </div>

                                                <?php
                                                    $userModel = new class ($user) {
                                                        private $user;
                                                        public function __construct($user)
                                                        {
                                                            $this->user = $user;
                                                        }
                                                        public function getTranslation($key, $langCode)
                                                        {
                                                            $language = \App\Models\Language::where(
                                                                'code',
                                                                $langCode,
                                                            )->first();
                                                            return $this->user->translations
                                                                ->where('lang_id', $language?->id)
                                                                ->where('lang_key', $key)
                                                                ->first()->lang_value ?? '';
                                                        }
                                                    };
                                                ?>

                                                <?php if (isset($component)) { $__componentOriginal36fb2a0d5a2d77000f95bef7ddc5a9bb = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal36fb2a0d5a2d77000f95bef7ddc5a9bb = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.multilingual-input','data' => ['name' => 'name','label' => trans('admin.name'),'labelAr' => trans('admin.name', [], 'ar'),'type' => 'text','placeholder' => trans('admin.name'),'placeholderAr' => trans('admin.name', [], 'ar'),'required' => true,'languages' => $languages ?? \App\Models\Language::all(),'model' => $userModel,'oldPrefix' => 'translations','cols' => 6]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('multilingual-input'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'name','label' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(trans('admin.name')),'labelAr' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(trans('admin.name', [], 'ar')),'type' => 'text','placeholder' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(trans('admin.name')),'placeholderAr' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(trans('admin.name', [], 'ar')),'required' => true,'languages' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($languages ?? \App\Models\Language::all()),'model' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($userModel),'oldPrefix' => 'translations','cols' => 6]); ?>
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

                                                <div class="col-md-6 mb-25">
                                                    <label for="email"
                                                        class="form-label fw-500"><?php echo e(trans('admin.email')); ?></label>
                                                    <div class="position-relative">
                                                        <input type="email"
                                                            class="form-control <?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                                            id="email" name="email"
                                                            value="<?php echo e(old('email', $user->email)); ?>"
                                                            placeholder="<?php echo e(trans('admin.email')); ?>" required>
                                                    </div>
                                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                                        <div class="invalid-feedback d-block"><?php echo e($message); ?></div>
                                                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                                </div>

                                                <div class="col-md-3 mb-25 text-muted">
                                                    <label
                                                        class="form-label fw-500 mb-1 d-block"><?php echo e(trans('admin.role')); ?></label>
                                                    <div class="d-flex flex-wrap gap-1">
                                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $user->roles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $role): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                            <span
                                                                class="d-inline-flex align-items-center px-2 py-1 rounded-pill bg- border text-secondary fs-12">
                                                                <i class="uil uil-shield-check me-1 text-primary"></i>
                                                                <?php echo e($role->getNameAttribute()); ?>

                                                            </span>
                                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                                    </div>
                                                </div>

                                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($user->vendor_id && $user->vendorById): ?>
                                                    <div class="col-md-3 mb-25 text-muted">
                                                        <label
                                                            class="form-label fw-500 mb-1 d-block"><?php echo e(trans('admin.vendor')); ?></label>
                                                        <p class="fs-14 color-dark mb-0">
                                                            <i class="uil uil-building me-1 text-primary"></i>
                                                            <?php echo e($user->vendorById->name); ?>

                                                        </p>
                                                    </div>
                                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                            </div>

                                            <div class="d-flex justify-content-end mt-4">
                                                <button type="submit"
                                                    class="btn btn-primary btn-default btn-squared shadow-sm">
                                                    <i class="uil uil-check me-1"></i><?php echo e(trans('admin.update_user')); ?>

                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </form>

                                
                                <form action="<?php echo e(route('admin.profile.password.update')); ?>" method="POST">
                                    <?php echo csrf_field(); ?>
                                    <?php echo method_field('PUT'); ?>
                                    <div class="card card-holder overflow-hidden border-0 shadow-sm">
                                        <div class="card-header card-header-gradient">
                                            <h3 class="mb-0">
                                                <i class="uil uil-lock me-1"></i><?php echo e(trans('admin.change_password')); ?>

                                            </h3>
                                        </div>
                                        <div class="card-body">
                                            <div class="row">
                                                <div class="col-md-12 mb-25">
                                                    <label for="current_password"
                                                        class="form-label fw-500"><?php echo e(trans('admin.current_password')); ?></label>
                                                    <div class="position-relative">
                                                        <input type="password"
                                                            class="form-control <?php $__errorArgs = ['current_password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                                            id="current_password" name="current_password"
                                                            placeholder="<?php echo e(trans('admin.current_password')); ?>" required>
                                                        <span toggle="#current_password"
                                                            class="uil uil-eye-slash field-icon toggle-password"></span>
                                                    </div>
                                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['current_password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                                        <div class="invalid-feedback d-block"><?php echo e($message); ?></div>
                                                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                                </div>

                                                <div class="col-md-6 mb-25">
                                                    <label for="password"
                                                        class="form-label fw-500"><?php echo e(trans('admin.new_password')); ?></label>
                                                    <div class="position-relative">
                                                        <input type="password"
                                                            class="form-control <?php $__errorArgs = ['password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                                            id="password" name="password"
                                                            placeholder="<?php echo e(trans('admin.new_password')); ?>" required>
                                                        <span toggle="#password"
                                                            class="uil uil-eye-slash field-icon toggle-password"></span>
                                                    </div>
                                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                                        <div class="invalid-feedback d-block"><?php echo e($message); ?></div>
                                                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                                    <small class="text-muted"><?php echo e(trans('admin.password_min_8')); ?></small>
                                                </div>

                                                <div class="col-md-6 mb-25">
                                                    <label for="password_confirmation"
                                                        class="form-label fw-500"><?php echo e(trans('admin.confirm_password')); ?></label>
                                                    <div class="position-relative">
                                                        <input type="password" class="form-control"
                                                            id="password_confirmation" name="password_confirmation"
                                                            placeholder="<?php echo e(trans('admin.confirm_password')); ?>" required>
                                                        <span toggle="#password_confirmation"
                                                            class="uil uil-eye-slash field-icon toggle-password"></span>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="d-flex justify-content-end mt-4">
                                                <button type="submit"
                                                    class="btn btn-warning btn-default btn-squared shadow-sm">
                                                    <i
                                                        class="uil uil-key-skeleton me-1"></i><?php echo e(trans('admin.change_password')); ?>

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
        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
    <script>
        $(document).on('click', '.toggle-password', function() {
            $(this).toggleClass('uil-eye uil-eye-slash');
            let input = $($(this).attr('toggle'));
            if (input.attr('type') == 'password') {
                input.attr('type', 'text');
            } else {
                input.attr('type', 'password');
            }
        });
    </script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layout.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\eramo-multi-vendor\resources\views/profile/index.blade.php ENDPATH**/ ?>