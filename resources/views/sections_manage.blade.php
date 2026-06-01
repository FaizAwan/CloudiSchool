@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card shadow-sm border-0">
                <div style="background: linear-gradient(to right, #243b55, #141e30); color:#fff;" class="card-header d-flex justify-content-between align-items-center py-3">
                    <h4 class="mb-0 fw-bold"><i class="bi bi-layers me-2"></i>SECTION MANAGEMENT</h4>
                </div>
                
                <div class="card-body p-4 bg-light">
                    @if (session('message'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('message') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    <div class="row g-4">
                        <!-- Add Section Form -->
                        <div class="col-lg-4">
                            <div class="card h-100 border-0 shadow-sm">
                                <div class="card-header bg-primary text-white py-3">
                                    <h5 class="mb-0 fw-bold">Add New Section</h5>
                                </div>
                                <div class="card-body py-4">
                                    <form id="addSectionForm">
                                        @csrf
                                        <div class="mb-4">
                                            <label class="form-label fw-bold">Select Class <span class="text-danger">*</span></label>
                                            <select class="form-select form-select-lg" name="class_id" id="classSelect" required>
                                                <option value="">-- Choose Class --</option>
                                                @foreach($classes as $class)
                                                    <option value="{{ $class->id }}">{{ $class->schoolName }} - {{ $class->className }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        
                                        <div class="mb-4">
                                            <label class="form-label fw-bold">Section Name <span class="text-danger">*</span></label>
                                            <div class="input-group input-group-lg">
                                                <span class="input-group-text bg-white"><i class="bi bi-type text-primary"></i></span>
                                                <input type="text" class="form-control" name="sectionName" id="sectionNameInput" placeholder="e.g. A, Rose, Lotus" required>
                                            </div>
                                            <div class="form-text mt-2 text-muted">Enter a single section name (e.g. "A").</div>
                                        </div>
                                        
                                        <button type="submit" class="btn btn-primary btn-lg w-100 py-3 fw-bold mt-2 shadow-sm" id="submitBtn">
                                            <i class="bi bi-plus-circle me-2"></i>Save Section
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>

                        <!-- Sections List -->
                        <div class="col-lg-8">
                            <div class="card h-100 border-0 shadow-sm">
                                <div class="card-header bg-dark text-white py-3 d-flex justify-content-between align-items-center">
                                    <h5 class="mb-0 fw-bold">Existing Sections</h5>
                                    <span class="badge bg-secondary" id="classLabel">Select a class to view</span>
                                </div>
                                <div class="card-body p-0">
                                    <div id="sectionsTableContainer" class="table-responsive">
                                        <table class="table table-hover align-middle mb-0">
                                            <thead class="table-light">
                                                <tr>
                                                    <th class="ps-4" style="width: 80px">#</th>
                                                    <th>Section Name</th>
                                                    <th class="text-end pe-4" style="width: 150px">Actions</th>
                                                </tr>
                                            </thead>
                                            <tbody id="sectionsTbody">
                                                <tr>
                                                    <td colspan="3" class="text-center py-5 text-muted">
                                                        <i class="bi bi-info-circle fs-2 d-block mb-2"></i>
                                                        Please select a class from the left to manage its sections.
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                    
                                    <!-- Loading Spinner -->
                                    <div id="tableLoader" class="text-center py-5 d-none">
                                        <div class="spinner-border text-primary" role="status">
                                            <span class="visually-hidden">Loading...</span>
                                        </div>
                                        <p class="mt-2 text-muted fw-bold">Fetching sections...</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Edit Section Modal -->
<div class="modal fade" id="editSectionModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header bg-warning py-3">
                <h5 class="modal-title fw-bold"><i class="bi bi-pencil-square me-2"></i>Edit Section</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="editSectionForm">
                @csrf
                @method('PUT')
                <div class="modal-body p-4">
                    <input type="hidden" id="edit_section_id">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Section Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control form-control-lg" id="edit_section_name" name="sectionName" required>
                    </div>
                </div>
                <div class="modal-footer bg-light border-0 p-3">
                    <button type="button" class="btn btn-secondary px-4" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary px-4 fw-bold shadow-sm" id="updateBtn">Update Section</button>
                </div>
            </form>
        </div>
    </div>
</div>

@section('scripts')
<script>
$(function() {
    const classSelect = $('#classSelect');
    const sectionsTbody = $('#sectionsTbody');
    const tableLoader = $('#tableLoader');
    const tableContainer = $('#sectionsTableContainer');
    const classLabel = $('#classLabel');
    const BASE_URL = "{{ request()->getBaseUrl() }}";

    // Handle Class Selection
    classSelect.on('change', function() {
        const classId = $(this).val();
        if (!classId) {
            resetTable();
            return;
        }
        
        const className = $(this).find('option:selected').text();
        classLabel.text(className).removeClass('bg-secondary').addClass('bg-primary');
        loadSections(classId);
    });

    function loadSections(classId) {
        tableContainer.addClass('d-none');
        tableLoader.removeClass('d-none');
        
        $.get(`${BASE_URL}/sections/class/${classId}`, function(data) {
            tableLoader.addClass('d-none');
            tableContainer.removeClass('d-none');
            
            let html = '';
            if (data.length === 0) {
                html = '<tr><td colspan="3" class="text-center py-5 text-muted">No sections found for this class.</td></tr>';
            } else {
                data.forEach((sec, index) => {
                    html += `
                        <tr class="section-row">
                            <td class="ps-4 fw-bold text-muted">${index + 1}</td>
                            <td><span class="fs-5 fw-medium text-dark">${sec.sectionName}</span></td>
                            <td class="text-end pe-4">
                                <button type="button" class="btn btn-sm btn-outline-warning me-2 edit-btn shadow-sm" 
                                    data-id="${sec.id}" data-name="${sec.sectionName}">
                                    <i class="bi bi-pencil-fill"></i>
                                </button>
                                <button type="button" class="btn btn-sm btn-outline-danger delete-btn shadow-sm" 
                                    data-id="${sec.id}" data-name="${sec.sectionName}">
                                    <i class="bi bi-trash-fill"></i>
                                </button>
                            </td>
                        </tr>
                    `;
                });
            }
            sectionsTbody.html(html);
        }).fail(function() {
            tableLoader.addClass('d-none');
            tableContainer.removeClass('d-none');
            sectionsTbody.html('<tr><td colspan="3" class="text-center py-5 text-danger fw-bold">Error loading data. Please try again.</td></tr>');
        });
    }

    function resetTable() {
        classLabel.text('Select a class to view').removeClass('bg-primary').addClass('bg-secondary');
        sectionsTbody.html('<tr><td colspan="3" class="text-center py-5 text-muted"><i class="bi bi-info-circle fs-2 d-block mb-2"></i>Please select a class from the left to manage its sections.</td></tr>');
    }

    // Add Section
    $('#addSectionForm').on('submit', function(e) {
        e.preventDefault();
        const classId = classSelect.val();
        const sectionName = $('#sectionNameInput').val().trim();
        const btn = $('#submitBtn');
        const originalText = btn.html();

        if (!classId) return alert('Please select a class first.');

        btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-2"></span>Saving...');

        $.post(`${BASE_URL}/sections`, $(this).serialize(), function(res) {
            btn.prop('disabled', false).html(originalText);
            if (res.ok) {
                $('#sectionNameInput').val('');
                toastr.success('Section added successfully');
                loadSections(classId);
            } else {
                alert('Error: ' + (res.message || 'Could not save section'));
            }
        }).fail(function(xhr) {
            btn.prop('disabled', false).html(originalText);
            alert('Failed to save: ' + (xhr.responseJSON?.message || 'Server error'));
        });
    });

    // Edit Button Click
    $(document).on('click', '.edit-btn', function() {
        const id = $(this).data('id');
        const name = $(this).data('name');
        $('#edit_section_id').val(id);
        $('#edit_section_name').val(name);
        $('#editSectionModal').modal('show');
    });

    // Update Section
    $('#editSectionForm').on('submit', function(e) {
        e.preventDefault();
        const id = $('#edit_section_id').val();
        const btn = $('#updateBtn');
        
        btn.prop('disabled', true).text('Updating...');

        $.ajax({
            url: `${BASE_URL}/sections/${id}`,
            type: 'PUT',
            data: $(this).serialize(),
            success: function(res) {
                btn.prop('disabled', false).text('Update Section');
                if (res.ok) {
                    $('#editSectionModal').modal('hide');
                    toastr.success('Section updated successfully');
                    loadSections(classSelect.val());
                }
            },
            error: function() {
                btn.prop('disabled', false).text('Update Section');
                alert('Error updating section');
            }
        });
    });

    // Delete Section
    $(document).on('click', '.delete-btn', function() {
        const id = $(this).data('id');
        const name = $(this).data('name');
        
        if (!confirm(`Are you sure you want to delete section "${name}"?`)) return;

        $.ajax({
            url: `${BASE_URL}/sections/${id}`,
            type: 'DELETE',
            data: { _token: '{{ csrf_token() }}' },
            success: function(res) {
                if (res.ok) {
                    toastr.info('Section deleted');
                    loadSections(classSelect.val());
                }
            }
        });
    });
});
</script>

<style>
    .section-row:hover { background-color: rgba(0,0,0,.02); }
    .form-select-lg, .form-control-lg { border-radius: 12px; }
    .btn-lg { border-radius: 12px; }
    .card { border-radius: 16px; overflow: hidden; }
</style>
@endsection
@endsection
