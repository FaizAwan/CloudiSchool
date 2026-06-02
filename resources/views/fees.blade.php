@extends('layouts.app')

@section('content')
<!-- Include crisp DataTables stylesheet -->
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.css">

<style>
    /* Premium Page Header styling */
    .page-title-box h1 {
        font-family: 'Outfit', sans-serif !important;
        font-weight: 800 !important;
        color: #1e293b !important;
        text-transform: uppercase !important;
        letter-spacing: 2px !important;
        font-size: 1.5rem !important;
    }

    /* Card Overhauls with smooth shadows */
    .card-premium {
        border-radius: 16px !important;
        border: 1px solid #e2e8f0 !important;
        box-shadow: 0 4px 12px rgba(0, 74, 198, 0.02) !important;
        background: #ffffff !important;
        transition: transform 0.3s ease, box-shadow 0.3s ease !important;
        overflow: hidden;
    }
    .card-premium:hover {
        box-shadow: 0 10px 25px rgba(0, 74, 198, 0.05) !important;
    }

    .card-header-premium {
        background: linear-gradient(135deg, #004ac6 0%, #1e40af 100%) !important;
        padding: 1.25rem 1.75rem !important;
        font-size: 0.95rem !important;
        font-weight: 700 !important;
        font-family: 'Outfit', sans-serif !important;
        color: #ffffff !important;
        border: none !important;
        display: flex;
        align-items: center;
    }

    /* Form Controls */
    .form-control-premium {
        border-radius: 10px !important;
        padding: 0.75rem 1rem !important;
        border: 1px solid #cbd5e1 !important;
        transition: all 0.25s ease !important;
        font-weight: 500 !important;
        background-color: #f8fafc !important;
        font-size: 0.88rem !important;
    }
    .form-control-premium:focus {
        border-color: #004ac6 !important;
        box-shadow: 0 0 0 3px rgba(0, 74, 198, 0.1) !important;
        background-color: #ffffff !important;
    }

    /* Cinematic primary buttons */
    .btn-cinematic {
        background: linear-gradient(135deg, #004ac6 0%, #1e40af 100%) !important;
        color: #ffffff !important;
        font-weight: 700 !important;
        border-radius: 10px !important;
        padding: 12px 24px !important;
        letter-spacing: 0.5px !important;
        border: none !important;
        box-shadow: 0 4px 12px rgba(0, 74, 198, 0.15) !important;
        transition: all 0.25s ease !important;
    }
    .btn-cinematic:hover {
        transform: translateY(-1px) !important;
        box-shadow: 0 6px 20px rgba(0, 74, 198, 0.25) !important;
    }

    /* Table Premium refinements with absolute header relative fixes */
    .table-premium {
        width: 100% !important;
        border-collapse: separate !important;
        border-spacing: 0 6px !important;
    }
    .table-premium thead th {
        background: #f8fafc !important;
        color: #475569 !important;
        font-weight: 800 !important;
        font-size: 0.75rem !important;
        text-transform: uppercase !important;
        letter-spacing: 0.8px !important;
        padding: 14px 16px !important;
        border: 1px solid #e2e8f0 !important;
        border-width: 1px 0 !important;
        position: relative !important; /* MANDATORY FIX FOR DATATABLE TRIANGLES */
    }
    .table-premium thead th:first-child {
        border-left-width: 1px !important;
        border-top-left-radius: 8px !important;
        border-bottom-left-radius: 8px !important;
    }
    .table-premium thead th:last-child {
        border-right-width: 1px !important;
        border-top-right-radius: 8px !important;
        border-bottom-right-radius: 8px !important;
    }

    .table-premium tbody tr {
        background: #ffffff !important;
        box-shadow: 0 1px 2px rgba(0,0,0,0.01) !important;
        transition: all 0.2s ease !important;
    }
    .table-premium tbody tr:hover {
        background: #f8fafc !important;
        transform: translateY(-1px) !important;
        box-shadow: 0 4px 10px rgba(0, 74, 198, 0.04) !important;
    }
    .table-premium tbody td {
        padding: 14px 16px !important;
        border: 1px solid #e2e8f0 !important;
        border-width: 1px 0 !important;
        vertical-align: middle;
    }
    .table-premium tbody tr td:first-child {
        border-left-width: 1px !important;
        border-top-left-radius: 10px !important;
        border-bottom-left-radius: 10px !important;
    }
    .table-premium tbody tr td:last-child {
        border-right-width: 1px !important;
        border-top-right-radius: 10px !important;
        border-bottom-right-radius: 10px !important;
    }

    /* Action Pill buttons */
    .action-btn-pill {
        border-radius: 8px !important;
        padding: 6px 14px !important;
        font-size: 0.8rem !important;
        font-weight: 700 !important;
        border: 1px solid transparent !important;
        transition: all 0.2s ease !important;
        display: inline-flex;
        align-items: center;
        gap: 6px;
    }
    .btn-pill-edit {
        background: rgba(0, 74, 198, 0.06) !important;
        color: #004ac6 !important;
        border-color: rgba(0, 74, 198, 0.1) !important;
    }
    .btn-pill-edit:hover {
        background: #004ac6 !important;
        color: #ffffff !important;
        transform: translateY(-1px) !important;
    }
    .btn-pill-delete {
        background: rgba(220, 53, 69, 0.06) !important;
        color: #dc3545 !important;
        border-color: rgba(220, 53, 69, 0.1) !important;
        margin-left: 6px;
    }
    .btn-pill-delete:hover {
        background: #dc3545 !important;
        color: #ffffff !important;
        transform: translateY(-1px) !important;
    }

    /* DataTables inputs and select boxes */
    .dataTables_wrapper .dataTables_filter {
        margin-bottom: 15px !important;
    }
    .dataTables_wrapper .dataTables_filter input {
        border-radius: 8px !important;
        padding: 6px 12px !important;
        border: 1px solid #cbd5e1 !important;
        background: #f8fafc !important;
        font-weight: 500 !important;
        font-size: 0.85rem !important;
        outline: none !important;
        transition: all 0.25s !important;
    }
    .dataTables_wrapper .dataTables_filter input:focus {
        border-color: #004ac6 !important;
        background: #ffffff !important;
        box-shadow: 0 0 0 3px rgba(0, 74, 198, 0.08) !important;
    }
    .dataTables_wrapper .dataTables_length select {
        border-radius: 6px !important;
        padding: 5px 8px !important;
        border: 1px solid #cbd5e1 !important;
        background: #f8fafc !important;
        outline: none !important;
    }

    /* COMPLETE RE-ENGINEERING OF DATATABLES SORT ARROWS TO ELIMINATE REPEAT LINES */
    table.dataTable thead>tr>th.sorting,
    table.dataTable thead>tr>th.sorting_asc,
    table.dataTable thead>tr>th.sorting_desc {
        padding-right: 28px !important;
    }
    table.dataTable thead th.sorting:before,
    table.dataTable thead th.sorting_asc:before,
    table.dataTable thead th.sorting_desc:before {
        position: absolute !important;
        right: 12px !important;
        bottom: 50% !important;
        content: "▲" !important;
        font-size: 0.62rem !important;
        line-height: 9px !important;
        opacity: 0.3 !important;
        color: #64748b !important;
        display: block !important;
    }
    table.dataTable thead th.sorting:after,
    table.dataTable thead th.sorting_asc:after,
    table.dataTable thead th.sorting_desc:after {
        position: absolute !important;
        right: 12px !important;
        top: 50% !important;
        content: "▼" !important;
        font-size: 0.62rem !important;
        line-height: 9px !important;
        opacity: 0.3 !important;
        color: #64748b !important;
        display: block !important;
    }
    table.dataTable thead th.sorting_asc:before {
        opacity: 1 !important;
        color: #004ac6 !important;
    }
    table.dataTable thead th.sorting_desc:after {
        opacity: 1 !important;
        color: #004ac6 !important;
    }

    /* Refined Modals styling */
    .modal-content-premium {
        border-radius: 20px !important;
        border: none !important;
        box-shadow: 0 15px 30px rgba(0, 74, 198, 0.08) !important;
        overflow: hidden !important;
    }
    .modal-header-premium {
        background: linear-gradient(135deg, #004ac6 0%, #1e40af 100%) !important;
        border-radius: 0 !important;
        padding: 1.25rem 2rem !important;
    }
</style>

<div class="container-fluid px-0 py-3">
    <!-- Premium Breadcrumb and Title Header -->
    <div class="row mb-4">
        <div class="col-12 d-flex flex-column flex-md-row justify-content-between align-items-md-center">
            <div class="page-title-box">
                <h1><i class="bi bi-tags-fill me-2" style="color: #004ac6;"></i> Fees Types</h1>
                <p class="text-muted mb-0 small">Manage your institution's fee categories with precision and ease.</p>
            </div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 mt-2 mt-md-0 bg-transparent p-0">
                    <li class="breadcrumb-item"><a href="{{ route('home') }}" class="text-decoration-none fw-semibold" style="color: #64748b;">Administration</a></li>
                    <li class="breadcrumb-item active fw-bold" style="color: #004ac6;" aria-current="page">Fee Categories</li>
                </ol>
            </nav>
        </div>
    </div>

    <!-- Alert System -->
    @if(session('message'))
        <div class="alert alert-success alert-dismissible shadow-sm fade show rounded-4 border-0 p-3 mb-4" role="alert" style="background-color: #ecfdf5; border-left: 4px solid #10b981 !important;">
            <div class="d-flex align-items-center">
                <i class="bi bi-check-circle-fill me-2 text-success" style="font-size: 1.1rem;"></i>
                <div class="fw-semibold text-dark small">{!! session('message') !!}</div>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger shadow-sm alert-dismissible fade show rounded-4 border-0 p-3 mb-4" role="alert" style="background-color: #fef2f2; border-left: 4px solid #ef4444 !important;">
            <div class="d-flex align-items-center">
                <i class="bi bi-exclamation-triangle-fill me-2 text-danger" style="font-size: 1.1rem;"></i>
                <ul class="mb-0 list-unstyled text-dark fw-semibold small">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- Two Column Layout Grid -->
    <div class="row g-4">
        <!-- Left Side: Register Fee Category Form -->
        <div class="col-lg-4">
            <div class="card card-premium border-0">
                <div class="card-header-premium">
                    <i class="bi bi-plus-circle-fill me-2"></i> Add New Fees Type
                </div>
                <div class="card-body p-4">
                    <form action="{{ url('addFeeType') }}" method="POST">
                        @csrf
                        <div class="mb-4">
                            <label class="form-label fw-bold text-dark small mb-2">
                                <i class="bi bi-tag-fill me-1 text-primary"></i> Fee Type Name <span class="text-danger">*</span>
                            </label>
                            <input type="text" name="name" class="form-control-premium w-100" placeholder="e.g., Annual Sports Fund" required autocomplete="off">
                            <div class="mt-2 text-muted small" style="font-size: 0.78rem;"><i class="bi bi-info-circle me-1"></i> Tip: Use descriptive names like 'Term-1 Exam Fee'</div>
                        </div>
                        <button type="submit" class="btn btn-cinematic w-100 py-2.5">
                            <i class="bi bi-check2-circle me-1"></i> Register Fee Category
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Right Side: Registry Table -->
        <div class="col-lg-8">
            <div class="card card-premium border-0">
                <div class="card-header-premium d-flex justify-content-between align-items-center">
                    <div><i class="bi bi-grid-3x3-gap-fill me-2"></i> Fee Category Registry</div>
                    <span class="badge bg-white text-primary rounded-pill fw-bold" style="font-size: 0.72rem; padding: 6px 14px; box-shadow: 0 2px 4px rgba(0,0,0,0.05);">
                        {{ $feeTypeList->count() }} ACTIVE RECORDS
                    </span>
                </div>
                <div class="card-body p-4">
                    <div class="table-responsive">
                        <table class="table table-premium mb-0" id="myTable">
                            <thead>
                                <tr>
                                    <th style="width: 70%;">Fee Type Narrative</th>
                                    <th class="text-center" style="width: 30%;">Operations</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($feeTypeList as $row)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center ps-2">
                                            <div class="rounded-circle bg-primary bg-opacity-10 p-2 me-3 text-center d-flex align-items-center justify-content-center" style="width: 32px; height: 32px; background-color: rgba(0,74,198,0.05); color: #004ac6;">
                                                <i class="bi bi-bookmark-star-fill" style="font-size: 0.88rem;"></i>
                                            </div>
                                            <span class="text-dark fw-bold" style="font-size: 0.88rem;">{{ $row->name }}</span>
                                        </div>
                                    </td>
                                    <td class="text-center">
                                        <button class="action-btn-pill btn-pill-edit" data-bs-toggle="modal" data-bs-target="#editModal{{$row->id}}">
                                            <i class="bi bi-pencil-fill small"></i> Edit
                                        </button>
                                        <button class="action-btn-pill btn-pill-delete" onclick="confirmDelete('{{ route('deleteFeeType', $row->id) }}')">
                                            <i class="bi bi-trash3-fill small"></i> Del
                                        </button>
                                    </td>

                                    <!-- Refined Edit Modal -->
                                    <div class="modal fade" id="editModal{{$row->id}}" tabindex="-1" aria-hidden="true">
                                        <div class="modal-dialog modal-dialog-centered">
                                            <div class="modal-content modal-content-premium">
                                                <div class="modal-header modal-header-premium">
                                                    <h5 class="modal-title fw-bold text-white mb-0" style="font-family: 'Outfit'; letter-spacing: 0.5px;">
                                                        <i class="bi bi-pencil-square me-2"></i> Modify Fee Entry
                                                    </h5>
                                                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                                                </div>
                                                <form method="POST" action="{{ route('updateFeeType') }}">
                                                    @csrf
                                                    <div class="modal-body p-4">
                                                        <input type="hidden" name="id" value="{{$row->id}}">
                                                        <div class="mb-2">
                                                            <label class="form-label fw-bold text-secondary text-uppercase small mb-2" style="font-size: 0.72rem; letter-spacing: 0.5px;">Update Fee Type Name</label>
                                                            <input type="text" class="form-control form-control-premium w-100" name="feeType" value="{{$row->name}}" required autocomplete="off">
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer modal-footer-premium border-0 pt-0">
                                                        <button type="button" class="btn btn-light rounded-3 px-4 py-2 fw-semibold flex-grow-1 border-0" style="background: #f1f5f9; color: #64748b;" data-bs-dismiss="modal">Discard</button>
                                                        <button type="submit" class="btn btn-cinematic px-4 py-2 flex-grow-1">Commit Changes</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@section('scripts')
<script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    $(document).ready(function() {
        $('#myTable').DataTable({
            "pageLength": 10,
            "ordering": true,
            "info": true,
            "language": {
                "search": "",
                "searchPlaceholder": "🔍 Search records...",
                "lengthMenu": "_MENU_",
                "paginate": {
                    "next": "<i class='bi bi-chevron-right'></i>",
                    "previous": "<i class='bi bi-chevron-left'></i>"
                }
            },
            "drawCallback": function() {
                $('.dataTables_paginate').addClass('mt-4 d-flex justify-content-end');
                $('.dataTables_filter').addClass('mb-3');
            }
        });
    });

    function confirmDelete(url) {
        Swal.fire({
            title: 'Delete Category?',
            text: "This operation cannot be undone. All linked data will be affected.",
            icon: 'warning',
            background: '#ffffff',
            showCancelButton: true,
            confirmButtonColor: '#dc3545',
            cancelButtonColor: '#94a3b8',
            confirmButtonText: 'Yes, Delete Record',
            cancelButtonText: 'No, Keep it',
            buttonsStyling: true,
            customClass: {
                popup: 'p-3 rounded-4 border-0 shadow-lg',
                confirmButton: 'btn-cinematic px-4 py-2.5 m-2',
                cancelButton: 'btn btn-light rounded-3 px-4 py-2.5 m-2'
            }
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = url;
            }
        })
    }
</script>
@endsection
@endsection
