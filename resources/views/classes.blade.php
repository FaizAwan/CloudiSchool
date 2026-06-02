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
    <!-- Add Action Row -->
    <div class="row mb-4">
        <div class="col-md-12 text-end">
            <button type="button" class="btn btn-primary px-4 py-2 shadow-lg" data-bs-toggle="modal" data-bs-target="#addClassModal" 
                    style="background: linear-gradient(135deg, #004ac6, #1e40af) !important; 
                           border: none !important; 
                           color: #ffffff !important; 
                           font-weight: 700; 
                           border-radius: 12px; 
                           font-size: 0.88rem; 
                           letter-spacing: 0.3px;
                           box-shadow: 0 4px 15px rgba(0, 74, 198, 0.25) !important;
                           transition: all 0.3s ease;">
                <i class="bi bi-plus-circle-fill me-2"></i> Add New Class
            </button>
        </div>
    </div>

    <div class="row">
        <!-- Class List Table -->
        <div class="col-lg-12">
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

<!-- Add Class Modal -->
<div class="modal fade" id="addClassModal" tabindex="-1" role="dialog" aria-labelledby="addClassModalLabel" aria-hidden="true" data-backdrop="static">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content modal-content-premium border-0 shadow-lg" style="border-radius: 24px !important; overflow: hidden;">
      <div class="modal-header border-0 bg-primary text-white p-4" style="background: linear-gradient(135deg, #004ac6 0%, #1e40af 100%) !important; padding: 1.75rem 2rem !important; display: flex !important; align-items: center !important;">
        <h5 class="modal-title fw-bold text-white mb-0 d-flex align-items-center" id="addClassModalLabel" style="font-family: 'Outfit', sans-serif !important; font-size: 1.15rem; letter-spacing: 0.5px;">
          <i class="bi bi-plus-circle-fill me-2" style="font-size: 1.25rem; color: #fff !important;"></i> Add New Class
        </h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close" style="filter: invert(1); opacity: 0.8;"></button>
      </div>
      <form action="{{url('addClass')}}" method="POST">
        @csrf
        <div class="modal-body p-4" style="background-color: #f8fafc;">
          
          <div class="mb-4">
            <label class="form-label" style="font-weight: 700; color: #475569; font-size: 0.8rem; margin-bottom: 8px;">Branch Name <span class="text-danger">*</span></label>
            @if($schoolList->isEmpty())
            <div class="alert alert-warning py-2 mb-2" style="font-size: 11px;">No branches found. Please add a branch first.</div>
            @endif
            <div class="input-group">
              <span class="input-group-text" style="background-color: rgba(0, 74, 198, 0.04); border: 1.5px solid #e2e8f0; border-right: none; border-radius: 12px 0 0 12px; color: #004ac6; padding: 10px 15px;"><i class="bi bi-building"></i></span>
              <select class="form-select" name="school_id" required style="border-radius: 0 12px 12px 0; border: 1.5px solid #e2e8f0; border-left: none; padding: 12px 15px; font-weight: 600; color: #1e293b; font-size: 0.9rem;">
                <option value="">-- Select Branch --</option>
                @foreach($schoolList as $rowSchoolList)
                <option value="{{$rowSchoolList->id}}">{{$rowSchoolList->schoolName}}</option>
                @endforeach
              </select>
            </div>
          </div>
          
          <div class="form-floating mb-4">
            <input class="form-control" name="className" id="className" type="text" placeholder="e.g. Class 1" required style="border-radius: 12px; border: 1.5px solid #e2e8f0; padding: 12px 15px; font-weight: 600; color: #1e293b; font-size: 0.9rem;">
            <label for="className" style="font-weight: 600; color: #899bbd;">Class Name <span class="text-danger">*</span></label>
          </div>
          
          <div class="form-floating mb-4">
            <input class="form-control" name="sections" id="sections" type="text" placeholder="e.g. A, B, C" style="border-radius: 12px; border: 1.5px solid #e2e8f0; padding: 12px 15px; font-weight: 600; color: #1e293b; font-size: 0.9rem;">
            <label for="sections" style="font-weight: 600; color: #899bbd;">Sections (Comma separated, optional)</label>
          </div>

        </div>
        <div class="modal-footer border-0 p-4" style="background-color: #f1f5f9; display: flex; gap: 10px; justify-content: flex-end;">
          <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal" style="font-weight: 700; font-size: 0.85rem; padding: 10px 24px; border: 1.5px solid #e2e8f0;">Close</button>
          <button type="submit" class="btn btn-primary rounded-pill px-5 shadow-lg" style="background: linear-gradient(135deg, #004ac6, #1e40af) !important; border: none; font-weight: 700; font-size: 0.85rem; padding: 10px 28px; box-shadow: 0 4px 15px rgba(0, 74, 198, 0.25) !important;">
            <i class="bi bi-save-fill me-1"></i> Add Class
          </button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Edit Modal -->
