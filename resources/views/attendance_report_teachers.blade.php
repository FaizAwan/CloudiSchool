@extends('layouts.app')

@section('content')
<div class="pagetitle">
    <h1>Teacher Attendance Report</h1>
    <nav>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
            <li class="breadcrumb-item">Attendance</li>
            <li class="breadcrumb-item active">Monthly Report</li>
        </ol>
    </nav>
</div>

<div class="row">
    <div class="col-lg-12">
        <div class="card border-0 shadow-sm overflow-hidden rounded-4">
            <div class="card-header bg-primary text-white py-3 d-flex justify-content-between align-items-center border-0">
                <h5 class="card-title p-0 m-0 text-white fw-bold"><i class="bi bi-calendar3 me-2"></i>Teacher Monthly Report</h5>
                <div class="badge bg-white text-primary fs-6 py-2 px-3 rounded-pill shadow-sm">
                    {{ date('F', mktime(0, 0, 0, $month, 10)) }} {{ $year }}
                </div>
            </div>
            <div class="card-body pt-4">
                <form method="GET" action="{{ route('attendance.reports.teachers') }}" class="row g-3 mb-4 p-3 bg-light rounded-4 mx-0 align-items-end border border-white">
                    <div class="col-md-3">
                        <label class="form-label small fw-bold text-muted text-uppercase ls-1">Select Month</label>
                        <select name="month" class="form-select border-0 shadow-sm rounded-3 py-2" onchange="this.form.submit()">
                            @for ($m = 1; $m <= 12; $m++)
                                <option value="{{ $m }}" {{ $month == $m ? 'selected' : '' }}>
                                    {{ date('F', mktime(0, 0, 0, $m, 10)) }}
                                </option>
                            @endfor
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label small fw-bold text-muted text-uppercase ls-1">Select Year</label>
                        <select name="year" class="form-select border-0 shadow-sm rounded-3 py-2" onchange="this.form.submit()">
                            @for ($y = date('Y') - 5; $y <= date('Y') + 1; $y++)
                                <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>{{ $y }}</option>
                            @endfor
                        </select>
                    </div>
                    <div class="col-md-auto ms-auto">
                        <div class="d-flex gap-2">
                             <a href="javascript:void(0)" onclick="window.print()" class="btn btn-white shadow-sm border rounded-3 py-2 px-4 fw-bold">
                                <i class="bi bi-printer me-2"></i> Print
                            </a>
                        </div>
                    </div>
                </form>

                <div class="attendance-table-container rounded-4 overflow-hidden border">
                    <div class="attendance-table-wrapper position-relative overflow-auto" style="max-height: 550px;">
                        <table class="table table-hover mb-0 align-middle">
                            <thead class="sticky-top bg-white" style="z-index: 100;">
                                <tr class="border-bottom-0">
                                    <th class="sticky-column bg-white text-center text-uppercase small ls-1 text-muted fw-bold py-3" style="min-width: 220px; left: 0; z-index: 101; border-right: 2px solid #f8f9fa;">
                                        Teacher Information
                                    </th>
                                    @for($d=1; $d<=$daysInMonth; $d++)
                                        @php 
                                            $dateObj = \Carbon\Carbon::create($year, $month, $d);
                                            $isWeekend = $dateObj->isWeekend();
                                        @endphp
                                        <th class="text-center p-0 border-0 {{ $isWeekend ? 'bg-light-danger' : '' }}" style="min-width: 48px;">
                                            <div class="py-2">
                                                <div class="fw-normal text-muted" style="font-size: 0.6rem;">{{ $dateObj->format('D') }}</div>
                                                <div class="fw-bold {{ $isWeekend ? 'text-danger' : 'text-dark' }}">{{ $d }}</div>
                                            </div>
                                        </th>
                                    @endfor
                                    <th class="bg-success text-white text-center fw-bold small p-0" style="min-width: 40px;">P</th>
                                    <th class="bg-danger text-white text-center fw-bold small p-0" style="min-width: 40px;">A</th>
                                    <th class="bg-warning text-dark text-center fw-bold small p-0" style="min-width: 40px;">L</th>
                                    <th class="bg-info text-white text-center fw-bold small p-0" style="min-width: 40px;">LV</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($teachers as $t)
                                    @php $present=$absent=$late=$leave=0; @endphp
                                    <tr class="attendance-row">
                                        <td class="sticky-column bg-white fw-bold text-dark border-right" style="left: 0; z-index: 50; border-right: 2px solid #f8f9fa;">
                                            <div class="d-flex align-items-center py-1">
                                                <div class="avatar-sm bg-primary bg-opacity-10 rounded-3 p-2 me-3 text-center d-flex align-items-center justify-content-center" style="width: 38px; height: 38px;">
                                                    <i class="bi bi-person-fill text-primary"></i>
                                                </div>
                                                <div class="lh-1">
                                                    <div class="text-truncate" style="max-width: 150px;">{{ $t->teacherName }}</div>
                                                    <small class="text-muted fw-normal" style="font-size: 0.65rem;">EmpID: {{ $t->id }}</small>
                                                </div>
                                            </div>
                                        </td>
                                        @for($d=1; $d<=$daysInMonth; $d++)
                                            @php 
                                                $st = $attendance[$t->id][$d] ?? ''; 
                                                $isWeekend = \Carbon\Carbon::create($year, $month, $d)->isWeekend();
                                            @endphp
                                            <td class="p-1 text-center {{ $isWeekend ? 'bg-light-soft' : '' }}">
                                                @if($st==='present') @php $present++; @endphp 
                                                    <span class="mkr mkr-p" title="Present">P</span>
                                                @elseif($st==='absent') @php $absent++; @endphp 
                                                    <span class="mkr mkr-a" title="Absent">A</span>
                                                @elseif($st==='late') @php $late++; @endphp 
                                                    <span class="mkr mkr-l" title="Late">L</span>
                                                @elseif($st==='leave') @php $leave++; @endphp 
                                                    <span class="mkr mkr-lv" title="Leave">LV</span>
                                                @elseif($isWeekend)
                                                    <span class="text-muted opacity-25 small" style="font-size: 0.6rem;">W</span>
                                                @else
                                                    <span class="dot-empty"></span>
                                                @endif
                                            </td>
                                        @endfor
                                        <td class="text-center fw-bold text-success bg-success bg-opacity-10 py-3">{{ $present }}</td>
                                        <td class="text-center fw-bold text-danger bg-danger bg-opacity-10 py-3">{{ $absent }}</td>
                                        <td class="text-center fw-bold text-warning bg-warning bg-opacity-10 py-3">{{ $late }}</td>
                                        <td class="text-center fw-bold text-info bg-info bg-opacity-10 py-3">{{ $leave }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="mt-4 p-3 bg-light rounded-4 d-flex flex-wrap justify-content-center gap-4">
                    <div class="d-flex align-items-center"><span class="mkr mkr-p me-2">P</span> <b>Present</b></div>
                    <div class="d-flex align-items-center"><span class="mkr mkr-a me-2">A</span> <b>Absent</b></div>
                    <div class="d-flex align-items-center"><span class="mkr mkr-l me-2">L</span> <b>Late</b></div>
                    <div class="d-flex align-items-center"><span class="mkr mkr-lv me-2">LV</span> <b>Leave</b></div>
                    <div class="ms-md-4 text-muted small"><i class="bi bi-info-circle me-1"></i> Scroll horizontally to view full month</div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .ls-1 { letter-spacing: 1px; }
    .bg-light-danger { background-color: #fff5f5; }
    .bg-light-soft { background-color: #f9fafb; }
    
    .attendance-table-wrapper::-webkit-scrollbar { height: 12px; width: 8px; }
    .attendance-table-wrapper::-webkit-scrollbar-track { background: #f8f9fa; border-radius: 10px; }
    .attendance-table-wrapper::-webkit-scrollbar-thumb { background: #e2e8f0; border-radius: 10px; border: 3px solid #f8f9fa; }
    .attendance-table-wrapper::-webkit-scrollbar-thumb:hover { background: #cbd5e0; }
    
    .mkr {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 28px;
        height: 28px;
        border-radius: 8px;
        font-size: 0.75rem;
        font-weight: 800;
        transition: all 0.2s;
        border: 1px solid transparent;
    }
    
    .mkr-p { background-color: #ecfdf5; color: #059669; border-color: #10b98133; }
    .mkr-a { background-color: #fef2f2; color: #dc2626; border-color: #ef444433; }
    .mkr-l { background-color: #fffbeb; color: #d97706; border-color: #f59e0b33; }
    .mkr-lv { background-color: #f0f9ff; color: #0284c7; border-color: #0ea5e933; }
    
    .dot-empty { width: 4px; height: 4px; background: #e2e8f0; border-radius: 50%; display: inline-block; }
    
    .sticky-column {
        position: sticky;
        left: 0;
        background-color: white !important;
        box-shadow: 8px 0 16px -8px rgba(0,0,0,0.15);
        z-index: 10;
        text-align: left !important;
        padding-left: 1.5rem !important;
    }
    
    .attendance-row:hover .sticky-column { background-color: #f8f9fa !important; }
    
    @media print {
        .btn, .breadcrumb, form, .pagetitle, .attendance-table-wrapper::-webkit-scrollbar { display: none !important; }
        .attendance-table-wrapper { overflow: visible !important; max-height: none !important; }
        .sticky-column { position: static !important; box-shadow: none !important; border-right: 1px solid #ddd !important; }
        .card { border: none !important; box-shadow: none !important; }
        .attendance-table-container { border: none !important; }
    }
    
    @media (max-width: 768px) {
        .sticky-column { min-width: 151px !important; padding-left: 0.75rem !important; }
        .mkr { width: 24px; height: 24px; font-size: 0.65rem; }
    }
</style>

<style>
    .attendance-table-wrapper::-webkit-scrollbar {
        height: 10px;
        width: 10px;
    }
    .attendance-table-wrapper::-webkit-scrollbar-track {
        background: #f1f1f1;
        border-radius: 10px;
    }
    .attendance-table-wrapper::-webkit-scrollbar-thumb {
        background: #cbd5e0;
        border-radius: 10px;
    }
    .attendance-table-wrapper::-webkit-scrollbar-thumb:hover {
        background: #a0aec0;
    }
    
    .attendance-marker {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 24px;
        height: 24px;
        border-radius: 6px;
        font-size: 0.7rem;
        font-weight: 800;
        cursor: default;
    }
    
    .attendance-marker.static { width: 30px; height: 30px; font-size: 0.8rem; }
    
    .present { background-color: #d1fae5; color: #065f46; border: 1px solid #10b981; }
    .absent { background-color: #fee2e2; color: #991b1b; border: 1px solid #ef4444; }
    .late { background-color: #fef3c7; color: #92400e; border: 1px solid #f59e0b; }
    .leave { background-color: #e0f2fe; color: #075985; border: 1px solid #0ea5e9; }
    
    .sticky-column {
        position: sticky;
        left: 0;
        background-color: white !important;
        box-shadow: 2px 0 5px -2px rgba(0,0,0,0.1);
    }
    
    @media print {
        .card-header, .pagetitle, form, .attendance-table-wrapper::-webkit-scrollbar { display: none !important; }
        .attendance-table-wrapper { overflow: visible !important; max-height: none !important; }
        .sticky-column { position: static !important; }
        .attendance-marker { border: 1px solid #ccc !important; }
    }
</style>
@endsection


