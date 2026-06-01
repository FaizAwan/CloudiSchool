@extends('layouts.app')

@section('content')
<div class="container my-4">
  <div class="card">
    <div class="card-header" style="background:#1488CC;color:#fff;font-weight:bold;">Teacher Attendance - Yearly Report</div>
    <div class="card-body">
      <form method="GET" action="{{ request()->getBaseUrl() }}/attendance/reports/teachers-yearly" class="row g-2 mb-3">
        <div class="col-md-2">
          <label>Year</label>
          <input type="number" name="year" class="form-control" min="2000" max="2100" value="{{ $year }}" onchange="this.form.submit()" />
        </div>
      </form>

      <div class="table-responsive">
        <table class="table table-bordered table-sm align-middle text-center">
          <thead>
            <tr>
              <th class="text-start">Teacher</th>
              <th>Present</th>
              <th>Absent</th>
              <th>Late</th>
              <th>Leave</th>
            </tr>
          </thead>
          <tbody>
            @foreach($teachers as $t)
              @php $tot = $totals[$t->id] ?? ['present'=>0,'absent'=>0,'late'=>0,'leave'=>0]; @endphp
              <tr>
                <td class="text-start">{{ $t->teacherName }}</td>
                <td>{{ $tot['present'] }}</td>
                <td>{{ $tot['absent'] }}</td>
                <td>{{ $tot['late'] }}</td>
                <td>{{ $tot['leave'] }}</td>
              </tr>
            @endforeach
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>
@endsection


