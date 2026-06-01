@extends('layouts.app')

@section('content')
    <div class="pagetitle">
        <h1>Events Management</h1>
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                <li class="breadcrumb-item">CMS</li>
                <li class="breadcrumb-item active">Events</li>
            </ol>
        </nav>
    </div>

    <section class="section">
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h5 class="card-title">School Events</h5>
                            <a href="{{ route('cms.events.create') }}" class="btn btn-primary"><i class="bi bi-plus"></i>
                                Add New Event</a>
                        </div>

                        @if(session('success'))
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                {{ session('success') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        @endif

                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Title</th>
                                        <th>Date</th>
                                        <th>Location</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($events as $event)
                                        <tr>
                                            <td>{{ $event->title }}</td>
                                            <td>{{ $event->event_date ? \Carbon\Carbon::parse($event->event_date)->format('d M Y') : 'N/A' }}
                                            </td>
                                            <td>{{ $event->location }}</td>
                                            <td>
                                                <span
                                                    class="badge bg-{{ $event->status == 'published' ? 'success' : 'warning' }}">
                                                    {{ ucfirst($event->status ?? 'Draft') }}
                                                </span>
                                            </td>
                                            <td>
                                                <a href="{{ route('cms.events.edit', $event->id) }}"
                                                    class="btn btn-sm btn-info text-white"><i class="bi bi-pencil"></i></a>
                                                <form action="{{ route('cms.events.destroy', $event->id) }}" method="POST"
                                                    class="d-inline"
                                                    onsubmit="return confirm('Are you sure you want to delete this event?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-danger"><i
                                                            class="bi bi-trash"></i></button>
                                                </form>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5" class="text-center">No events found.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        <div class="mt-3">
                            {{ $events->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection