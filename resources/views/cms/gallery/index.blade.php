@extends('layouts.app')

@section('content')
    <div class="pagetitle">
        <h1>Gallery Management</h1>
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                <li class="breadcrumb-item">CMS</li>
                <li class="breadcrumb-item active">Gallery</li>
            </ol>
        </nav>
    </div>

    <section class="section">
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h5 class="card-title">School Gallery</h5>
                            <a href="{{ route('cms.gallery.create') }}" class="btn btn-primary"><i class="bi bi-plus"></i>
                                Add New Image</a>
                        </div>

                        @if(session('success'))
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                {{ session('success') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        @endif

                        <div class="row row-cols-1 row-cols-md-3 row-cols-lg-4 g-4">
                            @forelse($gallery as $item)
                                <div class="col">
                                    <div class="card h-100 shadow-sm border-0">
                                        <img src="{{ asset('storage/' . $item->image) }}" class="card-img-top"
                                            alt="{{ $item->title }}" style="height: 200px; object-fit: cover;">
                                        <div class="card-body">
                                            <h6 class="card-title p-0 m-0 mb-1" style="font-size: 0.9rem;">{{ $item->title }}
                                            </h6>
                                            <span class="badge bg-secondary mb-2">{{ $item->category }}</span>
                                            <div class="mt-2 text-end">
                                                <a href="{{ route('cms.gallery.edit', $item->id) }}"
                                                    class="btn btn-sm btn-outline-primary"><i class="bi bi-pencil"></i></a>
                                                <form action="{{ route('cms.gallery.destroy', $item->id) }}" method="POST"
                                                    class="d-inline" onsubmit="return confirm('Delete this image?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-outline-danger"><i
                                                            class="bi bi-trash"></i></button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <div class="col-12 text-center py-5">
                                    <p>No images found in gallery.</p>
                                </div>
                            @endforelse
                        </div>

                        <div class="mt-4">
                            {{ $gallery->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection