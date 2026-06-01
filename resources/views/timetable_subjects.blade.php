@extends('layouts.app')

@section('content')

<style>
    .tt-card-header { background: linear-gradient(90deg, #2B32B2 0%, #1488CC 100%); color:#fff; font-weight:600; }
    .tt-table { width:100%; border-collapse:separate; border-spacing:0; table-layout:fixed; font-size:13px; }
    .tt-table th, .tt-table td { border:1px solid #e5e7eb; padding:6px 8px; vertical-align:top; word-wrap:break-word; background:#fff; }
    .tt-table thead th { background:#f8fafc; text-transform:uppercase; font-size:12px; letter-spacing:.02em; }
    .period-badge { display:inline-block; background:#eef2ff; color:#3730a3; border:1px solid #e0e7ff; border-radius:4px; padding:1px 6px; font-weight:600; font-size:11px; margin-right:6px; }
    .slot { margin-bottom:6px; line-height:1.2; }
    .class { color:#0d6efd; font-weight:600; }
    .teacher { color:#6b7280; }
</style>

<div class="fluid-container">
  <div class="row justify-content-center">
    <div class="col-md-12">
      <div class="card shadow-sm border-0">
        <div class="card-header tt-card-header">
          <i class="bi bi-grid-3x3-gap"></i> Weekly Timetable - Subject-wise
        </div>
        <div class="card-body">
          <div class="table-responsive">
            <table class="tt-table">
              <thead>
                <tr>
                  <th style="width:200px;">Subject</th>
                  @foreach($days as $day)
                    <th>{{ strtoupper($day) }}</th>
                  @endforeach
                </tr>
              </thead>
              <tbody>
                @foreach($subjects as $subject)
                  <tr>
                    <td><strong>{{ $subject->subject_name }}</strong></td>
                    @foreach($days as $day)
                      <td>
                        @foreach($periods as $period)
                          @php
                            $entry = DB::table('timetables')
                              ->join('teachers','timetables.teacher_id','=','teachers.id')
                              ->where('timetables.subject', $subject->subject_name)
                              ->where('timetables.day', $day)
                              ->where('timetables.period_id', $period->id)
                              ->select('timetables.*','teachers.teacher_name')
                              ->first();
                          @endphp
                          @if($entry)
                            <div class="slot">
                              <span class="period-badge">{{ $period->periodName }}</span>
                              <span class="class">{{ $entry->class }}</span>
                              <span class="teacher">- {{ $entry->teacher_name }}</span>
                            </div>
                          @endif
                        @endforeach
                      </td>
                    @endforeach
                  </tr>
                @endforeach
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

@endsection


