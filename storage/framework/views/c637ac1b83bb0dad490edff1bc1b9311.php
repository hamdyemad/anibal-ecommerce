

<?php $__env->startSection('title', trans('vendor::vendor.vendor_details')); ?>
<?php $__env->startSection('styles'); ?>
    <style>
        /* Modern Glassmorphism Document Cards */
        .modern-document-card {
            position: relative;
            height: 100%;
            border-radius: 16px;
            overflow: hidden;
            transition: all 0.4s cubic-bezier(0.23, 1, 0.320, 1);
        }

        .document-glass-bg {
            position: absolute;
            inset: 0;
            background: rgba(255, 255, 255, 0.85);
            backdrop-filter: blur(15px);
            -webkit-backdrop-filter: blur(15px);
            border: 1px solid rgba(255, 255, 255, 0.95);
            border-radius: 16px;
            box-shadow: inset 0 0 30px rgba(255, 255, 255, 0.5);
        }

        .document-card-content {
            position: relative;
            z-index: 1;
            padding: 28px 20px;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: space-between;
            height: 100%;
            text-align: center;
        }

        .document-icon-wrapper {
            width: 90px;
            height: 90px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, rgba(95, 99, 242, 0.25) 0%, rgba(142, 146, 247, 0.15) 100%);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            border: 2px solid rgba(95, 99, 242, 0.4);
            border-radius: 16px;
            margin-bottom: 16px;
            box-shadow: 0 8px 25px rgba(95, 99, 242, 0.2), inset 0 0 20px rgba(255, 255, 255, 0.3);
            font-size: 48px;
            color: #5f63f2;
            transition: all 0.4s ease;
        }

        .modern-document-card:hover .document-icon-wrapper {
            transform: scale(1.15) translateY(-4px);
            background: linear-gradient(135deg, rgba(95, 99, 242, 0.35) 0%, rgba(142, 146, 247, 0.25) 100%);
            border-color: rgba(95, 99, 242, 0.6);
            box-shadow: 0 12px 35px rgba(95, 99, 242, 0.3), inset 0 0 20px rgba(255, 255, 255, 0.4);
        }

        .document-info {
            flex-grow: 1;
            display: flex;
            flex-direction: column;
            justify-content: center;
            width: 100%;
            margin-bottom: 16px;
        }

        .document-name {
            font-weight: 700;
            margin-bottom: 6px;
            word-break: break-word;
            line-height: 1.4;
            font-size: 15px;
        }

        .document-type {
            font-size: 12px;
            color: #8e92f7;
            font-weight: 500;
            margin: 0;
        }

        .document-actions {
            display: flex;
            gap: 8px;
            justify-content: center;
            width: 100%;
        }

        .action-btn {
            width: 40px;
            height: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 10px;
            border: none;
            cursor: pointer;
            transition: all 0.3s ease;
            font-size: 16px;
            background: rgba(95, 99, 242, 0.1);
            color: #5f63f2;
        }

        .action-btn:hover {
            background: rgba(95, 99, 242, 0.2);
            transform: translateY(-2px);
        }

        .download-btn {
            background: linear-gradient(135deg, rgba(95, 99, 242, 0.15) 0%, rgba(142, 146, 247, 0.1) 100%);
            color: #5f63f2;
        }

        .download-btn:hover {
            background: linear-gradient(135deg, rgba(95, 99, 242, 0.25) 0%, rgba(142, 146, 247, 0.2) 100%);
            box-shadow: 0 4px 12px rgba(95, 99, 242, 0.2);
        }

        .delete-btn {
            background: rgba(220, 53, 69, 0.1);
            color: #dc3545;
        }

        .delete-btn:hover {
            background: rgba(220, 53, 69, 0.2);
            box-shadow: 0 4px 12px rgba(220, 53, 69, 0.2);
        }

        .modern-document-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 20px 40px rgba(95, 99, 242, 0.2);
        }

        .modern-document-card:hover .document-glass-bg {
            background: rgba(255, 255, 255, 0.65);
            border-color: rgba(95, 99, 242, 0.5);
            box-shadow: inset 0 0 30px rgba(255, 255, 255, 0.4), 0 0 30px rgba(95, 99, 242, 0.15);
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .document-card-content {
                padding: 20px 16px;
            }

            .document-icon-wrapper {
                width: 60px;
                height: 60px;
                font-size: 28px;
            }

            .document-name {
                color: #545454;
                font-size: 14px;
            }

            .action-btn {
                width: 36px;
                height: 36px;
                font-size: 14px;
            }
        }

        /* Transaction Card Animations */
        .transaction-card {
            animation: slideInUp 0.6s ease-out;
            transition: all 0.3s cubic-bezier(0.23, 1, 0.320, 1);
        }

        .transaction-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 12px 30px rgba(95, 99, 242, 0.2) !important;
        }

        @keyframes slideInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Staggered Animation for Multiple Cards */
        .transaction-card:nth-child(1) {
            animation-delay: 0.1s;
        }

        .transaction-card:nth-child(2) {
            animation-delay: 0.2s;
        }

        .transaction-card:nth-child(3) {
            animation-delay: 0.3s;
        }

        /* Icon Pulse Animation on Hover */
        .transaction-card:hover i {
            animation: iconPulse 0.6s ease-out;
        }

        @keyframes iconPulse {
            0% {
                transform: scale(1);
            }

            50% {
                transform: scale(1.2);
            }

            100% {
                transform: scale(1);
            }
        }

        /* Dark Mode Support */
        @media (prefers-color-scheme: dark) {
            .document-glass-bg {
                border-color: rgba(95, 99, 242, 0.2);
            }


            .document-type {
                color: #a8acff;
            }

        }
    </style>
