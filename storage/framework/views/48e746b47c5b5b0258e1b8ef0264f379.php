

<?php $__env->startSection('styles'); ?>
    <?php echo app('Illuminate\Foundation\Vite')(['Modules/CatalogManagement/resources/css/tree-view.css']); ?>
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
                ['title' => trans('catalogmanagement::variantsconfig.tree_view')]
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
                ['title' => trans('catalogmanagement::variantsconfig.tree_view')]
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
            <div class="userDatatable global-shadow border-light-0 p-30 bg-white radius-xl w-100 mb-30">
                <div class="d-flex justify-content-between align-items-center mb-25">
                    <h4 class="mb-0 fw-500"><?php echo e(trans('catalogmanagement::variantsconfig.variants_configurations_tree')); ?></h4>
                    <div class="d-flex gap-2">
                        <a href="<?php echo e(route('admin.variants-configurations.index')); ?>" class="btn btn-light btn-default btn-squared text-capitalize">
                            <i class="uil uil-list-ul"></i> <?php echo e(trans('common.list_view')); ?>

                        </a>
                        <a href="<?php echo e(route('admin.variants-configurations.create')); ?>" class="btn btn-primary btn-default btn-squared text-capitalize">
                            <i class="uil uil-plus"></i> <?php echo e(trans('catalogmanagement::variantsconfig.add_variants_config')); ?>

                        </a>
                    </div>
                </div>

                
                <div class="variant-configs-tree">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($treeData && $treeData->count() > 0): ?>
                        <div class="tree-container">
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $treeData; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $rootKey): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <?php echo $__env->make('catalogmanagement::variants-config.partials.tree-node', ['variantKey' => $rootKey, 'level' => 0], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </div>
                    <?php else: ?>
                        <div class="text-center py-5">
                            <i class="uil uil-folder-open" style="font-size: 64px; color: #ccc;"></i>
                            <p class="text-muted mt-3"><?php echo e(trans('catalogmanagement::variantsconfig.no_variant_keys_found')); ?></p>
                        </div>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>


<?php if (isset($component)) { $__componentOriginal4d4be0bcf29da35c820833c3b98d2b58 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal4d4be0bcf29da35c820833c3b98d2b58 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.delete-with-loading','data' => ['modalId' => 'modal-delete-variants-config','tableId' => '','deleteButtonClass' => 'delete-variants-config','title' => __('main.confirm delete'),'message' => __('main.are you sure you want to delete this'),'itemNameId' => 'delete-variants-config-name','confirmBtnId' => 'confirmDeleteBtn','cancelText' => __('main.cancel'),'deleteText' => __('main.delete'),'loadingDeleting' => trans('main.deleting') ?? 'Deleting...','loadingPleaseWait' => trans('main.please wait') ?? 'Please wait...','loadingDeletedSuccessfully' => trans('main.deleted success') ?? 'Deleted Successfully!','loadingRefreshing' => trans('main.refreshing') ?? 'Refreshing...','errorDeleting' => __('main.error on delete')]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('delete-with-loading'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['modalId' => 'modal-delete-variants-config','tableId' => '','deleteButtonClass' => 'delete-variants-config','title' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(__('main.confirm delete')),'message' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(__('main.are you sure you want to delete this')),'itemNameId' => 'delete-variants-config-name','confirmBtnId' => 'confirmDeleteBtn','cancelText' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(__('main.cancel')),'deleteText' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(__('main.delete')),'loadingDeleting' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(trans('main.deleting') ?? 'Deleting...'),'loadingPleaseWait' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(trans('main.please wait') ?? 'Please wait...'),'loadingDeletedSuccessfully' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(trans('main.deleted success') ?? 'Deleted Successfully!'),'loadingRefreshing' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(trans('main.refreshing') ?? 'Refreshing...'),'errorDeleting' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(__('main.error on delete'))]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal4d4be0bcf29da35c820833c3b98d2b58)): ?>
<?php $attributes = $__attributesOriginal4d4be0bcf29da35c820833c3b98d2b58; ?>
<?php unset($__attributesOriginal4d4be0bcf29da35c820833c3b98d2b58); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal4d4be0bcf29da35c820833c3b98d2b58)): ?>
<?php $component = $__componentOriginal4d4be0bcf29da35c820833c3b98d2b58; ?>
<?php unset($__componentOriginal4d4be0bcf29da35c820833c3b98d2b58); ?>
<?php endif; ?>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('after-body'); ?>
    <?php if (isset($component)) { $__componentOriginal115e82920da0ed7c897ee494af74b9d8 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal115e82920da0ed7c897ee494af74b9d8 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.loading-overlay','data' => []] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('loading-overlay'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
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

<?php $__env->startPush('scripts'); ?>
<script>
    $(document).ready(function() {
        // Toggle tree node expansion
        $('.tree-toggle').on('click', function(e) {
            e.stopPropagation();
            const $toggle = $(this);
            const $node = $toggle.closest('.tree-node');
            const $children = $node.find('> .tree-children');
            
            $toggle.toggleClass('collapsed');
            $children.toggleClass('expanded');
            
            // Update icon
            const icon = $toggle.find('i');
            if ($toggle.hasClass('collapsed')) {
                icon.removeClass('uil-angle-down').addClass('uil-angle-right');
            } else {
                icon.removeClass('uil-angle-right').addClass('uil-angle-down');
            }
        });

        // Expand/collapse on node click (optional)
        $('.tree-node-content').on('click', function(e) {
            if (!$(e.target).closest('.tree-actions').length) {
                $(this).find('.tree-toggle').trigger('click');
            }
        });
    });
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layout.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\eramo-multi-vendor\Modules/CatalogManagement\resources/views/variants-config/tree.blade.php ENDPATH**/ ?>