@extends('layouts.app')

@section('content')
<!-- High-Speed Asset Loading -->
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css" rel="stylesheet" />
<link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.min.css">

<style>
  body {
    font-family: 'Inter', sans-serif;
    background-color: #f8faff;
  }

  .pagetitle h1 {
    font-size: 24px;
    font-weight: 700;
    color: #012970;
  }

  /* Optimized Table Squeeze */
  #studentTable {
    table-layout: fixed;
    width: 100% !important;
    border-collapse: collapse !important;
  }

  #studentTable th,
  #studentTable td {
    padding: 8px 10px !important;
    font-size: 12px !important;
    white-space: nowrap !important;
    overflow: hidden !important;
    text-overflow: ellipsis !important;
    vertical-align: middle;
  }

  #studentTable th:nth-child(1) {
    width: 40px;
  }

  /* S.NO */
  #studentTable th:nth-child(2) {
    width: 80px;
  }

  /* GR No. */
  #studentTable th:nth-child(3) {
    width: 25%;
  }

  /* Student Name */
  #studentTable th:nth-child(4) {
    width: 20%;
  }

  /* Father Name */
  #studentTable th:nth-child(5) {
    width: 100px;
  }

  /* Class */
  #studentTable th:nth-child(6) {
    width: 80px;
  }

  /* Section */
  #studentTable th:nth-child(7) {
    width: 120px;
    text-align: right;
  }

  /* Action */

  .card {
    border: none;
    border-radius: 12px;
    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
  }

  .card-header {
    background: white;
    border-bottom: 1px solid #edf2f9;
    padding: 15px 20px;
  }

  /* Loading Indicator */
  #tableLoader {
    display: none;
    margin-left: 10px;
  }

  .badge-status {
    font-size: 10px;
    padding: 4px 8px;
    border-radius: 20px;
    font-weight: 700;
    text-transform: uppercase;
  }
</style>

<div class="pagetitle d-flex justify-content-between align-items-center mb-4">
  <h1><i class="bi bi-people-fill me-2" style="color: #4154f1;"></i>Students Master</h1>
  <div class="d-flex gap-2">
    <button class="btn btn-outline-primary btn-sm rounded-pill px-3" onclick="location.reload()"><i class="bi bi-arrow-clockwise"></i> Refresh</button>
    <button class="btn btn-primary btn-sm rounded-pill px-3" data-bs-toggle="collapse" data-bs-target="#addStudentFormBody"><i class="bi bi-plus-lg"></i> New Student</button>
  </div>
</div>

<div class="collapse mb-4" id="addStudentFormBody">
  <div class="card">
    <div class="card-body p-4">
      <h5 class="fw-bold mb-4 text-primary">Register New Student</h5>
      <form id="studentAddForm" action="{{url('addStudent')}}" method="POST">
        @csrf
        <div class="row g-3">
          <div class="col-md-3">
            <label class="form-label small fw-bold">GR Number</label>
            @php
            $tenantId = auth()->user()->tenant_id ?? null;
            $latestStudent = DB::table('students')->when($tenantId, fn($q)=>$q->where('tenant_id', $tenantId))->orderByDesc(DB::raw('CAST(grno AS UNSIGNED)'))->first();
            $nextGr = ($latestStudent ? (int) $latestStudent->grno : 0) + 1;
            @endphp
            <input class="form-control" name="grno" value="{{ sprintf('%02d', $nextGr) }}" type="text" required />
          </div>
          <div class="col-md-5">
            <label class="form-label small fw-bold">Student Full Name</label>
            <input class="form-control" name="studentName" type="text" placeholder="Full legal name" required />
          </div>
          <div class="col-md-4">
            <label class="form-label small fw-bold">Father's Name</label>
            <select class="form-select select2" name="parentID" required>
              <option value="">Select Parent</option>
              @foreach($parentList as $parent)<option value="{{$parent->id}}">{{$parent->parentName}}</option>@endforeach
            </select>
          </div>
          <div class="col-md-3">
            <label class="form-label small fw-bold">Class</label>
            <select class="form-select select2" id="add-class-id" name="class_id" required>
              <option value="">Select</option>
              @foreach($classList as $class)<option value="{{$class->id}}">{{$class->className}}</option>@endforeach
            </select>
          </div>
          <div class="col-md-3">
            <label class="form-label small fw-bold">Section</label>
            <select class="form-select" id="add-section-id" name="section">
              <option value="">--</option>
            </select>
          </div>
          <div class="col-md-3">
            <label class="form-label small fw-bold">Gender</label>
            <select class="form-select" name="gender" required>
              <option value="Male">Male</option>
              <option value="Female">Female</option>
            </select>
          </div>
          <div class="col-md-3">
            <label class="form-label small fw-bold">Branch</label>
            <select class="form-select" name="school_id" required>@foreach($schoolList as $school)<option value="{{$school->id}}">{{$school->schoolName}}</option>@endforeach</select>
          </div>
          <div class="col-12 text-end">
            <button type="submit" class="btn btn-primary px-4 fw-bold">Complete Registration</button>
          </div>
        </div>
      </form>
    </div>
  </div>
