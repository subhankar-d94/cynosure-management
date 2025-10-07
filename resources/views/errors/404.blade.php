@extends('layouts.error')

@section('title', 'Page Not Found')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card error-card">
            <div class="card-body text-center p-5">
                <div class="error-content">
                    <h1 class="display-1 text-primary mb-4">404</h1>
                    <h3 class="mb-3 text-dark">Oops! Page Not Found</h3>
                    <p class="mb-4 text-muted">The page you are looking for might have been removed, had its name changed, or is temporarily unavailable.</p>
                    <div class="d-flex justify-content-center gap-3 flex-wrap">
                        <a href="{{ url('/') }}" class="btn btn-primary btn-lg">
                            <i class="fas fa-home me-2"></i>Go to Home
                        </a>
                        <button onclick="history.back()" class="btn btn-outline-secondary btn-lg">
                            <i class="fas fa-arrow-left me-2"></i>Go Back
                        </button>
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
    text-shadow: 2px 2px 4px rgba(0,0,0,0.1);
}

@media (max-width: 768px) {
    .error-content h1 {
        font-size: 4rem;
    }
    .d-flex.gap-3 {
        flex-direction: column;
        gap: 1rem !important;
    }
}
</style>
@endsection