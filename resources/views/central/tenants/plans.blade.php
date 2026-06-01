@extends('layouts.app')

@section('content')
<div class="container py-4">
  <h1 class="h4 mb-4">Choose a Plan for {{ $school->schoolName }}</h1>

  @if (session('error'))
    <div class="alert alert-danger">{{ session('error') }}</div>
  @endif

  <div class="row">
    <div class="col-md-4">
      <div class="card shadow-sm mb-3">
        <div class="card-body">
          <h5 class="card-title">Basic</h5>
          <p class="card-text">Good for small schools.</p>
          <form method="POST" action="{{ route('billing.checkout', $school) }}">
            @csrf
            <input type="hidden" name="price" value="{{ config('services.stripe_basic_price', 'price_basic_monthly') }}">
            <button type="submit" class="btn btn-primary w-100">Subscribe</button>
          </form>
        </div>
      </div>
    </div>

    {{-- Add more plans as needed --}}
  </div>
</div>
@endsection