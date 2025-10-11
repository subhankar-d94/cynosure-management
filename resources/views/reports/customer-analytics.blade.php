@extends('layouts.app')

@section('title', 'Customer Analytics Report')

@section('content')
<div class="container-fluid px-4">
    <div class="row mb-4">
        <div class="col-12">
            <div class="bg-white rounded-lg shadow-sm border-0 p-4">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h1 class="h2 mb-1 text-dark font-weight-bold">📊 Customer Analytics</h1>
                        <p class="text-muted mb-0">Purchase behavior and lifetime value analysis</p>
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
                    <i class="fas fa-chart-line fa-4x text-warning mb-4"></i>
                    <h3 class="mb-3">Advanced Customer Analytics Under Development</h3>
                    <p class="text-muted mb-4">Detailed customer behavior analysis and lifetime value calculations coming soon.</p>
                    <div class="d-flex justify-content-center gap-2">
                        <a href="{{ route('reports.customers') }}" class="btn btn-primary">
                            <i class="fas fa-users"></i> Customer Overview
                        </a>
                        <a href="{{ route('reports.sales.customer') }}" class="btn btn-outline-primary">
                            <i class="fas fa-chart-bar"></i> Sales by Customer
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
function exportReport() {
    alert('Customer analytics export will be available soon.');
}
</script>
@endpush
@endsection