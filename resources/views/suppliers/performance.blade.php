@extends('layouts.app')

@section('title', 'Supplier Performance')

@push('styles')
<style>
    .performance-header {
        background: linear-gradient(135deg, #6f42c1 0%, #563d7c 100%);
        color: white;
        padding: 30px;
        border-radius: 12px;
        margin-bottom: 30px;
    }

    .supplier-info {
        background: #fff;
        border-radius: 8px;
        padding: 20px;
        margin-bottom: 25px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    }

    .performance-metrics {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 20px;
        margin-bottom: 30px;
    }

    .metric-card {
        background: #fff;
        border-radius: 12px;
        padding: 25px;
        text-align: center;
        box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        transition: transform 0.3s ease;
        position: relative;
        overflow: hidden;
    }

    .metric-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 4px;
        background: linear-gradient(90deg, #6f42c1, #007bff);
    }

    .metric-card:hover {
        transform: translateY(-5px);
    }

    .metric-value {
        font-size: 2.5rem;
        font-weight: bold;
        margin-bottom: 10px;
    }

    .metric-label {
        color: #6c757d;
        font-size: 0.9rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-bottom: 15px;
    }

    .metric-trend {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 5px;
        font-size: 0.85rem;
    }

    .trend-up { color: #28a745; }
    .trend-down { color: #dc3545; }
    .trend-neutral { color: #6c757d; }

    .score-excellent { color: #28a745; }
    .score-good { color: #20c997; }
    .score-average { color: #ffc107; }
    .score-poor { color: #fd7e14; }
    .score-critical { color: #dc3545; }

    .performance-section {
        background: #fff;
        border-radius: 12px;
        padding: 25px;
        margin-bottom: 25px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    }

    .section-title {
        color: #495057;
        font-weight: 600;
        margin-bottom: 20px;
        padding-bottom: 10px;
        border-bottom: 2px solid #e9ecef;
        display: flex;
        align-items: center;
    }

    .section-title i {
        margin-right: 10px;
        color: #6f42c1;
    }

    .progress-container {
        margin-bottom: 20px;
    }

    .progress-label {
        display: flex;
        justify-content: between;
        margin-bottom: 5px;
        font-size: 0.9rem;
    }

    .progress {
        height: 12px;
        border-radius: 6px;
        background: #e9ecef;
    }

    .progress-bar {
        border-radius: 6px;
    }

    .chart-container {
        background: #fff;
        border-radius: 12px;
        padding: 25px;
        margin-bottom: 25px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    }

    .timeline-item {
        display: flex;
        align-items: center;
        padding: 15px 0;
        border-bottom: 1px solid #f0f0f0;
    }

    .timeline-item:last-child {
        border-bottom: none;
    }

    .timeline-icon {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-right: 15px;
        color: white;
        font-size: 0.8rem;
    }

    .timeline-content {
        flex: 1;
    }

    .timeline-title {
        font-weight: 600;
        margin-bottom: 5px;
    }

    .timeline-desc {
        color: #6c757d;
        font-size: 0.9rem;
        margin-bottom: 5px;
    }

    .timeline-date {
        color: #6c757d;
        font-size: 0.8rem;
    }

    .benchmark-comparison {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 15px;
        margin-top: 20px;
    }

    .benchmark-item {
        text-align: center;
        padding: 15px;
        background: #f8f9fa;
        border-radius: 8px;
    }

    .benchmark-label {
        font-size: 0.8rem;
        color: #6c757d;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-bottom: 5px;
    }

    .benchmark-value {
        font-size: 1.2rem;
        font-weight: bold;
        color: #495057;
    }

    .rating-stars {
        color: #ffc107;
        margin-bottom: 10px;
    }

    .feedback-item {
        background: #f8f9fa;
        border-radius: 8px;
        padding: 15px;
        margin-bottom: 15px;
        border-left: 4px solid #6f42c1;
    }

    .feedback-header {
        display: flex;
        justify-content: between;
        align-items: center;
        margin-bottom: 10px;
    }

    .feedback-author {
        font-weight: 600;
        color: #495057;
    }

    .feedback-date {
        color: #6c757d;
        font-size: 0.85rem;
    }

    .feedback-text {
        color: #495057;
        font-size: 0.9rem;
        line-height: 1.5;
    }

    .action-buttons {
        display: flex;
        gap: 10px;
        flex-wrap: wrap;
    }

    @media (max-width: 768px) {
        .performance-metrics {
            grid-template-columns: repeat(2, 1fr);
        }

        .performance-header {
            padding: 20px;
            text-align: center;
        }

        .benchmark-comparison {
            grid-template-columns: repeat(2, 1fr);
        }

        .action-buttons {
            justify-content: center;
        }
    }

    @media (max-width: 480px) {
        .performance-metrics {
            grid-template-columns: 1fr;
        }

        .benchmark-comparison {
            grid-template-columns: 1fr;
        }
    }
</style>
@endpush

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="performance-header">
        <div class="row align-items-center">
            <div class="col-md-8">
                <h1 class="h3 mb-0">Performance Analytics</h1>
                <p class="mb-0 opacity-75">Comprehensive supplier performance metrics and analytics dashboard</p>
            </div>
            <div class="col-md-4 text-md-end">
                <div class="dropdown">
                    <button class="btn btn-light dropdown-toggle" type="button" data-bs-toggle="dropdown">
                        <i class="fas fa-calendar-alt"></i> Time Period
                    </button>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="#" onclick="changePeriod('7days')">Last 7 Days</a></li>
                        <li><a class="dropdown-item" href="#" onclick="changePeriod('30days')">Last 30 Days</a></li>
                        <li><a class="dropdown-item" href="#" onclick="changePeriod('3months')">Last 3 Months</a></li>
                        <li><a class="dropdown-item" href="#" onclick="changePeriod('6months')">Last 6 Months</a></li>
                        <li><a class="dropdown-item" href="#" onclick="changePeriod('1year')">Last Year</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item" href="#" onclick="changePeriod('custom')">Custom Range</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <!-- Current Supplier Info -->
    @if(isset($supplier))
    <div class="supplier-info">
        <div class="row align-items-center">
            <div class="col-md-8">
                <h5 class="mb-1">{{ $supplier->company_name ?? 'TechCorp Solutions Ltd.' }}</h5>
                <p class="text-muted mb-0">
                    <i class="fas fa-calendar-check"></i> Partnership since {{ $supplier->created_at ? $supplier->created_at->format('M Y') : 'Jan 2022' }} •
                    <i class="fas fa-shopping-cart"></i> {{ $supplier->total_orders ?? '156' }} Total Orders •
                    <i class="fas fa-dollar-sign"></i> ${{ number_format($supplier->total_value ?? 890450, 0) }} Total Value
                </p>
            </div>
            <div class="col-md-4 text-md-end">
                <a href="{{ route('suppliers.show', $supplier->id ?? 1) }}" class="btn btn-outline-primary btn-sm me-2">
                    <i class="fas fa-eye"></i> View Profile
                </a>
                <a href="{{ route('suppliers.index') }}" class="btn btn-secondary btn-sm">
                    <i class="fas fa-arrow-left"></i> All Suppliers
                </a>
            </div>
        </div>
    </div>
    @endif

    <!-- Performance Metrics -->
    <div class="performance-metrics">
        <div class="metric-card">
            <div class="metric-value score-excellent">{{ $overallScore ?? '87' }}%</div>
            <div class="metric-label">Overall Score</div>
            <div class="metric-trend trend-up">
                <i class="fas fa-arrow-up"></i> +3.2% from last period
            </div>
        </div>

        <div class="metric-card">
            <div class="metric-value score-excellent">{{ $qualityScore ?? '92' }}%</div>
            <div class="metric-label">Quality Score</div>
            <div class="metric-trend trend-up">
                <i class="fas fa-arrow-up"></i> +1.8% from last period
            </div>
        </div>

        <div class="metric-card">
            <div class="metric-value score-good">{{ $deliveryScore ?? '85' }}%</div>
            <div class="metric-label">Delivery Performance</div>
            <div class="metric-trend trend-down">
                <i class="fas fa-arrow-down"></i> -2.1% from last period
            </div>
        </div>

        <div class="metric-card">
            <div class="metric-value score-average">{{ $responseTime ?? '12' }}h</div>
            <div class="metric-label">Avg Response Time</div>
            <div class="metric-trend trend-up">
                <i class="fas fa-arrow-up"></i> +15min from last period
            </div>
        </div>

        <div class="metric-card">
            <div class="metric-value score-excellent">${{ number_format($costSavings ?? 24500, 0) }}</div>
            <div class="metric-label">Cost Savings</div>
            <div class="metric-trend trend-up">
                <i class="fas fa-arrow-up"></i> +$2,300 from last period
            </div>
        </div>

        <div class="metric-card">
            <div class="metric-value score-good">{{ $complianceScore ?? '94' }}%</div>
            <div class="metric-label">Compliance Score</div>
            <div class="metric-trend trend-neutral">
                <i class="fas fa-minus"></i> No change
            </div>
        </div>
    </div>

    <!-- Charts Section -->
    <div class="row">
        <div class="col-lg-8">
            <div class="chart-container">
                <h5 class="section-title">
                    <i class="fas fa-chart-line"></i>
                    Performance Trends
                </h5>
                <canvas id="performanceTrendsChart" height="300"></canvas>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="chart-container">
                <h5 class="section-title">
                    <i class="fas fa-chart-pie"></i>
                    Score Breakdown
                </h5>
                <canvas id="scoreBreakdownChart" height="300"></canvas>
            </div>
        </div>
    </div>

    <!-- Detailed Metrics -->
    <div class="row">
        <div class="col-lg-6">
            <div class="performance-section">
                <h5 class="section-title">
                    <i class="fas fa-tachometer-alt"></i>
                    Key Performance Indicators
                </h5>

                <div class="progress-container">
                    <div class="progress-label">
                        <span>On-Time Delivery</span>
                        <span>{{ $onTimeDelivery ?? '85' }}%</span>
                    </div>
                    <div class="progress">
                        <div class="progress-bar bg-success" style="width: {{ $onTimeDelivery ?? '85' }}%"></div>
                    </div>
                </div>

                <div class="progress-container">
                    <div class="progress-label">
                        <span>Quality Rating</span>
                        <span>{{ $qualityRating ?? '92' }}%</span>
                    </div>
                    <div class="progress">
                        <div class="progress-bar bg-primary" style="width: {{ $qualityRating ?? '92' }}%"></div>
                    </div>
                </div>

                <div class="progress-container">
                    <div class="progress-label">
                        <span>Cost Competitiveness</span>
                        <span>{{ $costCompetitiveness ?? '78' }}%</span>
                    </div>
                    <div class="progress">
                        <div class="progress-bar bg-info" style="width: {{ $costCompetitiveness ?? '78' }}%"></div>
                    </div>
                </div>

                <div class="progress-container">
                    <div class="progress-label">
                        <span>Communication</span>
                        <span>{{ $communicationScore ?? '88' }}%</span>
                    </div>
                    <div class="progress">
                        <div class="progress-bar bg-warning" style="width: {{ $communicationScore ?? '88' }}%"></div>
                    </div>
                </div>

                <div class="progress-container">
                    <div class="progress-label">
                        <span>Innovation</span>
                        <span>{{ $innovationScore ?? '72' }}%</span>
                    </div>
                    <div class="progress">
                        <div class="progress-bar bg-secondary" style="width: {{ $innovationScore ?? '72' }}%"></div>
                    </div>
                </div>

                <div class="progress-container">
                    <div class="progress-label">
                        <span>Sustainability</span>
                        <span>{{ $sustainabilityScore ?? '65' }}%</span>
                    </div>
                    <div class="progress">
                        <div class="progress-bar bg-success" style="width: {{ $sustainabilityScore ?? '65' }}%"></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="performance-section">
                <h5 class="section-title">
                    <i class="fas fa-balance-scale"></i>
                    Industry Benchmarks
                </h5>

                <p class="text-muted mb-3">Comparison with industry standards and top performers</p>

                <div class="benchmark-comparison">
                    <div class="benchmark-item">
                        <div class="benchmark-label">Our Supplier</div>
                        <div class="benchmark-value score-excellent">87%</div>
                    </div>
                    <div class="benchmark-item">
                        <div class="benchmark-label">Industry Average</div>
                        <div class="benchmark-value">74%</div>
                    </div>
                    <div class="benchmark-item">
                        <div class="benchmark-label">Top Quartile</div>
                        <div class="benchmark-value">91%</div>
                    </div>
                    <div class="benchmark-item">
                        <div class="benchmark-label">Best in Class</div>
                        <div class="benchmark-value">95%</div>
                    </div>
                </div>

                <div class="mt-4">
                    <h6 class="text-primary mb-3">Performance Categories</h6>
                    <div class="row">
                        <div class="col-6">
                            <div class="text-center mb-3">
                                <div class="h5 score-excellent">92%</div>
                                <div class="small text-muted">Quality</div>
                                <div class="small">Top 15%</div>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="text-center mb-3">
                                <div class="h5 score-good">85%</div>
                                <div class="small text-muted">Delivery</div>
                                <div class="small">Top 25%</div>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="text-center mb-3">
                                <div class="h5 score-average">78%</div>
                                <div class="small text-muted">Cost</div>
                                <div class="small">Top 40%</div>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="text-center mb-3">
                                <div class="h5 score-good">88%</div>
                                <div class="small text-muted">Service</div>
                                <div class="small">Top 20%</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Activity & Feedback -->
    <div class="row">
        <div class="col-lg-6">
            <div class="performance-section">
                <h5 class="section-title">
                    <i class="fas fa-history"></i>
                    Recent Performance Events
                </h5>

                <div class="timeline-item">
                    <div class="timeline-icon bg-success">
                        <i class="fas fa-check"></i>
                    </div>
                    <div class="timeline-content">
                        <div class="timeline-title">Early Delivery Achievement</div>
                        <div class="timeline-desc">Delivered PO-2024-001 2 days ahead of schedule</div>
                        <div class="timeline-date">Feb 12, 2024</div>
                    </div>
                </div>

                <div class="timeline-item">
                    <div class="timeline-icon bg-warning">
                        <i class="fas fa-exclamation-triangle"></i>
                    </div>
                    <div class="timeline-content">
                        <div class="timeline-title">Quality Issue Reported</div>
                        <div class="timeline-desc">Minor defect found in batch MC-ARM-2024, resolved quickly</div>
                        <div class="timeline-date">Feb 08, 2024</div>
                    </div>
                </div>

                <div class="timeline-item">
                    <div class="timeline-icon bg-info">
                        <i class="fas fa-lightbulb"></i>
                    </div>
                    <div class="timeline-content">
                        <div class="timeline-title">Process Improvement Suggestion</div>
                        <div class="timeline-desc">Proposed new packaging method to reduce shipping costs</div>
                        <div class="timeline-date">Feb 05, 2024</div>
                    </div>
                </div>

                <div class="timeline-item">
                    <div class="timeline-icon bg-primary">
                        <i class="fas fa-award"></i>
                    </div>
                    <div class="timeline-content">
                        <div class="timeline-title">Quality Certification Renewed</div>
                        <div class="timeline-desc">ISO 9001:2015 certification successfully renewed</div>
                        <div class="timeline-date">Jan 28, 2024</div>
                    </div>
                </div>

                <div class="timeline-item">
                    <div class="timeline-icon bg-success">
                        <i class="fas fa-handshake"></i>
                    </div>
                    <div class="timeline-content">
                        <div class="timeline-title">Contract Renewal</div>
                        <div class="timeline-desc">Successfully renewed annual supply agreement</div>
                        <div class="timeline-date">Jan 15, 2024</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="performance-section">
                <h5 class="section-title">
                    <i class="fas fa-comments"></i>
                    Recent Feedback & Reviews
                </h5>

                <div class="feedback-item">
                    <div class="feedback-header">
                        <span class="feedback-author">Sarah Johnson - Procurement Manager</span>
                        <span class="feedback-date">Feb 10, 2024</span>
                    </div>
                    <div class="rating-stars mb-2">
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                    </div>
                    <div class="feedback-text">
                        Excellent quality control and delivery performance. The team is very responsive to our requirements and consistently delivers on time.
                    </div>
                </div>

                <div class="feedback-item">
                    <div class="feedback-header">
                        <span class="feedback-author">Mike Chen - Quality Assurance</span>
                        <span class="feedback-date">Feb 05, 2024</span>
                    </div>
                    <div class="rating-stars mb-2">
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="far fa-star"></i>
                    </div>
                    <div class="feedback-text">
                        Minor quality issue with recent batch but was handled professionally. Quick resolution and compensation provided.
                    </div>
                </div>

                <div class="feedback-item">
                    <div class="feedback-header">
                        <span class="feedback-author">Lisa Brown - Operations Director</span>
                        <span class="feedback-date">Jan 30, 2024</span>
                    </div>
                    <div class="rating-stars mb-2">
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                    </div>
                    <div class="feedback-text">
                        Outstanding partnership! Their proactive communication and flexibility help us maintain smooth operations.
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Action Buttons -->
    <div class="performance-section">
        <h5 class="section-title">
            <i class="fas fa-tasks"></i>
            Performance Actions
        </h5>

        <div class="action-buttons">
            <button class="btn btn-primary" onclick="generateReport()">
                <i class="fas fa-file-alt"></i> Generate Performance Report
            </button>
            <button class="btn btn-success" onclick="scheduleReview()">
                <i class="fas fa-calendar-plus"></i> Schedule Performance Review
            </button>
            <button class="btn btn-info" onclick="sendFeedback()">
                <i class="fas fa-comment"></i> Send Feedback
            </button>
            <button class="btn btn-warning" onclick="requestImprovement()">
                <i class="fas fa-exclamation-triangle"></i> Request Improvement Plan
            </button>
            <button class="btn btn-secondary" onclick="exportData()">
                <i class="fas fa-download"></i> Export Performance Data
            </button>
        </div>
    </div>
</div>

<!-- Performance Report Modal -->
<div class="modal fade" id="reportModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Performance Report Options</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="reportForm">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Report Type</label>
                                <select class="form-select" name="report_type">
                                    <option value="comprehensive">Comprehensive Report</option>
                                    <option value="executive_summary">Executive Summary</option>
                                    <option value="detailed_metrics">Detailed Metrics</option>
                                    <option value="comparison">Benchmark Comparison</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Time Period</label>
                                <select class="form-select" name="time_period">
                                    <option value="last_month">Last Month</option>
                                    <option value="last_quarter">Last Quarter</option>
                                    <option value="last_6_months">Last 6 Months</option>
                                    <option value="last_year">Last Year</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Include Sections</label>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="sections[]" value="metrics" checked>
                                    <label class="form-check-label">Performance Metrics</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="sections[]" value="trends" checked>
                                    <label class="form-check-label">Trend Analysis</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="sections[]" value="benchmarks">
                                    <label class="form-check-label">Industry Benchmarks</label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="sections[]" value="feedback">
                                    <label class="form-check-label">Feedback & Reviews</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="sections[]" value="recommendations">
                                    <label class="form-check-label">Recommendations</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="sections[]" value="action_items">
                                    <label class="form-check-label">Action Items</label>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" onclick="submitReport()">Generate Report</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
$(document).ready(function() {
    initializeCharts();
});

function initializeCharts() {
    // Performance Trends Chart
    const trendsCtx = document.getElementById('performanceTrendsChart').getContext('2d');
    new Chart(trendsCtx, {
        type: 'line',
        data: {
            labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
            datasets: [
                {
                    label: 'Overall Performance',
                    data: [82, 84, 86, 85, 88, 87],
                    borderColor: '#6f42c1',
                    backgroundColor: 'rgba(111, 66, 193, 0.1)',
                    tension: 0.4
                },
                {
                    label: 'Quality Score',
                    data: [88, 90, 91, 89, 93, 92],
                    borderColor: '#28a745',
                    backgroundColor: 'rgba(40, 167, 69, 0.1)',
                    tension: 0.4
                },
                {
                    label: 'Delivery Performance',
                    data: [80, 82, 84, 86, 83, 85],
                    borderColor: '#007bff',
                    backgroundColor: 'rgba(0, 123, 255, 0.1)',
                    tension: 0.4
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true,
                    max: 100,
                    ticks: {
                        callback: function(value) {
                            return value + '%';
                        }
                    }
                }
            },
            plugins: {
                legend: {
                    position: 'top'
                }
            }
        }
    });

    // Score Breakdown Chart
    const breakdownCtx = document.getElementById('scoreBreakdownChart').getContext('2d');
    new Chart(breakdownCtx, {
        type: 'doughnut',
        data: {
            labels: ['Quality', 'Delivery', 'Cost', 'Service', 'Innovation'],
            datasets: [{
                data: [92, 85, 78, 88, 72],
                backgroundColor: [
                    '#28a745',
                    '#007bff',
                    '#ffc107',
                    '#17a2b8',
                    '#6c757d'
                ]
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom'
                }
            }
        }
    });
}

function changePeriod(period) {
    console.log('Changing period to:', period);
    if (period === 'custom') {
        // Show custom date range picker
        showCustomDatePicker();
    } else {
        // Refresh data for selected period
        refreshData(period);
    }
}

function showCustomDatePicker() {
    // Implementation for custom date range picker
    alert('Custom date range picker would be implemented here');
}

function refreshData(period) {
    // Simulate data refresh
    console.log('Refreshing data for period:', period);
    // In real implementation, this would make an AJAX call to update the dashboard
}

function generateReport() {
    $('#reportModal').modal('show');
}

function submitReport() {
    const formData = new FormData(document.getElementById('reportForm'));
    const reportType = formData.get('report_type');
    const timePeriod = formData.get('time_period');

    $('#reportModal').modal('hide');

    // Simulate report generation
    showToast(`Generating ${reportType} for ${timePeriod}...`, 'info');

    setTimeout(function() {
        showToast('Performance report generated successfully!', 'success');
    }, 2000);
}

function scheduleReview() {
    const nextReviewDate = new Date();
    nextReviewDate.setMonth(nextReviewDate.getMonth() + 3);

    const dateStr = nextReviewDate.toLocaleDateString();
    showToast(`Performance review scheduled for ${dateStr}`, 'success');
}

function sendFeedback() {
    // Implementation for feedback form
    alert('Feedback form would be implemented here');
}

function requestImprovement() {
    const result = confirm('This will send an improvement plan request to the supplier. Continue?');
    if (result) {
        showToast('Improvement plan request sent to supplier', 'success');
    }
}

function exportData() {
    showToast('Exporting performance data to Excel...', 'info');
    // Simulate export
    setTimeout(function() {
        showToast('Performance data exported successfully!', 'success');
    }, 1500);
}

function showToast(message, type = 'info') {
    // Create toast notification
    const toast = $(`
        <div class="toast align-items-center text-white bg-${type === 'success' ? 'success' : type === 'warning' ? 'warning' : type === 'danger' ? 'danger' : 'info'} border-0" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="d-flex">
                <div class="toast-body">${message}</div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
            </div>
        </div>
    `);

    // Add to toast container (create if doesn't exist)
    if (!$('#toastContainer').length) {
        $('body').append('<div id="toastContainer" class="toast-container position-fixed top-0 end-0 p-3" style="z-index: 1055;"></div>');
    }

    $('#toastContainer').append(toast);

    // Show toast
    const bsToast = new bootstrap.Toast(toast[0]);
    bsToast.show();

    // Remove from DOM after hiding
    toast.on('hidden.bs.toast', function() {
        $(this).remove();
    });
}
</script>
@endpush