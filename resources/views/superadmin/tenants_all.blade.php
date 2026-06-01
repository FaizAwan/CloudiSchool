@extends('layouts.app')

@section('content')
<div class="container">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h1 class="mb-0">All Tenants (Central)</h1>
    <a href="{{ route('superadmin.dashboard') }}" class="btn btn-outline-secondary">Back to Dashboard</a>
  </div>

  <form method="GET" class="row g-2 mb-3" action="{{ route('superadmin.tenants.all') }}">
    <div class="col-sm-6 col-md-4">
      <input type="text" name="q" value="{{ $search }}" class="form-control" placeholder="Search by tenant id or name">
    </div>
    <div class="col-auto">
      <button type="submit" class="btn btn-primary">Search</button>
    </div>
  </form>

  <div class="card">
    <div class="card-body">
      <div class="table-responsive">
        <table class="table table-striped align-middle">
          <thead>
            <tr>
              <th>#</th>
              <th>Tenant ID</th>
              <th>Name</th>
              <th>Domains</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            @php($start = ($tenants->currentPage()-1)*$tenants->perPage())
            @foreach($tenants as $i => $tenant)
            <tr>
              <td>{{ $start + $i + 1 }}</td>
              <td>{{ $tenant->id }}</td>
              <td>{{ data_get($tenant->data, 'name', $tenant->id) }}</td>
              <td>{{ $tenant->domains ? $tenant->domains->pluck('domain')->implode(', ') : '' }}</td>
              <td>
                <form method="POST" action="{{ route('superadmin.impersonate', ['tenant' => $tenant->id]) }}" class="d-inline">
                  @csrf
                  <button type="submit" class="btn btn-sm btn-primary">Impersonate</button>
                </form>
              </td>
            </tr>
            @endforeach
          </tbody>
        </table>
      </div>
      <div>
        {{ $tenants->appends(['q' => $search])->links() }}
      </div>
    </div>
  </div>
</div>
@endsection
