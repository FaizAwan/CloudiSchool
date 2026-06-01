@extends('layouts.app')

@section('content')
<div class="container my-4">
  <div class="card">
    <div class="card-header" style="background:#1488CC;color:#fff;font-weight:bold;">Student Attendance</div>
    <div class="card-body">
      @if(session('message'))<div class="alert alert-success" id="alert-message">{{ session('message') }}</div>@endif

      <form method="GET" action="{{ request()->getBaseUrl() }}/attendance/students" class="row g-2 mb-3">
        <div class="col-md-3">
          <label>Class</label>
          <select name="class_id" class="form-control" onchange="this.form.submit()">
            <option value="">-- Select Class --</option>
            @foreach($classes as $c)
              <option value="{{ $c->id }}" {{ ($selectedClassId ?? 0)==$c->id? 'selected':'' }}>{{ $c->className }}</option>
            @endforeach
          </select>
        </div>
        <div class="col-md-3">
          <label>Date</label>
          <input type="date" name="date" class="form-control" value="{{ $date }}" onchange="this.form.submit()" />
        </div>
        <div class="col-md-3">
          <label>Session</label>
          <select name="session" class="form-control" onchange="this.form.submit()">
            @foreach(($sessions ?? []) as $s)
              <option value="{{ $s->academicYear }}" {{ ($sessionValue ?? '')===$s->academicYear ? 'selected' : '' }}>
                {{ $s->academicYear }}{{ ($s->is_active ?? '')==='yes' ? ' (Active)' : '' }}
              </option>
            @endforeach
          </select>
        </div>
      </form>

      <form method="POST" action="{{ request()->getBaseUrl() }}/attendance/students">
        @csrf
        <input type="hidden" name="class_id" value="{{ $selectedClassId }}" />
        <input type="hidden" name="date" value="{{ $date }}" />
        <input type="hidden" name="session" value="{{ $sessionValue }}" />

        @if($selectedClassId)
          <div class="mb-2">
            <strong>Class:</strong> {{ $selectedClass->className ?? '' }}
            <span class="ms-3"><strong>Date:</strong> {{ \Carbon\Carbon::parse($date)->format('d/m/Y') }}</span>
            <span class="ms-3"><strong>Session:</strong> {{ $sessionValue }}</span>
          </div>
        @endif

        <div class="table-responsive">
          <table class="table table-bordered align-middle">
            <thead>
              <tr>
                <th>#</th>
                <th>Student</th>
                <th>Status</th>
                <th>Remarks</th>
              </tr>
            </thead>
            <tbody>
              @php $i=1; @endphp
              @foreach($students as $s)
                @php $row = $existingByStudent[$s->id] ?? null; @endphp
                <tr>
                  <td>{{ $i++ }}</td>
                  <td>{{ $s->studentName }}</td>
                  <td>
                    <select class="form-control" name="entries[{{ $s->id }}][status]">
                      @foreach(['present','absent','leave','late'] as $st)
                        <option value="{{ $st }}" {{ ($row->status ?? 'present')===$st ? 'selected' : '' }}>{{ ucfirst($st) }}</option>
                      @endforeach
                    </select>
                  </td>
                  <td>
                    <input class="form-control" name="entries[{{ $s->id }}][remarks]" value="{{ $row->remarks ?? '' }}" />
                  </td>
                </tr>
              @endforeach
            </tbody>
          </table>
        </div>
        <div class="text-end"><button class="btn btn-primary" type="submit">Save Attendance</button></div>
      </form>
    </div>
  </div>
</div>
<script>setTimeout(function(){ var m=document.getElementById('alert-message'); if(m) m.style.display='none'; }, 4000);</script>
@endsection


