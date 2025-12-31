
<div class="tree-node tree-level-<?php echo e($level); ?> tree-key-node">
    <div class="tree-node-content <?php echo e(($variantKey->variants && $variantKey->variants->count() > 0) || ($variantKey->childrenKeys && $variantKey->childrenKeys->count() > 0) ? 'has-children' : ''); ?>">
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(($variantKey->variants && $variantKey->variants->count() > 0) || ($variantKey->childrenKeys && $variantKey->childrenKeys->count() > 0)): ?>
            <div class="tree-toggle">
                <i class="uil uil-angle-down"></i>
            </div>
        <?php else: ?>
            <div class="tree-toggle" style="background: transparent; border: 1px dashed #dee2e6;">
                <i class="uil uil-minus" style="color: #adb5bd;"></i>
            </div>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        
        <div class="tree-info">
            <span class="tree-key-icon">
                <i class="uil uil-key-skeleton-alt"></i>
            </span>
            
            <div class="tree-names">
                <span class="tree-name-item" title="English">
                    <?php echo e($variantKey->getTranslation('name', 'en')); ?>

                </span>
                <span class="tree-name-item" dir="rtl" title="Arabic">
                    <?php echo e($variantKey->getTranslation('name', 'ar')); ?>

                </span>
            </div>

            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($variantKey->variants && $variantKey->variants->count() > 0): ?>
                <span class="children-count" title="<?php echo e(trans('catalogmanagement::variantsconfig.variants_count')); ?>">
                    <?php echo e($variantKey->variants->count()); ?> <?php echo e(trans('catalogmanagement::variantsconfig.variants')); ?>

                </span>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </div>

        <div class="tree-actions">
            <a href="<?php echo e(route('admin.variant-keys.show', $variantKey->id)); ?>" 
               class="view" 
               title="<?php echo e(trans('common.view')); ?>">
                <i class="uil uil-eye"></i>
            </a>
        </div>
    </div>

    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(($variantKey->variants && $variantKey->variants->count() > 0) || ($variantKey->childrenKeys && $variantKey->childrenKeys->count() > 0)): ?>
        <div class="tree-children expanded">
            
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($variantKey->variants && $variantKey->variants->count() > 0): ?>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $variantKey->variants; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $variant): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div class="tree-node tree-level-<?php echo e($level + 1); ?> tree-variant-node">
                        <div class="tree-node-content <?php echo e($variant->children && $variant->children->count() > 0 ? 'has-children' : ''); ?>">
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($variant->children && $variant->children->count() > 0): ?>
                                <div class="tree-toggle">
                                    <i class="uil uil-angle-down"></i>
                                </div>
                            <?php else: ?>
                                <div class="tree-toggle" style="background: transparent; border: 1px dashed #dee2e6;">
                                    <i class="uil uil-circle" style="color: #adb5bd; font-size: 8px;"></i>
                                </div>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            
                            <div class="tree-info">
                                <span class="tree-variant-icon">
                                    <i class="uil uil-cube"></i>
                                </span>
                                <div class="tree-names">
                                    <span class="tree-name-item" title="English">
                                        <?php echo e($variant->getTranslation('name', 'en')); ?>

                                    </span>
                                    <span class="tree-name-item" dir="rtl" title="Arabic">
                                        <?php echo e($variant->getTranslation('name', 'ar')); ?>

                                    </span>
                                </div>

                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($variant->type): ?>
                                    <span class="tree-type-badge" title="<?php echo e(trans('catalogmanagement::variantsconfig.type')); ?>">
                                        <i class="uil uil-<?php echo e($variant->type == 'color' ? 'palette' : 'text'); ?>"></i> 
                                        <?php echo e(trans('catalogmanagement::variantsconfig.' . $variant->type)); ?>

                                    </span>
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($variant->value): ?>
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($variant->type == 'color'): ?>
                                        <span class="tree-color-value" title="<?php echo e($variant->value); ?>">
                                            <span class="color-preview" style="background-color: <?php echo e($variant->value); ?>;"></span>
                                            <?php echo e($variant->value); ?>

                                        </span>
                                    <?php else: ?>
                                        <span class="tree-text-value" title="<?php echo e(trans('catalogmanagement::variantsconfig.value')); ?>">
                                            <?php echo e($variant->value); ?>

                                        </span>
                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </div>

                            <div class="tree-actions">
                                <a href="<?php echo e(route('admin.variants-configurations.show', $variant->id)); ?>" 
                                   class="view" 
                                   title="<?php echo e(trans('common.view')); ?>">
                                    <i class="uil uil-eye"></i>
                                </a>
                            </div>
                        </div>

                        
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($variant->children && $variant->children->count() > 0): ?>
                            <div class="tree-children expanded">
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $variant->children; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $childVariant): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <?php echo $__env->make('catalogmanagement::variants-config.partials.variant-child-node', ['variant' => $childVariant, 'level' => $level + 2], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </div>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

            
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($variantKey->childrenKeys && $variantKey->childrenKeys->count() > 0): ?>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $variantKey->childrenKeys; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $childKey): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <?php echo $__env->make('catalogmanagement::variants-config.partials.tree-node', ['variantKey' => $childKey, 'level' => $level + 1], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
</div>
<?php /**PATH C:\laragon\www\eramo-multi-vendor\Modules/CatalogManagement\resources/views/variants-config/partials/tree-node.blade.php ENDPATH**/ ?>