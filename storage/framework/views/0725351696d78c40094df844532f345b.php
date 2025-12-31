
<?php $__env->startSection('title'); ?>
    <?php echo e(trans('catalogmanagement::variantsconfig.view_variants_config')); ?> | Bnaia
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12">
                <?php if (isset($component)) { $__componentOriginale19f62b34dfe0bfdf95075badcb45bc2 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginale19f62b34dfe0bfdf95075badcb45bc2 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.breadcrumb','data' => ['items' => [
                    ['title' => trans('dashboard.title'), 'url' => route('admin.dashboard'), 'icon' => 'uil uil-estate'],
                    ['title' => trans('catalogmanagement::variantsconfig.variants_configurations'), 'url' => route('admin.variants-configurations.index')],
                    ['title' => trans('catalogmanagement::variantsconfig.view_variants_config')]
                ]]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('breadcrumb'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['items' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute([
                    ['title' => trans('dashboard.title'), 'url' => route('admin.dashboard'), 'icon' => 'uil uil-estate'],
                    ['title' => trans('catalogmanagement::variantsconfig.variants_configurations'), 'url' => route('admin.variants-configurations.index')],
                    ['title' => trans('catalogmanagement::variantsconfig.view_variants_config')]
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
                <div class="card border-0 shadow-sm mb-3">
                    <div class="card-header bg-white border-bottom py-20 d-flex justify-content-between align-items-center">
                        <h5 class="mb-0 fw-500"><?php echo e(trans('catalogmanagement::variantsconfig.variants_config_details')); ?></h5>
                        <div class="d-flex gap-10">
                            <a href="<?php echo e(route('admin.variants-configurations.index')); ?>" class="btn btn-light btn-sm">
                                <i class="uil uil-arrow-left me-2"></i><?php echo e(trans('common.back_to_list')); ?>

                            </a>
                            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('variants-configurations.edit')): ?>
                            <a href="<?php echo e(route('admin.variants-configurations.edit', $variantsConfig->id)); ?>" class="btn btn-primary btn-sm">
                                <i class="uil uil-edit me-2"></i><?php echo e(trans('common.edit')); ?>

                            </a>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="card card-holder">
                                    <div class="card-header">
                                        <h3>
                                            <i class="uil uil-info-circle me-1"></i><?php echo e(trans('common.basic_information')); ?>

                                        </h3>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            
                                            <?php if (isset($component)) { $__componentOriginale5a3093cad4b0bccb881a74044179ded = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginale5a3093cad4b0bccb881a74044179ded = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.translation-display','data' => ['label' => trans('catalogmanagement::variantsconfig.name'),'model' => $variantsConfig,'fieldName' => 'name','languages' => $languages]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('translation-display'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['label' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(trans('catalogmanagement::variantsconfig.name')),'model' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($variantsConfig),'fieldName' => 'name','languages' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($languages)]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginale5a3093cad4b0bccb881a74044179ded)): ?>
<?php $attributes = $__attributesOriginale5a3093cad4b0bccb881a74044179ded; ?>
<?php unset($__attributesOriginale5a3093cad4b0bccb881a74044179ded); ?>
<?php endif; ?>
<?php if (isset($__componentOriginale5a3093cad4b0bccb881a74044179ded)): ?>
<?php $component = $__componentOriginale5a3093cad4b0bccb881a74044179ded; ?>
<?php unset($__componentOriginale5a3093cad4b0bccb881a74044179ded); ?>
<?php endif; ?>

                                            
                                            <div class="col-md-6">
                                                <div class="view-item">
                                                    <label class="il-gray fs-14 fw-500 mb-10"><?php echo e(trans('catalogmanagement::variantsconfig.type')); ?></label>
                                                    <p class="fs-15 color-dark">
                                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($variantsConfig->type): ?>
                                                            <span class="badge badge-<?php echo e($variantsConfig->type == 'color' ? 'info' : 'secondary'); ?> badge-round badge-lg">
                                                                <i class="uil uil-<?php echo e($variantsConfig->type == 'color' ? 'palette' : 'text'); ?>"></i>
                                                                <?php echo e(trans('catalogmanagement::variantsconfig.' . $variantsConfig->type)); ?>

                                                            </span>
                                                        <?php else: ?>
                                                            <span class="text-muted">-</span>
                                                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                                    </p>
                                                </div>
                                            </div>

                                            
                                            <div class="col-md-6">
                                                <div class="view-item">
                                                    <label class="il-gray fs-14 fw-500 mb-10"><?php echo e(trans('catalogmanagement::variantsconfig.value')); ?></label>
                                                    <p class="fs-15 color-dark">
                                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($variantsConfig->value): ?>
                                                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($variantsConfig->type == 'color'): ?>
                                                                <span class="d-inline-flex align-items-center gap-2">
                                                                    <span class="color-preview" style="background-color: <?php echo e($variantsConfig->value); ?>; width: 30px; height: 30px; border-radius: 6px; border: 2px solid #dee2e6; display: inline-block;"></span>
                                                                    <strong><?php echo e($variantsConfig->value); ?></strong>
                                                                </span>
                                                            <?php else: ?>
                                                                <strong><?php echo e($variantsConfig->value); ?></strong>
                                                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                                        <?php else: ?>
                                                            <span class="text-muted">-</span>
                                                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                                    </p>
                                                </div>
                                            </div>

                                            
                                            <div class="col-md-6">
                                                <div class="view-item">
                                                    <label class="il-gray fs-14 fw-500 mb-10"><?php echo e(trans('catalogmanagement::variantsconfig.key')); ?></label>
                                                    <p class="fs-15 color-dark">
                                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($variantsConfig->key): ?>
                                                            <span class="badge badge-primary badge-round badge-lg">
                                                                <i class="uil uil-key-skeleton-alt"></i>
                                                                <?php echo e($variantsConfig->key->getTranslation('name', app()->getLocale())); ?>

                                                            </span>
                                                        <?php else: ?>
                                                            <span class="text-muted">-</span>
                                                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                                    </p>
                                                </div>
                                            </div>

                                            
                                            <div class="col-md-6">
                                                <div class="view-item">
                                                    <label class="il-gray fs-14 fw-500 mb-10"><?php echo e(trans('catalogmanagement::variantsconfig.parent') ?? 'Parent'); ?></label>
                                                    <p class="fs-15 color-dark">
                                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($variantsConfig->parent_data): ?>
                                                            <a href="<?php echo e(route('admin.variants-configurations.show', $variantsConfig->parent_data->id)); ?>" class="badge badge-primary badge-round badge-lg">
                                                                <?php echo e($variantsConfig->parent_data->getTranslation('name', app()->getLocale())); ?>

                                                            </a>
                                                        <?php else: ?>
                                                            <span class="text-muted"><?php echo e(trans('catalogmanagement::variantsconfig.no_parent') ?? 'No Parent'); ?></span>
                                                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                                    </p>
                                                </div>
                                            </div>

                                            
                                            <div class="col-md-6">
                                                <div class="view-item">
                                                    <label class="il-gray fs-14 fw-500 mb-10"><?php echo e(trans('catalogmanagement::variantsconfig.children_count')); ?></label>
                                                    <p class="fs-15 color-dark">
                                                        <span class="badge badge-info badge-round badge-lg"><?php echo e($variantsConfig->children->count()); ?></span>
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($variantsConfig->children && $variantsConfig->children->count() > 0): ?>
                                    <div class="card card-holder mt-3">
                                        <div class="card-header">
                                            <h3>
                                                <i class="uil uil-sitemap me-1"></i><?php echo e(trans('catalogmanagement::variantsconfig.children') ?? 'Children'); ?>

                                            </h3>
                                        </div>
                                        <div class="card-body">
                                            <div class="row">
                                                <div class="col-md-12">
                                                    <div class="view-item">
                                                        <p class="fs-15 color-dark">
                                                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $variantsConfig->children; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $child): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                                <a href="<?php echo e(route('admin.variants-configurations.show', $child->id)); ?>" class="badge badge-primary badge-round badge-lg me-2 mb-2">
                                                                    <?php echo e($child->getTranslation('name', app()->getLocale())); ?>

                                                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($child->value): ?>
                                                                        (<?php echo e($child->value); ?>)
                                                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                                                </a>
                                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                                        </p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                                <div class="card card-holder mt-3">
                                    <div class="card-header">
                                        <h3>
                                            <i class="uil uil-clock me-1"></i><?php echo e(trans('common.timestamps')); ?>

                                        </h3>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="view-item">
                                                    <label class="il-gray fs-14 fw-500 mb-10"><?php echo e(trans('common.created_at')); ?></label>
                                                    <p class="fs-15 color-dark"><?php echo e($variantsConfig->created_at); ?></p>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="view-item">
                                                    <label class="il-gray fs-14 fw-500 mb-10"><?php echo e(trans('common.updated_at')); ?></label>
                                                    <p class="fs-15 color-dark"><?php echo e($variantsConfig->updated_at); ?></p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layout.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\eramo-multi-vendor\Modules/CatalogManagement\resources/views/variants-config/show.blade.php ENDPATH**/ ?>