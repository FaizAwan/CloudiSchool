@extends('layouts.app')

@section('content')

<div class="container-fluid py-4 px-4">
    <!-- Perfect Heading Section -->
    <div class="row mb-5">
        <div class="col-12">
            <div class="page-title-box d-flex flex-column flex-md-row justify-content-between align-items-md-center">
                <div>
                    <h1 class="mb-0"><i class="bi bi-check-circle-fill me-3"></i>P A I D &nbsp; &nbsp; C H A L L A N S</h1>
                    <p class="text-muted mb-0">Review and manage successfully processed student invoices.</p>
                </div>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-0 mt-3 mt-md-0">
                        <li class="breadcrumb-item"><a href="#" class="text-decoration-none fw-semibold">Finance</a></li>
                        <li class="breadcrumb-item active fw-bold" aria-current="page">Paid Ledgers</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>

    <!-- Alert System -->
    @if (session('message'))
    <div class="row mb-4">
        <div class="col-12 text-center">
            <div id="alert-message" class="alert alert-soft-success border-0 shadow-sm rounded-4 p-3 d-inline-flex align-items-center">
                <i class="bi bi-check-circle-fill fs-5 me-3 text-success"></i>
                <div class="fw-bold text-success">{{ session('message') }}</div>
            </div>
        </div>
    </div>
    @endif

    <div class="row justify-content-center">
        <div class="col-xl-9 col-lg-10">
            <div class="card card-premium shadow-lg border-0 rounded-4 overflow-hidden">
                <div class="card-header bg-white py-4 px-5 border-bottom border-light d-flex align-items-center">
                    <div class="d-flex align-items-center">
                        <div class="icon-shape bg-soft-primary rounded-circle p-3 me-3">
                            <i class="bi bi-cash-stack fs-4 text-primary"></i>
                        </div>
                        <div>
                            <h5 class="mb-0 fw-800 text-dark">Record Payment</h5>
                            <small class="text-muted">Enter challan details to mark as paid</small>
                        </div>
                    </div>
                </div>
                
                <div class="card-body p-5">
                    <form action="{{url('paidChallan')}}" method="POST">
                        @csrf
                        <div class="row g-4">
                            <!-- Institution & Class -->
                            <div class="col-md-6 text-start">
                                <label class="form-label fw-bold text-uppercase small letter-spacing-1 mb-2">School Branch</label>
                                <select name="school_id" class="form-select select2-premium" required>
                                    @foreach($schoolList as $rowSchools)
                                        <option value="{{$rowSchools->id}}">{{$rowSchools->schoolName}}</option>
                                    @endforeach
                                </select>
                            </div>
                            
                            <div class="col-md-6 text-start">
                                <label class="form-label fw-bold text-uppercase small letter-spacing-1 mb-2">Target Class</label>
                                <select name="class_id" class="form-select select2-premium" required>
                                    @foreach($classList as $rowClasses)
                                        <option value="{{$rowClasses->className}}">{{$rowClasses->className}}</option>
                                    @endforeach
                                </select>
                            </div>
                            
                            <!-- Payment & Student Scope -->
                            <div class="col-md-6 text-start">
                                <label class="form-label fw-bold text-uppercase small letter-spacing-1 mb-2">Billing Cycle</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0"><i class="bi bi-calendar-event"></i></span>
                                    <select name="howManyMonth" class="form-select border-start-0" required>
                                        <option value="" disabled selected>Select billing period...</option>
                                        <option value="one">Single Month</option>
                                        <option value="more">Custom Date Range</option>
                                    </select>
                                </div>
                            </div>
                            
                            <div class="col-md-6 text-start">
                                <label class="form-label fw-bold text-uppercase small letter-spacing-1 mb-2">Recipient Scope</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0"><i class="bi bi-person-check"></i></span>
                                    <select name="howManyStudents" class="form-select border-start-0" required>
                                        <option value="" disabled selected>Select students...</option>
                                        <option value="oneStudent">Individual Student</option>
                                        <option value="allClass">Entire Class Population</option>
                                    </select>
                                </div>
                            </div>

                            <!-- Dynamic Sections -->
                            <div class="col-12 oneStudent" style="display:none;">
                                <div class="p-4 bg-soft-primary rounded-4 border border-primary border-opacity-10 mt-2 text-start">
                                    <label class="form-label fw-bold small text-primary text-uppercase mb-2">Find Student</label>
                                    <select name="student_id" class="form-select select2-premium">
                                        <option value="">Search by Name or GRNO...</option>
                                        @foreach($studentList as $rowStudents)
                                            <option value="{{$rowStudents->id}}">{{$rowStudents->studentName}} [GR: {{$rowStudents->grno}}] - {{$rowStudents->className}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="col-12 oneMonth" style="display:none;">
                                <div class="p-4 bg-light rounded-4 border mt-2">
                                    <div class="row g-3">
                                        <div class="col-md-6 text-start">
                                            <label class="form-label fw-bold small text-uppercase mb-2">Billing Month</label>
                                            <select name="month" class="form-select">
                                                @foreach(['January','February','March','April','May','June','July','August','September','October','November','December'] as $m)
                                                    <option value="{{$m}}">{{$m}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-md-6 text-start">
                                            <label class="form-label fw-bold small text-uppercase mb-2">Year</label>
                                            <select name="year" class="form-select">
                                                @for($y=2024; $y<=2030; $y++)
                                                    <option value="{{$y}}">{{$y}}</option>
                                                @endfor
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-12 moreMonth" style="display:none;">
                                <div class="p-4 bg-light rounded-4 border mt-2">
                                    <div class="row g-3">
                                        <div class="col-md-3 text-start">
                                            <label class="form-label fw-bold small text-uppercase mb-1">From Month</label>
                                            <select id="fromMonth" name="fromMonth" class="form-select text-uppercase">
                                                @foreach(['JANUARY','FEBRUARY','MARCH','APRIL','MAY','JUNE','JULY','AUGUST','SEPTEMBER','OCTOBER','NOVEMBER','DECEMBER'] as $m)
                                                    <option value="{{$m}}">{{$m}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-md-3 text-start">
                                            <label class="form-label fw-bold small text-uppercase mb-1">From Year</label>
                                            <select id="fromYear" name="fromYear" class="form-select">
                                                @for($y=2024; $y<=2030; $y++)
                                                    <option value="{{$y}}">{{$y}}</option>
                                                @endfor
                                            </select>
                                        </div>
                                        <div class="col-md-3 text-start">
                                            <label class="form-label fw-bold small text-uppercase mb-1">To Month</label>
                                            <select id="toMonth" name="toMonth" class="form-select text-uppercase">
                                                @foreach(['JANUARY','FEBRUARY','MARCH','APRIL','MAY','JUNE','JULY','AUGUST','SEPTEMBER','OCTOBER','NOVEMBER','DECEMBER'] as $m)
                                                    <option value="{{$m}}">{{$m}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-md-3 text-start">
                                            <label class="form-label fw-bold small text-uppercase mb-1">To Year</label>
                                            <select id="toYear" name="toYear" class="form-select">
                                                @for($y=2024; $y<=2030; $y++)
                                                    <option value="{{$y}}">{{$y}}</option>
                                                @endfor
                                            </select>
                                        </div>
                                        <div class="col-12 text-start mt-3">
                                            <label class="form-label fw-bold text-muted small text-uppercase">Total Billing Months</label>
                                            <input class="form-control bg-white fw-bold text-primary" style="font-size: 1.25rem; letter-spacing: 2px;" type="text" readonly id="how-many-Months" name="howManyMonths" value="0"/>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Submit -->
                            <div class="col-12 mt-5">
                                <button type="submit" name="submit" class="btn btn-primary d-flex align-items-center justify-content-center w-100 py-3 rounded-4 shadow-sm btn-cinematic">
                                    <i class="bi bi-wallet2 me-3 fs-5"></i>
                                    <span class="fw-bold text-uppercase letter-spacing-1">Validate & Mark as Paid</span>
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .btn-cinematic {
        background: linear-gradient(135deg, var(--secondary-30), #0284c7);
        border: none;
        transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
    }
    .btn-cinematic:hover {
        transform: translateY(-3px) scale(1.01);
        box-shadow: 0 15px 30px rgba(14, 165, 233, 0.3);
    }
    .icon-shape {
        width: 60px;
        height: 60px;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .select2-container--default .select2-selection--single {
        border-radius: 0.75rem !important;
        height: 50px !important;
        border-color: #e2e8f0 !important;
        display: flex;
        align-items: center;
    }
</style>

@endsection

@section('scripts')
<link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css" rel="stylesheet" />
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>

<script>
 $(document).ready(function() {
     $('.select2-premium').select2({
         width: '100%'
     });
     
     $('select[name="howManyMonth"]').on('change', function() {
        const selected = $(this).val();
        if (selected === 'one') {
            $('.oneMonth').fadeIn(400);
            $('.moreMonth').hide();
        } else if (selected === 'more') {
            $('.moreMonth').fadeIn(400);
            $('.oneMonth').hide();
            calculateMonths();
        } else {
            $('.oneMonth, .moreMonth').hide();
        }
    });

    function getMonthNumber(monthName) {
        const months = ["JANUARY", "FEBRUARY", "MARCH", "APRIL", "MAY", "JUNE", "JULY", "AUGUST", "SEPTEMBER", "OCTOBER", "NOVEMBER", "DECEMBER"];
        return months.indexOf(monthName.toUpperCase());
    }

    function calculateMonths() {
        const fromMonth = getMonthNumber($('#fromMonth').val()); 
        const fromYear = parseInt($('#fromYear').val());
        const toMonth = getMonthNumber($('#toMonth').val()); 
        const toYear = parseInt($('#toYear').val());
        
        if (isNaN(fromMonth) || isNaN(fromYear) || isNaN(toMonth) || isNaN(toYear)) return;

        const fromDate = new Date(fromYear, fromMonth, 1); 
        const toDate = new Date(toYear, toMonth, 1);
    
        let totalMonths = (toDate.getFullYear() - fromDate.getFullYear()) * 12 + (toDate.getMonth() - fromDate.getMonth());
        totalMonths = totalMonths < 0 ? 0 : totalMonths;
        
        $('#how-many-Months').val(totalMonths + 1); // +1 because inclusive
    }

    $('#fromMonth, #fromYear, #toMonth, #toYear').change(calculateMonths);
    
    $('select[name="howManyStudents"]').on('change', function() {
        if ($(this).val() === 'oneStudent') {
            $('.oneStudent').fadeIn(400);
        } else {
            $('.oneStudent').hide();
        }
    });
 });
</script>
@endsection
