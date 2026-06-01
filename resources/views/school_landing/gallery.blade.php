@extends('school_landing.layout')

@section('title', 'Photo Gallery | ' . $school->schoolName)
@section('page_title', 'Photo Gallery')

@section('content')
<div class="row g-4" data-aos="fade-up">
    @forelse($gallery as $item)
    <div class="col-lg-3 col-md-6">
        <div class="card card-premium overflow-hidden h-100">
            <img src="{{ $item->image }}" class="card-img-top" alt="{{ $item->title }}" style="height: 250px; object-fit: cover;">
            <div class="card-body">
                <h5 class="fw-bold mb-0">{{ $item->title }}</h5>
                <p class="text-muted small mb-0">{{ $item->category }}</p>
            </div>
        </div>
    </div>
    @empty
    <div class="col-12 text-center py-5">
        <i class="bi bi-images display-1 text-muted opacity-25"></i>
        <h3 class="mt-4 text-muted">No photos found</h3>
    </div>
    @endforelse
</div>

<div class="mt-5 d-flex justify-content-center">
    {{ $gallery->links() }}
</div>
@endsection