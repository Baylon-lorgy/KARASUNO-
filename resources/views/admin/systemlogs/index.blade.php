@extends('layouts.admindashboardlayout')

@section('title', 'System Logs - Rainwater Catch Basin')

@section('content')
<style>
    /* Existing styles remain unchanged */
    
    /* Water Usage Stats Card */
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

    /* Log Type Badges */
    .badge-water-level {
        background: linear-gradient(45deg, #2196F3, #00BCD4);
    }
    
    .badge-water-detection {
        background: linear-gradient(45deg, #4CAF50, #8BC34A);
    }
    
    .badge-system {
        background: linear-gradient(45deg, #9C27B0, #E91E63);
    }

    .badge-performance {
        background: linear-gradient(45deg, #FF9800, #FF5722);
    }

    /* Filter Controls */
    .filter-controls {
        background: rgba(255, 255, 255, 0.8);
        padding: 1rem;
        border-radius: 0.5rem;
        margin-bottom: 1rem;
    }

    .date-filter {
        display: flex;
        gap: 1rem;
        align-items: center;
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
                            <div class="stat-value" id="totalWaterUsage">0L</div>
                            <div class="stat-label">Total Water Usage</div>
                        </div>
                        <div class="col-md-3 stat-item">
                            <div class="stat-value" id="avgDailyUsage">0L</div>
                            <div class="stat-label">Average Daily Usage</div>
                        </div>
                        <div class="col-md-3 stat-item">
                            <div class="stat-value" id="detectionRate">0%</div>
                            <div class="stat-label">Water Detection Rate</div>
                        </div>
                        <div class="col-md-3 stat-item">
                            <div class="stat-value" id="systemUptime">0h</div>
                            <div class="stat-label">System Uptime</div>
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
                                    System Logs
                                    <span class="text-gradient text-primary">
                                        <i class="bi bi-journal-text"></i>
                                    </span>
                                </h5>
                                <p class="text-sm mb-0 text-muted">Monitor and analyze water usage and system performance</p>
                    </div>
                </div>
                        <div class="col-md-4 text-md-end">
                            <div class="d-flex gap-2 justify-content-md-end">
                                <div class="dropdown">
                                    <button class="btn btn-primary btn-sm mb-0 dropdown-toggle d-flex align-items-center" 
                                            type="button" 
                                            data-bs-toggle="dropdown">
                                        <i class="bi bi-download me-2"></i>
                                        Export Data
                                    </button>
                                    <ul class="dropdown-menu">
                                        <li>
                                            <a class="dropdown-item" href="#" onclick="exportData('selected')">
                                                <i class="bi bi-check2-square me-2"></i>
                                                Export Selected
                                            </a>
                                        </li>
                                        <li>
                                            <a class="dropdown-item" href="#" onclick="exportData('filtered')">
                                                <i class="bi bi-funnel me-2"></i>
                                                Export Filtered
                                            </a>
                                        </li>
                                        <li>
                                            <a class="dropdown-item" href="#" onclick="exportData('all')">
                                                <i class="bi bi-download me-2"></i>
                                                Export All
                                            </a>
                                        </li>
                                    </ul>
                </div>
            </div>
        </div>
                    </div>

                    <!-- Filter Controls -->
                    <div class="filter-controls">
                        <div class="row g-3">
                            <div class="col-md-4">
                                <div class="date-filter">
                                    <label class="form-label mb-0">Date Range:</label>
                                    <input type="date" class="form-control form-control-sm" id="startDate">
                                    <span>to</span>
                                    <input type="date" class="form-control form-control-sm" id="endDate">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Log Type</label>
                                <select class="form-select form-select-sm" id="logType">
                                    <option value="all">All Logs</option>
                                    <option value="water-level">Water Level</option>
                                    <option value="water-detection">Water Detection</option>
                                    <option value="performance">Performance</option>
                                    <option value="system">System</option>
                                </select>
                            </div>
                            <div class="col-md-4 text-end">
                                <button class="btn btn-primary btn-sm" onclick="applyFilters()">
                                    <i class="bi bi-funnel me-2"></i>Apply Filters
                                </button>
                                <button class="btn btn-outline-secondary btn-sm" onclick="resetFilters()">
                                    <i class="bi bi-x-circle me-2"></i>Reset
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Logs Timeline with Enhanced Categories -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header pb-0">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="mb-0">System Logs</h6>
                            <p class="text-sm mb-0 text-muted">
                                <i class="bi bi-clock me-1"></i>
                                Water usage and system events log
                            </p>
                        </div>
                        <div class="d-flex gap-2">
                            <button id="refreshButton" class="btn bg-gradient-success btn-sm mb-0">
                                <i class="bi bi-arrow-clockwise me-2"></i>
                                <span id="refreshText">Refresh</span>
                            </button>
                            <div class="selection-controls d-none">
                                <span class="badge bg-primary me-2" id="selectedCount">0 Selected</span>
                                <button class="btn btn-danger btn-sm mb-0" onclick="clearSelection()">
                                    <i class="bi bi-x-lg me-1"></i>
                                    Clear Selection
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card-body px-0 pt-0 pb-2">
                    @if(!isset($logs))
                        <div class="text-center py-5">
                            <div class="icon icon-shape bg-gradient-danger shadow text-center mx-auto mb-3">
                                <i class="bi bi-exclamation-triangle text-lg opacity-10"></i>
                            </div>
                            <h6 class="text-danger">Error Loading Logs</h6>
                            <p class="text-sm text-muted">There was an error loading the system logs. Please try refreshing the page.</p>
                        </div>
                    @elseif(empty($logs))
                        <div class="text-center py-5">
                            <div class="icon icon-shape bg-gradient-secondary shadow text-center mx-auto mb-3">
                                <i class="bi bi-journal-x text-lg opacity-10"></i>
                            </div>
                            <h6 class="text-secondary">No System Logs</h6>
                            <p class="text-sm text-muted">Water usage and system events will appear here</p>
                        </div>
                    @else
                        <div class="timeline-modern p-4" id="logsContainer">
                            <div class="d-flex align-items-center mb-3">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="selectAll" onchange="toggleAllLogs()">
                                    <label class="form-check-label text-sm" for="selectAll">Select All</label>
                                </div>
                            </div>
                                @foreach($logs as $log)
                            <div class="timeline-block" data-log-id="{{ $log['id'] ?? '' }}" data-log-type="{{ $log['type'] ?? 'system' }}">
                                <div class="timeline-checkbox">
                                    <div class="form-check">
                                        <input class="form-check-input log-checkbox" type="checkbox" value="{{ $log['id'] ?? '' }}">
                                    </div>
                                </div>
                                <div class="timeline-step bg-gradient-{{ 
                                    isset($log['type']) && $log['type'] === 'water-level' ? 'info' : 
                                    (isset($log['type']) && $log['type'] === 'water-detection' ? 'success' : 
                                    (isset($log['type']) && $log['type'] === 'performance' ? 'warning' : 'primary')) 
                                }}">
                                    <i class="bi bi-{{ 
                                        isset($log['type']) && $log['type'] === 'water-level' ? 'water' : 
                                        (isset($log['type']) && $log['type'] === 'water-detection' ? 'droplet' : 
                                        (isset($log['type']) && $log['type'] === 'performance' ? 'graph-up' : 'gear')) 
                                    }}"></i>
                                </div>
                                <div class="timeline-content">
                                    <div class="d-flex justify-content-between mb-1">
                                        <h6 class="text-dark text-sm font-weight-bold mb-0">{{ $log['message'] ?? 'No message' }}</h6>
                                        <div class="d-flex align-items-center">
                                            <span class="badge badge-{{ $log['type'] ?? 'system' }} me-2">
                                                {{ ucfirst($log['type'] ?? 'system') }}
                                            </span>
                                            <span class="text-muted text-xs" id="timestamp-{{ $log['id'] ?? '' }}">
                                                {{ isset($log['created_at']) ? \Carbon\Carbon::parse($log['created_at'])->format('M d, Y H:i:s') : 'No timestamp' }}
                                        </span>
                                        </div>
                                    </div>
                                    <p class="text-sm mb-0">
                                        {{ $log['details'] ?? 'No details available' }}
                                    </p>
                                    @if(isset($log['data']))
                                    <div class="mt-2">
                                        <small class="text-muted">Additional Data:</small>
                                        <div class="text-sm">
                                            @foreach($log['data'] as $key => $value)
                                            <span class="badge bg-light text-dark me-2">
                                                {{ $key }}: {{ $value }}
                                        </span>
                                @endforeach
                                        </div>
                                    </div>
                                    @endif
                                </div>
                    </div>
                            @endforeach
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    let retryCount = 0;
    const MAX_RETRIES = 3;
    let isOnline = navigator.onLine;
    
    // Network status monitoring
    window.addEventListener('online', handleOnline);
    window.addEventListener('offline', handleOffline);
    
    function handleOnline() {
        isOnline = true;
        document.getElementById('connectionStatus').style.display = 'none';
        showSystemStatus('Connection restored', 'success');
        if (document.getElementById('errorMessage').style.display === 'block') {
            retryLoading();
        }
    }
    
    function handleOffline() {
        isOnline = false;
        document.getElementById('connectionStatus').style.display = 'block';
        showSystemStatus('Connection lost', 'error');
    }
    
    function showSystemStatus(message, type = 'info') {
        const statusAlert = document.getElementById('systemStatusAlert');
        const statusMessage = document.getElementById('systemStatusMessage');
        statusMessage.textContent = message;
        statusAlert.className = `alert alert-${type} alert-dismissible fade show`;
        statusAlert.style.display = 'block';
        
        setTimeout(() => {
            statusAlert.style.display = 'none';
        }, 5000);
    }
    
    function retryLoading() {
        if (!isOnline) {
            showSystemStatus('No internet connection. Please check your connection and try again.', 'error');
            return;
        }
        
        if (retryCount >= MAX_RETRIES) {
            showSystemStatus('Maximum retry attempts reached. Please refresh the page.', 'error');
            return;
        }
        
        retryCount++;
        document.getElementById('loadingIndicator').style.display = 'block';
        document.getElementById('errorMessage').style.display = 'none';
        
        refreshData()
            .catch(error => {
                console.error('Error retrying:', error);
                showError('Failed to load logs. Please try again.');
            });
    }
    
    function showError(message) {
        document.getElementById('loadingIndicator').style.display = 'none';
        document.getElementById('errorMessage').style.display = 'block';
        document.getElementById('errorMessageText').textContent = message;
    }
    
    // Enhanced refresh data function with error handling
    async function refreshData() {
        if (!isOnline) {
            throw new Error('No internet connection');
        }
        
        try {
            document.getElementById('loadingIndicator').style.display = 'block';
            const response = await fetch(window.location.href);
            
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            
            const html = await response.text();
            const parser = new DOMParser();
            const doc = parser.parseFromString(html, 'text/html');
            
            const newLogsContainer = doc.querySelector('#logsContainer');
            if (!newLogsContainer) {
                throw new Error('Could not find logs container in response');
            }
            
            const currentContainer = document.querySelector('#logsContainer');
            if (currentContainer) {
                currentContainer.innerHTML = newLogsContainer.innerHTML;
                
                // Reapply animations and event listeners
                initializeTimelineAnimations();
                initializeSelectionSystem();
            }
            
            retryCount = 0; // Reset retry count on successful refresh
            document.getElementById('loadingIndicator').style.display = 'none';
            
        } catch (error) {
            console.error('Error refreshing data:', error);
            showError(error.message);
            throw error;
        }
    }
    
    // Initialize refresh button with error handling
    function initializeRefreshButton() {
        const refreshButton = document.getElementById('refreshButton');
        const refreshText = document.getElementById('refreshText');
        let refreshInterval;
        
        refreshButton.addEventListener('click', function() {
            if (refreshButton.classList.contains('refreshing')) {
                clearInterval(refreshInterval);
                refreshButton.classList.remove('refreshing');
                refreshText.textContent = 'Refresh';
            } else {
                if (!isOnline) {
                    showSystemStatus('Cannot start auto-refresh: No internet connection', 'error');
                    return;
                }
                
                refreshButton.classList.add('refreshing');
                refreshText.textContent = 'Stop Refresh';
                
                refreshData().catch(error => {
                    console.error('Error in refresh:', error);
                    refreshButton.classList.remove('refreshing');
                    refreshText.textContent = 'Refresh';
                    clearInterval(refreshInterval);
                });
                
                refreshInterval = setInterval(() => {
                    if (isOnline) {
                        refreshData().catch(error => {
                            console.error('Error in auto-refresh:', error);
                            clearInterval(refreshInterval);
                            refreshButton.classList.remove('refreshing');
                            refreshText.textContent = 'Refresh';
                        });
                    }
                }, 5000);
            }
        });
    }
    
    // Enhanced download function with error handling
    function downloadSelected() {
        try {
            const selectedLogs = [];
            const checkboxes = document.querySelectorAll('.log-checkbox:checked');
            
            if (checkboxes.length === 0) {
                showSystemStatus('Please select at least one log to download', 'warning');
                return;
            }
            
            checkboxes.forEach(checkbox => {
                const logBlock = document.querySelector(`[data-log-id="${checkbox.value}"]`);
                if (!logBlock) {
                    throw new Error(`Could not find log block for ID: ${checkbox.value}`);
                }
                
                const message = logBlock.querySelector('.text-sm')?.textContent ?? 'No message';
                const timestamp = logBlock.querySelector('[id^="timestamp-"]')?.textContent ?? 'No timestamp';
                const status = logBlock.querySelector('.badge')?.textContent.trim() ?? 'No status';
                
                selectedLogs.push({ timestamp, status, message });
            });
            
            // Create and download file with error handling
            try {
                const content = formatLogsContent(selectedLogs);
                downloadFile(content);
                showSystemStatus(`Successfully downloaded ${checkboxes.length} log(s)`, 'success');
            } catch (error) {
                console.error('Error creating download:', error);
                showSystemStatus('Error creating download file', 'error');
            }
            
        } catch (error) {
            console.error('Error in download process:', error);
            showSystemStatus('Error processing selected logs', 'error');
        }
    }
    
    function formatLogsContent(logs) {
        const header = `Botanical Garden System Logs\nExported on: ${new Date().toLocaleString()}\n\n`;
        const content = logs.map(log => 
            `[${log.timestamp}] ${log.status}\n${log.message}\n${'-'.repeat(50)}`
        ).join('\n\n');
        return header + content;
    }
    
    function downloadFile(content) {
        const blob = new Blob([content], { type: 'text/plain' });
        const url = window.URL.createObjectURL(blob);
        const a = document.createElement('a');
        a.href = url;
        a.download = `system_logs_${new Date().toISOString().slice(0,10)}.txt`;
        document.body.appendChild(a);
        a.click();
        window.URL.revokeObjectURL(url);
        document.body.removeChild(a);
    }
    
    // Initialize everything
    try {
        initializeRefreshButton();
        initializeSelectionSystem();
        initializeTimelineAnimations();
    } catch (error) {
        console.error('Error during initialization:', error);
        showSystemStatus('Error initializing page components', 'error');
    }
});

