
<?php $__env->startSection('title', trans('catalogmanagement::brand.brands_management')); ?>

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
                    ['title' => trans('catalogmanagement::brand.brands_management')],
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
                    ['title' => trans('catalogmanagement::brand.brands_management')],
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
                        <h4 class="mb-0 fw-500"><?php echo e(trans('catalogmanagement::brand.brands_management')); ?></h4>
                        <div class="d-flex gap-2">
                            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('brands.create')): ?>
                                <a href="<?php echo e(route('admin.brands.create')); ?>"
                                    class="btn btn-primary btn-default btn-squared text-capitalize">
                                    <i class="uil uil-plus"></i> <?php echo e(trans('catalogmanagement::brand.add_brand')); ?>

                                </a>
                            <?php endif; ?>
                        </div>
                    </div>

                    
                    <div class="alert alert-info glowing-alert" role="alert">
                        <?php echo e(__('common.live_search_info')); ?>

                    </div>

                    
                    <div class="mb-25">
                        <div class="card border-0 shadow-sm">
                            <div class="card-body">
                                <div class="row g-3 align-items-end">

                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="search" class="il-gray fs-14 fw-500 mb-10">
                                                <i class="uil uil-search me-1"></i> <?php echo e(trans('common.search')); ?>

                                            </label>
                                            <input type="text"
                                                class="form-control ih-medium ip-gray radius-xs b-light px-15"
                                                id="search"
                                                placeholder="<?php echo e(trans('catalogmanagement::brand.search_by_name')); ?>"
                                                autocomplete="off">
                                        </div>
                                    </div>

                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="active" class="il-gray fs-14 fw-500 mb-10">
                                                <i class="uil uil-check-circle me-1"></i>
                                                <?php echo e(trans('catalogmanagement::brand.activation')); ?>

                                            </label>
                                            <select
                                                class="form-control ih-medium ip-gray radius-xs b-light px-15 form-select"
                                                id="active">
                                                <option value=""><?php echo e(trans('catalogmanagement::brand.all')); ?></option>
                                                <option value="1"><?php echo e(trans('catalogmanagement::brand.active')); ?>

                                                </option>
                                                <option value="0"><?php echo e(trans('catalogmanagement::brand.inactive')); ?>

                                                </option>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="created_date_from" class="il-gray fs-14 fw-500 mb-10">
                                                <i class="uil uil-calendar-alt me-1"></i>
                                                <?php echo e(trans('common.created_date_from')); ?>

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
                                                <?php echo e(trans('common.created_date_to')); ?>

                                            </label>
                                            <input type="date"
                                                class="form-control ih-medium ip-gray radius-xs b-light px-15"
                                                id="created_date_to">
                                        </div>
                                    </div>

                                    <div class="col-md-12 d-flex align-items-center">
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

                    
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div class="d-flex align-items-center">
                            <label class="me-2 mb-0"><?php echo e(trans('common.show')); ?></label>
                            <select id="entriesSelect" class="form-select form-select-sm" style="width: auto;">
                                <option value="10">10</option>
                                <option value="25">25</option>
                                <option value="50">50</option>
                                <option value="100">100</option>
                            </select>
                            <label class="ms-2 mb-0"><?php echo e(trans('common.entries')); ?></label>
                        </div>
                    </div>

                    
                    <div class="table-responsive">
                        <table id="brandsDataTable" class="table mb-0 table-bordered table-hover" style="width:100%">
                            <thead>
                                <tr class="userDatatable-header">
                                    <th><span class="userDatatable-title">#</span></th>
                                    <th><span
                                            class="userDatatable-title"><?php echo e(trans('catalogmanagement::brand.logo')); ?></span>
                                    </th>
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $languages; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $language): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <th>
                                            <span class="userDatatable-title"
                                                <?php if($language->rtl): ?> dir="rtl" <?php endif; ?>>
                                                <?php echo e(trans('catalogmanagement::brand.name')); ?> (<?php echo e($language->name); ?>)
                                            </span>
                                        </th>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                    <th><span
                                            class="userDatatable-title"><?php echo e(trans('catalogmanagement::brand.activation')); ?></span>
                                    </th>
                                    <th><span
                                            class="userDatatable-title"><?php echo e(trans('catalogmanagement::brand.created_at')); ?></span>
                                    </th>
                                    <th><span class="userDatatable-title"><?php echo e(trans('common.actions')); ?></span></th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>

                </div>
            </div>
        </div>
    </div>
    
    <?php if (isset($component)) { $__componentOriginal4d4be0bcf29da35c820833c3b98d2b58 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal4d4be0bcf29da35c820833c3b98d2b58 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.delete-with-loading','data' => ['modalId' => 'modal-delete-brand','tableId' => 'brandsDataTable','deleteButtonClass' => 'delete-brand','title' => __('main.confirm delete'),'message' => __('main.are you sure you want to delete this'),'itemNameId' => 'delete-brand-name','confirmBtnId' => 'confirmDeleteBtn','cancelText' => __('main.cancel'),'deleteText' => __('main.delete'),'loadingDeleting' => trans('main.deleting') ?? 'Deleting...','loadingPleaseWait' => trans('main.please wait') ?? 'Please wait...','loadingDeletedSuccessfully' => trans('main.deleted success') ?? 'Deleted Successfully!','loadingRefreshing' => trans('main.refreshing') ?? 'Refreshing...','errorDeleting' => __('main.error on delete')]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('delete-with-loading'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['modalId' => 'modal-delete-brand','tableId' => 'brandsDataTable','deleteButtonClass' => 'delete-brand','title' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(__('main.confirm delete')),'message' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(__('main.are you sure you want to delete this')),'itemNameId' => 'delete-brand-name','confirmBtnId' => 'confirmDeleteBtn','cancelText' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(__('main.cancel')),'deleteText' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(__('main.delete')),'loadingDeleting' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(trans('main.deleting') ?? 'Deleting...'),'loadingPleaseWait' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(trans('main.please wait') ?? 'Please wait...'),'loadingDeletedSuccessfully' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(trans('main.deleted success') ?? 'Deleted Successfully!'),'loadingRefreshing' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(trans('main.refreshing') ?? 'Refreshing...'),'errorDeleting' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(__('main.error on delete'))]); ?>
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
            let per_page = 10;

            let viewRoute = '<?php echo e(route('admin.brands.show', ':id')); ?>';
            let editRoute = '<?php echo e(route('admin.brands.edit', ':id')); ?>';
            let deleteRoute = '<?php echo e(route('admin.brands.destroy', ':id')); ?>';
            // Server-side processing with pagination
            var table = $('#brandsDataTable').DataTable({
                processing: true,
                serverSide: true, // Server-side processing
                ajax: {
                    url: '<?php echo e(route('admin.brands.datatable')); ?>',
                    type: 'GET',
                    data: function(d) {
                        // Map DataTables parameters to backend parameters
                        d.per_page = d.length;
                        d.page = (d.start / d.length) + 1;

                        // Add search parameter from custom input
                        d.search = $('#search').val();

                        // Add filter parameters
                        d.active = $('#active').val();
                        d.created_date_from = $('#created_date_from').val();
                        d.created_date_to = $('#created_date_to').val();

                        console.log('📤 Sending to server:', {
                            search: d.search,
                            active: d.active,
                            created_date_from: d.created_date_from,
                            created_date_to: d.created_date_to
                        });

                        return d;
                    },
                    dataSrc: function(json) {
                        // Map backend response to DataTables format
                        json.recordsTotal = json.total || json.recordsTotal || 0;
                        json.recordsFiltered = json.recordsFiltered || json.total || 0;
                        return json.data || [];
                    },
                    error: function(xhr, error, code) {
                        console.log('DataTables Error:', xhr, error, code);
                        alert('Error loading data. Please check console for details.');
                    }
                },
                columns: [
                    // ID column
                    {
                        data: 'id',
                        name: 'id',
                        render: function(data) {
                            return data;
                        }
                    },
                    // Logo column
                    {
                        data: 'logo_path',
                        name: 'logo',
                        orderable: false,
                        searchable: false,
                        render: function(data) {
                            if (data) {
                                return '<img src="<?php echo e(asset('storage/')); ?>/' + data +
                                    '" alt="Logo" style="width: 50px; height: 50px; object-fit: contain;" />';
                            }
                            return '-';
                        }
                    },
                    // Name columns for each language
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $languages; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $language): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        {
                            data: 'translations.<?php echo e($language->code); ?>.name',
                            name: 'name_<?php echo e($language->code); ?>',
                            render: function(data, type, row) {
                                if (row.translations && row.translations[
                                        '<?php echo e($language->code); ?>']) {
                                    const translation = row.translations['<?php echo e($language->code); ?>'];
                                    const name = translation.name || '-';
                                    if (translation.rtl) {
                                        return '<span dir="rtl">' + $('<div>').text(name).html() +
                                            '</span>';
                                    }
                                    return $('<div>').text(name).html();
                                }
                                return '-';
                            }
                        },
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    // Active Status column
                    {
                        data: 'active',
                        name: 'active',
                        render: function(data) {
                            if (data == 1) {
                                return '<span class="badge badge-success badge-lg badge-round"><?php echo e(__('common.active')); ?></span>';
                            } else {
                                return '<span class="badge badge-danger badge-lg badge-round"><?php echo e(__('common.inactive')); ?></span>';
                            }
                        }
                    },
                    // Created At column
                    {
                        data: 'created_at',
                        name: 'created_at',
                        render: function(data) {
                            return data;
                        }
                    },
                    // Actions column
                    {
                        data: null,
                        name: 'actions',
                        orderable: false,
                        searchable: false,
                        render: function(data, type, row) {
                            return `
                                <div class="orderDatatable_actions d-inline-flex gap-1">
                                    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('brands.show')): ?>
                                    <a href="${viewRoute.replace(':id', row.id)}"
                                    class="view btn btn-primary table_action_father"
                                    title="<?php echo e(trans('common.view')); ?>">
                                        <i class="uil uil-eye table_action_icon"></i>
                                    </a>
                                    <?php endif; ?>

                                    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('brands.edit')): ?>
                                    <a href="${editRoute.replace(':id', row.id)}"
                                    class="edit btn btn-warning table_action_father"
                                    title="<?php echo e(trans('common.edit')); ?>">
                                        <i class="uil uil-edit table_action_icon"></i>
                                    </a>
                                    <?php endif; ?>

                                    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('brands.delete')): ?>
                                    <a href="javascript:void(0);"
                                    class="remove delete-brand btn btn-danger table_action_father"
                                    data-id="${row.id}"
                                    data-name="${row.translations?.<?php echo e(app()->getLocale()); ?>?.name || 'Brand'}"
                                    data-url="${deleteRoute.replace(':id', row.id)}"
                                    title="<?php echo e(trans('common.delete')); ?>">
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
                buttons: [{
                    extend: 'excel',
                    exportOptions: {
                        columns: ':not(:last-child)'
                    },
                    title: '<?php echo e(trans('catalogmanagement::brand.brands_management')); ?>'
                }],
                searching: true, // Enable built-in search
                language: {
                    lengthMenu: "<?php echo e(trans('common.show')); ?> _MENU_",
                    info: "<?php echo e(trans('common.showing')); ?> _START_ <?php echo e(trans('common.to')); ?> _END_ <?php echo e(trans('common.of')); ?> _TOTAL_ <?php echo e(trans('common.entries')); ?>",
                    infoEmpty: "<?php echo e(trans('common.showing')); ?> 0 <?php echo e(trans('common.to')); ?> 0 <?php echo e(trans('common.of')); ?> 0 <?php echo e(trans('common.entries')); ?>",
                    infoFiltered: "(<?php echo e(trans('common.filtered_from')); ?> _MAX_ <?php echo e(trans('common.total_entries')); ?>)",
                    zeroRecords: "<?php echo e(trans('catalogmanagement::brand.no_brands_found')); ?>",
                    emptyTable: "<?php echo e(trans('catalogmanagement::brand.no_brands_found')); ?>",
                    loadingRecords: "<?php echo e(trans('common.loading')); ?>...",
                    processing: "<?php echo e(trans('common.processing')); ?>...",
                    search: "<?php echo e(trans('common.search')); ?>:",
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
                    },
                    aria: {
                        sortAscending: ": <?php echo e(trans('common.sort_ascending')); ?>",
                        sortDescending: ": <?php echo e(trans('common.sort_descending')); ?>"
                    }
                }
            });

            // Initialize Select2 on custom entries select
            if ($.fn.select2) {
                $('#entriesSelect').select2({
                    theme: 'bootstrap-5',
                    minimumResultsForSearch: Infinity,
                    width: 'auto'
                });
            }

            // Handle entries select change
            $('#entriesSelect').on('change', function() {
                table.page.len($(this).val()).draw();
            });

            // Handle Excel export button
            $('#exportExcel').on('click', function() {
                table.button('.buttons-excel').trigger();
            });

            // Search with server-side processing and debounce
            let searchTimer;
            $('#search').on('keyup', function() {
                clearTimeout(searchTimer);
                searchTimer = setTimeout(function() {
                    console.log('🔍 Search triggered:', $('#search').val());
                    table.ajax.reload(); // Reload data from server with new search value
                }, 500);
            });

            $('#search').on('change', function() {
                clearTimeout(searchTimer);
                console.log('🔍 Search changed:', $(this).val());
                table.ajax.reload();
            });

            // Server-side filter event listeners - reload data when filters change
            $('#active, #created_date_from, #created_date_to').on('change', function() {
                console.log('Filter changed:', $(this).attr('id'), '=', $(this).val());
                table.ajax.reload();
            });

            // Reset filters button
            $('#resetFilters').on('click', function() {
                console.log('Resetting all filters...');
                // Clear all filter inputs
                $('#search').val('');
                $('#active').val('');
                $('#created_date_from').val('');
                $('#created_date_to').val('');
                // Clear search and reload table
                table.search('').ajax.reload();
            });

            // Delete functionality is now handled by the delete-with-loading component
        });
    </script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layout.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\eramo-multi-vendor\Modules/CatalogManagement\resources/views/brand/index.blade.php ENDPATH**/ ?>