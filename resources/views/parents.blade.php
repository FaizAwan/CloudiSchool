@extends('layouts.app')

@section('content')
<link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css" rel="stylesheet" />
<link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.min.css">

<div class="container-fluid px-4 py-5">
    <!-- Perfect Heading Section -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="page-title-box d-flex flex-column flex-md-row justify-content-between align-items-md-center">
                <div>
                    <h1 class="mb-0" style="font-family: 'Outfit', sans-serif; font-size: 1.75rem; font-weight: 800; color: var(--accent-10); text-transform: uppercase; letter-spacing: 4px; white-space: nowrap;">
                        <i class="bi bi-people-fill me-3"></i>P A R E N T S &nbsp; &nbsp; M A N A G E M E N T
                    </h1>
                    <p class="text-muted mb-0">Orchestrate parent-institution relationships with sophisticated digital tracking.</p>
                </div>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-0 mt-3 mt-md-0">
                        <li class="breadcrumb-item"><a href="{{ route('home') }}" class="text-decoration-none fw-semibold">Dashboard</a></li>
                        <li class="breadcrumb-item active fw-bold" aria-current="page">Parent Directory</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>

<section class="section">
    <div class="row">
        <!-- Add Parent Form -->
        <div class="col-lg-3">
            <div class="card card-premium shadow-sm border-0 overflow-hidden">
                <div class="card-header-premium">
                    <i class="bi bi-person-plus-fill me-2"></i> Add Parent
                </div>
                <div class="card-body pt-4">
                    <form action="{{ url('addParent') }}" method="POST" class="row g-3">
                        @csrf
                        <div class="col-12 mb-3">
                            <label class="form-label fw-bold small text-muted text-uppercase">Selected Branch</label>
                            @php 
                                $uid = auth()->id();
                                $u = $uid ? DB::table('users')->where('id', $uid)->first() : null;
                                $sid = $u ? ($u->tenant_id ?: $u->school_id) : null;
                                $s = $sid ? DB::table('schools')->select('id','schoolName','schoolCity')->where('id',$sid)->first() : null; 
                            @endphp
                            <div class="p-3 bg-light rounded-3 d-flex align-items-center border">
                                <i class="bi bi-building me-2 text-primary"></i>
                                <span class="fw-bold small">{{ $s ? ($s->schoolName.' - '.$s->schoolCity) : 'No school active' }}</span>
                            </div>
                            <input type="hidden" name="school_id" value="{{$sid}}" />
                        </div>

                        <div class="col-12 mb-2">
                            <div class="form-floating">
                                <input class="form-control border-0 bg-light" name="parentName" id="parentName" type="text" placeholder="Parent Name" required />
                                <label for="parentName">Parent Name</label>
                            </div>
                        </div>

                        <div class="col-12 mb-2">
                            <label class="form-label fw-bold small">Is School Employee?</label>
                            <select name="is_commandercityschool_employee" id="isSchoolEmployee" class="form-select border-0 bg-light">
                                <option value="No">No</option>
                                <option value="Yes">Yes</option>
                            </select>
                        </div>

                        <div class="col-12 mb-2">
                            <div class="form-floating">
                                <input class="form-control border-0 bg-light" name="phoneNumber" id="phoneNumber" type="text" placeholder="Phone Number" required />
                                <label for="phoneNumber">Phone Number</label>
                            </div>
                        </div>

                        <div class="col-12 mb-2">
                            <div class="form-floating">
                                <input class="form-control border-0 bg-light" name="address" id="address" type="text" placeholder="Address" />
                                <label for="address">Address</label>
                            </div>
                        </div>

                        <div class="col-12 mb-2">
                            <label class="form-label fw-bold small">Profession / Status</label>
                            <select name="status" id="parentStatus" class="form-select border-0 bg-light">
                                <option value="Government Employee">Government Employee</option>
                                <option value="Business Person">Business Person</option>
                                <option value="Private Job">Private Job</option>
                                <option value="Unemployed">Unemployed</option>
                                <option value="Army Personnel">Army Personnel</option>
                                <option value="PAF Uniform Person">PAF Uniform Person</option>
                                <option value="PAF Civilian">PAF Civilian</option>
                                <option value="PAF Retired">PAF Retired</option>
                                <option value="Army / Navy">Army / Navy</option>
                                <option value="Defense Paid">Defense Paid</option>
                                <option value="Pure Civilian">Pure Civilian</option>
                                <option value="Ward of Shohada">Ward of Shohada</option>
                                <option value="Other">Other</option>
                            </select>
                            
                            <div id="statusStaffWrap" class="mt-2" style="display:none;">
                                <input class="form-control bg-light border-0" name="status_staff_detail" type="text" placeholder="Profession details" />
                            </div>
                            <div id="statusGovWrap" class="mt-2" style="display:none;">
                                <input class="form-control bg-light border-0" name="status_government_job_detail" type="text" placeholder="Gov details" />
                            </div>
                            <div id="statusBusinessWrap" class="mt-2" style="display:none;">
                                <input class="form-control bg-light border-0" name="status_business_name" type="text" placeholder="Business name" />
                            </div>
                            <div id="statusPrivateWrap" class="mt-2" style="display:none;">
                                <input class="form-control bg-light border-0" name="status_private_job_detail" type="text" placeholder="Private details" />
                            </div>
                            <div id="statusUnemployedWrap" class="mt-2" style="display:none;">
                                <input class="form-control bg-light border-0" name="status_unemployed_reason" type="text" placeholder="Reason" />
                            </div>
                            <div id="statusOtherWrap" class="mt-2" style="display:none;">
                                <input class="form-control bg-light border-0" name="status_other" type="text" placeholder="Specifics" />
                            </div>
                        </div>

                        <div class="col-12 mb-3">
                            <label class="form-label fw-bold small">Resident Location</label>
                            <div class="row g-2">
                                <div class="col-6">
                                    <input class="form-control bg-light border-0" name="resident_country" id="residentCountry" list="countryList" placeholder="Country" />
                                </div>
                                <div class="col-6" id="residentCityWrap" style="display:none;">
                                    <input class="form-control bg-light border-0" name="resident_city" id="residentCity" type="text" placeholder="City" />
                                </div>
                            </div>
                        </div>

                        <div class="col-12">
                            <button class="btn btn-gradient w-100 py-3 rounded-pill shadow-sm fw-bold" type="submit">
                                Add Parent Record
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Parent List -->
        <div class="col-lg-9">
            <div class="card card-premium shadow-sm border-0 overflow-hidden">
                <div class="card-header-premium d-flex justify-content-between align-items-center">
                    <div><i class="bi bi-person-lines-fill me-2"></i> Parent Directory</div>
                    <div class="d-flex align-items-center gap-2">
                        @if (session('message'))
                        <div id="alert-message" class="badge bg-success-light text-success border border-success border-opacity-25 px-3 py-2 fw-bold" style="font-size: 11px;">
                            <i class="bi bi-check-circle me-1"></i> {{ session('message') }}
                        </div>
                        @endif
                    </div>
                </div>
                <div class="card-body pt-4">
                    <div id="tableLoader" style="display:none;" class="text-center py-5">
                       <div class="spinner-grow text-primary" role="status"></div>
                       <p class="mt-3 text-muted fw-bold">Syncing Records...</p>
                    </div>
                    <div class="table-responsive px-2">
                        <table id="parentsTable" class="table table-premium w-100">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Children</th>
                                    <th>Staff</th>
                                    <th>Phone</th>
                                    <th>Location</th>
                                    <th class="text-center">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- AJAX Content -->
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
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 25px;">
            <div class="modal-header border-0 bg-primary text-white p-4 d-block position-relative" style="border-radius:25px 25px 0 0;">
                <div class="d-flex justify-content-between align-items-center">
                    <h5 class="modal-title fw-bold fs-4">Edit Parent Information</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <p class="mb-0 opacity-75 small mt-1">Update parent details and professional status</p>
            </div>
            <form id="editForm" method="POST" action="{{ url('updateParent') }}">
                @csrf
                <div class="modal-body p-4 pt-5">
                    <input type="hidden" name="id" id="editId">
                    <div class="row g-4">
                        <div class="col-md-6">
                            <div class="form-floating">
                                <input type="text" class="form-control border-0 bg-light" id="editParentName" name="parentName" placeholder="Name">
                                <label for="editParentName">Parent Name</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold small text-muted">Is School Employee?</label>
                            <select id="editIsEmployee" name="is_commandercityschool_employee" class="form-select border-0 bg-light">
                                <option value="No">No</option>
                                <option value="Yes">Yes</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <div class="form-floating">
                                <input type="text" class="form-control border-0 bg-light" id="editPhoneNumber" name="phoneNumber" placeholder="Phone">
                                <label for="editPhoneNumber">Phone Number</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-floating">
                                <input type="text" class="form-control border-0 bg-light" id="editAddress" name="address" placeholder="Address">
                                <label for="editAddress">Residential Address</label>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="p-3 bg-light rounded-4">
                                <label class="form-label fw-bold small text-primary text-uppercase mb-3">Profession & Status Details</label>
                                <select name="status" id="editParentStatus" class="form-select border-0 mb-3 py-3 shadow-sm" style="border-radius: 12px;">
                                    <option value="Government Employee">Government Employee</option>
                                    <option value="Business Person">Business Person</option>
                                    <option value="Private Job">Private Job</option>
                                    <option value="Unemployed">Unemployed</option>
                                    <option value="Army Personnel">Army Personnel</option>
                                    <option value="PAF Uniform Person">PAF Uniform Person</option>
                                    <option value="PAF Civilian">PAF Civilian</option>
                                    <option value="PAF Retired">PAF Retired</option>
                                    <option value="Army / Navy">Army / Navy</option>
                                    <option value="Defense Paid">Defense Paid</option>
                                    <option value="Pure Civilian">Pure Civilian</option>
                                    <option value="Ward of Shohada">Ward of Shohada</option>
                                    <option value="Other">Other</option>
                                </select>
                                <div id="editStatusStaffWrap" class="mb-2" style="display:none;">
                                    <input class="form-control py-3" name="status_staff_detail" id="editStatusStaffDetail" type="text" placeholder="Specify Profession" />
                                </div>
                                <div id="editStatusGovWrap" class="mb-2" style="display:none;">
                                    <input class="form-control py-3" name="status_government_job_detail" id="editGovernmentJobDetail" type="text" placeholder="Dept Name" />
                                </div>
                                <div id="editStatusBusinessWrap" class="mb-2" style="display:none;">
                                    <input class="form-control py-3" name="status_business_name" id="editBusinessName" type="text" placeholder="Business Title" />
                                </div>
                                <div id="editStatusPrivateWrap" class="mb-2" style="display:none;">
                                    <input class="form-control py-3" name="status_private_job_detail" id="editPrivateJobDetail" type="text" placeholder="Company Name" />
                                </div>
                                <div id="editStatusUnemployedWrap" class="mb-2" style="display:none;">
                                    <input class="form-control py-3" name="status_unemployed_reason" id="editUnemployedReason" type="text" placeholder="Current Status" />
                                </div>
                                <div id="editStatusOtherWrap" class="mb-2" style="display:none;">
                                    <input class="form-control py-3" name="status_other" id="editStatusOther" type="text" placeholder="Details" />
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold small">Resident Country</label>
                            <input class="form-control border-0 bg-light py-3 rounded-3" name="resident_country" id="editResidentCountry" list="countryList" placeholder="Country" />
                        </div>
                        <div class="col-md-6" id="editResidentCityWrap" style="display:none;">
                            <label class="form-label fw-bold small">Resident City</label>
                            <input class="form-control border-0 bg-light py-3 rounded-3" name="resident_city" id="editResidentCity" type="text" placeholder="City" />
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0 p-4 pt-0">
                    <button type="button" class="btn btn-light rounded-pill px-5 fw-bold" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary rounded-pill px-5 fw-bold shadow">Save Changes</button>
                </div>
            </form>
        </div>
    </div>
