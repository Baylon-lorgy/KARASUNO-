@extends('layouts.admindashboardlayout')

@section('title', 'History - Rainwater Catch Basin')

@section('content')
<style>
    /* Background and Container Styles */
    .content-container {
        position: relative;
        z-index: 2;
    }

    /* Card Styles */
    .card {
        background: var(--color-mist, rgba(255, 255, 255, 0.95));
        backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 255, 255, 0.1);
        box-shadow: 0 0 20px rgba(0, 0, 0, 0.05);
        transition: all 0.3s ease;
    }

    .card:hover {
        transform: translateY(-2px);
        box-shadow: 
            0 12px 40px rgba(0, 0, 0, 0.15),
            0 3px 8px rgba(255, 255, 255, 0.1);
    }
</style>

@php
// Set default values for $dailyUsage if not provided
$dailyUsage = $dailyUsage ?? [
    'today' => 0,
    'yesterday' => 0,
    'week_avg' => 0,
    'month_avg' => 0
];

// Set default values for other variables
$distributionHistory = $distributionHistory ?? collect([]);
$waterLevelHistory = $waterLevelHistory ?? collect([]);
$alertHistory = $alertHistory ?? collect([]);
@endphp

<div class="container-fluid py-4">
    <!-- Header Card -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-body p-3">
                    <div class="row align-items-center">
                        <div class="col-md-8">
                            <div class="numbers">
                                <h5 class="font-weight-bolder mb-0">
                                    Sensor History
                                    <span class="text-gradient text-primary">
                                        <i class="bi bi-clock-history"></i>
                                    </span>
                                </h5>
                                <p class="text-sm mb-0 text-muted">Monitor your garden's water usage and sensor activities</p>
                            </div>
                        </div>
                        <div class="col-md-4 text-md-end mt-3 mt-md-0">
                            <div class="btn-group" role="group">
                                <button type="button" class="btn btn-outline-primary btn-sm active" data-period="day">
                                    <i class="bi bi-calendar-day me-1"></i> Day
                                </button>
                                <button type="button" class="btn btn-outline-primary btn-sm" data-period="week">
                                    <i class="bi bi-calendar-week me-1"></i> Week
                                </button>
                                <button type="button" class="btn btn-outline-primary btn-sm" data-period="month">
                                    <i class="bi bi-calendar-month me-1"></i> Month
                                </button>
                            </div>
                    </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Usage Statistics Cards -->
    <div class="row g-3 mb-4">
        <div class="col-xl-3 col-sm-6">
            <div class="card stat-card">
                <div class="card-body p-3">
                    <div class="d-flex align-items-center">
                        <div class="icon icon-shape bg-gradient-primary shadow text-white">
                            <i class="bi bi-droplet-fill"></i>
                        </div>
                        <div class="ms-3">
                            <p class="text-sm mb-0 text-capitalize">Today's Usage</p>
                            <h5 class="font-weight-bolder mb-0">
                                {{ $dailyUsage['today'] }}L
                                @if($dailyUsage['yesterday'] > 0)
                                    @php
                                        $change = ($dailyUsage['today'] - $dailyUsage['yesterday']) / $dailyUsage['yesterday'] * 100;
                                    @endphp
                                    <span class="text-{{ $change > 0 ? 'success' : 'danger' }} text-sm font-weight-bolder">
                                        {{ number_format(abs($change), 1) }}% {{ $change > 0 ? '↑' : '↓' }}
                        </span> 
                                @endif
                            </h5>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-sm-6">
            <div class="card stat-card">
                <div class="card-body p-3">
                    <div class="d-flex align-items-center">
                        <div class="icon icon-shape bg-gradient-success shadow text-white">
                            <i class="bi bi-calendar-check"></i>
                        </div>
                        <div class="ms-3">
                            <p class="text-sm mb-0 text-capitalize">Weekly Average</p>
                            <h5 class="font-weight-bolder mb-0">
                                {{ $dailyUsage['week_avg'] }}L
                                @if($dailyUsage['month_avg'] > 0)
                                    @php
                                        $weekChange = ($dailyUsage['week_avg'] - $dailyUsage['month_avg']) / $dailyUsage['month_avg'] * 100;
                                    @endphp
                                    <span class="text-{{ $weekChange > 0 ? 'success' : 'danger' }} text-sm font-weight-bolder">
                                        {{ number_format(abs($weekChange), 1) }}% {{ $weekChange > 0 ? '↑' : '↓' }}
                                    </span>
                                @endif
                            </h5>
                    </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-sm-6">
            <div class="card stat-card">
                <div class="card-body p-3">
                    <div class="d-flex align-items-center">
                        <div class="icon icon-shape bg-gradient-info shadow text-white">
                            <i class="bi bi-graph-up"></i>
                        </div>
                        <div class="ms-3">
                            <p class="text-sm mb-0 text-capitalize">Monthly Average</p>
                            <h5 class="font-weight-bolder mb-0">
                                {{ $dailyUsage['month_avg'] }}L
                                <span class="text-muted text-sm">
                                    <i class="bi bi-arrow-repeat"></i>
                                </span>
                            </h5>
                    </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-sm-6">
            <div class="card stat-card">
                <div class="card-body p-3">
                    <div class="d-flex align-items-center">
                        <div class="icon icon-shape bg-gradient-warning shadow text-white">
                            <i class="bi bi-moisture"></i>
                        </div>
                        <div class="ms-3">
                            <p class="text-sm mb-0 text-capitalize">Total Zones</p>
                            <h5 class="font-weight-bolder mb-0">
                                {{ $distributionHistory->unique('zone')->count() }}
                                <span class="text-success text-sm">Active</span>
                            </h5>
                    </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Section -->
    <div class="row mb-4">
        <div class="col-lg-8 col-md-12">
            <div class="card">
                <div class="card-header p-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="mb-0">Water Level History</h6>
                            <p class="text-sm mb-0 text-muted">
                                <i class="bi bi-clock me-1"></i>
                                Last updated: {{ now()->format('M d, Y H:i A') }}
                            </p>
                        </div>
                            <div class="btn-group">
                            <button class="btn btn-outline-primary btn-sm active" onclick="updateChart('hourly')">Hourly</button>
                            <button class="btn btn-outline-primary btn-sm" onclick="updateChart('daily')">Daily</button>
                            <button class="btn btn-outline-primary btn-sm" onclick="updateChart('weekly')">Weekly</button>
                        </div>
                    </div>
                </div>
                <div class="card-body p-3">
                    @if($waterLevelHistory->isEmpty())
                        <div class="text-center py-5">
                            <div class="icon icon-shape bg-gradient-secondary shadow-secondary mx-auto mb-3">
                                <i class="bi bi-water"></i>
                            </div>
                            <h6 class="text-secondary">No Water Level Data</h6>
                            <p class="text-sm text-muted">Water level history will appear here when available</p>
                        </div>
                    @else
                        <div class="chart-container">
                            <canvas id="waterLevelChart" height="300"></canvas>
                        </div>
                    @endif
                </div>
            </div>
        </div>
        <div class="col-lg-4 col-md-12">
            <div class="card h-100">
                <div class="card-header p-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <h6 class="mb-0">Zone Distribution</h6>
                        <span class="badge bg-primary">{{ count($distributionHistory) }} Records</span>
                    </div>
                </div>
                <div class="card-body p-3">
                    <div class="chart-container">
                        <canvas id="zoneDistributionChart" height="200"></canvas>
                    </div>
                    <div class="mt-4">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <span class="text-sm">Most Active Zone</span>
                            <span class="text-sm font-weight-bolder">Zone A</span>
                        </div>
                        <div class="progress">
                            <div class="progress-bar bg-gradient-primary" style="width: 80%"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Distribution History and Alerts -->
    <div class="row">
        <div class="col-lg-8 col-md-12">
            <div class="card mb-4">
                <div class="card-header p-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="mb-0">Distribution History</h6>
                            <p class="text-sm mb-0 text-muted">
                                <i class="bi bi-clock me-1"></i>
                                Recent water distribution records
                            </p>
                        </div>
                        <div class="d-flex gap-2">
                            <div class="search-box">
                                <input type="text" class="form-control form-control-sm" placeholder="Search records...">
                            </div>
                            <button class="btn btn-primary btn-sm">
                                <i class="bi bi-download me-1"></i> Export
                            </button>
                        </div>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive" style="max-height: 400px;">
                        <table class="table align-items-center mb-0">
                            <thead>
                                <tr>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Date/Time</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Zone</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Duration</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Water Used</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($distributionHistory as $record)
                                <tr>
                                    <td>
                                        <div class="d-flex px-3 py-2">
                                            <div>
                                                <h6 class="mb-0 text-sm">{{ Carbon\Carbon::parse($record['date'])->format('M d, Y') }}</h6>
                                                <p class="text-xs text-muted mb-0">{{ Carbon\Carbon::parse($record['date'])->format('h:i A') }}</p>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="text-sm font-weight-bold">{{ $record['zone'] }}</span>
                                    </td>
                                    <td>
                                        <span class="text-sm">{{ $record['duration'] }} min</span>
                                    </td>
                                    <td>
                                        <span class="text-sm">{{ $record['water_used'] }}L</span>
                                    </td>
                                    <td>
                                        <span class="badge bg-gradient-success">Completed</span>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-4 col-md-12">
            <div class="card mb-4">
                <div class="card-header p-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="mb-0">Recent Alerts</h6>
                            <p class="text-sm mb-0 text-muted">
                                <i class="bi bi-bell me-1"></i>
                                Latest system notifications
                            </p>
                        </div>
                        <span class="badge bg-danger">{{ count($alertHistory) }} New</span>
                    </div>
                </div>
                <div class="card-body p-3">
                    <div class="timeline timeline-one-side" data-timeline-axis-style="dashed" style="max-height: 400px; overflow-y: auto;">
                        @foreach($alertHistory as $alert)
                        <div class="timeline-block mb-3">
                            <span class="timeline-step bg-{{ $alert->type === 'warning' ? 'warning' : 'danger' }}">
                                <i class="bi bi-{{ $alert->type === 'warning' ? 'exclamation-triangle' : 'x-circle' }} text-white"></i>
                            </span>
                            <div class="timeline-content">
                                <h6 class="text-dark text-sm font-weight-bold mb-0">{{ $alert->message }}</h6>
                                <p class="text-secondary font-weight-bold text-xs mt-1 mb-0">
                                    {{ Carbon\Carbon::parse($alert->timestamp)->format('M d, Y h:i A') }}
                                </p>
                                <p class="text-sm mt-3 mb-0">
                                    <i class="bi bi-router me-1"></i>
                                    Device: {{ $alert->device->name }}
                                </p>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@push('styles')