function initializeSelectionSystem() {
    const checkboxes = document.querySelectorAll('.log-checkbox');
    const selectionControls = document.querySelector('.selection-controls');
    const selectedCountBadge = document.getElementById('selectedCount');

    checkboxes.forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            const timelineBlock = this.closest('.timeline-block');
            if (this.checked) {
                timelineBlock.classList.add('selected');
            } else {
                timelineBlock.classList.remove('selected');
            }
            updateSelectionCount();
        });
    });

    function updateSelectionCount() {
        const selectedCount = document.querySelectorAll('.log-checkbox:checked').length;
        selectedCountBadge.textContent = `${selectedCount} Selected`;
        
        if (selectedCount > 0) {
            selectionControls.classList.add('show');
        } else {
            selectionControls.classList.remove('show');
        }

        // Update select all checkbox
        const selectAll = document.getElementById('selectAll');
        const totalCheckboxes = document.querySelectorAll('.log-checkbox').length;
        selectAll.checked = selectedCount === totalCheckboxes;
        selectAll.indeterminate = selectedCount > 0 && selectedCount < totalCheckboxes;
    }
}

function toggleAllLogs() {
    const selectAll = document.getElementById('selectAll');
    const checkboxes = document.querySelectorAll('.log-checkbox');
    const timelineBlocks = document.querySelectorAll('.timeline-block');
    
    checkboxes.forEach((checkbox, index) => {
        checkbox.checked = selectAll.checked;
        if (selectAll.checked) {
            timelineBlocks[index].classList.add('selected');
        } else {
            timelineBlocks[index].classList.remove('selected');
        }
    });
    
    const selectionControls = document.querySelector('.selection-controls');
    if (selectAll.checked) {
        selectionControls.classList.add('show');
    } else {
        selectionControls.classList.remove('show');
    }
    
    const selectedCountBadge = document.getElementById('selectedCount');
    selectedCountBadge.textContent = `${selectAll.checked ? checkboxes.length : 0} Selected`;
}

