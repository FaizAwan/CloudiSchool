@extends('layouts.app')

@section('content')
@include('partials.compact_module_styles')
<style>
  /* Make action buttons more visible on Schools page */
  .btn-mid { font-size: 0.875rem !important; padding: 0.3rem 0.75rem !important; line-height: 1.2 !important; font-weight: 600; letter-spacing: .2px; }
  /* High-contrast colors for readability */
  .btn-primary.btn-mid { background-color: #0d6efd !important; border-color: #0b5ed7 !important; color: #ffffff !important; text-shadow: 0 0 0 rgba(0,0,0,0.2); }
  .btn-outline-info.btn-mid { color: #084c61 !important; border-color: #0ea5b6 !important; background-color: #e7faff !important; }
  .btn-outline-info.btn-mid:hover { background-color: #d8f4fb !important; color: #063846 !important; }
  /* Ensure action column has enough room and no truncation */
  th.actions-col, td.actions { min-width: 160px !important; white-space: nowrap !important; overflow: visible !important; }

  /* Premium DataTables controls and pagination overrides */
  .dataTables_wrapper .dataTables_length,
  .dataTables_wrapper .dataTables_filter {
      margin-bottom: 20px;
      font-size: 0.9rem;
      color: #495057;
      display: inline-block;
  }
  .dataTables_wrapper .dataTables_length {
      float: left;
  }
  .dataTables_wrapper .dataTables_filter {
      float: right;
      text-align: right;
  }
  .dataTables_wrapper .dataTables_length select {
      padding: 6px 12px;
      border: 1px solid #ced4da;
      border-radius: 6px;
      background-color: #fff;
      font-size: 0.875rem;
      margin: 0 6px;
      outline: none;
      transition: border-color 0.15s ease-in-out;
  }
  .dataTables_wrapper .dataTables_length select:focus {
      border-color: #1488CC;
  }
  .dataTables_wrapper .dataTables_filter input {
      padding: 6px 12px;
      border: 1px solid #ced4da;
      border-radius: 20px; /* Modern pill shape */
      background-color: #fff;
      font-size: 0.875rem;
      margin-left: 8px;
      outline: none;
      transition: all 0.2s ease-in-out;
      width: 220px;
  }
  .dataTables_wrapper .dataTables_filter input:focus {
      border-color: #1488CC;
      box-shadow: 0 0 0 0.2rem rgba(20, 136, 204, 0.15);
      width: 260px;
  }

  /* Clear float after DataTables filters */
  .dataTables_wrapper::after {
      content: "";
      clear: both;
      display: table;
  }

  /* Style the DataTables Info and Pagination */
  .dataTables_wrapper .dataTables_info {
      font-size: 0.875rem;
      color: #6c757d;
      padding-top: 20px;
      float: left;
  }
  .dataTables_wrapper .dataTables_paginate {
      padding-top: 20px;
      float: right;
      text-align: right;
  }
  .dataTables_wrapper .dataTables_paginate .paginate_button {
      display: inline-block;
      padding: 6px 14px;
      margin-left: 6px;
      border: 1px solid #dee2e6 !important;
      border-radius: 6px !important;
      background: #ffffff !important;
      color: #1488CC !important;
      font-size: 0.875rem;
      font-weight: 600;
      text-decoration: none;
      cursor: pointer;
      transition: all 0.2s ease-in-out;
      box-shadow: 0 2px 4px rgba(0,0,0,0.02);
  }
  .dataTables_wrapper .dataTables_paginate .paginate_button:hover {
      background: #1488CC !important;
      border-color: #1488CC !important;
      color: #ffffff !important;
      box-shadow: 0 4px 8px rgba(20,136,204,0.2);
  }
  .dataTables_wrapper .dataTables_paginate .paginate_button.current,
  .dataTables_wrapper .dataTables_paginate .paginate_button.current:hover {
      background: #1488CC !important;
      border-color: #1488CC !important;
      color: #ffffff !important;
      font-weight: 700;
      box-shadow: 0 4px 8px rgba(20,136,204,0.3);
  }
  .dataTables_wrapper .dataTables_paginate .paginate_button.disabled,
  .dataTables_wrapper .dataTables_paginate .paginate_button.disabled:hover {
      background: #f8f9fa !important;
      border-color: #e9ecef !important;
      color: #6c757d !important;
      cursor: not-allowed;
      box-shadow: none;
      opacity: 0.6;
  }
</style>

<div class="container my-4">
    <div class="row justify-content-center">
        <div class="col-md-12">
            @php
                $canAddSchool = in_array(Auth::user()->role, ['superadmin', 'admin']);
            @endphp

            @if($canAddSchool)
            <!-- Button to open add branch modal -->
            <div class="row mb-3">
                <div class="col-md-12 text-end">
                    <button type="button" class="btn btn-primary btn-mid shadow-sm" data-bs-toggle="modal" data-bs-target="#addBranchModal" style="background-color:#1488CC !important; border-color:#2B32B2 !important; color:#ffffff !important; font-weight:600; border-radius:6px; padding: 8px 18px; font-size: 13px;">
                        <i class="bi bi-plus-lg me-1"></i> Add New School Branch
                    </button>
                </div>
            </div>
            @endif

            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header">
                            <h6 class="mb-0">School Branches' List</h6>
                        </div>
                        <div class="card-body">
                                    @if (session('message'))
                                      <div id="alert-message" class="alert alert-primary">
                                          {{ session('message') }}
                                      </div>
                                    @endif
                                    @if (session('errorMessage'))
                                      <div id="alert-error" class="alert alert-danger">
                                          {{ session('errorMessage') }}
                                      </div>
                                    @endif
                                    <div id="datatable-container">
                                    <div id="buttons-container" class="mb-3"></div>
                                        <table class="table table-striped table-hover" id="schoolTable">
                                            <thead>
                                                <tr>
                                                    <th>S.NO</th>
                                                    <th>Branch Name</th>
                                                    <th>City</th>
                                                    <th>Admin</th>
                                                    <th>Email</th>
                                                    <th class="actions-col">Action</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @php $serial = 1; @endphp
                                                @foreach($schoolList as $school)
                                                <tr>
                                                    <td>{{ $serial++ }}</td>
                                                    <td>{{ $school->schoolName }}</td>
                                                    <td>{{ $school->schoolCity }}</td>
                                                    <td>{{ $school->schoolAdminName }}</td>
                                                    <td>{{ $school->schoolAdminEmail }}</td>
                                                    <td class="actions">
                                                        <button type="button" class="btn btn-primary btn-mid edit-button"
                                                            data-bs-toggle="modal" data-bs-target="#editModal"
                                                            style="color:#ffffff !important; background-color:#0d6efd !important; border-color:#0b5ed7 !important;"
                                                            onclick="openEditSchool({{ (int) $school->id }})">
                                                            Edit
                                                        </button>
                                                        <button type="button" class="btn btn-outline-info btn-mid ms-1"
                                                            data-bs-toggle="modal" data-bs-target="#viewModal"
                                                            style="color:#084c61 !important; background-color:#e7faff !important; border-color:#0ea5b6 !important;"
                                                            onclick="openViewSchool({{ (int) $school->id }})">
                                                            View
                                                        </button>
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
                </div>
            </div>
        </div>

<!-- Add Branch Modal -->
<div class="modal fade" id="addBranchModal" tabindex="-1" role="dialog" aria-labelledby="addBranchModalLabel" aria-hidden="true" data-backdrop="static">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content" style="border: none; border-radius: 12px; overflow: hidden; box-shadow: 0 10px 30px rgba(0,0,0,0.15);">
      <div class="modal-header" style="background: linear-gradient(135deg, #1488CC, #2B32B2); color: #fff; padding: 15px 20px;">
        <h6 class="modal-title mb-0" id="addBranchModalLabel" style="font-weight: 700; letter-spacing: 0.5px;">
          <i class="bi bi-bank me-2"></i>Add New School Branch
        </h6>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" style="filter: invert(1); opacity: .8;"></button>
      </div>
      <form action="{{ request()->getBaseUrl() }}/addSchool" method="POST">
        @csrf
        <div class="modal-body" style="padding: 25px; background-color: #f8f9fa;">
          
          <!-- Section 1: Branch Details -->
          <div class="mb-4">
            <h6 style="color: #2B32B2; font-weight: 700; border-bottom: 2px solid #e9ecef; padding-bottom: 6px; margin-bottom: 15px; font-size: 14px;">
              <i class="bi bi-info-circle me-2"></i>Branch & Admin Information
            </h6>
            <div class="row">
              <div class="col-md-6 mb-3">
                <label class="form-label" style="font-weight: 600; color: #495057; font-size: 11px;">Branch Name <span class="text-danger">*</span></label>
                <input class="form-control shadow-sm" required name="schoolName" type="text" placeholder="e.g. Aptech Main Branch" style="border-radius: 6px;"/>
              </div>
              <div class="col-md-6 mb-3">
                <label class="form-label" style="font-weight: 600; color: #495057; font-size: 11px;">Branch City <span class="text-danger">*</span></label>
                <input class="form-control shadow-sm" required name="schoolCity" type="text" placeholder="e.g. Hyderabad" style="border-radius: 6px;"/>
              </div>
            </div>
            <div class="row">
              <div class="col-md-6 mb-3">
                <label class="form-label" style="font-weight: 600; color: #495057; font-size: 11px;">Branch Admin Name <span class="text-danger">*</span></label>
                <input class="form-control shadow-sm" required name="schoolAdminName" type="text" placeholder="e.g. John Doe" style="border-radius: 6px;"/>
              </div>
              <div class="col-md-6 mb-3">
                <label class="form-label" style="font-weight: 600; color: #495057; font-size: 11px;">Branch Admin Email <span class="text-danger">*</span></label>
                <input class="form-control shadow-sm" required name="schoolAdminEmail" type="email" placeholder="e.g. admin@branch.com" style="border-radius: 6px;"/>
              </div>
            </div>
            <div class="row">
              <div class="col-md-12 mb-3">
                <label class="form-label" style="font-weight: 600; color: #495057; font-size: 11px;">Branch Admin Password <span class="text-danger">*</span></label>
                <input class="form-control shadow-sm" required name="schoolAdminPassword" type="password" placeholder="Min. 6 characters" style="border-radius: 6px;"/>
              </div>
            </div>
          </div>

          <!-- Section 2: Bank Details -->
          <div>
            <h6 style="color: #2B32B2; font-weight: 700; border-bottom: 2px solid #e9ecef; padding-bottom: 6px; margin-bottom: 15px; font-size: 14px;">
              <i class="bi bi-credit-card me-2"></i>Bank Account Information (Optional)
            </h6>
            <div class="row">
              <div class="col-md-6 mb-3">
                <label class="form-label" style="font-weight: 600; color: #495057; font-size: 11px;">Bank Name</label>
                <input class="form-control shadow-sm" name="bank_name" type="text" placeholder="e.g. HBL Bank" style="border-radius: 6px;"/>
              </div>
              <div class="col-md-6 mb-3">
                <label class="form-label" style="font-weight: 600; color: #495057; font-size: 11px;">Bank Branch</label>
                <input class="form-control shadow-sm" name="bank_branch" type="text" placeholder="e.g. Saddar Branch" style="border-radius: 6px;"/>
              </div>
            </div>
            <div class="row">
              <div class="col-md-6 mb-3">
                <label class="form-label" style="font-weight: 600; color: #495057; font-size: 11px;">Account Title</label>
                <input class="form-control shadow-sm" name="bank_account_title" type="text" placeholder="e.g. Aptech School System" style="border-radius: 6px;"/>
              </div>
              <div class="col-md-6 mb-3">
                <label class="form-label" style="font-weight: 600; color: #495057; font-size: 11px;">Account Number</label>
                <input class="form-control shadow-sm" name="bank_account_number" type="text" placeholder="e.g. 123456789012" style="border-radius: 6px;"/>
              </div>
            </div>
            <div class="row">
              <div class="col-md-12 mb-3">
                <label class="form-label" style="font-weight: 600; color: #495057; font-size: 11px;">IBAN (optional)</label>
                <input class="form-control shadow-sm" name="bank_iban" type="text" placeholder="e.g. PK00HABB0012345678901234" style="border-radius: 6px;"/>
              </div>
            </div>
          </div>

        </div>
        <div class="modal-footer" style="background-color: #f1f3f5; border-top: 1px solid #dee2e6; padding: 15px 20px;">
          <button type="button" class="btn btn-secondary btn-mid" data-bs-dismiss="modal" style="border-radius: 6px;">Close</button>
          <button type="submit" class="btn btn-primary btn-mid" style="border-radius: 6px; background-color: #1488CC; border-color: #1488CC;">
            <i class="bi bi-save me-1"></i> Add School Branch
          </button>
        </div>
      </form>
    </div>
  </div>
</div>

<div class="modal fade" id="editModal" tabindex="-1" role="dialog" aria-labelledby="editModalLabel" aria-hidden="true" data-backdrop="static">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header" style="background:#1488CC; color:#fff;">
        <h6 class="modal-title mb-0" id="editModalLabel">Edit School Branch Details</h6>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" style="filter: invert(1); opacity: .8;"></button>
      </div>
      <form id="editForm" method="POST" action="{{ request()->getBaseUrl() }}/save-branch-details">
        @csrf
        <div class="modal-body">
          <input type="hidden" name="id" id="editId">
          <div class="form-group">
            <label for="editSchoolName">Branch Name</label>
            <input type="text" class="form-control" id="editSchoolName" name="schoolName">
            <label for="editSchoolCity">Branch City</label>
            <input type="text" class="form-control" id="editSchoolCity" name="schoolCity">
            <label for="editSchoolAdminName">Admin</label>
            <input type="text" class="form-control" id="editSchoolAdminName" name="schoolAdminName">
            <label for="editSchoolAdminEmail">Admin Email</label>
            <input type="text" class="form-control" id="editSchoolAdminEmail" name="schoolAdminEmail">
            <hr/>
            <label>Bank Name</label>
            <input type="text" class="form-control" id="editBankName" name="bank_name">
            <label>Bank Branch</label>
            <input type="text" class="form-control" id="editBankBranch" name="bank_branch">
            <label>Account Title</label>
            <input type="text" class="form-control" id="editBankAccountTitle" name="bank_account_title">
            <label>Account Number</label>
            <input type="text" class="form-control" id="editBankAccountNumber" name="bank_account_number">
            <label>IBAN (optional)</label>
            <input type="text" class="form-control" id="editBankIban" name="bank_iban">
          </div>
        </div>
        <div class="modal-footer">
          <button type="submit" class="btn btn-primary">Update changes</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- View Modal -->
<div class="modal fade" id="viewModal" tabindex="-1" role="dialog" aria-labelledby="viewModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header" style="background:#1488CC; color:#fff;">
        <h6 class="modal-title mb-0" id="viewModalLabel">School Branch Details</h6>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" style="filter: invert(1); opacity: .8;"></button>
      </div>
      <div class="modal-body">
        <div class="mb-2"><strong>Name:</strong> <span id="viewSchoolName"></span></div>
        <div class="mb-2"><strong>City:</strong> <span id="viewSchoolCity"></span></div>
        <div class="mb-2"><strong>Admin:</strong> <span id="viewSchoolAdminName"></span></div>
        <div class="mb-2"><strong>Email:</strong> <span id="viewSchoolAdminEmail"></span></div>
        <hr/>
        <div class="mb-2"><strong>Bank:</strong> <span id="viewBankName"></span></div>
        <div class="mb-2"><strong>Branch:</strong> <span id="viewBankBranch"></span></div>
        <div class="mb-2"><strong>Account Title:</strong> <span id="viewBankAccountTitle"></span></div>
        <div class="mb-2"><strong>Account Number:</strong> <span id="viewBankAccountNumber"></span></div>
        <div class="mb-2"><strong>IBAN:</strong> <span id="viewBankIban"></span></div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>

@section('scripts')
<!-- DataTables (requires jQuery already loaded in layout) -->
<script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.js"></script>
<script src="https://cdn.datatables.net/buttons/2.2.2/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.2.2/js/buttons.html5.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>

<script>
const BASE_PATH = "{{ request()->getBaseUrl() }}";
const BASE_URL = `${window.location.origin}${BASE_PATH}`;

async function fetchSchool(id){
    const res = await fetch(`${BASE_URL}/fetch-branch-details/${id}`, { headers: { 'Accept': 'application/json' } });
    if (!res.ok) throw new Error(`HTTP ${res.status}`);
    const json = await res.json();
    if (!json.ok) throw new Error(json.error || 'Failed');
    return json.data;
}

async function openEditSchool(id) {
    try {
        const s = await fetchSchool(id);
        $('#editId').val(s.id);
        $('#editSchoolName').val(s.schoolName || '');
        $('#editSchoolCity').val(s.schoolCity || '');
        $('#editSchoolAdminName').val(s.schoolAdminName || '');
        $('#editSchoolAdminEmail').val(s.schoolAdminEmail || '');
        $('#editBankName').val(s.bank_name || '');
        $('#editBankBranch').val(s.bank_branch || '');
        $('#editBankAccountTitle').val(s.bank_account_title || '');
        $('#editBankAccountNumber').val(s.bank_account_number || '');
        $('#editBankIban').val(s.bank_iban || '');
        const el = document.getElementById('editModal');
        if (window.bootstrap && bootstrap.Modal) {
            bootstrap.Modal.getOrCreateInstance(el).show();
        } else if (window.jQuery) { $('#editModal').modal('show'); }
    } catch (e) {
        alert('Failed to load branch: ' + e.message);
    }
}

async function openViewSchool(id) {
    try {
        const s = await fetchSchool(id);
        document.getElementById('viewSchoolName').textContent = s.schoolName || '';
        document.getElementById('viewSchoolCity').textContent = s.schoolCity || '';
        document.getElementById('viewSchoolAdminName').textContent = s.schoolAdminName || '';
        document.getElementById('viewSchoolAdminEmail').textContent = s.schoolAdminEmail || '';
        document.getElementById('viewBankName').textContent = s.bank_name || '';
        document.getElementById('viewBankBranch').textContent = s.bank_branch || '';
        document.getElementById('viewBankAccountTitle').textContent = s.bank_account_title || '';
        document.getElementById('viewBankAccountNumber').textContent = s.bank_account_number || '';
        document.getElementById('viewBankIban').textContent = s.bank_iban || '';
        const el = document.getElementById('viewModal');
        if (window.bootstrap && bootstrap.Modal) {
            bootstrap.Modal.getOrCreateInstance(el).show();
        } else if (window.jQuery) { $('#viewModal').modal('show'); }
    } catch (e) {
        alert('Failed to load school: ' + e.message);
    }
}

$(function() {
    // Initialize DataTable for client-side processing
    $('#schoolTable').DataTable({
        pageLength: 25,
        responsive: true,
        order: [[1, 'asc']],
        language: {
            search: 'Search School Branches:',
            lengthMenu: 'Show _MENU_ branches per page',
            info: 'Showing _START_ to _END_ of _TOTAL_ branches',
            emptyTable: 'No branches found'
        },
        columnDefs: [{ orderable: false, targets: 5 }]
    });

    // Bootstrap handles close via data-bs-dismiss; no JS needed here.

    // Auto-hide alerts
    setTimeout(function(){ $('#alert-message, #alert-error').fadeOut('slow'); }, 5000);
});
</script>
@endsection

@endsection