<div class="modal fade" id="editModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content modal-content-premium border-0 shadow-lg" style="border-radius: 24px !important; overflow: hidden;">
            <div class="modal-header border-0 bg-primary text-white p-4" style="background: linear-gradient(135deg, #004ac6 0%, #1e40af 100%) !important; padding: 1.75rem 2rem !important; display: flex !important; align-items: center !important;">
                <h5 class="modal-title fw-bold text-white mb-0 d-flex align-items-center" id="editModalLabel" style="font-family: 'Outfit', sans-serif !important; font-size: 1.15rem; letter-spacing: 0.5px;">
                    <i class="bi bi-pencil-square me-2" style="font-size: 1.25rem; color: #fff !important;"></i> Edit Class Details
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close" style="filter: invert(1); opacity: 0.8;"></button>
            </div>
            <form id="editForm" method="POST" action="{{route('updateClass')}}">
                @csrf
                <div class="modal-body p-4" style="background-color: #f8fafc;">
                    <input type="hidden" name="id" id="editId">
                    <div class="form-floating mb-4">
                        <input type="text" class="form-control" id="editClassName" name="className" placeholder="Class Name" required style="border-radius: 12px; border: 1.5px solid #e2e8f0; padding: 12px 15px; font-weight: 600; color: #1e293b; font-size: 0.9rem;">
                        <label for="editClassName" style="font-weight: 600; color: #899bbd;">Class Name</label>
                    </div>

                    <h6 class="fw-bold text-primary mb-3" style="color: #004ac6 !important; font-family: 'Outfit', sans-serif; font-size: 0.95rem; font-weight: 700; letter-spacing: 0.3px;">Manage Sections</h6>
                    <div class="input-group mb-3">
                        <input type="text" id="editModalNewSection" class="form-control" placeholder="Section Name (e.g. D)" style="border: 1.5px solid #e2e8f0; border-right: none; border-radius: 12px 0 0 12px; font-weight: 600; color: #1e293b; font-size: 0.9rem; padding: 12px 15px;">
                        <button type="button" id="editModalAddSectionBtn" class="btn btn-primary px-4" style="background: linear-gradient(135deg, #004ac6, #1e40af) !important; border: none; border-radius: 0 12px 12px 0; font-weight: 700; font-size: 0.85rem;"><i class="bi bi-plus-lg"></i> Add</button>
                    </div>

                    <div class="table-responsive rounded-3 border" style="max-height: 250px; background: white; border: 1.5px solid #e2e8f0 !important; border-radius: 12px !important;">
                        <table class="table table-sm table-hover mb-0" style="margin-bottom: 0 !important; border-collapse: collapse !important; border-spacing: 0 !important;">
                            <thead class="bg-light">
                                <tr>
                                    <th class="ps-3 py-2" style="background: #f1f5f9 !important; color: #475569 !important; font-weight: 700; font-size: 0.78rem; text-transform: uppercase;">Section Name</th>
                                    <th class="text-end pe-3 py-2" style="background: #f1f5f9 !important; color: #475569 !important; font-weight: 700; font-size: 0.78rem; text-transform: uppercase;">Action</th>
                                </tr>
                            </thead>
                            <tbody id="editModalSectionsBody">
                                <!-- Sections load here -->
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="modal-footer border-0 p-4" style="background-color: #f1f5f9; display: flex; gap: 10px; justify-content: flex-end;">
                    <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal" style="font-weight: 700; font-size: 0.85rem; padding: 10px 24px; border: 1.5px solid #e2e8f0;">Close</button>
                    <button type="submit" class="btn btn-primary rounded-pill px-5 shadow-lg" style="background: linear-gradient(135deg, #004ac6, #1e40af) !important; border: none; font-weight: 700; font-size: 0.85rem; padding: 10px 28px; box-shadow: 0 4px 15px rgba(0, 74, 198, 0.25) !important;">Save Changes</button>
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