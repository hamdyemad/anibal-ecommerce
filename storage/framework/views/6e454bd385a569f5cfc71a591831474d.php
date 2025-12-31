
<?php $__env->startSection('title'); ?>
    <?php echo e(trans('shipping.shipping_management')); ?> | Bnaia
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
                    ['title' => trans('shipping.shipping_management')],
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
                    ['title' => trans('shipping.shipping_management')],
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
                        <h4 class="mb-0 fw-500 fw-bold"><?php echo e(trans('shipping.shipping_management')); ?></h4>
                        <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('shippings.create')): ?>
                        <div class="d-flex gap-2">
                            <a href="<?php echo e(route('admin.shippings.create')); ?>"
                                class="btn btn-primary btn-default btn-squared text-capitalize">
                                <i class="uil uil-plus"></i> <?php echo e(trans('shipping.create_shipping')); ?>

                            </a>
                        </div>
                        <?php endif; ?>
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
                                                id="search"
                                                placeholder="<?php echo e(__('common.search')); ?>..."
                                                autocomplete="off">
                                        </div>
                                    </div>

                                    
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="active" class="il-gray fs-14 fw-500 mb-10">
                                                <i class="uil uil-check-circle me-1"></i>
                                                <?php echo e(trans('shipping.status')); ?>

                                            </label>
                                            <select
                                                class="form-control ih-medium ip-gray radius-xs b-light px-15 form-select"
                                                id="active">
                                                <option value=""><?php echo e(trans('common.all')); ?></option>
                                                <option value="1"><?php echo e(trans('shipping.active')); ?></option>
                                                <option value="0"><?php echo e(trans('shipping.inactive')); ?></option>
                                            </select>
                                        </div>
                                    </div>

                                    
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="created_from_filter" class="il-gray fs-14 fw-500 mb-10">
                                                <i class="uil uil-calendar-alt me-1"></i>
                                                <?php echo e(trans('shipping.created_from')); ?>

                                            </label>
                                            <input type="date"
                                                class="form-control ih-medium ip-gray radius-xs b-light px-15"
                                                id="created_from_filter">
                                        </div>
                                    </div>

                                    
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="created_until_filter" class="il-gray fs-14 fw-500 mb-10">
                                                <i class="uil uil-calendar-alt me-1"></i>
                                                <?php echo e(trans('shipping.created_until')); ?>

                                            </label>
                                            <input type="date"
                                                class="form-control ih-medium ip-gray radius-xs b-light px-15"
                                                id="created_until_filter">
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
                                            class="btn btn-warning btn-default btn-squared me-1"
                                            title="<?php echo e(__('common.reset')); ?>">
                                            <i class="uil uil-redo me-1"></i>
                                            <?php echo e(__('common.reset_filters')); ?>

                                        </button>
                                    </div>

                                </div>
                            </div>
                        </div>
                    </div>

                    
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div class="d-flex align-items-center">
                            <label class="me-2 mb-0"><?php echo e(__('common.show')); ?></label>
                            <select id="entriesSelect" class="form-select form-select-sm" style="width: auto;">
                                <option value="10">10</option>
                                <option value="25">25</option>
                                <option value="50">50</option>
                                <option value="100">100</option>
                            </select>
                            <label class="ms-2 mb-0"><?php echo e(__('common.entries')); ?></label>
                        </div>
                    </div>

                    
                    <div class="table-responsive">
                        <table id="shippingsDataTable" class="table mb-0 table-bordered table-hover" style="width:100%">
                            <thead>
                                <tr class="userDatatable-header">
                                    <th class="text-center"><span class="userDatatable-title">#</span></th>
                                    <th><span class="userDatatable-title"><?php echo e(trans('shipping.name')); ?></span></th>
                                    <th><span class="userDatatable-title"><?php echo e(trans('shipping.cost')); ?></span></th>
                                    <th><span class="userDatatable-title"><?php echo e(trans('shipping.cities')); ?></span></th>
                                    <th><span class="userDatatable-title"><?php echo e(trans('shipping.categories')); ?></span></th>
                                    <th><span class="userDatatable-title"><?php echo e(trans('shipping.status')); ?></span></th>
                                    <th><span class="userDatatable-title"><?php echo e(trans('shipping.created_at')); ?></span></th>
                                    <th><span class="userDatatable-title"><?php echo e(__('common.actions')); ?></span></th>
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