function clearSelection() {
    const checkboxes = document.querySelectorAll('.log-checkbox');
    const timelineBlocks = document.querySelectorAll('.timeline-block');
    const selectAll = document.getElementById('selectAll');
    
    checkboxes.forEach((checkbox, index) => {
        checkbox.checked = false;
        timelineBlocks[index].classList.remove('selected');
    });
    
    selectAll.checked = false;
    selectAll.indeterminate = false;
    
    document.querySelector('.selection-controls').classList.remove('show');
    document.getElementById('selectedCount').textContent = '0 Selected';
}

@keyframes fadeIn {
    from { opacity: 0; transform: translateY(10px); }
    to { opacity: 1; transform: translateY(0); }
}

// Add new functions for water usage logging
function updateWaterUsageStats() {
    fetch('/api/water-usage-stats')
        .then(response => response.json())
        .then(data => {
            document.getElementById('totalWaterUsage').textContent = `${data.totalUsage}L`;
            document.getElementById('avgDailyUsage').textContent = `${data.avgDailyUsage}L`;
            document.getElementById('detectionRate').textContent = `${data.detectionRate}%`;
            document.getElementById('systemUptime').textContent = `${data.uptime}h`;
        })
        .catch(error => console.error('Error fetching water usage stats:', error));
}

