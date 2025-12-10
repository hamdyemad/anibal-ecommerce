@extends('layout.app')
@section('title', trans('systemsetting::points.user_points_management'))

@push('styles')
    <style>
        .user-info-cell {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .user-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            object-fit: cover;
            background-color: #f0f0f0;
        }

        .user-details {
            display: flex;
            flex-direction: column;
            gap: 2px;
        }

        .user-name {
            font-weight: 600;
            color: #333;
            font-size: 13px;
        }

        .user-email {
            font-size: 12px;
            color: #999;
        }

        .points-badge {
            display: inline-block;
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 12px;
            font-weight: 600;
            text-align: center;
            min-width: 80px;
        }

        .points-total {
            background-color: #e3f2fd;
            color: #1976d2;
        }

        .points-earned {
            background-color: #e8f5e9;
            color: #388e3c;
        }

        .points-redeemed {
            background-color: #fff3e0;
            color: #f57c00;
        }

        .points-expired {
            background-color: #ffebee;
            color: #c62828;
        }

        .action-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 32px;
            height: 32px;
            border-radius: 50%;
            border: none;
            cursor: pointer;
            transition: all 0.3s ease;
            font-size: 16px;
        }

        .action-btn-points {
            background-color: #e3f2fd;
            color: #1976d2;
        }

        .action-btn-points:hover {
            background-color: #1976d2;
            color: white;
        }

        .filter-section {
            background-color: #f8f9fa;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
        }

        .filter-group {
            display: flex;
            gap: 10px;
            align-items: center;
            flex-wrap: wrap;
        }

        .filter-input {
            flex: 1;
            min-width: 200px;
        }

        .filter-btn {
            padding: 8px 16px;
            border-radius: 6px;
            border: none;
            cursor: pointer;
            font-weight: 500;
            transition: all 0.3s ease;
        }

        .filter-btn-search {
            background-color: var(--color-primary);
            color: white;
        }

        .filter-btn-search:hover {
            opacity: 0.9;
        }

        .filter-btn-reset {
            background-color: #e0e0e0;
            color: #333;
        }

        .filter-btn-reset:hover {
            background-color: #d0d0d0;
        }
    </style>
@endpush

