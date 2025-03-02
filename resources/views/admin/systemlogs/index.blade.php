@extends('layouts.admindashboardlayout')

@section('title', 'System Logs - Rainwater Catch Basin')

@section('content')
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

    <!-- System Status Alert -->
    <div id="systemStatusAlert" style="display: none;" class="alert alert-info alert-dismissible fade show" role="alert">
        <div class="d-flex align-items-center">
            <i class="bi bi-info-circle-fill me-2"></i>
            <span id="systemStatusMessage"></span>
                    </div>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>

    <!-- Connection Status -->
    <div id="connectionStatus" style="display: none;" class="alert alert-warning alert-dismissible fade show" role="alert">
        <div class="d-flex align-items-center">
            <i class="bi bi-wifi-off me-2"></i>
            <span>Connection lost. Retrying...</span>
        </div>
                    </div>

    <!-- Header Card -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-body p-3">
                    <div class="row align-items-center">
                        <div class="col-md-8">
                            <div class="numbers">
                                <h5 class="font-weight-bolder mb-0">
                                    System Logs
                                    <span class="text-gradient text-primary">
                                        <i class="bi bi-journal-text"></i>
                                    </span>
                                </h5>
                                <p class="text-sm mb-0 text-muted">Monitor and manage all system activities</p>
                    </div>
                </div>
                        <div class="col-md-4 text-md-end mt-3 mt-md-0">
                            <div class="d-flex gap-2 justify-content-md-end">
                                <div class="dropdown">
                                    <button class="btn btn-primary btn-sm mb-0 dropdown-toggle d-flex align-items-center" 
                                            type="button" 
                                            data-bs-toggle="dropdown">
                                        <i class="bi bi-download me-2"></i>
                                        Download
                                    </button>
                                    <ul class="dropdown-menu">
                                        <li>
                                            <a class="dropdown-item" href="#" onclick="downloadSelected()">
                                                <i class="bi bi-check2-square me-2"></i>
                                                Download Selected
                                            </a>
                                        </li>
                                        <li>
                                            <a class="dropdown-item" href="{{ route('admin.logs.download') }}">
                                                <i class="bi bi-download me-2"></i>
                                                Download All
                                            </a>
                                        </li>
                                    </ul>
                </div>
                                <form action="{{ route('admin.logs.clear') }}" method="POST" class="d-inline">
                                    @csrf
                                    <button type="submit" 
                                            class="btn btn-danger btn-sm mb-0 d-flex align-items-center"
                                            onclick="return confirm('Are you sure you want to clear all logs?')">
                                        <i class="bi bi-trash me-2"></i>
                                        Clear
                                    </button>
                                </form>
            </div>
        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Logs Timeline -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header pb-0">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="mb-0">System Logs</h6>
                            <p class="text-sm mb-0 text-muted">
                                <i class="bi bi-clock me-1"></i>
                                System events and activities log
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
                            <div class="icon icon-shape bg-gradient-danger shadow text-center mx-auto mb-3" style="width: 50px; height: 50px; border-radius: 50%;">
                                <i class="bi bi-exclamation-triangle text-lg opacity-10" aria-hidden="true"></i>
                            </div>
                            <h6 class="text-danger">Error Loading Logs</h6>
                            <p class="text-sm text-muted">There was an error loading the system logs. Please try refreshing the page.</p>
                        </div>
                    @elseif(empty($logs))
                        <div class="text-center py-5">
                            <div class="icon icon-shape bg-gradient-secondary shadow text-center mx-auto mb-3" style="width: 50px; height: 50px; border-radius: 50%;">
                                <i class="bi bi-journal-x text-lg opacity-10" aria-hidden="true"></i>
                            </div>
                            <h6 class="text-secondary">No System Logs</h6>
                            <p class="text-sm text-muted">System events will appear here</p>
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
                                <div class="timeline-block" data-log-id="{{ $log['id'] ?? '' }}">
                                    <div class="timeline-checkbox">
                                        <div class="form-check">
                                            <input class="form-check-input log-checkbox" type="checkbox" value="{{ $log['id'] ?? '' }}">
                                        </div>
                                    </div>
                                    <div class="timeline-step bg-gradient-{{ isset($log['level']) && $log['level'] === 'error' ? 'danger' : (isset($log['level']) && $log['level'] === 'warning' ? 'warning' : 'success') }}">
                                        <i class="bi bi-{{ isset($log['level']) && $log['level'] === 'error' ? 'exclamation-circle' : (isset($log['level']) && $log['level'] === 'warning' ? 'exclamation-triangle' : 'info-circle') }}"></i>
                                    </div>
                                    <div class="timeline-content">
                                        <div class="d-flex justify-content-between mb-1">
                                            <h6 class="text-dark text-sm font-weight-bold mb-0">{{ $log['message'] ?? 'No message' }}</h6>
                                            <div class="d-flex align-items-center">
                                                <span class="badge bg-gradient-{{ isset($log['level']) && $log['level'] === 'error' ? 'danger' : (isset($log['level']) && $log['level'] === 'warning' ? 'warning' : 'success') }} me-2">
                                                    {{ ucfirst($log['level'] ?? 'info') }}
                                        </span>
                                                <span class="text-muted text-xs" id="timestamp-{{ $log['id'] ?? '' }}">
                                                    {{ isset($log['created_at']) ? \Carbon\Carbon::parse($log['created_at'])->format('M d, Y H:i:s') : 'No timestamp' }}
                                        </span>
                                            </div>
                                        </div>
                                        <p class="text-sm mb-0">
                                            {{ $log['details'] ?? 'No details available' }}
                                        </p>
                                    </div>
                                </div>
                                @endforeach
                        </div>
                    @endif

                    <!-- Loading Indicator -->
                    <div id="loadingIndicator" style="display: none;" class="text-center py-4">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                        <p class="text-sm text-muted mt-2">Loading logs...</p>
                    </div>

                    <!-- Error Message -->
                    <div id="errorMessage" style="display: none;" class="text-center py-4">
                        <div class="icon icon-shape bg-gradient-danger shadow text-center mx-auto mb-3" style="width: 50px; height: 50px; border-radius: 50%;">
                            <i class="bi bi-exclamation-circle text-lg opacity-10" aria-hidden="true"></i>
                        </div>
                        <h6 class="text-danger">Error</h6>
                        <p class="text-sm text-muted" id="errorMessageText"></p>
                        <button class="btn btn-sm btn-outline-primary mt-3" onclick="retryLoading()">
                            <i class="bi bi-arrow-repeat me-2"></i>Retry
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
/* Timeline Modern Styles */
.timeline-modern {
    position: relative;
    padding: 1rem 0;
    scrollbar-width: thin;
    scrollbar-color: var(--primary-color) rgba(0,0,0,0.1);
}

