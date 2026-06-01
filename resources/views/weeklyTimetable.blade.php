@extends('layouts.app')

@section('content')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.css">
<style>
  .fc {
    font-family: 'Inter', sans-serif;
  }

  .fc-header-toolbar {
    padding: 1rem !important;
    background: #fff;
    border-radius: 15px;
    box-shadow: var(--premium-shadow);
    margin-bottom: 2rem !important;
  }

  .fc-toolbar-title {
    font-weight: 800 !important;
    color: var(--cloudi-navy-dark) !important;
    font-size: 1.5rem !important;
  }

  .fc-button-primary {
    background-color: var(--cloudi-navy-mid) !important;
    border-color: var(--cloudi-navy-mid) !important;
    border-radius: 8px !important;
    text-transform: capitalize !important;
    font-weight: 600 !important;
  }

  .fc-button-primary:hover {
    background-color: var(--cloudi-navy-dark) !important;
  }

  .fc-button-active {
    background-color: var(--cloudi-navy-dark) !important;
  }

  .fc-day-today {
    background-color: rgba(59, 130, 246, 0.05) !important;
  }

  .fc-event {
    border-radius: 6px !important;
    border: none !important;
    padding: 3px 6px !important;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05) !important;
    cursor: pointer;
    transition: transform 0.2s ease;
  }

  .fc-event:hover {
    transform: scale(1.02);
  }

  .fc-v-event {
    background-color: var(--cloudi-navy-mid) !important;
  }

  .fc-h-event {
    background-color: var(--cloudi-navy-light) !important;
  }
</style>

<div class="pagetitle">
  <h1>Weekly Timetable</h1>
  <nav>
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
      <li class="breadcrumb-item active">Timetable</li>
    </ol>
  </nav>
</div>

<section class="section">
  <div class="card">
    <div class="card-body pt-3">
      <div id='calendar'></div>
    </div>
  </div>
</section>

<!-- Event Modal -->
<div class="modal fade" id="eventDetailsModal" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content border-0 shadow-lg" style="border-radius: 20px;">
      <div class="modal-header border-0 bg-primary text-white p-4" style="border-radius:20px 20px 0 0;">
        <h5 class="modal-title fw-bold">Session Details</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body p-4">
        <div class="d-flex align-items-center mb-4">
          <div class="rounded-circle bg-primary text-white p-3 me-3">
            <i class="bi bi-clock-history fs-4"></i>
          </div>
          <div>
            <h6 class="fw-bold mb-0" id="modalTitle">Math Class</h6>
            <small class="text-muted" id="modalDay">Monday, 10:00 AM</small>
          </div>
        </div>
        <div class="row g-3">
          <div class="col-6">
            <div class="p-3 bg-light rounded-3">
              <small class="text-muted d-block small text-uppercase fw-bold">Teacher</small>
              <span class="fw-bold" id="modalTeacher">Mr. Ayub</span>
            </div>
          </div>
          <div class="col-6">
            <div class="p-3 bg-light rounded-3">
              <small class="text-muted d-block small text-uppercase fw-bold">Subject</small>
              <span class="fw-bold" id="modalSubject">Mathematics</span>
            </div>
          </div>
        </div>
      </div>
      <div class="modal-footer border-0 p-4">
        <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.4/moment.min.js"></script>
<script>
  document.addEventListener('DOMContentLoaded', function() {
    const subjects = ["Math", "Science", "English", "Physics", "Chemistry", "Biology", "History", "Islamiyat"];
    const teachers = ["Mr. Ayub", "Mr. Qutbi Alam", "Ms. Salma", "Ms. Humaira Nasir", "Mr. Khalid", "Ms. Farida"];
    const classes = ["Class 1", "Class 2", "Class 3", "Class 4", "Class 5"];

    function generateEvents() {
      let events = [];
      let startOfMonth = moment().startOf('month');
      for (let i = 0; i < 31; i++) {
        let day = startOfMonth.clone().add(i, 'days');
        if (day.day() === 0 || day.day() === 6) continue; // Skip weekends

        for (let j = 0; j < 6; j++) {
          let subject = subjects[Math.floor(Math.random() * subjects.length)];
          let teacher = teachers[Math.floor(Math.random() * teachers.length)];
          let cls = classes[Math.floor(Math.random() * classes.length)];
          let startTime = day.clone().hour(8 + j).minute(0);
          let endTime = day.clone().hour(8 + j).minute(45);

          events.push({
            title: subject + ' (' + cls + ')',
            start: startTime.format(),
            end: endTime.format(),
            extendedProps: {
              teacher: teacher,
              subject: subject,
              class: cls
            },
            backgroundColor: j % 2 === 0 ? '#1e40af' : '#3b82f6'
          });
        }
      }
      return events;
    }

    var calendarEl = document.getElementById('calendar');
    var calendar = new FullCalendar.Calendar(calendarEl, {
      initialView: 'timeGridWeek',
      headerToolbar: {
        left: 'prev,next today',
        center: 'title',
        right: 'timeGridWeek,timeGridDay,dayGridMonth'
      },
      slotMinTime: '08:00:00',
      slotMaxTime: '15:00:00',
      allDaySlot: false,
      height: 'auto',
      events: generateEvents(),
      eventClick: function(info) {
        const props = info.event.extendedProps;
        $('#modalTitle').text(info.event.title);
        $('#modalDay').text(moment(info.event.start).format('dddd, h:mm A'));
        $('#modalTeacher').text(props.teacher);
        $('#modalSubject').text(props.subject);
        new bootstrap.Modal(document.getElementById('eventDetailsModal')).show();
      }
    });
    calendar.render();
  });
</script>
@endsection
@endsection