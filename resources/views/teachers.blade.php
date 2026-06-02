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
    <!-- Add Action Row -->
    <div class="row mb-4">
        <div class="col-md-12 text-end">
            <button type="button" class="btn btn-primary px-4 py-2 shadow-lg" data-bs-toggle="modal" data-bs-target="#addTeacherModal" 
                    style="background: linear-gradient(135deg, #004ac6, #1e40af) !important; 
                           border: none !important; 
                           color: #ffffff !important; 
                           font-weight: 700; 
                           border-radius: 12px; 
                           font-size: 0.88rem; 
                           letter-spacing: 0.3px;
                           box-shadow: 0 4px 15px rgba(0, 74, 198, 0.25) !important;
                           transition: all 0.3s ease;">
                <i class="bi bi-person-plus-fill me-2"></i> Add New Teacher
            </button>
        </div>
    </div>

    <div class="row">
        <!-- Teacher List -->
        <div class="col-lg-12">
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

    <!-- Add Teacher Modal -->
    <div class="modal fade add-teacher-modal" id="addTeacherModal" tabindex="-1" role="dialog" aria-labelledby="addTeacherModalLabel" aria-hidden="true" data-backdrop="static">
      <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content modal-content-premium border-0 shadow-lg" style="border-radius: 24px !important; overflow: hidden;">
          <div class="modal-header border-0 bg-primary text-white p-4" style="background: linear-gradient(135deg, #004ac6 0%, #1e40af 100%) !important; padding: 1.75rem 2rem !important; display: flex !important; align-items: center !important;">
            <h5 class="modal-title fw-bold text-white mb-0 d-flex align-items-center" id="addTeacherModalLabel" style="font-family: 'Outfit', sans-serif !important; font-size: 1.15rem; letter-spacing: 0.5px;">
              <i class="bi bi-person-plus-fill me-2" style="font-size: 1.25rem; color: #fff !important;"></i> Add New Teacher
            </h5>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close" style="filter: invert(1); opacity: 0.8;"></button>
          </div>
          <form action="{{url('addTeacher')}}" method="POST">
            @csrf
            <div class="modal-body p-4" style="background-color: #f8fafc; text-align: left !important;">
              
              <div class="mb-4">
                <label class="form-label" style="font-weight: 700; color: #475569; font-size: 0.8rem; margin-bottom: 8px;">Branch Name <span class="text-danger">*</span></label>
                @if($schoolList->isEmpty())
                <div class="alert alert-warning py-2 mb-2" style="font-size: 11px;">No branches found. Please add a branch first.</div>
                @endif
                <div class="input-group">
                  <span class="input-group-text" style="background-color: rgba(0, 74, 198, 0.04); border: 1.5px solid #e2e8f0; border-right: none; border-radius: 12px 0 0 12px; color: #004ac6; padding: 10px 15px;"><i class="bi bi-building"></i></span>
                  <select class="form-select select2-add-modal" name="school_id" required style="border-radius: 0 12px 12px 0; border: 1.5px solid #e2e8f0; border-left: none; padding: 12px 15px; font-weight: 600; color: #1e293b; font-size: 0.9rem;">
                    <option value="">-- Select Branch --</option>
                    @foreach($schoolList as $sch)
                    <option value="{{$sch->id}}">{{$sch->schoolName}}</option>
                    @endforeach
                  </select>
                </div>
              </div>
              
              <div class="form-floating mb-4">
                <input class="form-control" name="teacherName" id="teacherName" type="text" placeholder="Teacher Name" required style="border-radius: 12px; border: 1.5px solid #e2e8f0; padding: 12px 15px; font-weight: 600; color: #1e293b; font-size: 0.9rem;">
                <label for="teacherName" style="font-weight: 600; color: #899bbd;">Teacher Name <span class="text-danger">*</span></label>
              </div>

              <div class="form-floating mb-4">
                <input class="form-control" name="teacherEmail" id="teacherEmail" type="email" placeholder="Email" required style="border-radius: 12px; border: 1.5px solid #e2e8f0; padding: 12px 15px; font-weight: 600; color: #1e293b; font-size: 0.9rem;">
                <label for="teacherEmail" style="font-weight: 600; color: #899bbd;">Teacher Email <span class="text-danger">*</span></label>
              </div>

              <div class="form-floating mb-4">
                <input class="form-control" name="teacherPhoneNumber" id="teacherPhone" type="text" placeholder="Phone Number" style="border-radius: 12px; border: 1.5px solid #e2e8f0; padding: 12px 15px; font-weight: 600; color: #1e293b; font-size: 0.9rem;">
                <label for="teacherPhone" style="font-weight: 600; color: #899bbd;">Phone Number</label>
              </div>

              <div class="form-floating mb-4">
                <input class="form-control" name="teacherPassword" id="teacherPass" type="password" placeholder="Password" required style="border-radius: 12px; border: 1.5px solid #e2e8f0; padding: 12px 15px; font-weight: 600; color: #1e293b; font-size: 0.9rem;">
                <label for="teacherPass" style="font-weight: 600; color: #899bbd;">Login Password <span class="text-danger">*</span></label>
              </div>
              
              <div class="mb-4">
                <label class="form-label" style="font-weight: 700; color: #475569; font-size: 0.8rem; margin-bottom: 8px;">Assign Class</label>
                <div class="input-group">
                  <span class="input-group-text" style="background-color: rgba(0, 74, 198, 0.04); border: 1.5px solid #e2e8f0; border-right: none; border-radius: 12px 0 0 12px; color: #004ac6; padding: 10px 15px;"><i class="bi bi-journal-bookmark"></i></span>
                  <select class="form-select select2-add-modal" name="class_id" style="border-radius: 0 12px 12px 0; border: 1.5px solid #e2e8f0; border-left: none; padding: 12px 15px; font-weight: 600; color: #1e293b; font-size: 0.9rem;">
                    <option value="">None</option>
                    @foreach($classList as $cls)
                    <option value="{{$cls->id}}">{{$cls->className}}</option>
                    @endforeach
                  </select>
                </div>
              </div>

            </div>
            <div class="modal-footer border-0 p-4" style="background-color: #f1f5f9; display: flex; gap: 10px; justify-content: flex-end;">
              <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal" style="font-weight: 700; font-size: 0.85rem; padding: 10px 24px; border: 1.5px solid #e2e8f0;">Close</button>
              <button type="submit" class="btn btn-primary rounded-pill px-5 shadow-lg" style="background: linear-gradient(135deg, #004ac6, #1e40af) !important; border: none; font-weight: 700; font-size: 0.85rem; padding: 10px 28px; box-shadow: 0 4px 15px rgba(0, 74, 198, 0.25) !important;">
                <i class="bi bi-person-plus-fill me-1"></i> Add Teacher
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>

    <!-- Edit Modals (Moved outside table for stability) -->
    @foreach($teacherList as $rowStudentList)
    <div class="modal fade edit-teacher-modal" id="editModal{{$rowStudentList->id}}" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content modal-content-premium border-0 shadow-lg" style="border-radius: 24px !important; overflow: hidden;">
                <div class="modal-header border-0 bg-primary text-white p-4" style="background: linear-gradient(135deg, #004ac6 0%, #1e40af 100%) !important; padding: 1.75rem 2rem !important; display: flex !important; align-items: center !important;">
                    <h5 class="modal-title fw-bold text-white mb-0 d-flex align-items-center" style="font-family: 'Outfit', sans-serif !important; font-size: 1.15rem; letter-spacing: 0.5px;">
                        <i class="bi bi-pencil-square me-2" style="font-size: 1.25rem; color: #fff !important;"></i> Edit Teacher
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close" style="filter: invert(1); opacity: 0.8;"></button>
                </div>
                <form method="POST" action="{{ route('teachers.update') }}">
                    @csrf
                    <input type="hidden" name="id" value="{{$rowStudentList->id}}" />
                    <div class="modal-body p-4" style="background-color: #f8fafc; text-align: left !important;">
                        <div class="mb-4">
                            <label class="form-label" style="font-weight: 700; color: #475569; font-size: 0.8rem; margin-bottom: 8px;">Branch Name <span class="text-danger">*</span></label>
                            <div class="input-group">
                              <span class="input-group-text" style="background-color: rgba(0, 74, 198, 0.04); border: 1.5px solid #e2e8f0; border-right: none; border-radius: 12px 0 0 12px; color: #004ac6; padding: 10px 15px;"><i class="bi bi-building"></i></span>
                              <select name="school_id" class="form-select select2-modal" style="border-radius: 0 12px 12px 0; border: 1.5px solid #e2e8f0; border-left: none; padding: 12px 15px; font-weight: 600; color: #1e293b; font-size: 0.9rem;">
                                  @foreach($schoolList as $sch)
                                  <option value="{{$sch->id}}" {{ $sch->id == $rowStudentList->school_id ? 'selected' : '' }}>{{$sch->schoolName}}</option>
                                  @endforeach
                              </select>
                            </div>
                        </div>
                        <div class="form-floating mb-4">
                            <input class="form-control" name="teacherName" id="editName{{$rowStudentList->id}}" value="{{$rowStudentList->teacherName}}" placeholder="Name" required style="border-radius: 12px; border: 1.5px solid #e2e8f0; padding: 12px 15px; font-weight: 600; color: #1e293b; font-size: 0.9rem;" />
                            <label for="editName{{$rowStudentList->id}}" style="font-weight: 600; color: #899bbd;">Teacher Name</label>
                        </div>
                        <div class="form-floating mb-4">
                            <input class="form-control" name="teacherEmail" id="editEmail{{$rowStudentList->id}}" type="email" value="{{$rowStudentList->email}}" placeholder="Email" required style="border-radius: 12px; border: 1.5px solid #e2e8f0; padding: 12px 15px; font-weight: 600; color: #1e293b; font-size: 0.9rem;" />
                            <label for="editEmail{{$rowStudentList->id}}" style="font-weight: 600; color: #899bbd;">Email</label>
                        </div>
                        <div class="form-floating mb-4">
                            <input class="form-control" name="teacherPhoneNumber" id="editPhone{{$rowStudentList->id}}" value="{{$rowStudentList->phone}}" placeholder="Phone" style="border-radius: 12px; border: 1.5px solid #e2e8f0; padding: 12px 15px; font-weight: 600; color: #1e293b; font-size: 0.9rem;" />
                            <label for="editPhone{{$rowStudentList->id}}" style="font-weight: 600; color: #899bbd;">Phone Number</label>
                        </div>
                        <div class="mb-2">
                            <label class="form-label" style="font-weight: 700; color: #475569; font-size: 0.8rem; margin-bottom: 8px;">Assign Class</label>
                            <div class="input-group">
                              <span class="input-group-text" style="background-color: rgba(0, 74, 198, 0.04); border: 1.5px solid #e2e8f0; border-right: none; border-radius: 12px 0 0 12px; color: #004ac6; padding: 10px 15px;"><i class="bi bi-journal-bookmark"></i></span>
                              <select name="class_id" class="form-select select2-modal" style="border-radius: 0 12px 12px 0; border: 1.5px solid #e2e8f0; border-left: none; padding: 12px 15px; font-weight: 600; color: #1e293b; font-size: 0.9rem;">
                                  <option value="">None</option>
                                  @foreach($classList as $cls)
                                  <option value="{{$cls->id}}" {{ ($rowStudentList->class_id == $cls->id) ? 'selected' : '' }}>{{$cls->className}}</option>
                                  @endforeach
                              </select>
                            </div>
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
        $('.edit-teacher-modal, .add-teacher-modal').on('shown.bs.modal', function() {
            const modalId = $(this).attr('id');
            $(this).find('.select2-modal, .select2-add-modal').select2({
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