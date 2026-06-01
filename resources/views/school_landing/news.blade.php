@extends('school_landing.layout')

@section('title', 'Latest News | ' . $school->schoolName)
@section('page_title', 'School News & Updates')

@section('content')
<div class="row g-4" data-aos="fade-up">
    @forelse($news as $item)
    <div class="col-lg-4 col-md-6">
        <div class="card card-premium overflow-hidden h-100">
            <img src="{{ $item->image ?? 'https://images.unsplash.com/photo-1593642532400-2682810df593?w=800' }}" class="card-img-top" alt="{{ $item->title }}" style="height: 200px; object-fit: cover;">
            <div class="card-body p-4">
                <span class="badge bg-primary-subtle text-primary mb-3">{{ $item->created_at->format('M d, Y') }}</span>
                <h4 class="fw-bold mb-3">{{ $item->title }}</h4>
                <p class="text-muted small mb-4">{{ Str::limit(strip_tags($item->content), 120) }}</p>
                <a href="{{ route('school.news.show', [$school->slug, $item->slug]) }}" class="btn btn-outline-primary rounded-pill w-100 fw-bold">Read Full Story</a>
            </div>
        </div>
    </div>
    @empty
    <div class="col-12 text-center py-5">
        <i class="bi bi-newspaper display-1 text-muted opacity-25"></i>
        <h3 class="mt-4 text-muted">No news articles found</h3>
    </div>
    @endforelse
</div>

<div class="d-flex justify-content-center mt-5">
    {{ $news->links() }}
</div>
@endsection