<style>
/* Base Layout Styles */
.container-fluid {
    padding: 1.5rem;
}

/* Card Styles */
.card {
    margin-bottom: 0;
    border: none;
    border-radius: var(--border-radius);
    background: rgba(255, 255, 255, 0.9);
    backdrop-filter: blur(6px);
}

.card.shadow-sm {
    box-shadow: var(--shadow-sm) !important;
}

/* Card Header Styles */
.card-header {
    background: none;
    border-bottom: 1px solid rgba(0,0,0,0.05);
}

/* Card Body Styles */
.card-body {
    position: relative;
}

/* Responsive Adjustments */
@media (max-width: 768px) {
    .container-fluid {
        padding: 1rem;
    }
    
    .btn-group {
        width: 100%;
    }
    
    .btn-group .btn {
        flex: 1;
    }
}

/* Chart Container Adjustments */
.chart-container {
    position: relative;
    margin: auto;
    height: 300px;
    width: 100%;
    max-height: 400px;
}

/* Table Responsive Fixes */
.table-responsive {
    margin: 0;
    border-radius: var(--border-radius);
    overflow: hidden;
}

/* Timeline Adjustments */
.timeline {
    padding-right: 1rem;
    margin-bottom: 0;
}

/* Stat Card Styles */
.stat-card {
    border: none;
    border-radius: var(--border-radius);
    box-shadow: var(--shadow-sm);
    transition: var(--transition);
    background: rgba(255, 255, 255, 0.8);
    backdrop-filter: blur(6px);
}