<?php $__env->stopSection(); ?>
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
                    ['title' => trans('vendor::vendor.vendors_management'), 'url' => route('admin.vendors.index')],
                    ['title' => trans('vendor::vendor.vendor_details')],
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
                    ['title' => trans('vendor::vendor.vendors_management'), 'url' => route('admin.vendors.index')],
                    ['title' => trans('vendor::vendor.vendor_details')],
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
                        <h5 class="mb-0 fw-500"><?php echo e(trans('vendor::vendor.vendor_details')); ?></h5>
                        <div class="d-flex gap-10">
                            <a href="<?php echo e(route('admin.vendors.index')); ?>" class="btn btn-light btn-sm">
                                <i class="uil uil-arrow-left me-2"></i><?php echo e(trans('common.back_to_list')); ?>

                            </a>
                            <a href="<?php echo e(route('admin.vendors.edit', $vendor->id)); ?>" class="btn btn-primary btn-sm">
                                <i class="uil uil-edit me-2"></i><?php echo e(trans('common.edit')); ?>

                            </a>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <?php
                                // Calculate vendor order data for 6 cards
                                $vendorOrderProducts = \Modules\Order\app\Models\OrderProduct::where('vendor_id', $vendor->id)->get();
                                $ordersPrice = $vendorOrderProducts->sum(function($op) {
                                    return $op->price * $op->quantity;
                                });
                                $bnaiaBalance = $vendorOrderProducts->sum(function($op) {
                                    return ($op->price * $op->quantity) * ($op->commission / 100);
                                });
                                $totalVendorBalance = $ordersPrice - $bnaiaBalance;
                                $totalSentMoney = $vendor->total_sent;
                            ?>
                            <div class="col-md-8 order-2 order-md-1">
                                
                                <div class="card card-holder">
                                    <div class="card-header">
                                        <h3>
                                            <i class="uil uil-dollar-alt me-1"></i><?php echo e(trans('vendor::vendor.money_transactions')); ?>

                                        </h3>
                                    </div>
                                        <div class="card-body p-20">
                                        
                                        <div class="mb-3">
                                            <h6 class="fw-bold text-muted mb-3"><?php echo e(trans('withdraw::withdraw.vendor_general_orders_data')); ?></h6>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-4 d-flex">
                                                
                                                <div class="d-flex align-items-center justify-content-between p-15 rounded mb-15 transaction-card flex-grow-1"
                                                    style="background: rgba(255, 255, 255, 0.85); backdrop-filter: blur(15px); -webkit-backdrop-filter: blur(15px); border: 1px solid rgba(255, 255, 255, 0.95); box-shadow: 0 4px 15px rgba(102, 126, 234, 0.15), inset 0 0 20px rgba(255, 255, 255, 0.4);">
                                                    <div>
                                                        <p class="mb-0 fs-13 fw-bold" style="color: #667eea;">
                                                            <?php echo e(trans('withdraw::withdraw.total_transactions')); ?></p>
                                                        <p class="mb-0 fs-20 fw-bold mt-5" style="color: #272b41;">
                                                            <?php echo e(number_format($ordersPrice, 2)); ?> <?php echo e(currency()); ?></p>
                                                    </div>
                                                    <div class="p-12 rounded-circle d-flex align-items-center justify-content-center"
                                                        style="background: linear-gradient(135deg, rgba(102, 126, 234, 0.15) 0%, rgba(118, 75, 162, 0.1) 100%); width: 45px; height: 45px; border: 1px solid rgba(102, 126, 234, 0.2);">
                                                        <i class="uil uil-wallet fs-20" style="color: #667eea;"></i>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-4 d-flex">
                                                
                                                <div class="d-flex align-items-center justify-content-between p-15 rounded mb-15 transaction-card flex-grow-1"
                                                    style="background: rgba(255, 255, 255, 0.85); backdrop-filter: blur(15px); -webkit-backdrop-filter: blur(15px); border: 1px solid rgba(255, 255, 255, 0.95); box-shadow: 0 4px 15px rgba(255, 159, 67, 0.15), inset 0 0 20px rgba(255, 255, 255, 0.4);">
                                                    <div>
                                                        <p class="mb-0 fs-13 fw-bold" style="color: #ff9f43;">
                                                            <?php echo e(trans('withdraw::withdraw.bnaia_commission_from_transactions')); ?></p>
                                                        <p class="mb-0 fs-20 fw-bold mt-5" style="color: #272b41;">
                                                            <?php echo e(number_format($bnaiaBalance, 2)); ?> <?php echo e(currency()); ?></p>
                                                    </div>
                                                    <div class="p-12 rounded-circle d-flex align-items-center justify-content-center"
                                                        style="background: linear-gradient(135deg, rgba(255, 159, 67, 0.15) 0%, rgba(255, 159, 67, 0.1) 100%); width: 45px; height: 45px; border: 1px solid rgba(255, 159, 67, 0.2);">
                                                        <i class="uil uil-percentage fs-20" style="color: #ff9f43;"></i>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-4 d-flex">
                                                
                                                <div class="d-flex align-items-center justify-content-between p-15 rounded mb-15 transaction-card flex-grow-1"
                                                    style="background: rgba(255, 255, 255, 0.85); backdrop-filter: blur(15px); -webkit-backdrop-filter: blur(15px); border: 1px solid rgba(255, 255, 255, 0.95); box-shadow: 0 4px 15px rgba(0, 207, 232, 0.15), inset 0 0 20px rgba(255, 255, 255, 0.4);">
                                                    <div>
                                                        <p class="mb-0 fs-13 fw-bold" style="color: #00cfe8;">
                                                            <?php echo e(trans('withdraw::withdraw.total_vendor_credit')); ?></p>
                                                        <p class="mb-0 fs-20 fw-bold mt-5" style="color: #272b41;">
                                                            <?php echo e(number_format($totalVendorBalance, 2)); ?> <?php echo e(currency()); ?></p>
                                                    </div>
                                                    <div class="p-12 rounded-circle d-flex align-items-center justify-content-center"
                                                        style="background: linear-gradient(135deg, rgba(0, 207, 232, 0.15) 0%, rgba(0, 207, 232, 0.1) 100%); width: 45px; height: 45px; border: 1px solid rgba(0, 207, 232, 0.2);">
                                                        <i class="uil uil-money-bill-stack fs-20" style="color: #00cfe8;"></i>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        
                                        <div class="mb-3 mt-4">
                                            <h6 class="fw-bold text-muted mb-3"><?php echo e(trans('dashboard.vendors_withdraw_transactions')); ?></h6>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-4 d-flex">
                                                
                                                <div class="d-flex align-items-center justify-content-between p-15 rounded mb-15 transaction-card flex-grow-1"
                                                    style="background: rgba(255, 255, 255, 0.85); backdrop-filter: blur(15px); -webkit-backdrop-filter: blur(15px); border: 1px solid rgba(255, 255, 255, 0.95); box-shadow: 0 4px 15px rgba(102, 126, 234, 0.15), inset 0 0 20px rgba(255, 255, 255, 0.4);">
                                                    <div>
                                                        <p class="mb-0 fs-13 fw-bold" style="color: #667eea;">
                                                            <?php echo e(trans('withdraw::withdraw.total_balance_needed')); ?></p>
                                                        <p class="mb-0 fs-20 fw-bold mt-5" style="color: #272b41;">
                                                            <?php echo e(number_format($totalVendorBalance, 2)); ?> <?php echo e(currency()); ?></p>
                                                    </div>
                                                    <div class="p-12 rounded-circle d-flex align-items-center justify-content-center"
                                                        style="background: linear-gradient(135deg, rgba(102, 126, 234, 0.15) 0%, rgba(118, 75, 162, 0.1) 100%); width: 45px; height: 45px; border: 1px solid rgba(102, 126, 234, 0.2);">
                                                        <i class="uil uil-wallet fs-20" style="color: #667eea;"></i>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-4 d-flex">
                                                
                                                <div class="d-flex align-items-center justify-content-between p-15 rounded mb-15 transaction-card flex-grow-1"
                                                    style="background: rgba(255, 255, 255, 0.85); backdrop-filter: blur(15px); -webkit-backdrop-filter: blur(15px); border: 1px solid rgba(255, 255, 255, 0.95); box-shadow: 0 4px 15px rgba(40, 199, 111, 0.15), inset 0 0 20px rgba(255, 255, 255, 0.4);">
                                                    <div>
                                                        <p class="mb-0 fs-13 fw-bold" style="color: #28c76f;">
                                                            <?php echo e(trans('vendor::vendor.total_sent_money')); ?></p>
                                                        <p class="mb-0 fs-20 fw-bold mt-5" style="color: #272b41;">
                                                            <?php echo e(number_format($totalSentMoney, 2)); ?> <?php echo e(currency()); ?></p>
                                                    </div>
                                                    <div class="p-12 rounded-circle d-flex align-items-center justify-content-center"
                                                        style="background: linear-gradient(135deg, rgba(40, 199, 111, 0.15) 0%, rgba(40, 199, 111, 0.1) 100%); width: 45px; height: 45px; border: 1px solid rgba(40, 199, 111, 0.2);">
                                                        <i class="uil uil-arrow-up-right fs-20" style="color: #28c76f;"></i>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-4 d-flex">
                                                
                                                <div class="d-flex align-items-center justify-content-between p-15 rounded mb-15 transaction-card flex-grow-1"
                                                    style="background: rgba(255, 255, 255, 0.85); backdrop-filter: blur(15px); -webkit-backdrop-filter: blur(15px); border: 1px solid rgba(255, 255, 255, 0.95); box-shadow: 0 4px 15px rgba(79, 172, 254, 0.15), inset 0 0 20px rgba(255, 255, 255, 0.4);">
                                                    <div>
                                                        <p class="mb-0 fs-13 fw-bold" style="color: #4facfe;">
                                                            <?php echo e(trans('vendor::vendor.total_remaining')); ?></p>
                                                        <p class="mb-0 fs-20 fw-bold mt-5" style="color: #272b41;">
                                                            <?php echo e(number_format($totalVendorBalance - $totalSentMoney, 2)); ?> <?php echo e(currency()); ?></p>
                                                    </div>
                                                    <div class="p-12 rounded-circle d-flex align-items-center justify-content-center"
                                                        style="background: linear-gradient(135deg, rgba(79, 172, 254, 0.15) 0%, rgba(0, 242, 254, 0.1) 100%); width: 45px; height: 45px; border: 1px solid rgba(79, 172, 254, 0.2);">
                                                        <i class="uil uil-calculator-alt fs-20" style="color: #4facfe;"></i>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
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
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.translation-display','data' => ['label' => trans('vendor::vendor.name'),'model' => $vendor,'fieldName' => 'name','languages' => $languages]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('translation-display'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['label' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(trans('vendor::vendor.name')),'model' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($vendor),'fieldName' => 'name','languages' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($languages)]); ?>
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
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.translation-display','data' => ['label' => trans('vendor::vendor.description'),'model' => $vendor,'fieldName' => 'description','languages' => $languages]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('translation-display'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['label' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(trans('vendor::vendor.description')),'model' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($vendor),'fieldName' => 'description','languages' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($languages)]); ?>
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
                                                        class="il-gray fs-14 fw-500 mb-10"><?php echo e(trans('vendor::vendor.country')); ?></label>
                                                    <p class="fs-15 color-dark">
                                                        <?php echo e($vendor->country->name ?? '--'); ?>

                                                    </p>
                                                </div>
                                            </div>
                                            
                                            <div class="col-md-6">
                                                <div class="view-item">
                                                    <label
                                                        class="il-gray fs-14 fw-500 mb-10"><?php echo e(trans('vendor::vendor.vendor_type')); ?></label>
                                                    <p class="fs-15 color-dark">
                                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($vendor->type == 'product'): ?>
                                                            <span
                                                                class="badge badge-primary badge-round badge-lg"><?php echo e(trans('vendor::vendor.product')); ?></span>
                                                        <?php elseif($vendor->type == 'booking'): ?>
                                                            <span
                                                                class="badge badge-info badge-round badge-lg"><?php echo e(trans('vendor::vendor.booking')); ?></span>
                                                        <?php elseif($vendor->type == 'product_booking'): ?>
                                                            <span
                                                                class="badge badge-warning badge-round badge-lg"><?php echo e(trans('vendor::vendor.product_booking')); ?></span>
                                                        <?php else: ?>
                                                            <span class="text-muted">-</span>
                                                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                                    </p>
                                                </div>
                                            </div>
                                            
                                            <div class="col-md-6">
                                                <div class="view-item">
                                                    <label
                                                        class="il-gray fs-14 fw-500 mb-10"><?php echo e(trans('vendor::vendor.departments')); ?></label>
                                                    <p class="fs-15 color-dark">
                                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($vendor->departments && $vendor->departments->count() > 0): ?>
                                                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $vendor->departments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $department): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                                <span
                                                                    class="badge badge-primary badge-round badge-lg"><?php echo e($department->name); ?></span>
                                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                                        <?php else: ?>
                                                            <span class="text-muted">-</span>
                                                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                                    </p>
                                                </div>
                                            </div>
                                            
                                            <div class="col-md-6">
                                                <div class="view-item">
                                                    <label
                                                        class="il-gray fs-14 fw-500 mb-10"><?php echo e(trans('vendor::vendor.email')); ?></label>
                                                    <p class="fs-15 color-dark">
                                                        <?php echo e($vendor->user ? $vendor->user->email : '-'); ?>

                                                    </p>
                                                </div>
                                            </div>
                                            
                                            <div class="col-md-6">
                                                <div class="view-item">
                                                    <label
                                                        class="il-gray fs-14 fw-500 mb-10"><?php echo e(__('common.phone')); ?></label>
                                                    <p class="fs-15 color-dark">
                                                        <?php echo e($vendor->phone ?? '-'); ?>

                                                    </p>
                                                </div>
                                            </div>
                                            
                                            <div class="col-md-6">
                                                <div class="view-item">
                                                    <label
                                                        class="il-gray fs-14 fw-500 mb-10"><?php echo e(trans('vendor::vendor.activation')); ?></label>
                                                    <p class="fs-15">
                                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($vendor->active): ?>
                                                            <span
                                                                class="badge badge-success badge-round badge-lg"><?php echo e(trans('vendor::vendor.active')); ?></span>
                                                        <?php else: ?>
                                                            <span
                                                                class="badge badge-danger badge-round badge-lg"><?php echo e(trans('vendor::vendor.inactive')); ?></span>
                                                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                                    </p>
                                                </div>
                                            </div>
                                            
                                            <div class="col-md-6">
                                                <div class="view-item">
                                                    <label
                                                        class="il-gray fs-14 fw-500 mb-10"><?php echo e(trans('main.slug')); ?></label>
                                                    <p class="fs-15">
                                                        <span
                                                            class="badge badge-success badge-round badge-lg"><?php echo e($vendor->slug); ?></span>
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($vendor->documents && $vendor->documents->count() > 0): ?>
                                    <div class="card card-holder mt-3">
                                        <div class="card-header">
                                            <h3>
                                                <i
                                                    class="uil uil-file me-1"></i><?php echo e(trans('vendor::vendor.vendor_documents')); ?>

                                            </h3>
                                        </div>
                                        <div class="card-body">
                                            <div class="row">
                                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $vendor->documents; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $document): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                    <div class="col-md-4 mb-4">
                                                        <div class="modern-document-card">
                                                            <div class="document-glass-bg"></div>
                                                            <div class="document-card-content">
                                                                <div class="document-icon-wrapper">
                                                                    <i class="uil uil-file"></i>
                                                                </div>
                                                                <div class="document-info">
                                                                    <h6 class="document-name">
                                                                        <?php echo e($document->getTranslation('name', app()->getLocale()) ?? trans('vendor::vendor.document')); ?>

                                                                    </h6>
                                                                    <p class="document-type">
                                                                        <?php echo e(trans('vendor::vendor.document')); ?>

                                                                    </p>
                                                                </div>
                                                                <div class="document-actions">
                                                                    <a href="<?php echo e(asset('storage/' . $document->path)); ?>"
                                                                        target="_blank" class="action-btn"
                                                                        title="<?php echo e(trans('common.show')); ?>">
                                                                        <i class="uil uil-eye"></i>
                                                                    </a>
                                                                    <button type="button"
                                                                        class="action-btn delete-btn delete-document-btn"
                                                                        data-bs-toggle="modal"
                                                                        data-bs-target="#modal-delete-document"
                                                                        data-document-id="<?php echo e($document->id); ?>"
                                                                        data-document-name="<?php echo e($document->getTranslation('name', app()->getLocale()) ?? trans('vendor::vendor.document')); ?>"
                                                                        title="<?php echo e(trans('common.delete')); ?>">
                                                                        <i class="uil uil-trash-alt"></i>
                                                                    </button>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                            </div>
                                        </div>
                                    </div>
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                
                                <div class="card card-holder mt-3">
                                    <div class="card-header">
                                        <h3>
                                            <i
                                                class="uil uil-search me-1"></i><?php echo e(trans('vendor::vendor.seo_information')); ?>

                                        </h3>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            
                                            <?php if (isset($component)) { $__componentOriginale5a3093cad4b0bccb881a74044179ded = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginale5a3093cad4b0bccb881a74044179ded = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.translation-display','data' => ['label' => trans('vendor::vendor.meta_title'),'model' => $vendor,'fieldName' => 'meta_title','languages' => $languages]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('translation-display'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['label' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(trans('vendor::vendor.meta_title')),'model' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($vendor),'fieldName' => 'meta_title','languages' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($languages)]); ?>
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
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.translation-display','data' => ['label' => trans('vendor::vendor.meta_description'),'model' => $vendor,'fieldName' => 'meta_description','languages' => $languages]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('translation-display'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['label' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(trans('vendor::vendor.meta_description')),'model' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($vendor),'fieldName' => 'meta_description','languages' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($languages)]); ?>
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
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.translation-display','data' => ['label' => trans('vendor::vendor.meta_keywords'),'model' => $vendor,'fieldName' => 'meta_keywords','languages' => $languages,'type' => 'keywords']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('translation-display'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['label' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(trans('vendor::vendor.meta_keywords')),'model' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($vendor),'fieldName' => 'meta_keywords','languages' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($languages),'type' => 'keywords']); ?>
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
                                                    <label
                                                        class="il-gray fs-14 fw-500 mb-10"><?php echo e(trans('common.created_at')); ?></label>
                                                    <p class="fs-15 color-dark"><?php echo e($vendor->created_at); ?></p>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="view-item">
                                                    <label
                                                        class="il-gray fs-14 fw-500 mb-10"><?php echo e(trans('common.updated_at')); ?></label>
                                                    <p class="fs-15 color-dark"><?php echo e($vendor->updated_at); ?></p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-md-4 order-1 order-md-2">
                                
                                <div class="card card-holder mb-3">
                                    <div class="card-header">
                                        <h3>
                                            <i class="uil uil-image me-1"></i><?php echo e(trans('vendor::vendor.logo')); ?>

                                        </h3>
                                    </div>
                                    <div class="card-body text-center">
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($vendor->logo && $vendor->logo->path): ?>
                                            <div class="image-wrapper">
                                                <img src="<?php echo e(asset('storage/' . $vendor->logo->path)); ?>"
                                                    alt="<?php echo e($vendor->getTranslation('name', app()->getLocale())); ?>"
                                                    class="vendor-image img-fluid">
                                            </div>
                                        <?php else: ?>
                                            <p class="fs-15 color-light fst-italic">
                                                <?php echo e(trans('vendor::vendor.no_logo_uploaded')); ?></p>
                                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                    </div>
                                </div>
                                
                                <div class="card card-holder mb-3">
                                    <div class="card-header">
                                        <h3>
                                            <i class="uil uil-image-v me-1"></i><?php echo e(trans('vendor::vendor.banner')); ?>

                                        </h3>
                                    </div>
                                    <div class="card-body text-center">
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($vendor->banner && $vendor->banner->path): ?>
                                            <div class="image-wrapper">
                                                <img src="<?php echo e(asset('storage/' . $vendor->banner->path)); ?>"
                                                    alt="<?php echo e($vendor->getTranslation('name', app()->getLocale())); ?>"
                                                    class="vendor-image img-fluid">
                                            </div>
                                        <?php else: ?>
                                            <p class="fs-15 color-light fst-italic">
                                                <?php echo e(trans('vendor::vendor.no_banner_uploaded')); ?></p>
                                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                    </div>
                                </div>


                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                
                                <div class="card card-holder mt-3">
                                    <div class="card-header">
                                        <h3>
                                            <i class="uil uil-chart-bar me-1"></i><?php echo e(trans('vendor::vendor.order_statistics')); ?>

                                        </h3>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            
                                            <div class="col-md-3 mb-3">
                                                <div class="card border-0 shadow-sm h-100" style="background: linear-gradient(135deg, #5b69ff15 0%, #5b69ff05 100%);">
                                                    <div class="card-body text-center py-4">
                                                        <div class="mb-2">
                                                            <i class="uil uil-shopping-cart fs-1" style="color: #5b69ff;"></i>
                                                        </div>
                                                        <h3 class="mb-1 fw-bold" style="color: #5b69ff;"><?php echo e($orderStats['total_order_products'] ?? 0); ?></h3>
                                                        <p class="mb-0 text-muted small"><?php echo e(trans('vendor::vendor.total_orders')); ?></p>
                                                    </div>
                                                </div>
                                            </div>

                                            
                                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(isset($orderStats['stages']) && count($orderStats['stages']) > 0): ?>
                                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $orderStats['stages']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $stageId => $stage): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                    <div class="col-md-3 mb-3">
                                                        <div class="card border-0 shadow-sm h-100" style="background: linear-gradient(135deg, <?php echo e($stage['color']); ?>15 0%, <?php echo e($stage['color']); ?>05 100%);">
                                                            <div class="card-body text-center py-4">
                                                                <div class="mb-2">
                                                                    <i class="uil <?php echo e($stage['icon']); ?> fs-1" style="color: <?php echo e($stage['color']); ?>;"></i>
                                                                </div>
                                                                <h3 class="mb-1 fw-bold" style="color: <?php echo e($stage['color']); ?>;"><?php echo e($stage['count']); ?></h3>
                                                                <p class="mb-0 text-muted small"><?php echo e($stage['name']); ?></p>
                                                            </div>
                                                        </div>
                                                    </div>
                                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                        </div>
                                    </div>
                                </div>

                                
                                <div class="card card-holder mt-3">
                                    <div class="card-header">
                                        <h3>
                                            <i class="uil uil-shopping-bag me-1"></i><?php echo e(trans('vendor::vendor.vendor_order_products')); ?>

                                        </h3>
                                    </div>
                                    <div class="card-body p-0">
                                        <div class="userDatatable global-shadow border-light-0 bg-white w-100">
                                            <div class="table-responsive">
                                                <table class="table mb-0 table-bordered table-hover">
                                                    <thead>
                                                        <tr class="userDatatable-header">
                                                            <th><span class="userDatatable-title">#</span></th>
                                                            <th><span class="userDatatable-title"><?php echo e(trans('vendor::vendor.order_product_information')); ?></span></th>
                                                            <th><span class="userDatatable-title"><?php echo e(trans('vendor::vendor.price_before_taxes')); ?></span></th>
                                                            <th><span class="userDatatable-title"><?php echo e(trans('vendor::vendor.taxes')); ?></span></th>
                                                            <th><span class="userDatatable-title"><?php echo e(trans('vendor::vendor.unit_price')); ?></span></th>
                                                            <th><span class="userDatatable-title"><?php echo e(trans('vendor::vendor.quantity')); ?></span></th>
                                                            <th><span class="userDatatable-title"><?php echo e(trans('vendor::vendor.total_price')); ?></span></th>
                                                            <th><span class="userDatatable-title"><?php echo e(trans('common.actions')); ?></span></th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $orderProducts ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $orderProduct): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                                            <?php
                                                                $unitPriceWithTax = $orderProduct->price ?? 0;
                                                                // Get tax from the relationship
                                                                $orderProductTax = $orderProduct->tax;
                                                                $taxPercentage = $orderProductTax ? $orderProductTax->percentage : 0;
                                                                $taxAmount = $orderProductTax ? $orderProductTax->amount : 0;
                                                                $priceBeforeTax = $taxPercentage > 0 ? round($unitPriceWithTax / (1 + ($taxPercentage / 100)), 2) : $unitPriceWithTax;
                                                                $productName = $orderProduct->name ?? ($orderProduct->vendorProduct && $orderProduct->vendorProduct->product ? $orderProduct->vendorProduct->product->getTranslation('name', app()->getLocale()) : '-');
                                                                $sku = $orderProduct->vendorProductVariant ? ($orderProduct->vendorProductVariant->sku ?? '-') : ($orderProduct->vendorProduct ? ($orderProduct->vendorProduct->sku ?? '-') : '-');
                                                                $variantName = ($orderProduct->vendorProductVariant && $orderProduct->vendorProductVariant->variantConfiguration) ? $orderProduct->vendorProductVariant->variant_name : null;
                                                                $orderStage = $orderProduct->order ? \Modules\Order\app\Models\OrderStage::withoutGlobalScopes()->find($orderProduct->order->stage_id) : null;
                                                            ?>
                                                            <tr>
                                                                <td><div class="userDatatable-content"><?php echo e(($orderProducts->currentPage() - 1) * $orderProducts->perPage() + $index + 1); ?></div></td>
                                                                <td>
                                                                    <div class="userDatatable-content">
                                                                        <div class="d-flex align-items-start gap-2">
                                                                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($orderProduct->vendorProduct && $orderProduct->vendorProduct->product && $orderProduct->vendorProduct->product->mainImage): ?>
                                                                                <img src="<?php echo e(asset('storage/' . $orderProduct->vendorProduct->product->mainImage->path)); ?>" 
                                                                                    alt="<?php echo e($productName); ?>" 
                                                                                    class="rounded" style="width: 50px; height: 50px; object-fit: cover;">
                                                                            <?php else: ?>
                                                                                <div class="bg-light rounded d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                                                                                    <i class="uil uil-image text-muted"></i>
                                                                                </div>
                                                                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                                                            <div class="d-flex flex-column gap-1">
                                                                                <div><span class="text-muted fw-bold"><?php echo e(trans('vendor::vendor.order_number')); ?>:</span> <strong>#<?php echo e($orderProduct->order->order_number ?? $orderProduct->order_id); ?></strong></div>
                                                                                <div><span class="text-muted fw-bold"><?php echo e(trans('vendor::vendor.product')); ?>:</span> <strong><?php echo e($productName); ?></strong></div>
                                                                                <div><span class="text-muted fw-bold"><?php echo e(trans('vendor::vendor.sku')); ?>:</span> <code><?php echo e($sku); ?></code></div>
                                                                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($variantName): ?>
                                                                                    <div><span class="text-muted fw-bold"><?php echo e(trans('vendor::vendor.variant')); ?>:</span> <span class="badge badge-primary badge-round badge-lg"><?php echo e($variantName); ?></span></div>
                                                                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                                                                <div><span class="text-muted fw-bold"><?php echo e(trans('vendor::vendor.order_status')); ?>:</span> 
                                                                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($orderStage): ?>
                                                                                        <span class="badge badge-round badge-lg" style="background-color: <?php echo e($orderStage->color ?? '#6c757d'); ?>; color: #fff;">
                                                                                            <?php echo e($orderStage->getTranslation('name', app()->getLocale())); ?>

                                                                                        </span>
                                                                                    <?php else: ?>
                                                                                        <span class="badge bg-secondary">-</span>
                                                                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </td>
                                                                <td><div class="userDatatable-content"><?php echo e(number_format($priceBeforeTax, 2)); ?> <?php echo e(currency()); ?></div></td>
                                                                <td><div class="userDatatable-content text-warning"><?php echo e(number_format($taxAmount, 2)); ?> <?php echo e(currency()); ?> <small class="text-muted">(<?php echo e($taxPercentage); ?>%)</small></div></td>
                                                                <td><div class="userDatatable-content"><?php echo e(number_format($unitPriceWithTax, 2)); ?> <?php echo e(currency()); ?></div></td>
                                                                <td><div class="userDatatable-content fw-medium"><?php echo e($orderProduct->quantity); ?></div></td>
                                                                <td><div class="userDatatable-content fw-bold text-success"><?php echo e(number_format($unitPriceWithTax * ($orderProduct->quantity ?? 1), 2)); ?> <?php echo e(currency()); ?></div></td>
                                                                <td>
                                                                    <div class="userDatatable-content">
                                                                        <a href="<?php echo e(route('admin.orders.show', $orderProduct->order_id)); ?>" target="_blank" class="btn btn-sm btn-primary">
                                                                            <i class="uil uil-eye m-0"></i>
                                                                        </a>
                                                                    </div>
                                                                </td>
                                                            </tr>
                                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                                            <tr>
                                                                <td colspan="8" class="text-center text-muted py-4"><?php echo e(trans('vendor::vendor.no_order_products_found')); ?></td>
                                                            </tr>
                                                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                                    </tbody>
                                                </table>
                                            </div>
                                            <div class="d-flex justify-content-between align-items-center p-3 border-top">
                                                <div class="text-muted small">
                                                    <?php echo e(trans('common.showing')); ?> <?php echo e($orderProducts->firstItem() ?? 0); ?> - <?php echo e($orderProducts->lastItem() ?? 0); ?> <?php echo e(trans('common.of')); ?> <?php echo e($orderProducts->total()); ?> <?php echo e(trans('common.items')); ?>

                                                </div>
                                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($orderProducts && $orderProducts->hasPages()): ?>
                                                    <?php echo e($orderProducts->links('vendor.pagination.custom')); ?>

                                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                
                                <div class="card card-holder mt-3">
                                    <div class="card-header">
                                        <h3>
                                            <i class="uil uil-money-withdraw me-1"></i><?php echo e(trans('vendor::vendor.vendor_withdraws')); ?>

                                        </h3>
                                    </div>
                                    <div class="card-body p-0">
                                        <div class="userDatatable global-shadow border-light-0 bg-white w-100">
                                            <div class="table-responsive">
                                                <table class="table mb-0 table-bordered table-hover">
                                                    <thead>
                                                        <tr class="userDatatable-header">
                                                            <th><span class="userDatatable-title">#</span></th>
                                                            <th><span class="userDatatable-title"><?php echo e(trans('vendor::vendor.withdraw_information')); ?></span></th>
                                                            <th><span class="userDatatable-title"><?php echo e(trans('vendor::vendor.invoice')); ?></span></th>
                                                            <th><span class="userDatatable-title"><?php echo e(trans('vendor::vendor.sent_by')); ?></span></th>
                                                            <th><span class="userDatatable-title"><?php echo e(trans('common.created_at')); ?></span></th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $withdraws ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $withdraw): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                                            <tr>
                                                                <td><div class="userDatatable-content"><?php echo e(($withdraws->currentPage() - 1) * $withdraws->perPage() + $index + 1); ?></div></td>
                                                                <td>
                                                                    <div class="userDatatable-content">
                                                                        <div class="d-flex flex-column gap-1">
                                                                            <div><span class="text-muted fw-bold"><?php echo e(trans('vendor::vendor.balance_before')); ?>:</span> <strong><?php echo e(number_format($withdraw->before_sending_money ?? 0, 2)); ?> <?php echo e(currency()); ?></strong></div>
                                                                            <div><span class="text-muted fw-bold"><?php echo e(trans('vendor::vendor.sent_amount')); ?>:</span> <strong class="text-success"><?php echo e(number_format($withdraw->sent_amount ?? 0, 2)); ?> <?php echo e(currency()); ?></strong></div>
                                                                            <div><span class="text-muted fw-bold"><?php echo e(trans('vendor::vendor.balance_after')); ?>:</span> <strong><?php echo e(number_format($withdraw->after_sending_amount ?? 0, 2)); ?> <?php echo e(currency()); ?></strong></div>
                                                                            <div><span class="text-muted fw-bold"><?php echo e(trans('vendor::vendor.withdraw_status')); ?>:</span> 
                                                                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($withdraw->status == 'accepted'): ?>
                                                                                    <strong class="text-success"><?php echo e(trans('vendor::vendor.accepted')); ?></strong>
                                                                                <?php elseif($withdraw->status == 'rejected'): ?>
                                                                                    <strong class="text-danger"><?php echo e(trans('vendor::vendor.rejected')); ?></strong>
                                                                                <?php else: ?>
                                                                                    <strong class="text-primary"><?php echo e(trans('vendor::vendor.new')); ?></strong>
                                                                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </td>
                                                                <td>
                                                                    <div class="userDatatable-content d-flex justify-content-center">
                                                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($withdraw->invoice): ?>
                                                                            <a href="<?php echo e(asset('storage/invoices/' . $withdraw->invoice)); ?>" target="_blank" class="btn btn-sm btn-primary">
                                                                                <i class="uil uil-download-alt m-0"></i>
                                                                            </a>
                                                                        <?php else: ?>
                                                                            <span class="text-muted">-</span>
                                                                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                                                    </div>
                                                                </td>
                                                                <td><div class="userDatatable-content"><?php echo e($withdraw->admin->name ?? '-'); ?></div></td>
                                                                <td><div class="userDatatable-content"><?php echo e($withdraw->created_at); ?></div></td>
                                                            </tr>
                                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                                            <tr>
                                                                <td colspan="5" class="text-center text-muted py-4"><?php echo e(trans('vendor::vendor.no_withdraws_found')); ?></td>
                                                            </tr>
                                                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                                    </tbody>
                                                </table>
                                            </div>
                                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($withdraws && $withdraws->count() > 0): ?>
                                            <div class="d-flex justify-content-between align-items-center p-3 border-top">
                                                <div class="text-muted small">
                                                    <?php echo e(trans('common.showing')); ?> <?php echo e($withdraws->firstItem() ?? 0); ?> - <?php echo e($withdraws->lastItem() ?? 0); ?> <?php echo e(trans('common.of')); ?> <?php echo e($withdraws->total()); ?> <?php echo e(trans('common.items')); ?>

                                                </div>
                                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($withdraws->hasPages()): ?>
                                                    <?php echo e($withdraws->links('vendor.pagination.custom')); ?>

                                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                            </div>
                                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
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

        
        <?php if (isset($component)) { $__componentOriginalb7eac87efb73c0c2c26fe03ec80faafd = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalb7eac87efb73c0c2c26fe03ec80faafd = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.delete-modal','data' => ['modalId' => 'modal-delete-document','title' => __('common.confirm_delete'),'message' => __('common.delete_confirmation'),'itemNameId' => 'delete-document-name','confirmBtnId' => 'confirmDeleteDocumentBtn','deleteRoute' => '#','cancelText' => __('common.cancel'),'deleteText' => __('common.delete')]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('delete-modal'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['modalId' => 'modal-delete-document','title' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(__('common.confirm_delete')),'message' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(__('common.delete_confirmation')),'itemNameId' => 'delete-document-name','confirmBtnId' => 'confirmDeleteDocumentBtn','deleteRoute' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute('#'),'cancelText' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(__('common.cancel')),'deleteText' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(__('common.delete'))]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalb7eac87efb73c0c2c26fe03ec80faafd)): ?>
<?php $attributes = $__attributesOriginalb7eac87efb73c0c2c26fe03ec80faafd; ?>
<?php unset($__attributesOriginalb7eac87efb73c0c2c26fe03ec80faafd); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalb7eac87efb73c0c2c26fe03ec80faafd)): ?>
<?php $component = $__componentOriginalb7eac87efb73c0c2c26fe03ec80faafd; ?>
<?php unset($__componentOriginalb7eac87efb73c0c2c26fe03ec80faafd); ?>
<?php endif; ?>
    <?php $__env->stopSection(); ?>

    <?php $__env->startPush('scripts'); ?>
        <script>
            $(document).ready(function() {
                // Setup CSRF token for all AJAX requests
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>'
                    }
                });
                // Set delete modal data when delete button is clicked
                $(document).on('click', '.delete-document-btn', function() {
                    const documentId = $(this).data('document-id');
                    const documentName = $(this).data('document-name');

                    // Set the document name in the modal
                    $('#delete-document-name').text(documentName);

                    // Store document ID and name in the confirm button
                    $('#confirmDeleteDocumentBtn').data('document-id', documentId);
                    $('#confirmDeleteDocumentBtn').data('document-name', documentName);
                });

                // Handle confirm delete button click
                $('#confirmDeleteDocumentBtn').on('click', function() {
                    const documentId = $(this).data('document-id');
                    const documentName = $(this).data('document-name');
                    const confirmBtn = $(this);

                    // Debug logging
                    console.log('Deleting document:', documentId, documentName);

                    // Disable button and show loading
                    confirmBtn.prop('disabled', true);
                    confirmBtn.html(
                    '<i class="uil uil-spinner-alt spin me-1"></i><?php echo e(__('common.deleting')); ?>');

                    // Send AJAX request
                    $.ajax({
                        url: `<?php echo e(route('admin.vendors.documents.destroy', ['vendor' => $vendor->id, 'document' => '__DOCUMENT_ID__'])); ?>`
                            .replace('__DOCUMENT_ID__', documentId),
                        type: 'DELETE',
                        data: {
                            _token: '<?php echo e(csrf_token()); ?>'
                        },
                        headers: {
                            'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>'
                        },
                        success: function(response) {
                            console.log('Delete response:', response);

                            // Hide the modal
                            $('#modal-delete-document').modal('hide');

                            if (response.success) {
                                // Display success message
                                if (typeof toastr !== 'undefined') {
                                    toastr.success(response.message);
                                } else {
                                    alert(response.message);
                                }

                                // Reload the page after a short delay
                                setTimeout(function() {
                                    location.reload();
                                }, 1000); // 1-second delay
                            } else {
                                console.error('Delete failed:', response);
                            }

                            // Reset button
                            confirmBtn.prop('disabled', false);
                            confirmBtn.html('<?php echo e(__('common.delete')); ?>');
                        },
                        error: function(xhr) {
                            console.error('AJAX Error deleting document:', xhr);
                            console.error('Status:', xhr.status);
                            console.error('Response:', xhr.responseText);

                            // Hide the modal
                            $('#modal-delete-document').modal('hide');

                            // Reset button
                            confirmBtn.prop('disabled', false);
                            confirmBtn.html('<?php echo e(__('common.delete')); ?>');
                        }
                    });
                });
            });
        </script>

        <style>
            .spin {
                animation: spin 1s linear infinite;
            }

            @keyframes spin {
                from {
                    transform: rotate(0deg);
                }

                to {
                    transform: rotate(360deg);
                }
            }

            .vendor-image {
                max-width: 100%;
                height: auto;
                border-radius: 8px;
                box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
            }

            .image-wrapper {
                cursor: pointer;
                transition: transform 0.2s ease;
            }

            .image-wrapper:hover {
                transform: scale(1.02);
            }
        </style>
    <?php $__env->stopPush(); ?>

<?php echo $__env->make('layout.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\eramo-multi-vendor\Modules/Vendor\resources/views/vendors/show.blade.php ENDPATH**/ ?>