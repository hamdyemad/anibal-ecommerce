
<?php $__env->startSection('title', isset($shipping) ? trans('shipping.edit_shipping') : trans('shipping.create_shipping')); ?>
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
                    ['title' => trans('shipping.shipping_management'), 'url' => route('admin.shippings.index')],
                    ['title' => isset($shipping) ? trans('shipping.edit_shipping') : trans('shipping.create_shipping')],
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
                    ['title' => trans('shipping.shipping_management'), 'url' => route('admin.shippings.index')],
                    ['title' => isset($shipping) ? trans('shipping.edit_shipping') : trans('shipping.create_shipping')],
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
                        <h5 class="mb-0 fw-500">
                            <?php echo e(isset($shipping) ? trans('shipping.edit_shipping') : trans('shipping.create_shipping')); ?>

                        </h5>
                    </div>
                    <div class="card-body">
                        <!-- Alert Container -->
                        <div id="alertContainer" class="mb-2"></div>

                        <form id="shippingForm"
                            action="<?php echo e(isset($shipping) ? route('admin.shippings.update', $shipping->id) : route('admin.shippings.store')); ?>"
                            method="POST">
                            <?php echo csrf_field(); ?>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(isset($shipping)): ?>
                                <?php echo method_field('PUT'); ?>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                            
                            <?php if (isset($component)) { $__componentOriginal36fb2a0d5a2d77000f95bef7ddc5a9bb = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal36fb2a0d5a2d77000f95bef7ddc5a9bb = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.multilingual-input','data' => ['name' => 'name','label' => 'Name','labelAr' => 'الاسم','placeholder' => 'Enter shipping name','placeholderAr' => 'أدخل اسم الشحن','required' => true,'languages' => $languages,'model' => $shipping ?? null]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('multilingual-input'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'name','label' => 'Name','labelAr' => 'الاسم','placeholder' => 'Enter shipping name','placeholderAr' => 'أدخل اسم الشحن','required' => true,'languages' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($languages),'model' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($shipping ?? null)]); ?>
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

                            <div class="row">
                                
                                <div class="col-md-6 mb-25">
                                    <div class="form-group">
                                        <label class="il-gray fs-14 fw-500 mb-10 d-block">
                                            <?php echo e(trans('shipping.cost')); ?>

                                            <span class="text-danger">*</span>
                                        </label>
                                        <input type="number" step="0.01"
                                            class="form-control ih-medium ip-gray radius-xs b-light px-15 <?php $__errorArgs = ['cost'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                            id="cost" name="cost" value="<?php echo e(old('cost', $shipping->cost ?? '')); ?>"
                                            placeholder="0.00" required>
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['cost'];
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
                                </div>

                                
                                <div class="col-md-6 mb-25">
                                    <div class="form-group">
                                        <label class="il-gray fs-14 fw-500 mb-10 d-block">
                                            <?php echo e(trans('shipping.status')); ?>

                                        </label>
                                        <div class="dm-switch-wrap d-flex align-items-center">
                                            <div class="form-check form-switch form-switch-primary form-switch-md">
                                                <input type="hidden" name="active" value="0">
                                                <input type="checkbox" class="form-check-input" id="active"
                                                    name="active" value="1"
                                                    <?php echo e(old('active', $shipping->active ?? 1) == 1 ? 'checked' : ''); ?>>
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
                            </div>

                            <div class="row">
                                
                                <div class="col-md-6 mb-25">
                                    <div class="form-group">
                                        <label class="il-gray fs-14 fw-500 mb-10 d-block">
                                            <?php echo e(trans('shipping.cities')); ?>

                                            <span class="text-danger">*</span>
                                        </label>
                                        <div class="tag-input-container" id="city-tags-container">
                                            <div class="tags-display" id="city-tags-display">
                                                <?php
                                                    $selectedCityIds = old(
                                                        'city_ids',
                                                        isset($shipping)
                                                            ? $shipping->cities->pluck('id')->toArray()
                                                            : [],
                                                    );
                                                ?>
                                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $cities; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $city): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(in_array($city->id, $selectedCityIds)): ?>
                                                        <span class="tag-badge" data-id="<?php echo e($city->id); ?>">
                                                            <?php echo e($city->name); ?>

                                                            <span class="tag-remove"
                                                                onclick="removeTag('city', <?php echo e($city->id); ?>)">&times;</span>
                                                            <input type="hidden" name="city_ids[]"
                                                                value="<?php echo e($city->id); ?>">
                                                        </span>
                                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                            </div>
                                            <input type="text" class="tag-input" id="city-input"
                                                placeholder="<?php echo e(trans('shipping.select_cities')); ?>" autocomplete="off">
                                            <div class="tag-dropdown" id="city-dropdown" style="display: none;">
                                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $cities; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $city): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                    <div class="tag-option" data-id="<?php echo e($city->id); ?>"
                                                        data-name="<?php echo e(addslashes($city->name)); ?>"
                                                        onclick="addTag('city', <?php echo e($city->id); ?>, '<?php echo e(addslashes($city->name)); ?>')">
                                                        <?php echo e($city->name); ?>

                                                    </div>
                                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                            </div>
                                        </div>
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['city_ids'];
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
                                </div>

                                
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($shippingSettings?->shipping_allow_departments && $departments->count() > 0): ?>
                                <div class="col-md-6 mb-25">
                                    <div class="form-group">
                                        <label class="il-gray fs-14 fw-500 mb-10 d-block">
                                            <?php echo e(trans('shipping.departments')); ?>

                                            <span class="text-danger">*</span>
                                        </label>
                                        <div class="tag-input-container" id="department-tags-container">
                                            <div class="tags-display" id="department-tags-display">
                                                <?php
                                                    $selectedDepartmentIds = old(
                                                        'department_ids',
                                                        isset($shipping) && $shipping->departments
                                                            ? $shipping->departments->pluck('id')->toArray()
                                                            : [],
                                                    );
                                                ?>
                                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $departments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $department): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(in_array($department->id, $selectedDepartmentIds)): ?>
                                                        <span class="tag-badge" data-id="<?php echo e($department->id); ?>">
                                                            <?php echo e($department->name); ?>

                                                            <span class="tag-remove"
                                                                onclick="removeTag('department', <?php echo e($department->id); ?>)">&times;</span>
                                                            <input type="hidden" name="department_ids[]"
                                                                value="<?php echo e($department->id); ?>">
                                                        </span>
                                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                            </div>
                                            <input type="text" class="tag-input" id="department-input"
                                                placeholder="<?php echo e(trans('shipping.select_departments')); ?>" autocomplete="off">
                                            <div class="tag-dropdown" id="department-dropdown" style="display: none;">
                                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $departments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $department): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                    <div class="tag-option" data-id="<?php echo e($department->id); ?>"
                                                        data-name="<?php echo e(addslashes($department->name)); ?>"
                                                        onclick="addTag('department', <?php echo e($department->id); ?>, '<?php echo e(addslashes($department->name)); ?>')">
                                                        <?php echo e($department->name); ?>

                                                    </div>
                                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                            </div>
                                        </div>
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['department_ids'];
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
                                </div>
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                                
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($shippingSettings?->shipping_allow_categories && $categories->count() > 0): ?>
                                <div class="col-md-6 mb-25">
                                    <div class="form-group">
                                        <label class="il-gray fs-14 fw-500 mb-10 d-block">
                                            <?php echo e(trans('shipping.categories')); ?>

                                            <span class="text-danger">*</span>
                                        </label>
                                        <div class="tag-input-container" id="category-tags-container">
                                            <div class="tags-display" id="category-tags-display">
                                                <?php
                                                    $selectedCategoryIds = old(
                                                        'category_ids',
                                                        isset($shipping)
                                                            ? $shipping->categories->pluck('id')->toArray()
                                                            : [],
                                                    );
                                                ?>
                                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $category): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(in_array($category->id, $selectedCategoryIds)): ?>
                                                        <span class="tag-badge" data-id="<?php echo e($category->id); ?>">
                                                            <?php echo e($category->name); ?>

                                                            <span class="tag-remove"
                                                                onclick="removeTag('category', <?php echo e($category->id); ?>)">&times;</span>
                                                            <input type="hidden" name="category_ids[]"
                                                                value="<?php echo e($category->id); ?>">
                                                        </span>
                                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                            </div>
                                            <input type="text" class="tag-input" id="category-input"
                                                placeholder="<?php echo e(trans('shipping.select_categories')); ?>" autocomplete="off">
                                            <div class="tag-dropdown" id="category-dropdown" style="display: none;">
                                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $category): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                    <div class="tag-option" data-id="<?php echo e($category->id); ?>"
                                                        data-name="<?php echo e(addslashes($category->name)); ?>"
                                                        onclick="addTag('category', <?php echo e($category->id); ?>, '<?php echo e(addslashes($category->name)); ?>')">
                                                        <?php echo e($category->name); ?>

                                                    </div>
                                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                            </div>
                                        </div>
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['category_ids'];
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
                                </div>
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                                
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($shippingSettings?->shipping_allow_sub_categories && $subCategories->count() > 0): ?>
                                <div class="col-md-6 mb-25">
                                    <div class="form-group">
                                        <label class="il-gray fs-14 fw-500 mb-10 d-block">
                                            <?php echo e(trans('shipping.sub_categories')); ?>

                                            <span class="text-danger">*</span>
                                        </label>
                                        <div class="tag-input-container" id="subcategory-tags-container">
                                            <div class="tags-display" id="subcategory-tags-display">
                                                <?php
                                                    $selectedSubCategoryIds = old(
                                                        'sub_category_ids',
                                                        isset($shipping) && $shipping->subCategories
                                                            ? $shipping->subCategories->pluck('id')->toArray()
                                                            : [],
                                                    );
                                                ?>
                                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $subCategories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $subCategory): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(in_array($subCategory->id, $selectedSubCategoryIds)): ?>
                                                        <span class="tag-badge" data-id="<?php echo e($subCategory->id); ?>">
                                                            <?php echo e($subCategory->name); ?>

                                                            <span class="tag-remove"
                                                                onclick="removeTag('subcategory', <?php echo e($subCategory->id); ?>)">&times;</span>
                                                            <input type="hidden" name="sub_category_ids[]"
                                                                value="<?php echo e($subCategory->id); ?>">
                                                        </span>
                                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                            </div>
                                            <input type="text" class="tag-input" id="subcategory-input"
                                                placeholder="<?php echo e(trans('shipping.select_sub_categories')); ?>" autocomplete="off">
                                            <div class="tag-dropdown" id="subcategory-dropdown" style="display: none;">
                                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $subCategories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $subCategory): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                    <div class="tag-option" data-id="<?php echo e($subCategory->id); ?>"
                                                        data-name="<?php echo e(addslashes($subCategory->name)); ?>"
                                                        onclick="addTag('subcategory', <?php echo e($subCategory->id); ?>, '<?php echo e(addslashes($subCategory->name)); ?>')">
                                                        <?php echo e($subCategory->name); ?>

                                                    </div>
                                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                            </div>
                                        </div>
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['sub_category_ids'];
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
                                </div>
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </div>

                            
                            <div class="row mt-30">
                                <div class="col-12">
                                    <div class="d-flex justify-content-end gap-2">
                                        <a href="<?php echo e(route('admin.shippings.index')); ?>"
                                            class="btn btn-light btn-default btn-squared">
                                            <i class="uil uil-arrow-left me-1"></i>
                                            <?php echo e(trans('main.cancel')); ?>

                                        </a>
                                        <button type="submit" class="btn btn-primary btn-squared" id="submitBtn">
                                            <i class="uil uil-check me-1"></i>
                                            <?php echo e(isset($shipping) ? trans('common.update') : trans('common.create')); ?>

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

    <?php $__env->startPush('styles'); ?>
        <style>
            .tag-input-container {
                position: relative;
                border: 1px solid #e3e6ef;
                border-radius: 4px;
                padding: 8px;
                min-height: 45px;
                background: #fff;
                cursor: text;
            }

            .tag-input-container:focus-within {
                border-color: #0056B7;
                box-shadow: 0 0 0 0.2rem rgba(0, 86, 183, 0.15);
            }

            .tags-display {
                display: inline-flex;
                flex-wrap: wrap;
                gap: 6px;
                margin-bottom: 4px;
            }

            .tag-badge {
                display: inline-flex;
                align-items: center;
                background-color: #0056B7;
                color: #fff;
                padding: 5px 10px;
                border-radius: 4px;
                font-size: 13px;
                font-weight: 500;
                gap: 6px;
            }

            .tag-remove {
                cursor: pointer;
                font-size: 18px;
                line-height: 1;
                font-weight: bold;
                opacity: 0.8;
                transition: opacity 0.2s;
            }

            .tag-remove:hover {
                opacity: 1;
                color: #ff6b6b;
            }

            .tag-input {
                border: none;
                outline: none;
                padding: 4px;
                font-size: 14px;
                flex: 1;
                min-width: 150px;
            }

            .tag-dropdown {
                position: absolute;
                top: 100%;
                left: 0;
                right: 0;
                background: #fff;
                border: 1px solid #e3e6ef;
                border-radius: 4px;
                margin-top: 4px;
                max-height: 200px;
                overflow-y: auto;
                z-index: 1000;
                box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            }

            .tag-option {
                padding: 10px 12px;
                cursor: pointer;
                transition: background-color 0.2s;
            }

            .tag-option:hover {
                background-color: #f8f9fa;
            }

            .tag-option.selected {
                background-color: #e3f2fd;
                color: #0056B7;
            }
        </style>
    <?php $__env->stopPush(); ?>

    <?php $__env->startPush('scripts'); ?>
        <script>
            // Wait for both DOM and jQuery to be ready
            (function() {
                function initTagInputs() {
                    if (typeof jQuery === 'undefined') {
                        console.error('jQuery is not loaded yet, retrying...');
                        setTimeout(initTagInputs, 100);
                        return;
                    }

                    console.log('Tag input initialization started');

                    // Tag input functionality
                    function setupTagInput(type) {
                        const input = $(`#${type}-input`);
                        const dropdown = $(`#${type}-dropdown`);
                        const container = $(`#${type}-tags-container`);

                        console.log(`Setting up ${type} tag input`, {
                            input: input.length,
                            dropdown: dropdown.length,
                            container: container.length
                        });

                        // Show dropdown on container click
                        container.on('click', function(e) {
                            e.stopPropagation();
                            input.focus();
                        });

                        // Show dropdown on focus
                        input.on('focus', function() {
                            console.log(`${type} input focused`);
                            dropdown.show();
                            filterOptions(type, '');
                        });

                        // Filter options on input
                        input.on('input', function() {
                            const searchTerm = $(this).val().toLowerCase();
                            filterOptions(type, searchTerm);
                        });

                        // Hide dropdown when clicking outside
                        $(document).on('click', function(e) {
                            if (!container.is(e.target) && container.has(e.target).length === 0) {
                                dropdown.hide();
                            }
                        });
                    }

                    function filterOptions(type, searchTerm) {
                        const dropdown = $(`#${type}-dropdown`);
                        dropdown.find('.tag-option').each(function() {
                            const optionText = $(this).data('name').toLowerCase();
                            const isSelected = $(
                                `#${type}-tags-display .tag-badge[data-id="${$(this).data('id')}"]`).length > 0;

                            if (isSelected) {
                                $(this).addClass('selected').hide();
                            } else if (searchTerm === '' || optionText.includes(searchTerm)) {
                                $(this).removeClass('selected').show();
                            } else {
                                $(this).hide();
                            }
                        });
                    }

                    // Initialize both inputs
                    setupTagInput('city');
                    setupTagInput('category');
                    setupTagInput('department');
                    setupTagInput('subcategory');
                    console.log('Tag input initialization completed');
                }

                // Start initialization when DOM is ready
                if (document.readyState === 'loading') {
                    document.addEventListener('DOMContentLoaded', initTagInputs);
                } else {
                    initTagInputs();
                }
            })();

            // Add tag function
            function addTag(type, id, name) {
                console.log('addTag called:', {
                    type,
                    id,
                    name
                });
                const display = $(`#${type}-tags-display`);
                const input = $(`#${type}-input`);

                // Check if already added
                if (display.find(`.tag-badge[data-id="${id}"]`).length > 0) {
                    console.log('Tag already exists, skipping');
                    return;
                }

                // Map type to correct input name
                let inputName = type + '_ids[]';
                if (type === 'subcategory') {
                    inputName = 'sub_category_ids[]';
                }

                // Create tag badge
                const tagHtml = `
                <span class="tag-badge" data-id="${id}">
                    ${name}
                    <span class="tag-remove" onclick="removeTag('${type}', ${id})">&times;</span>
                    <input type="hidden" name="${inputName}" value="${id}">
                </span>
            `;

                display.append(tagHtml);
                input.val('').focus();

                // Update dropdown
                $(`#${type}-dropdown .tag-option[data-id="${id}"]`).addClass('selected').hide();
                console.log('Tag added successfully');
            }

            // Remove tag function
            function removeTag(type, id) {
                console.log('removeTag called:', {
                    type,
                    id
                });
                $(`#${type}-tags-display .tag-badge[data-id="${id}"]`).remove();
                $(`#${type}-dropdown .tag-option[data-id="${id}"]`).removeClass('selected').show();
                console.log('Tag removed successfully');
            }

            // Form submission with loading overlay
            $('#shippingForm').on('submit', function(e) {
                e.preventDefault();

                // Show loading overlay
                if (typeof LoadingOverlay !== 'undefined') {
                    LoadingOverlay.show({
                        text: '<?php echo e(isset($shipping) ? trans('main.updating') : trans('main.creating')); ?>',
                        subtext: '<?php echo e(trans('main.please wait')); ?>'
                    });
                }

                const formData = new FormData(this);
                const url = $(this).attr('action');

                // Always use POST for AJAX requests, Laravel will handle method spoofing via _method field
                $.ajax({
                    url: url,
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        if (response.success) {
                            if (typeof LoadingOverlay !== 'undefined') {
                                LoadingOverlay.showSuccess(
                                    response.message,
                                    '<?php echo e(trans('main.redirecting')); ?>'
                                );
                            }

                            setTimeout(function() {
                                window.location.href = '<?php echo e(route('admin.shippings.index')); ?>';
                            }, 1500);
                        } else {
                            if (typeof LoadingOverlay !== 'undefined') {
                                LoadingOverlay.hide();
                            }
                            showAlert('danger', response.message);
                        }
                    },
                    error: function(xhr) {
                        if (typeof LoadingOverlay !== 'undefined') {
                            LoadingOverlay.hide();
                        }

                        if (xhr.status === 422) {
                            const errors = xhr.responseJSON.errors;
                            let errorHtml = '<ul class="mb-0">';
                            $.each(errors, function(key, value) {
                                errorHtml += '<li>' + value[0] + '</li>';
                            });
                            errorHtml += '</ul>';
                            showAlert('danger', errorHtml);
                        } else {
                            const message = xhr.responseJSON?.message ||
                                '<?php echo e(trans('shipping.error_creating')); ?>';
                            showAlert('danger', message);
                        }
                    }
                });
            });

            // Alert function
            function showAlert(type, message) {
                const alertHtml = `
                    <div class="alert alert-${type} alert-dismissible fade show" role="alert">
                        ${message}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                `;
                $('#alertContainer').html(alertHtml);
                $('html, body').animate({
                    scrollTop: 0
                }, 'slow');
            }
        </script>
    <?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?>


<?php $__env->startPush('after-body'); ?>
    <?php if (isset($component)) { $__componentOriginal115e82920da0ed7c897ee494af74b9d8 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal115e82920da0ed7c897ee494af74b9d8 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.loading-overlay','data' => ['loadingText' => isset($shipping) ? trans('main.updating') : trans('main.creating'),'loadingSubtext' => trans('main.please wait')]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('loading-overlay'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['loadingText' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(isset($shipping) ? trans('main.updating') : trans('main.creating')),'loadingSubtext' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(trans('main.please wait'))]); ?>
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
<?php echo $__env->make('layout.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\eramo-multi-vendor\Modules/Order\resources/views/shippings/form.blade.php ENDPATH**/ ?>