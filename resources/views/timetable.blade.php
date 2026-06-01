@extends('layouts.app')

@section('content')

<style>
    /* Professional compact timetable layout */
    .tt-card-header {
        background: linear-gradient(90deg, #2B32B2 0%, #1488CC 100%);
        color: #fff;
        font-weight: 600;
    }
    .tt-table {
        width: 100%;
        border-collapse: separate;
        border-spacing: 0;
        table-layout: fixed;
        font-size: 13px;
    }
    .tt-table th, .tt-table td {
        border: 1px solid #e5e7eb;
        padding: 6px 8px;
        vertical-align: top;
        word-wrap: break-word;
        background: #fff;
    }
    .tt-table thead th {
        position: sticky;
        top: 0;
        background: #f8fafc;
        z-index: 1;
        text-transform: uppercase;
        font-size: 12px;
        letter-spacing: .02em;
    }
    .tt-slot { margin-bottom: 6px; line-height: 1.2; }
    .tt-slot .period-badge {
        display: inline-block;
        background: #eef2ff;
        color: #3730a3;
        border: 1px solid #e0e7ff;
        border-radius: 4px;
        padding: 1px 6px;
        font-weight: 600;
        font-size: 11px;
        margin-right: 6px;
    }
    .tt-slot .subject { color: #0d6efd; font-weight: 600; }
    .tt-slot .class { color: #6b7280; }
    .tt-slot .del { color: #ef4444; text-decoration: none; margin-left: 6px; font-weight: 700; }
    .tt-wrapper { overflow-x: hidden; }
</style>
<div class="fluid-container">
    <div class="row justify-content-center">
        
        <div class="col-md-12">
            <div class="card">
                <div style="background: #0072ff;  /* fallback for old browsers */
                background: -webkit-linear-gradient(to right, #E4E5E6, #0072ff);  /* Chrome 10-25, Safari 5.1-6 */
                background: linear-gradient(to right, #E4E5E6, #0072ff); /* W3C, IE 10+/ Edge, Firefox 16+, Chrome 26+, Opera 12+, Safari 7+ */
                color:#000; font-size:27px; font-weight:bold; text-align:left;" class="card-header"> T I M E &nbsp; &nbsp; T A B L E </div>

                <div class="card-body">
                    <div class="row"><hr/>
                    <div class=" col-md-12">
                            <div class="card shadow-sm border-0">
                                <div class="card-header tt-card-header">
                                    <i class="bi bi-calendar3"></i> Weekly Timetable
                                </div>
                                <div class="card-body tt-wrapper">
                                    @if(Session::has('message'))
                                        <div class="alert alert-success">
                                            {{ Session::get('message') }}
                                        </div>
                                    @endif
                                    
                                    @if(Session::has('errorMessage'))
                                        <div class="alert alert-danger">
                                            {{ Session::get('errorMessage') }}
                                        </div>
                                    @endif
                                    
                                    @if ($errors->any())
                                        <div class="alert alert-warning">
                                            <strong>Validation Errors:</strong>
                                            <ul class="mb-0">
                                                @foreach ($errors->all() as $error)
                                                    <li>{{ $error }}</li>
                                                @endforeach
                                            </ul>
                                        </div>
                                    @endif
                                    
                                    <form action="{{ route('addTimetable') }}" method="POST">
                                      @csrf
                                    
                                      <div class="row mb-3"> <div class="col-md-2"> <label for="teacherSelect" class="form-label">Teacher</label>
                                          <select class="form-control" id="teacherSelect" name="teacher_id">
                                            @foreach ($teachers as $teacher)
                                              <option value="{{ $teacher->id }}">{{ $teacher->teacher_name }}</option>
                                            @endforeach
                                          </select>
                                        </div>
                                        <div class="col-md-2"> <label for="daySelect" class="form-label">Day</label>
                                          <select class="form-control" id="daySelect" name="day">
                                            <option value="Monday">Monday</option>
                                            <option value="Tuesday">Tuesday</option>
                                            <option value="Wednesday">Wednesday</option>
                                            <option value="Thursday">Thursday</option>
                                            <option value="Friday">Friday</option>
                                            <option value="Saturday">Saturday</option>
                                            <option value="Sunday">Sunday</option>
                                            </select>
                                        </div>
                                        <div class="col-md-2"> <label for="periodSelect" class="form-label">Period</label>
                                          <select class="form-control" id="periodSelect" name="period_id">
                                            @foreach ($periods as $period)
                                              <option value="{{ $period->id }}">{{ $period->periodName }}</option>
                                            @endforeach
                                          </select>
                                        </div>
                                        <div class="col-md-2"> <label for="periodSelect" class="form-label">Class</label>
                                          <select class="form-control" id="classSelect" name="class_id">
                                            @foreach ($classes as $rowClass)
                                              <option value="{{ $rowClass->className }}">{{ $rowClass->className }}</option>
                                            @endforeach
                                          </select>
                                        </div>
                                        
                                        <div class="col-md-2">  <label for="subjectSelect" class="form-label">Subject</label>
                                          <select class="form-control" id="subjectSelect" name="subject">
                                            @foreach ($subjects as $subject)
                                              <option value="{{ $subject->subject_name }}">{{ $subject->subject_name }}</option>
                                            @endforeach
                                          </select>
                                        </div>
                                        <div class="col-md-1">  <button type="submit" class="btn btn-primary mt-4">Add</button> </div>
                                      </div> 
                                    </form>

                                    
                                </div>
                            </div>
                    </div>
                        
                        <div class=" col-md-12">

                            <div class="card shadow-sm border-0 mt-3">
                                <div class="card-header bg-white" style="font-weight:600;">
                                    <i class="bi bi-grid-3x3-gap text-primary"></i> Timetable Grid
                                </div>
                                <div class="card-body" style="overflow-x: hidden;">
                                  @php $days = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday']; @endphp
                                  <ul class="nav nav-tabs" role="tablist">
                                    <li class="nav-item" role="presentation">
                                      <button class="nav-link active" id="tab-teacher-tab" data-bs-toggle="tab" data-bs-target="#tab-teacher" type="button" role="tab" aria-controls="tab-teacher" aria-selected="true">Teacher-wise</button>
                                    </li>
                                    <li class="nav-item" role="presentation">
                                      <button class="nav-link" id="tab-class-tab" data-bs-toggle="tab" data-bs-target="#tab-class" type="button" role="tab" aria-controls="tab-class" aria-selected="false">Class-wise</button>
                                    </li>
                                    <li class="nav-item" role="presentation">
                                      <button class="nav-link" id="tab-subject-tab" data-bs-toggle="tab" data-bs-target="#tab-subject" type="button" role="tab" aria-controls="tab-subject" aria-selected="false">Subject-wise</button>
                                    </li>
                                  </ul>
                                  <div class="tab-content pt-3">
                                    <div class="tab-pane fade show active" id="tab-teacher" role="tabpanel" aria-labelledby="tab-teacher-tab">
                                      <div class="table-responsive">
                                        <table class="tt-table">
                                          <thead>
                                            <tr>
                                              <th style="width: 180px;">Teacher</th>
                                              @foreach($days as $day)
                                                <th>{{ strtoupper($day) }}</th>
                                              @endforeach
                                            </tr>
                                          </thead>
                                          <tbody>
                                            @foreach($teachers as $teacher)
                                              <tr>
                                                <td><strong>{{ $teacher->teacher_name }}</strong></td>
                                                @foreach($days as $day)
                                                  <td>
                                                    @foreach($periods as $period)
                                                      @php 
                                                        $entry = DB::table('timetables')
                                                          ->where('teacher_id', $teacher->id)
                                                          ->where('day', $day)
                                                          ->where('period_id', $period->id)
                                                          ->first();
                                                      @endphp
                                                      @if($entry)
                                                        <div class="tt-slot">
                                                          <span class="period-badge">{{ $period->periodName }}</span>
                                                          <span class="subject">{{ $entry->subject }}</span>
                                                          <span class="class">- {{ $entry->class }}</span>
                                                          <a class="del" href="#" onclick="confirmDelete('{{ $entry->id }}', '{{ $day }}', '{{ $period->id }}')">×</a>
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
                                    <div class="tab-pane fade" id="tab-class" role="tabpanel" aria-labelledby="tab-class-tab">
                                      <div class="table-responsive">
                                        <table class="tt-table">
                                          <thead>
                                            <tr>
                                              <th style="width:160px;">Class</th>
                                              @foreach($days as $day)
                                                <th>{{ strtoupper($day) }}</th>
                                              @endforeach
                                            </tr>
                                          </thead>
                                          <tbody>
                                            @foreach($classes as $class)
                                              <tr>
                                                <td><strong>{{ $class->className }}</strong></td>
                                                @foreach($days as $day)
                                                  <td>
                                                    @foreach($periods as $period)
                                                      @php
                                                        $entry = DB::table('timetables')
                                                          ->join('teachers','timetables.teacher_id','=','teachers.id')
                                                          ->where('timetables.class', $class->className)
                                                          ->where('timetables.day', $day)
                                                          ->where('timetables.period_id', $period->id)
                                                          ->select('timetables.*','teachers.teacher_name')
                                                          ->first();
                                                      @endphp
                                                      @if($entry)
                                                        <div class="tt-slot">
                                                          <span class="period-badge">{{ $period->periodName }}</span>
                                                          <span class="subject">{{ $entry->subject }}</span>
                                                          <span class="class">- {{ $entry->teacher_name }}</span>
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
                                    <div class="tab-pane fade" id="tab-subject" role="tabpanel" aria-labelledby="tab-subject-tab">
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
                                                        <div class="tt-slot">
                                                          <span class="period-badge">{{ $period->periodName }}</span>
                                                          <span class="subject">{{ $entry->class }}</span>
                                                          <span class="class">- {{ $entry->teacher_name }}</span>
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

                            <script>
                                function confirmDelete(entryId, day, periodId) {
                                    if (confirm("Are you sure you want to delete this entry?")) {
                                        window.location.href = "{{ url('deleteTimeTable') }}" + '/' + entryId + '/' + day + '/' + periodId;
                                    }
                                }
                            </script>


                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

