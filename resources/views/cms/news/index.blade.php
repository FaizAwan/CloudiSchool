@extends('layouts.app')

@section('content')
    <div class="pagetitle">
        <h1>News Management</h1>
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                <li class="breadcrumb-item">CMS</li>
                <li class="breadcrumb-item active">News</li>
            </ol>
        </nav>
    </div>

    <section class="section">
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h5 class="card-title">School News Updates</h5>
                            <a href="{{ route('cms.news.create') }}" class="btn btn-primary"><i class="bi bi-plus"></i> Add
                                New News</a>
                        </div>

                        <table class="table datatable">
                            <thead>
                                <tr>
                                    <th>Title</th>
                                    <th>Status</th>
                                    <th>Date</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($news as $item)
                                    <tr>
                                        <td>{{ $item->title }}</td>
                                        <td><span
                                                class="badge bg-{{ $item->status == 'published' ? 'success' : 'warning' }}">{{ ucfirst($item->status) }}</span>
                                        </td>
                                        <td>{{ $item->created_at->format('d M Y') }}</td>
                                        <td>
                                            <a href="{{ route('cms.news.edit', $item->id) }}"
                                                class="btn btn-sm btn-info text-white"><i class="bi bi-pencil"></i></a>
                                            <form action="{{ route('cms.news.destroy', $item->id) }}" method="POST"
                                                class="d-inline" onsubmit="return confirm('Are you sure?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-danger"><i
                                                        class="bi bi-trash"></i></button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>

                        {{ $news->links() }}
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection