@extends('layouts.app')

@section('content')
<style>
    .blog-detail-header {
        padding: 80px 0 60px;
        background: #f8fafc;
    }

    .blog-meta {
        font-size: 0.95rem;
        color: #64748b;
        margin-bottom: 20px;
    }

    .blog-meta span {
        margin-right: 20px;
    }

    .blog-detail-title {
        font-size: 3rem;
        font-weight: 800;
        color: #1e293b;
        line-height: 1.2;
        margin-bottom: 30px;
    }

    .featured-image {
        width: 100%;
        height: 500px;
        object-fit: cover;
        border-radius: 30px;
        box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
        margin-bottom: 50px;
    }

    .blog-detail-content {
        font-size: 1.15rem;
        line-height: 1.8;
        color: #334155;
    }

    .blog-detail-content h2,
    .blog-detail-content h3 {
        color: #1e293b;
        font-weight: 800;
        margin-top: 40px;
        margin-bottom: 20px;
    }

    .sidebar-card {
        background: white;
        border-radius: 20px;
        padding: 30px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
        margin-bottom: 30px;
    }

    .related-blog-item {
        margin-bottom: 20px;
        padding-bottom: 20px;
        border-bottom: 1px solid #f1f5f9;
        display: flex;
        gap: 15px;
    }

    .related-img {
        width: 80px;
        height: 80px;
        border-radius: 12px;
        object-fit: cover;
    }

    .related-title {
        font-size: 1rem;
        font-weight: 700;
        color: #1e293b;
        line-height: 1.4;
    }
</style>

<div class="blog-detail-header">
    <div class="container">
        <div class="row justify-content-center text-center">
            <div class="col-lg-10">
                <div class="blog-meta">
                    <span><i class="bi bi-person me-1"></i> {{ $blog->author ?? 'Admin' }}</span>
                    <span><i class="bi bi-calendar3 me-1"></i> {{ $blog->created_at->format('M d, Y') }}</span>
                    <span><i class="bi bi-clock me-1"></i> 5 min read</span>
                </div>
                <h1 class="blog-detail-title">{{ $blog->title }}</h1>
            </div>
        </div>
    </div>
</div>

<div class="container mb-5">
    <div class="row">
        <div class="col-lg-8">
            @if($blog->image)
            <img src="{{ Str::startsWith($blog->image, 'http') ? $blog->image : asset('storage/' . $blog->image) }}" class="featured-image" alt="{{ $blog->title }}">
            @else
            <img src="https://images.unsplash.com/photo-1503676260728-1c00da094a0b?auto=format&fit=crop&w=1200&q=80" class="featured-image" alt="{{ $blog->title }}">
            @endif

            <article class="blog-detail-content">
                {!! $blog->content !!}
            </article>

            <div class="mt-5 pt-5 border-top">
                <div class="d-flex align-items-center justify-content-between">
                    <h5 class="fw-bold mb-0">Share this post:</h5>
                    <div class="d-flex gap-2">
                        <a href="#" class="btn btn-outline-primary btn-icon rounded-circle"><i class="bi bi-facebook"></i></a>
                        <a href="#" class="btn btn-outline-info btn-icon rounded-circle"><i class="bi bi-twitter-x"></i></a>
                        <a href="#" class="btn btn-outline-secondary btn-icon rounded-circle"><i class="bi bi-link-45deg"></i></a>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="sidebar-card">
                <h5 class="fw-bold mb-4">Related Articles</h5>
                @foreach($relatedBlogs as $related)
                <a href="{{ route('blogs.show', $related->slug) }}" class="text-decoration-none">
                    <div class="related-blog-item">
                        <img src="{{ Str::startsWith($related->image, 'http') ? $related->image : ( $related->image ? asset('storage/' . $related->image) : 'https://images.unsplash.com/photo-1503676260728-1c00da094a0b?auto=format&fit=crop&w=150&q=80') }}" class="related-img">
                        <div>
                            <h6 class="related-title mb-1">{{ $related->title }}</h6>
                            <small class="text-muted">{{ $related->created_at->format('M d, Y') }}</small>
                        </div>
                    </div>
                </a>
                @endforeach
            </div>

            <div class="sidebar-card text-center bg-primary text-white">
                <h4 class="fw-bold mb-3">Ready to Start?</h4>
                <p class="opacity-75 mb-4">Join CloudiSchool and manage your institution with ease.</p>
                <a href="{{ route('tenant.register.show') }}" class="btn btn-light w-100 fw-bold rounded-pill">Try Free Trial</a>
            </div>
        </div>
    </div>
</div>
@endsection