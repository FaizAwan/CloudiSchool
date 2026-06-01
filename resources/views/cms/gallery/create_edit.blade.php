@extends('layouts.app')

@section('content')
    <div class="pagetitle">
        <h1>{{ isset($item) ? 'Edit Gallery Image' : 'Add Image to Gallery' }}</h1>
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                <li class="breadcrumb-item">CMS</li>
                <li class="breadcrumb-item"><a href="{{ route('cms.gallery.index') }}">Gallery</a></li>
                <li class="breadcrumb-item active">{{ isset($item) ? 'Edit' : 'Add' }}</li>
            </ol>
        </nav>
    </div>

    <section class="section">
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Image Details</h5>

                        <form action="{{ isset($item) ? route('cms.gallery.update', $item->id) : route('cms.gallery.store') }}" 
                              method="POST" 
                              enctype="multipart/form-data">
                            @csrf
                            @if(isset($item))
                                @method('POST') {{-- Using POST since the route is defined as POST /update --}}
                            @endif

                            <div class="row mb-3">
                                <label for="title" class="col-sm-2 col-form-label">Image Title</label>
                                <div class="col-sm-10">
                                    <input type="text" name="title"
                                        class="form-control @error('title') is-invalid @enderror" 
                                        value="{{ old('title', $item->title ?? '') }}"
                                        required>
                                    @error('title')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="row mb-3">
                                <label for="category" class="col-sm-2 col-form-label">Category</label>
                                <div class="col-sm-10">
                                    <select name="category" class="form-select @error('category') is-invalid @enderror"
                                        required>
                                        <option value="">Select Category</option>
                                        @php
                                            $categories = ['Events', 'Campus', 'Academics', 'Sports', 'Others'];
                                            $currentCategory = old('category', $item->category ?? '');
                                        @endphp
                                        @foreach($categories as $cat)
                                            <option value="{{ $cat }}" {{ $currentCategory == $cat ? 'selected' : '' }}>{{ $cat }}</option>
                                        @endforeach
                                    </select>
                                    @error('category')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="row mb-3">
                                <label for="image" class="col-sm-2 col-form-label">
                                    {{ isset($item) ? 'Change Image' : 'Upload Image' }}
                                </label>
                                <div class="col-sm-10">
                                    @if(isset($item) && $item->image)
                                        <div class="mb-2">
                                            <img src="{{ asset('storage/' . $item->image) }}" alt="" style="height: 100px; border-radius: 8px;">
                                        </div>
                                    @endif
                                    <input type="file" name="image"
                                        class="form-control @error('image') is-invalid @enderror" 
                                        {{ isset($item) ? '' : 'required' }}>
                                    <small class="text-muted">Max size: 2MB. Format: JPG, PNG, WEBP.</small>
                                    @error('image')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-sm-10 offset-sm-2">
                                    <button type="submit" class="btn btn-primary">
                                        {{ isset($item) ? 'Update Gallery Item' : 'Add to Gallery' }}
                                    </button>
                                    <a href="{{ route('cms.gallery.index') }}" class="btn btn-secondary">Cancel</a>
                                </div>
                            </div>

                        </form>

                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
