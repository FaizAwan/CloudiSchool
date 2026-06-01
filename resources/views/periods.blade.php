@extends('layouts.app')

@section('content')
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.css">

<div class="pagetitle">
    <h1>Periods Management</h1>
    <nav>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
            <li class="breadcrumb-item active">Periods</li>
        </ol>
    </nav>
</div>

<section class="section">
    <div class="row">
        <!-- Add Period Form -->
        <div class="col-lg-3">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">Add New Period</h5>
                </div>
                <div class="card-body pt-3">
                    <form action="{{ url('addPeriod') }}" method="POST" class="row g-3">
                        @csrf
                        <div class="col-12 mb-2">
                            <label class="form-label">Branch Name</label>
                            <select class="form-select" name="school_id" required>
                                <option value="">-- Select Branch --</option>
                                @foreach($schoolList as $sch)
                                <option value="{{ $sch->id }}">{{ $sch->schoolName }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-12 mb-2">
                            <div class="form-floating">
                                <input class="form-control" name="periodName" id="periodName" type="text" placeholder="e.g. 1st Period" required />
                                <label for="periodName">Period Name</label>
                            </div>
                        </div>

                        <div class="col-12 mb-2">
                            <label class="form-label">Day</label>
                            <select class="form-select" name="day" required>
                                <option value="Monday">Monday</option>
                                <option value="Tuesday">Tuesday</option>
                                <option value="Wednesday">Wednesday</option>
                                <option value="Thursday">Thursday</option>
                                <option value="Friday">Friday</option>
                                <option value="Saturday">Saturday</option>
                                <option value="Sunday">Sunday</option>
                            </select>
                        </div>

                        <div class="col-12 mb-2">
                            <div class="form-floating">
                                <input class="form-control" name="start_time" id="startTime" type="text" placeholder="e.g. 08:00 AM" />
                                <label for="startTime">Start Time</label>
                            </div>
                        </div>

                        <div class="col-12 mb-2">
                            <div class="form-floating">
                                <input class="form-control" name="end_time" id="endTime" type="text" placeholder="e.g. 08:40 AM" />
                                <label for="endTime">End Time</label>
                            </div>
                        </div>

                        <div class="col-12 mt-4">
                            <button class="btn btn-primary w-100 py-3 rounded-pill shadow" type="submit">
                                <i class="bi bi-plus-circle me-2"></i> Add Period
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Period List -->
        <div class="col-lg-9">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title">Period List</h5>
                    @if(session('message'))
                    <div id="alert-message" class="badge bg-light text-primary p-2">
                        {{ session('message') }}
                    </div>
                    @endif
                </div>
                <div class="card-body pt-3">
                    <div class="table-responsive">
                        <table class="table table-hover" id="periodsTable">
                            <thead>
                                <tr>
                                    <th>S.NO</th>
                                    <th>Branch</th>
                                    <th>Period Name</th>
                                    <th>Day</th>
                                    <th>Start</th>
                                    <th>End</th>
                                    <th class="text-center">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php $serial = 1; @endphp
                                @foreach($periods as $period)
                                <tr>
                                    <td>{{ $serial++ }}</td>
                                    <td>{{ $period->school_name ?? 'N/A' }}</td>
                                    <td class="fw-bold text-primary">{{ $period->periodName }}</td>
                                    <td><span class="badge bg-light text-dark">{{ $period->day ?? '-' }}</span></td>
                                    <td>{{ $period->start_time ?? '-' }}</td>
                                    <td>{{ $period->end_time ?? '-' }}</td>
                                    <td class="text-center">
                                        <div class="d-flex justify-content-center gap-2">
                                            <button class="btn btn-sm btn-outline-primary rounded-pill edit-button"
                                                data-id="{{ $period->id }}"
                                                data-name="{{ $period->periodName }}"
                                                data-day="{{ $period->day }}"
                                                data-start="{{ $period->start_time }}"
                                                data-end="{{ $period->end_time }}"
                                                data-school="{{ $period->school_id }}"><i class="bi bi-pencil-square"></i></button>
                                            <a href="{{ url('deletePeriod/'.$period->id) }}"
                                                class="btn btn-sm btn-outline-danger rounded-pill"
                                                onclick="return confirm('Delete this period?');"><i class="bi bi-trash"></i></a>
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Weekly Timetable Preview -->
            <div class="card mt-4 border-0 shadow-sm">
                <div class="card-header bg-white d-flex justify-content-between align-items-center py-3">
                    <h5 class="card-title mb-0"><i class="bi bi-calendar3 me-2"></i>Weekly Timetable Preview</h5>
                    <a href="{{ url('weeklyTimetable') }}" class="btn btn-sm btn-primary rounded-pill px-3">Manage Full Timetable</a>
                </div>
                <div class="card-body pt-3">
                    @php $days = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday']; @endphp
                    <ul class="nav nav-pills mb-3" role="tablist">
                        <li class="nav-item"><button class="nav-link active rounded-pill" data-bs-toggle="tab" data-bs-target="#tab-teacher">Teacher-wise</button></li>
                        <li class="nav-item"><button class="nav-link rounded-pill" data-bs-toggle="tab" data-bs-target="#tab-class">Class-wise</button></li>
                    </ul>
                    <div class="tab-content">
                        <div class="tab-pane fade show active" id="tab-teacher">
                            <div class="table-responsive">
                                <table class="table table-bordered small align-middle">
                                    <thead class="bg-light">
                                        <tr>
                                            <th>Teacher</th>
                                            @foreach($days as $day) <th>{{ $day }}</th> @endforeach
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($teachers->take(10) as $teacher)
                                        <tr>
                                            <td class="fw-bold">{{ $teacher->teacher_name }}</td>
                                            @foreach($days as $day)
                                            <td>
                                                @foreach($periods->where('day', $day) as $p)
                                                @php
                                                $entry = DB::table('timetables')->where('teacher_id', $teacher->id)->where('day', $day)->where('period_id', $p->id)->first();
                                                @endphp
                                                @if($entry)
                                                <div class="p-1 mb-1 bg-primary text-white rounded x-small" style="font-size: 10px;">
                                                    <strong>{{ $p->periodName }}</strong>: {{ $entry->subject }} ({{ $entry->class }})
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
                        <div class="tab-pane fade" id="tab-class">
                            <div class="table-responsive">
                                <table class="table table-bordered small align-middle">
                                    <thead class="bg-light">
                                        <tr>
                                            <th>Class</th>
                                            @foreach($days as $day) <th>{{ $day }}</th> @endforeach
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($classes->take(10) as $class)
                                        <tr>
                                            <td class="fw-bold">{{ $class->className }}</td>
                                            @foreach($days as $day)
                                            <td>
                                                @foreach($periods->where('day', $day) as $p)
                                                @php
                                                $entry = DB::table('timetables')->where('class', $class->className)->where('day', $day)->where('period_id', $p->id)->first();
                                                @endphp
                                                @if($entry)
                                                <div class="p-1 mb-1 bg-success text-white rounded x-small" style="font-size: 10px;">
                                                    <strong>{{ $p->periodName }}</strong>: {{ $entry->subject }}
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
    </div>
</section>

<!-- Edit Modal -->
<div class="modal fade" id="editModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 20px;">
            <div class="modal-header border-0 bg-primary text-white p-4" style="border-radius:20px 20px 0 0;">
                <h5 class="modal-title fw-bold">Edit Period Details</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form id="editForm" method="POST" action="{{ url('updatePeriod') }}">
                @csrf
                <div class="modal-body p-4">
                    <input type="hidden" name="id" id="editId">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Branch Name</label>
                        <select class="form-select" name="school_id" id="editSchool" required>
                            @foreach($schoolList as $sch)
                            <option value="{{ $sch->id }}">{{ $sch->schoolName }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-floating mb-3">
                        <input type="text" class="form-control" id="editName" name="periodName" placeholder="Period Name">
                        <label for="editName">Period Name</label>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Day</label>
                        <select class="form-select" name="day" id="editDay">
                            <option value="Monday">Monday</option>
                            <option value="Tuesday">Tuesday</option>
                            <option value="Wednesday">Wednesday</option>
                            <option value="Thursday">Thursday</option>
                            <option value="Friday">Friday</option>
                            <option value="Saturday">Saturday</option>
                            <option value="Sunday">Sunday</option>
                        </select>
                    </div>
                    <div class="form-floating mb-3">
                        <input type="text" class="form-control" id="editStart" name="start_time" placeholder="Start Time">
                        <label for="editStart">Start Time</label>
                    </div>
                    <div class="form-floating mb-3">
                        <input type="text" class="form-control" id="editEnd" name="end_time" placeholder="End Time">
                        <label for="editEnd">End Time</label>
                    </div>
                </div>
                <div class="modal-footer border-0 p-4">
                    <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary rounded-pill px-4 shadow">Update changes</button>
                </div>
            </form>
        </div>
    </div>
</div>

@section('scripts')
<script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.js"></script>
<script>
    $(document).ready(function() {
        $('#periodsTable').DataTable({
            pageLength: 25,
            order: [
                [2, 'asc']
            ],
            language: {
                search: "_INPUT_",
                searchPlaceholder: "Search periods...",
                paginate: {
                    previous: '<i class="bi bi-chevron-left"></i>',
                    next: '<i class="bi bi-chevron-right"></i>'
                }
            }
        });

        $(document).on('click', '.edit-button', function() {
            $('#editId').val($(this).data('id'));
            $('#editName').val($(this).data('name'));
            $('#editDay').val($(this).data('day'));
            $('#editStart').val($(this).data('start'));
            $('#editEnd').val($(this).data('end'));
            $('#editSchool').val($(this).data('school'));
            new bootstrap.Modal(document.getElementById('editModal')).show();
        });

        setTimeout(() => {
            $('#alert-message').fadeOut();
        }, 5000);
    });
</script>
@endsection
@endsection