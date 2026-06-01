@extends('layouts.app')

@section('content')
<div class="container my-4">
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center" style="background: #1488CC; color:#fff; font-weight:bold;">
            <h5 class="mb-0">Principal Remarks Management</h5>
            <a href="{{ route('principal-remarks.create') }}" class="btn btn-light btn-sm">
                <i class="bi bi-plus-circle"></i> Add New Remark
            </a>
        </div>
        <div class="card-body">
            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if (session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <div class="alert alert-info">
                <i class="bi bi-info-circle"></i>
                <strong>About Principal Remarks:</strong> These remarks will be automatically selected based on the student's overall percentage in their report card. You can define different remarks for different percentage ranges.
            </div>

            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead class="table-dark">
                        <tr>
                            <th>Percentage Range</th>
                            <th>Principal Remark</th>
                            <th>Sort Order</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($remarks as $remark)
                            <tr>
                                <td>
                                    <span class="badge bg-primary">
                                        {{ number_format($remark->percentage_min, 1) }}% - {{ number_format($remark->percentage_max, 1) }}%
                                    </span>
                                </td>
                                <td>
                                    <div class="remark-text" style="max-width: 400px;">
                                        {{ Str::limit($remark->remark, 100) }}
                                        @if(strlen($remark->remark) > 100)
                                            <button class="btn btn-sm btn-link p-0" onclick="showFullRemark('{{ addslashes($remark->remark) }}')" title="View Full Remark">
                                                <i class="bi bi-eye"></i>
                                            </button>
                                        @endif
                                    </div>
                                </td>
                                <td>
                                    <span class="badge bg-secondary">{{ $remark->sort_order }}</span>
                                </td>
                                <td>
                                    <button class="btn btn-sm status-toggle {{ $remark->is_active ? 'btn-success' : 'btn-secondary' }}" 
                                            onclick="toggleStatus({{ $remark->id }}, this)"
                                            data-id="{{ $remark->id }}">
                                        <i class="bi {{ $remark->is_active ? 'bi-check-circle' : 'bi-x-circle' }}"></i>
                                        {{ $remark->is_active ? 'Active' : 'Inactive' }}
                                    </button>
                                </td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <a href="{{ route('principal-remarks.edit', $remark->id) }}" 
                                           class="btn btn-outline-primary btn-sm" title="Edit">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        <button class="btn btn-outline-danger btn-sm" 
                                                onclick="deleteRemark({{ $remark->id }})" title="Delete">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center text-muted py-4">
                                    <i class="bi bi-inbox display-4 d-block mb-3"></i>
                                    No principal remarks found. <a href="{{ route('principal-remarks.create') }}">Create your first remark</a>.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Full Remark Modal -->
<div class="modal fade" id="remarkModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Principal Remark</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p id="fullRemarkText"></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Confirm Delete</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                Are you sure you want to delete this principal remark? This action cannot be undone.
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <form id="deleteForm" method="POST" style="display: inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Delete</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
function showFullRemark(remarkText) {
    document.getElementById('fullRemarkText').textContent = remarkText;
    new bootstrap.Modal(document.getElementById('remarkModal')).show();
}

function toggleStatus(id, button) {
    fetch(`/principal-remarks/${id}/toggle-status`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Content-Type': 'application/json',
        },
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            if (data.is_active) {
                button.className = 'btn btn-sm status-toggle btn-success';
                button.innerHTML = '<i class="bi bi-check-circle"></i> Active';
            } else {
                button.className = 'btn btn-sm status-toggle btn-secondary';
                button.innerHTML = '<i class="bi bi-x-circle"></i> Inactive';
            }
            const alert = document.createElement('div');
            alert.className = 'alert alert-success alert-dismissible fade show';
            alert.innerHTML = `
                ${data.message}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            `;
            document.querySelector('.card-body').prepend(alert);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error updating status. Please try again.');
    });
}

function deleteRemark(id) {
    document.getElementById('deleteForm').action = `/principal-remarks/${id}`;
    new bootstrap.Modal(document.getElementById('deleteModal')).show();
}

setTimeout(function() {
    document.querySelectorAll('.alert').forEach(function(alert) {
        if (alert.querySelector('.btn-close')) {
            alert.querySelector('.btn-close').click();
        }
    });
}, 5000);
</script>
@endsection
