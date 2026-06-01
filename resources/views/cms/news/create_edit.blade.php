@extends('layouts.app')

@section('content')
    <div class="pagetitle">
        <h1>{{ isset($news) ? 'Edit News' : 'Add New News' }}</h1>
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                <li class="breadcrumb-item">CMS</li>
                <li class="breadcrumb-item"><a href="{{ route('cms.news.index') }}">News</a></li>
                <li class="breadcrumb-item active">{{ isset($news) ? 'Edit' : 'Add' }}</li>
            </ol>
        </nav>
    </div>

    <section class="section">
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">News Details</h5>

                        <form action="{{ isset($news) ? route('cms.news.update', $news->id) : route('cms.news.store') }}"
                            method="POST" enctype="multipart/form-data">
                            @csrf
                            @if(isset($news))
                                @method('POST')
                            @endif

                            <div class="row mb-3">
                                <label for="title" class="col-sm-2 col-form-label">Title</label>
                                <div class="col-sm-10">
                                    <input type="text" name="title"
                                        class="form-control @error('title') is-invalid @enderror"
                                        value="{{ old('title', $news->title ?? '') }}" required>
                                    @error('title')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="row mb-3">
                                <label for="content" class="col-sm-2 col-form-label">Content</label>
                                <div class="col-sm-10">
                                    <textarea name="content" class="form-control @error('content') is-invalid @enderror"
                                        rows="10" required>{{ old('content', $news->content ?? '') }}</textarea>
                                    @error('content')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="row mb-3">
                                <label for="image" class="col-sm-2 col-form-label">Image</label>
                                <div class="col-sm-10">
                                    <input type="file" name="image"
                                        class="form-control @error('image') is-invalid @enderror">
                                    @if(isset($news) && $news->image)
                                        <div class="mt-2">
                                            <img src="{{ asset('storage/' . $news->image) }}" alt="News Image"
                                                style="max-width: 200px;">
                                        </div>
                                    @endif
                                    @error('image')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="row mb-3">
                                <label for="status" class="col-sm-2 col-form-label">Status</label>
                                <div class="col-sm-10">
                                    <select name="status" class="form-select @error('status') is-invalid @enderror"
                                        required>
                                        <option value="draft" {{ old('status', $news->status ?? '') == 'draft' ? 'selected' : '' }}>Draft</option>
                                        <option value="published" {{ old('status', $news->status ?? '') == 'published' ? 'selected' : '' }}>Published</option>
                                    </select>
                                    @error('status')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-sm-10 offset-sm-2">
                                    <button type="submit"
                                        class="btn btn-primary">{{ isset($news) ? 'Update News' : 'Save News' }}</button>
                                    <a href="{{ route('cms.news.index') }}" class="btn btn-secondary">Cancel</a>
                                </div>
                            </div>

                        </form>

                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection