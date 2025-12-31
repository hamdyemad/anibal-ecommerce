<div class="col-xxl-12 mb-25">
    <div class="userDatatable global-shadow border-light-0 p-30 bg-white radius-xl w-100 mb-30">
        <div class="d-flex justify-content-between align-items-center mb-25">
            <h4 class="mb-0 fw-500"><?php echo e(trans('dashboard.recent_activities')); ?></h4>
        </div>
        <div class="table-responsive">
            <table class="table mb-0 table-bordered table-hover" style="width:100%">
                <thead>
                    <tr class="userDatatable-header">
                        <th><span class="userDatatable-title">#</span></th>
                        <th><span class="userDatatable-title"><?php echo e(trans('dashboard.employee')); ?></span></th>
                        <th><span class="userDatatable-title"><?php echo e(trans('dashboard.activity')); ?></span></th>
                        <th><span class="userDatatable-title"><?php echo e(trans('dashboard.activity_date')); ?></span></th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $recentActivities ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $activity): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <tr>
                        <td><?php echo e($index + 1); ?></td>
                        <td>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($activity->user): ?>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($activity->user->image): ?>
                                    <img class="rounded-circle" src="<?php echo e(asset('storage/' . $activity->user->image)); ?>" alt="employee" style="width: 40px; height: 40px;">
                                <?php else: ?>
                                    <div class="bg-light rounded-circle d-inline-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                        <i class="uil uil-user text-muted"></i>
                                    </div>
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                <span class="ms-3"><?php echo e($activity->user->getTranslation('name', app()->getLocale()) ?? $activity->user->email); ?></span>
                            <?php else: ?>
                                <span class="text-muted"><?php echo e(trans('common.system')); ?></span>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </td>
                        <td>
                            <?php
                                $actionClass = match($activity->action) {
                                    'created' => 'text-success',
                                    'updated' => 'text-primary',
                                    'deleted' => 'text-danger',
                                    'restored' => 'text-info',
                                    'login' => 'text-info',
                                    'logout' => 'text-secondary',
                                    default => 'text-secondary'
                                };
                                
                                // Get translated description, fallback to action + model if translation key not found
                                $description = $activity->translated_description;
                                if (str_contains($description, 'activity_log.')) {
                                    // Translation key not found, build a simple description
                                    $actionText = __("activity_log.actions.{$activity->action}");
                                    $modelName = class_basename($activity->model ?? 'Unknown');
                                    $translatedModel = __("activity_log.models.{$modelName}");
                                    if (str_contains($translatedModel, 'activity_log.models.')) {
                                        $translatedModel = $modelName;
                                    }
                                    $description = "{$actionText} {$translatedModel}: {$activity->model_id}";
                                }
                            ?>
                            <span class="<?php echo e($actionClass); ?> fw-medium"><?php echo e($description); ?></span>
                        </td>
                        <td><?php echo e($activity->created_at); ?></td>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <tr>
                        <td colspan="4" class="text-center text-muted"><?php echo e(trans('common.no_data_available')); ?></td>
                    </tr>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php /**PATH C:\laragon\www\eramo-multi-vendor\resources\views/pages/dashboard/recent-activities.blade.php ENDPATH**/ ?>