function exportData(type) {
    let endpoint = '/api/logs/export';
    let data = {
        type: type,
        startDate: document.getElementById('startDate').value,
        endDate: document.getElementById('endDate').value,
        logType: document.getElementById('logType').value
    };

    if (type === 'selected') {
        const selectedLogs = Array.from(document.querySelectorAll('.log-checkbox:checked'))
            .map(checkbox => checkbox.value);
        data.selectedLogs = selectedLogs;
    }

    fetch(endpoint, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify(data)
    })
    .then(response => response.blob())
    .then(blob => {
        const url = window.URL.createObjectURL(blob);
        const a = document.createElement('a');
        a.href = url;
        a.download = `water-usage-logs-${new Date().toISOString().slice(0,10)}.csv`;
        document.body.appendChild(a);
        a.click();
        window.URL.revokeObjectURL(url);
        document.body.removeChild(a);
    })
    .catch(error => {
        console.error('Error exporting data:', error);
        showAlert('Error exporting data. Please try again.', 'error');
    });
}

function applyFilters() {
    const filters = {
        startDate: document.getElementById('startDate').value,
        endDate: document.getElementById('endDate').value,
        logType: document.getElementById('logType').value
    };

    // Show loading state
    document.getElementById('logsContainer').style.opacity = '0.5';
    
    fetch('/api/logs/filter', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify(filters)
    })
    .then(response => response.json())
    .then(data => {
        // Update logs display
        updateLogsDisplay(data);
        document.getElementById('logsContainer').style.opacity = '1';
    })
    .catch(error => {
        console.error('Error applying filters:', error);
        showAlert('Error applying filters. Please try again.', 'error');
        document.getElementById('logsContainer').style.opacity = '1';
    });
}

function resetFilters() {
    document.getElementById('startDate').value = '';
    document.getElementById('endDate').value = '';
    document.getElementById('logType').value = 'all';
    applyFilters();
}

// Initialize components
window.addEventListener('load', function() {
    updateWaterUsageStats();
    initializeTimelineAnimations();
    initializeSelectionSystem();
    
    // Auto-refresh water usage stats every 5 minutes
    setInterval(updateWaterUsageStats, 300000);
});
</script>
@endpush 
@endsection 