.stat-card:hover {
    transform: translateY(-5px);
    box-shadow: var(--shadow-lg);
}

.icon-shape {
    width: 48px;
    height: 48px;
    background-position: 50%;
    border-radius: 0.75rem;
    display: flex;
    align-items: center;
    justify-content: center;
}

.icon-shape i {
    font-size: 1.5rem;
    opacity: 0.8;
}

/* Timeline Styles */
.timeline {
    margin: 0;
    padding: 0;
    list-style: none;
    position: relative;
}

.timeline:before {
    content: '';
    position: absolute;
    top: 0;
    left: 16px;
    height: 100%;
    width: 2px;
    background: rgba(0,0,0,0.1);
}

.timeline-block {
    display: flex;
    align-items: flex-start;
    margin-bottom: 1.5rem;
}

.timeline-step {
    width: 34px;
    height: 34px;
    border-radius: 50%;
    display: flex;
    justify-content: center;
    align-items: center;
    margin-right: 1rem;
    flex-shrink: 0;
    box-shadow: var(--shadow-sm);
}

.timeline-content {
    flex-grow: 1;
    background: rgba(255,255,255,0.8);
    backdrop-filter: blur(6px);
    padding: 1rem;
    border-radius: var(--border-radius);
    box-shadow: var(--shadow-sm);
}

