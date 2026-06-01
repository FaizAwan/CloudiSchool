@extends('layouts.app')

@section('content')
<div class="container my-4">
  <div class="card">
    <div class="card-header" style="background:#1488CC;color:#fff;font-weight:bold;">Teacher Attendance</div>
    <div class="card-body">
      @if(session('message'))<div class="alert alert-success" id="alert-message">{{ session('message') }}</div>@endif

      <form method="GET" action="{{ request()->getBaseUrl() }}/attendance/teachers" class="row g-2 mb-3">
        <div class="col-md-3">
          <label>Date</label>
          <input type="date" name="date" class="form-control" value="{{ $date }}" onchange="this.form.submit()" />
        </div>
      </form>

      <form method="POST" action="{{ request()->getBaseUrl() }}/attendance/teachers">
        @csrf
        <input type="hidden" name="date" value="{{ $date }}" />
        <div class="table-responsive">
          <table class="table table-bordered align-middle">
            <thead>
              <tr>
                <th>#</th>
                <th>Teacher</th>
                <th>Status</th>
                <th>Remarks</th>
              </tr>
            </thead>
            <tbody>
              @php $i=1; @endphp
              @foreach($teachers as $t)
                @php $row = $existingByTeacher[$t->id] ?? null; @endphp
                <tr>
                  <td>{{ $i++ }}</td>
                  <td>{{ $t->teacher_name }}</td>
                  <td>
                    <select class="form-control" name="entries[{{ $t->id }}][status]">
                      @foreach(['present','absent','leave','late'] as $st)
                        <option value="{{ $st }}" {{ ($row->status ?? 'present')===$st ? 'selected' : '' }}>{{ ucfirst($st) }}</option>
                      @endforeach
                    </select>
                  </td>
                  <td>
                    <input class="form-control" name="entries[{{ $t->id }}][remarks]" value="{{ $row->remarks ?? '' }}" />
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


