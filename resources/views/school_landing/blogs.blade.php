@extends('school_landing.layout')

@section('title', 'School Blogs | ' . $school->schoolName)
@section('page_title', 'EduBlogs & Articles')

@section('content')
<div class="row g-4" data-aos="fade-up">
    @forelse($blogs as $blog)
    <div class="col-lg-4 col-md-6">
        <div class="card card-premium overflow-hidden h-100">
            @if($blog->image)
            <img src="{{ $blog->image }}" class="card-img-top" alt="{{ $blog->title }}" style="height: 200px; object-fit: cover;">
            @endif
            <div class="card-body p-4">
                <div class="text-primary small fw-bold mb-2">{{ $blog->created_at->format('M d, Y') }}</div>
                <h4 class="fw-bold mb-3">{{ $blog->title }}</h4>
                <p class="text-muted small mb-4">{{ Str::limit(strip_tags($blog->content), 120) }}</p>
                <a href="{{ route('school.blog.show', [$school->slug, $blog->slug]) }}" class="btn btn-primary rounded-pill w-100 fw-bold shadow-sm text-white">Read More</a>
            </div>
        </div>
    </div>
    @empty
    <div class="col-12 text-center py-5">
        <i class="bi bi-journal-text display-1 text-muted opacity-25"></i>
        <h3 class="mt-4 text-muted">No blog posts found</h3>
    </div>
    @endforelse
</div>

<div class="mt-5 d-flex justify-content-center">
    {{ $blogs->links() }}
</div>
@endsection