.timeline-modern::-webkit-scrollbar {
    width: 6px;
}

.timeline-modern::-webkit-scrollbar-track {
    background: rgba(0,0,0,0.1);
    border-radius: 10px;
}

.timeline-modern::-webkit-scrollbar-thumb {
    background: var(--primary-color);
    border-radius: 10px;
}

.timeline-modern::before {
    content: '';
    position: absolute;
    top: 0;
    left: 3.75rem;
    height: 100%;
    border-left: 2px dashed rgba(0,0,0,0.1);
}

.timeline-block {
    position: relative;
    display: flex;
    align-items: flex-start;
    margin-bottom: 1.5rem;
    gap: 1rem;
    padding-right: 1rem;
}

.timeline-block:last-child {
    margin-bottom: 0;
}

.timeline-checkbox {
    padding-top: 1rem;
    opacity: 0.7;
    transition: all 0.3s ease;
}

.timeline-checkbox:hover {
    opacity: 1;
}

.timeline-step {
    width: 3.5rem;
    height: 3.5rem;
    border-radius: 1rem;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
    position: relative;
    z-index: 1;
    box-shadow: 0 2px 12px rgba(0,0,0,0.12);
    transition: all 0.3s ease;
}

.timeline-step:hover {
    transform: scale(1.1);
}

