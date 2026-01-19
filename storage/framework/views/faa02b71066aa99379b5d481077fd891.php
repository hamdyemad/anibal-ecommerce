<?php
    $vendor = auth()->user()->vendor ?? null;
    $withdrawNotifications = [];

    try {
        if ($vendor) {
            // For vendors: show accepted/rejected withdraw notifications
            $withdrawNotifications = \App\Models\AdminNotification::notViewedBy(auth()->id())
                ->where('type', 'withdraw_status')
                ->where('vendor_id', $vendor->id)
                ->orderBy('created_at', 'desc')
                ->limit(10)
                ->get();
        } else {
            // For admin: show new withdraw requests
            $withdrawNotifications = \App\Models\AdminNotification::notViewedBy(auth()->id())
                ->where('type', 'withdraw_request')
                ->whereNull('vendor_id')
                ->orderBy('created_at', 'desc')
                ->limit(10)
                ->get();
        }
    } catch (\Exception $e) {
        // Silently fail
        $withdrawNotifications = collect([]);
    }
?>
<li class="nav-notification">
    <div class="dropdown-custom" style="position: relative;">
        <a href="javascript:;" class="nav-item-toggle icon-active">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="svg">
                <rect x="2" y="5" width="20" height="14" rx="2"></rect>
                <line x1="2" y1="10" x2="22" y2="10"></line>
            </svg>
            <span class="nav-item__badge"
                style="position: absolute; top: -8px; background-color: #fa8b0c; color: white; border-radius: 50%; width: 18px; height: 18px; display: flex; align-items: center; justify-content: center; font-size: 10px; font-weight: 600; line-height: 1; z-index: 10;"
                dir="ltr">
                <style>[dir="rtl"] .nav-item__badge { left: -8px !important; right: auto !important; } [dir="ltr"]
                .nav-item__badge { right: -8px !important; left: auto !important; }</style>
                <?php echo e($withdrawNotifications->count()); ?>

            </span>
        </a>
        <div class="dropdown-wrapper">
            <h2 class="dropdown-wrapper__title"><?php echo e(trans('menu.withdraw module.vendors_withdraw_requests')); ?> <span
                    class="badge-circle badge-warning ms-1"><?php echo e($withdrawNotifications->count()); ?></span></h2>
            <ul>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $withdrawNotifications; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $notification): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <li class="nav-notification__single d-flex flex-wrap">
                        <div class="nav-notification__type nav-notification__type--<?php echo e($notification->color); ?>">
                            <i class="<?php echo e($notification->icon); ?>"></i>
                        </div>
                        <div class="nav-notification__details">
                            <p>
                                <a href="<?php echo e(route('admin.notifications.show', $notification->id)); ?>" class="subject stretched-link text-truncate"
                                    style="max-width: 180px;"><?php echo e($notification->getTranslatedTitle()); ?></a>
                            </p>
                            <p>
                                <span class="time-posted"><?php echo e($notification->getTranslatedDescription()); ?></span>
                            </p>
                            <p>
                                <span class="time-posted text-muted"><?php echo e($notification->created_at); ?></span>
                            </p>
                        </div>
                    </li>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <li class="nav-notification__single d-flex flex-wrap">
                        <div class="nav-notification__details">
                            <p class="text-muted"><?php echo e(trans('menu.withdraw module.no_requests')); ?></p>
                        </div>
                    </li>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </ul>
            <a href="<?php echo e(route('admin.transactionsRequests', 'new')); ?>" class="dropdown-wrapper__more"><?php echo e(trans('menu.withdraw module.see_all_requests')); ?></a>
        </div>
    </div>
</li>
<?php /**PATH C:\laragon\www\eramo-multi-vendor\resources\views/partials/top_nav/_vendors_withdraw_requests.blade.php ENDPATH**/ ?>