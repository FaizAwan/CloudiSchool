@extends('layouts.app')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="mb-0">Superadmin Dashboard</h1>
    </div>

    <!-- Quick Actions -->
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card h-100">
                <div class="card-body d-flex flex-column text-center">
                    <h5 class="card-title">Packages</h5>
                    <p class="card-text">Manage subscription packages.</p>
                    <a href="{{ route('superadmin.packages.index') }}" class="btn btn-primary mt-auto">Manage Packages</a>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card h-100">
                <div class="card-body d-flex flex-column">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <h5 class="card-title mb-0">Impersonate Tenant</h5>
                        <form method="POST" action="{{ route('superadmin.leave_impersonation') }}" class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn-sm btn-outline-secondary">Leave</button>
                        </form>
                    </div>
                    <form method="POST" action="{{ route('superadmin.impersonate.start') }}" class="d-flex flex-column flex-grow-1">
                        @csrf
                        <label class="form-label">Select Tenant</label>
                        <select name="tenant_id" class="form-select mb-3" required>
                            @foreach($tenants as $tenant)
                            <option value="{{ $tenant->id }}">{{ $tenant->id }} @if($tenant->domains) ({{ $tenant->domains->pluck('domain')->implode(', ') }}) @endif</option>
                            @endforeach
                        </select>
                        <button type="submit" class="btn btn-primary mt-auto">Impersonate</button>
                    </form>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card h-100">
                <div class="card-body d-flex flex-column text-center">
                    <h5 class="card-title">Schools (Central)</h5>
                    <p class="card-text">Browse all schools centrally.</p>
                    <a href="{{ route('superadmin.schools.all') }}" class="btn btn-primary mt-auto">Open Schools</a>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card h-100">
                <div class="card-body d-flex flex-column text-center">
                    <h5 class="card-title">Audit Logs</h5>
                    <p class="card-text">Review recent cross-tenant events.</p>
                    <a href="{{ route('superadmin.audit') }}" class="btn btn-primary mt-auto">View Audit Logs</a>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card h-100">
                <div class="card-body d-flex flex-column text-center">
                    <h5 class="card-title">Blogs</h5>
                    <p class="card-text">Manage public blog posts.</p>
                    <a href="{{ route('superadmin.blogs.index') }}" class="btn btn-primary mt-auto">Manage Blogs</a>
                </div>
            </div>
        </div>
    </div>



    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card">
                <div class="card-body">
                    <h6 class="card-title">Total Tenants</h6>
                    <div class="display-6">{{ $totalTenants }}</div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card">
                <div class="card-body">
                    <h6 class="card-title">Total Students</h6>
                    <div class="display-6">{{ number_format($totals['students']) }}</div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card">
                <div class="card-body">
                    <h6 class="card-title">Total Teachers</h6>
                    <div class="display-6">{{ number_format($totals['teachers']) }}</div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card">
                <div class="card-body">
                    <h6 class="card-title">Total Fees Collected</h6>
                    <div class="display-6">₹ {{ number_format($totals['fees_amount'], 2) }}</div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card">
                <div class="card-body">
                    <h6 class="card-title">Premium Subscriptions</h6>
                    <div class="display-6">{{ $premiumTenants }}</div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card">
                <div class="card-body">
                    <h6 class="card-title">Gold Subscriptions</h6>
                    <div class="display-6">{{ $goldTenants }}</div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card">
                <div class="card-body">
                    <h6 class="card-title">Tenants Online</h6>
                    <div class="display-6">{{ $onlineTenants }}</div>
                </div>
            </div>
        </div>
    </div>

    <div class="card mb-4">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">Schools</h5>
                <div class="d-flex gap-2">
                    <a href="{{ route('superadmin.schools.all') }}" class="btn btn-outline-secondary">Open Full List</a>
                    <button id="refreshActivities" class="btn btn-outline-primary">Refresh Activities</button>
                </div>
            </div>
            <div class="table-responsive mt-3">
                @php($rows = isset($schoolsComplete) ? $schoolsComplete : $schools)
                <table class="table table-striped align-middle">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Name</th>
                            <th>Tenant ID</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($rows as $i => $school)
                        <tr>
                            <td>{{ $i + 1 }}</td>
                            <td>{{ is_array($school) ? ($school['name'] ?? '') : $school->schoolName }}</td>
                            <td>{{ is_array($school) ? ($school['tenant_id'] ?? '') : $school->tenant_id }}</td>
                            <td>
                                @php($tenantIdForRow = is_array($school) ? ($school['tenant_id'] ?? '') : $school->tenant_id)
                                @if(!empty($tenantIdForRow))
                                <form method="POST" action="{{ route('superadmin.impersonate', ['tenant' => $tenantIdForRow]) }}" class="d-inline">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-primary">Impersonate</button>
                                </form>
                                @else
                                <span class="text-muted">No tenant linked</span>
                                @endif
                                <form method="POST" action="{{ route('superadmin.leave_impersonation') }}" class="d-inline ms-2">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-secondary">Leave Impersonation</button>
                                </form>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="card mb-4">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-3">Tenants</h5>
                <a href="{{ route('superadmin.tenants.all') }}" class="btn btn-outline-secondary">Open Full List</a>
            </div>
            <div class="table-responsive">
                <table class="table table-striped align-middle">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Tenant ID</th>
                            <th>Domains</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($tenants as $j => $tenant)
                        <tr>
                            <td>{{ $j + 1 }}</td>
                            <td>{{ $tenant->id }}</td>
                            <td>{{ $tenant->domains ? $tenant->domains->pluck('domain')->implode(', ') : '' }}</td>
                            <td>
                                <form method="POST" action="{{ route('superadmin.impersonate', ['tenant' => $tenant->id]) }}" class="d-inline">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-primary">Impersonate</button>
                                </form>
                                @php($school = $schools->firstWhere('tenant_id', (string) $tenant->id))
                                @if($school)
                                <form method="POST" action="{{ route('saas.tenants.suspend', ['school' => $school->id]) }}" class="d-inline ms-2">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-outline-danger">Suspend</button>
                                </form>
                                <form method="POST" action="{{ route('saas.tenants.resume', ['school' => $school->id]) }}" class="d-inline ms-2">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-outline-success">Resume</button>
                                </form>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="card" id="activities">
        <div class="card-body">
            <h5 class="card-title">Recent Activities (last 7 days)</h5>
            <div class="table-responsive">
                <table class="table table-hover" id="activitiesTable">
                    <thead>
                        <tr>
                            <th>When</th>
                            <th>Tenant</th>
                            <th>Type</th>
                            <th>Label</th>
                            <th>Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if(isset($activitiesInitial))
                        @foreach($activitiesInitial as $a)
                        <tr>
                            <td>{{ \Carbon\Carbon::parse($a['at'])->toDateTimeString() }}</td>
                            <td>{{ $a['tenant'] }}</td>
                            <td>{{ $a['type'] }}</td>
                            <td>{{ $a['label'] ?? '' }}</td>
                            <td>{{ $a['type'] === 'fee' ? number_format((float) ($a['amount'] ?? 0), 2) : '' }}</td>
                        </tr>
                        @endforeach
                        @endif
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

@php($activitiesUrl = route('superadmin.activities'))
<script>
    document.getElementById('refreshActivities').addEventListener('click', async () => {
        const res = await fetch('{{ $activitiesUrl }}');
        const data = await res.json();
        const tbody = document.querySelector('#activitiesTable tbody');
        tbody.innerHTML = '';
        (data.activities || []).slice(0, 100).forEach(a => {
            const tr = document.createElement('tr');
            const amount = a.type === 'fee' ? (Number(a.amount).toFixed(2)) : '';
            tr.innerHTML = `
            <td>${new Date(a.at).toLocaleString()}</td>
            <td>${a.tenant}</td>
            <td>${a.type}</td>
            <td>${a.label || ''}</td>
            <td>${amount}</td>
        `;
            tbody.appendChild(tr);
        });
    });

    document.addEventListener('DOMContentLoaded', () => {
        document.getElementById('refreshActivities').click();
    });
</script>
@endsection