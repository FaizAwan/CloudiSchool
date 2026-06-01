@extends('layouts.app')

@section('content')
    <div class="pagetitle">
        <h1>{{ isset($event) ? 'Edit Event' : 'Add New Event' }}</h1>
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                <li class="breadcrumb-item">CMS</li>
                <li class="breadcrumb-item"><a href="{{ route('cms.events.index') }}">Events</a></li>
                <li class="breadcrumb-item active">{{ isset($event) ? 'Edit' : 'Add' }}</li>
            </ol>
        </nav>
    </div>

    <section class="section">
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Event Details</h5>

                        <form
                            action="{{ isset($event) ? route('cms.events.update', $event->id) : route('cms.events.store') }}"
                            method="POST" enctype="multipart/form-data">
                            @csrf
                            @if(isset($event))
                                @method('POST') {{-- Using POST for update since some servers have issues with PUT --}}
                            @endif

                            <div class="row mb-3">
                                <label for="title" class="col-sm-2 col-form-label">Event Title</label>
                                <div class="col-sm-10">
                                    <input type="text" name="title"
                                        class="form-control @error('title') is-invalid @enderror"
                                        value="{{ old('title', $event->title ?? '') }}" required>
                                    @error('title')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="row mb-3">
                                <label for="event_date" class="col-sm-2 col-form-label">Event Date</label>
                                <div class="col-sm-10">
                                    <input type="date" name="event_date"
                                        class="form-control @error('event_date') is-invalid @enderror"
                                        value="{{ old('event_date', isset($event->event_date) ? \Carbon\Carbon::parse($event->event_date)->format('Y-m-d') : '') }}"
                                        required>
                                    @error('event_date')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="row mb-3">
                                <label for="location" class="col-sm-2 col-form-label">Location</label>
                                <div class="col-sm-10">
                                    <input type="text" name="location"
                                        class="form-control @error('location') is-invalid @enderror"
                                        value="{{ old('location', $event->location ?? '') }}" required>
                                    @error('location')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="row mb-3">
                                <label for="content" class="col-sm-2 col-form-label">Content</label>
                                <div class="col-sm-10">
                                    <textarea name="content" class="form-control @error('content') is-invalid @enderror"
                                        rows="5" required>{{ old('content', $event->content ?? '') }}</textarea>
                                    @error('content')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="row mb-3">
                                <label for="image" class="col-sm-2 col-form-label">Event Image</label>
                                <div class="col-sm-10">
                                    <input type="file" name="image"
                                        class="form-control @error('image') is-invalid @enderror">
                                    @if(isset($event) && $event->image)
                                        <div class="mt-2">
                                            <img src="{{ asset('storage/' . $event->image) }}" alt="Event Image"
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
                                        <option value="draft" {{ old('status', $event->status ?? '') == 'draft' ? 'selected' : '' }}>Draft</option>
                                        <option value="published" {{ old('status', $event->status ?? '') == 'published' ? 'selected' : '' }}>Published</option>
                                    </select>
                                    @error('status')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-sm-10 offset-sm-2">
                                    <button type="submit"
                                        class="btn btn-primary">{{ isset($event) ? 'Update Event' : 'Save Event' }}</button>
                                    <a href="{{ route('cms.events.index') }}" class="btn btn-secondary">Cancel</a>
                                </div>
                            </div>

                        </form>

                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection