@extends('layouts.app')

@section('content')
<div class="container py-5 text-center">
  <h1 class="h4 mb-3 text-danger">Limit Reached</h1>
  <p class="mb-0">You have reached the maximum allowed <strong>{{ $resource }}</strong> for your current plan.</p>
  @isset($limit)
    <p class="text-muted">Limit: {{ $limit }}</p>
  @endisset
  <p class="mt-3">Please contact your administrator to upgrade the plan.</p>
</div>
@endsection
