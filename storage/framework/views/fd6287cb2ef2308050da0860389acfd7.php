

<?php $__env->startSection('title', __('catalogmanagement::product.view_product')); ?>

<?php $__env->startPush('styles'); ?>
    <style>
        /* Product View HTML Content Styling */
        .fs-15.color-dark {
            line-height: 1.6;
        }

        .fs-15.color-dark table {
            width: 100%;
            border-collapse: collapse;
            margin: 10px 0;
            background: #fff;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }

        .fs-15.color-dark table th,
        .fs-15.color-dark table td {
            padding: 12px 15px;
            text-align: left;
            border-bottom: 1px solid #e3e6f0;
        }

        .fs-15.color-dark table th {
            background: linear-gradient(135deg, #4e73df 0%, #224abe 100%);
            color: white;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            font-size: 12px;
        }

        .fs-15.color-dark table tr:hover {
            background-color: #f8f9fa;
        }

        .fs-15.color-dark table tr:last-child td {
            border-bottom: none;
        }

        .fs-15.color-dark strong {
            color: #2c3e50;
            font-weight: 600;
        }

        .fs-15.color-dark em {
            color: #7f8c8d;
            font-style: italic;
        }

        .fs-15.color-dark ul,
        .fs-15.color-dark ol {
            margin: 10px 0;
            padding-left: 20px;
        }

        .fs-15.color-dark li {
            margin-bottom: 5px;
            line-height: 1.5;
        }

        .fs-15.color-dark p {
            margin-bottom: 10px;
            line-height: 1.6;
        }

        .fs-15.color-dark h1,
        .fs-15.color-dark h2,
        .fs-15.color-dark h3,
        .fs-15.color-dark h4,
        .fs-15.color-dark h5,
        .fs-15.color-dark h6 {
            margin: 15px 0 10px 0;
            color: #2c3e50;
            font-weight: 600;
        }

        .fs-15.color-dark blockquote {
            border-left: 4px solid #4e73df;
            padding-left: 15px;
            margin: 15px 0;
            background: #f8f9fa;
            padding: 15px;
            border-radius: 4px;
        }

        .fs-15.color-dark a {
            color: #4e73df;
            text-decoration: none;
        }

        .fs-15.color-dark a:hover {
            color: #224abe;
            text-decoration: underline;
        }

        /* Arabic content styling */
        .fs-15.color-dark[style*="direction: rtl"] {
            font-family: 'Cairo', 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .fs-15.color-dark[style*="direction: rtl"] table th,
        .fs-15.color-dark[style*="direction: rtl"] table td {
            text-align: right;
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
                    [
                        'title' => __('catalogmanagement::product.products_management'),
                        'url' => route('admin.products.index'),
                    ],
                    ['title' => __('catalogmanagement::product.view_product')],
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
                    [
                        'title' => __('catalogmanagement::product.products_management'),
                        'url' => route('admin.products.index'),
                    ],
                    ['title' => __('catalogmanagement::product.view_product')],
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
                        <h5 class="mb-0 fw-500"><?php echo e(__('catalogmanagement::product.product_details')); ?></h5>
                        <div class="d-flex gap-10">
                            <a href="<?php echo e(route('admin.products.index')); ?>" class="btn btn-light btn-sm">
                                <i class="uil uil-arrow-left me-2"></i><?php echo e(__('common.back_to_list')); ?>

                            </a>
                            <a href="<?php echo e(route('admin.products.edit', $product->id)); ?>" class="btn btn-primary btn-sm">
                                <i class="uil uil-edit me-2"></i><?php echo e(__('common.edit')); ?>

                            </a>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-8 order-2 order-md-1">
                                
                                <div class="card card-holder">
                                    <div class="card-header">
                                        <h3>
                                            <i
                                                class="uil uil-info-circle me-1"></i><?php echo e(__('catalogmanagement::product.basic_information') ?? 'Basic Information'); ?>

                                        </h3>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <?php if (isset($component)) { $__componentOriginale5a3093cad4b0bccb881a74044179ded = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginale5a3093cad4b0bccb881a74044179ded = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.translation-display','data' => ['label' => __('catalogmanagement::product.title'),'model' => $product->product,'fieldName' => 'title','languages' => $languages]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('translation-display'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['label' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(__('catalogmanagement::product.title')),'model' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($product->product),'fieldName' => 'title','languages' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($languages)]); ?>
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

                                            <?php if (isset($component)) { $__componentOriginale5a3093cad4b0bccb881a74044179ded = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginale5a3093cad4b0bccb881a74044179ded = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.translation-display','data' => ['label' => __('catalogmanagement::product.details'),'model' => $product->product,'fieldName' => 'details','languages' => $languages,'type' => 'html']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('translation-display'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['label' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(__('catalogmanagement::product.details')),'model' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($product->product),'fieldName' => 'details','languages' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($languages),'type' => 'html']); ?>
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

                                            <?php if (isset($component)) { $__componentOriginale5a3093cad4b0bccb881a74044179ded = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginale5a3093cad4b0bccb881a74044179ded = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.translation-display','data' => ['label' => __('catalogmanagement::product.summary'),'model' => $product->product,'fieldName' => 'summary','languages' => $languages,'type' => 'html']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('translation-display'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['label' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(__('catalogmanagement::product.summary')),'model' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($product->product),'fieldName' => 'summary','languages' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($languages),'type' => 'html']); ?>
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

                                            <?php if (isset($component)) { $__componentOriginale5a3093cad4b0bccb881a74044179ded = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginale5a3093cad4b0bccb881a74044179ded = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.translation-display','data' => ['label' => __('catalogmanagement::product.features'),'model' => $product->product,'fieldName' => 'features','languages' => $languages,'type' => 'html']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('translation-display'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['label' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(__('catalogmanagement::product.features')),'model' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($product->product),'fieldName' => 'features','languages' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($languages),'type' => 'html']); ?>
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

                                            <?php if (isset($component)) { $__componentOriginale5a3093cad4b0bccb881a74044179ded = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginale5a3093cad4b0bccb881a74044179ded = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.translation-display','data' => ['label' => __('catalogmanagement::product.instructions'),'model' => $product->product,'fieldName' => 'instructions','languages' => $languages,'type' => 'html']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('translation-display'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['label' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(__('catalogmanagement::product.instructions')),'model' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($product->product),'fieldName' => 'instructions','languages' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($languages),'type' => 'html']); ?>
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
                                            <?php if (isset($component)) { $__componentOriginale5a3093cad4b0bccb881a74044179ded = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginale5a3093cad4b0bccb881a74044179ded = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.translation-display','data' => ['label' => __('catalogmanagement::product.tags'),'model' => $product->product,'fieldName' => 'tags','languages' => $languages,'type' => 'keywords']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('translation-display'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['label' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(__('catalogmanagement::product.tags')),'model' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($product->product),'fieldName' => 'tags','languages' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($languages),'type' => 'keywords']); ?>
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
                                                    <label
                                                        class="il-gray fs-14 fw-500 mb-10"><?php echo e(__('catalogmanagement::product.brand')); ?></label>
                                                    <p class="fs-15 color-dark">
                                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($product->product->brand): ?>
                                                            <span class="badge badge-round badge-primary badge-lg">
                                                                <?php echo e($product->product->brand->getTranslation('name', app()->getLocale()) ?? ($product->product->brand->getTranslation('name', 'en') ?? ($product->product->brand->getTranslation('name', 'ar') ?? '-'))); ?>

                                                            </span>
                                                        <?php else: ?>
                                                            <span class="text-muted">-</span>
                                                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                                    </p>
                                                </div>
                                            </div>

                                            
                                            <div class="col-md-6">
                                                <div class="view-item">
                                                    <label
                                                        class="il-gray fs-14 fw-500 mb-10"><?php echo e(__('catalogmanagement::product.department')); ?></label>
                                                    <p class="fs-15 color-dark">
                                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($product->product->department): ?>
                                                            <span class="badge badge-round badge-info badge-round badge-lg">
                                                                <?php echo e($product->product->department->getTranslation('name', app()->getLocale()) ?? ($product->product->department->getTranslation('name', 'en') ?? ($product->product->department->getTranslation('name', 'ar') ?? '-'))); ?>

                                                            </span>
                                                        <?php else: ?>
                                                            <span class="text-muted">-</span>
                                                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                                    </p>
                                                </div>
                                            </div>

                                            
                                            <div class="col-md-6">
                                                <div class="view-item">
                                                    <label
                                                        class="il-gray fs-14 fw-500 mb-10"><?php echo e(__('catalogmanagement::product.category')); ?></label>
                                                    <p class="fs-15 color-dark">
                                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($product->product->category): ?>
                                                            <span
                                                                class="badge badge-round badge-primary badge-round badge-lg">
                                                                <?php echo e($product->product->category->getTranslation('name', app()->getLocale()) ?? ($product->product->category->getTranslation('name', 'en') ?? ($product->product->category->getTranslation('name', 'ar') ?? '-'))); ?>

                                                            </span>
                                                        <?php else: ?>
                                                            <span class="text-muted">-</span>
                                                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                                    </p>
                                                </div>
                                            </div>

                                            
                                            <div class="col-md-6">
                                                <div class="view-item">
                                                    <label
                                                        class="il-gray fs-14 fw-500 mb-10"><?php echo e(__('catalogmanagement::product.sub_category')); ?></label>
                                                    <p class="fs-15 color-dark">
                                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($product->product->subCategory): ?>
                                                            <span
                                                                class="badge badge-round badge-warning badge-round badge-lg">
                                                                <?php echo e($product->product->subCategory->getTranslation('name', app()->getLocale()) ?? ($product->product->subCategory->getTranslation('name', 'en') ?? ($product->product->subCategory->getTranslation('name', 'ar') ?? '-'))); ?>

                                                            </span>
                                                        <?php else: ?>
                                                            <span class="text-muted">-</span>
                                                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                                    </p>
                                                </div>
                                            </div>

                                            
                                            <div class="col-md-6">
                                                <div class="view-item">
                                                    <label
                                                        class="il-gray fs-14 fw-500 mb-10"><?php echo e(__('catalogmanagement::product.vendor')); ?></label>
                                                    <p class="fs-15">
                                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($product->vendor): ?>
                                                            <span
                                                                class="badge badge-round badge-primary badge-round badge-lg">
                                                                <?php echo e($product->vendor->getTranslation('name', app()->getLocale()) ?? ($product->vendor->getTranslation('name', 'en') ?? ($product->vendor->getTranslation('name', 'ar') ?? '-'))); ?>

                                                            </span>
                                                        <?php else: ?>
                                                            <span class="text-muted">-</span>
                                                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                                    </p>
                                                </div>
                                            </div>
                                            
                                            <div class="col-md-6">
                                                <div class="view-item">
                                                    <label
                                                        class="il-gray fs-14 fw-500 mb-10"><?php echo e(__('catalogmanagement::product.type') ?? 'Type'); ?></label>
                                                    <p class="fs-15 color-dark fw-500">
                                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($product->product->configuration_type === 'simple'): ?>
                                                            <span
                                                                class="badge badge-round badge-success badge-lg"><?php echo e(__('catalogmanagement::product.simple') ?? 'Simple'); ?></span>
                                                        <?php elseif($product->product->configuration_type === 'variants'): ?>
                                                            <span
                                                                class="badge badge-round badge-info badge-lg"><?php echo e(__('catalogmanagement::product.variants') ?? 'Variants'); ?></span>
                                                        <?php else: ?>
                                                            <span
                                                                class="text-muted"><?php echo e($product->product->configuration_type ?? '-'); ?></span>
                                                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                                    </p>
                                                </div>
                                            </div>
                                            
                                            <div class="col-md-6">
                                                <div class="view-item">
                                                    <label
                                                        class="il-gray fs-14 fw-500 mb-10"><?php echo e(__('catalogmanagement::product.product_type') ?? 'Product Type'); ?></label>
                                                    <p class="fs-15 color-dark fw-500">
                                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($product->product->type === 'bank'): ?>
                                                            <span
                                                                class="badge badge-round badge-primary badge-lg"><?php echo e(__('catalogmanagement::product.bank_product') ?? 'Bank Product'); ?></span>
                                                        <?php else: ?>
                                                            <span
                                                                class="badge badge-round badge-secondary badge-lg"><?php echo e(__('catalogmanagement::product.regular_product') ?? 'Regular Product'); ?></span>
                                                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                                    </p>
                                                </div>
                                            </div>
                                            
                                            <div class="col-md-6">
                                                <div class="view-item">
                                                    <label
                                                        class="il-gray fs-14 fw-500 mb-10"><?php echo e(__('catalogmanagement::product.sku')); ?></label>
                                                    <p class="fs-15 color-dark fw-500">
                                                        <?php echo e($product->sku ?? '-'); ?>

                                                    </p>
                                                </div>
                                            </div>

                                            
                                            <div class="col-md-6">
                                                <div class="view-item">
                                                    <label
                                                        class="il-gray fs-14 fw-500 mb-10"><?php echo e(__('catalogmanagement::product.slug')); ?></label>
                                                    <p class="fs-15 color-dark fw-500">
                                                        <code><?php echo e($product->product->slug ?? '-'); ?></code>
                                                    </p>
                                                </div>
                                            </div>

                                            
                                            <div class="col-md-6">
                                                <div class="view-item">
                                                    <label
                                                        class="il-gray fs-14 fw-500 mb-10"><?php echo e(__('catalogmanagement::product.video_link')); ?></label>
                                                    <p class="fs-15 color-dark fw-500">
                                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($product->video_link): ?>
                                                            <a href="<?php echo e($product->video_link); ?>" target="_blank" class="btn btn-danger btn-sm text-white" title="<?php echo e(__('catalogmanagement::product.open_video_link')); ?>">
                                                                <i class="uil uil-play-circle me-1"></i><?php echo e(__('catalogmanagement::product.open_video_link')); ?>

                                                            </a>
                                                        <?php else: ?>
                                                            <span class="text-muted">-</span>
                                                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="card card-holder mt-3">
                                    <div class="card-header">
                                        <h3>
                                            <i
                                                class="uil uil-info-circle me-1"></i><?php echo e(__('catalogmanagement::product.additional_information')); ?>

                                        </h3>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            
                                            <div class="col-md-4 mb-3">
                                                <div class="p-3 border rounded" style="background: #e7f3ff;">
                                                    <small
                                                        class="text-muted d-block mb-1"><?php echo e(__('catalogmanagement::product.max_per_order')); ?></small>
                                                    <div class="fw-bold text-info" style="font-size: 18px;">
                                                        <i
                                                            class="uil uil-shopping-cart me-1"></i><?php echo e($product->max_per_order ?? '-'); ?>

                                                    </div>
                                                </div>
                                            </div>

                                            
                                            <div class="col-md-4 mb-3">
                                                <div class="p-3 border rounded" style="background: #f8f9fa;">
                                                    <small
                                                        class="text-muted d-block mb-1"><?php echo e(__('catalogmanagement::product.tax')); ?></small>
                                                    <div class="fw-bold text-dark" style="font-size: 16px;">
                                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($product->taxes && $product->taxes->count() > 0): ?>
                                                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $product->taxes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $tax): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                                <span class="badge badge-round badge-info me-1">
                                                                    <?php echo e($tax->getTranslation('name', app()->getLocale()) ?? $tax->name); ?>

                                                                </span>
                                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                                        <?php else: ?>
                                                            <span class="text-muted">-</span>
                                                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                                    </div>
                                                </div>
                                            </div>

                                            
                                            <div class="col-md-4 mb-3">
                                                <div class="p-3 border rounded"
                                                    style="background: <?php echo e($product->is_featured ? '#d4edda' : '#f8d7da'); ?>;">
                                                    <small
                                                        class="text-muted d-block mb-1"><?php echo e(__('catalogmanagement::product.featured')); ?></small>
                                                    <div class="fw-bold <?php echo e($product->is_featured ? 'text-success' : 'text-danger'); ?>"
                                                        style="font-size: 16px;">
                                                        <i
                                                            class="uil <?php echo e($product->is_featured ? 'uil-star' : 'uil-times'); ?> me-1"></i>
                                                        <?php echo e($product->is_featured ? __('common.yes') : __('common.no')); ?>

                                                    </div>
                                                </div>
                                            </div>

                                            
                                            <div class="col-md-4 mb-3">
                                                <div class="p-3 border rounded"
                                                    style="background: <?php echo e($product->is_active ? '#d4edda' : '#f8d7da'); ?>;">
                                                    <small
                                                        class="text-muted d-block mb-1"><?php echo e(__('common.status')); ?></small>
                                                    <div class="fw-bold <?php echo e($product->is_active ? 'text-success' : 'text-danger'); ?>"
                                                        style="font-size: 16px;">
                                                        <i
                                                            class="uil <?php echo e($product->is_active ? 'uil-check-circle' : 'uil-times-circle'); ?> me-1"></i>
                                                        <?php echo e($product->is_active ? __('common.active') : __('common.inactive')); ?>

                                                    </div>
                                                </div>
                                            </div>

                                            
                                            <div class="col-md-4 mb-3">
                                                <div class="p-3 border rounded"
                                                    style="background:
                                                    <?php if($product->status === 'approved'): ?> #d4edda
                                                    <?php elseif($product->status === 'rejected'): ?> #f8d7da
                                                    <?php else: ?> #fff3cd <?php endif; ?>;">
                                                    <small
                                                        class="text-muted d-block mb-1"><?php echo e(__('catalogmanagement::product.approval_status')); ?></small>
                                                    <div class="fw-bold
                                                        <?php if($product->status === 'approved'): ?> text-success
                                                        <?php elseif($product->status === 'rejected'): ?> text-danger
                                                        <?php else: ?> text-warning <?php endif; ?>"
                                                        style="font-size: 16px;">
                                                        <i
                                                            class="uil
                                                            <?php if($product->status === 'approved'): ?> uil-check-circle
                                                            <?php elseif($product->status === 'rejected'): ?> uil-times-circle
                                                            <?php else: ?> uil-clock <?php endif; ?> me-1"></i>
                                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($product->status === 'approved'): ?>
                                                            <?php echo e(__('common.approved')); ?>

                                                        <?php elseif($product->status === 'rejected'): ?>
                                                            <?php echo e(__('common.rejected')); ?>

                                                        <?php else: ?>
                                                            <?php echo e(__('common.pending')); ?>

                                                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                                    </div>
                                                </div>
                                            </div>

                                            
                                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($product->status === 'rejected' && $product->rejection_reason): ?>
                                                <div class="col-md-12 mb-3">
                                                    <div class="alert alert-danger d-flex align-items-start"
                                                        role="alert">
                                                        <i class="uil uil-exclamation-triangle me-2"
                                                            style="font-size: 24px;"></i>
                                                        <div>
                                                            <h6 class="alert-heading mb-2">
                                                                <i
                                                                    class="uil uil-info-circle me-1"></i><?php echo e(__('catalogmanagement::product.rejection_reason')); ?>

                                                            </h6>
                                                            <p class="mb-0"><?php echo e($product->rejection_reason); ?></p>
                                                        </div>
                                                    </div>
                                                </div>
                                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                        </div>
                                    </div>
                                </div>

                                
                                <div class="card card-holder mt-3">
                                    <div class="card-header">
                                        <h3>
                                            <i
                                                class="uil uil-search me-1"></i><?php echo e(__('catalogmanagement::product.seo_information') ?? 'SEO Information'); ?>

                                        </h3>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <?php if (isset($component)) { $__componentOriginale5a3093cad4b0bccb881a74044179ded = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginale5a3093cad4b0bccb881a74044179ded = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.translation-display','data' => ['label' => __('catalogmanagement::product.meta_title'),'model' => $product->product,'fieldName' => 'meta_title','languages' => $languages]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('translation-display'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['label' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(__('catalogmanagement::product.meta_title')),'model' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($product->product),'fieldName' => 'meta_title','languages' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($languages)]); ?>
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
                                            <?php if (isset($component)) { $__componentOriginale5a3093cad4b0bccb881a74044179ded = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginale5a3093cad4b0bccb881a74044179ded = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.translation-display','data' => ['label' => __('catalogmanagement::product.meta_description'),'model' => $product->product,'fieldName' => 'meta_description','languages' => $languages]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('translation-display'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['label' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(__('catalogmanagement::product.meta_description')),'model' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($product->product),'fieldName' => 'meta_description','languages' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($languages)]); ?>
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
                                            <?php if (isset($component)) { $__componentOriginale5a3093cad4b0bccb881a74044179ded = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginale5a3093cad4b0bccb881a74044179ded = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.translation-display','data' => ['label' => __('catalogmanagement::product.meta_keywords'),'model' => $product->product,'fieldName' => 'meta_keywords','languages' => $languages,'type' => 'keywords']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('translation-display'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['label' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(__('catalogmanagement::product.meta_keywords')),'model' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($product->product),'fieldName' => 'meta_keywords','languages' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($languages),'type' => 'keywords']); ?>
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
                                        </div>
                                    </div>
                                </div>
                            </div>

                            
                            <div class="col-md-4 order-1 order-md-2">
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="card card-holder">
                                            <div class="card-header">
                                                <h3>
                                                    <i
                                                        class="uil uil-image me-1"></i><?php echo e(__('catalogmanagement::product.images')); ?>

                                                </h3>
                                            </div>
                                            <div class="card-body">
                                                
                                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($product->product->mainImage): ?>
                                                    <div class="mb-3">
                                                        <label
                                                            class="il-gray fs-14 fw-500 mb-10"><?php echo e(__('catalogmanagement::product.main_image')); ?></label>
                                                        <div class="image-wrapper text-center">
                                                            <img src="<?php echo e(asset('storage/' . $product->product->mainImage->path)); ?>"
                                                                alt="<?php echo e($product->product->getTranslation('title')); ?>"
                                                                class="product-image img-fluid rounded"
                                                                style="max-height: 300px;">
                                                        </div>
                                                    </div>
                                                <?php else: ?>
                                                    <div class="mb-3">
                                                        <label
                                                            class="il-gray fs-14 fw-500 mb-10"><?php echo e(__('catalogmanagement::product.main_image')); ?></label>
                                                        <div class="image-wrapper text-center">
                                                            <img src="<?php echo e(asset('assets/img/default.png')); ?>"
                                                                alt="<?php echo e($product->product->getTranslation('title')); ?>"
                                                                class="product-image img-fluid rounded"
                                                                style="max-height: 300px;">
                                                        </div>
                                                    </div>
                                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($product->product->additionalImages && $product->product->additionalImages->count() > 0): ?>
                                            <div class="card card-holder mt-3">
                                                <div class="card-header">
                                                    <h3>
                                                        <i
                                                            class="uil uil-images me-1"></i><?php echo e(__('catalogmanagement::product.additional_images')); ?>

                                                    </h3>
                                                </div>
                                                <div class="card-body">
                                                    <div class="slick-slider global-slider slick-dots-bottom"
                                                        data-dots-slick='true' data-autoplay-slick='true'>
                                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $product->product->additionalImages; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $image): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                            <div class="slick-slider__single d-flex justify-content-center align-items-center"
                                                                style="height: 400px; background: #f8f9fa; cursor: pointer;"
                                                                ondblclick="openImageModal(<?php echo e($index); ?>)">
                                                                <img src="<?php echo e(asset('storage/' . $image->path)); ?>"
                                                                    alt="<?php echo e(__('catalogmanagement::product.additional_image') ?? 'Additional Image'); ?>"
                                                                    style="max-width: 100%; max-height: 100%; object-fit: contain;">
                                                            </div>
                                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                                    </div>
                                                </div>
                                            </div>

                                            
                                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $product->product->additionalImages; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $image): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <div class="modal fade" id="imageModal<?php echo e($index); ?>"
                                                    tabindex="-1" aria-labelledby="imageModalLabel<?php echo e($index); ?>"
                                                    aria-hidden="true">
                                                    <div class="modal-dialog modal-lg modal-dialog-centered">
                                                        <div class="modal-content">
                                                            <div class="modal-body p-0 d-flex justify-content-center align-items-center"
                                                                style="min-height: 500px; background: #f8f9fa;">
                                                                <img src="<?php echo e(asset('storage/' . $image->path)); ?>"
                                                                    alt="<?php echo e(__('catalogmanagement::product.additional_image') ?? 'Additional Image'); ?>"
                                                                    style="max-width: 100%; max-height: 100%; object-fit: contain;">
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12">
                                                                
                                <div class="card card-holder mt-3">
                                    <div class="card-header">
                                        <h3>
                                            <i
                                                class="uil uil-box me-1"></i><?php echo e(__('catalogmanagement::product.variants_and_stock') ?? 'Variants & Stock'); ?>

                                        </h3>
                                    </div>
                                    <div class="card-body">
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $product->variants; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $variantIndex => $variant): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <div class="mb-4 pb-4"
                                                style="<?php if(!$loop->last): ?> border-bottom: 1px solid #e9ecef; <?php endif; ?>">
                                                
                                                <div class="mb-3">
                                                    <div class="d-flex align-items-center flex-wrap gap-2 mb-2">
                                                        
                                                        <span class="badge badge-round badge-lg"
                                                            style="background-color: #17a2b8; color: white; padding: 8px 12px; border-radius: 20px;">
                                                            <i
                                                                class="uil uil-barcode me-1"></i><?php echo e(__('catalogmanagement::product.sku')); ?>:
                                                            <?php echo e($variant->sku ?? '-'); ?>

                                                        </span>

                                                        
                                                        <span class="badge badge-round badge-lg"
                                                            style="background-color: #28a745; color: white; padding: 8px 12px; border-radius: 20px;">
                                                            <i
                                                                class="uil uil-box me-1"></i><?php echo e(__('catalogmanagement::product.stock')); ?>:
                                                            <?php echo e($variant->total_stock ?? 0); ?>

                                                        </span>

                                                        
                                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($variant->variantConfiguration): ?>
                                                            <div class="variant-tree-display">
                                                                <?php
                                                                    // Build the variant hierarchy by traversing up the parent chain
                                                                    $values = [];
                                                                    $rootKeyName = '';
                                                                    $current = $variant->variantConfiguration;
                                                                    $visited = []; // Prevent infinite loops

                                                                    // Collect all values from leaf to root
                                                                    while (
                                                                        $current &&
                                                                        !in_array($current->id, $visited)
                                                                    ) {
                                                                        $visited[] = $current->id;

                                                                        // Get the value name (current node)
                                                                        $valueName =
                                                                            $current->getTranslation(
                                                                                'name',
                                                                                app()->getLocale(),
                                                                            ) ??
                                                                            ($current->getTranslation('name', 'en') ??
                                                                                ($current->name ??
                                                                                    ($current->value ?? 'Value')));

                                                                        // Add value to the beginning of array
                                                                        array_unshift($values, $valueName);

                                                                        // Move to parent
                                                                        if ($current->parent_data) {
                                                                            $current = $current->parent_data;
                                                                        } else {
                                                                            // Reached root, get the key name
                                                                            $rootKeyName = $current->key
                                                                                ? $current->key->getTranslation(
                                                                                        'name',
                                                                                        app()->getLocale(),
                                                                                    ) ??
                                                                                    ($current->key->getTranslation(
                                                                                        'name',
                                                                                        'en',
                                                                                    ) ??
                                                                                        ($current->key->name ?? 'Key'))
                                                                                : 'Key';
                                                                            break;
                                                                        }
                                                                    }
                                                                ?>

                                                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(count($values) > 0): ?>
                                                                    <div class="d-flex align-items-center flex-wrap gap-2">
                                                                        
                                                                        <span class="badge badge-round badge-lg"
                                                                            style="background: linear-gradient(135deg, #6f42c1 0%, #5a32a3 100%);
                                                                                     color: white; padding: 6px 10px; border-radius: 15px; font-size: 12px;
                                                                                     box-shadow: 0 2px 4px rgba(0,0,0,0.1); font-weight: bold;">
                                                                            <i
                                                                                class="uil uil-key-skeleton me-1"></i><?php echo e($rootKeyName); ?>

                                                                        </span>

                                                                        <span class="text-muted fw-bold">:</span>

                                                                        
                                                                        <?php $__currentLoopData = $values; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $valueIndex => $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($valueIndex > 0): ?>
                                                                                <span class="text-muted fw-bold">:</span>
                                                                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                                                                            
                                                                            <span class="badge badge-round badge-lg"
                                                                                style="background: linear-gradient(135deg,
                                                                                         <?php echo e($valueIndex % 3 === 0 ? '#17a2b8' : ($valueIndex % 3 === 1 ? '#28a745' : '#fd7e14')); ?> 0%,
                                                                                         <?php echo e($valueIndex % 3 === 0 ? '#138496' : ($valueIndex % 3 === 1 ? '#218838' : '#e8590c')); ?> 100%);
                                                                                         color: white; padding: 6px 10px; border-radius: 15px; font-size: 12px;
                                                                                         box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
                                                                                <i
                                                                                    class="uil uil-tag me-1"></i><?php echo e($value); ?>

                                                                            </span>
                                                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                                                    </div>
                                                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                                            </div>
                                                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                                    </div>

                                                    
                                                    <div class="mt-3">
                                                        <div class="row">
                                                            
                                                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($variant->price): ?>
                                                                <div class="col-md-4 mb-2">
                                                                    <div class="p-3 border rounded"
                                                                        style="background: #f8f9fa;">
                                                                        <small
                                                                            class="text-muted d-block mb-1"><?php echo e(__('catalogmanagement::product.price')); ?></small>
                                                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($variant->has_discount && $variant->price_before_discount): ?>
                                                                            <div
                                                                                class="fw-bold text-danger text-decoration-line-through mb-1">
                                                                                <i
                                                                                    class="uil uil-money-bill me-1"></i><?php echo e(number_format($variant->price_before_discount, 2)); ?> <?php echo e(currency()); ?>

                                                                            </div>
                                                                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                                                        <div class="fw-bold text-success">
                                                                            <i
                                                                                class="uil uil-money-bill me-1"></i><?php echo e(number_format($variant->price, 2)); ?> <?php echo e(currency()); ?>

                                                                        </div>
                                                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($variant->has_discount && $variant->discount_end_date): ?>
                                                                            <small class="text-muted d-block mt-2">
                                                                                <i
                                                                                    class="uil uil-calendar-alt me-1"></i><?php echo e(__('catalogmanagement::product.discount_until') ?? 'Discount until'); ?>:
                                                                                <strong><?php echo e($variant->discount_end_date->format('Y-m-d')); ?></strong>
                                                                            </small>
                                                                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                                                    </div>
                                                                </div>
                                                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                                        </div>
                                                    </div>
                                                </div>
                                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($variant->stocks && $variant->stocks->count() > 0): ?>
                                                    <div class="mt-3">
                                                        <?php
                                                            $variantTotalStock = $variant->stocks->sum('quantity');
                                                            $variantBookedStock = \Modules\CatalogManagement\app\Models\StockBooking::where('vendor_product_variant_id', $variant->id)
                                                                ->where('status', 'booked')
                                                                ->sum('booked_quantity');
                                                            $variantAllocatedStock = \Modules\CatalogManagement\app\Models\StockBooking::where('vendor_product_variant_id', $variant->id)
                                                                ->where('status', 'allocated')
                                                                ->sum('booked_quantity');
                                                            $variantFulfilledStock = \Modules\CatalogManagement\app\Models\StockBooking::where('vendor_product_variant_id', $variant->id)
                                                                ->where('status', 'fulfilled')
                                                                ->sum('booked_quantity');
                                                            $variantRemainingStock = max(0, $variantTotalStock - $variantBookedStock - $variantAllocatedStock - $variantFulfilledStock);
                                                        ?>
                                                        <div class="d-flex align-items-center gap-3 mb-3 flex-wrap">
                                                            <h6 class="fw-600 mb-0">
                                                                <?php echo e(__('catalogmanagement::product.stock_summary') ?? 'Stock Summary'); ?>:
                                                            </h6>
                                                            <span class="badge badge-round badge-lg badge-success">
                                                                <i class="uil uil-package me-1"></i><?php echo e(__('catalogmanagement::product.total') ?? 'Total'); ?>:
                                                                <?php echo e($variantTotalStock); ?>

                                                            </span>
                                                            <span class="badge badge-round badge-lg badge-warning">
                                                                <i class="uil uil-lock me-1"></i><?php echo e(__('common.booked') ?? 'Booked'); ?>:
                                                                <?php echo e($variantBookedStock); ?>

                                                            </span>
                                                            <span class="badge badge-round badge-lg badge-info">
                                                                <i class="uil uil-tag me-1"></i><?php echo e(__('common.allocated') ?? 'Allocated'); ?>:
                                                                <?php echo e($variantAllocatedStock); ?>

                                                            </span>
                                                            <span class="badge badge-round badge-lg badge-primary">
                                                                <i class="uil uil-check-circle me-1"></i><?php echo e(__('common.fulfilled') ?? 'Delivered'); ?>:
                                                                <?php echo e($variantFulfilledStock); ?>

                                                            </span>
                                                            <span class="badge badge-round badge-lg badge-secondary">
                                                                <i class="uil uil-box me-1"></i><?php echo e(__('catalogmanagement::product.remaining') ?? 'Remaining'); ?>:
                                                                <?php echo e($variantRemainingStock); ?>

                                                            </span>
                                                        </div>
                                                        <h6 class="fw-600 mb-3">
                                                            <?php echo e(__('catalogmanagement::product.stock_per_region') ?? 'Stock per Region'); ?>:
                                                        </h6>
                                                        <div class="table-responsive">
                                                            <table class="table table-bordered table-hover">
                                                                <thead class="table-light">
                                                                    <tr>
                                                                        <th><i class="uil uil-location-point me-1"></i><?php echo e(__('catalogmanagement::product.region') ?? 'Region'); ?></th>
                                                                        <th class="text-center"><i class="uil uil-package me-1"></i><?php echo e(__('catalogmanagement::product.total_stock') ?? 'Total Stock'); ?></th>
                                                                        <th class="text-center"><i class="uil uil-lock me-1"></i><?php echo e(__('common.booked') ?? 'Booked'); ?></th>
                                                                        <th class="text-center"><i class="uil uil-tag me-1"></i><?php echo e(__('common.allocated') ?? 'Allocated'); ?></th>
                                                                        <th class="text-center"><i class="uil uil-check-circle me-1"></i><?php echo e(__('common.fulfilled') ?? 'Delivered'); ?></th>
                                                                        <th class="text-center"><i class="uil uil-box me-1"></i><?php echo e(__('catalogmanagement::product.remaining') ?? 'Remaining'); ?></th>
                                                                    </tr>
                                                                </thead>
                                                                <tbody>
                                                                    <?php
                                                                        $totalStock = 0;
                                                                        $totalBooked = 0;
                                                                        $totalAllocated = 0;
                                                                        $totalFulfilled = 0;
                                                                        $totalRemaining = 0;
                                                                    ?>
                                                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $variant->stocks; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $stock): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                                                        <?php
                                                                            $bookedInRegion = \Modules\CatalogManagement\app\Models\StockBooking::where('vendor_product_variant_id', $variant->id)
                                                                                ->where('region_id', $stock->region_id)
                                                                                ->where('status', 'booked')
                                                                                ->sum('booked_quantity');
                                                                            $allocatedInRegion = \Modules\CatalogManagement\app\Models\StockBooking::where('vendor_product_variant_id', $variant->id)
                                                                                ->where('allocated_region_id', $stock->region_id)
                                                                                ->where('status', 'allocated')
                                                                                ->sum('booked_quantity');
                                                                            $fulfilledInRegion = \Modules\CatalogManagement\app\Models\StockBooking::where('vendor_product_variant_id', $variant->id)
                                                                                ->where('allocated_region_id', $stock->region_id)
                                                                                ->where('status', 'fulfilled')
                                                                                ->sum('booked_quantity');
                                                                            $remainingInRegion = max(0, $stock->quantity - $bookedInRegion - $allocatedInRegion - $fulfilledInRegion);
                                                                            
                                                                            // Accumulate totals
                                                                            $totalStock += $stock->quantity ?? 0;
                                                                            $totalBooked += $bookedInRegion;
                                                                            $totalAllocated += $allocatedInRegion;
                                                                            $totalFulfilled += $fulfilledInRegion;
                                                                            $totalRemaining += $remainingInRegion;
                                                                        ?>
                                                                        <tr>
                                                                            <td>
                                                                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($stock->region): ?>
                                                                                    <?php echo e($stock->region->getTranslation('name', app()->getLocale()) ?? ($stock->region->getTranslation('name', 'en') ?? ($stock->region->getTranslation('name', 'ar') ?? ($stock->region->name ?? '-')))); ?>

                                                                                <?php else: ?>
                                                                                    <?php echo e(__('catalogmanagement::product.default_region') ?? 'Default Region'); ?>

                                                                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                                                            </td>
                                                                            <td class="text-center">
                                                                                <span class="badge badge-round badge-primary badge-lg"><?php echo e($stock->quantity ?? 0); ?></span>
                                                                            </td>
                                                                            <td class="text-center">
                                                                                <span class="badge badge-round badge-warning badge-lg"><?php echo e($bookedInRegion); ?></span>
                                                                            </td>
                                                                            <td class="text-center">
                                                                                <span class="badge badge-round badge-info badge-lg"><?php echo e($allocatedInRegion); ?></span>
                                                                            </td>
                                                                            <td class="text-center">
                                                                                <span class="badge badge-round badge-success badge-lg"><?php echo e($fulfilledInRegion); ?></span>
                                                                            </td>
                                                                            <td class="text-center">
                                                                                <span class="badge badge-round badge-secondary badge-lg"><?php echo e($remainingInRegion); ?></span>
                                                                            </td>
                                                                        </tr>
                                                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                                                        <tr>
                                                                            <td colspan="6" class="text-center text-muted">
                                                                                <?php echo e(__('catalogmanagement::product.no_regional_stock_data') ?? 'No regional stock data available.'); ?>

                                                                            </td>
                                                                        </tr>
                                                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                                                </tbody>
                                                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($variant->stocks->count() > 0): ?>
                                                                <tfoot class="table-light">
                                                                    <tr>
                                                                        <th class="fw-bold"><?php echo e(__('common.total') ?? 'Total'); ?></th>
                                                                        <th class="text-center">
                                                                            <span class="badge badge-round badge-primary badge-lg"><?php echo e($totalStock); ?></span>
                                                                        </th>
                                                                        <th class="text-center">
                                                                            <span class="badge badge-round badge-warning badge-lg"><?php echo e($totalBooked); ?></span>
                                                                        </th>
                                                                        <th class="text-center">
                                                                            <span class="badge badge-round badge-info badge-lg"><?php echo e($totalAllocated); ?></span>
                                                                        </th>
                                                                        <th class="text-center">
                                                                            <span class="badge badge-round badge-success badge-lg"><?php echo e($totalFulfilled); ?></span>
                                                                        </th>
                                                                        <th class="text-center">
                                                                            <span class="badge badge-round badge-secondary badge-lg"><?php echo e($totalRemaining); ?></span>
                                                                        </th>
                                                                    </tr>
                                                                </tfoot>
                                                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                                            </table>
                                                        </div>

                                                        
                                                        <?php
                                                            $allBookings = \Modules\CatalogManagement\app\Models\StockBooking::with(['order', 'orderProduct', 'region', 'allocatedRegion'])
                                                                ->where('vendor_product_variant_id', $variant->id)
                                                                ->latest()
                                                                ->get();
                                                        ?>
                                                        <div class="mt-4">
                                                            <h6 class="fw-600 mb-3">
                                                                <i class="uil uil-clipboard-notes me-1"></i>
                                                                <?php echo e(__('catalogmanagement::product.stock_bookings') ?? 'Stock Bookings'); ?>

                                                                <span class="badge badge-round badge-primary ms-2"><?php echo e($allBookings->count()); ?></span>
                                                            </h6>
                                                            
                                                            <div class="mb-3 d-flex flex-wrap gap-2">
                                                                <span class="badge badge-round badge-success">
                                                                    <i class="uil uil-check-circle me-1"></i><?php echo e(__('common.fulfilled')); ?>: <?php echo e($allBookings->where('status', 'fulfilled')->count()); ?>

                                                                </span>
                                                                <span class="badge badge-round badge-info">
                                                                    <i class="uil uil-tag me-1"></i><?php echo e(__('common.allocated')); ?>: <?php echo e($allBookings->where('status', 'allocated')->count()); ?>

                                                                </span>
                                                                <span class="badge badge-round badge-warning">
                                                                    <i class="uil uil-clock me-1"></i><?php echo e(__('common.booked')); ?>: <?php echo e($allBookings->where('status', 'booked')->count()); ?>

                                                                </span>
                                                                <span class="badge badge-round badge-danger">
                                                                    <i class="uil uil-times-circle me-1"></i><?php echo e(__('common.released')); ?>: <?php echo e($allBookings->where('status', 'released')->count()); ?>

                                                                </span>
                                                            </div>
                                                            <div class="table-responsive">
                                                                <table class="table table-bordered table-hover table-sm stock-bookings-table">
                                                                    <thead class="table-light">
                                                                        <tr>
                                                                            <th><?php echo e(__('order::order.order_id') ?? 'Order ID'); ?></th>
                                                                            <th><?php echo e(__('catalogmanagement::product.booked_region') ?? 'Booked Region'); ?></th>
                                                                            <th><?php echo e(__('catalogmanagement::product.allocated_region') ?? 'Allocated Region'); ?></th>
                                                                            <th class="text-center"><?php echo e(__('common.quantity') ?? 'Quantity'); ?></th>
                                                                            <th class="text-center"><?php echo e(__('common.status') ?? 'Status'); ?></th>
                                                                            <th><?php echo e(__('common.date') ?? 'Date'); ?></th>
                                                                        </tr>
                                                                    </thead>
                                                                    <tbody>
                                                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $allBookings; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $booking): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                                                            <tr class="<?php if($booking->status === 'fulfilled'): ?> table-success <?php elseif($booking->status === 'allocated'): ?> table-info <?php elseif($booking->status === 'booked'): ?> table-warning <?php else: ?> table-danger <?php endif; ?>">
                                                                                <td>
                                                                                    <a href="<?php echo e(route('admin.orders.show', $booking->order_id)); ?>" class="text-primary fw-500">
                                                                                        #<?php echo e($booking->order->order_number ?? $booking->order_id); ?>

                                                                                    </a>
                                                                                </td>
                                                                                <td>
                                                                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($booking->region): ?>
                                                                                        <?php echo e($booking->region->getTranslation('name', app()->getLocale()) ?? $booking->region->name); ?>

                                                                                    <?php else: ?>
                                                                                        -
                                                                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                                                                </td>
                                                                                <td>
                                                                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($booking->allocatedRegion): ?>
                                                                                        <span class="fw-500">
                                                                                            <?php echo e($booking->allocatedRegion->getTranslation('name', app()->getLocale()) ?? $booking->allocatedRegion->name); ?>

                                                                                        </span>
                                                                                    <?php else: ?>
                                                                                        <span class="text-muted">-</span>
                                                                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                                                                </td>
                                                                                <td class="text-center">
                                                                                    <span class="badge badge-round <?php if($booking->status === 'fulfilled'): ?> badge-success <?php elseif($booking->status === 'allocated'): ?> badge-info <?php elseif($booking->status === 'booked'): ?> badge-warning <?php else: ?> badge-danger <?php endif; ?>">
                                                                                        <?php echo e($booking->booked_quantity); ?>

                                                                                    </span>
                                                                                </td>
                                                                                <td class="text-center">
                                                                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($booking->status === 'fulfilled'): ?>
                                                                                        <span class="badge badge-round badge-success">
                                                                                            <i class="uil uil-check-circle me-1"></i><?php echo e(__('common.fulfilled')); ?>

                                                                                        </span>
                                                                                    <?php elseif($booking->status === 'allocated'): ?>
                                                                                        <span class="badge badge-round badge-info">
                                                                                            <i class="uil uil-tag me-1"></i><?php echo e(__('common.allocated')); ?>

                                                                                        </span>
                                                                                    <?php elseif($booking->status === 'booked'): ?>
                                                                                        <span class="badge badge-round badge-warning">
                                                                                            <i class="uil uil-clock me-1"></i><?php echo e(__('common.booked')); ?>

                                                                                        </span>
                                                                                    <?php else: ?>
                                                                                        <span class="badge badge-round badge-danger">
                                                                                            <i class="uil uil-times-circle me-1"></i><?php echo e(__('common.released')); ?>

                                                                                        </span>
                                                                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                                                                </td>
                                                                                <td>
                                                                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($booking->status === 'fulfilled'): ?>
                                                                                        <?php echo e($booking->fulfilled_at ? $booking->fulfilled_at : '-'); ?>

                                                                                    <?php elseif($booking->status === 'allocated'): ?>
                                                                                        <?php echo e($booking->allocated_at ? $booking->allocated_at : '-'); ?>

                                                                                    <?php elseif($booking->status === 'booked'): ?>
                                                                                        <?php echo e($booking->booked_at ? $booking->booked_at : '-'); ?>

                                                                                    <?php else: ?>
                                                                                        <?php echo e($booking->released_at ? $booking->released_at : '-'); ?>

                                                                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                                                                </td>
                                                                            </tr>
                                                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                                                            <tr>
                                                                                <td colspan="6" class="text-center text-muted py-3">
                                                                                    <?php echo e(__('catalogmanagement::product.no_stock_bookings') ?? 'No stock bookings yet.'); ?>

                                                                                </td>
                                                                            </tr>
                                                                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                                                    </tbody>
                                                                </table>
                                                            </div>
                                                        </div>
                                                    </div>
                                                <?php else: ?>
                                                    <p class="text-muted fs-14 mt-3">
                                                        <?php echo e(__('catalogmanagement::product.no_stock_data') ?? 'No stock data available.'); ?>

                                                    </p>
                                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                            </div>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    
    <?php if (isset($component)) { $__componentOriginal428f5f1760e699cb50a829dfa3984f87 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal428f5f1760e699cb50a829dfa3984f87 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.image-modal','data' => []] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('image-modal'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal428f5f1760e699cb50a829dfa3984f87)): ?>
<?php $attributes = $__attributesOriginal428f5f1760e699cb50a829dfa3984f87; ?>
<?php unset($__attributesOriginal428f5f1760e699cb50a829dfa3984f87); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal428f5f1760e699cb50a829dfa3984f87)): ?>
<?php $component = $__componentOriginal428f5f1760e699cb50a829dfa3984f87; ?>
<?php unset($__componentOriginal428f5f1760e699cb50a829dfa3984f87); ?>
<?php endif; ?>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
    <script>
        /**
         * Open image modal for additional images carousel
         */
        function openImageModal(index) {
            const modalId = 'imageModal' + index;
            const modalElement = document.getElementById(modalId);

            if (modalElement) {
                const modal = new bootstrap.Modal(modalElement);
                modal.show();
            }
        }

        /**
         * Initialize DataTables for Stock tables
         */
        $(document).ready(function() {
            // Initialize Stock Bookings tables
            $('.stock-bookings-table').each(function() {
                // Only initialize if table has thead and tbody with proper structure
                // Skip tables with colspan cells (empty state rows)
                var $table = $(this);
                var $tbody = $table.find('tbody');
                var $rows = $tbody.find('tr');
                var headerCount = $table.find('thead tr th').length;
                
                // Check if table has proper structure:
                // 1. Has header columns
                // 2. Has at least one row
                // 3. First row has same number of cells as headers (no colspan)
                var hasProperStructure = headerCount > 0 && 
                                         $rows.length > 0 &&
                                         $rows.first().find('td').length === headerCount &&
                                         $rows.first().find('td[colspan]').length === 0;
                
                if (hasProperStructure && !$.fn.DataTable.isDataTable(this)) {
                    $(this).DataTable({
                        paging: true,
                        pageLength: 10,
                        lengthMenu: [[5, 10, 25, 50, -1], [5, 10, 25, 50, "<?php echo e(__('common.all')); ?>"]],
                        searching: true,
                        ordering: true,
                        order: [[0, 'desc']],
                        info: true,
                        autoWidth: false,
                        responsive: true,
                        language: {
                            search: "<?php echo e(__('common.search')); ?>:",
                            lengthMenu: "<?php echo e(__('common.show')); ?> _MENU_ <?php echo e(__('common.entries')); ?>",
                            info: "<?php echo e(__('common.showing')); ?> _START_ <?php echo e(__('common.to')); ?> _END_ <?php echo e(__('common.of')); ?> _TOTAL_ <?php echo e(__('common.entries')); ?>",
                            infoEmpty: "<?php echo e(__('common.showing')); ?> 0 <?php echo e(__('common.to')); ?> 0 <?php echo e(__('common.of')); ?> 0 <?php echo e(__('common.entries')); ?>",
                            infoFiltered: "(<?php echo e(__('common.filtered_from')); ?> _MAX_ <?php echo e(__('common.total_entries')); ?>)",
                            zeroRecords: "<?php echo e(__('common.no_matching_records_found')); ?>",
                            paginate: {
                                first: "<?php echo e(__('common.first')); ?>",
                                last: "<?php echo e(__('common.last')); ?>",
                                next: "<?php echo e(__('common.next')); ?>",
                                previous: "<?php echo e(__('common.previous')); ?>"
                            }
                        }
                    });
                }
            });
        });
    </script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layout.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\eramo-multi-vendor\Modules/CatalogManagement\resources/views/product/show.blade.php ENDPATH**/ ?>