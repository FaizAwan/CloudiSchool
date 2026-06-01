@extends('layouts.app')

@section('content')
<div class="container">
    <h1 class="mb-4">Create Package</h1>

    <div class="card">
        <div class="card-body">
            <form action="{{ route('superadmin.packages.store') }}" method="POST">
                @csrf
                <div class="mb-3">
                    <label class="form-label">Package Name</label>
                    <input type="text" name="name" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Price</label>
                    <input type="number" step="0.01" name="price" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Interval</label>
                    <select name="interval" class="form-select" required>
                        <option value="monthly">Monthly</option>
                        <option value="yearly">Yearly</option>
                    </select>
                </div>
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Max Students</label>
                        <input type="number" name="max_students" class="form-control" placeholder="0 for unlimited">
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Max Staff</label>
                        <input type="number" name="max_staff" class="form-control" placeholder="0 for unlimited">
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Max Storage (e.g. 1GB)</label>
                        <input type="text" name="max_storage_size" class="form-control" placeholder="1GB">
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label">Features (one per line)</label>
                    <textarea name="features" class="form-control" rows="5"></textarea>
                </div>
                <div class="mb-3 form-check">
                    <input type="checkbox" name="is_active" class="form-check-input" id="isActive" checked>
                    <label class="form-check-label" for="isActive">Active</label>
                </div>
                <button type="submit" class="btn btn-primary">Create Package</button>
            </form>
        </div>
    </div>
</div>
@endsection
