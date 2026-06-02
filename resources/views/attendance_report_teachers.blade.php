@extends('layouts.app')

@section('content')
<div class="pagetitle">
  <h1 style="font-family: 'Outfit', sans-serif !important; font-weight: 800 !important; color: #1e293b; text-transform: uppercase !important; letter-spacing: 2px !important; font-size: 1.5rem !important;">
    <i class="bi bi-calendar3 me-2" style="color: #004ac6;"></i> Teacher Attendance Report
  </h1>
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
    <div class="card border-0 shadow-sm overflow-hidden rounded-4" style="background: #fff; border: 1px solid #f1f5f9 !important;">
      <!-- Premium linear-gradient Header cover -->
      <div class="card-header py-4 d-flex justify-content-between align-items-center border-0" style="background: linear-gradient(135deg, #004ac6 0%, #1e40af 100%);">
        <h5 class="card-title p-0 m-0 text-white fw-bold" style="font-family: 'Outfit', sans-serif; font-size: 1.2rem; letter-spacing: 0.5px;">
          <i class="bi bi-calendar-check me-2"></i> Monthly Registry Logs
        </h5>
        <div class="badge bg-white text-primary fs-6 py-2.5 px-4 rounded-pill shadow-sm fw-bold" style="font-size: 0.88rem !important; letter-spacing: 0.3px;">
          {{ date('F', mktime(0, 0, 0, $month, 10)) }} {{ $year }}
        </div>
      </div>

      <div class="card-body pt-4" style="padding: 25px !important;">
        <!-- Filters Toolbar with custom styled elements -->
        <form method="GET" action="{{ route('attendance.reports.teachers') }}" class="row g-3 mb-4 p-3.5 bg-light rounded-4 mx-0 align-items-end border border-white shadow-xs">
          <div class="col-md-3">
            <label class="form-label small fw-bold text-muted text-uppercase ls-1" style="font-size: 0.72rem; letter-spacing: 0.8px;">Select Month</label>
            <select name="month" class="form-select border-0 shadow-sm rounded-3 py-2 fw-semibold" style="font-size: 0.88rem; color: #475569;" onchange="this.form.submit()">
              @for ($m = 1; $m <= 12; $m++)
                <option value="{{ $m }}" {{ $month == $m ? 'selected' : '' }}>
                  {{ date('F', mktime(0, 0, 0, $m, 10)) }}
                </option>
              @endfor
            </select>
          </div>
          <div class="col-md-3">
            <label class="form-label small fw-bold text-muted text-uppercase ls-1" style="font-size: 0.72rem; letter-spacing: 0.8px;">Select Year</label>
            <select name="year" class="form-select border-0 shadow-sm rounded-3 py-2 fw-semibold" style="font-size: 0.88rem; color: #475569;" onchange="this.form.submit()">
              @for ($y = date('Y') - 5; $y <= date('Y') + 1; $y++)
                <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>{{ $y }}</option>
              @endfor
            </select>
          </div>
          <div class="col-md-auto ms-auto">
            <div class="d-flex gap-2">
              <a href="javascript:void(0)" onclick="window.print()" class="btn shadow-sm py-2 px-4 fw-bold" style="background: #fea619; color: #fff; border-radius: 8px; border: none; font-size: 0.88rem; transition: all 0.3s ease;">
                <i class="bi bi-printer-fill me-2"></i> Print Registry Report
              </a>
            </div>
          </div>
        </form>

        <!-- Attendance Calendar Matrix table -->
        <div class="attendance-table-container rounded-4 overflow-hidden border" style="border-color: #e2e8f0 !important; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.02);">
          <div class="attendance-table-wrapper position-relative overflow-auto" style="max-height: 550px;">
            <table class="table table-hover mb-0 align-middle">
              <thead class="sticky-top bg-white" style="z-index: 100; border-bottom: 2px solid #e2e8f0;">
                <tr class="border-bottom-0">
                  <th class="sticky-column bg-white text-center text-uppercase small ls-1 text-muted fw-bold py-3" style="min-width: 240px; left: 0; z-index: 101; border-right: 2px solid #f1f5f9; font-size: 0.75rem; letter-spacing: 0.8px;">
                    Instructor Details
                  </th>
                  @for($d=1; $d<=$daysInMonth; $d++)
                    @php 
                      $dateObj = \Carbon\Carbon::create($year, $month, $d);
                      $isWeekend = $dateObj->isWeekend();
                    @endphp
                    <th class="text-center p-0 border-0 {{ $isWeekend ? 'bg-light-danger' : '' }}" style="min-width: 48px;">
                      <div class="py-2.5">
                        <div class="fw-bold text-muted uppercase" style="font-size: 0.58rem; letter-spacing: 0.3px;">{{ $dateObj->format('D') }}</div>
                        <div class="fw-extrabold {{ $isWeekend ? 'text-danger' : 'text-dark' }}" style="font-size: 0.95rem; font-family: 'Outfit', sans-serif;">{{ $d }}</div>
                      </div>
                    </th>
                  @endfor
                  <th class="bg-success text-white text-center fw-bold small p-0" style="min-width: 44px; font-size: 0.85rem; font-family: 'Outfit', sans-serif;">P</th>
                  <th class="bg-danger text-white text-center fw-bold small p-0" style="min-width: 44px; font-size: 0.85rem; font-family: 'Outfit', sans-serif;">A</th>
                  <th class="bg-warning text-dark text-center fw-bold small p-0" style="min-width: 44px; font-size: 0.85rem; font-family: 'Outfit', sans-serif;">L</th>
                  <th class="bg-info text-white text-center fw-bold small p-0" style="min-width: 44px; font-size: 0.85rem; font-family: 'Outfit', sans-serif;">LV</th>
                </tr>
              </thead>
              <tbody>
                @foreach($teachers as $t)
                  @php $present=$absent=$late=$leave=0; @endphp
                  <tr class="attendance-row" style="transition: background-color 0.2s;">
                    <td class="sticky-column bg-white fw-bold text-dark border-right" style="left: 0; z-index: 50; border-right: 2px solid #f1f5f9; padding-top: 15px; padding-bottom: 15px;">
                      <div class="d-flex align-items-center py-1">
                        <div class="avatar-sm bg-primary bg-opacity-10 rounded-3 p-2 me-3 text-center d-flex align-items-center justify-content-center" style="width: 38px; height: 38px; background-color: rgba(0, 74, 198, 0.05); color: #004ac6;">
                          <i class="bi bi-person-fill" style="font-size: 1.1rem;"></i>
                        </div>
                        <div class="lh-1">
                          <div class="text-truncate fw-bold" style="max-width: 160px; color: #1e293b; font-size: 0.9rem;">{{ $t->teacherName }}</div>
                          <small class="text-muted fw-semibold" style="font-size: 0.65rem; text-transform: uppercase;">EmpID: #{{ $t->id }}</small>
                        </div>
                      </div>
                    </td>
                    @for($d=1; $d<=$daysInMonth; $d++)
                      @php 
                        $st = $attendance[$t->id][$d] ?? ''; 
                        $isWeekend = \Carbon\Carbon::create($year, $month, $d)->isWeekend();
                      @endphp
                      <td class="p-1 text-center {{ $isWeekend ? 'bg-light-soft' : '' }}">
                        @if($st==='present' || strtolower($st)==='p' || strtolower($st)==='active') @php $present++; @endphp 
                          <span class="mkr mkr-p" title="Present">P</span>
                        @elseif($st==='absent' || strtolower($st)==='a') @php $absent++; @endphp 
                          <span class="mkr mkr-a" title="Absent">A</span>
                        @elseif($st==='late' || strtolower($st)==='l') @php $late++; @endphp 
                          <span class="mkr mkr-l" title="Late">L</span>
                        @elseif($st==='leave' || strtolower($st)==='lv') @php $leave++; @endphp 
                          <span class="mkr mkr-lv" title="Leave">LV</span>
                        @elseif($isWeekend)
                          <span class="text-muted opacity-25 small" style="font-size: 0.62rem; font-weight: 700;">W</span>
                        @else
                          <span class="dot-empty"></span>
                        @endif
                      </td>
                    @endfor
                    <td class="text-center fw-bold text-success bg-success bg-opacity-10 py-3" style="background-color: rgba(25, 135, 84, 0.04); font-size: 0.9rem; font-family: 'Outfit', sans-serif;">{{ $present }}</td>
                    <td class="text-center fw-bold text-danger bg-danger bg-opacity-10 py-3" style="background-color: rgba(220, 53, 69, 0.04); font-size: 0.9rem; font-family: 'Outfit', sans-serif;">{{ $absent }}</td>
                    <td class="text-center fw-bold text-warning bg-warning bg-opacity-10 py-3" style="background-color: rgba(254, 166, 25, 0.04); font-size: 0.9rem; font-family: 'Outfit', sans-serif;">{{ $late }}</td>
                    <td class="text-center fw-bold text-info bg-info bg-opacity-10 py-3" style="background-color: rgba(13, 202, 240, 0.04); font-size: 0.9rem; font-family: 'Outfit', sans-serif;">{{ $leave }}</td>
                  </tr>
                @endforeach
              </tbody>
            </table>
          </div>
        </div>

        <!-- Legend Scorecard indicators -->
        <div class="mt-4 p-3.5 d-flex flex-wrap justify-content-center gap-4 align-items-center" style="background-color: #f8fafc; border: 1px solid #e2e8f0; border-radius: 16px;">
          <div class="d-flex align-items-center"><span class="mkr mkr-p me-2">P</span> <span class="fw-bold" style="font-size: 0.85rem; color: #475569;">Present</span></div>
          <div class="d-flex align-items-center"><span class="mkr mkr-a me-2">A</span> <span class="fw-bold" style="font-size: 0.85rem; color: #475569;">Absent</span></div>
          <div class="d-flex align-items-center"><span class="mkr mkr-l me-2">L</span> <span class="fw-bold" style="font-size: 0.85rem; color: #475569;">Late</span></div>
          <div class="d-flex align-items-center"><span class="mkr mkr-lv me-2">LV</span> <span class="fw-bold" style="font-size: 0.85rem; color: #475569;">Leave</span></div>
          <div class="ms-md-auto text-muted small fw-semibold" style="font-size: 0.78rem;"><i class="bi bi-info-circle me-1"></i> Scroll horizontally to view full month</div>
        </div>
      </div>
    </div>
  </div>
