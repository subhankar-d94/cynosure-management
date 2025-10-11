@extends('layouts.app')

@section('title', 'Delivery Performance Report')

@section('content')
<div class="container-fluid px-4">
    <div class="row mb-4">
        <div class="col-12">
            <div class="bg-white rounded-lg shadow-sm border-0 p-4">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h1 class="h2 mb-1 text-dark font-weight-bold">⚡ Delivery Performance</h1>
                        <p class="text-muted mb-0">On-time delivery and performance metrics</p>
                    </div>
                    <div class="d-flex gap-2">
                        <a href="{{ route('reports.index') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-arrow-left"></i> Back to Reports
                        </a>
                        <button class="btn btn-primary" onclick="exportReport()">
                            <i class="fas fa-download"></i> Export
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="bg-white rounded-lg shadow-sm border-0 p-5">
                <div class="text-center">
                    <i class="fas fa-tachometer-alt fa-4x text-warning mb-4"></i>
                    <h3 class="mb-3">Performance Analytics Under Development</h3>
                    <p class="text-muted mb-4">Delivery performance metrics and KPI tracking will be available soon.</p>
                    <a href="{{ route('reports.delivery') }}" class="btn btn-primary">
                        <i class="fas fa-truck"></i> View Delivery Reports
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
function exportReport() {
    alert('Performance report export will be available soon.');
}
</script>
@endpush
@endsection