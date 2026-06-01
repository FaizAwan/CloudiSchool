@extends('layouts.app')

@section('content')
<div class="pagetitle">
  <h1>Privacy Policy</h1>
</div>
<section class="section">
  <div class="card">
    <div class="card-body">
      <p class="mt-3">We respect your privacy. Data is used only to power the school’s operations (attendance, results, fees, etc.) and is not shared with third parties except as required by law.</p>
      <ul>
        <li>Access is role-based (superadmin, admin, teacher)</li>
        <li>Passwords are hashed; sessions secured</li>
        <li>Exports are limited to authorized users</li>
      </ul>
    </div>
  </div>
</section>
@endsection