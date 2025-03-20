@if(isset($error))
    <div class="alert alert-danger fade show">
        <div class="d-flex align-items-center">
            <i class="bi bi-exclamation-triangle-fill me-2"></i>
            <span>{{ $error }}</span>
        </div>
    </div>
@endif

@if(empty($logs))
    <div class="text-center py-5">
        <div class="icon icon-shape bg-gradient-secondary shadow-secondary mx-auto mb-3">
            <i class="bi bi-journal-x"></i>
        </div>
        <h6 class="text-secondary">No Logs Available</h6>
        <p class="text-sm text-muted">System activities will appear here when available</p>
    </div>
@else
    <div class="timeline-modern">
        @foreach($logs as $log)
            @php
                // First try to extract the message from the log format
                $pattern = '/^\[.*?\] \w+\.\w+: (.*?)$/';
                preg_match($pattern, $log, $matches);
                $message = $matches[1] ?? $log;

                // If the message contains a JSON water level reading, extract only the status
                if (str_contains($message, 'Latest water level reading')) {
                    $jsonStart = strpos($message, '{');
                    if ($jsonStart !== false) {
                        $jsonData = json_decode(substr($message, $jsonStart), true);
                        if (isset($jsonData['data']['App\\Models\\WaterLevel']['status'])) {
                            $message = $jsonData['data']['App\\Models\\WaterLevel']['status'];
                        }
                    }
                }

                if (str_contains(strtolower($message), 'water detected') || $message === 'Water Detected') {
                    $icon = 'bi bi-droplet-fill';
                    $colorClass = 'info';
                } elseif (str_contains(strtolower($message), 'no water') || $message === 'No Water') {
                    $icon = 'bi bi-droplet';
                    $colorClass = 'warning';
                } elseif (str_contains(strtolower($message), 'cleared')) {
                    $icon = 'bi bi-trash';
                    $colorClass = 'dark';
                } else {
                    $icon = 'bi bi-bell-fill';
                    $colorClass = 'secondary';
                }
            @endphp
            <div class="timeline-block">
                <span class="timeline-step bg-gradient-{{ $colorClass }}">
                    <i class="{{ $icon }}"></i>
                </span>
                <div class="timeline-content">
                    <div class="d-flex justify-content-between">
                        <span class="badge bg-gradient-{{ $colorClass }} text-xxs mb-2">
                            Water Level
                        </span>
                    </div>
                    <h6 class="text-sm font-weight-bold mb-0">{{ $message }}</h6>
                    <p class="text-xs text-muted mt-1 mb-0">
                        <i class="bi bi-clock-history me-1"></i>
                        Just now
                    </p>
                </div>
            </div>
        @endforeach
    </div>
@endif 