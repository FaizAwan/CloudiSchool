@extends('layouts.app')

@section('content')
<style>
    .blog-hero {
        background: linear-gradient(135deg, #0A6CFF 0%, #0056cc 100%);
        padding: 100px 0;
        color: white;
        text-align: center;
        margin-bottom: 60px;
    }

    .blog-card {
        border: none;
        border-radius: 20px;
        overflow: hidden;
        transition: all 0.3s;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
        height: 100%;
        background: white;
    }

    .blog-card:hover {
        transform: translateY(-10px);
        box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
    }

    .blog-img-wrapper {
        height: 220px;
        overflow: hidden;
    }

    .blog-img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.5s;
    }

    .blog-card:hover .blog-img {
        transform: scale(1.1);
    }

    .blog-content {
        padding: 25px;
    }

    .blog-category {
        font-size: 12px;
        font-weight: 700;
        color: #0A6CFF;
        text-transform: uppercase;
        margin-bottom: 10px;
        display: block;
    }

    .blog-title {
        font-weight: 800;
        font-size: 1.25rem;
        margin-bottom: 15px;
        line-height: 1.4;
        color: #1e293b;
    }

    .blog-excerpt {
        color: #64748b;
        font-size: 0.95rem;
        margin-bottom: 20px;
    }

    .blog-footer {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding-top: 20px;
        border-top: 1px solid #f1f5f9;
        font-size: 0.85rem;
        color: #94a3b8;
    }

    .read-more {
        font-weight: 700;
        color: #0A6CFF;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
    }

    .read-more i {
        margin-left: 5px;
        transition: transform 0.3s;
    }

    .read-more:hover i {
        transform: translateX(5px);
    }

    /* Premium Pagination */
    .pagination {
        gap: 10px;
        margin-top: 50px;
    }

    .page-item .page-link {
        border: none;
        padding: 12px 20px;
        border-radius: 12px !important;
        color: #64748b;
        font-weight: 600;
        transition: all 0.3s;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
    }

    .page-item.active .page-link {
        background: var(--primary);
        color: white;
        box-shadow: 0 10px 20px rgba(10, 108, 255, 0.2);
    }

    .page-item .page-link:hover:not(.active) {
        background: #f1f5f9;
        color: var(--primary);
        transform: translateY(-2px);
    }

    .pagination-info {
        color: #94a3b8;
        font-size: 0.9rem;
        margin-top: 15px;
        text-align: center;
    }
</style>

<div class="blog-hero">
    <div class="container">
        <h1 class="display-4 fw-bold mb-3">CloudiSchool Insights</h1>
        <p class="lead opacity-90 mx-auto" style="max-width: 700px;">Expert advice, tutorials, and latest trends in school management and educational technology.</p>
    </div>
</div>

<div class="container mb-5">
    <div class="row g-4">
        @foreach($blogs as $blog)
        <div class="col-lg-4 col-md-6">
            <div class="blog-card">
                <div class="blog-img-wrapper">
                    @if($blog->image)
                    <img src="{{ Str::startsWith($blog->image, 'http') ? $blog->image : asset('storage/' . $blog->image) }}" class="blog-img" alt="{{ $blog->title }}">
                    @else
                    <img src="https://images.unsplash.com/photo-1503676260728-1c00da094a0b?auto=format&fit=crop&w=800&q=80" class="blog-img" alt="{{ $blog->title }}">
                    @endif
                </div>
                <div class="blog-content">
                    <span class="blog-category">Education Technology</span>
                    <h3 class="blog-title">
                        <a href="{{ route('blogs.show', $blog->slug) }}" class="text-decoration-none text-dark">{{ $blog->title }}</a>
                    </h3>
                    <p class="blog-excerpt">{{ Str::limit(strip_tags($blog->content), 120) }}</p>
                    <div class="blog-footer">
                        <span><i class="bi bi-calendar3 me-1"></i> {{ $blog->created_at->format('M d, Y') }}</span>
                        <a href="{{ route('blogs.show', $blog->slug) }}" class="read-more">Read More <i class="bi bi-arrow-right"></i></a>
                    </div>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    <div class="mt-5 d-flex flex-column align-items-center">
        {{ $blogs->links() }}
        <div class="pagination-info mt-3">
            Showing {{ $blogs->firstItem() }} to {{ $blogs->lastItem() }} of {{ $blogs->total() }} insightful articles
        </div>
    </div>
</div>
@endsection