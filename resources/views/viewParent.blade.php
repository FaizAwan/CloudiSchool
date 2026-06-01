@extends('layouts.app')

@section('content')
<style>
    .info-label {
        color: #64748b;
        font-size: 0.85rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        margin-bottom: 0.25rem;
    }

    .info-value {
        color: var(--accent-10);
        font-size: 1.1rem;
        font-weight: 700;
    }

    .status-badge {
        padding: 0.5rem 1rem;
        border-radius: 30px;
        font-weight: 700;
        font-size: 0.85rem;
        letter-spacing: 0.02em;
    }

    .badge-staff {
        background: rgba(14, 165, 233, 0.1);
        color: #0284c7;
        border: 1px solid rgba(14, 165, 233, 0.2);
    }

    .student-avatar {
        width: 45px;
        height: 45px;
        background: var(--secondary-30);
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-weight: 700;
        font-size: 1.2rem;
        margin-right: 1rem;
        box-shadow: 0 4px 10px rgba(14, 165, 233, 0.2);
    }
</style>

<div class="container-fluid px-4 py-5">
    <!-- Perfect Heading Section -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="page-title-box d-flex flex-column flex-md-row justify-content-between align-items-md-center">
                <div>
                    <h1 class="mb-0" style="font-family: 'Outfit', sans-serif; font-size: 1.75rem; font-weight: 800; color: var(--accent-10); text-transform: uppercase; letter-spacing: 12px;">
                        <i class="bi bi-person-badge-fill me-3"></i>P A R E N T &nbsp; &nbsp; P R O F I L E
                    </h1>
                    <p class="text-muted mb-0">Comprehensive pedagogical and administrative overview of the parent profile.</p>
                </div>
                <div class="d-flex gap-2 mt-3 mt-md-0">
                    <a href="{{ route('parents') }}" class="btn btn-cinematic px-4 py-2 shadow-sm fw-bold">
                        <i class="bi bi-arrow-left me-2"></i> RETURN TO DIRECTORY
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-xl-4 col-lg-5">
            <div class="card card-premium shadow-sm border-0 overflow-hidden">
                <div class="card-header-premium">
                    <i class="bi bi-person-badge me-2"></i> Primary Information
                </div>
                <div class="card-body p-4">
                    <div class="text-center mb-4">
                        <div class="mx-auto student-avatar mb-3" style="width: 80px; height: 80px; font-size: 2rem;">
                            {{ strtoupper(substr($parent->parentName, 0, 1)) }}
                        </div>
                        <h4 class="mb-1 fw-bold">{{ $parent->parentName }}</h4>
                        <span class="status-badge {{ $parent->is_commandercityschool_employee == 'Yes' ? 'badge-staff' : 'bg-light text-dark' }}">
                            {{ $parent->is_commandercityschool_employee == 'Yes' ? 'Staff Member' : 'Regular Parent' }}
                        </span>
                    </div>

                    <div class="row g-4 mt-2">
                        <div class="col-12">
                            <div class="info-label">Contact Number</div>
                            <div class="info-value"><i class="bi bi-telephone me-2 text-primary"></i>{{ $parent->phone ?: 'N/A' }}</div>
                        </div>
                        <div class="col-12">
                            <div class="info-label">Home Address</div>
                            <div class="info-value"><i class="bi bi-geo-alt me-2 text-danger"></i>{{ $parent->address ?: 'N/A' }}</div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card card-premium shadow-sm mt-4 border-0 overflow-hidden">
                <div class="card-header-premium">
                    <i class="bi bi-info-circle me-2"></i> Other Details
                </div>
                <div class="card-body p-4">
                    <div class="row g-3">
                        <div class="col-6">
                            <div class="info-label">Current Status</div>
                            <div class="fw-bold text-capitalize">{{ $parent->status ?: 'N/A' }}</div>
                        </div>
                        <div class="col-6">
                            <div class="info-label">City / Country</div>
                            <div class="fw-bold">{{ $parent->resident_city ?: 'N/A' }} / {{ $parent->resident_country ?: 'N/A' }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-8 col-lg-7">
            <div class="card card-premium shadow-sm border-0 overflow-hidden">
                <div class="card-header-premium d-flex justify-content-between align-items-center">
                    <div><i class="bi bi-people me-2"></i> Associated Students</div>
                    <span class="badge bg-white text-primary rounded-pill px-3">{{ count($students) }} Students</span>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-premium mb-0">
                            <thead>
                                <tr>
                                    <th>Student Name</th>
                                    <th>GR No.</th>
                                    <th>Class</th>
                                    <th class="text-center">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($students as $student)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="student-avatar">
                                                {{ strtoupper(substr($student->studentName, 0, 1)) }}
                                            </div>
                                            <div>
                                                <div class="fw-bold mb-0 text-dark">{{ $student->studentName }}</div>
                                                <small class="text-muted">Section: {{ $student->section ?: 'A' }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td><span class="badge bg-soft-primary text-primary px-2">{{ $student->grno }}</span></td>
                                    <td>{{ $student->className ?: 'N/A' }}</td>
                                    <td class="text-center">
                                        <a href="{{ request()->getBaseUrl() }}/viewStudent/{{ $student->id }}" class="btn btn-sm btn-light border">
                                            <i class="bi bi-arrow-right-short fs-5"></i>
                                        </a>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="4" class="text-center py-5">
                                        <div class="text-muted">
                                            <i class="bi bi-person-x display-4 d-block mb-3"></i>
                                            <p class="mb-0">No students are currently associated with this parent record.</p>
                                        </div>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
