@extends('layouts.app')

@section('content')
<style>
    .pagetitle h1 {
        font-weight: 800;
        color: #1e293b;
    }
    .card-premium {
        border-radius: 20px;
        border: none;
        overflow: hidden;
        background: rgba(255, 255, 255, 0.95);
        backdrop-filter: blur(10px);
    }
    .card-header-premium {
        background: #1e40af;
        color: white;
        padding: 1.5rem;
    }
    .table thead th {
        background: #f1f5f9 !important;
        color: #475569 !important;
        text-transform: uppercase;
        font-size: 0.75rem;
        font-weight: 800;
        padding: 1.25rem 1rem;
        border: none;
    }
    .btn-rounded {
        border-radius: 99px;
        padding: 0.5rem 1.5rem;
        font-weight: 700;
        transition: all 0.3s ease;
    }
    .btn-rounded:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
    }
    /* Modal customization */
    .modal-content {
        border-radius: 24px;
        border: none;
    }
    .modal-header {
        border-radius: 24px 24px 0 0;
        padding: 1.5rem 2rem;
    }
    .form-control, .form-select {
        border-radius: 12px;
        padding: 0.75rem 1rem;
        border: 1px solid #e2e8f0;
    }
    .form-control:focus {
        box-shadow: 0 0 0 4px rgba(30, 64, 175, 0.1);
        border-color: #3b82f6;
    }
    /* Bulk Edit Styles */
    .bulk-row {
        background: #f8fafc;
        border-radius: 16px;
        margin-bottom: 1rem;
        padding: 1rem;
        transition: all 0.2s;
    }
    .bulk-row:hover {
        background: #f1f5f9;
    }
</style>

<div class="pagetitle text-white">
    <h1>Subjects Management</h1>
    <nav>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ url('/') }}">Home</a></li>
            <li class="breadcrumb-item"><a href="{{ route('exams.index') }}">Exam Management</a></li>
            <li class="breadcrumb-item active">Subjects</li>
        </ol>
    </nav>
</div>

