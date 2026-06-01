@extends('layouts.app')

@section('content')
<div class="container">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h1 class="mb-0">All Schools (Central)</h1>
    <a href="{{ route('superadmin.dashboard') }}" class="btn btn-outline-secondary">Back to Dashboard</a>
  </div>

  <form method="GET" class="row g-2 mb-3" action="{{ route('superadmin.schools.all') }}">
    <div class="col-sm-6 col-md-4">
      <input type="text" name="q" value="{{ $search }}" class="form-control" placeholder="Search by name, city, admin name, email">
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
              <th>School Name</th>
              <th>City</th>
              <th>Admin Name</th>
              <th>Admin Email</th>
              <th>Tenant ID</th>
              <th>Status</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            @php($start = ($schools->currentPage()-1)*$schools->perPage())
            @foreach($schools as $i => $school)
            <tr>
              <td>{{ $start + $i + 1 }}</td>
              <td>{{ $school->schoolName }}</td>
              <td>{{ $school->schoolCity }}</td>
              <td>{{ $school->schoolAdminName }}</td>
              <td>{{ $school->schoolAdminEmail }}</td>
              <td>{{ $school->tenant_id }}</td>
              <td>{{ $school->status ?? 'active' }}</td>
              <td>
                <div class="d-inline-flex align-items-center flex-nowrap actions" style="gap:6px;">
                  @if(($school->status ?? 'active') === 'suspended')
                    <form method="POST" action="{{ route('saas.tenants.resume', $school->id) }}" class="d-inline">
                      @csrf
                      <button type="submit" class="btn btn-sm btn-success">Activate</button>
                    </form>
                  @else
                    <form method="POST" action="{{ route('saas.tenants.suspend', $school->id) }}" class="d-inline" onsubmit="return confirm('Block this school?');">
                      @csrf
                      <button type="submit" class="btn btn-sm btn-danger">Block</button>
                    </form>
                  @endif
                  @if(!empty($school->tenant_id))
                  <form method="POST" action="{{ route('superadmin.impersonate', ['tenant' => $school->tenant_id]) }}" class="d-inline">
                    @csrf
                    <button type="submit" class="btn btn-sm btn-primary">Impersonate</button>
                  </form>
                  @else
                    <span class="text-muted small">No tenant linked</span>
                  @endif
                </div>
              </td>
            </tr>
            @endforeach
          </tbody>
        </table>
      </div>
      <div>
        {{ $schools->appends(['q' => $search])->links() }}
      </div>
    </div>
  </div>
</div>
@endsection