</div>

<style>
  .ls-1 { letter-spacing: 1px; }
  .bg-light-danger { background-color: #fff8f8; }
  .bg-light-soft { background-color: #fcfdfe; }
  
  .attendance-table-wrapper::-webkit-scrollbar { height: 10px; width: 8px; }
  .attendance-table-wrapper::-webkit-scrollbar-track { background: #f8f9fa; border-radius: 10px; }
  .attendance-table-wrapper::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 10px; border: 2px solid #f8f9fa; }
  .attendance-table-wrapper::-webkit-scrollbar-thumb:hover { background: #94a3b8; }
  
  .mkr {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 28px;
    height: 28px;
    border-radius: 8px;
    font-size: 0.78rem;
    font-weight: 800;
    transition: all 0.2s ease;
    border: 1px solid transparent;
  }
  .mkr:hover {
    transform: scale(1.15);
  }
  
  .mkr-p { background-color: #ecfdf5; color: #059669; border-color: rgba(16, 185, 129, 0.15); }
  .mkr-a { background-color: #fef2f2; color: #dc2626; border-color: rgba(239, 68, 68, 0.15); }
  .mkr-l { background-color: #fffbeb; color: #d97706; border-color: rgba(245, 158, 11, 0.15); }
  .mkr-lv { background-color: #f0f9ff; color: #0284c7; border-color: rgba(14, 165, 233, 0.15); }
  
  .dot-empty { width: 5px; height: 5px; background: #cbd5e1; border-radius: 50%; display: inline-block; }
  
  .sticky-column {
    position: sticky;
    left: 0;
    background-color: white !important;
    box-shadow: 6px 0 12px -6px rgba(0, 74, 198, 0.12);
    z-index: 10;
    text-align: left !important;
  }
  
  .attendance-row:hover .sticky-column { background-color: #f8fafc !important; }
  
  @media print {
    .btn, .breadcrumb, form, .pagetitle, .attendance-table-wrapper::-webkit-scrollbar { display: none !important; }
    .attendance-table-wrapper { overflow: visible !important; max-height: none !important; }
    .sticky-column { position: static !important; box-shadow: none !important; border-right: 1px solid #dee2e6 !important; }
    .card { border: none !important; box-shadow: none !important; }
    .attendance-table-container { border: none !important; }
  }
</style>
@endsection
