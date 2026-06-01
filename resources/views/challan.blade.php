@extends('layouts.app')

@section('content')
<style>
    .page-title-box h1 {
        font-family: 'Outfit', sans-serif;
        font-size: 1.75rem;
        font-weight: 800;
        color: var(--accent-10);
        margin-bottom: 0.25rem;
        text-transform: uppercase;
        letter-spacing: 12px;
    }
    .form-label-premium {
        font-size: 0.75rem;
        font-weight: 700;
        color: #64748b;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-bottom: 0.5rem;
    }
    .sticky-sidebar { position: sticky; top: 20px; }
</style>

<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.css">
<link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css" rel="stylesheet" />

<div class="container-fluid py-4 px-4">
    <!-- Perfect Heading Section -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="page-title-box d-flex flex-column flex-md-row justify-content-between align-items-md-center">
                <div>
                    <h1><i class="bi bi-receipt-cutoff me-3"></i>F E E &nbsp; &nbsp; C H A L L A N S</h1>
                    <p class="text-muted mb-0">Generate, track, and manage student fee challans with real-time status monitoring.</p>
                </div>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-0 mt-3 mt-md-0">
                        <li class="breadcrumb-item"><a href="#" class="text-decoration-none fw-semibold">Administration</a></li>
                        <li class="breadcrumb-item active fw-bold" aria-current="page">Challan Center</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>

    <!-- Alert System -->
    @if (session('message'))
    <div class="row mb-4">
        <div class="col-12">
            <div id="alert-message" class="alert alert-primary border-0 shadow-sm rounded-4 p-3 d-flex align-items-center">
                <i class="bi bi-info-circle-fill fs-4 me-3"></i>
                <div class="fw-semibold">{!! session('message') !!}</div>
                <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        </div>
    </div>
    @endif

    <div class="row g-4">
        <!-- Sidebar: Create Challan -->
        <div class="col-xl-3">
            <div class="card card-premium shadow-sm border-0 sticky-sidebar">
                <div class="card-header-premium">
                    <i class="bi bi-gear-fill me-2"></i> Configuration & Execution
                </div>
                <div class="card-body p-4">
                    <form action="{{url('addChallan')}}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label-premium">Institution Select</label>
                            <select name="school_id" class="form-select select2" required>
                                @foreach($schoolList as $rowSchools)
                                    <option value="{{$rowSchools->id}}">{{$rowSchools->schoolName}}</option>
                                @endforeach
                            </select>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label-premium">Class Target</label>
                            <select name="class_id" class="form-select select2" required>
                                @foreach($classList as $rowClasses)
                                    <option value="{{$rowClasses->className}}">{{$rowClasses->className}}</option>
                                @endforeach
                            </select>
                        </div>
                        
                        <div class="row g-2 mb-3">
                            <div class="col-6">
                                <label class="form-label-premium">Billing Type</label>
                                <select name="howManyMonth" class="form-select form-control-premium" required>
                                    <option value="one">Single Month</option>
                                    <option value="more">Custom Range</option>
                                </select>
                            </div>
                            <div class="col-6">
                                <label class="form-label-premium">Scope</label>
                                <select name="howManyStudents" class="form-select form-control-premium" required>
                                    <option value="oneStudent">Individual</option>
                                    <option value="allClass">Entire Class</option>
                                </select>
                            </div>
                        </div>

                        <div class="oneStudent mb-3">
                            <label class="form-label-premium">Select Student</label>
                            <select name="student_id" class="form-select select2">
                                @foreach($studentList as $rowStudents)
                                    <option value="{{$rowStudents->id}}">{{$rowStudents->studentName}} - {{$rowStudents->grno}} ({{$rowStudents->className}})</option>
                                @endforeach
                            </select>
                        </div>
                        
                        <div class="oneMonth">
                            <div class="row g-2 mb-3">
                                <div class="col-7">
                                    <label class="form-label-premium">Month</label>
                                    <select name="month" class="form-select form-control-premium">
                                        @foreach(['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'] as $m)
                                            <option value="{{$m}}">{{$m}}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-5">
                                    <label class="form-label-premium">Year</label>
                                    <select name="year" class="form-select form-control-premium">
                                        @for($y=2024; $y<=2032; $y++) <option value="{{$y}}">{{$y}}</option> @endfor
                                    </select>
                                </div>
                            </div>
                        </div>
                        
                        <div class="moreMonth" style="display:none;">
                            <div class="row g-2 mb-3">
                                <div class="col-6">
                                    <label class="form-label-premium">From Month</label>
                                    <select id="fromMonth" name="fromMonth" class="form-select form-control-premium">
                                        @foreach(['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'] as $m)
                                            <option value="{{$m}}">{{$m}}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-6">
                                    <label class="form-label-premium">From Year</label>
                                    <select id="fromYear" name="fromYear" class="form-select form-control-premium">
                                        @for($y=2024; $y<=2032; $y++) <option value="{{$y}}">{{$y}}</option> @endfor
                                    </select>
                                </div>
                                <div class="col-6">
                                    <label class="form-label-premium">To Month</label>
                                    <select id="toMonth" name="toMonth" class="form-select form-control-premium">
                                        @foreach(['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'] as $m)
                                            <option value="{{$m}}">{{$m}}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-6">
                                    <label class="form-label-premium">To Year</label>
                                    <select id="toYear" name="toYear" class="form-select form-control-premium">
                                        @for($y=2024; $y<=2032; $y++) <option value="{{$y}}">{{$y}}</option> @endfor
                                    </select>
                                </div>
                            </div>
                            <div class="mb-4">
                                <label class="form-label-premium">Total Billing Cycle</label>
                                <div class="input-group">
                                    <input class="form-control form-control-premium bg-light" type="text" readonly id="how-many-Months" name="howManyMonths" value="0"/>
                                    <span class="input-group-text bg-white border-start-0 text-muted small fw-bold">MONTHS</span>
                                </div>
                            </div>
                        </div>
                        
                        <button class="btn btn-primary btn-premium w-100 shadow-sm mt-3" type="submit">
                            <i class="bi bi-layer-forward me-2"></i>Initiate Challans
                        </button>
                    </form>
                </div>
            </div>
        </div>
        
        <!-- Main Content: Challan Registry -->
        <div class="col-lg-12">
            <div class="card card-premium shadow-sm border-0 overflow-hidden">
                <div class="card-header-premium">
                    <i class="bi bi-gear-fill me-2"></i> Configuration & Execution
                </div>
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title"><i class="bi bi-grid-3x3-gap-fill me-2"></i>Challan Ledger</h5>
                    <div class="badge bg-white text-primary px-3 py-2 rounded-pill fw-bold">
                        ACTIVE SESSION: {{ date('Y') }}
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover table-premium mb-0" id="myTable">
                            <thead>
                                <tr>
                                    <th>Session</th>
                                    <th>Class / Sec</th>
                                    <th>Period</th>
                                    <th>Student Narrative</th>
                                    <th>Emp</th>
                                    <th class="text-end">Value</th>
                                    <th class="text-center">Status</th>
                                    <th class="text-center">Operations</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($challanList as $rowFeesList)
                                    @php
                                        $student = null;
                                        if (is_numeric($rowFeesList->student_id)) {
                                            $student = DB::table('students')->where('id', (int)$rowFeesList->student_id)->first();
                                        }
                                        if (!$student) {
                                            $student = DB::table('students')->where('studentName', $rowFeesList->student_id)->first();
                                        }
                                        $isEmployee = '';
                                        if ($student && !empty($student->parent_id)) {
                                            $parent = DB::table('parents')->where('id', $student->parent_id)->first();
                                            $isEmployee = ($parent && ($parent->is_commandercityschool_employee ?? '') == 'yes') ? 'YES' : '';
                                        }
                                    @endphp
                                    <tr>
                                        <td class="fw-semibold text-dark">{{$rowFeesList->session}}</td>
                                        <td class="fw-bold">{{$rowFeesList->class_name}}</td>
                                        <td>
                                            <span class="text-primary fw-medium">{{$rowFeesList->month}}</span>
                                            <br><small class="text-muted fw-bold">{{$rowFeesList->year}}</small>
                                        </td>
                                        <td>
                                            <div class="fw-bold text-dark">{{$rowFeesList->student_id}}</div>
                                            <small class="text-muted fw-bold">GR#: {{$rowFeesList->grno}}</small>
                                        </td>
                                        <td class="text-center">
                                            @if($isEmployee == 'YES')
                                                <span class="badge bg-info-subtle text-info border border-info-subtle">YES</span>
                                            @endif
                                        </td>
                                        <td class="text-end fw-bold text-primary">{{number_format($rowFeesList->total_fee, 2)}}</td>
                                        <td class="text-center">
                                            @if($rowFeesList->status == 'un-paid')
                                                <span class="badge bg-danger-subtle text-danger px-3 py-2 rounded-pill border border-danger-subtle" style="font-size: 0.7rem;">UNPAID</span>
                                            @else
                                                <span class="badge bg-success-subtle text-success px-3 py-2 rounded-pill border border-success-subtle" style="font-size: 0.7rem;">PAID</span>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            <div class="btn-group shadow-sm rounded-pill overflow-hidden">
                                                @if($rowFeesList->status == 'un-paid')
                                                    <a class="btn btn-sm btn-outline-success border-0 px-2" href="{{url('challanPaidByID',$rowFeesList->id)}}" title="Pay"><i class="bi bi-currency-dollar"></i></a>
                                                    <a class="btn btn-sm btn-outline-primary border-0 px-2" href="{{url('editChallan',$rowFeesList->id)}}" title="Edit"><i class="bi bi-pencil-square"></i></a>
                                                @endif
                                                <a class="btn btn-sm btn-outline-info border-0 px-2" href="{{url('viewChallan',$rowFeesList->id)}}" title="View"><i class="bi bi-eye-fill"></i></a>
                                                @if($rowFeesList->status == 'un-paid')
                                                    <a class="btn btn-sm btn-outline-danger border-0 px-2" href="{{url('deleteChallanByChallanID',$rowFeesList->id)}}" onclick="return confirm('Secure Delete: Continue?')" title="Delete"><i class="bi bi-trash-fill"></i></a> 
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot class="bg-light">
                                <tr>
                                    <th colspan="5" class="text-end py-3">VIEWPORT TOTAL:</th>
                                    <th id="totalFees" class="text-end text-primary fs-5 fw-bold py-3 pe-3">0.00</th>
                                    <th colspan="2"></th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