@section('content')
    <div class="container-fluid mb-4">
        <div class="row">
            <div class="col-lg-12">
                <x-breadcrumb :items="[
                    [
                        'title' => trans('dashboard.title'),
                        'url' => route('admin.dashboard'),
                        'icon' => 'uil uil-estate',
                    ],
                    [
                        'title' => trans('systemsetting::points.user_points_management'),
                    ],
                ]" />
            </div>
        </div>

        <div class="row">
            <div class="col-lg-12">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white border-bottom py-20">
                        <h5 class="mb-0 fw-500 fw-bold">
                            {{ trans('systemsetting::points.user_points_management') }}
                        </h5>
                    </div>
                    <div class="card-body">
                        <!-- Filter Section -->
                        <div class="filter-section">
                            <div class="filter-group">
                                <div class="filter-input">
                                    <input type="text"
                                           id="searchInput"
                                           class="form-control form-control-sm"
                                           placeholder="{{ trans('common.search_by_name_or_email') }}">
                                </div>
                                <button class="filter-btn filter-btn-search" onclick="filterTable()">
                                    <i class="uil uil-search"></i> {{ trans('common.search') }}
                                </button>
                                <button class="filter-btn filter-btn-reset" onclick="resetFilters()">
                                    <i class="uil uil-redo"></i> {{ trans('common.reset') }}
                                </button>
                            </div>
                        </div>

                        <!-- User Points Table -->
                        <div class="table-responsive">
                            <table class="table mb-0 table-bordered table-hover" id="userPointsTable">
                                <thead>
                                    <tr class="userDatatable-header">
                                        <th class="text-center"><span class="userDatatable-title">#</span></th>
                                        <th><span class="userDatatable-title">{{ trans('systemsetting::points.user_information') }}</span></th>
                                        <th class="text-center"><span class="userDatatable-title">{{ trans('systemsetting::points.total_points') }}</span></th>
                                        <th class="text-center"><span class="userDatatable-title">{{ trans('systemsetting::points.earned_points') }}</span></th>
                                        <th class="text-center"><span class="userDatatable-title">{{ trans('systemsetting::points.redeemed_points') }}</span></th>
                                        <th class="text-center"><span class="userDatatable-title">{{ trans('systemsetting::points.expired_points') }}</span></th>
                                        <th class="text-center"><span class="userDatatable-title">{{ trans('common.actions') }}</span></th>
                                    </tr>
                                </thead>
                                <tbody>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Points Details Modal -->
    <div class="modal fade" id="pointsDetailsModal" tabindex="-1" aria-labelledby="pointsDetailsModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-light border-bottom">
                    <h5 class="modal-title" id="pointsDetailsModalLabel">
                        <i class="uil uil-star me-2"></i>{{ trans('systemsetting::points.points_details') }}
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div id="pointsDetailsContent"></div>
                </div>
            </div>
        </div>
    </div>

    <script>
        let dataTable;

        document.addEventListener('DOMContentLoaded', function() {
            initializeDataTable();
        });

        function initializeDataTable() {
            dataTable = $('#userPointsTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: '{{ route("admin.user-points.datatable") }}',
                    type: 'GET',
                    data: function(d) {
                        d.search = {
                            value: $('#searchInput').val()
                        };
                    }
                },
                columns: [
                    {
                        data: 'index',
                        name: 'index',
                        className: 'text-center',
                        orderable: false,
                        searchable: false,
                        render: function(data) {
                            return '<span class="userDatatable-content">' + data + '</span>';
                        }
                    },
                    {
                        data: 'user_information',
                        name: 'user_information',
                        orderable: false,
                        searchable: false,
                        render: function(data) {
                            let photo = data.photo ? '{{ asset("storage/") }}/' + data.photo : '{{ asset("images/avatar.png") }}';
                            return `
                                <div class="user-info-cell">
                                    <img src="${photo}" alt="${data.name}" class="user-avatar" onerror="this.src='{{ asset('images/avatar.png') }}'">
                                    <div class="user-details">
                                        <div class="user-name">${$('<div/>').text(data.name).html()}</div>
                                        <div class="user-email">${$('<div/>').text(data.email).html()}</div>
                                    </div>
                                </div>
                            `;
                        }
                    },
                    {
                        data: 'total_points',
                        name: 'total_points',
                        className: 'text-center',
                        orderable: false,
                        searchable: false,
                        render: function(data) {
                            return '<span class="points-badge points-total">' + data + '</span>';
                        }
                    },
                    {
                        data: 'earned_points',
                        name: 'earned_points',
                        className: 'text-center',
                        orderable: false,
                        searchable: false,
                        render: function(data) {
                            return '<span class="points-badge points-earned">' + data + '</span>';
                        }
                    },
                    {
                        data: 'redeemed_points',
                        name: 'redeemed_points',
                        className: 'text-center',
                        orderable: false,
                        searchable: false,
                        render: function(data) {
                            return '<span class="points-badge points-redeemed">' + data + '</span>';
                        }
                    },
                    {
                        data: 'expired_points',
                        name: 'expired_points',
                        className: 'text-center',
                        orderable: false,
                        searchable: false,
                        render: function(data) {
                            return '<span class="points-badge points-expired">' + data + '</span>';
                        }
                    },
                    {
                        data: 'id',
                        name: 'actions',
                        className: 'text-center',
                        orderable: false,
                        searchable: false,
                        render: function(data) {
                            return `
                                <button class="action-btn action-btn-points"
                                        onclick="viewPointsDetails(${data})"
                                        title="{{ trans('systemsetting::points.view_points_details') }}">
                                    <i class="uil uil-star"></i>
                                </button>
                            `;
                        }
                    }
                ],
                language: {
                    url: '{{ asset("vendor/datatables/i18n/" . app()->getLocale() . ".json") }}'
                },
                pageLength: 10,
                lengthMenu: [[10, 25, 50, 100], [10, 25, 50, 100]],
                order: [[0, 'desc']]
            });
        }

        function filterTable() {
            if (dataTable) {
                dataTable.draw();
            }
        }

        function resetFilters() {
            $('#searchInput').val('');
            if (dataTable) {
                dataTable.draw();
            }
        }

        function viewPointsDetails(id) {
            fetch('{{ route("admin.user-points.show", ":id") }}'.replace(':id', id), {
                method: 'GET',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const userPhoto = data.data.user_photo
                        ? '{{ asset("storage/") }}/' + data.data.user_photo
                        : '{{ asset("images/avatar.png") }}';

                    const html = `
                        <div class="text-center mb-3">
                            <img src="${userPhoto}" alt="${data.data.user_name}"
                                 class="rounded-circle" style="width: 80px; height: 80px; object-fit: cover;"
                                 onerror="this.src='{{ asset('images/avatar.png') }}'">
                        </div>
                        <div class="mb-3">
                            <h6 class="fw-bold">${$('<div/>').text(data.data.user_name).html()}</h6>
                            <small class="text-muted">${$('<div/>').text(data.data.user_email).html()}</small>
                        </div>
                        <hr>
                        <div class="row">
                            <div class="col-6 mb-3">
                                <div class="text-center">
                                    <div class="points-badge points-total w-100">${data.data.total_points}</div>
                                    <small class="d-block mt-2">{{ trans('systemsetting::points.total_points') }}</small>
                                </div>
                            </div>
                            <div class="col-6 mb-3">
                                <div class="text-center">
                                    <div class="points-badge points-earned w-100">${data.data.earned_points}</div>
                                    <small class="d-block mt-2">{{ trans('systemsetting::points.earned_points') }}</small>
                                </div>
                            </div>
                            <div class="col-6 mb-3">
                                <div class="text-center">
                                    <div class="points-badge points-redeemed w-100">${data.data.redeemed_points}</div>
                                    <small class="d-block mt-2">{{ trans('systemsetting::points.redeemed_points') }}</small>
                                </div>
                            </div>
                            <div class="col-6 mb-3">
                                <div class="text-center">
                                    <div class="points-badge points-expired w-100">${data.data.expired_points}</div>
                                    <small class="d-block mt-2">{{ trans('systemsetting::points.expired_points') }}</small>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="alert alert-info mb-0">
                                    <strong>{{ trans('systemsetting::points.available_points') }}:</strong> ${data.data.available_points}
                                </div>
                            </div>
                        </div>
                    `;

                    document.getElementById('pointsDetailsContent').innerHTML = html;
                    new bootstrap.Modal(document.getElementById('pointsDetailsModal')).show();
                } else {
                    toastr.error(data.message || '{{ trans("common.error_occurred") }}');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                toastr.error('{{ trans("common.error_occurred") }}');
            });
        }
    </script>
@endsection