.timeline-step i {
    font-size: 1.25rem;
    color: white;
}

.timeline-content {
    flex-grow: 1;
    padding: 1.25rem;
    background: rgba(255,255,255,0.8);
    backdrop-filter: blur(6px);
    border-radius: 1rem;
    box-shadow: 0 2px 12px rgba(0,0,0,0.08);
    border: 1px solid rgba(0,0,0,0.05);
    transition: all 0.3s ease;
}

.timeline-content:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 20px rgba(0,0,0,0.12);
}

/* Form Check Styles */
.form-check-input {
    cursor: pointer;
    border-color: var(--primary-color);
}

.form-check-input:checked {
    background-color: var(--primary-color);
    border-color: var(--primary-color);
}

/* Refresh Button Animation */
@keyframes spin {
    from { transform: rotate(0deg); }
    to { transform: rotate(360deg); }
}

.refreshing i {
    animation: spin 1s linear infinite;
}

/* Alert Styles */
.alert {
    border: none;
    border-radius: 1rem;
    padding: 1rem;
    margin-bottom: 1rem;
    backdrop-filter: blur(6px);
}

.alert-success {
    background: rgba(40, 167, 69, 0.1);
    border: 1px solid rgba(40, 167, 69, 0.2);
    color: #28a745;
}

.alert-danger {
    background: rgba(220, 53, 69, 0.1);
    border: 1px solid rgba(220, 53, 69, 0.2);
    color: #dc3545;
}

/* Button Styles */
.btn {
    border-radius: 0.75rem;
    padding: 0.625rem 1.25rem;
    font-weight: 500;
    transition: all 0.3s ease;
}

.btn:hover {
    transform: translateY(-2px);
}

.btn-sm {
    padding: 0.5rem 1rem;
    font-size: 0.875rem;
}

/* Responsive Adjustments */
@media (max-width: 768px) {
    .timeline-modern::before {
        left: 1.5rem;
    }
    
    .timeline-step {
        width: 3rem;
        height: 3rem;
    }
    
    .timeline-step i {
        font-size: 1rem;
    }
    
    .timeline-content {
        padding: 1rem;
    }
}

/* Selection Styles */
.timeline-block.selected .timeline-content {
    border: 2px solid var(--success-color);
    background: rgba(var(--success-color-rgb), 0.05);
}

.timeline-block.selected .timeline-step {
    border: 2px solid var(--success-color);
}

.selection-controls {
    transition: all 0.3s ease;
}

.selection-controls.show {
    display: flex !important;
    align-items: center;
    animation: slideIn 0.3s ease;
}

@keyframes slideIn {
    from { transform: translateX(20px); opacity: 0; }
    to { transform: translateX(0); opacity: 1; }
}

/* Checkbox Styles */
.form-check-input:checked {
    background-color: var(--success-color);
    border-color: var(--success-color);
}

/* Error States */
.error-state {
    color: var(--danger);
    background-color: var(--danger-bg);
    border: 1px solid var(--danger-border);
}

/* Loading States */
.loading-overlay {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(255, 255, 255, 0.8);
    display: flex;
    justify-content: center;
    align-items: center;
    z-index: 1000;
}

/* Status Indicators */
#connectionStatus {
    position: fixed;
    bottom: 1rem;
    right: 1rem;
    z-index: 1050;
    animation: slideIn 0.3s ease;
}

/* System Status Alert */
#systemStatusAlert {
    margin-bottom: 1rem;
    animation: slideDown 0.3s ease;
}

@keyframes slideDown {
    from { transform: translateY(-20px); opacity: 0; }
    to { transform: translateY(0); opacity: 1; }
}
</style>
@endpush

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
</script>
@endpush 
@endsection 