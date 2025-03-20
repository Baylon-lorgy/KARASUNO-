@extends('layouts.admindashboardlayout')

@section('title', 'Generate Reports')

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

    /* Notification Badge */
    .notification-badge {
        position: relative;
        display: inline-flex;
        align-items: center;
    }

    .notification-count {
        position: absolute;
        top: -8px;
        right: -8px;
        background: #dc3545;
        color: white;
        border-radius: 10px;
        padding: 2px 6px;
        font-size: 0.75rem;
        font-weight: 600;
        min-width: 20px;
        height: 20px;
        display: flex;
        align-items: center;
        justify-content: center;
        border: 2px solid white;
        transform-origin: center;
        animation: pulse 1.5s infinite;
    }

    @keyframes pulse {
        0% {
            transform: scale(1);
            box-shadow: 0 0 0 0 rgba(220, 53, 69, 0.4);
        }
        70% {
            transform: scale(1.1);
            box-shadow: 0 0 0 10px rgba(220, 53, 69, 0);
        }
        100% {
            transform: scale(1);
            box-shadow: 0 0 0 0 rgba(220, 53, 69, 0);
        }
    }

    .notification-shake {
        animation: shake 0.82s cubic-bezier(.36,.07,.19,.97) both;
    }

    @keyframes shake {
        10%, 90% { transform: translate3d(-1px, 0, 0); }
        20%, 80% { transform: translate3d(2px, 0, 0); }
        30%, 50%, 70% { transform: translate3d(-4px, 0, 0); }
        40%, 60% { transform: translate3d(4px, 0, 0); }
    }
</style>

