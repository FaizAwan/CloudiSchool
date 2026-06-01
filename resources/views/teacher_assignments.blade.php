@extends('layouts.app')

@section('content')
<div class="container my-4">
  <div class="card">
    <div class="card-header" style="background:#1488CC;color:#fff;font-weight:bold;">Classes & Subjects Mapping</div>
    <div class="card-body">
      @if(session('message'))
        <div class="alert alert-success" id="alert-message">{{ session('message') }}</div>
      @endif

      <div class="row">
        <div class="col-md-6">
          <div class="card mb-3">
            <div class="card-header">Assign Classes to Teacher</div>
            <div class="card-body">
              <form method="POST" action="{{ request()->getBaseUrl() }}/teacher-assignments/classes">
                @csrf
                <label>Teacher</label>
                <select name="teacher_id" class="form-control">
                  @foreach($teachers as $t)
                    <option value="{{ $t->id }}">{{ $t->teacher_name }}</option>
                  @endforeach
                </select>
                <label class="mt-2">Classes</label>
                <select name="class_ids[]" class="form-control" multiple size="8">
                  @foreach($classes as $c)
                    <option value="{{ $c->id }}">{{ $c->className }}</option>
                  @endforeach
                </select>
                <button class="btn btn-primary mt-3" type="submit">Save Classes</button>
              </form>
            </div>
          </div>
        </div>

        <div class="col-md-6">
          <div class="card mb-3">
            <div class="card-header">Assign Subjects to Teacher (by Class)</div>
            <div class="card-body">
              <form method="POST" action="{{ request()->getBaseUrl() }}/teacher-assignments/subjects">
                @csrf
                <label>Teacher</label>
                <select name="teacher_id" class="form-control">
                  @foreach($teachers as $t)
                    <option value="{{ $t->id }}">{{ $t->teacher_name }}</option>
                  @endforeach
                </select>
                <label class="mt-2">Class</label>
                <select name="class_id" class="form-control">
                  @foreach($classes as $c)
                    <option value="{{ $c->id }}">{{ $c->className }}</option>
                  @endforeach
                </select>
                <label class="mt-2">Subjects (hold Ctrl to select multiple)</label>
                <select name="subject_names[]" class="form-control" multiple size="8">
                  @foreach($subjects as $s)
                    <option value="{{ $s->subject_name }}">{{ $s->subject_name }}</option>
                  @endforeach
                </select>
                <button class="btn btn-primary mt-3" type="submit">Save Subjects</button>
              </form>
            </div>
          </div>
        </div>
      </div>

      <div class="row">
        <div class="col-md-12">
          <div class="card">
            <div class="card-header">Assigned Classes & Subjects Overview</div>
            <div class="card-body">
              <div class="table-responsive">
                <table class="table table-bordered">
                  <thead>
                    <tr>
                      <th>Teacher</th>
                      <th>Classes</th>
                      <th>Subjects by Class</th>
                    </tr>
                  </thead>
                  <tbody>
                    @foreach($teachers as $t)
                      @php
                        $cls = $teacherClasses->where('teacher_id', $t->id)->pluck('class_id');
                        $classNames = \DB::table('classes')->whereIn('id', $cls)->pluck('className')->implode(', ');
                        $subs = $teacherSubjects->where('teacher_id', $t->id);
                        $byClass = [];
                        foreach($subs as $row){
                          $cn = \DB::table('classes')->where('id', $row->class_id)->value('className');
                          $byClass[$cn] = ($byClass[$cn] ?? []);
                          $byClass[$cn][] = $row->subject_name;
                        }
                      @endphp
                      <tr>
                        <td>{{ $t->teacher_name }}</td>
                        <td>{{ $classNames }}</td>
                        <td>
                          @foreach($byClass as $cn => $list)
                            <div><strong>{{ $cn }}:</strong> {{ implode(', ', $list) }}</div>
                          @endforeach
                        </td>
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
  </div>
</div>

<script>
setTimeout(function(){
  var m = document.getElementById('alert-message');
  if(m) m.style.display='none';
}, 4000);
</script>
@endsection


