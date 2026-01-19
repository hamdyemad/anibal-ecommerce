<?php
    // Get admin notifications from database (not viewed by current user)
    $adminNotificationsQuery = \App\Models\AdminNotification::notViewedBy(auth()->id())->orderBy('created_at', 'desc');
    
    // Filter by vendor if not admin
    if (isAdmin()) {
        // Admin sees all notifications without vendor_id
        $adminNotificationsQuery->whereNull('vendor_id');
    } else {
        // Vendors see their own notifications, but exclude admin-only types
        $vendorId = auth()->user()->vendor->id;
        $adminNotificationsQuery->where(function($q) use ($vendorId) {
            $q->where('vendor_id', $vendorId)
              ->orWhereNull('vendor_id');
        })->whereNotIn('type', ['vendor_request', 'new_message']);
    }
    
    $notifications = $adminNotificationsQuery->limit(20)
        ->get()
        ->map(function($notification) {
            return [
                'id' => $notification->id,
                'type' => $notification->type,
                'icon' => $notification->icon,
                'color' => $notification->color,
                'title' => $notification->getTranslatedTitle(),
                'description' => $notification->getTranslatedDescription(),
                'url' => $notification->url ?? '#',
                'created_at' => $notification->getRawOriginal('created_at'),
                'source' => 'admin_notifications',
            ];
        });
    
    $notificationsCount = $notifications->count();
?>

<li class="nav-notification">
    <div class="dropdown-custom" style="position: relative;">
        <a href="javascript:;" class="nav-item-toggle icon-active">
            <img class="svg" src="<?php echo e(asset('assets/img/svg/alarm.svg')); ?>" alt="img">
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($notificationsCount > 0): ?>
                <span class="nav-item__badge" style="position: absolute; top: -8px; background-color: #fa8b0c; color: white; border-radius: 50%; width: 18px; height: 18px; display: flex; align-items: center; justify-content: center; font-size: 10px; font-weight: 600; line-height: 1; z-index: 10;"><?php echo e($notificationsCount); ?></span>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </a>
        <div class="dropdown-wrapper">
            <h2 class="dropdown-wrapper__title"><?php echo e(trans('menu.notifications.title')); ?> <span class="badge-circle badge-warning ms-1"><?php echo e($notificationsCount); ?></span></h2>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($notificationsCount > 0): ?>
                <ul>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $notifications; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $notification): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <li class="nav-notification__single nav-notification__single--unread d-flex flex-wrap">
                            <div class="nav-notification__type nav-notification__type--<?php echo e($notification['color']); ?>">
                                <i class="<?php echo e($notification['icon']); ?>"></i>
                            </div>
                            <div class="nav-notification__details">
                                <p>
                                    <a href="<?php echo e(route('admin.notifications.show', $notification['id'])); ?>" class="subject stretched-link text-truncate" style="max-width: 180px;"><?php echo e($notification['title']); ?></a>
                                    <span><?php echo e($notification['description']); ?></span>
                                </p>
                                <p>
                                    <span class="time-posted"><?php echo e($notification['created_at']); ?></span>
                                </p>
                            </div>
                        </li>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </ul>
            <?php else: ?>
                <div class="text-center py-4">
                    <p class="text-muted"><?php echo e(trans('menu.no_notifications')); ?></p>
                </div>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            <a href="<?php echo e(route('admin.dashboard')); ?>" class="dropdown-wrapper__more"><?php echo e(trans('menu.see_all_notifications')); ?></a>
        </div>
    </div>
</li>
<?php /**PATH C:\laragon\www\eramo-multi-vendor\resources\views/partials/top_nav/_notifications.blade.php ENDPATH**/ ?>