<?php $__env->startPush('scripts'); ?>
    <script>
        $(document).ready(function() {
            let per_page = 10;

            // Populate filters from URL parameters on page load
            const urlParams = new URLSearchParams(window.location.search);
            if (urlParams.has('search')) $('#search').val(urlParams.get('search'));
            if (urlParams.has('active')) $('#active').val(urlParams.get('active'));
            if (urlParams.has('created_from')) $('#created_from_filter').val(urlParams.get('created_from'));
            if (urlParams.has('created_until')) $('#created_until_filter').val(urlParams.get('created_until'));

            // Server-side processing with pagination
            let table = $('#shippingsDataTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: '<?php echo e(route('admin.shippings.datatable')); ?>',
                    type: 'GET',
                    data: function(d) {
                        d.per_page = d.length;
                        d.page = (d.start / d.length) + 1;
                        d.search = $('#search').val();
                        d.active = $('#active').val() !== '' ? $('#active').val() : null;
                        d.created_date_from = $('#created_from_filter').val();
                        d.created_date_to = $('#created_until_filter').val();
                        return d;
                    }
                },
                columns: [
                    {
                        data: 'index',
                        name: 'index',
                        orderable: false,
                        searchable: false,
                        className: 'text-center fw-bold'
                    },
                    {
                        data: 'name',
                        name: 'name',
                        orderable: false,
                        searchable: true
                    },
                    {
                        data: 'cost',
                        name: 'cost',
                        orderable: false,
                        searchable: false,
                        render: function(data) {
                            return '<?php echo e(currency()); ?> ' + parseFloat(data).toFixed(2);
                        }
                    },
                    {
                        data: 'cities',
                        name: 'cities',
                        orderable: false,
                        searchable: false,
                        render: function(data, type, row) {
                            if (row.cities && row.cities.length > 0) {
                                let badges = row.cities.slice(0, 3).map(city => 
                                    `<span class="badge badge-sm" style="background-color: #0056B7; color: white; margin: 2px; padding: 4px 8px; border-radius: 4px; font-size: 11px;">${city.name}</span>`
                                ).join(' ');
                                
                                if (row.cities.length > 3) {
                                    let remainingCities = row.cities.slice(3).map(city => city.name).join(', ');
                                    badges += ` <span class="badge badge-sm" style="background-color: #6c757d; color: white; margin: 2px; padding: 4px 8px; border-radius: 4px; font-size: 11px;"  data-bs-toggle="tooltip" data-bs-placement="top" title="${remainingCities}">+${row.cities.length - 3}</span>`;
                                }
                                
                                return badges;
                            }
                            return '<span style="color: #999;">-</span>';
                        }
                    },
                    {
                        data: 'categories',
                        name: 'categories',
                        orderable: false,
                        searchable: false,
                        render: function(data, type, row) {
                            if (row.categories && row.categories.length > 0) {
                                let badges = row.categories.slice(0, 3).map(category => 
                                    `<span class="badge badge-sm" style="background-color: #9C27B0; color: white; margin: 2px; padding: 4px 8px; border-radius: 4px; font-size: 11px;">${category.name}</span>`
                                ).join(' ');
                                
                                if (row.categories.length > 3) {
                                    let remainingCategories = row.categories.slice(3).map(category => category.name).join(', ');
                                    badges += ` <span class="badge badge-sm" style="background-color: #6c757d; color: white; margin: 2px; padding: 4px 8px; border-radius: 4px; font-size: 11px;" data-bs-toggle="tooltip" data-bs-placement="top" title="${remainingCategories}">+${row.categories.length - 3}</span>`;
                                }
                                
                                return badges;
                            }
                            return '<span style="color: #999;">-</span>';
                        }
                    },
                    {
                        data: 'active',
                        name: 'active',
                        orderable: false,
                        searchable: false,
                        render: function(data, type, row) {
                            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('shippings.change-status')): ?>
                            const isChecked = data === 1 || data === true ? 'checked' : '';
                            const switchId = 'status-switch-' + row.id;
                            return `
                                <div class="form-check form-switch  form-switch-primary form-switch-sm">
                                    <input type="checkbox" class="form-check-input status-switcher" 
                                        id="${switchId}" data-id="${row.id}" ${isChecked}>
                                    <label class="form-check-label" for="${switchId}"></label>
                                </div>
                            `;
                            <?php else: ?>
                            if (data === 1 || data === true) {
                                return '<span class="badge badge-success badge-round badge-lg"><?php echo e(trans('shipping.active')); ?></span>';
                            } else {
                                return '<span class="badge badge-danger badge-round badge-lg"><?php echo e(trans('shipping.inactive')); ?></span>';
                            }
                            <?php endif; ?>
                        }
                    },
                    {
                        data: 'created_at',
                        name: 'created_at',
                        orderable: false,
                        searchable: false,
                        render: function(data) {
                            return data || '-';
                        }
                    },
                    {
                        data: null,
                        name: 'actions',
                        orderable: false,
                        searchable: false,
                        render: function(data, type, row) {
                            let editUrl = "<?php echo e(route('admin.shippings.edit', ':id')); ?>".replace(':id', row.id);
                            let showUrl = "<?php echo e(route('admin.shippings.show', ':id')); ?>".replace(':id', row.id);
                            return `
                                <div class="orderDatatable_actions d-inline-flex gap-1 justify-content-center">
                                    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('shippings.show')): ?>
                                    <a href="${showUrl}"
                                    class="view btn btn-primary table_action_father"
                                    title="<?php echo e(trans('common.view')); ?>">
                                        <i class="uil uil-eye table_action_icon"></i>
                                    </a>
                                    <?php endif; ?>
                                    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('shippings.edit')): ?>
                                    <a href="${editUrl}"
                                    class="edit btn btn-warning table_action_father"
                                    title="<?php echo e(trans('shipping.edit')); ?>">
                                        <i class="uil uil-edit table_action_icon"></i>
                                    </a>
                                    <?php endif; ?>
                                    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('shippings.delete')): ?>
                                    <button type="button"
                                    class="delete-btn btn btn-danger table_action_father"
                                    data-id="${row.id}"
                                    title="<?php echo e(trans('shipping.delete')); ?>">
                                        <i class="uil uil-trash table_action_icon"></i>
                                    </button>
                                    <?php endif; ?>
                                </div>
                            `;
                        }
                    }
                ],
                pageLength: per_page,
                lengthMenu: [[10, 25, 50, 100], [10, 25, 50, 100]],
                pagingType: 'full_numbers',
                dom: '<"row"<"col-sm-12"tr>>' +
                    '<"row mt-3"<"col-sm-12 col-md-5"i><"col-sm-12 col-md-7"p>>',
                language: {
                    search: "<?php echo e(__('common.search')); ?>:",
                }
            });

            $('#entriesSelect').on('change', function() {
                table.page.len($(this).val()).draw();
            });

            // Live search with debounce
            let searchTimer;
            $('#search').on('keyup', function() {
                clearTimeout(searchTimer);
                searchTimer = setTimeout(function() {
                    table.ajax.reload();
                    updateUrlParams();
                }, 500);
            });

            // Search button click
            $('#searchBtn').on('click', function() {
                table.ajax.reload();
                updateUrlParams();
            });

            // Filter change handlers
            $('#active, #created_from_filter, #created_until_filter').on('change', function() {
                table.ajax.reload();
                updateUrlParams();
            });

            // Reset filters
            $('#resetFilters').on('click', function() {
                $('#search').val('');
                $('#active').val('');
                $('#created_from_filter').val('');
                $('#created_until_filter').val('');
                table.ajax.reload();
                // Clear URL parameters
                window.history.replaceState({}, '', window.location.pathname);
            });

            // Update URL parameters function
            function updateUrlParams() {
                const params = new URLSearchParams();
                if ($('#search').val()) params.set('search', $('#search').val());
                if ($('#active').val()) params.set('active', $('#active').val());
                if ($('#created_from_filter').val()) params.set('created_from', $('#created_from_filter').val());
                if ($('#created_until_filter').val()) params.set('created_until', $('#created_until_filter').val());

                const newUrl = params.toString() ? `${window.location.pathname}?${params.toString()}` : window.location.pathname;
                window.history.replaceState({}, '', newUrl);
            }

            // Change shipping status
            $(document).on('change', '.status-switcher', function() {
                const switcher = $(this);
                const id = switcher.data('id');
                const newStatus = switcher.is(':checked') ? 1 : 0;

                switcher.prop('disabled', true);

                $.ajax({
                    url: "<?php echo e(route('admin.shippings.change-status', ':id')); ?>".replace(':id', id),
                    type: 'POST',
                    data: {
                        _token: '<?php echo e(csrf_token()); ?>',
                        status: newStatus
                    },
                    success: function(response) {
                        switcher.prop('disabled', false);
                        if (response.status) {
                            toastr.success(response.message);
                        } else {
                            toastr.error(response.message);
                            switcher.prop('checked', !switcher.is(':checked'));
                        }
                    },
                    error: function() {
                        switcher.prop('disabled', false);
                        switcher.prop('checked', !switcher.is(':checked'));
                        toastr.error('<?php echo e(trans('shipping.error_changing_status')); ?>');
                    }
                });
            });

            // Delete shipping
            $(document).on('click', '.delete-btn', function() {
                let id = $(this).data('id');
                if (confirm('<?php echo e(trans('shipping.confirm_delete')); ?>')) {
                    $.ajax({
                        url: "<?php echo e(route('admin.shippings.destroy', ':id')); ?>".replace(':id', id),
                        type: 'DELETE',
                        data: { _token: '<?php echo e(csrf_token()); ?>' },
                        success: function(response) {
                            toastr.success('<?php echo e(trans('shipping.deleted_successfully')); ?>');
                            table.ajax.reload();
                        },
                        error: function() {
                            toastr.error('<?php echo e(trans('shipping.error_deleting')); ?>');
                        }
                    });
                }
            });
        });
    </script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layout.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\eramo-multi-vendor\Modules/Order\resources/views/shippings/index.blade.php ENDPATH**/ ?>