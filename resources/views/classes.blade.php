@extends('layouts.app')

@section('content')

<div class="pagetitle">
    <h1>Classes Management</h1>
    <nav>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
            <li class="breadcrumb-item active">Classes</li>
        </ol>
    </nav>
</div>

<section class="section">
    <div class="row">
        <!-- Add Class Form -->
        <div class="col-lg-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">Add New Class</h5>
                </div>
                <div class="card-body pt-3">
                    <form action="{{url('addClass')}}" method="POST" class="row g-3">
                        @csrf
                        <div class="col-12 mb-3">
                            <label class="form-label">Branch Name</label>
                            @if($schoolList->isEmpty())
                            <div class="alert alert-warning">No branches found. Please add a branch first.</div>
                            @endif
                            <select class="form-select" name="school_id" required>
                                <option value="">-- Select Branch --</option>
                                @foreach($schoolList as $rowSchoolList)
                                <option value="{{$rowSchoolList->id}}">{{$rowSchoolList->schoolName}} - {{$rowSchoolList->address}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-12 mb-3">
                            <div class="form-floating">
                                <input class="form-control" name="className" id="className" type="text" placeholder="e.g. Class 1" required />
                                <label for="className">Class Name</label>
                            </div>
                        </div>
                        <div class="col-12 mb-3">
                            <div class="form-floating">
                                <input class="form-control" name="sections" id="sections" type="text" placeholder="e.g. A, B, C" />
                                <label for="sections">Sections (Comma separated)</label>
                            </div>
                        </div>
                        <div class="col-12 mt-4">
                            <button class="btn btn-primary w-100 py-3 rounded-pill shadow" type="submit">
                                <i class="bi bi-plus-circle me-2"></i> Add Class
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Class List Table -->
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title">Class List</h5>
                    @if (session('message'))
                    <div id="alert-message" class="badge bg-light text-primary p-2">
                        {{ session('message') }}
                    </div>
                    @endif
                </div>
                <div class="card-body pt-3">
                    <div id="datatable-container">
                        <table class="table table-hover" id="classesTable">
                            <thead>
                                <tr>
                                    <th>S.NO</th>
                                    <th>School</th>
                                    <th>Class Name</th>
                                    <th class="text-center">Action</th>
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
</section>

<!-- Edit Modal -->
<div class="modal fade" id="editModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 20px;">
            <div class="modal-header border-0 bg-primary text-white p-4" style="border-radius: 20px 20px 0 0;">
                <h5 class="modal-title fw-bold" id="editModalLabel">Edit Class Details</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="editForm" method="POST" action="{{route('updateClass')}}">
                @csrf
                <div class="modal-body p-4">
                    <input type="hidden" name="id" id="editId">
                    <div class="form-floating mb-4">
                        <input type="text" class="form-control" id="editClassName" name="className" placeholder="Class Name">
                        <label for="editClassName">Class Name</label>
                    </div>

                    <h6 class="fw-bold text-primary mb-3">Manage Sections</h6>
                    <div class="input-group mb-3">
                        <input type="text" id="editModalNewSection" class="form-control border-end-0" placeholder="Section Name (e.g. D)">
                        <button type="button" id="editModalAddSectionBtn" class="btn btn-primary px-4"><i class="bi bi-plus-lg"></i> Add</button>
                    </div>

                    <div class="table-responsive rounded-3 border" style="max-height: 250px;">
                        <table class="table table-sm table-hover mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th class="ps-3">Section Name</th>
                                    <th class="text-end pe-3">Action</th>
                                </tr>
                            </thead>
                            <tbody id="editModalSectionsBody">
                                <!-- Sections load here -->
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="modal-footer border-0 p-4">
                    <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary rounded-pill px-4 shadow">Update changes</button>
                </div>
            </form>
        </div>
    </div>
</div>

@section('scripts')
<script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.js"></script>
<script>
    $(document).ready(function() {
        var table = $('#classesTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: "{{ route('getClasses') }}",
            },
            order: [
                [2, 'asc']
            ],
            columns: [{
                    data: "id"
                },
                {
                    data: "schoolName"
                },
                {
                    data: "className"
                },
                {
                    data: "action",
                    orderable: false,
                    searchable: false,
                    className: "text-center"
                }
            ],
            language: {
                search: "_INPUT_",
                searchPlaceholder: "Search classes...",
                lengthMenu: "_MENU_ entries per page",
                paginate: {
                    previous: '<i class="bi bi-chevron-left"></i>',
                    next: '<i class="bi bi-chevron-right"></i>'
                }
            },
            drawCallback: function() {
                $('.dataTables_paginate > .paginate_button').addClass('btn btn-sm btn-light border mx-1');
            }
        });

        var editModal = new bootstrap.Modal(document.getElementById('editModal'));
        var currentClassId = null;

        $(document).on('click', '.edit-button', function() {
            currentClassId = $(this).data('id');
            var name = $(this).data('name');
            if (!currentClassId) return;

            $('#editId').val(currentClassId);
            $('#editClassName').val(name);
            $('#editModalNewSection').val('');
            loadSectionsForEdit(currentClassId);
            editModal.show();
        });

        function loadSectionsForEdit(classId) {
            $('#editModalSectionsBody').html('<tr><td colspan="2" class="text-center py-4"><div class="spinner-border text-primary" role="status"></div></td></tr>');
            $.get('{{ url("sections/class") }}/' + classId, function(data) {
                var html = '';
                if (Array.isArray(data)) {
                    data.forEach(function(sec) {
                        html += '<tr>' +
                            '<td class="ps-3 fw-bold">' + (sec.sectionName || 'N/A') + '</td>' +
                            '<td class="text-end pe-3"><button type="button" class="btn btn-sm btn-link text-danger delete-section" data-id="' + sec.id + '"><i class="bi bi-trash"></i></button></td>' +
                            '</tr>';
                    });
                }
                if (!html) html = '<tr><td colspan="2" class="text-center text-muted py-4">No sections available.</td></tr>';
                $('#editModalSectionsBody').html(html);
            });
        }

        $('#editModalAddSectionBtn').on('click', function() {
            var name = $('#editModalNewSection').val().trim();
            if (!name) return alert('Section name required');

            var btn = $(this);
            btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm"></span>');

            $.post('{{ route("sections.store") }}', {
                    _token: '{{ csrf_token() }}',
                    class_id: currentClassId,
                    sectionName: name
                },
                function(res) {
                    btn.prop('disabled', false).html('<i class="bi bi-plus-lg"></i> Add');
                    if (res.ok) {
                        $('#editModalNewSection').val('');
                        loadSectionsForEdit(currentClassId);
                    }
                });
        });

        $(document).on('click', '.delete-section', function() {
            if (!confirm('Delete this section?')) return;
            var id = $(this).data('id');
            $.ajax({
                url: '{{ url("sections") }}/' + id,
                type: 'DELETE',
                data: {
                    _token: '{{ csrf_token() }}'
                },
                success: function() {
                    loadSectionsForEdit(currentClassId);
                }
            });
        });

        setTimeout(function() {
            $('#alert-message').fadeOut();
        }, 5000);
    });
</script>
@endsection
@endsection