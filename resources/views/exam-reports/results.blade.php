@extends('layouts.app')

@section('content')
<div class="pagetitle">
  <h1>Student Results</h1>
  <nav>
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
      <li class="breadcrumb-item"><a href="{{ route('exam-reports.index') }}">Exam Reports</a></li>
      <li class="breadcrumb-item active">Student Results</li>
    </ol>
  </nav>
</div>

<section class="section">
  <div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
      <h5 class="mb-0">Filters</h5>
      <div>
        <a href="{{ request()->fullUrlWithQuery(['export' => 'csv']) }}" class="btn btn-sm btn-success">
          <i class="bi bi-download"></i> Export CSV
        </a>
      </div>
    </div>
    <div class="card-body">
      <form method="GET" class="row g-2">
        <div class="col-md-3">
          <label class="form-label">Exam</label>
          <select name="exam_id" class="form-select">
            <option value="">All Exams</option>
            @foreach($exams as $e)
              <option value="{{ $e->id }}" {{ (string)request('exam_id') === (string)$e->id ? 'selected' : '' }}>{{ $e->exam_name }}</option>
            @endforeach
          </select>
        </div>
        <div class="col-md-3">
          <label class="form-label">Class</label>
          <select name="class_id" class="form-select">
            <option value="">All Classes</option>
            @foreach($classes as $c)
              <option value="{{ $c->id }}" {{ (string)request('class_id') === (string)$c->id ? 'selected' : '' }}>{{ $c->className }}</option>
            @endforeach
          </select>
        </div>
        <div class="col-md-3">
          <label class="form-label">Student</label>
          <select name="student_id" class="form-select">
            <option value="">All Students</option>
            @foreach($students as $s)
              <option value="{{ $s->id }}" {{ (string)request('student_id') === (string)$s->id ? 'selected' : '' }}>{{ $s->studentName }}</option>
            @endforeach
          </select>
        </div>
        <div class="col-md-3">
          <label class="form-label">Status</label>
          <select name="status" class="form-select">
            <option value="">All</option>
            @foreach(['pass' => 'Pass','fail' => 'Fail','absent' => 'Absent'] as $val => $label)
              <option value="{{ $val }}" {{ request('status') === $val ? 'selected' : '' }}>{{ $label }}</option>
            @endforeach
          </select>
        </div>
        <div class="col-md-3">
          <label class="form-label">From</label>
          <input type="date" name="from_date" value="{{ request('from_date') }}" class="form-control" />
        </div>
        <div class="col-md-3">
          <label class="form-label">To</label>
          <input type="date" name="to_date" value="{{ request('to_date') }}" class="form-control" />
        </div>
        <div class="col-md-3 align-self-end">
          <button class="btn btn-primary"><i class="bi bi-funnel"></i> Apply</button>
          <a href="{{ route('exam-reports.results') }}" class="btn btn-secondary">Reset</a>
        </div>
      </form>
    </div>
  </div>

  <div class="card">
    <div class="card-header">
      <h5 class="mb-0">Results</h5>
    </div>
    <div class="card-body table-responsive">
      <table class="table table-striped table-hover align-middle">
        <thead>
          <tr>
            <th>GR No</th>
            <th>Student</th>
            <th>Class</th>
            <th>Exam</th>
            <th>Subject</th>
            <th class="text-end">Total</th>
            <th class="text-end">Obtained</th>
            <th class="text-end">%</th>
            <th>Grade</th>
            <th>Position</th>
            <th>Status</th>
            <th>Graded At</th>
          </tr>
        </thead>
        <tbody>
          @forelse($results as $r)
            <tr>
              <td>{{ $r->grno }}</td>
              <td>{{ $r->student_name }}</td>
              <td>{{ $r->class_name }}</td>
              <td>{{ $r->exam_name }}</td>
              <td>{{ $r->subject_name }}</td>
              <td class="text-end">{{ $r->total_marks }}</td>
              <td class="text-end">{{ number_format((float)$r->obtained_marks, 2) }}</td>
              <td class="text-end">{{ number_format((float)$r->percentage, 2) }}</td>
              <td>
                <span class="badge bg-{{ in_array($r->grade, ['A+','A']) ? 'success' : (in_array($r->grade, ['B+','B','C+','C']) ? 'warning' : 'danger') }}">{{ $r->grade }}</span>
              </td>
              <td>{{ $r->position ?: '—' }}</td>
              <td>
                @php $color = $r->status === 'pass' ? 'success' : ($r->status === 'absent' ? 'secondary' : 'danger'); @endphp
                <span class="badge bg-{{ $color }} text-uppercase">{{ $r->status }}</span>
              </td>
              <td>{{ $r->graded_at }}</td>
            </tr>
          @empty
            <tr>
              <td colspan="12" class="text-center text-muted">No results found</td>
            </tr>
          @endforelse
        </tbody>
      </table>

      <div class="d-flex justify-content-end">
        {{ $results->links() }}
      </div>
    </div>
  </div>
</section>
@endsection