<section class="section">
    <div class="row">
        <div class="col-lg-12">
            <div class="card card-premium shadow-lg">
                <div class="card-header-premium d-flex justify-content-between align-items-center">
                    <h5 class="mb-0 fw-bold"><i class="bi bi-book-half me-2"></i> Manage School Subjects</h5>
                    <div class="d-flex gap-2">
                        <button type="button" class="btn btn-light btn-rounded text-primary shadow-sm" data-bs-toggle="modal" data-bs-target="#addSubjectModal">
                            <i class="bi bi-plus-circle me-1"></i> Add Subject
                        </button>
                        <button type="button" class="btn btn-success btn-rounded shadow-sm" data-bs-toggle="modal" data-bs-target="#addSubjectsBulkModal">
                            <i class="bi bi-collection me-1"></i> Bulk Add
                        </button>
                        <button type="button" class="btn btn-warning btn-rounded shadow-sm" data-bs-toggle="modal" data-bs-target="#editSubjectsBulkModal">
                            <i class="bi bi-pencil-square me-1"></i> Bulk Edit
                        </button>
                    </div>
                </div>

                <div class="card-body p-4">
                    <!-- Filter Section -->
                    <div class="bg-light p-4 rounded-4 mb-4 border-0 shadow-sm">
                        <form method="GET" action="{{ route('subjects.index') }}" class="row g-3 align-items-end">
                            <div class="col-md-3">
                                <label class="small fw-bold text-muted mb-2">Filter by Class</label>
                                <select class="form-select border-0 shadow-sm" name="class_id" onchange="this.form.submit()">
                                    <option value="">All Classes</option>
                                    @foreach($classes as $class)
                                    <option value="{{ $class->id }}" {{ ($selectedClassId == $class->id) ? 'selected' : '' }}>{{ $class->className }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="small fw-bold text-muted mb-2">Filter by Subject</label>
                                <select class="form-select border-0 shadow-sm" name="subject_name" onchange="this.form.submit()">
                                    <option value="">All Subjects</option>
                                    @foreach($uniqueSubjects as $subjectName)
                                    <option value="{{ $subjectName }}" {{ ($selectedSubjectName == $subjectName) ? 'selected' : '' }}>{{ $subjectName }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="small fw-bold text-muted mb-2">Filter by Term</label>
                                <select class="form-select border-0 shadow-sm" name="term" onchange="this.form.submit()">
                                    <option value="">All Terms</option>
                                    @foreach($availableTerms as $termValue => $termLabel)
                                    <option value="{{ $termValue }}" {{ ($selectedTerm == $termValue) ? 'selected' : '' }}>{{ $termLabel }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3 d-flex gap-2">
                                <button type="submit" class="btn btn-primary rounded-pill flex-fill fw-bold shadow-sm">
                                    <i class="bi bi-funnel me-1"></i> Apply Filters
                                </button>
                                @if($selectedClassId || $selectedSubjectName || $selectedTerm)
                                <a href="{{ route('subjects.index') }}" class="btn btn-outline-danger rounded-pill shadow-sm"><i class="bi bi-x-circle"></i></a>
                                @endif
                            </div>
                        </form>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-hover" id="subjectsTable">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Subject Name</th>
                                    <th>Class</th>
                                    <th>Marks (Total/Pass)</th>
                                    <th>Term</th>
                                    <th>Status</th>
                                    <th class="text-center">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($subjects as $index => $subject)
                                <tr>
                                    <td class="text-muted small">{{ $index + 1 }}</td>
                                    <td><h6 class="fw-bold text-primary mb-0">{{ $subject->subject_name }}</h6></td>
                                    <td><span class="badge bg-light text-dark shadow-sm px-2">{{ $subject->class->className ?? 'N/A' }}</span></td>
                                    <td>
                                        <div class="d-flex align-items-center gap-2">
                                            <span class="badge bg-info-light text-info">{{ $subject->total_marks }}</span>
                                            <span class="text-muted">/</span>
                                            <span class="badge bg-neutral-light text-secondary">{{ $subject->passing_marks }}</span>
                                        </div>
                                    </td>
                                    <td><span class="text-muted small">{{ $subject->term ?? 'General' }}</span></td>
                                    <td>
                                        @if($subject->status == 'active')
                                            <span class="badge bg-success-light text-success"><i class="bi bi-check-circle me-1"></i> Active</span>
                                        @else
                                            <span class="badge bg-danger-light text-danger"><i class="bi bi-dash-circle me-1"></i> Inactive</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        <div class="d-flex justify-content-center gap-2">
                                            <button class="btn btn-sm btn-outline-primary rounded-circle edit-subject-btn"
                                                data-bs-toggle="modal" data-bs-target="#editSubjectModal"
                                                data-id="{{ $subject->id }}"
                                                data-name="{{ $subject->subject_name }}"
                                                data-class="{{ $subject->class_id }}"
                                                data-total="{{ $subject->total_marks }}"
                                                data-pass="{{ $subject->passing_marks }}"
                                                data-term="{{ $subject->term }}"
                                                data-status="{{ $subject->status }}">
                                                <i class="bi bi-pencil"></i>
                                            </button>
                                            <form action="{{ route('subjects.destroy', $subject->id) }}" method="POST" class="d-inline">
                                                @csrf @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-outline-danger rounded-circle" onclick="return confirm('Delete this subject?')">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Add Subject Modal -->
<div class="modal fade" id="addSubjectModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content shadow-lg">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title fw-bold"><i class="bi bi-plus-circle me-2"></i> Add Individual Subject</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('subjects.store') }}" method="POST">
                @csrf
                <div class="modal-body p-4">
                    <div class="mb-4">
                        <label class="form-label fw-bold small">Subject Name</label>
                        <input type="text" class="form-control" name="subject_name" required placeholder="e.g. Mathematics">
                    </div>
                    <div class="mb-4">
                        <label class="form-label fw-bold small">Select Class</label>
                        <select class="form-select" name="class_id" required>
                            @foreach($classes as $class)
                            <option value="{{ $class->id }}">{{ $class->className }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="row g-3 mb-4">
                        <div class="col-6">
                            <label class="form-label fw-bold small">Total Marks</label>
                            <input type="number" class="form-control" name="total_marks" value="100" required>
                        </div>
                        <div class="col-6">
                            <label class="form-label fw-bold small">Passing Marks</label>
                            <input type="number" class="form-control" name="passing_marks" value="33" required>
                        </div>
                    </div>
                    <div class="row g-3">
                        <div class="col-6">
                            <label class="form-label fw-bold small">Term</label>
                            <select class="form-select" name="term">
                                @foreach($availableTerms as $val => $label)
                                <option value="{{ $val }}">{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-6">
                            <label class="form-label fw-bold small">Initial Status</label>
                            <select class="form-select" name="status">
                                <option value="active">Active</option>
                                <option value="inactive">Inactive</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0 p-4 pt-0">
                    <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary rounded-pill px-5 shadow fw-bold">Create Subject</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Bulk Add Modal -->
<div class="modal fade" id="addSubjectsBulkModal" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content shadow-lg">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title fw-bold"><i class="bi bi-collection me-2"></i> Bulk Subject Entry</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('subjects.bulkStore') }}" method="POST">
                @csrf
                <div class="modal-body p-4">
                    <p class="text-muted small mb-4">Add multiple subjects at once. At least one row is required.</p>
                    <div id="bulkAddContainer">
                        <!-- Rows injected via JS -->
                        <div class="bulk-row row g-3 align-items-center">
                            <div class="col-md-3">
                                <input type="text" class="form-control form-control-sm" name="subjects[0][subject_name]" placeholder="Subject Name" required>
                            </div>
                            <div class="col-md-3">
                                <select class="form-select form-select-sm" name="subjects[0][class_id]" required>
                                    @foreach($classes as $class)
                                    <option value="{{ $class->id }}">{{ $class->className }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2">
                                <input type="number" class="form-control form-control-sm" name="subjects[0][total_marks]" placeholder="Total" value="100">
                            </div>
                            <div class="col-md-2">
                                <input type="number" class="form-control form-control-sm" name="subjects[0][passing_marks]" placeholder="Pass" value="33">
                            </div>
                            <div class="col-md-2">
                                <input type="hidden" name="subjects[0][status]" value="active">
                                <button type="button" class="btn btn-sm btn-outline-danger w-100 rounded-pill remove-row-btn" disabled><i class="bi bi-trash"></i></button>
                            </div>
                        </div>
                    </div>
                    <button type="button" id="addNewRowBtn" class="btn btn-outline-primary btn-sm mt-3 rounded-pill"><i class="bi bi-plus-lg me-1"></i> Add Another Row</button>
                </div>
                <div class="modal-footer border-0 p-4">
                    <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success rounded-pill px-5 shadow fw-bold">Import All Subjects</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Bulk Edit Modal -->
<div class="modal fade" id="editSubjectsBulkModal" tabindex="-1">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content shadow-lg">
            <div class="modal-header bg-warning">
                <h5 class="modal-title fw-bold"><i class="bi bi-pencil-square me-2"></i> Bulk Update Active Subjects</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('subjects.bulkUpdate') }}" method="POST">
                @csrf
                <div class="modal-body p-4" style="max-height: 70vh; overflow-y: auto;">
                    <div class="table-responsive">
                        <table class="table table-sm table-hover align-middle">
                            <thead class="bg-light">
                                <tr>
                                    <th>Subject</th>
                                    <th>Class</th>
                                    <th>Total Marks</th>
                                    <th>Pass Marks</th>
                                    <th>Term</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($subjects as $index => $subject)
                                <tr>
                                    <td>
                                        <input type="hidden" name="subjects[{{ $index }}][id]" value="{{ $subject->id }}">
                                        <input type="text" class="form-control form-control-sm border-0 bg-transparent" name="subjects[{{ $index }}][subject_name]" value="{{ $subject->subject_name }}">
                                    </td>
                                    <td>
                                        <select class="form-select form-select-sm border-0 bg-transparent" name="subjects[{{ $index }}][class_id]">
                                            @foreach($classes as $class)
                                            <option value="{{ $class->id }}" {{ $subject->class_id == $class->id ? 'selected' : '' }}>{{ $class->className }}</option>
                                            @endforeach
                                        </select>
                                    </td>
                                    <td><input type="number" class="form-control form-control-sm" name="subjects[{{ $index }}][total_marks]" value="{{ $subject->total_marks }}"></td>
                                    <td><input type="number" class="form-control form-control-sm" name="subjects[{{ $index }}][passing_marks]" value="{{ $subject->passing_marks }}"></td>
                                    <td>
                                        <select class="form-select form-select-sm" name="subjects[{{ $index }}][term]">
                                            @foreach($availableTerms as $val => $label)
                                            <option value="{{ $val }}" {{ $subject->term == $val ? 'selected' : '' }}>{{ $label }}</option>
                                            @endforeach
                                        </select>
                                    </td>
                                    <td>
                                        <select class="form-select form-select-sm" name="subjects[{{ $index }}][status]">
                                            <option value="active" {{ $subject->status == 'active' ? 'selected' : '' }}>Active</option>
                                            <option value="inactive" {{ $subject->status == 'inactive' ? 'selected' : '' }}>Inactive</option>
                                        </select>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="modal-footer border-0 p-4">
                    <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Discard</button>
                    <button type="submit" class="btn btn-warning rounded-pill px-5 shadow fw-bold">Sync All Changes</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Individual Subject Modal -->
<div class="modal fade" id="editSubjectModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content shadow-lg">
            <div class="modal-header bg-warning">
                <h5 class="modal-title fw-bold text-dark"><i class="bi bi-pencil me-2"></i> Edit Subject</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="editSubjectForm" method="POST">
                @csrf @method('PUT')
                <div class="modal-body p-4">
                    <div class="mb-4">
                        <label class="form-label fw-bold small">Subject Name</label>
                        <input type="text" class="form-control" name="subject_name" id="edit_subject_name" required>
                    </div>
                    <div class="mb-4">
                        <label class="form-label fw-bold small">Select Class</label>
                        <select class="form-select" name="class_id" id="edit_class_id" required>
                            @foreach($classes as $class)
                            <option value="{{ $class->id }}">{{ $class->className }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="row g-3 mb-4">
                        <div class="col-6">
                            <label class="form-label fw-bold small">Total Marks</label>
                            <input type="number" class="form-control" name="total_marks" id="edit_total_marks" required>
                        </div>
                        <div class="col-6">
                            <label class="form-label fw-bold small">Passing Marks</label>
                            <input type="number" class="form-control" name="pass_marks" id="edit_pass_marks" required>
                        </div>
                    </div>
                    <div class="row g-3">
                        <div class="col-6">
                            <label class="form-label fw-bold small">Term</label>
                            <select class="form-select" name="term" id="edit_term">
                                @foreach($availableTerms as $val => $label)
                                <option value="{{ $val }}">{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-6">
                            <label class="form-label fw-bold small">Status</label>
                            <select class="form-select" name="status" id="edit_status">
                                <option value="active">Active</option>
                                <option value="inactive">Inactive</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0 p-4 pt-0">
                    <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-warning rounded-pill px-5 shadow fw-bold text-dark">Save Changes</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
    $(document).ready(function() {
        // Individual Edit Modal Population
        $('.edit-subject-btn').on('click', function() {
            const data = $(this).data();
            $('#edit_subject_name').val(data.name);
            $('#edit_class_id').val(data.class);
            $('#edit_total_marks').val(data.total);
            $('#edit_pass_marks').val(data.pass);
            $('#edit_term').val(data.term);
            $('#edit_status').val(data.status);
            $('#editSubjectForm').attr('action', '{{ url("subjects") }}/' + data.id);
        });

        // Bulk Add Row Management
        let rowIndex = 1;
        $('#addNewRowBtn').on('click', function() {
            const newRow = `
                <div class="bulk-row row g-3 align-items-center mt-2">
                    <div class="col-md-3">
                        <input type="text" class="form-control form-control-sm" name="subjects[${rowIndex}][subject_name]" placeholder="Subject Name" required>
                    </div>
                    <div class="col-md-3">
                        <select class="form-select form-select-sm" name="subjects[${rowIndex}][class_id]" required>
                            @foreach($classes as $class)
                            <option value="{{ $class->id }}">{{ $class->className }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <input type="number" class="form-control form-control-sm" name="subjects[${rowIndex}][total_marks]" placeholder="Total" value="100">
                    </div>
                    <div class="col-md-2">
                        <input type="number" class="form-control form-control-sm" name="subjects[${rowIndex}][passing_marks]" placeholder="Pass" value="33">
                    </div>
                    <div class="col-md-2">
                        <input type="hidden" name="subjects[${rowIndex}][status]" value="active">
                        <button type="button" class="btn btn-sm btn-outline-danger w-100 rounded-pill remove-row-btn"><i class="bi bi-trash"></i></button>
                    </div>
                </div>`;
            $('#bulkAddContainer').append(newRow);
            rowIndex++;
        });

        $(document).on('click', '.remove-row-btn', function() {
            $(this).closest('.bulk-row').remove();
        });
    });
</script>
<style>
    .bg-info-light { background: #e0f2fe; }
    .text-info { color: #0369a1 !important; }
    .bg-success-light { background: #dcfce7; }
    .text-success { color: #166534 !important; }
    .bg-danger-light { background: #fee2e2; }
    .text-danger { color: #991b1b !important; }
    .bg-neutral-light { background: #f1f5f9; }
</style>
@endsection