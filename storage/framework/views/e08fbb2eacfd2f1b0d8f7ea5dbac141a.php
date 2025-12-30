
<?php $__env->startSection('title'); ?>
    <?php echo e(__('catalogmanagement::promocodes.title')); ?> | Bnaia
<?php $__env->stopSection(); ?>
<?php $__env->startPush('styles'); ?>
    <!-- Select2 CSS loaded via Vite -->
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
                    ['title' => __('catalogmanagement::promocodes.title')],
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
                    ['title' => __('catalogmanagement::promocodes.title')],
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
                        <h4 class="mb-0 fw-500 fw-bold"><?php echo e(__('catalogmanagement::promocodes.title')); ?></h4>
                        <div class="d-flex gap-2">
                            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('promocodes.create')): ?>
                                <a href="<?php echo e(route('admin.promocodes.create')); ?>"
                                    class="btn btn-primary btn-default btn-squared text-capitalize">
                                    <i class="uil uil-plus"></i> <?php echo e(__('catalogmanagement::promocodes.add_promocode')); ?>

                                </a>
                            <?php endif; ?>
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
                                                id="search" placeholder="<?php echo e(__('common.search')); ?>..."
                                                autocomplete="off">
                                        </div>
                                    </div>

                                    
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="active" class="il-gray fs-14 fw-500 mb-10">
                                                <i class="uil uil-check-circle me-1"></i>
                                                <?php echo e(__('catalogmanagement::promocodes.status')); ?>

                                            </label>
                                            <select
                                                class="form-control ih-medium ip-gray radius-xs b-light px-15 form-select"
                                                id="active">
                                                <option value=""><?php echo e(__('catalogmanagement::promocodes.all_status')); ?>

                                                </option>
                                                <option value="1"><?php echo e(__('catalogmanagement::promocodes.active')); ?>

                                                </option>
                                                <option value="0"><?php echo e(__('catalogmanagement::promocodes.inactive')); ?>

                                                </option>
                                            </select>
                                        </div>
                                    </div>

                                    
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="valid_from_filter" class="il-gray fs-14 fw-500 mb-10">
                                                <i class="uil uil-calendar-alt me-1"></i>
                                                <?php echo e(__('catalogmanagement::promocodes.valid_from')); ?>

                                            </label>
                                            <input type="date"
                                                class="form-control ih-medium ip-gray radius-xs b-light px-15"
                                                id="valid_from_filter">
                                        </div>
                                    </div>

                                    
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="valid_until_filter" class="il-gray fs-14 fw-500 mb-10">
                                                <i class="uil uil-calendar-alt me-1"></i>
                                                <?php echo e(__('catalogmanagement::promocodes.valid_until')); ?>

                                            </label>
                                            <input type="date"
                                                class="form-control ih-medium ip-gray radius-xs b-light px-15"
                                                id="valid_until_filter">
                                        </div>
                                    </div>

                                    
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="type_filter" class="il-gray fs-14 fw-500 mb-10">
                                                <i class="uil uil-percentage me-1"></i>
                                                <?php echo e(__('catalogmanagement::promocodes.type')); ?>

                                            </label>
                                            <select
                                                class="form-control ih-medium ip-gray radius-xs b-light px-15 form-select"
                                                id="type_filter">
                                                <option value=""><?php echo e(__('common.all')); ?></option>
                                                <option value="percent">
                                                    <?php echo e(__('catalogmanagement::promocodes.types.percent')); ?></option>
                                                <option value="amount">
                                                    <?php echo e(__('catalogmanagement::promocodes.types.amount')); ?></option>
                                            </select>
                                        </div>
                                    </div>

                                    
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="dedicated_to_filter" class="il-gray fs-14 fw-500 mb-10">
                                                <i class="uil uil-users-alt me-1"></i>
                                                <?php echo e(__('catalogmanagement::promocodes.dedicated_to')); ?>

                                            </label>
                                            <select
                                                class="form-control ih-medium ip-gray radius-xs b-light px-15 form-select"
                                                id="dedicated_to_filter">
                                                <option value=""><?php echo e(__('common.all')); ?></option>
                                                <option value="all">
                                                    <?php echo e(__('catalogmanagement::promocodes.dedicated_options.all')); ?>

                                                </option>
                                                <option value="male">
                                                    <?php echo e(__('catalogmanagement::promocodes.dedicated_options.male')); ?>

                                                </option>
                                                <option value="female">
                                                    <?php echo e(__('catalogmanagement::promocodes.dedicated_options.female')); ?>

                                                </option>
                                            </select>
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
                        <table id="promocodesDataTable" class="table mb-0 table-bordered table-hover" style="width:100%">
                            <thead>
                                <tr class="userDatatable-header">
                                    <th class="text-center"><span class="userDatatable-title">#</span></th>
                                    <th><span
                                            class="userDatatable-title"><?php echo e(__('catalogmanagement::promocodes.code')); ?></span>
                                    </th>
                                    <th><span
                                            class="userDatatable-title"><?php echo e(__('catalogmanagement::promocodes.type')); ?></span>
                                    </th>
                                    <th><span
                                            class="userDatatable-title"><?php echo e(__('catalogmanagement::promocodes.value')); ?></span>
                                    </th>
                                    <th><span
                                            class="userDatatable-title"><?php echo e(__('catalogmanagement::promocodes.valid_from')); ?></span>
                                    </th>
                                    <th><span
                                            class="userDatatable-title"><?php echo e(__('catalogmanagement::promocodes.valid_until')); ?></span>
                                    </th>
                                    <th><span
                                            class="userDatatable-title"><?php echo e(__('catalogmanagement::promocodes.dedicated_to')); ?></span>
                                    </th>
                                    <th><span
                                            class="userDatatable-title"><?php echo e(__('catalogmanagement::promocodes.status')); ?></span>
                                    </th>
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
    
    <?php if (isset($component)) { $__componentOriginalb7eac87efb73c0c2c26fe03ec80faafd = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalb7eac87efb73c0c2c26fe03ec80faafd = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.delete-modal','data' => ['modalId' => 'modal-delete-promocode','title' => __('catalogmanagement::promocodes.confirm_delete'),'message' => __('catalogmanagement::promocodes.delete_confirmation'),'itemNameId' => 'delete-promocode-name','confirmBtnId' => 'confirmDeletePromocodeBtn','deleteRoute' => route('admin.promocodes.index'),'cancelText' => __('catalogmanagement::promocodes.cancel'),'deleteText' => __('catalogmanagement::promocodes.delete_promocode')]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('delete-modal'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['modalId' => 'modal-delete-promocode','title' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(__('catalogmanagement::promocodes.confirm_delete')),'message' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(__('catalogmanagement::promocodes.delete_confirmation')),'itemNameId' => 'delete-promocode-name','confirmBtnId' => 'confirmDeletePromocodeBtn','deleteRoute' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(route('admin.promocodes.index')),'cancelText' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(__('catalogmanagement::promocodes.cancel')),'deleteText' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(__('catalogmanagement::promocodes.delete_promocode'))]); ?>
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
            let per_page = 10;

            // Populate filters from URL parameters on page load
            const urlParams = new URLSearchParams(window.location.search);
            if (urlParams.has('search')) $('#search').val(urlParams.get('search'));
            if (urlParams.has('active')) $('#active').val(urlParams.get('active'));
            if (urlParams.has('type')) $('#type_filter').val(urlParams.get('type'));
            if (urlParams.has('dedicated_to')) $('#dedicated_to_filter').val(urlParams.get('dedicated_to'));
            if (urlParams.has('valid_from')) $('#valid_from_filter').val(urlParams.get('valid_from'));
            if (urlParams.has('valid_until')) $('#valid_until_filter').val(urlParams.get('valid_until'));

            // Server-side processing with pagination
            let table = $('#promocodesDataTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: '<?php echo e(route('admin.promocodes.datatable')); ?>',
                    type: 'GET',
                    data: function(d) {
                        d.per_page = d.length;
                        d.page = (d.start / d.length) + 1;
                        d.search = $('#search').val();
                        d.active = $('#active').val();
                        d.type = $('#type_filter').val();
                        d.dedicated_to = $('#dedicated_to_filter').val();
                        d.valid_from = $('#valid_from_filter').val();
                        d.valid_until = $('#valid_until_filter').val();
                        return d;
                    }
                },
                columns: [{
                        data: 'index',
                        name: 'index',
                        orderable: false,
                        searchable: false,
                        className: 'text-center fw-bold'
                    },
                    {
                        data: 'code',
                        name: 'code'
                    },
                    {
                        data: 'type',
                        name: 'type',
                        render: function(data) {
                            const types = <?php echo json_encode(__('catalogmanagement::promocodes.types'), 15, 512) ?>;
                            return types[data] || data;
                        }
                    },
                    {
                        data: 'value',
                        name: 'value'
                    },
                    {
                        data: 'valid_from',
                        name: 'valid_from'
                    },
                    {
                        data: 'valid_until',
                        name: 'valid_until'
                    },
                    {
                        data: 'dedicated_to',
                        name: 'dedicated_to',
                        render: function(data) {
                            const options = <?php echo json_encode(__('catalogmanagement::promocodes.dedicated_options'), 15, 512) ?>;
                            return options[data] || data;
                        }
                    },
                    {
                        data: 'is_active',
                        name: 'is_active',
                        orderable: false,
                        render: function(data, type, row) {
                            const isChecked = data ? 'checked' : '';
                            const switchId = 'status-switch-' + row.id;
                            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('promocodes.change-status')): ?>
                            return `<div class="userDatatable-content">
                                <div class="form-switch">
                                    <input class="form-check-input status-switcher"
                                           type="checkbox"
                                           id="${switchId}"
                                           data-id="${row.id}"
                                           ${isChecked}
                                           style="cursor: pointer;">
                                    <label class="form-check-label" for="${switchId}"></label>
                                </div>
                            </div>`;
                            <?php else: ?>
                            if (data) {
                                return `<span class="badge badge-success badge-round badge-lg"><?php echo e(__('catalogmanagement::promocodes.active')); ?></span>`;
                            } else {
                                return `<span class="badge badge-secondary badge-round badge-lg"><?php echo e(__('catalogmanagement::promocodes.inactive')); ?></span>`;
                            }
                            <?php endif; ?>
                        }
                    },
                    {
                        data: null,
                        name: 'actions',
                        orderable: false,
                        searchable: false,
                        render: function(data, type, row) {
                            let viewUrl = "<?php echo e(route('admin.promocodes.show', ':id')); ?>".replace(
                                ':id', row.id)
                            let editUrl = "<?php echo e(route('admin.promocodes.edit', ':id')); ?>".replace(
                                ':id', row.id)
                            return `
                                <div class="orderDatatable_actions d-inline-flex gap-1">
                                    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('promocodes.show')): ?>
                                    <a href="${viewUrl}"
                                    class="view btn btn-primary table_action_father"
                                    title="<?php echo e(__('catalogmanagement::promocodes.view_promocode')); ?>">
                                        <i class="uil uil-eye table_action_icon"></i>
                                    </a>
                                    <?php endif; ?>

                                    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('promocodes.edit')): ?>
                                    <a href="${editUrl}"
                                    class="edit btn btn-warning table_action_father"
                                    title="<?php echo e(__('catalogmanagement::promocodes.edit_promocode')); ?>">
                                        <i class="uil uil-edit table_action_icon"></i>
                                    </a>
                                    <?php endif; ?>

                                    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('promocodes.delete')): ?>
                                    <a href="javascript:void(0);"
                                    class="remove delete-promocode btn btn-danger table_action_father"
                                    data-bs-toggle="modal"
                                    data-bs-target="#modal-delete-promocode"
                                    data-item-id="${row.id}"
                                    data-item-name="${row.code}"
                                    title="<?php echo e(__('catalogmanagement::promocodes.delete_promocode')); ?>">
                                        <i class="uil uil-trash-alt table_action_icon"></i>
                                    </a>
                                    <?php endif; ?>
                                </div>
                            `;
                        }
                    }
                ],
                pageLength: per_page,
                lengthMenu: [
                    [10, 25, 50, 100],
                    [10, 25, 50, 100]
                ],
                order: [
                    [0, 'desc']
                ],
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
            $('#active, #type_filter, #dedicated_to_filter, #valid_from_filter, #valid_until_filter').on('change',
                function() {
                    table.ajax.reload();
                    updateUrlParams();
                });

            // Reset filters
            $('#resetFilters').on('click', function() {
                $('#search').val('');
                $('#active').val('');
                $('#type_filter').val('');
                $('#dedicated_to_filter').val('');
                $('#valid_from_filter').val('');
                $('#valid_until_filter').val('');
                table.ajax.reload();
                // Clear URL parameters
                window.history.replaceState({}, '', window.location.pathname);
            });

            // Update URL parameters function
            function updateUrlParams() {
                const params = new URLSearchParams();
                if ($('#search').val()) params.set('search', $('#search').val());
                if ($('#active').val()) params.set('active', $('#active').val());
                if ($('#type_filter').val()) params.set('type', $('#type_filter').val());
                if ($('#dedicated_to_filter').val()) params.set('dedicated_to', $('#dedicated_to_filter').val());
                if ($('#valid_from_filter').val()) params.set('valid_from', $('#valid_from_filter').val());
                if ($('#valid_until_filter').val()) params.set('valid_until', $('#valid_until_filter').val());

                const newUrl = params.toString() ? `${window.location.pathname}?${params.toString()}` : window
                    .location.pathname;
                window.history.replaceState({}, '', newUrl);
            }

            $(document).on('change', '.status-switcher', function() {
                const switcher = $(this);
                const id = switcher.data('id');
                const newStatus = switcher.is(':checked') ? 1 : 0;

                switcher.prop('disabled', true);

                // Show loading overlay for status change
                if (typeof LoadingOverlay !== 'undefined') {
                    LoadingOverlay.show({
                        text: '<?php echo e(__('catalogmanagement::promocodes.messages.status_changed')); ?>',
                        subtext: '<?php echo e(__('common.please_wait')); ?>...'
                    });
                }
                let url = "<?php echo e(route('admin.promocodes.change-status', ':id')); ?>".replace(':id', id)
                $.ajax({
                    url: url,
                    type: 'POST',
                    data: {
                        _token: '<?php echo e(csrf_token()); ?>',
                        status: newStatus
                    },
                    success: function(response) {
                        if (typeof LoadingOverlay !== 'undefined') {
                            LoadingOverlay.hide();
                        }
                        switcher.prop('disabled', false);
                        // Optional: Toast
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
                        }
                    },
                    error: function(xhr) {
                        if (typeof LoadingOverlay !== 'undefined') {
                            LoadingOverlay.hide();
                        }
                        switcher.prop('disabled', false);
                        switcher.prop('checked', !switcher.is(':checked'));
                        alert('Error changing status');
                    }
                });
            });
        });
    </script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layout.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\eramo-multi-vendor\Modules/CatalogManagement\resources/views/promocodes/index.blade.php ENDPATH**/ ?>