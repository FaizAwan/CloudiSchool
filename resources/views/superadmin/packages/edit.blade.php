@extends('layouts.app')

@section('content')
<div class="container">
    <h1 class="mb-4">Edit Package</h1>

    <div class="card">
        <div class="card-body">
            <form action="{{ route('superadmin.packages.update', $package->id) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="mb-3">
                    <label class="form-label">Package Name</label>
                    <input type="text" name="name" class="form-control" value="{{ $package->name }}" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Price</label>
                    <input type="number" step="0.01" name="price" class="form-control" value="{{ $package->price }}" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Interval</label>
                    <select name="interval" class="form-select" required>
                        <option value="monthly" {{ $package->interval == 'monthly' ? 'selected' : '' }}>Monthly</option>
                        <option value="yearly" {{ $package->interval == 'yearly' ? 'selected' : '' }}>Yearly</option>
                    </select>
                </div>
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Max Students</label>
                        <input type="number" name="max_students" class="form-control" value="{{ $package->max_students }}" placeholder="0 for unlimited">
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Max Staff</label>
                        <input type="number" name="max_staff" class="form-control" value="{{ $package->max_staff }}" placeholder="0 for unlimited">
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Max Storage (e.g. 1GB)</label>
                        <input type="text" name="max_storage_size" class="form-control" value="{{ $package->max_storage_size }}" placeholder="1GB">
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label">Features (one per line)</label>
                    <textarea name="features" class="form-control" rows="5">{{ is_array($package->features) ? implode("\n", $package->features) : $package->features }}</textarea>
                </div>
                <div class="mb-3 form-check">
                    <input type="checkbox" name="is_active" class="form-check-input" id="isActive" {{ $package->is_active ? 'checked' : '' }}>
                    <label class="form-check-label" for="isActive">Active</label>
                </div>
                <button type="submit" class="btn btn-primary">Update Package</button>
            </form>
        </div>
    </div>
</div>
@endsection
