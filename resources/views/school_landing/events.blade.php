@extends('school_landing.layout')

@section('title', 'Upcoming Events | ' . $school->schoolName)
@section('page_title', 'School Events')

@section('content')
<div class="row g-4" data-aos="fade-up">
    @forelse($events as $event)
    <div class="col-lg-4 col-md-6">
        <div class="card card-premium overflow-hidden h-100">
            @if($event->image)
            <img src="{{ $event->image }}" class="card-img-top" alt="{{ $event->title }}" style="height: 200px; object-fit: cover;">
            @endif
            <div class="card-body p-4">
                <div class="badge bg-primary mb-3">{{ \Carbon\Carbon::parse($event->event_date)->format('d M, Y') }}</div>
                <h4 class="fw-bold mb-3">{{ $event->title }}</h4>
                <p class="text-muted small mb-4">{{ Str::limit(strip_tags($event->content), 120) }}</p>
                <div class="d-flex align-items-center mb-4 text-muted small">
                    <i class="bi bi-geo-alt me-2 text-primary"></i> {{ $event->location ?? 'School Campus' }}
                </div>
                <a href="{{ route('school.event.show', [$school->slug, $event->slug]) }}" class="btn btn-outline-primary rounded-pill w-100 fw-bold">View Details</a>
            </div>
        </div>
    </div>
    @empty
    <div class="col-12 text-center py-5">
        <i class="bi bi-calendar-event display-1 text-muted opacity-25"></i>
        <h3 class="mt-4 text-muted">No upcoming events</h3>
    </div>
    @endforelse
</div>

<div class="mt-5 d-flex justify-content-center">
    {{ $events->links() }}
</div>
@endsection