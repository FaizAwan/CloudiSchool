@extends('school_landing.layout')

@section('title', $item->title . ' | ' . $school->schoolName)
@section('page_title', $item->title)

<!-- SEO Metadata Overrides -->
@section('meta_description', Str::limit(strip_tags($item->content), 160))
@section('og_image', $item->image ?? $school->school_logo)

@section('content')
<div class="row justify-content-center" data-aos="fade-up">
    <div class="col-lg-9">
        <div class="bg-white p-5 rounded-4 shadow-sm">
            @if($item->image)
            <img src="{{ $item->image }}" class="img-fluid rounded-4 mb-5 shadow-sm" alt="{{ $item->title }}" style="width: 100%; max-height: 500px; object-fit: cover;">
            @endif

            <div class="d-flex align-items-center mb-4 pb-4 border-bottom">
                <div class="me-4">
                    <span class="text-muted small d-block">Published On</span>
                    <span class="fw-bold fs-5">{{ $item->created_at->format('d M, Y') }}</span>
                </div>
                @if($type == 'event')
                <div class="ms-auto text-end">
                    <span class="text-muted small d-block">Location</span>
                    <span class="fw-bold fs-5"><i class="bi bi-geo-alt text-primary"></i> {{ $item->location ?? 'School Campus' }}</span>
                </div>
                @endif
            </div>

            <div class="cms-content fs-5 leading-relaxed">
                {!! $item->content !!}
            </div>

            <div class="mt-5 pt-5 border-top">
                <div class="d-flex justify-content-between align-items-center">
                    <h5 class="fw-bold mb-0">Share this {{ $type }}:</h5>
                    <div class="d-flex gap-2">
                        <a href="https://facebook.com/sharer/sharer.php?u={{ url()->current() }}" class="btn btn-primary rounded-circle"><i class="bi bi-facebook"></i></a>
                        <a href="https://twitter.com/intent/tweet?url={{ url()->current() }}&text={{ urlencode($item->title) }}" class="btn btn-info text-white rounded-circle"><i class="bi bi-twitter-x"></i></a>
                        <a href="https://api.whatsapp.com/send?text={{ urlencode($item->title) }}%20{{ url()->current() }}" class="btn btn-success rounded-circle"><i class="bi bi-whatsapp"></i></a>
                    </div>
                </div>
            </div>
        </div>

        <div class="mt-4">
            <a href="{{ route('school.' . ($type == 'news' ? 'news' : ($type == 'event' ? 'events' : 'blogs')), $school->slug) }}" class="btn btn-link text-decoration-none p-0 text-dark fw-bold">
                <i class="bi bi-arrow-left me-2"></i> Back to {{ Str::plural($type) }}
            </a>
        </div>
    </div>
</div>

<style>
    .cms-content h2,
    .cms-content h3 {
        font-weight: 700;
        margin-top: 2rem;
        margin-bottom: 1rem;
        color: #0a6cff;
    }

    .cms-content p {
        color: #4b5563;
        margin-bottom: 1.5rem;
    }

    .leading-relaxed {
        line-height: 1.8;
    }
</style>
@endsection