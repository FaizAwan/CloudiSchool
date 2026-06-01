@extends('layouts.app')

@section('content')
<div class="container py-4">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h1 class="h4 mb-0">Tenants</h1>
  </div>

  @if (session('status'))
    <div class="alert alert-success">{{ session('status') }}</div>
  @endif
  @if (session('error'))
    <div class="alert alert-danger">{{ session('error') }}</div>
  @endif

  <div class="table-responsive">
    <table class="table table-striped align-middle">
      <thead>
        <tr>
          <th>ID</th>
          <th>Name</th>
          <th>Domain</th>
          <th>Status</th>
          <th>Stripe</th>
          <th class="text-end">Actions</th>
        </tr>
      </thead>
      <tbody>
        @foreach ($schools as $school)
        <tr>
          <td>{{ $school->id }}</td>
          <td>{{ $school->schoolName }}</td>
          <td>{{ $school->domain }}</td>
          <td>
            <span class="badge bg-{{ $school->status === 'active' ? 'success' : 'secondary' }}">{{ $school->status ?? 'unknown' }}</span>
          </td>
          <td>
            @if ($school->stripe_id)
              <span class="text-success">Linked</span>
            @else
              <span class="text-muted">Not linked</span>
            @endif
          </td>
          <td class="text-end">
            <div class="btn-group" role="group">
              <a class="btn btn-sm btn-outline-primary" href="{{ route('billing.plans', $school) }}">Plans</a>
              <a class="btn btn-sm btn-outline-secondary" href="{{ route('billing.portal', $school) }}">Manage Billing</a>
              <form class="d-inline" action="{{ route('saas.tenants.suspend', $school) }}" method="POST">
                @csrf
                <button class="btn btn-sm btn-outline-warning" {{ $school->status === 'suspended' ? 'disabled' : '' }}>Suspend</button>
              </form>
              <form class="d-inline" action="{{ route('saas.tenants.resume', $school) }}" method="POST">
                @csrf
                <button class="btn btn-sm btn-outline-success" {{ $school->status === 'active' ? 'disabled' : '' }}>Resume</button>
              </form>
            </div>
          </td>
        </tr>
        @endforeach
      </tbody>
    </table>
  </div>

  {{ $schools->links() }}
</div>
@endsection