@endsection

@section('scripts')
<script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>

<script>
 $(document).ready(function() {
     
     
     $('select[name="howManyMonth"]').on('change', function() {
        const selectedValues = $(this).val(); // Get selected values (array)
        if (selectedValues === 'one') {
            $('.oneMonth').show(); // Show if "One Month" is selected
            $('.moreMonth').hide(); // Show if "One Month" is selected
        } else {
            $('.moreMonth').show(); // Show if "One Month" is selected
            $('.oneMonth').hide(); // Hide otherwise
            
            function getMonthNumber(monthName) {
                const months = ["JANUARY", "FEBRUARY", "MARCH", "APRIL", "MAY", "JUNE", "JULY", "AUGUST", "SEPTEMBER", "OCTOBER", "NOVEMBER", "DECEMBER"];
                return months.indexOf(monthName.toUpperCase());
            }
    
      function calculateMonths() {
            const fromMonth = getMonthNumber($('#fromMonth').val()); 
            const fromYear = parseInt($('#fromYear').val());
            const toMonth = getMonthNumber($('#toMonth').val()); 
            const toYear = parseInt($('#toYear').val());
            
            const fromDate = new Date(fromYear, parseInt(fromMonth), 1); 
            const toDate = new Date(toYear, parseInt(toMonth), 1);
        
            let yearDiff = toDate.getFullYear() - fromDate.getFullYear(); 
            let monthDiff = toDate.getMonth() - fromDate.getMonth(); // Change const to let
            let totalMonths = yearDiff * 12 + monthDiff; 
        
            if (monthDiff < 0) {
                yearDiff--;
                monthDiff += 11; // Add 11 instead of 12
            }
            
            $('#how-many-Months').val(totalMonths);
            console.log("totalMonths:", totalMonths); 
        }

          $('#fromMonth, #fromYear, #toMonth, #toYear').change(calculateMonths);
      
      calculateMonths();
        }
    });
    
    $('select[name="howManyStudents"]').on('change', function() {
        const selectedValuesStudent = $(this).val(); // Get selected values (array)
        if (selectedValuesStudent === 'oneStudent') {
            $('.oneStudent').show(); // Show if "One Month" is selected
            $('.moreStudent').hide(); // Show if "One Month" is selected
        } else {
            $('.moreStudent').show(); // Show if "One Month" is selected
            $('.oneStudent').hide(); // Hide otherwise
        }
    });
    
    
    var table = $('#myTable').DataTable({
        columnDefs: [{
            targets: [4], // Assuming the fees column is the 5th column (index 4)
            render: function(data, type, row) {
                return type === 'display' && data ? data.toString().replace(/1 - /g, '') : data;
            }
        }]
    });
    
    // Recalculate total fees on each draw event (including after search/filter)
    table.on('draw', function () {
        var totalFees = 0;
        table.rows({ search: 'applied' }).every(function () {
            var rowData = this.data();
            var valStr = rowData[4] ? (rowData[4].display ? rowData[4].display : rowData[4]) : "0";
            var feeValue = parseFloat(valStr.toString().replace(/[^0-9.]/g, '').trim());
            if (!isNaN(feeValue)) {
                totalFees += feeValue;
            }
        });
        $('#totalFees').text(totalFees.toFixed(2)); // Display total fees in the footer
    });

    $('.select2').select2();
});
</script>
@endsection
