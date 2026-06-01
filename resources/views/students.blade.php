@extends('layouts.app')

@section('content')
<link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css" rel="stylesheet" />
<link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.min.css">

<div class="pagetitle">
    <h1>Students Management</h1>
    <nav>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
            <li class="breadcrumb-item active">Students</li>
        </ol>
    </nav>
</div>

<section class="section">
    <div class="row">
        <!-- Add Student Form -->
        <div class="col-lg-3">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">Add New Student</h5>
                </div>
                <div class="card-body pt-3">
                    @if ($errors->any())
                    <div class="alert alert-danger py-2 small">
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                    @endif

                    @if($message = Session::get('message'))
                    <div class="alert alert-info py-2 small">{{$message}}</div>
                    @endif

                    <form id="studentAddForm" action="{{url('addStudent')}}" method="POST" class="row g-3">
                        @csrf

                        <div class="col-12 mb-2">
                            <label class="form-label">Branch Name</label>
                            <select class="form-select select2" name="school_id" required>
                                @foreach($schoolList as $rowSchoolList)
                                <option value="{{$rowSchoolList->id}}">{{$rowSchoolList->schoolName}}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-12 mb-2">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <label class="form-label mb-0">Father / Parent Name</label>
                                <button type="button" class="btn btn-link btn-sm p-0 text-decoration-none fw-bold" data-bs-toggle="modal" data-bs-target="#addNewParentModal">
                                    <i class="bi bi-plus-circle me-1"></i>New
                                </button>
                            </div>
                            <select required class="form-select select2" name="parentID">
                                <option value="">-- Select Parent --</option>
                                @foreach($parentList as $rowParentList)
                                <option value="{{$rowParentList->id}}">{{$rowParentList->parentName}}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-12 mb-2">
                            <label class="form-label">Class</label>
                            <select class="form-select select2" name="class_id" id="classSelect" required>
                                @foreach($classList as $rowClassList)
                                <option value="{{$rowClassList->id}}">{{$rowClassList->className}}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-12 mb-2">
                            <label class="form-label">Section</label>
                            <select class="form-select" name="section" id="sectionSelect">
                                <option value="">Select Section</option>
                            </select>
                        </div>

                        <div class="col-12 mb-2">
                            <div class="form-floating">
                                @php
                                $tenantId = auth()->user()->tenant_id ?? null;
                                $latestStudent = DB::table('students')
                                ->when($tenantId, function($q) use ($tenantId) {
                                $q->where('tenant_id', $tenantId);
                                })
                                ->orderByDesc(DB::raw('CAST(grno AS UNSIGNED)'))
                                ->first();
                                $grno = $latestStudent ? (int) $latestStudent->grno : 0;
                                $nextGr = $grno + 1;
                                @endphp
                                <input required class="form-control" name="grno" id="grno" value="{{ sprintf('%02d', $nextGr) }}" type="text" placeholder="GR No" />
                                <label for="grno">GR No</label>
                            </div>
                        </div>

                        <div class="col-12 mb-2">
                            <div class="form-floating">
                                <input required class="form-control" name="studentName" id="studentName" type="text" placeholder="Student Name" />
                                <label for="studentName">Student Name</label>
                            </div>
                        </div>

                        <div class="col-12 mb-2">
                            <label class="form-label">Gender</label>
                            <select class="form-select" name="gender">
                                <option value="Male">Male</option>
                                <option value="Female">Female</option>
                            </select>
                        </div>

                        <div class="col-12 mt-4">
                            <button class="btn btn-primary w-100 py-3 rounded-pill shadow" type="submit">
                                <i class="bi bi-person-plus me-2"></i> Add Student
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Student List -->
        <div class="col-lg-9">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title">Student List</h5>
                    <button type="button" class="btn btn-light border btn-sm" id="printStudentsDT">
                        <i class="bi bi-printer me-1"></i> Print List
                    </button>
                </div>
                <div class="card-body pt-3">
                    <div class="table-responsive">
                        <table id="studentTable" class="table table-hover w-100">
                            <thead>
                                <tr>
                                    <th>S.NO</th>
                                    <th>GR No.</th>
                                    <th>Branch</th>
                                    <th>Class</th>
                                    <th>Section</th>
                                    <th>Student Name</th>
                                    <th>Gender</th>
                                    <th>Father Name</th>
                                    <th>Status</th>
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