/* Table Styles */
.table > :not(caption) > * > * {
    padding: 1rem 1rem;
    background-color: transparent;
    border-bottom-width: 1px;
    box-shadow: inset 0 0 0 9999px transparent;
}

.table tbody tr:hover {
    background: rgba(0,0,0,0.02);
}

/* Search Box Styles */
.search-box {
    position: relative;
}

.search-box input {
    padding-left: 2rem;
    background: rgba(255,255,255,0.8);
    backdrop-filter: blur(6px);
}

.search-box:before {
    content: '\F52A';
    font-family: bootstrap-icons;
    position: absolute;
    left: 0.75rem;
    top: 50%;
    transform: translateY(-50%);
    color: var(--text-secondary);
    font-size: 0.875rem;
}

/* Animation */
@keyframes fadeIn {
    from { opacity: 0; transform: translateY(10px); }
    to { opacity: 1; transform: translateY(0); }
}

.fade-in {
    animation: fadeIn 0.3s ease forwards;
}
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    @if(!$waterLevelHistory->isEmpty())
        initWaterLevelChart();
    @endif
    
    @if(!$distributionHistory->isEmpty())
        initZoneDistributionChart();
    @endif
    
    initializeSearchFilter();
    
    // Add fade-in animation to cards
    const cards = document.querySelectorAll('.card');
    cards.forEach((card, index) => {
        card.style.animation = `fadeIn 0.3s ease forwards ${index * 0.1}s`;
        card.style.opacity = '0';
    });
});

function initWaterLevelChart() {
    const ctx = document.getElementById('waterLevelChart').getContext('2d');
    const gradient = ctx.createLinearGradient(0, 0, 0, 300);
    gradient.addColorStop(0, 'rgba(94,114,228,0.2)');
    gradient.addColorStop(1, 'rgba(94,114,228,0.0)');

    new Chart(ctx, {
        type: 'line',
        data: {
            labels: @json($waterLevelHistory->pluck('timestamp')->map(function($timestamp) {
                return Carbon\Carbon::parse($timestamp)->format('H:i');
            })),
            datasets: [{
                label: 'Water Level',
                data: @json($waterLevelHistory->pluck('level')),
                borderColor: '#5e72e4',
                backgroundColor: gradient,
                fill: true,
                tension: 0.4,
                pointRadius: 0,
                pointHoverRadius: 4,
                borderWidth: 3
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                }
            },
            interaction: {
                intersect: false,
                mode: 'index'
            },
            scales: {
                y: {
                    grid: {
                        drawBorder: false,
                        display: true,
                        drawOnChartArea: true,
                        drawTicks: false,
                        borderDash: [5, 5]
                    },
                    ticks: {
                        display: true,
                        padding: 10,
                        color: '#9ca2b7'
                    }
                },
                x: {
                    grid: {
                        drawBorder: false,
                        display: false,
                        drawOnChartArea: false,
                        drawTicks: true
                    },
                    ticks: {
                        display: true,
                        color: '#9ca2b7',
                        padding: 10
                    }
                }
            }
        }
    });
}

function initZoneDistributionChart() {
    const ctx = document.getElementById('zoneDistributionChart').getContext('2d');
    
    new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: ['Zone A', 'Zone B', 'Zone C', 'Zone D'],
            datasets: [{
                data: [30, 25, 20, 25],
                backgroundColor: [
                    'rgba(94, 114, 228, 0.8)',
                    'rgba(45, 206, 137, 0.8)',
                    'rgba(251, 99, 64, 0.8)',
                    'rgba(17, 205, 239, 0.8)'
                ],
                borderWidth: 0
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        padding: 20,
                        boxWidth: 10
                    }
                }
            },
            cutout: '75%'
        }
    });
}

function updateChart(period) {
    // Add logic to update chart based on period
    console.log(`Updating chart for period: ${period}`);
}

function initializeSearchFilter() {
    const searchInput = document.querySelector('.search-box input');
    const tableRows = document.querySelectorAll('tbody tr');

    searchInput.addEventListener('input', function(e) {
        const searchTerm = e.target.value.toLowerCase();

        tableRows.forEach(row => {
            const text = row.textContent.toLowerCase();
            row.style.display = text.includes(searchTerm) ? '' : 'none';
        });
    });
}

// Period selector functionality
document.querySelectorAll('[data-period]').forEach(button => {
    button.addEventListener('click', function() {
        document.querySelectorAll('[data-period]').forEach(btn => btn.classList.remove('active'));
        this.classList.add('active');
        // Add logic to update data based on selected period
    });
});
</script>
@endpush 