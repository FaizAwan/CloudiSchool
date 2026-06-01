@extends('layouts.app')

@section('content')
<!-- Stitch Custom Styles -->
<style>
    :root {
        --stitch-navy: linear-gradient(135deg, #3b82f6 0%, #1e40af 50%, #172554 100%);
        --stitch-gold: linear-gradient(to bottom, #f3d078 0%, #c9a041 50%, #8a6e2a 100%);
        --stitch-glass: rgba(255, 255, 255, 0.95);
        --stitch-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
    }
    .card-stitch {
        background: var(--stitch-glass);
        border: 1px solid rgba(0,0,0,0.05);
        border-radius: 16px;
        box-shadow: var(--stitch-shadow);
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }
    .card-stitch:hover {
        transform: translateY(-5px);
        box-shadow: 0 20px 40px rgba(0,0,0,0.12);
    }
    .btn-stitch {
        background: var(--stitch-navy);
        color: white;
        border: none;
        box-shadow: 0 4px 15px rgba(30, 64, 175, 0.3);
        transition: all 0.3s ease;
    }
    .btn-stitch:hover {
        background: linear-gradient(135deg, #2563eb 0%, #172554 100%);
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(30, 64, 175, 0.4);
        color: white;
    }
    .badge-stitch {
        background: rgba(59, 130, 246, 0.1);
        color: #1e40af;
        border: 1px solid rgba(59, 130, 246, 0.2);
    }
    .pagetitle h1 {
        font-weight: 800;
        letter-spacing: -1px;
        background: var(--stitch-navy);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
    }
</style>

<div class="pagetitle mb-4">
    <h1>Blog Management</h1>
    <nav>
        <ol class="breadcrumb mt-2">
            <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
            <li class="breadcrumb-item">CMS</li>
            <li class="breadcrumb-item active">Blogs</li>
        </ol>
    </nav>
</div>

<div class="container-fluid p-0">
    <!-- AI Banner -->
    <div class="card border-0 shadow-sm rounded-4 mb-4" style="background: linear-gradient(135deg, #0f172a 0%, #1e40af 100%); overflow: hidden; position: relative;">
        <div class="position-absolute top-0 end-0 p-3 opacity-10">
            <i class="fas fa-robot fa-10x text-white"></i>
        </div>
        <div class="card-body p-4 text-white d-flex align-items-center justify-content-between position-relative z-1">
            <div class="d-flex align-items-center">
                <div class="bg-white bg-opacity-25 p-3 rounded-circle me-3">
                    <i class="fas fa-magic fa-2x"></i>
                </div>
                <div>
                    <h4 class="fw-bold mb-1">AI Blog Architect</h4>
                    <p class="mb-0 text-white-50">Generate SEO-optimized articles in seconds with GPT-4o.</p>
                </div>
            </div>
            <a href="{{ route('cms.blogs.create') }}" class="btn btn-light rounded-pill fw-bold text-primary px-4 py-2 shadow-lg">
                <i class="fas fa-plus me-2"></i> Create with AI
            </a>
        </div>
    </div>

    <!-- Blogs Grid -->
    <div class="row g-4">
        @forelse($blogs as $blog)
        <div class="col-md-6 col-lg-4">
            <div class="card card-stitch h-100 border-0">
                <!-- Status Badge -->
                <div class="position-absolute top-0 end-0 m-3 z-1">
                    <span class="badge rounded-pill px-3 py-2 fw-bold {{ $blog->status === 'published' ? 'bg-success text-white' : 'bg-warning text-dark' }} shadow-sm">
                        {{ ucfirst($blog->status) }}
                    </span>
                </div>

                <!-- Blog Image -->
                <div class="bg-light d-flex align-items-center justify-content-center overflow-hidden rounded-top-4" style="height: 220px;">
                    @if($blog->image)
                        <img src="{{ Str::startsWith($blog->image, 'http') ? $blog->image : asset('storage/' . $blog->image) }}" class="w-100 h-100 object-fit-cover" alt="{{ $blog->title }}" style="object-fit: cover;">
                    @else
                        <div class="text-center text-muted">
                            <i class="bi bi-card-image fs-1 opacity-50"></i>
                            <p class="small mb-0 mt-2">No Image</p>
                        </div>
                    @endif
                </div>

                <!-- Content -->
                <div class="card-body p-4 d-flex flex-column">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="badge badge-stitch rounded-pill px-2 py-1" style="font-size: 0.7rem;">BLOG</span>
                        <small class="text-muted fw-bold" style="font-size: 0.75rem;"><i class="bi bi-calendar3 me-1"></i> {{ $blog->created_at->format('M d, Y') }}</small>
                    </div>
                    
                    <h5 class="card-title fw-bold text-dark mb-3" style="min-height: 48px; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;">{{ $blog->title }}</h5>
                    
                    <p class="card-text text-muted small mb-4 flex-grow-1" style="display: -webkit-box; -webkit-line-clamp: 3; -webkit-box-orient: vertical; overflow: hidden;">
                        {{ Str::limit(strip_tags($blog->content), 120) }}
                    </p>
                    
                    <div class="d-flex justify-content-between align-items-center mt-auto border-top pt-3 border-light">
                        <div class="d-flex align-items-center">
                            <div class="bg-primary bg-opacity-10 rounded-circle d-flex align-items-center justify-content-center text-primary fw-bold me-2" style="width: 32px; height: 32px; font-size: 0.8rem;">
                                {{ substr($blog->author ?? 'A', 0, 1) }}
                            </div>
                            <small class="text-dark fw-bold">{{ $blog->author ?? 'Admin' }}</small>
                        </div>
                        <div class="dropdown">
                            <button class="btn btn-light btn-sm rounded-circle shadow-sm" type="button" data-bs-toggle="dropdown">
                                <i class="bi bi-three-dots-vertical text-muted"></i>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end border-0 shadow-lg rounded-3">
                                <li><a class="dropdown-item py-2" href="{{ route('cms.blogs.edit', $blog->id) }}"><i class="bi bi-pencil-square text-primary me-2"></i> Edit</a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <form action="{{ route('cms.blogs.destroy', $blog->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="dropdown-item py-2 text-danger" onclick="return confirm('Are you sure you want to delete this blog?');"><i class="bi bi-trash me-2"></i> Delete</button>
                                    </form>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @empty
        <div class="col-12">
            <div class="card border-0 shadow-sm rounded-4 text-center py-5">
                <div class="card-body">
                    <div class="bg-light rounded-circle d-inline-flex align-items-center justify-content-center mb-4" style="width: 100px; height: 100px;">
                        <i class="bi bi-newspaper fs-1 text-muted opacity-50"></i>
                    </div>
                    <h4 class="fw-bold text-dark mb-2">No Blogs Found</h4>
                    <p class="text-muted mb-4 mx-auto" style="max-width: 500px;">Your blog is currently empty. Use our AI tools to generate high-quality content and engage your audience.</p>
                    <a href="{{ route('cms.blogs.create') }}" class="btn btn-stitch rounded-pill px-5 py-3 fw-bold">
                        <i class="fas fa-magic me-2"></i> Create First Blog with AI
                    </a>
                </div>
            </div>
        </div>
        @endforelse
    </div>

    <!-- Pagination -->
    <div class="d-flex justify-content-center mt-5">
        {{ $blogs->links() }}
    </div>
</div>
@endsection