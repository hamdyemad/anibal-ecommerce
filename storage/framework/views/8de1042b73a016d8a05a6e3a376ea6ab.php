

<?php $__env->startSection('title'); ?>
    <?php echo e(__('systemsetting::ads.ads_management')); ?>

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
                    ['title' => __('systemsetting::ads.ads_management')],
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
                    ['title' => __('systemsetting::ads.ads_management')],
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
                        <h4 class="mb-0 fw-500 fw-bold"><?php echo e(__('systemsetting::ads.ads_management')); ?></h4>
                        <div class="d-flex gap-2">
                            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('ads.create')): ?>
                            <a href="<?php echo e(route('admin.system-settings.ads.create')); ?>"
                                class="btn btn-primary btn-default btn-squared text-capitalize">
                                <i class="uil uil-plus"></i> <?php echo e(__('systemsetting::ads.add_ad')); ?>

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
                                                <i class="uil uil-search me-1"></i> <?php echo e(__('systemsetting::ads.search')); ?>

                                                <small class="text-muted">(<?php echo e(__('systemsetting::ads.real_time')); ?>)</small>
                                            </label>
                                            <input type="text"
                                                class="form-control ih-medium ip-gray radius-xs b-light px-15"
                                                id="search"
                                                placeholder="<?php echo e(__('systemsetting::ads.search_placeholder')); ?>"
                                                autocomplete="off">
                                        </div>
                                    </div>

                                    
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="type" class="il-gray fs-14 fw-500 mb-10">
                                                <i class="uil uil-layers me-1"></i>
                                                <?php echo e(__('systemsetting::ads.type')); ?>

                                            </label>
                                            <select
                                                class="form-control ih-medium ip-gray radius-xs b-light px-15 form-select"
                                                id="type">
                                                <option value="">
                                                    <?php echo e(__('systemsetting::ads.all_types') ?? 'All Types'); ?></option>
                                                <option value="mobile"><?php echo e(__('systemsetting::ads.mobile')); ?></option>
                                                <option value="website"><?php echo e(__('systemsetting::ads.website')); ?></option>
                                            </select>
                                        </div>
                                    </div>

                                    
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="position" class="il-gray fs-14 fw-500 mb-10">
                                                <i class="uil uil-map-pin me-1"></i>
                                                <?php echo e(__('systemsetting::ads.position')); ?>

                                            </label>
                                            <select
                                                class="form-control ih-medium ip-gray radius-xs b-light px-15 form-select"
                                                id="position">
                                                <option value=""><?php echo e(__('systemsetting::ads.all_positions')); ?>

                                                </option>
                                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $positions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                    <option value="<?php echo e($key); ?>"><?php echo e($value); ?></option>
                                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                            </select>
                                        </div>
                                    </div>

                                    
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="active" class="il-gray fs-14 fw-500 mb-10">
                                                <i class="uil uil-check-circle me-1"></i>
                                                <?php echo e(__('systemsetting::ads.status')); ?>

                                            </label>
                                            <select
                                                class="form-control ih-medium ip-gray radius-xs b-light px-15 form-select"
                                                id="active">
                                                <option value=""><?php echo e(__('systemsetting::ads.all_status')); ?></option>
                                                <option value="1"><?php echo e(__('systemsetting::ads.active')); ?></option>
                                                <option value="0"><?php echo e(__('systemsetting::ads.inactive')); ?></option>
                                            </select>
                                        </div>
                                    </div>

                                    
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="created_date_from" class="il-gray fs-14 fw-500 mb-10">
                                                <i class="uil uil-calendar-alt me-1"></i>
                                                <?php echo e(__('systemsetting::ads.created_from')); ?>

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
                                                <?php echo e(__('systemsetting::ads.created_to')); ?>

                                            </label>
                                            <input type="date"
                                                class="form-control ih-medium ip-gray radius-xs b-light px-15"
                                                id="created_date_to">
                                        </div>
                                    </div>

                                    <div class="col-md-12 d-flex align-items-center">
                                        <button type="button" id="searchBtn"
                                            class="btn btn-success btn-default btn-squared me-1"
                                            title="<?php echo e(__('systemsetting::ads.search')); ?>">
                                            <i class="uil uil-search me-1"></i>
                                            <?php echo e(__('systemsetting::ads.search')); ?>

                                        </button>
                                        <button type="button" id="resetFilters"
                                            class="btn btn-warning btn-default btn-squared me-1"
                                            title="<?php echo e(__('systemsetting::ads.reset_filters')); ?>">
                                            <i class="uil uil-redo me-1"></i>
                                            <?php echo e(__('systemsetting::ads.reset_filters')); ?>

                                        </button>
                                    </div>

                                </div>
                            </div>
                        </div>
                    </div>

                    
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div class="d-flex align-items-center">
                            <label class="me-2 mb-0"><?php echo e(__('systemsetting::ads.show')); ?></label>
                            <select id="entriesSelect" class="form-select form-select-sm" style="width: auto;">
                                <option value="10">10</option>
                                <option value="25">25</option>
                                <option value="50">50</option>
                                <option value="100">100</option>
                            </select>
                            <label class="ms-2 mb-0"><?php echo e(__('systemsetting::ads.entries')); ?></label>
                        </div>
                    </div>

                    
                    <div class="table-responsive">
                        <table id="adsDataTable" class="table mb-0 table-bordered table-hover" style="width:100%">
                            <thead>
                                <tr class="userDatatable-header">
                                    <th class="text-center"><span class="userDatatable-title">#</span></th>
                                    <th><span class="userDatatable-title"><?php echo e(__('systemsetting::ads.title')); ?></span></th>
                                    <th><span class="userDatatable-title"><?php echo e(__('systemsetting::ads.position')); ?></span>
                                    </th>
                                    <th><span class="userDatatable-title"><?php echo e(__('systemsetting::ads.type')); ?></span>
                                    </th>
                                    <th><span class="userDatatable-title"><?php echo e(__('systemsetting::ads.ad_image')); ?></span>
                                    </th>
                                    <th><span class="userDatatable-title"><?php echo e(__('systemsetting::ads.status')); ?></span>
                                    </th>
                                    <th><span class="userDatatable-title"><?php echo e(__('systemsetting::ads.created_at')); ?></span>
                                    </th>
                                    <th><span class="userDatatable-title"><?php echo e(__('systemsetting::ads.action')); ?></span>
                                    </th>
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
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.delete-modal','data' => ['modalId' => 'modal-delete-ad','title' => __('systemsetting::ads.confirm_delete'),'message' => __('systemsetting::ads.delete_confirmation'),'itemNameId' => 'delete-ad-name','confirmBtnId' => 'confirmDeleteAdBtn','deleteRoute' => ''.e(rtrim(route('admin.system-settings.ads.index'), '/')).'','cancelText' => __('systemsetting::ads.cancel'),'deleteText' => __('systemsetting::ads.delete_ad')]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('delete-modal'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['modalId' => 'modal-delete-ad','title' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(__('systemsetting::ads.confirm_delete')),'message' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(__('systemsetting::ads.delete_confirmation')),'itemNameId' => 'delete-ad-name','confirmBtnId' => 'confirmDeleteAdBtn','deleteRoute' => ''.e(rtrim(route('admin.system-settings.ads.index'), '/')).'','cancelText' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(__('systemsetting::ads.cancel')),'deleteText' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(__('systemsetting::ads.delete_ad'))]); ?>
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
            // Permission flags
            const canToggleStatus = <?php echo json_encode(auth()->user()->can('ads.toggle-status'), 15, 512) ?>;
            
            // Get URL parameters and populate filters
            const urlParams = new URLSearchParams(window.location.search);
            if (urlParams.has('search')) {
                $('#search').val(urlParams.get('search'));
            }
            if (urlParams.has('position')) {
                $('#position').val(urlParams.get('position'));
            }
            if (urlParams.has('active')) {
                $('#active').val(urlParams.get('active'));
            }
            if (urlParams.has('type')) {
                $('#type').val(urlParams.get('type'));
            }
            if (urlParams.has('created_date_from')) {
                $('#created_date_from').val(urlParams.get('created_date_from'));
            }
            if (urlParams.has('created_date_to')) {
                $('#created_date_to').val(urlParams.get('created_date_to'));
            }

            let table = $('#adsDataTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: '<?php echo e(route('admin.system-settings.ads.datatable')); ?>',
                    type: 'GET',
                    data: function(d) {
                        d.type = $('#type').val();
                        d.position = $('#position').val();
                        d.active = $('#active').val();
                        d.search = $('#search').val();
                        d.created_date_from = $('#created_date_from').val();
                        d.created_date_to = $('#created_date_to').val();
                    }
                },
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'title_subtitle',
                        name: 'title_subtitle',
                        orderable: false
                    },
                    {
                        data: 'position_badge',
                        name: 'position_badge',
                        orderable: false
                    },
                    {
                        data: 'type_badge',
                        name: 'type_badge',
                        orderable: false
                    },
                    {
                        data: 'image_preview',
                        name: 'image_preview',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'active',
                        name: 'active',
                        orderable: false,
                        render: function(data, type, row) {
                            if (canToggleStatus) {
                                const isChecked = data === 1 || data === true ? 'checked' : '';
                                const switchId = 'status-switch-' + row.id;
                                return `
                            <div class="form-check form-switch form-switch-primary form-switch-sm d-flex justify-content-center">
                                <input type="checkbox" class="form-check-input status-switcher" 
                                    id="${switchId}" data-id="${row.id}" ${isChecked}>
                                <label class="form-check-label" for="${switchId}"></label>
                            </div>
                        `;
                            } else {
                                if (data === 1 || data === true) {
                                    return '<span class="badge badge-round badge-lg badge-success"><?php echo e(__('systemsetting::ads.active')); ?></span>';
                                }
                                return '<span class="badge badge-round badge-lg badge-danger"><?php echo e(__('systemsetting::ads.inactive')); ?></span>';
                            }
                        }
                    },
                    {
                        data: 'created_date',
                        name: 'created_date',
                        orderable: false
                    },
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false
                    }
                ],
                pageLength: 10,
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
                searching: false,
                language: {
                    lengthMenu: "<?php echo e(__('systemsetting::ads.show')); ?> _MENU_",
                    info: "<?php echo e(__('systemsetting::ads.showing_entries')); ?>",
                    infoEmpty: "<?php echo e(__('systemsetting::ads.showing_empty')); ?>",
                    emptyTable: "<?php echo e(__('systemsetting::ads.no_data_available')); ?>",
                    zeroRecords: "<?php echo e(__('systemsetting::ads.no_ads_found')); ?>",
                    loadingRecords: "<?php echo e(__('systemsetting::ads.loading')); ?>",
                    processing: "<?php echo e(__('systemsetting::ads.processing')); ?>",
                    search: "<?php echo e(__('systemsetting::ads.search')); ?>:",
                }
            });

            // Initialize Select2
            if ($.fn.select2) {
                $('#entriesSelect, #type, #position, #active').select2({
                    theme: 'bootstrap-5',
                    minimumResultsForSearch: Infinity,
                    width: '100%'
                });
            }

            $('#entriesSelect').on('change', function() {
                table.page.len($(this).val()).draw();
            });

            // Function to update URL with filter parameters
            function updateUrlWithFilters() {
                const params = new URLSearchParams();
                const search = $('#search').val();
                const type = $('#type').val();
                const position = $('#position').val();
                const active = $('#active').val();
                const createdDateFrom = $('#created_date_from').val();
                const createdDateTo = $('#created_date_to').val();

                if (search) params.set('search', search);
                if (type) params.set('type', type);
                if (position) params.set('position', position);
                if (active) params.set('active', active);
                if (createdDateFrom) params.set('created_date_from', createdDateFrom);
                if (createdDateTo) params.set('created_date_to', createdDateTo);

                const newUrl = window.location.pathname + (params.toString() ? '?' + params.toString() : '');
                window.history.pushState({}, '', newUrl);
            }

            // Search button functionality
            $('#searchBtn').on('click', function() {
                console.log('Search button clicked, updating URL and reloading table...');
                updateUrlWithFilters();
                table.draw();
            });

            // Search input with debounce
            let searchTimer;
            $('#search').on('keyup', function() {
                clearTimeout(searchTimer);
                const searchValue = $(this).val();
                searchTimer = setTimeout(function() {
                    updateUrlWithFilters();
                    table.draw();
                }, 500);
            });

            // Filter change handlers
            $('#position, #type, #active, #created_date_from, #created_date_to').on('change', function() {
                updateUrlWithFilters();
                table.draw();
            });

            // Reset filters button
            $('#resetFilters').on('click', function() {
                console.log('Resetting all filters...');
                $('#search').val('');
                $('#position').val('').trigger('change');
                $('#active').val('').trigger('change');
                $('#created_date_from').val('');
                $('#created_date_to').val('');
                updateUrlWithFilters();
                table.draw();
            });

            // Toggle status
            $(document).on('change', '.status-switcher', function() {
                const switcher = $(this);
                const id = switcher.data('id');
                const newStatus = switcher.is(':checked') ? 1 : 0;

                switcher.prop('disabled', true);

                $.ajax({
                    url: "<?php echo e(route('admin.system-settings.ads.toggle-status', ':id')); ?>".replace(
                        ':id', id),
                    type: 'POST',
                    data: {
                        _token: '<?php echo e(csrf_token()); ?>',
                        status: newStatus
                    },
                    success: function(response) {
                        switcher.prop('disabled', false);
                        if (response.success) {
                            toastr.success(response.message);
                        } else {
                            toastr.error(response.message);
                            switcher.prop('checked', !switcher.is(':checked'));
                        }
                    },
                    error: function(xhr) {
                        switcher.prop('disabled', false);
                        switcher.prop('checked', !switcher.is(':checked'));
                        const message = xhr.responseJSON ? xhr.responseJSON.message :
                            'Error changing status';
                        toastr.error(message);
                    }
                });
            });

            // Reload table after successful delete
            $('#modal-delete-ad').on('hidden.bs.modal', function() {
                // Check if delete was successful and reload table
                if (window.deleteSuccess) {
                    table.draw();
                    window.deleteSuccess = false;
                }
            });
        });
    </script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layout.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\eramo-multi-vendor\Modules/SystemSetting\resources/views/ads/index.blade.php ENDPATH**/ ?>