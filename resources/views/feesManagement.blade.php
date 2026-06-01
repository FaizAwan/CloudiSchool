@extends('layouts.app')

@section('content')
<style>
    .page-title-box {
        background: white;
        padding: 1.5rem;
        border-radius: 15px;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
        margin-bottom: 2rem;
        border-left: 5px solid #1e40af;
    }
    .page-title-box h1 {
        font-size: 1.75rem;
        font-weight: 800;
        color: #172554;
        margin-bottom: 0.25rem;
        text-transform: uppercase;
        letter-spacing: 1px;
    }
    .breadcrumb-item + .breadcrumb-item::before {
        content: "›";
        font-size: 1.2rem;
        vertical-align: middle;
    }
    .card-premium {
        border: none;
        border-radius: 15px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
        overflow: hidden;
    }
    .card-premium .card-header {
        background: linear-gradient(135deg, #1e40af 0%, #172554 100%) !important;
        color: white;
        padding: 1.25rem;
        border: none;
    }
    .card-premium .card-title {
        color: white !important;
        margin: 0;
        font-weight: 700;
        font-size: 1.25rem;
    }
    .btn-premium {
        border-radius: 50px;
        padding: 0.5rem 1.5rem;
        font-weight: 600;
        transition: all 0.3s;
    }
    .btn-premium-sm {
        border-radius: 50px;
        padding: 0.25rem 0.75rem;
        font-size: 0.875rem;
    }
    .table thead th {
        background-color: #f8f9fa;
        color: #495057;
        font-weight: 700;
        text-transform: uppercase;
        font-size: 0.8rem;
        letter-spacing: 0.5px;
        padding: 1rem;
        border: none;
    }
    .badge-fee {
        font-size: 0.75rem;
        padding: 0.4em 0.8em;
    }
</style>

<div class="container-fluid py-4">
    <!-- Perfect Heading Section -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="page-title-box d-flex flex-column flex-md-row justify-content-between align-items-md-center">
                <div>
                    <h1><i class="bi bi-wallet2 me-3"></i>F E E S &nbsp; &nbsp; M A N A G E M E N T</h1>
                    <p class="text-muted mb-0">Configure and monitor school-wide fee structures and billing sessions.</p>
                </div>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-0 mt-3 mt-md-0">
                        <li class="breadcrumb-item"><a href="#" class="text-decoration-none fw-semibold">Administration</a></li>
                        <li class="breadcrumb-item active fw-bold" aria-current="page">Fees Master</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <!-- Sidebar: Add New Fee -->
        <div class="col-md-3">
            <div class="card card-premium h-100">
                <div class="card-header d-flex align-items-center">
                    <h5 class="card-title text-uppercase fw-bold" style="letter-spacing: 1px;"><i class="bi bi-database-add me-2"></i>Add New Fees</h5>
                </div>
                <div class="card-body p-4">
                    <form action="{{url('addFees')}}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label text-muted small fw-bold">SCHOOL</label>
                            <select name="school_id" class="form-select select2" required>
                                @foreach($schoolList as $rowSchools)
                                    <option value="{{$rowSchools->id}}">{{$rowSchools->schoolName}}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="row g-2 mb-3">
                            <div class="col-6">
                                <label class="form-label text-muted small fw-bold">CLASS</label>
                                <select name="class_id" class="form-select" required>
                                    @foreach($classList as $rowClasses)
                                        <option value="{{$rowClasses->id}}">{{$rowClasses->className}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-6">
                                <label class="form-label text-muted small fw-bold">MONTH</label>
                                <select name="month" class="form-select" required>
                                    @foreach(['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'] as $m)
                                        <option value="{{$m}}" {{$m == 'April' ? 'selected' : ''}}>{{$m}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="row g-2 mb-3">
                            <div class="col-6">
                                <label class="form-label text-muted small fw-bold">YEAR</label>
                                <select name="year" class="form-select" required>
                                    @for($year = 2023; $year <= 2030; $year++)
                                        <option value="{{ $year }}" {{ $year == date('Y') ? 'selected' : '' }}>{{ $year }}</option>
                                    @endfor
                                </select>
                            </div>
                            <div class="col-6">
                                <label class="form-label text-muted small fw-bold">SESSION</label>
                                <select name="academicYear" class="form-select" required>
                                    @foreach($academicYears->sortBy('academicYear') as $rowAcademicYears)
                                        <option value="{{$rowAcademicYears->academicYear}}" {{ ($rowAcademicYears->is_active ?? '') == 'yes' ? 'selected' : '' }}>
                                            {{$rowAcademicYears->academicYear}}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="fee-stucture-box mt-4 p-3 bg-light rounded-3">
                            <h6 class="fw-bold mb-3 border-bottom pb-2">Fee Amounts</h6>
                            <div class="row g-3">
                                @foreach($feeTypeList as $rowFeeTypeList)
                                <div class="col-6">
                                    <label class="form-label small mb-1">{{$rowFeeTypeList->name}}</label>
                                    <input name="fee_types[{{$rowFeeTypeList->name}}]" class="form-control form-control-sm" type="number" step="0.01" placeholder="0.00"/>
                                </div>
                                @endforeach
                            </div>
                        </div>

                        <div class="mt-4">
                            <button class="btn btn-primary btn-premium w-100 shadow-sm" type="submit">
                                <i class="bi bi-save me-2"></i>Save Fee Structure
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Main Content: Fee List -->
        <div class="col-md-9">
            <div class="card card-premium">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title"><i class="bi bi-table me-2"></i>Class Wise Fee Structure</h5>
                    <div class="badge bg-white text-primary px-3 py-2 rounded-pill fw-bold">
                        Academic Year: {{ $academicYears->where('is_active', 'yes')->first()->academicYear ?? 'N/A' }}
                    </div>
                </div>
                <div class="card-body p-0">
                    <!-- Session messages -->
                    @if(session('message'))
                        <div class="alert alert-success border-0 rounded-0 m-0 alert-dismissible fade show" role="alert">
                            <i class="bi bi-check-circle-fill me-2"></i> {{ session('message') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    <div class="p-3">
                        <table class="table table-hover mb-0 display" id="myTable" style="width:100%">
                            <thead>
                                <tr>
                                    <th>Class</th>
                                    <th>Month / Year</th>
                                    <th>Fee Structure</th>
                                    <th class="text-end">Total Amount</th>
                                    <th class="text-center">Quick Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $feeTypesByClass = [];
                                    $totalSummary = 0;
                                    foreach($feesList as $rowFeesList) {
                                        $feeTypesByClass[$rowFeesList->class_name][$rowFeesList->month_name][$rowFeesList->year][] = [
                                            'name' => ($rowFeesList->fee_type_name ?: ($rowFeesList->fee_name ?: $rowFeesList->fee_type_id)),
                                            'value' => (float)($rowFeesList->fee_value ?: 0)
                                        ];
                                    }
                                @endphp

                                @foreach($feeTypesByClass as $class => $months)
                                    @foreach($months as $month => $years)
                                        @foreach($years as $year => $feeItems)
                                            @php
                                                $rowTotal = collect($feeItems)->sum('value');
                                                $totalSummary += $rowTotal;
                                                $groupId = preg_replace('/[^A-Za-z0-9_-]/','_', $class.'_'.$month.'_'.$year);
                                                
                                                // Group key for matching with controller data
                                                $groupKey = trim($class).'|'.trim($month).'|'.trim($year);
                                                $groupSession = $feeGroups[$groupKey]['session'] ?? ($academicYears->where('is_active', 'yes')->first()->academicYear ?? '');
                                                $currentValues = $feeGroups[$groupKey]['values'] ?? [];
                                            @endphp
                                            <tr>
                                                <td class="fw-bold">{{$class}}</td>
                                                <td>
                                                    <span class="text-dark fw-semibold">{{$month}}</span>
                                                    <br><small class="text-muted">{{$year}}</small>
                                                </td>
                                                <td>
                                                    <div class="d-flex flex-wrap gap-1">
                                                        @foreach(collect($feeItems)->take(3) as $fi)
                                                            <span class="badge bg-light text-dark border-secondary-subtle fw-medium badge-fee">
                                                                {{Str::limit($fi['name'], 12)}}: {{number_format($fi['value'], 0)}}
                                                            </span>
                                                        @endforeach
                                                        @if(count($feeItems) > 3)
                                                            <span class="badge bg-primary-subtle text-primary border-primary-subtle fw-bold badge-fee">+{{count($feeItems)-3}} more</span>
                                                        @endif
                                                    </div>
                                                </td>
                                                <td class="text-end">
                                                    <span class="h6 mb-0 fw-bold text-primary">{{number_format($rowTotal, 2)}}</span>
                                                </td>
                                                <td class="text-center">
                                                    <div class="btn-group shadow-sm rounded-pill overflow-hidden">
                                                        <button type="button" class="btn btn-sm btn-outline-success" data-bs-toggle="modal" data-bs-target="#viewFeesModal_{{$groupId}}" title="View Details">
                                                            <i class="bi bi-eye-fill"></i>
                                                        </button>
                                                        <button type="button" class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#editFeesModal_{{$groupId}}" title="Edit Fees">
                                                            <i class="bi bi-pencil-fill"></i>
                                                        </button>
                                                        <button type="button" class="btn btn-sm btn-outline-danger" data-bs-toggle="modal" data-bs-target="#deleteFeesModal_{{$groupId}}" title="Delete">
                                                            <i class="bi bi-trash-fill"></i>
                                                        </button>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    @endforeach
                                @endforeach
                            </tbody>
                            <tfoot class="bg-light">
                                <tr>
                                    <th colspan="3" class="ps-4">TOTAL FOR CURRENT VIEW</th>
                                    <th id="totalFees" class="text-end text-primary fs-5 fw-bold">{{number_format($totalSummary, 2)}}</th>
                                    <th></th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- MODALS SECTION - Outside table to prevent glitches --}}
@foreach($feeTypesByClass as $class => $months)
    @foreach($months as $month => $years)
        @foreach($years as $year => $feeItems)
            @php
                $groupId = preg_replace('/[^A-Za-z0-9_-]/','_', $class.'_'.$month.'_'.$year);
                $groupKey = trim($class).'|'.trim($month).'|'.trim($year);
                $currentValues = $feeGroups[$groupKey]['values'] ?? [];
                $groupSession = $feeGroups[$groupKey]['session'] ?? ($academicYears->where('is_active', 'yes')->first()->academicYear ?? '');
                $rowTotal = collect($feeItems)->sum('value');
            @endphp

            <!-- View Modal -->
            <div class="modal fade" id="viewFeesModal_{{$groupId}}" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content border-0 shadow-lg" style="border-radius: 20px;">
                        <div class="modal-header text-white" style="background: linear-gradient(135deg, #1e40af 0%, #172554 100%); border-radius: 20px 20px 0 0;">
                            <h5 class="modal-title fw-bold"><i class="bi bi-eye me-2"></i>{{$class}} ({{$month}} {{$year}})</h5>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body p-0">
                            <div class="list-group list-group-flush">
                                @forelse($currentValues as $name => $val)
                                    <div class="list-group-item d-flex justify-content-between align-items-center py-3 border-light mx-2">
                                        <div class="d-flex align-items-center">
                                            <div class="rounded-circle bg-primary-subtle p-2 me-3">
                                                <i class="bi bi-wallet2 text-primary"></i>
                                            </div>
                                            <span class="fw-semibold text-dark">{{$name}}</span>
                                        </div>
                                        <span class="badge bg-primary-subtle text-primary rounded-pill px-3 py-2 fs-6 fw-bold border border-primary-subtle">{{number_format((float)$val, 2)}}</span>
                                    </div>
                                @empty
                                    <div class="p-5 text-center text-muted">
                                        <i class="bi bi-folder-x fs-1 opacity-25"></i>
                                        <p class="mt-2 mb-0">No fee records found for this selection.</p>
                                    </div>
                                @endforelse
                            </div>
                            @if(count($currentValues) > 0)
                            <div class="p-3 bg-light border-top mt-2 rounded-bottom-4">
                                <div class="d-flex justify-content-between align-items-center px-2">
                                    <h5 class="mb-0 fw-bold">TOTAL AMOUNT</h5>
                                    <h5 class="mb-0 fw-bold text-primary">{{number_format($rowTotal, 2)}}</h5>
                                </div>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Edit Modal -->
            <div class="modal fade" id="editFeesModal_{{$groupId}}" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-lg modal-dialog-centered">
                    <div class="modal-content border-0 shadow-lg" style="border-radius: 20px;">
                        <div class="modal-header text-white" style="background: linear-gradient(135deg, #1e40af 0%, #172554 100%); border-radius: 20px 20px 0 0;">
                            <h5 class="modal-title fw-bold"><i class="bi bi-pencil-square me-2"></i>Modify Fee Structure</h5>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <form action="{{ route('updateFeesGroup') }}" method="POST">
                            @csrf
                            <input type="hidden" name="class_name" value="{{$class}}"/>
                            <input type="hidden" name="month" value="{{$month}}"/>
                            <input type="hidden" name="year" value="{{$year}}"/>
                            <input type="hidden" name="session" value="{{$groupSession}}"/>
                            
                            <div class="modal-body p-4">
                                <div class="alert bg-primary-subtle text-primary border-primary-subtle mb-4 rounded-4 fw-semibold p-3">
                                    Updating fees for {{$class}} in {{$month}} {{$year}}.
                                </div>
                                <div class="row g-4">
                                    @php
                                        $modalTypes = [];
                                        foreach ($feeTypeList as $ft) { $modalTypes[] = trim($ft->name); }
                                        foreach (array_keys($currentValues) as $k) { if (!in_array(trim($k), $modalTypes)) { $modalTypes[] = trim($k); } }
                                        sort($modalTypes);
                                    @endphp
                                    @foreach($modalTypes as $fname)
                                    <div class="col-md-6">
                                        <div class="form-floating shadow-sm rounded-4">
                                            <input type="number" step="0.01" name="fee_values[{{$fname}}]" id="edit_{{$groupId}}_{{Str::slug($fname)}}" class="form-control" value="{{ array_key_exists($fname, $currentValues) ? $currentValues[$fname] : 0 }}" placeholder="{{$fname}}">
                                            <label for="edit_{{$groupId}}_{{Str::slug($fname)}}">{{$fname}}</label>
                                        </div>
                                    </div>
                                    @endforeach
                                </div>
                            </div>
                            <div class="modal-footer bg-light border-0 px-4 py-3" style="border-radius: 0 0 20px 20px;">
                                <button type="button" class="btn btn-outline-secondary btn-premium" data-bs-dismiss="modal">Discard Changes</button>
                                <button type="submit" class="btn btn-primary btn-premium">Update Structure</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Delete Modal -->
            <div class="modal fade" id="deleteFeesModal_{{$groupId}}" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content border-0 shadow-lg" style="border-radius: 20px;">
                        <div class="modal-header bg-danger text-white border-0" style="border-radius: 20px 20px 0 0;">
                            <h5 class="modal-title fw-bold"><i class="bi bi-exclamation-triangle-fill me-2"></i>Security Confirmation</h5>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body p-4 text-center">
                            <div class="mb-3">
                                <div class="bg-danger-subtle d-inline-block rounded-circle p-3 mb-3">
                                    <i class="bi bi-trash3-fill text-danger fs-1"></i>
                                </div>
                            </div>
                            <h5 class="fw-bold mb-2">Permanent Deletion?</h5>
                            <p class="text-muted">You are about to delete all fee items for <br><strong>{{$class}}</strong> ({{$month}} {{$year}}). <br>This action cannot be undone.</p>
                        </div>
                        <div class="modal-footer bg-light border-0 px-4 py-3 justify-content-center" style="border-radius: 0 0 20px 20px;">
                            <button type="button" class="btn btn-outline-secondary px-4 rounded-pill fw-bold" data-bs-dismiss="modal">No, Cancel</button>
                            <form method="POST" action="{{ route('deleteFeesGroup') }}" class="d-inline">
                                @csrf
                                <input type="hidden" name="class_name" value="{{$class}}"/>
                                <input type="hidden" name="month" value="{{$month}}"/>
                                <input type="hidden" name="year" value="{{$year}}"/>
                                <input type="hidden" name="session" value="{{$groupSession}}"/>
                                <button type="submit" class="btn btn-danger px-4 rounded-pill fw-bold shadow-sm">Yes, Delete All</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    @endforeach
@endforeach

@endsection

@section('scripts')
<script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>
<script>
 $(document).ready(function() {
    var table = $('#myTable').DataTable({
        pageLength: 10,
        dom: 'rtip', // Hide default entry selection and search
        responsive: true,
        columnDefs: [
            { targets: [4], orderable: false }
        ]
    });

    // Custom search binding
    $('#customSearch').on('keyup', function() {
        table.search(this.value).draw();
    });

    // Recalculate total fees dynamically on draw (search/filter/etc)
    function updateGrandTotal() {
        var total = 0;
        table.rows({ search: 'applied' }).every(function () {
            var data = this.data();
            // Data in index 3 is formatted as <span>4,400.00</span> or similar
            // We need to extract the numeric value from the 4th column (index 3)
            var valStr = $(data[3]).text() || data[3];
            var feeValue = parseFloat(valStr.replace(/[^0-9.]/g, '').trim());
            if (!isNaN(feeValue)) {
                total += feeValue;
            }
        });
        $('#totalFees').text(total.toLocaleString(undefined, {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        }));
    }

    table.on('draw', function() {
        updateGrandTotal();
    });

    // Initial total
    updateGrandTotal();

    // Initialize Select2
    $('.select2').select2({
        width: '100%'
    });
});
</script>
@endsection
