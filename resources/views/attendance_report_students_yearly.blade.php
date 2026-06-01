@extends('layouts.app')

@section('content')
<div class="container my-4">
  <div class="card">
    <div class="card-header" style="background:#1488CC;color:#fff;font-weight:bold;">Student Attendance - Yearly Report</div>
    <div class="card-body">
      <form method="GET" action="{{ request()->getBaseUrl() }}/attendance/reports/students-yearly" class="row g-2 mb-3">
        <div class="col-md-3">
          <label>Class</label>
          <select name="class_id" class="form-control" onchange="this.form.submit()">
            <option value="">-- Select Class --</option>
            @foreach($classes as $c)
              <option value="{{ $c->id }}" {{ ($selectedClassId ?? 0)==$c->id? 'selected':'' }}>{{ $c->className }}</option>
            @endforeach
          </select>
        </div>
        <div class="col-md-2">
          <label>Year</label>
          <input type="number" name="year" class="form-control" min="2000" max="2100" value="{{ $year }}" onchange="this.form.submit()" />
        </div>
      </form>

      @if(($selectedClassId ?? 0) && $students->isNotEmpty())
      <div class="table-responsive">
        <table class="table table-bordered table-sm align-middle text-center">
          <thead>
            <tr>
              <th class="text-start">Student</th>
              <th>Present</th>
              <th>Absent</th>
              <th>Late</th>
              <th>Leave</th>
            </tr>
          </thead>
          <tbody>
            @foreach($students as $s)
              @php $t = $totals[$s->id] ?? ['present'=>0,'absent'=>0,'late'=>0,'leave'=>0]; @endphp
              <tr>
                <td class="text-start">{{ $s->studentName }}</td>
                <td>{{ $t['present'] ?? 0 }}</td>
                <td>{{ $t['absent'] ?? 0 }}</td>
                <td>{{ $t['late'] ?? 0 }}</td>
                <td>{{ $t['leave'] ?? 0 }}</td>
              </tr>
            @endforeach
          </tbody>
        </table>
      </div>
      @else
        <div class="alert alert-info">Select class and year to view the report.</div>
      @endif
    </div>
  </div>
</div>
@endsection


