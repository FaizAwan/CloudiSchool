@extends('layouts.saas')

@section('title', 'Community')
@section('header_title', 'Community')
@section('header_subtitle', 'Connect with other educators and share best practices.')

@section('content')
<div class="text-center py-5">
    <i class="bi bi-chat-heart display-1 text-primary mb-4"></i>
    <h2 class="fw-bold">Coming Soon!</h2>
    <p class="lead text-muted mb-5">We are building a vibrant community forum for school admins and teachers to collaborate.</p>

    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card bg-light border-0 p-5 rounded-4">
                <h5 class="fw-bold mb-3">Get Early Access</h5>
                <form>
                    <div class="input-group">
                        <input type="email" class="form-control" placeholder="Your email address">
                        <button class="btn btn-primary" type="button">Notify Me</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection