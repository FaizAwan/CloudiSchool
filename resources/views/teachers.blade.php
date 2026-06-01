@extends('layouts.app')

@section('content')
<link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css" rel="stylesheet" />
<link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.min.css">

<div class="pagetitle">
    <h1>Teachers Management</h1>
    <nav>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
            <li class="breadcrumb-item active">Teachers</li>
        </ol>
    </nav>
</div>

<section class="section">
    <div class="row">
        <!-- Add Teacher Form -->
        <div class="col-lg-3">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">Add New Teacher</h5>
                </div>
                <div class="card-body pt-3">
                    <form action="{{url('addTeacher')}}" method="POST" class="row g-3">
                        @csrf
                        <div class="col-12 mb-2">
                            <label class="form-label">Branch Name</label>
                            <select class="form-select select2" name="school_id" required>
                                @foreach($schoolList as $rowSchoolList)
                                <option value="{{$rowSchoolList->id}}">{{$rowSchoolList->schoolName}} - {{$rowSchoolList->address}}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-12 mb-2">
                            <div class="form-floating">
                                <input class="form-control" name="teacherName" id="teacherName" type="text" placeholder="Teacher Name" required />
                                <label for="teacherName">Teacher Name</label>
                            </div>
                        </div>

                        <div class="col-12 mb-2">
                            <div class="form-floating">
                                <input class="form-control" name="teacherEmail" id="teacherEmail" type="email" placeholder="Email" required />
                                <label for="teacherEmail">Teacher Email</label>
                            </div>
                        </div>

                        <div class="col-12 mb-2">
                            <div class="form-floating">
                                <input class="form-control" name="teacherPhoneNumber" id="teacherPhone" type="text" placeholder="Phone Number" />
                                <label for="teacherPhone">Phone Number</label>
                            </div>
                        </div>

                        <div class="col-12 mb-2">
                            <div class="form-floating">
                                <input class="form-control" name="teacherPassword" id="teacherPass" type="password" placeholder="Password" required />
                                <label for="teacherPass">Login Password</label>
                            </div>
                        </div>

                        <div class="col-12 mb-2">
                            <label class="form-label">Assign Class</label>
                            <select class="form-select select2" name="class_id">
                                <option value="">None</option>
                                @foreach($classList as $rowClasslList)
                                <option value="{{$rowClasslList->id}}">{{$rowClasslList->className}}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-12 mt-4">
                            <button class="btn btn-primary w-100 py-3 rounded-pill shadow" type="submit">
                                <i class="bi bi-person-plus me-2"></i> Add Teacher
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Teacher List -->
        <div class="col-lg-9">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title">Teacher List <span class="badge bg-light text-primary ms-2">{{ count($teacherList) }} Total</span></h5>
                </div>
                <div class="card-body pt-3">
                    <div class="table-responsive">
                        <table id="teacherTable" class="table table-hover">
                            <thead>
                                <tr>
                                    <th>S.NO</th>
                                    <th>Branch</th>
                                    <th>Class</th>
                                    <th>Teacher Name</th>
                                    <th>Email</th>
                                    <th>Phone</th>
                                    <th class="text-center">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php $number = 1; @endphp
                                @foreach($teacherList as $rowStudentList)
                                <tr>
                                    <td>{{$number++}}</td>
                                    <td>{{$rowStudentList->schoolName}}</td>
                                    <td><span class="badge bg-light text-dark bordey py-2 px-3">{{$rowStudentList->classNameFromJoin ?? $rowStudentList->className ?? 'N/A'}}</span></td>
                                    <td class="fw-bold text-primary">{{$rowStudentList->teacherName}}</td>
                                    <td>{{$rowStudentList->email}}</td>
                                    <td>{{$rowStudentList->phone}}</td>
                                    <td class="text-center">
                                        <div class="d-flex justify-content-center gap-2">
                                            <a href="{{ route('teachers.view', $rowStudentList->id) }}" class="btn btn-sm btn-outline-info rounded-pill" title="View"><i class="bi bi-eye"></i></a>
                                            <button type="button" class="btn btn-sm btn-outline-primary rounded-pill" data-bs-toggle="modal" data-bs-target="#editModal{{$rowStudentList->id}}" title="Edit"><i class="bi bi-pencil"></i></button>
                                            <a href="{{ route('teachers.delete', $rowStudentList->id) }}" class="btn btn-sm btn-outline-danger rounded-pill" onclick="return confirm('Delete this teacher?');" title="Delete"><i class="bi bi-trash"></i></a>
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

    <!-- Edit Modals (Moved outside table for stability) -->
    @foreach($teacherList as $rowStudentList)
    <div class="modal fade edit-teacher-modal" id="editModal{{$rowStudentList->id}}" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg" style="border-radius: 20px;">
                <div class="modal-header border-0 bg-primary text-white p-4" style="border-radius:20px 20px 0 0;">
                    <h5 class="modal-title fw-bold">Edit Teacher</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST" action="{{ route('teachers.update') }}">
                    @csrf
                    <input type="hidden" name="id" value="{{$rowStudentList->id}}" />
                    <div class="modal-body p-4 text-start">
                        <div class="mb-4">
                            <label class="form-label fw-bold">Branch</label>
                            <select name="school_id" class="form-select select2-modal" style="width:100%">
                                @foreach($schoolList as $sch)
                                <option value="{{$sch->id}}" {{ $sch->id == $rowStudentList->school_id ? 'selected' : '' }}>{{$sch->schoolName}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-floating mb-4">
                            <input class="form-control" name="teacherName" id="editName{{$rowStudentList->id}}" value="{{$rowStudentList->teacherName}}" placeholder="Name" required />
                            <label for="editName{{$rowStudentList->id}}">Teacher Name</label>
                        </div>
                        <div class="form-floating mb-4">
                            <input class="form-control" name="teacherEmail" id="editEmail{{$rowStudentList->id}}" type="email" value="{{$rowStudentList->email}}" placeholder="Email" required />
                            <label for="editEmail{{$rowStudentList->id}}">Email</label>
                        </div>
                        <div class="form-floating mb-4">
                            <input class="form-control" name="teacherPhoneNumber" id="editPhone{{$rowStudentList->id}}" value="{{$rowStudentList->phone}}" placeholder="Phone" />
                            <label for="editPhone{{$rowStudentList->id}}">Phone Number</label>
                        </div>
                        <div class="mb-2">
                            <label class="form-label fw-bold">Assign Class</label>
                            <select name="class_id" class="form-select select2-modal" style="width:100%">
                                <option value="">None</option>
                                @foreach($classList as $cls)
                                <option value="{{$cls->id}}" {{ ($rowStudentList->class_id == $cls->id) ? 'selected' : '' }}>{{$cls->className}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer border-0 p-4">
                        <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary rounded-pill px-4 shadow">Save changes</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

@section('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>
<script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
<script>
    $(document).ready(function() {
        $('#teacherTable').DataTable({
            pageLength: 25,
            order: [
                [0, 'asc']
            ],
            language: {
                search: "_INPUT_",
                searchPlaceholder: "Search teachers...",
                paginate: {
                    previous: '<i class="bi bi-chevron-left"></i>',
                    next: '<i class="bi bi-chevron-right"></i>'
                }
            }
        });
        $('.select2').select2({
            width: '100%'
        });

        // Initialize Select2 for modals with proper dropdown parent
        $('.edit-teacher-modal').on('shown.bs.modal', function() {
            const modalId = $(this).attr('id');
            $(this).find('.select2-modal').select2({
                dropdownParent: $('#' + modalId),
                width: '100%'
            });
        });

        setTimeout(function() {
            $('.alert').fadeOut('slow');
        }, 5000);
    });
</script>
@endsection
@endsection