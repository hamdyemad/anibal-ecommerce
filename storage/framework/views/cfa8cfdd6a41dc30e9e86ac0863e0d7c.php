
<?php $__env->startSection('title', trans('catalogmanagement::product.bank_products_management')); ?>

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
                    ['title' => trans('catalogmanagement::product.bank_products_management')],
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
                    ['title' => trans('catalogmanagement::product.bank_products_management')],
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
                <div class="userDatatable global-shadow border-0 p-30 bg-white radius-xl w-100 mb-30">
                    <div class="d-flex justify-content-between align-items-center mb-25">
                        <h4 class="mb-0 fw-600 text-primary">
                            <i class="uil uil-database me-2"></i>
                            <?php echo e(trans('catalogmanagement::product.bank_products_management')); ?>

                        </h4>
                        <div class="d-flex gap-2">
                            
                            <a href="<?php echo e(route('admin.products.index')); ?>"
                                class="btn btn-secondary btn-squared shadow-sm px-4">
                                <i class="uil uil-arrow-left"></i> <?php echo e(trans('common.back') ?? 'Back'); ?>

                            </a>
                        </div>
                    </div>
                    
                    <div class="mb-25">
                        <div class="card border-0 shadow-sm">
                            <div class="card-body">
                                <div class="row g-3 align-items-end">

                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="search" class="il-gray fs-14 fw-500 mb-10">
                                                <i class="uil uil-search me-1"></i> <?php echo e(__('common.search')); ?>

                                            </label>
                                            <input type="text"
                                                class="form-control ih-medium ip-gray radius-xs b-light px-15"
                                                id="search" placeholder="<?php echo e(__('common.search')); ?>" autocomplete="off">
                                        </div>
                                    </div>

                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="brand_filter" class="il-gray fs-14 fw-500 mb-10">
                                                <i class="uil uil-tag-alt me-1"></i>
                                                <?php echo e(__('catalogmanagement::product.brand')); ?>

                                            </label>
                                            <select
                                                class="select2 form-control ih-medium ip-gray radius-xs b-light px-15 form-select"
                                                id="brand_filter">
                                                <option value=""><?php echo e(__('common.all')); ?></option>
                                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $brands; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $brand): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                    <option value="<?php echo e($brand['id']); ?>"><?php echo e($brand['name']); ?></option>
                                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="category_filter" class="il-gray fs-14 fw-500 mb-10">
                                                <i class="uil uil-folder me-1"></i>
                                                <?php echo e(__('catalogmanagement::product.category')); ?>

                                            </label>
                                            <select
                                                class="select2 form-control ih-medium ip-gray radius-xs b-light px-15 form-select"
                                                id="category_filter">
                                                <option value=""><?php echo e(__('common.all')); ?></option>
                                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $category): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                    <option value="<?php echo e($category['id']); ?>"><?php echo e($category['name']); ?></option>
                                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                            </select>
                                        </div>
                                    </div>

                                    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('products.bank.change-activation')): ?>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="active" class="il-gray fs-14 fw-500 mb-10">
                                                    <i class="uil uil-check-circle me-1"></i>
                                                    <?php echo e(__('common.active_status')); ?>

                                                </label>
                                                <select
                                                    class="select2 form-control ih-medium ip-gray radius-xs b-light px-15 form-select"
                                                    id="active">
                                                    <option value=""><?php echo e(__('common.all')); ?></option>
                                                    <option value="1"><?php echo e(__('common.active')); ?></option>
                                                    <option value="2"><?php echo e(__('common.inactive')); ?></option>
                                                </select>
                                            </div>
                                        </div>
                                    <?php endif; ?>

                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="created_date_from" class="il-gray fs-14 fw-500 mb-10">
                                                <i class="uil uil-calendar-alt me-1"></i>
                                                <?php echo e(__('common.created_date_from')); ?>

                                            </label>
                                            <input type="date"
                                                class="form-control ih-medium ip-gray radius-xs b-light px-15"
                                                id="created_date_from">
                                        </div>
                                    </div>

                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="created_date_to" class="il-gray fs-14 fw-500 mb-10">
                                                <i class="uil uil-calendar-alt me-1"></i>
                                                <?php echo e(__('common.created_date_to')); ?>

                                            </label>
                                            <input type="date"
                                                class="form-control ih-medium ip-gray radius-xs b-light px-15"
                                                id="created_date_to">
                                        </div>
                                    </div>

                                    <div class="col-md-12 d-flex align-items-center">
                                        <button type="button" id="searchBtn"
                                            class="btn btn-success btn-default btn-squared me-1"
                                            title="<?php echo e(__('common.search')); ?>">
                                            <i class="uil uil-search me-1"></i>
                                            <?php echo e(__('common.search')); ?>

                                        </button>
                                        <button type="button" id="resetFilters"
                                            class="btn btn-warning btn-default btn-squared"
                                            title="<?php echo e(__('common.reset')); ?>">
                                            <i class="uil uil-redo me-1"></i>
                                            <?php echo e(__('common.reset_filters')); ?>

                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    
                    <div class="table-responsive">
                        <table id="bankProductsDataTable" class="table mb-0 table-bordered table-hover" style="width:100%">
                            <thead>
                                <tr class="userDatatable-header">
                                    <th class="text-center"><span class="userDatatable-title">#</span></th>
                                    <th><span
                                            class="userDatatable-title"><?php echo e(__('catalogmanagement::product.product_information')); ?></span>
                                    </th>
                                    <th><span
                                            class="userDatatable-title"><?php echo e(__('catalogmanagement::product.brand')); ?></span>
                                    </th>
                                    <th><span
                                            class="userDatatable-title"><?php echo e(__('catalogmanagement::product.category')); ?></span>
                                    </th>
                                    <th><span class="userDatatable-title"><?php echo e(__('common.activation')); ?></span></th>
                                    <th><span class="userDatatable-title"><?php echo e(__('common.created_at')); ?></span></th>
                                    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('products.bank')): ?>
                                        <th><span class="userDatatable-title"><?php echo e(__('common.actions')); ?></span></th>
                                    <?php endif; ?>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
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
            const translations = {
                active: '<?php echo e(__('common.active')); ?>',
                inactive: '<?php echo e(__('common.inactive')); ?>'
            };

            // Check if user is admin
            const isAdmin =
                <?php echo e(in_array(auth()->user()->user_type_id, \App\Models\UserType::adminIds()) ? 'true' : 'false'); ?>;

            // Check if user is vendor
            const isVendor =
                <?php echo e(in_array(auth()->user()->user_type_id, \App\Models\UserType::vendorIds()) ? 'true' : 'false'); ?>;

            let table = $('#bankProductsDataTable').DataTable({
                processing: true,
                serverSide: true,
                searching: false,
                ajax: {
                    url: '<?php echo e(route('admin.products.bank.datatable')); ?>',
                    data: function(d) {
                        d.search = $('#search').val();
                        d.brand_id = $('#brand_filter').val();
                        d.category_id = $('#category_filter').val();
                        <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('products.bank.change-activation')): ?>
                            d.active = $('#active').val();
                        <?php endif; ?>
                        d.created_date_from = $('#created_date_from').val();
                        d.created_date_to = $('#created_date_to').val();
                        d.per_page = $('#entriesSelect').val() || 10;
                    }
                },
                columns: (() => {
                    let columns = [{
                            data: 'index',
                            name: 'index',
                            orderable: false,
                            searchable: false,
                            className: 'text-center fw-bold'
                        },
                        {
                            data: 'product_information',
                            name: 'product_information',
                            orderable: false,
                            searchable: true,
                            render: function(data, type, row) {
                                if (!data) return '<span class="text-muted">—</span>';
                                let html = '<div class="product-info-container">';

                                if (data.name_en && data.name_en !== '-') {
                                    html += `<div class="product-name-item mb-2">
                                        <span class="language-badge badge bg-primary text-white px-2 py-1 me-2 rounded-pill fw-bold" style="font-size: 10px;">EN</span>
                                        <span class="product-name text-dark fw-semibold">${$('<div/>').text(data.name_en).html()}</span>
                                    </div>`;
                                }

                                if (data.name_ar && data.name_ar !== '-') {
                                    html += `<div class="product-name-item">
                                        <span class="language-badge badge bg-success text-white px-2 py-1 me-2 rounded-pill fw-bold" style="font-size: 10px;">AR</span>
                                        <span class="product-name text-dark fw-semibold" dir="rtl">${$('<div/>').text(data.name_ar).html()}</span>
                                    </div>`;
                                }

                                html += '</div>';
                                return html;
                            },
                            className: 'text-start'
                        },
                        {
                            data: 'brand',
                            name: 'brand',
                            searchable: false,
                            orderable: false,
                            render: function(data) {
                                if (!data?.name) return '<span class="text-muted">—</span>';
                                return `<span class="badge badge-info badge-round badge-lg">${$('<div/>').text(data.name).html()}</span>`;
                            }
                        },
                        {
                            data: 'category',
                            name: 'category',
                            searchable: false,
                            orderable: false,
                            render: function(data) {
                                if (!data?.name) return '<span class="text-muted">—</span>';
                                return `<span class="badge badge-secondary badge-round badge-lg">${$('<div/>').text(data.name).html()}</span>`;
                            }
                        }
                    ];

                    // Add activation column only for users with permission
                    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('products.bank.change-activation')): ?>
                        columns.push({
                            data: 'active',
                            name: 'active',
                            orderable: false,
                            searchable: false,
                            className: 'text-center',
                            render: function(data, type, row) {
                                if (type === 'sort' || type === 'type') {
                                    return data ? 1 : 0;
                                }

                                const isChecked = data ? 'checked' : '';
                                const switchId = 'activation-switch-' + row.id;
                                const productName = row.product_information?.name_en ||
                                    row.product_information?.name_ar || 'Product #' +
                                    row.id;

                                return `<div class="userDatatable-content">
                                    <div class="form-switch">
                                        <input class="form-check-input activation-switcher"
                                               type="checkbox"
                                               id="${switchId}"
                                               data-product-id="${row.id}"
                                               data-product-name="${$('<div>').text(productName).html()}"
                                               ${isChecked}
                                               style="cursor: pointer;">
                                        <label class="form-check-label" for="${switchId}"></label>
                                    </div>
                                </div>`;
                            }
                        });
                    <?php else: ?>
                        columns.push({
                            data: 'active',
                            name: 'active',
                            orderable: false,
                            searchable: false,
                            className: 'text-center',
                            render: function(data, type, row) {
                                return data 
                                    ? '<span class="badge badge-round bg-success"><?php echo e(trans("common.active")); ?></span>'
                                    : '<span class="badge badge-round bg-danger"><?php echo e(trans("common.inactive")); ?></span>';
                            }
                        });
                    <?php endif; ?>

                    // Add created_at column
                    columns.push({
                        data: 'created_at',
                        name: 'created_at',
                        searchable: false,
                        orderable: false,
                        render: function(data) {
                            return data;
                        }
                    });

                    // Add actions column for users with permission
                    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('products.bank')): ?>
                        columns.push({
                            data: null,
                            orderable: false,
                            searchable: false,
                            className: 'text-center',
                            render: function(data) {
                                const bankViewUrl =
                                    "<?php echo e(route('admin.products.bank.view', ':id')); ?>"
                                    .replace(':id', data.id);
                                const editUrl =
                                    "<?php echo e(route('admin.products.edit', ':id')); ?>"
                                    .replace(':id', data.id);

                                let actionsHtml = `<div class="orderDatatable_actions d-inline-flex gap-1 justify-content-center">
                                    <a href="${bankViewUrl}" class="view btn btn-info table_action_father" title="<?php echo e(trans('catalogmanagement::product.view_bank_product')); ?>">
                                        <i class="uil uil-eye table_action_icon"></i>
                                    </a>`;

                                actionsHtml += `</div>`;
                                return actionsHtml;
                            }
                        });
                    <?php endif; ?>

                    return columns;
                })(),
                pageLength: 10,
                lengthMenu: [10, 25, 50, 100],
                order: [
                    [5, 'desc']
                ],
                language: {
                    lengthMenu: "<?php echo e(__('common.show') ?? 'Show'); ?> _MENU_",
                    info: "<?php echo e(__('common.showing') ?? 'Showing'); ?> _START_ <?php echo e(__('common.to') ?? 'to'); ?> _END_ <?php echo e(__('common.of') ?? 'of'); ?> _TOTAL_ <?php echo e(__('common.entries') ?? 'entries'); ?>",
                    infoEmpty: "<?php echo e(__('common.showing') ?? 'Showing'); ?> 0 <?php echo e(__('common.to') ?? 'to'); ?> 0 <?php echo e(__('common.of') ?? 'of'); ?> 0 <?php echo e(__('common.entries') ?? 'entries'); ?>",
                    infoFiltered: "(<?php echo e(__('common.filtered_from') ?? 'filtered from'); ?> _MAX_ <?php echo e(__('common.total_entries') ?? 'total entries'); ?>)",
                    zeroRecords: "<?php echo e(__('catalogmanagement::product.no_products_found') ?? 'No products found'); ?>",
                    emptyTable: "<?php echo e(__('catalogmanagement::product.no_products_found') ?? 'No products found'); ?>",
                    loadingRecords: "<?php echo e(__('common.loading') ?? 'Loading'); ?>...",
                    processing: "<?php echo e(__('common.processing') ?? 'Processing'); ?>...",
                    paginate: {
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(app()->getLocale() == 'en'): ?>
                            first: '<i class="uil uil-angle-double-left"></i>',
                            last: '<i class="uil uil-angle-double-right"></i>',
                            next: '<i class="uil uil-angle-right"></i>',
                            previous: '<i class="uil uil-angle-left"></i>'
                        <?php else: ?>
                            first: '<i class="uil uil-angle-double-right"></i>',
                            last: '<i class="uil uil-angle-double-left"></i>',
                            next: '<i class="uil uil-angle-left"></i>',
                            previous: '<i class="uil uil-angle-right"></i>'
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    }
                },
                dom: '<"row"<"col-sm-12"tr>><"row mt-3"<"col-sm-12 col-md-5"i><"col-sm-12 col-md-7"p>>'
            });

            // Search button click
            $('#searchBtn').on('click', function() {
                table.ajax.reload();
            });

            // Live search
            let searchTimer;
            $('#search').on('keyup', function() {
                clearTimeout(searchTimer);
                searchTimer = setTimeout(() => table.ajax.reload(), 600);
            });

            // Filters
            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('products.bank.change-activation')): ?>
                $('#brand_filter, #category_filter, #active').on('select2:select select2:clear change', function() {
                    table.ajax.reload();
                });
            <?php else: ?>
                $('#brand_filter, #category_filter').on('select2:select select2:clear change', function() {
                    table.ajax.reload();
                });
            <?php endif; ?>

            // Date filters
            $('#created_date_from, #created_date_to').on('change', function() {
                table.ajax.reload();
            });

            // Reset
            $('#resetFilters').on('click', function() {
                $('#search').val('');
                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('products.bank.change-activation')): ?>
                    $('#brand_filter, #category_filter, #active').val(null).trigger('change');
                <?php else: ?>
                    $('#brand_filter, #category_filter').val(null).trigger('change');
                <?php endif; ?>
                $('#created_date_from, #created_date_to').val('');
                table.ajax.reload();
            });

            // Activation switcher handler (admin only)
            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('products.bank.change-activation')): ?>
                $(document).on('change', '.activation-switcher', function() {
                    const switcher = $(this);
                    const productId = switcher.data('product-id');
                    const productName = switcher.data('product-name');
                    const newStatus = switcher.is(':checked') ? 1 : 2;

                    switcher.prop('disabled', true);

                    if (typeof LoadingOverlay !== 'undefined') {
                        LoadingOverlay.show({
                            text: '<?php echo e(__('catalogmanagement::product.change_activation')); ?>',
                            subtext: '<?php echo e(__('common.please_wait') ?? 'Please wait'); ?>...'
                        });
                    }

                    $.ajax({
                        url: '<?php echo e(route('admin.products.change-bank-activation', ':id')); ?>'.replace(
                            ':id', productId),
                        type: 'POST',
                        data: {
                            _token: '<?php echo e(csrf_token()); ?>',
                            status: newStatus
                        },
                        success: function(response) {
                            if (typeof LoadingOverlay !== 'undefined') {
                                LoadingOverlay.hide();
                            }

                            if (response.success) {
                                if (typeof Swal !== 'undefined') {
                                    Swal.fire({
                                        icon: 'success',
                                        title: '<?php echo e(__('common.success') ?? 'Success'); ?>',
                                        text: response.message,
                                        timer: 2000,
                                        showConfirmButton: false,
                                        toast: true,
                                        position: 'top-end'
                                    });
                                } else if (typeof toastr !== 'undefined') {
                                    toastr.success(response.message);
                                }
                                table.ajax.reload(null, false);
                            } else {
                                switcher.prop('checked', !switcher.is(':checked'));
                                if (typeof Swal !== 'undefined') {
                                    Swal.fire({
                                        icon: 'error',
                                        title: '<?php echo e(__('common.error') ?? 'Error'); ?>',
                                        text: response.message
                                    });
                                } else if (typeof toastr !== 'undefined') {
                                    toastr.error(response.message);
                                }
                            }
                        },
                        error: function(xhr) {
                            if (typeof LoadingOverlay !== 'undefined') {
                                LoadingOverlay.hide();
                            }
                            switcher.prop('checked', !switcher.is(':checked'));
                            let errorMessage =
                                '<?php echo e(__('catalogmanagement::product.error_changing_activation')); ?>';
                            if (xhr.responseJSON && xhr.responseJSON.message) {
                                errorMessage = xhr.responseJSON.message;
                            }
                            if (typeof Swal !== 'undefined') {
                                Swal.fire({
                                    icon: 'error',
                                    title: '<?php echo e(__('common.error') ?? 'Error'); ?>',
                                    text: errorMessage
                                });
                            } else if (typeof toastr !== 'undefined') {
                                toastr.error(errorMessage);
                            }
                        },
                        complete: function() {
                            switcher.prop('disabled', false);
                        }
                    });
                });
            <?php endif; ?>
        });
    </script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layout.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\eramo-multi-vendor\Modules/CatalogManagement\resources/views/product/bank.blade.php ENDPATH**/ ?>