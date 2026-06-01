@extends('layouts.app')

@section('content')
<div class="container my-4">
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center" style="background: #1488CC; color:#fff; font-weight:bold;">
            <h5 class="mb-0">Add New Principal Remark</h5>
            <a href="{{ route('principal-remarks.index') }}" class="btn btn-light btn-sm">
                <i class="bi bi-arrow-left"></i> Back to List
            </a>
        </div>
        <div class="card-body">
            @if ($errors->any())
                <div class="alert alert-danger">
                    <h6><i class="bi bi-exclamation-triangle"></i> Please fix the following errors:</h6>
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('principal-remarks.store') }}">
                @csrf
                
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="percentage_min" class="form-label">Minimum Percentage <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <input type="number" 
                                   class="form-control @error('percentage_min') is-invalid @enderror" 
                                   id="percentage_min" 
                                   name="percentage_min" 
                                   value="{{ old('percentage_min') }}" 
                                   step="0.01" 
                                   min="0" 
                                   max="100" 
                                   required>
                            <span class="input-group-text">%</span>
                        </div>
                        @error('percentage_min')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="col-md-6">
                        <label for="percentage_max" class="form-label">Maximum Percentage <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <input type="number" 
                                   class="form-control @error('percentage_max') is-invalid @enderror" 
                                   id="percentage_max" 
                                   name="percentage_max" 
                                   value="{{ old('percentage_max') }}" 
                                   step="0.01" 
                                   min="0" 
                                   max="100" 
                                   required>
                            <span class="input-group-text">%</span>
                        </div>
                        @error('percentage_max')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="mb-3">
                    <label for="remark" class="form-label">Principal Remark <span class="text-danger">*</span></label>
                    <textarea class="form-control @error('remark') is-invalid @enderror" 
                              id="remark" 
                              name="remark" 
                              rows="4" 
                              placeholder="Enter the principal's remark that will appear for students with marks in this percentage range..."
                              required>{{ old('remark') }}</textarea>
                    @error('remark')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    <div class="form-text">
                        This remark will be automatically displayed for students whose overall percentage falls within the specified range.
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="sort_order" class="form-label">Sort Order</label>
                        <input type="number" 
                               class="form-control @error('sort_order') is-invalid @enderror" 
                               id="sort_order" 
                               name="sort_order" 
                               value="{{ old('sort_order', 0) }}" 
                               min="0">
                        @error('sort_order')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <div class="form-text">Lower numbers appear first. Use 0 for default ordering.</div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="form-check mt-4">
                            <input class="form-check-input" 
                                   type="checkbox" 
                                   id="is_active" 
                                   name="is_active" 
                                   value="1" 
                                   {{ old('is_active', true) ? 'checked' : '' }}>
                            <label class="form-check-label" for="is_active">
                                Active
                            </label>
                            <div class="form-text">Only active remarks will be used in student reports.</div>
                        </div>
                    </div>
                </div>

                <div class="alert alert-info">
                    <i class="bi bi-lightbulb"></i>
                    <strong>Tips:</strong>
                    <ul class="mb-0">
                        <li>Percentage ranges should not overlap with existing active remarks.</li>
                        <li>Use clear, encouraging language appropriate for students and parents.</li>
                        <li>Consider including specific suggestions for improvement when applicable.</li>
                        <li>Preview how your remark will look in the student report.</li>
                    </ul>
                </div>

                <div class="d-flex justify-content-end gap-2">
                    <a href="{{ route('principal-remarks.index') }}" class="btn btn-secondary">
                        <i class="bi bi-x-circle"></i> Cancel
                    </a>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-check-circle"></i> Create Remark
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
// Validate percentage range
document.addEventListener('DOMContentLoaded', function() {
    const minInput = document.getElementById('percentage_min');
    const maxInput = document.getElementById('percentage_max');
    
    function validateRange() {
        const min = parseFloat(minInput.value);
        const max = parseFloat(maxInput.value);
        
        if (min && max && min > max) {
            maxInput.setCustomValidity('Maximum percentage must be greater than or equal to minimum percentage.');
        } else {
            maxInput.setCustomValidity('');
        }
    }
    
    minInput.addEventListener('input', validateRange);
    maxInput.addEventListener('input', validateRange);
});
</script>
@endsection