</div>

<datalist id="countryList">
    <option value="Pakistan"></option>
    <option value="United Arab Emirates"></option>
    <option value="Saudi Arabia"></option>
    <option value="United Kingdom"></option>
    <option value="United States"></option>
    <option value="Canada"></option>
    <option value="Australia"></option>
</datalist>

@endsection

@section('scripts')
<script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>
<script>
    $(document).ready(function() {
        const parentsTable = $('#parentsTable').DataTable({
            serverSide: true,
            processing: false,
            ajax: {
                url: "{{ route('getParents') }}",
                beforeSend: function() {
                    $('#tableLoader').show();
                    $('#parentsTable').closest('.table-responsive').hide();
                },
                complete: function() {
                    $('#tableLoader').hide();
                    $('#parentsTable').closest('.table-responsive').show();
                }
            },
            pageLength: 25,
            columns: [
                { 
                    data: 'parentName',
                    render: function(data) {
                        return `<div class="d-flex align-items-center">
                                    <div class="rounded-circle p-2 me-2 d-flex align-items-center justify-content-center border" style="width:35px;height:35px;background:#f8f9fc;">
                                        <i class="bi bi-person text-primary"></i>
                                    </div>
                                    <span class="fw-bold text-dark">${data}</span>
                                </div>`;
                    }
                },
                { 
                    data: 'totalChildren', 
                    render: v => `<span class="badge badge-soft-info px-3 py-2 rounded-pill fw-bold"><i class="bi bi-people me-1"></i> ${v} Child(ren)</span>`
                },
                { 
                    data: 'is_commandercityschool_employee',
                    render: v => v === 'Yes' ? '<span class="badge badge-soft-success px-3 py-2 rounded-pill fw-bold">Staff member</span>' : '<span class="badge badge-soft-secondary px-3 py-2 rounded-pill fw-bold">External</span>'
                },
                { 
                    data: 'phoneNumber',
                    render: v => `<span class="small fw-bold opacity-75">${v}</span>`
                },
                { 
                    data: 'address',
                    render: function(data) {
                        return `<span class="text-muted small d-inline-block text-truncate" style="max-width: 150px;">${data || '---'}</span>`;
                    }
                },
                { 
                    data: 'action',
                    orderable: false,
                    className: 'text-center'
                }
            ],
            language: {
                search: "",
                searchPlaceholder: "Search records...",
                paginate: {
                    previous: '<i class="bi bi-arrow-left-short"></i>',
                    next: '<i class="bi bi-arrow-right-short"></i>'
                }
            }
        });

        // Search Bar Aesthetic Customization
        $('.dataTables_filter input').addClass('form-control border-0 bg-light rounded-pill px-4 py-2').css('width', '250px');

        const originalOptions = ['Government Employee', 'Business Person', 'Private Job', 'Unemployed', 'Army Personnel', 'PAF Uniform Person', 'PAF Civilian', 'PAF Retired', 'Army / Navy', 'Defense Paid', 'Pure Civilian', 'Ward of Shohada', 'Other'];

        function setupStatusLogic(empEl, statusEl, wraps) {
            empEl.on('change', function() {
                if (this.value === 'Yes') {
                    statusEl.empty().append(new Option('Teaching Staff', 'Teaching Staff')).append(new Option('Non-Teaching Staff', 'Non-Teaching Staff'));
                    wraps.staff.show();
                    Object.values(wraps).filter(w => w !== wraps.staff).forEach(w => w.hide());
                } else {
                    statusEl.empty();
                    originalOptions.forEach(o => statusEl.append(new Option(o, o)));
                    wraps.staff.hide();
                    statusEl.trigger('change');
                }
            });

            statusEl.on('change', function() {
                if (empEl.val() === 'Yes') return;
                const v = this.value;
                wraps.other.toggle(v === 'Other');
                wraps.gov.toggle(v === 'Government Employee');
                wraps.business.toggle(v === 'Business Person');
                wraps.private.toggle(v === 'Private Job');
                wraps.unemployed.toggle(v === 'Unemployed');
            });
        }

        setupStatusLogic($('#isSchoolEmployee'), $('#parentStatus'), {
            staff: $('#statusStaffWrap'),
            other: $('#statusOtherWrap'),
            gov: $('#statusGovWrap'),
            business: $('#statusBusinessWrap'),
            private: $('#statusPrivateWrap'),
            unemployed: $('#statusUnemployedWrap')
        });

        $('#residentCountry').on('input', function() {
            $('#residentCityWrap').toggle(!!this.value.trim());
        });

        setupStatusLogic($('#editIsEmployee'), $('#editParentStatus'), {
            staff: $('#editStatusStaffWrap'),
            other: $('#editStatusOtherWrap'),
            gov: $('#editStatusGovWrap'),
            business: $('#editStatusBusinessWrap'),
            private: $('#editStatusPrivateWrap'),
            unemployed: $('#editStatusUnemployedWrap')
        });

        $(document).on('click', '.btn-primary[data-bs-id], .btn-outline-primary[data-bs-id]', function() {
            const id = $(this).attr('data-bs-id');
            if(!id) return;
            $.get("{{ url('parents') }}/" + id + "/edit-json", function(data) {
                $('#editId').val(data.id);
                $('#editParentName').val(data.parentName);
                $('#editIsEmployee').val(data.is_commandercityschool_employee || 'No').trigger('change');
                $('#editPhoneNumber').val(data.phone || data.phoneNumber);
                $('#editAddress').val(data.address);
                if (data.status) $('#editParentStatus').val(data.status).trigger('change');
                
                $('#editStatusStaffDetail').val(data.status_staff_detail || '');
                $('#editGovernmentJobDetail').val(data.status_government_job_detail || '');
                $('#editBusinessName').val(data.status_business_name || '');
                $('#editPrivateJobDetail').val(data.status_private_job_detail || '');
                $('#editUnemployedReason').val(data.status_unemployed_reason || '');
                $('#editStatusOther').val(data.status_other || '');

                $('#editResidentCountry').val(data.resident_country || '').trigger('input');
                $('#editResidentCity').val(data.resident_city || '');

                const modal = new bootstrap.Modal(document.getElementById('editModal'));
                modal.show();
            });
        });

        $('#editResidentCountry').on('input', function() {
            $('#editResidentCityWrap').toggle(!!this.value.trim());
        });

        setTimeout(() => { $('#alert-message').fadeOut(); }, 7000);
    });
</script>
@endsection