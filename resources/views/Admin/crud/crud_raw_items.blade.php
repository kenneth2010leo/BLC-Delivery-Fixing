@extends('layout.admin_home')

@section('container')
    <div class="container py-5 admin-data-page">
        <!-- Header Section -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h1 class="fw-bold text-dark mb-1">Raw Items</h1>
                <p class="text-muted mb-0">Manage your raw materials inventory</p>
            </div>
        </div>

        <!-- Create Card -->
        <div class="dashboard-card">
            <div class="card-header">
                <div class="card-icon"><i class="bi bi-plus-circle"></i></div>
                <h5 class="card-title">Create New Raw Item</h5>
            </div>
            <form id="rawItemCreateForm">
                @csrf
                <div class="row g-3 align-items-end">
                    <div class="col-md-6">
                        <label for="rawItemCreateName" class="form-label">Name</label>
                        <input type="text" class="form-control" id="rawItemCreateName" name="raw_item_name" required>
                    </div>
                    <div class="col-md-6">
                        <label for="rawItemCreatePrice" class="form-label">Price per pcs</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light">Rp</span>
                            <input type="number" class="form-control" id="rawItemCreatePrice" name="raw_item_price"
                                required min="0">
                        </div>
                    </div>
                </div>
                <div class="col-md-12 d-grid mt-4">
                    <button type="submit" class="btn btn-primary btn-lg">
                        <i class="fas fa-save me-2"></i>Create
                    </button>
                </div>
            </form>
        </div>

        <!-- Table Card -->
        <div class="dashboard-card p-0">
            <div class="card-header px-4 pt-4">
                <div class="card-icon"><i class="bi bi-box-seam"></i></div>
                <h5 class="card-title">Raw Items List</h5>
            </div>
            <div class="card-body p-0">
                <table id="rawItemsTable" class="table table-hover mb-0">
                    <thead>
                        <tr>
                            <th class="px-4 py-3">ID</th>
                            <th class="px-4 py-3">Name</th>
                            <th class="px-4 py-3">Price per pcs</th>
                            <th class="px-4 py-3 text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="rawItemModal" tabindex="-1" aria-labelledby="rawItemModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow">
                <form id="rawItemForm">
                    @csrf
                    <div class="modal-header border-bottom bg-light">
                        <h5 class="modal-title fw-bold" id="rawItemModalLabel">Add Raw Item</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body p-4">
                        <input type="hidden" id="rawItemId">
                        <div class="mb-4">
                            <label for="rawItemName" class="form-label text-dark fw-medium">Name</label>
                            <input type="text" class="form-control form-control-lg" id="rawItemName" name="raw_item_name"
                                required>
                        </div>
                        <div class="mb-3">
                            <label for="rawItemPrice" class="form-label text-dark fw-medium">Price per pcs</label>
                            <div class="input-group input-group-lg">
                                <span class="input-group-text bg-light">Rp </span>
                                <input type="number" class="form-control" id="rawItemPrice" name="raw_item_price"
                                    min="0" max="1000000" required>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer border-top bg-light">
                        <button type="button" class="btn btn-light px-4" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary px-4">Save Changes</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Styles -->
    <style>
        /* Match the Admin Management cards and form controls. */
        .admin-data-page {
            background: #f8fafc;
            min-height: 100vh;
        }

        .dashboard-card {
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, .05);
            padding: 2rem;
            margin-bottom: 2rem;
            overflow: hidden;
        }

        .card-header {
            display: flex;
            align-items: center;
            gap: .75rem;
            margin: 0 1.5rem 1rem;
        }

        .card-icon {
            width: 40px;
            height: 40px;
            flex: 0 0 40px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #e0f2fe;
            color: #0284c7;
            border-radius: 10px;
            font-size: 1.25rem;
        }

        .card-title {
            color: #1e293b;
            font-size: 1.25rem;
            font-weight: 600;
            margin: 0;
        }

        .admin-data-page .form-label {
            color: #475569;
            font-weight: 500;
        }

        .admin-data-page .form-control {
            border: 1px solid #e2e8f0;
            border-radius: 8px;
        }

        .admin-data-page .form-control:focus {
            border-color: #0284c7;
            box-shadow: 0 0 0 3px rgba(2, 132, 199, .1);
        }

        .admin-data-page table thead th {
            background: #f8fafc;
            color: #475569;
            font-weight: 600;
            border-bottom: 2px solid #e2e8f0;
        }

        .admin-data-page .table> :not(caption)>*>* {
            padding: 1rem 1.5rem;
            vertical-align: middle;
        }

        @media (max-width: 768px) {
            .dashboard-card {
                padding: 1.25rem;
            }
        }

        /* Custom styles for better contrast and visual hierarchy */
        .table> :not(caption)>*>* {
            padding: 1rem 1.5rem;
            vertical-align: middle;
        }

        .table>tbody>tr:hover {
            background-color: #f8f9fa;
        }

        .btn-action {
            width: 36px;
            height: 36px;
            padding: 0;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 6px;
            margin-left: 0.5rem;
            transition: all 0.2s;
        }

        .btn-edit {
            background-color: #e3f2fd;
            color: #1976d2;
            border: none;
        }

        .btn-edit:hover {
            background-color: #1976d2;
            color: white;
        }

        .btn-delete {
            background-color: #ffebee;
            color: #d32f2f;
            border: none;
        }

        .btn-delete:hover {
            background-color: #d32f2f;
            color: white;
        }

        .form-control:focus {
            border-color: #1976d2;
            box-shadow: 0 0 0 0.2rem rgba(25, 118, 210, 0.1);
        }

        /* DataTables customization */
        .dataTables_wrapper .dataTables_length,
        .dataTables_wrapper .dataTables_filter {
            margin-bottom: 1.5rem;
            padding: 0 1.5rem;
        }

        .dataTables_wrapper .dataTables_info,
        .dataTables_wrapper .dataTables_paginate {
            padding: 1.5rem;
        }
    </style>

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.12.1/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        $(document).ready(function() {
            // CSRF token setup
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            // Initialize DataTable
            var table = $('#rawItemsTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: '/blc-delivery/raw-items/data',
                columns: [{
                        data: 'id',
                        name: 'id'
                    },
                    {
                        data: 'raw_item_name',
                        name: 'raw_item_name'
                    },
                    {
                        data: 'raw_item_price',
                        name: 'raw_item_price',
                        render: function(data) {
                            return `Rp ${parseFloat(data).toLocaleString('ID-id')} / pcs`;
                        }
                    },
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false,
                        render: function(data, type, row) {
                            return `
                        <div class="text-end">
                            <button class="btn btn-action btn-edit editRawItem" data-id="${row.id}">
                                <i class="bi bi-pencil"></i>
                            </button>
                            <button class="btn btn-action btn-delete deleteRawItem" data-id="${row.id}">
                                <i class="bi bi-trash"></i>
                            </button>
                        </div>
                    `;
                        }
                    }
                ],
                order: [
                    [0, 'desc']
                ],
                drawCallback: function() {
                    $('.dataTables_paginate > .pagination').addClass('btn-group');
                }
            });

            // Create Raw Item
            $('#rawItemCreateForm').on('submit', function(e) {
                e.preventDefault();
                $.ajax({
                    url: '/blc-delivery/raw-items/',
                    method: 'POST',
                    data: $(this).serialize(),
                    success: function(response) {
                        $('#rawItemCreateForm').trigger('reset');
                        table.ajax.reload();
                        Swal.fire({
                            icon: 'success',
                            title: 'Success!',
                            text: response.message,
                            timer: 2000,
                            showConfirmButton: false
                        });
                    },
                    error: function(xhr) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error!',
                            text: xhr.responseJSON.message || 'Something went wrong!'
                        });
                    }
                });
            });

            // Edit Raw Item
            $(document).on('click', '.editRawItem', function() {
                var id = $(this).data('id');
                $.get('/blc-delivery/raw-items/' + id + '/edit', function(data) {
                    $('#rawItemModal').modal('show');
                    $('#rawItemModalLabel').text('Edit Raw Item');
                    $('#rawItemId').val(data.id);
                    $('#rawItemName').val(data.raw_item_name);
                    $('#rawItemPrice').val(data.raw_item_price);
                });
            });

            // Save Raw Item
            $('#rawItemForm').on('submit', function(e) {
                e.preventDefault();
                var id = $('#rawItemId').val();
                var url = id ? '/blc-delivery/raw-items/' + id : '/blc-delivery/raw-items/';
                var method = id ? 'PUT' : 'POST';

                $.ajax({
                    url: url,
                    method: method,
                    data: $(this).serialize(),
                    success: function(response) {
                        $('#rawItemModal').modal('hide');
                        table.ajax.reload();
                        Swal.fire({
                            icon: 'success',
                            title: 'Success!',
                            text: response.message,
                            timer: 2000,
                            showConfirmButton: false
                        });
                    },
                    error: function(xhr) {
                        var msg = 'Something went wrong!';
                        if (xhr.responseJSON) {
                            if (xhr.responseJSON.errors) {
                                msg = Object.values(xhr.responseJSON.errors).flat().join('\n');
                            } else if (xhr.responseJSON.message) {
                                msg = xhr.responseJSON.message;
                            }
                        }
                        Swal.fire({
                            icon: 'error',
                            title: 'Error!',
                            text: msg
                        });
                    }
                });
            });

            // Delete Raw Item
            $(document).on('click', '.deleteRawItem', function() {
                var id = $(this).data('id');

                Swal.fire({
                    title: 'Are you sure?',
                    text: "This action cannot be undone.",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Yes, delete it!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: '/blc-delivery/raw-items/delete/' + id,
                            method: 'DELETE',
                            success: function(response) {
                                table.ajax.reload();
                                if (response.status === 'error') {
                                    Swal.fire({
                                        icon: 'error',
                                        title: 'Error!',
                                        text: response.message
                                    });
                                    return;
                                }
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Deleted!',
                                    text: response.message,
                                    timer: 2000,
                                    showConfirmButton: false
                                });
                            }
                        });
                    }
                });
            });
        });
    </script>
@endsection
