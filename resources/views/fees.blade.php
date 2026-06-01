@extends('layouts.app')

@section('content')
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.css">

<style>
    .page-title-box h1 {
        font-family: 'Outfit', sans-serif;
        font-size: 1.75rem;
        font-weight: 800;
        color: var(--accent-10);
        margin-bottom: 0.25rem;
        text-transform: uppercase;
        letter-spacing: 12px;
    }

    .form-control-premium {
        border-radius: 12px !important;
        padding: 0.8rem 1.2rem !important;
        border: 2px solid #e2e8f0 !important;
        transition: all 0.3s !important;
        font-weight: 500 !important;
        background-color: #f8fafc !important;
    }
    .form-control-premium:focus {
        border-color: var(--secondary-30) !important;
        box-shadow: 0 0 0 4px rgba(14, 165, 233, 0.1) !important;
        background: white !important;
    }

    .action-btn-pill {
        border-radius: 50px;
        padding: 8px 20px;
        font-size: 0.85rem;
        font-weight: 700;
        border: none;
        transition: all 0.3s ease;
        display: inline-flex;
        align-items: center;
        gap: 8px;
    }
    .btn-pill-edit { background: #eff6ff; color: #1e40af; }
    .btn-pill-edit:hover { background: #1e40af; color: white; transform: scale(1.05); }
    .btn-pill-delete { background: #fff1f2; color: #e11d48; margin-left: 8px; }
    .btn-pill-delete:hover { background: #e11d48; color: white; transform: scale(1.05); }
</style>

<div class="container-fluid px-4 py-5">
    <!-- Perfect Heading Section -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="page-title-box d-flex flex-column flex-md-row justify-content-between align-items-md-center">
                <div>
                    <h1><i class="bi bi-tags-fill me-3"></i>F E E S &nbsp; &nbsp; T Y P E S</h1>
                    <p class="text-muted mb-0">Manage your institution's fee categories with precision and ease.</p>
                </div>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-0 mt-3 mt-md-0">
                        <li class="breadcrumb-item"><a href="#" class="text-decoration-none fw-semibold">Administration</a></li>
                        <li class="breadcrumb-item active fw-bold" aria-current="page">Fee Categories</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>

    <!-- Alert System -->
    <div class="row mb-4 justify-content-center">
        <div class="col-xl-10">
            @if(session('message'))
                <div class="alert alert-success alert-dismissible shadow-sm fade show rounded-4 border-0 p-4" role="alert">
                    <div class="d-flex align-items-center">
                        <i class="bi bi-check2-all display-6 me-3 text-success"></i>
                        <div class="fw-medium text-dark">{!! session('message') !!}</div>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @if($errors->any())
                <div class="alert alert-danger shadow-sm fade show rounded-4 border-0 p-4" role="alert">
                    <div class="d-flex align-items-center">
                        <i class="bi bi-shield-exclamation display-6 me-3 text-danger"></i>
                        <ul class="mb-0 list-unstyled text-dark fw-medium">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif
        </div>
    </div>

    <div class="row g-5">
        <!-- Add Section -->
        <div class="col-xl-4">
            <div class="card card-premium h-100 border-0 overflow-hidden">
                <div class="card-header-premium">
                    <i class="bi bi-plus-square-fill me-3 text-white"></i> Add New Fees Type
                </div>
                <div class="card-body p-5">
                    <form action="{{ url('addFeeType') }}" method="POST">
                        @csrf
                        <div class="form-group-premium mb-5">
                            <label><i class="bi bi-tag-fill me-2 opacity-50"></i> Fee Type Name <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control-premium w-100" placeholder="e.g., Annual Sports Fund" required autocomplete="off">
                            <div class="mt-3 text-secondary small px-1">Tip: Use descriptive names like 'Term-1 Exam Fee'</div>
                        </div>
                        <button type="submit" class="btn btn-cinematic w-100">
                            <i class="bi bi-cpu me-2"></i> Register Fee Category
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- List Section -->
        <div class="col-xl-8">
            <div class="card card-premium h-100 border-0">
                <div class="card-header-premium d-flex justify-content-between align-items-center">
                    <div><i class="bi bi-grid-3x3-gap-fill me-3 text-white"></i> Fee Category Registry</div>
                    <div class="badge bg-primary rounded-pill fw-bold" style="font-size: 0.75rem; padding: 10px 18px;">
                        {{ $feeTypeList->count() }} ACTIVE RECORDS
                    </div>
                </div>
                <div class="card-body p-4">
                    <div class="table-responsive table-container">
                        <table class="table table-premium" id="myTable">
                            <thead>
                                <tr>
                                    <th style="width: 65%;">Fee Type Narrative</th>
                                    <th class="text-center">Operations</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($feeTypeList as $row)
                                <tr>
                                    <td class="ps-3"><span class="text-dark fw-semibold h6 mb-0">{{ $row->name }}</span></td>
                                    <td class="text-center">
                                        <button class="action-btn-pill btn-pill-edit" data-bs-toggle="modal" data-bs-target="#editModal{{$row->id}}">
                                            <i class="bi bi-pencil-fill me-2 small"></i> Edit
                                        </button>
                                        <button class="action-btn-pill btn-pill-delete" onclick="confirmDelete('{{ route('deleteFeeType', $row->id) }}')">
                                            <i class="bi bi-trash3-fill me-2 small"></i> Del
                                        </button>
                                    </td>

                                    <!-- Refined Edit Modal -->
                                    <div class="modal fade" id="editModal{{$row->id}}" tabindex="-1" aria-hidden="true">
                                        <div class="modal-dialog modal-dialog-centered">
                                            <div class="modal-content modal-content-premium">
                                                <div class="modal-header modal-header-premium">
                                                    <h4 class="modal-title fw-bold text-white mb-0" style="font-family: 'Outfit'; letter-spacing: 1px;">
                                                        <i class="bi bi-pencil-square me-2"></i> Modify Fee Entry
                                                    </h4>
                                                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                                                </div>
                                                <form method="POST" action="{{ route('updateFeeType') }}">
                                                    @csrf
                                                    <div class="modal-body p-5">
                                                        <input type="hidden" name="id" value="{{$row->id}}">
                                                        <div class="mb-4">
                                                            <label class="form-label fw-bold text-secondary text-uppercase mb-3" style="font-size: 0.75rem; letter-spacing: 1px;">Update Fee Type Name</label>
                                                            <input type="text" class="form-control form-control-premium w-100" name="feeType" value="{{$row->name}}" required>
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer modal-footer-premium">
                                                        <button type="button" class="btn btn-light rounded-4 px-4 py-3 fw-semibold flex-grow-1 border-0" style="background: #f1f5f9; color: #64748b;" data-bs-dismiss="modal">Discard</button>
                                                        <button type="submit" class="btn btn-cinematic px-5 py-3 flex-grow-1">Commit Changes</button>
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
            confirmButtonColor: '#e11d48',
            cancelButtonColor: '#94a3b8',
            confirmButtonText: 'Yes, Delete Record',
            cancelButtonText: 'No, Keep it',
            buttonsStyling: true,
            customClass: {
                popup: 'p-4 rounded-5 border-0 shadow-lg',
                confirmButton: 'btn-cinematic px-5 py-3 m-2',
                cancelButton: 'btn btn-light rounded-4 px-5 py-3 m-2'
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
