@extends('layouts.app')

@section('title', 'Page Not Found')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h4 class="mb-0">404 - Page Not Found</h4>
                </div>
                <div class="card-body text-center">
                    <div class="error-content">
                        <h1 class="display-1 text-muted">404</h1>
                        <h3 class="mb-3">Oops! Page Not Found</h3>
                        <p class="mb-4">The page you are looking for might have been removed, had its name changed, or is temporarily unavailable.</p>
                        <div class="d-flex justify-content-center gap-3">
                            <a href="{{ url('/') }}" class="btn btn-primary">
                                <i class="fas fa-home me-2"></i>Go to Home
                            </a>
                            <button onclick="history.back()" class="btn btn-secondary">
                                <i class="fas fa-arrow-left me-2"></i>Go Back
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.error-content h1 {
    font-size: 8rem;
    font-weight: 300;
}

@media (max-width: 768px) {
    .error-content h1 {
        font-size: 4rem;
    }
}
</style>
@endsection