<!-- Modals -->
<div class="modal fade" id="addNewParentModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 20px;">
            <div class="modal-header border-0 bg-primary text-white p-4" style="border-radius:20px 20px 0 0;">
                <h5 class="modal-title fw-bold">Add New Father / Parent</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{url('addParentFromStudent')}}" method="POST">
                @csrf
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Branch Name</label>
                        <select class="form-select" name="school_id" required>
                            @foreach($schoolList as $rowSchoolList)
                            <option value="{{$rowSchoolList->id}}">{{$rowSchoolList->schoolName}}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-floating mb-3">
                        <input class="form-control" name="parentName" id="mParentName" type="text" placeholder="Name" required />
                        <label for="mParentName">Father Name</label>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">School Employee?</label>
                        <select name="is_commandercityschool_employee" class="form-select">
                            <option value="No">No</option>
                            <option value="Yes">Yes</option>
                        </select>
                    </div>
                    <div class="form-floating mb-3">
                        <input class="form-control" name="phoneNumber" id="mPhone" type="text" placeholder="Phone" />
                        <label for="mPhone">Phone Number</label>
                    </div>
                    <div class="form-floating mb-3">
                        <input class="form-control" name="address" id="mAddress" type="text" placeholder="Address" />
                        <label for="mAddress">Address</label>
                    </div>
                </div>
                <div class="modal-footer border-0 p-4">
                    <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary rounded-pill px-4 shadow">Add Parent</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="promoteModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 20px;">
            <div class="modal-header border-0 bg-success text-white p-4" style="border-radius:20px 20px 0 0;">
                <h5 class="modal-title fw-bold">Promote Student</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form id="editForm" method="POST" action="{{route('promoteStudent')}}">
                @csrf
                <div class="modal-body p-4">
                    <input type="hidden" name="id" id="studentID">
                    <div class="form-floating mb-3">
                        <input type="text" class="form-control bg-light" id="editStudentName" name="studentName" readonly placeholder="Name">
                        <label for="editStudentName">Student Name</label>
                    </div>
                    <div class="form-floating mb-3">
                        <input type="text" class="form-control bg-light" id="editClassName" name="previousClassName" readonly placeholder="Prev Class">
                        <label for="editClassName">Previous Class</label>
                    </div>
                    <div class="form-floating mb-3">
                        <input type="text" class="form-control bg-light" id="editParentName" name="parentName" readonly placeholder="Parent">
                        <label for="editParentName">Parent Name</label>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Promote to Class</label>
                        <select name="promoteToClass" class="form-select select2" required style="width:100%">
                            @foreach($allClasses as $rowAllClasses)
                            <option value="{{ json_encode(['id' => $rowAllClasses->id, 'className' => $rowAllClasses->className]) }}">
                                {{ $rowAllClasses->className }}
                            </option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="modal-footer border-0 p-4">
                    <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-success rounded-pill px-4 shadow">Confirm Promotion</button>
                </div>
            </form>
        </div>
    </div>
</div>

@section('scripts')
<script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>
<script>
    $(document).ready(function() {
        $('.select2').select2({
            width: '100%'
        });

        var table = $('#studentTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: "{{ route('getStudents') }}",
                error: function(xhr, error, code) {
                    console.error("DataTables Error:", error);
                }
            },
            order: [
                [1, 'asc']
            ],
            columns: [{
                    data: "serialNumber",
                    name: "id"
                },
                {
                    data: "grno",
                    name: "grno"
                },
                {
                    data: "schoolName",
                    name: "schoolName"
                },
                {
                    data: "className",
                    name: "className"
                },
                {
                    data: "section",
                    name: "section"
                },
                {
                    data: "studentName",
                    name: "studentName",
                    render: function(data, type, row) {
                        if (type !== 'display') return data;
                        return '<div class="fw-bold text-primary">' + data + '</div>';
                    }
                },
                {
                    data: "gender",
                    name: "gender"
                },
                {
                    data: "parentName",
                    name: "parentName",
                    render: function(data, type, row) {
                        if (type !== 'display') return data;
                        var linkUrl = row.parentId ? '{{ url("parents/view") }}/' + row.parentId : '#';
                        return '<a href="' + linkUrl + '" class="text-secondary small fw-bold">' + (data || 'N/A') + '</a>';
                    }
                },
                {
                    data: "status",
                    name: "status",
                    render: function(data) {
                        return '<span class="badge bg-success">' + data + '</span>';
                    }
                },
                {
                    data: "id",
                    name: "action",
                    className: "text-center",
                    orderable: false,
                    searchable: false,
                    render: function(data, type, row) {
                        var sName = row.studentName ? row.studentName.replace(/'/g, "\\'") : '';
                        var pName = row.parentName ? row.parentName.replace(/'/g, "\\'") : '';
                        var cName = row.className ? row.className.replace(/'/g, "\\'") : '';

                        return '<div class="d-flex justify-content-center gap-2">' +
                            '<a href="{{url("editStudent")}}/' + data + '" class="btn btn-outline-primary btn-sm rounded-pill"><i class="bi bi-pencil"></i></a>' +
                            '<button type="button" class="btn btn-outline-success btn-sm rounded-pill promote-btn" data-id="' + data + '" data-name="' + sName + '" data-class="' + cName + '" data-parent="' + pName + '"><i class="bi bi-arrow-up-circle"></i></button>' +
                            '<a href="{{url("deleteStudent")}}/' + data + '" class="btn btn-outline-danger btn-sm rounded-pill" onclick="return confirm(\'Delete this student?\')"><i class="bi bi-trash"></i></a>' +
                            '</div>';
                    }
                }
            ],
            language: {
                search: "_INPUT_",
                searchPlaceholder: "Search students...",
                paginate: {
                    previous: '<i class="bi bi-chevron-left"></i>',
                    next: '<i class="bi bi-chevron-right"></i>'
                }
            }
        });

        $(document).on('click', '.promote-btn', function() {
            $('#studentID').val($(this).data('id'));
            $('#editStudentName').val($(this).data('name'));
            $('#editClassName').val($(this).data('class'));
            $('#editParentName').val($(this).data('parent'));
            $('#promoteModal').modal('show');
        });

        $('#classSelect').on('change', function() {
            var classId = $(this).val();
            if (classId) {
                $.get('{{ request()->getBaseUrl() }}/sections/class/' + classId, function(data) {
                    $('#sectionSelect').empty().append('<option value="">Select Section</option>');
                    $.each(data, function(k, v) {
                        $('#sectionSelect').append('<option value="' + v.sectionName + '">' + v.sectionName + '</option>');
                    });
                });
            }
        });

        $('#printStudentsDT').on('click', function() {
            window.print();
        });
    });
</script>
@endsection
@endsection