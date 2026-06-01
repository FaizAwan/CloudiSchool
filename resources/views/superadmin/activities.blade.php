@extends('layouts.app')

@section('content')
<div class="container">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h1 class="mb-0">Audit Logs</h1>
    <a href="{{ route('superadmin.dashboard') }}" class="btn btn-outline-secondary">Back to Dashboard</a>
  </div>

  <form method="GET" class="row g-2 mb-3" action="{{ route('superadmin.audit') }}">
    <div class="col-auto">
      <label for="days" class="form-label">Days</label>
      <input type="number" min="1" max="30" step="1" id="days" name="days" value="{{ $days }}" class="form-control" />
    </div>
    <div class="col-auto align-self-end">
      <button type="submit" class="btn btn-primary">Refresh</button>
    </div>
  </form>

  <div class="card">
    <div class="card-body">
      <div class="table-responsive">
        <table class="table table-hover" id="auditTable">
          <thead>
            <tr>
              <th>When</th>
              <th>Tenant</th>
              <th>Type</th>
              <th>Label</th>
              <th>Amount</th>
            </tr>
          </thead>
          <tbody></tbody>
        </table>
      </div>
    </div>
  </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
  const tbody = document.querySelector('#auditTable tbody');
  const days = document.getElementById('days').value;
  const url = `{{ route('superadmin.activities') }}?days=${encodeURIComponent(days)}`;
  fetch(url)
    .then(r => r.json())
    .then(data => {
      tbody.innerHTML = '';
      (data.activities || []).forEach(a => {
        const tr = document.createElement('tr');
        const amount = (a.amount === null || a.amount === undefined) ? '' : a.amount;
        tr.innerHTML = `
          <td>${a.at}</td>
          <td>${a.tenant}</td>
          <td>${a.type}</td>
          <td>${a.label}</td>
          <td>${amount}</td>
        `;
        tbody.appendChild(tr);
      });
    })
    .catch(() => { tbody.innerHTML = '<tr><td colspan="5" class="text-muted">Failed to load</td></tr>'; });
});
</script>
@endsection

