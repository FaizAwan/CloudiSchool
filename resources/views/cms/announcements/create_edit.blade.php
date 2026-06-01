@extends('layouts.app')

@section('content')
    <div class="pagetitle">
        <h1>{{ isset($announcement) ? 'Edit Announcement' : 'Add New Announcement' }}</h1>
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                <li class="breadcrumb-item">CMS</li>
                <li class="breadcrumb-item"><a href="{{ route('cms.announcements.index') }}">Announcements</a></li>
                <li class="breadcrumb-item active">{{ isset($announcement) ? 'Edit' : 'Add' }}</li>
            </ol>
        </nav>
    </div>

    <section class="section">
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Announcement Details</h5>

                        <form
                            action="{{ isset($announcement) ? route('cms.announcements.update', $announcement->id) : route('cms.announcements.store') }}"
                            method="POST">
                            @csrf
                            @if(isset($announcement))
                                @method('PUT')
                            @endif

                            <div class="row mb-3">
                                <label for="title" class="col-sm-2 col-form-label">Title</label>
                                <div class="col-sm-10">
                                    <input type="text" name="title"
                                        class="form-control @error('title') is-invalid @enderror"
                                        value="{{ old('title', $announcement->title ?? '') }}" required>
                                    @error('title')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="row mb-3">
                                <label for="type" class="col-sm-2 col-form-label">Type</label>
                                <div class="col-sm-10">
                                    <select name="type" class="form-select @error('type') is-invalid @enderror" required>
                                        <option value="general" {{ old('type', $announcement->type ?? '') == 'general' ? 'selected' : '' }}>General Notice</option>
                                        <option value="exam" {{ old('type', $announcement->type ?? '') == 'exam' ? 'selected' : '' }}>Exam Related</option>
                                        <option value="holiday" {{ old('type', $announcement->type ?? '') == 'holiday' ? 'selected' : '' }}>Holiday Notice</option>
                                        <option value="urgent" {{ old('type', $announcement->type ?? '') == 'urgent' ? 'selected' : '' }}>Urgent / Alert</option>
                                    </select>
                                    @error('type')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="row mb-3">
                                <label for="content" class="col-sm-2 col-form-label">Content</label>
                                <div class="col-sm-10">
                                    <textarea name="content" class="form-control @error('content') is-invalid @enderror"
                                        rows="5" required>{{ old('content', $announcement->content ?? '') }}</textarea>
                                    @error('content')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="row mb-3">
                                <label for="status" class="col-sm-2 col-form-label">Status</label>
                                <div class="col-sm-10">
                                    <select name="status" class="form-select @error('status') is-invalid @enderror"
                                        required>
                                        <option value="active" {{ old('status', $announcement->status ?? '') == 'active' ? 'selected' : '' }}>Active (Visible)</option>
                                        <option value="inactive" {{ old('status', $announcement->status ?? '') == 'inactive' ? 'selected' : '' }}>Inactive (Hidden)</option>
                                    </select>
                                    @error('status')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-sm-10 offset-sm-2">
                                    <button type="submit"
                                        class="btn btn-primary">{{ isset($announcement) ? 'Update Announcement' : 'Save Announcement' }}</button>
                                    <a href="{{ route('cms.announcements.index') }}" class="btn btn-secondary">Cancel</a>
                                </div>
                            </div>

                        </form>

                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection