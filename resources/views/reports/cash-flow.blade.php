@extends('layouts.app')

@section('title', 'Cash Flow Report')

@section('content')
<div class="container-fluid px-4">
    <div class="row mb-4">
        <div class="col-12">
            <div class="bg-white rounded-lg shadow-sm border-0 p-4">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h1 class="h2 mb-1 text-dark font-weight-bold">💰 Cash Flow Report</h1>
                        <p class="text-muted mb-0">Cash in/out and liquidity analysis</p>
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
                    <i class="fas fa-water fa-4x text-info mb-4"></i>
                    <h3 class="mb-3">Cash Flow Report Under Development</h3>
                    <p class="text-muted mb-4">This cash flow analysis report is being developed to track cash movements and liquidity.</p>
                    <a href="{{ route('reports.financial') }}" class="btn btn-primary">
                        <i class="fas fa-chart-line"></i> View Financial Overview
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
function exportReport() {
    alert('Cash flow report export will be available soon.');
}
</script>
@endpush
@endsection