</div>

<div class="card">
  <div class="card-header d-flex justify-content-between align-items-center">
    <h5 class="mb-0 fw-bold">Student Records <span class="spinner-border spinner-border-sm text-primary" id="tableLoader"></span></h5>
    <div class="btn-group shadow-sm">
      <button class="btn btn-light btn-sm"><i class="bi bi-download me-1"></i> CSV</button>
      <button class="btn btn-light btn-sm"><i class="bi bi-printer me-1"></i> Print</button>
    </div>
  </div>
  <div class="card-body p-0">
    <div class="table-responsive">
      <table id="studentTable" class="table table-hover mb-0">
        <thead class="bg-light text-muted">
          <tr>
            <th>#</th>
            <th>GR No.</th>
            <th>Student Name</th>
            <th>Father Name</th>
            <th>Class</th>
            <th>Section</th>
            <th class="text-end">Action</th>
          </tr>
        </thead>
        <tbody></tbody>
      </table>
    </div>
  </div>
</div>

<!-- Simple View Modal -->
<div class="modal fade" id="viewStudentModal" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-body p-4" id="viewModalBody"></div>
    </div>
  </div>
</div>

@endsection

@section('scripts')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>

<script>
  $(document).ready(function() {
    $('.select2').select2({
      width: '100%',
      dropdownParent: $('#addStudentFormBody')
    });

    // High Speed Server Side DataTables
    var table = $('#studentTable').DataTable({
      serverSide: true,
      processing: false, // Custom loader instead
      ajax: {
        url: "{{ route('getStudents') }}",
        beforeSend: function() {
          $('#tableLoader').show();
        },
        complete: function() {
          $('#tableLoader').hide();
        }
      },
      pageLength: 25,
      columns: [{
          data: 'serialNumber'
        },
        {
          data: 'grno',
          render: v => `<span class="fw-bold text-primary">${v}</span>`
        },
        {
          data: 'studentName',
          className: 'fw-bold'
        },
        {
          data: 'parentName'
        },
        {
          data: 'className',
          render: v => `<span class="badge bg-light text-dark border">${v}</span>`
        },
        {
          data: 'section'
        },
        {
          data: 'action',
          orderable: false,
          className: 'text-end'
        }
      ],
      language: {
        search: "",
        searchPlaceholder: "Search GR or Name..."
      }
    });

    // Class to Section Dynamic Loader
    $('#add-class-id').on('change', function() {
      $.get('{{ url("/sections/class") }}/' + $(this).val(), function(data) {
        var html = '<option value="">--</option>';
        data.forEach(s => html += `<option value="${s.sectionName}">${s.sectionName}</option>`);
        $('#add-section-id').html(html);
      });
    });
  });
</script>
@endsection