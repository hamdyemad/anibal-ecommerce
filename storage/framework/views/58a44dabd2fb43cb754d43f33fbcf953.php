

<?php $__env->startSection('title', isset($customer) ? __('customer::customer.edit_customer') : __('customer::customer.create_customer')); ?>

<?php $__env->startPush('styles'); ?>
<!-- Select2 CSS loaded via Vite -->
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>
    <div class="container-fluid mb-3">
        <div class="row">
            <div class="col-lg-12">
                <?php if (isset($component)) { $__componentOriginale19f62b34dfe0bfdf95075badcb45bc2 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginale19f62b34dfe0bfdf95075badcb45bc2 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.breadcrumb','data' => ['items' => [
                    ['title' => trans('dashboard.title'), 'url' => route('admin.dashboard'), 'icon' => 'uil uil-estate'],
                    ['title' => __('customer::customer.customers_management'), 'url' => route('admin.customers.index')],
                    ['title' => isset($customer) ? __('customer::customer.edit_customer') : __('customer::customer.add_customer')]
                ]]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('breadcrumb'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['items' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute([
                    ['title' => trans('dashboard.title'), 'url' => route('admin.dashboard'), 'icon' => 'uil uil-estate'],
                    ['title' => __('customer::customer.customers_management'), 'url' => route('admin.customers.index')],
                    ['title' => isset($customer) ? __('customer::customer.edit_customer') : __('customer::customer.add_customer')]
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
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title mb-0 fw-bold"><?php echo e(isset($customer) ? __('customer::customer.edit_customer') : __('customer::customer.add_customer')); ?></h4>
                    </div>
                    <div class="card-body">
                        <!-- Alert Container -->
                        <div id="alertContainer"></div>

                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($errors->any()): ?>
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <strong><?php echo e(__('customer::customer.validation_errors')); ?></strong>
                                <ul class="mb-0 mt-2">
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <li><?php echo e($error); ?></li>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                </ul>
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                        <form id="customerForm" method="POST" action="<?php echo e(isset($customer) ? route('admin.customers.update', $customer->id) : route('admin.customers.store')); ?>">
                            <?php echo csrf_field(); ?>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(isset($customer)): ?>
                                <?php echo method_field('PUT'); ?>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                            <div class="card">
                                <div class="card-header">
                                    <h3 class="fw-bold m-0">
                                        <i class="uil uil-user me-1"></i><?php echo e(__('customer::customer.basic_information')); ?>

                                    </h3>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        
                                        <div class="col-md-6">
                                            <div class="form-group mb-25">
                                                <label for="first_name" class="form-label">
                                                    <?php echo e(__('customer::customer.first_name')); ?> <span class="text-danger">*</span>
                                                </label>
                                                <input
                                                    type="text"
                                                    name="first_name"
                                                    id="first_name"
                                                    class="form-control ih-medium ip-gray radius-xs b-light px-15"
                                                    value="<?php echo e(isset($customer) ? $customer->first_name : old('first_name')); ?>"
                                                    placeholder="<?php echo e(__('customer::customer.first_name')); ?>"

                                                >
                                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['first_name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                                    <div class="text-danger"><?php echo e($message); ?></div>
                                                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                            </div>
                                        </div>

                                        
                                        <div class="col-md-6">
                                            <div class="form-group mb-25">
                                                <label for="last_name" class="form-label">
                                                    <?php echo e(__('customer::customer.last_name')); ?> <span class="text-danger">*</span>
                                                </label>
                                                <input
                                                    type="text"
                                                    name="last_name"
                                                    id="last_name"
                                                    class="form-control ih-medium ip-gray radius-xs b-light px-15"
                                                    value="<?php echo e(isset($customer) ? $customer->last_name : old('last_name')); ?>"
                                                    placeholder="<?php echo e(__('customer::customer.last_name')); ?>"

                                                >
                                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['last_name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                                    <div class="text-danger"><?php echo e($message); ?></div>
                                                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                            </div>
                                        </div>
                                        
                                        <div class="col-md-6">
                                            <div class="form-group mb-25">
                                                <label for="phone" class="form-label">
                                                    <?php echo e(__('customer::customer.phone')); ?>

                                                </label>
                                                <input
                                                    type="text"
                                                    name="phone"
                                                    id="phone"
                                                    class="form-control ih-medium ip-gray radius-xs b-light px-15"
                                                    value="<?php echo e(isset($customer) ? $customer->phone : old('phone')); ?>"
                                                    placeholder="<?php echo e(__('customer::customer.phone')); ?>"
                                                >
                                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['phone'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                                    <div class="text-danger"><?php echo e($message); ?></div>
                                                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                            </div>
                                        </div>

                                        
                                        <div class="col-md-6">
                                            <div class="form-group mb-25">
                                                <label for="gender" class="form-label">
                                                    <?php echo e(__('customer::customer.gender')); ?> <span class="text-danger">*</span>
                                                </label>
                                                <select name="gender" id="gender" class="form-control ih-medium ip-gray radius-xs b-light px-15 select2">
                                                    <option value=""><?php echo e(__('customer::customer.select_gender')); ?></option>
                                                    <option value="male" <?php echo e((isset($customer) && $customer->gender === 'male') || old('gender') === 'male' ? 'selected' : ''); ?>>
                                                        <?php echo e(__('customer::customer.male')); ?>

                                                    </option>
                                                    <option value="female" <?php echo e((isset($customer) && $customer->gender === 'female') || old('gender') === 'female' ? 'selected' : ''); ?>>
                                                        <?php echo e(__('customer::customer.female')); ?>

                                                    </option>
                                                </select>
                                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['gender'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                                    <div class="text-danger"><?php echo e($message); ?></div>
                                                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                            </div>
                                        </div>

                                        
                                        <div class="col-md-6">
                                            <div class="form-group mb-25">
                                                <label for="city_id" class="form-label">
                                                    <?php echo e(__('customer::customer.city')); ?> <span class="text-danger">*</span>
                                                </label>
                                                <select name="city_id" id="city_id" class="form-control ih-medium ip-gray radius-xs b-light px-15 select2">
                                                    <option value=""><?php echo e(__('main.choose')); ?></option>
                                                    <?php if(isset($customer) && $customer->city_id): ?>
                                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $cities ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $city): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                            <option value="<?php echo e($city->id); ?>" <?php echo e($customer->city_id === $city->id ? 'selected' : ''); ?>>
                                                                <?php echo e($city->name); ?>

                                                            </option>
                                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                                </select>
                                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['city_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                                    <div class="text-danger"><?php echo e($message); ?></div>
                                                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                            </div>
                                        </div>

                                        
                                        <div class="col-md-6">
                                            <div class="form-group mb-25">
                                                <label for="region_id" class="form-label">
                                                    <?php echo e(__('customer::customer.region')); ?> <span class="text-danger">*</span>
                                                </label>
                                                <select name="region_id" id="region_id" class="form-control ih-medium ip-gray radius-xs b-light px-15 select2">
                                                    <option value=""><?php echo e(__('main.choose')); ?></option>
                                                    <?php if(isset($customer) && $customer->region_id): ?>
                                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $regions ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $region): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                            <option value="<?php echo e($region->id); ?>" <?php echo e($customer->region_id === $region->id ? 'selected' : ''); ?>>
                                                                <?php echo e($region->name); ?>

                                                            </option>
                                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                                </select>
                                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['region_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                                    <div class="text-danger"><?php echo e($message); ?></div>
                                                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                            </div>
                                        </div>

                                        
                                        <div class="col-md-6">
                                            <div class="form-group mb-25">
                                                <label for="status" class="form-label">
                                                    <?php echo e(__('customer::customer.status')); ?>

                                                </label>
                                                <select name="status" id="status" class="form-control ih-medium ip-gray radius-xs b-light px-15 select2">
                                                    <option value="1" <?php echo e((isset($customer) && $customer->status) || old('status') == '1' ? 'selected' : ''); ?>>
                                                        <?php echo e(__('customer::customer.active')); ?>

                                                    </option>
                                                    <option value="0" <?php echo e((isset($customer) && !$customer->status) || old('status') == '0' ? 'selected' : ''); ?>>
                                                        <?php echo e(__('customer::customer.inactive')); ?>

                                                    </option>
                                                </select>
                                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['status'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                                    <div class="text-danger"><?php echo e($message); ?></div>
                                                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            
                            <div class="card mt-3">
                                <div class="card-header">
                                    <h3 class="fw-bold m-0">
                                        <i class="uil uil-lock me-1"></i><?php echo e(__('customer::customer.account_information')); ?>

                                    </h3>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        
                                        <div class="col-md-6">
                                            <div class="form-group mb-25">
                                                <label for="email" class="form-label">
                                                    <?php echo e(__('customer::customer.email')); ?> <span class="text-danger">*</span>
                                                </label>
                                                <input
                                                    type="email"
                                                    name="email"
                                                    id="email"
                                                    class="form-control ih-medium ip-gray radius-xs b-light px-15"
                                                    value="<?php echo e(isset($customer) ? $customer->email : old('email')); ?>"
                                                >
                                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                                    <div class="text-danger"><?php echo e($message); ?></div>
                                                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                            </div>
                                        </div>
                                        
                                        <div class="col-md-6">
                                            <div class="form-group mb-25">
                                                <label for="password" class="form-label">
                                                    <?php echo e(__('customer::customer.password')); ?>

                                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(!isset($customer)): ?> <span class="text-danger">*</span> <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                                </label>
                                                <input
                                                    type="password"
                                                    name="password"
                                                    id="password"
                                                    class="form-control ih-medium ip-gray radius-xs b-light px-15"
                                                    dir="ltr"
                                                >
                                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(isset($customer)): ?>
                                                    <small class="text-muted"><?php echo e(__('Leave empty to keep current password')); ?></small>
                                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                                    <div class="text-danger"><?php echo e($message); ?></div>
                                                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                            </div>
                                        </div>

                                        
                                        <div class="col-md-6">
                                            <div class="form-group mb-25">
                                                <label for="password_confirmation" class="form-label">
                                                    <?php echo e(__('customer::customer.password_confirmation')); ?>

                                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(!isset($customer)): ?> <span class="text-danger">*</span> <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                                </label>
                                                <input
                                                    type="password"
                                                    name="password_confirmation"
                                                    id="password_confirmation"
                                                    class="form-control ih-medium ip-gray radius-xs b-light px-15"
                                                    dir="ltr"
                                                >
                                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['password_confirmation'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                                    <div class="text-danger"><?php echo e($message); ?></div>
                                                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                            </div>
                                        </div>


                                    </div>
                                </div>
                            </div>
                            <div class="row mt-3">
                                
                                <div class="col-md-12">
                                    <div class="form-group d-flex gap-2 justify-content-end">
                                        <a href="<?php echo e(route('admin.customers.index')); ?>" class="btn btn-light btn-default btn-squared">
                                            <?php echo e(__('customer::customer.cancel')); ?>

                                        </a>
                                        <button type="submit" class="btn btn-primary btn-default btn-squared">
                                            <i class="uil uil-check me-1"></i>
                                            <?php echo e(isset($customer) ? __('customer::customer.update') : __('customer::customer.save')); ?>

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
    // Initialize Select2
    if (typeof $ !== 'undefined' && $.fn.select2) {
        $('.select2').select2({
            theme: 'bootstrap-5',
            width: '100%'
        });
    }

    // AJAX Form Submission
    const customerForm = document.getElementById('customerForm');
    const submitBtn = customerForm.querySelector('button[type="submit"]');
    const alertContainer = document.getElementById('alertContainer');
    let originalBtnHtml = '';

    customerForm.addEventListener('submit', function(e) {
        e.preventDefault();

        // Disable submit button and show loading
        submitBtn.disabled = true;
        originalBtnHtml = submitBtn.innerHTML;
        submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span><?php echo e(__("common.processing") ?? "Processing..."); ?>';

        // Update loading text dynamically
        const loadingText = <?php echo json_encode(isset($customer) ? trans('loading.updating') : trans('loading.creating'), 15, 512) ?>;
        const loadingSubtext = '<?php echo e(trans("loading.please_wait")); ?>';
        const overlay = document.getElementById('loadingOverlay');
        if (overlay) {
            overlay.querySelector('.loading-text').textContent = loadingText;
            overlay.querySelector('.loading-subtext').textContent = loadingSubtext;
        }

        // Show loading overlay
        if (typeof LoadingOverlay !== 'undefined') {
            LoadingOverlay.show();
        }

        // Clear previous alerts
        alertContainer.innerHTML = '';

        // Remove previous validation errors
        document.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));
        document.querySelectorAll('.invalid-feedback').forEach(el => el.remove());

        // Start progress bar animation
        const progressPromise = typeof LoadingOverlay !== 'undefined' ?
            LoadingOverlay.animateProgressBar(30, 300) :
            Promise.resolve();

        progressPromise.then(() => {
            // Prepare form data
            const formData = new FormData(customerForm);

            // Send AJAX request
            return fetch(customerForm.action, {
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
            if (typeof LoadingOverlay !== 'undefined') {
                LoadingOverlay.animateProgressBar(60, 200);
            }

            if (!response.ok) {
                return response.json().then(data => {
                    throw data;
                });
            }
            return response.json();
        })
        .then(data => {
            // Progress to 90%
            const progressPromise = typeof LoadingOverlay !== 'undefined' ?
                LoadingOverlay.animateProgressBar(90, 200) :
                Promise.resolve();
            return progressPromise.then(() => data);
        })
        .then(data => {
            // Complete progress bar
            const completePromise = typeof LoadingOverlay !== 'undefined' ?
                LoadingOverlay.animateProgressBar(100, 200) :
                Promise.resolve();

            return completePromise.then(() => {
                // Show success animation with dynamic message
                const successMessage = <?php echo json_encode(isset($customer) ? trans('customer::customer.customer_updated') : trans('customer::customer.customer_saved'), 15, 512) ?>;

                if (typeof LoadingOverlay !== 'undefined') {
                    LoadingOverlay.showSuccess(
                        successMessage,
                        '<?php echo e(trans("loading.redirecting")); ?>'
                    );
                }

                // Show success alert
                showAlert('success', data.message || successMessage);

                // Redirect after 1.5 seconds
                setTimeout(() => {
                    window.location.href = data.redirect || '<?php echo e(route("admin.customers.index")); ?>';
                }, 1500);
            });
        })
        .catch(error => {
            // Hide loading overlay and reset progress bar
            if (typeof LoadingOverlay !== 'undefined') {
                LoadingOverlay.hide();
            }

            // Handle validation errors
            if (error.errors) {
                console.log('Validation errors received:', error.errors);
                Object.keys(error.errors).forEach(key => {
                    console.log('Processing error key:', key);

                    const input = document.querySelector(`[name="${key}"]`);

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

    // Remove validation classes on input
    document.querySelectorAll('input, select, textarea').forEach(input => {
        input.addEventListener('input', function() {
            this.classList.remove('is-invalid');
            const feedback = this.parentNode.querySelector('.invalid-feedback');
            if (feedback) {
                feedback.remove();
            }
        });
    });

    // Load cities from session country on page load
    const citySelect = document.getElementById('city_id');
    const regionSelect = document.getElementById('region_id');
    const sessionCountryId = $("meta[name='current_country_id']").attr('content');
    const selectedCityId = "<?php echo e(isset($customer) ? $customer->city_id : ''); ?>";
    const selectedRegionId = "<?php echo e(isset($customer) ? $customer->region_id : ''); ?>";

    // Load cities on page load if session country exists
    if (sessionCountryId) {
        fetchCities();
    }

    function fetchCities(countryId) {
        fetch(`/api/area/countries/${sessionCountryId}/cities`, {
                method: 'GET',
                headers: {
                    'lang': "<?php echo e(app()->getLocale()); ?>"
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.data && Array.isArray(data.data)) {
                    data.data.forEach(city => {
                        const option = document.createElement('option');
                        option.value = city.id;
                        option.textContent = city.name;
                        $("#city_id").append(option);
                    });
                }
                // Reinitialize Select2
                if (typeof $ !== 'undefined' && $.fn.select2) {
                    $($("#city_id")).select2({
                        theme: 'bootstrap-5',
                        width: '100%'
                    });
                }

                // Set selected city value if editing
                if (selectedCityId) {
                    $("#city_id").val(selectedCityId);
                    // Fetch regions for the selected city
                    fetchRegions(selectedCityId);
                }
            })
            .catch(error => console.error('Error loading cities:', error));
    }

    function fetchRegions(cityId) {
        // Fetch regions for selected city
        fetch(`/api/area/cities/${cityId}/regions`,  {
            method: 'GET',
            headers: {
                'lang': "<?php echo e(app()->getLocale()); ?>"
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.data && Array.isArray(data.data)) {
                data.data.forEach(region => {
                    const option = document.createElement('option');
                    option.value = region.id;
                    option.textContent = region.name;
                    regionSelect.appendChild(option);
                });
            }
            // Reinitialize Select2
            if (typeof $ !== 'undefined' && $.fn.select2) {
                $(regionSelect).select2({
                    theme: 'bootstrap-5',
                    width: '100%'
                });
            }

            // Set selected region value if editing
            if (selectedRegionId) {
                $(regionSelect).val(selectedRegionId).trigger('change');
            }
        })
        .catch(error => console.error('Error loading regions:', error));
    }
    // Handle city change to load regions
    $("#city_id").on('change', function() {
        const cityId = this.value;
        console.log(cityId)

        // Clear existing regions
        regionSelect.innerHTML = '<option value=""><?php echo e(__("main.choose")); ?></option>';

        if (cityId) {
            fetchRegions(cityId)
        }
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

<?php echo $__env->make('layout.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\eramo-multi-vendor\Modules/Customer\resources/views/customer/form.blade.php ENDPATH**/ ?>