<div class="container-fluid py-4">
    <!-- Error Handling for Session Messages -->
    @if(session('success') || session('error') || session('warning'))
        <div class="alert alert-{{ session('success') ? 'success' : (session('error') ? 'danger' : 'warning') }} alert-dismissible fade show" role="alert">
            <div class="d-flex align-items-center">
                <i class="bi bi-{{ session('success') ? 'check-circle-fill' : (session('error') ? 'exclamation-circle-fill' : 'exclamation-triangle-fill') }} me-2"></i>
                <span>{{ session('success') ?? session('error') ?? session('warning') }}</span>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- Water Usage Statistics -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card stats-card">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3 stat-item">
                            <div class="stat-value">
                                <div class="notification-badge">
                                    {{ $waterLevelHistory->count() ?? 0 }}
                                    <span class="notification-count" id="notificationCount" style="display: none;">0</span>
                                </div>
                            </div>
                            <div class="stat-label">Total Readings</div>
                        </div>
                        <div class="col-md-3 stat-item">
                            <div class="stat-value">{{ $waterLevelHistory->isEmpty() ? 'N/A' : number_format($waterLevelHistory->avg('level'), 1) . ' cm' }}</div>
                            <div class="stat-label">Average Level</div>
                        </div>
                        <div class="col-md-3 stat-item">
                            <div class="stat-value">{{ $distributionHistory->count() ?? 0 }}</div>
                            <div class="stat-label">Distributions</div>
                        </div>
                        <div class="col-md-3 stat-item">
                            <div class="stat-value">
                                @if($waterLevelHistory->isNotEmpty() && $waterLevelHistory->first() && $waterLevelHistory->first()->created_at)
                                    {{ $waterLevelHistory->first()->created_at->diffForHumans() }}
                                @else
                                    N/A
                                @endif
                            </div>
                            <div class="stat-label">Last Update</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Header Card with Enhanced Controls -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-body p-3">
                    <div class="row align-items-center mb-3">
                        <div class="col-md-8">
                            <div class="numbers">
                                <h5 class="font-weight-bolder mb-0">
                                    Generate Reports
                                    <span class="text-gradient text-primary">
                                        <i class="bi bi-file-earmark-text"></i>
                                    </span>
                                </h5>
                                <p class="text-sm mb-0 text-muted">Generate and analyze water level data reports</p>
                            </div>
                        </div>
                        <div class="col-md-4 text-md-end">
                            <div class="d-flex gap-2 justify-content-md-end">
                                <button type="submit" form="reportForm" 
                                    class="btn btn-primary btn-sm mb-0 d-flex align-items-center">
                                    <i class="bi bi-file-earmark-text me-2"></i>
                                    Generate Report
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Filter Controls -->
                    <div class="filter-controls">
                        <form id="reportForm" method="POST" action="{{ route('admin.reports.generate') }}" class="row g-3">
                            @csrf
                            <div class="col-md-3">
                                <label class="form-label mb-0">Report Type</label>
                                <select id="report_type" name="report_type" class="form-select form-select-sm">
                                    <option value="daily">Daily Report</option>
                                    <option value="weekly">Weekly Report</option>
                                    <option value="monthly">Monthly Report</option>
                                </select>
                                <x-input-error :messages="$errors->get('report_type')" class="mt-2" />
                            </div>

                            <div class="col-md-3">
                                <label class="form-label mb-0">Export Format</label>
                                <select id="format" name="format" class="form-select form-select-sm">
                                    <option value="pdf">PDF Document</option>
                                    <option value="csv">CSV Spreadsheet</option>
                                    <option value="excel">Excel Workbook</option>
                                </select>
                                <x-input-error :messages="$errors->get('format')" class="mt-2" />
                            </div>

                            <div class="col-md-3">
                                <label class="form-label mb-0">Start Date</label>
                                <input type="date" id="start_date" name="start_date" class="form-control form-control-sm" required>
                                <x-input-error :messages="$errors->get('start_date')" class="mt-2" />
                            </div>

                            <div class="col-md-3">
                                <label class="form-label mb-0">End Date</label>
                                <input type="date" id="end_date" name="end_date" class="form-control form-control-sm" required>
                                <x-input-error :messages="$errors->get('end_date')" class="mt-2" />
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Water Level History -->
    @if($waterLevelHistory->isNotEmpty())
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header pb-0">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="mb-0">Recent Water Level History</h6>
                            <p class="text-sm mb-0 text-muted">
                                <i class="bi bi-clock me-1"></i>
                                Latest water level readings
                            </p>
                        </div>
                    </div>
                </div>

                <div class="card-body px-0 pt-0 pb-2">
                    <div class="table-responsive p-0">
                        <table class="table align-items-center mb-0">
                            <thead>
                                <tr>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Time</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Level</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($waterLevelHistory->take(5) as $reading)
                                <tr>
                                    <td class="align-middle">
                                        <span class="text-secondary text-xs font-weight-bold">
                                            {{ $reading->created_at ? $reading->created_at->format('M d, Y H:i:s') : 'N/A' }}
                                        </span>
                                    </td>
                                    <td class="align-middle">
                                        <span class="text-secondary text-xs font-weight-bold">
                                            {{ $reading->level ? number_format($reading->level, 2) . ' cm' : 'N/A' }}
                                        </span>
                                    </td>
                                    <td class="align-middle">
                                        @if($reading->level !== null)
                                            <span class="badge badge-sm {{ $reading->level > config('app.alert_threshold', 100) ? 'bg-gradient-danger' : 'bg-gradient-success' }}">
                                                {{ $reading->level > config('app.alert_threshold', 100) ? 'High' : 'Normal' }}
                                            </span>
                                        @else
                                            <span class="badge badge-sm bg-gradient-secondary">Unknown</span>
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Set default dates
    const today = new Date();
    const startDate = document.getElementById('start_date');
    const endDate = document.getElementById('end_date');
    
    startDate.valueAsDate = new Date(today.getTime() - (7 * 24 * 60 * 60 * 1000)); // 7 days ago
    endDate.valueAsDate = today;

    // Date validation
    startDate.addEventListener('change', function() {
        endDate.min = this.value;
        if (endDate.value && endDate.value < this.value) {
            endDate.value = this.value;
        }
    });

    endDate.addEventListener('change', function() {
        startDate.max = this.value;
        if (startDate.value && startDate.value > this.value) {
            startDate.value = this.value;
        }
    });

    // Report type change handler
    document.getElementById('report_type').addEventListener('change', function() {
        const selectedType = this.value;
        const today = new Date();
        
        switch(selectedType) {
            case 'daily':
                startDate.valueAsDate = today;
                endDate.valueAsDate = today;
                break;
            case 'weekly':
                startDate.valueAsDate = new Date(today.getTime() - (7 * 24 * 60 * 60 * 1000));
                endDate.valueAsDate = today;
                break;
            case 'monthly':
                startDate.valueAsDate = new Date(today.getFullYear(), today.getMonth(), 1);
                endDate.valueAsDate = today;
                break;
        }
    });

    // Notification handling
    let notificationCount = 0;
    const notificationCountElement = document.getElementById('notificationCount');
    
    // Function to update notification count
    function updateNotificationCount(count) {
        notificationCount += count;
        if (notificationCount > 0) {
            notificationCountElement.style.display = 'flex';
            notificationCountElement.textContent = notificationCount > 9 ? '9+' : notificationCount;
            notificationCountElement.classList.add('notification-shake');
            setTimeout(() => {
                notificationCountElement.classList.remove('notification-shake');
            }, 820);
        } else {
            notificationCountElement.style.display = 'none';
        }
    }

    // Listen for new notifications via WebSocket
    window.Echo.private('notifications')
        .listen('NewNotification', (e) => {
            updateNotificationCount(1);
            // You can also show a toast notification here
            showNotificationToast(e.message);
        });

    // Function to show toast notification
    function showNotificationToast(message) {
        const toast = document.createElement('div');
        toast.className = 'alert alert-info alert-dismissible fade show position-fixed bottom-0 end-0 m-3';
        toast.style.zIndex = '1050';
        toast.innerHTML = `
            <div class="d-flex align-items-center">
                <i class="bi bi-bell-fill me-2"></i>
                <span>${message}</span>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        `;
        document.body.appendChild(toast);
        
        // Remove toast after 5 seconds
        setTimeout(() => {
            toast.remove();
        }, 5000);
    }

    // For testing: Simulate new notifications every 10 seconds
    // Remove this in production
    setInterval(() => {
        updateNotificationCount(1);
    }, 10000);
});
</script>
@endpush

@push('styles')
<style>
    /* Stats Card */
    .stats-card {
        background: rgba(255, 255, 255, 0.9);
        border-radius: 1rem;
        margin-bottom: 1.5rem;
    }
    
    .stat-item {
        padding: 1rem;
        text-align: center;
        border-right: 1px solid rgba(0,0,0,0.1);
    }
    
    .stat-item:last-child {
        border-right: none;
    }
    
    .stat-value {
        font-size: 1.5rem;
        font-weight: 600;
        color: var(--primary-color);
    }
    
    .stat-label {
        font-size: 0.875rem;
        color: var(--text-secondary);
    }

    /* Filter Controls */
    .filter-controls {
        background: rgba(255, 255, 255, 0.8);
        padding: 1rem;
        border-radius: 0.5rem;
        margin-top: 1rem;
    }

    /* Table Styles */
    .table > :not(caption) > * > * {
        padding: 1rem 1rem;
    }

    .badge {
        padding: 0.5em 0.75em;
    }

    /* Responsive Design */
    @media (max-width: 768px) {
        .stat-item {
            border-right: none;
            border-bottom: 1px solid rgba(0,0,0,0.1);
        }
        
        .stat-item:last-child {
            border-bottom: none;
        }
    }
</style>
@endpush 