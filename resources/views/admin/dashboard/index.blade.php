@extends('layouts.admindashboardlayout')

@section('title', 'Dashboard - Rainwater Catch Basin')

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

    /* Water Status GIF Styles */
    .icon-shape {
        width: 48px;
        height: 48px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 0.75rem;
        position: relative;
        overflow: hidden;
    }

    .water-status-gif {
        width: 48px;
        height: 48px;
        border-radius: 50%;
        object-fit: cover;
        animation: waterGlow 2s ease-in-out infinite;
    }

    @keyframes waterGlow {
        0% {
            box-shadow: 0 0 10px rgba(23, 173, 55, 0.3),
                       0 0 20px rgba(23, 173, 55, 0.2);
        }
        50% {
            box-shadow: 0 0 20px rgba(23, 173, 55, 0.5),
                       0 0 40px rgba(23, 173, 55, 0.3);
        }
        100% {
            box-shadow: 0 0 10px rgba(23, 173, 55, 0.3),
                       0 0 20px rgba(23, 173, 55, 0.2);
        }
    }

    .icon-shape::after {
        content: '';
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        width: 54px;
        height: 54px;
        border-radius: 50%;
        background: radial-gradient(circle, 
            rgba(23, 173, 55, 0.2) 0%,
            rgba(23, 173, 55, 0.1) 50%,
            transparent 70%);
        z-index: -1;
        animation: pulse 2s ease-in-out infinite;
    }

    @keyframes pulse {
        0% {
            transform: translate(-50%, -50%) scale(0.8);
            opacity: 0.5;
        }
        50% {
            transform: translate(-50%, -50%) scale(1.2);
            opacity: 0.8;
        }
        100% {
            transform: translate(-50%, -50%) scale(0.8);
            opacity: 0.5;
        }
    }

    /* Gradient Text */
    .text-gradient {
        background-clip: text;
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        position: relative;
    }

    .text-gradient.text-success {
        background-image: linear-gradient(310deg, #17ad37 0%, #98ec2d 100%);
    }

    .text-gradient.text-warning {
        background-image: linear-gradient(310deg, #f53939 0%, #fbcf33 100%);
    }

    /* Water Level Monitoring Styles */
    .water-level-card {
        height: 100%;
    }

    .water-level-container {
        position: relative;
        height: 200px;
        width: 100%;
        background: rgba(255, 255, 255, 0.1);
        border-radius: 10px;
        overflow: hidden;
        margin: 1rem 0;
    }

    .water-fill {
        position: absolute;
        bottom: 0;
        left: 0;
        width: 100%;
        background: linear-gradient(180deg, 
            rgba(23, 173, 55, 0.8) 0%,
            rgba(23, 173, 55, 0.6) 100%);
        transition: height 1s ease-in-out;
    }

    .water-level-marker {
        position: absolute;
        left: 0;
        width: 100%;
        height: 1px;
        background: rgba(255, 255, 255, 0.3);
    }

    .water-level-label {
        position: absolute;
        right: 10px;
        transform: translateY(-50%);
        color: rgba(255, 255, 255, 0.8);
        font-size: 0.75rem;
    }

    /* Alert Styles */
    .alert-container {
        position: fixed;
        top: 20px;
        right: 20px;
        z-index: 1000;
    }

    .alert {
        padding: 1rem;
        margin-bottom: 0.5rem;
        border-radius: 0.5rem;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        display: flex;
        align-items: center;
        justify-content: space-between;
        animation: slideIn 0.3s ease-out;
    }

    @keyframes slideIn {
        from {
            transform: translateX(100%);
            opacity: 0;
        }
        to {
            transform: translateX(0);
            opacity: 1;
        }
    }

    /* History Chart Styles */
    .history-chart {
        height: 200px;
        margin-top: 1rem;
    }
</style>

<!-- Alert Container -->
<div class="alert-container" id="alertContainer"></div>

<div class="container-fluid py-4">
    <!-- Stats Overview -->
    <div class="row">
        <!-- Water Status Card -->
        <div class="col-xl-3 col-sm-6">
            <div class="card">
                <div class="card-body p-3">
                    <div class="row">
                        <div class="col-8">
                            <div class="numbers">
                                <p class="text-sm mb-0 text-uppercase font-weight-bold text-muted">Water Status</p>
                                <h5 class="font-weight-bolder mb-0" id="waterStatus">
                                    {{ $waterStatus }}
                                    <span class="text-sm text-{{ $waterStatus === 'Water Detected' ? 'success' : 'warning' }} text-gradient">
                                        <i class="bi bi-water me-2"></i>
                                    </span>
                                </h5>
                                <small class="text-muted" id="lastUpdate">
                                    Last update: {{ $lastUpdate ? Carbon\Carbon::parse($lastUpdate)->diffForHumans() : 'Never' }}
                                </small>
                            </div>
                        </div>
                        <div class="col-4 text-end">
                            <div class="icon icon-shape bg-gradient-{{ $waterStatus === 'Water Detected' ? 'success' : 'warning' }} shadow text-center rounded-circle">
                                <img src="https://i.pinimg.com/originals/b8/cb/0f/b8cb0fd2b86bbc036709351cc8325c31.gif" alt="water" class="water-status-gif">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Water Level Card -->
        <div class="col-xl-9 col-sm-6">
            <div class="card water-level-card">
                <div class="card-body p-3">
                    <div class="row">
                        <div class="col-12">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <div>
                                    <h6 class="text-sm mb-0 text-uppercase font-weight-bold text-muted">Water Level</h6>
                                    <p class="text-sm mb-0 text-muted">Real-time tank monitoring</p>
                                </div>
                                <div class="d-flex gap-2">
                                    <button class="btn btn-sm btn-outline-primary" onclick="toggleHistory()">
                                        <i class="bi bi-graph-up"></i> History
                                    </button>
                                    <button class="btn btn-sm btn-outline-secondary" onclick="configureAlerts()">
                                        <i class="bi bi-bell"></i> Alerts
                                    </button>
                                </div>
                            </div>

                            <div class="water-level-container" id="waterLevelVisual">
                                <div class="water-fill" id="waterFill"></div>
                                <!-- Water level markers will be added dynamically -->
                            </div>

                            <div class="row mt-3">
                                <div class="col-md-3">
                                    <div class="text-center">
                                        <h3 class="mb-0" id="currentLevel">0%</h3>
                                        <p class="text-sm text-muted mb-0">Current Level</p>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="text-center">
                                        <h3 class="mb-0" id="capacity">0L</h3>
                                        <p class="text-sm text-muted mb-0">Capacity</p>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="text-center">
                                        <h3 class="mb-0" id="dailyUsage">0L</h3>
                                        <p class="text-sm text-muted mb-0">Daily Usage</p>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="text-center">
                                        <h3 class="mb-0" id="lastRefill">--</h3>
                                        <p class="text-sm text-muted mb-0">Last Refill</p>
                                    </div>
                                </div>
                            </div>

                            <div class="history-chart d-none" id="historyChart">
                                <canvas id="waterLevelHistory"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/paho-mqtt/1.0.1/mqttws31.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // MQTT Client setup
    const mqttConfig = @json($mqttConfig);
    const clientId = mqttConfig.clientId || "web_" + Math.random().toString(16).substr(2, 8);
    
    const mqttClient = new Paho.MQTT.Client(
        mqttConfig.host,
        Number(mqttConfig.port),
        clientId
    );

    // Alert System
    const alertSettings = {
        waterDetection: {
            enabled: true,
            duration: 5000 // 5 seconds
        },
        waterLevel: {
            enabled: true,
            lowThreshold: 20,
            highThreshold: 80,
            duration: 5000
        }
    };

    function showAlert(message, type = 'info') {
        const alertContainer = document.getElementById('alertContainer');
        const alert = document.createElement('div');
        alert.className = `alert alert-${type} d-flex align-items-center`;
        alert.innerHTML = `
            <i class="bi bi-info-circle me-2"></i>
            <span>${message}</span>
            <button type="button" class="btn-close ms-auto" onclick="this.parentElement.remove()"></button>
        `;
        alertContainer.appendChild(alert);

        setTimeout(() => {
            alert.remove();
        }, 5000);
    }

    // Water Level Visualization
    function initWaterLevelVisual() {
        const container = document.getElementById('waterLevelVisual');
        const markers = [0, 25, 50, 75, 100];
        
        markers.forEach(level => {
            const marker = document.createElement('div');
            marker.className = 'water-level-marker';
            marker.style.bottom = `${level}%`;
            
            const label = document.createElement('span');
            label.className = 'water-level-label';
            label.textContent = `${level}%`;
            
            marker.appendChild(label);
            container.appendChild(marker);
        });
    }

    function updateWaterLevel(level) {
        const waterFill = document.getElementById('waterFill');
        const currentLevel = document.getElementById('currentLevel');
        
        waterFill.style.height = `${level}%`;
        currentLevel.textContent = `${level}%`;

        // Check thresholds for alerts
        if (alertSettings.waterLevel.enabled) {
            if (level <= alertSettings.waterLevel.lowThreshold) {
                showAlert(`Low water level alert: ${level}%`, 'warning');
            } else if (level >= alertSettings.waterLevel.highThreshold) {
                showAlert(`High water level alert: ${level}%`, 'warning');
            }
        }
    }

    // History Chart
    let historyChart;
    const historyData = {
        labels: [],
        values: []
    };

    function initHistoryChart() {
        const ctx = document.getElementById('waterLevelHistory').getContext('2d');
        historyChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: historyData.labels,
                datasets: [{
                    label: 'Water Level History',
                    data: historyData.values,
                    borderColor: 'rgba(23, 173, 55, 0.8)',
                    tension: 0.4,
                    fill: false
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        max: 100
                    }
                }
            }
        });
    }

    function updateHistory(level) {
        const now = new Date();
        const timeString = now.toLocaleTimeString();
        
        historyData.labels.push(timeString);
        historyData.values.push(level);

        // Keep last 24 data points (can be adjusted)
        if (historyData.labels.length > 24) {
            historyData.labels.shift();
            historyData.values.shift();
        }

        if (historyChart) {
            historyChart.update();
        }
    }

    function toggleHistory() {
        const historyElement = document.getElementById('historyChart');
        historyElement.classList.toggle('d-none');
        
        if (!historyChart && !historyElement.classList.contains('d-none')) {
            initHistoryChart();
        }
    }

    function configureAlerts() {
        // This could be expanded into a modal with more configuration options
        const enabled = confirm('Would you like to enable water level alerts?');
        alertSettings.waterLevel.enabled = enabled;
        showAlert(`Water level alerts ${enabled ? 'enabled' : 'disabled'}`, 'info');
    }

    function updateConnectionStatus(status, message = '') {
        const statusElement = document.getElementById('connectionStatus');
        if (statusElement) {
            let statusText, statusClass;
            switch (status) {
                case 'connecting':
                    statusText = 'Connecting...';
                    statusClass = 'text-warning';
                    break;
                case 'connected':
                    statusText = 'Connected';
                    statusClass = 'text-success';
                    break;
                case 'disconnected':
                    statusText = 'Disconnected';
                    statusClass = 'text-danger';
                    break;
                case 'error':
                    statusText = `Connection Error`;
                    statusClass = 'text-danger';
                    break;
            }
            statusElement.innerHTML = `${statusText} <i class="bi bi-${status === 'connected' ? 'check-circle' : 'x-circle'} ms-1"></i>`;
            statusElement.className = `font-weight-bolder mb-0 ${statusClass}`;
        }
    }

    function connectMQTT() {
        updateConnectionStatus('connecting');
        
        const options = {
            timeout: 3,
            keepAliveInterval: 30,
            useSSL: false,
            onSuccess: function() {
                updateConnectionStatus('connected');
                    mqttClient.subscribe(mqttConfig.topic);
                showAlert('Connected to MQTT broker', 'success');
            },
            onFailure: function(message) {
                updateConnectionStatus('error', message.errorMessage);
                showAlert('Failed to connect to MQTT broker', 'danger');
                setTimeout(connectMQTT, 5000);
            }
        };

        try {
            mqttClient.connect(options);
        } catch (error) {
            updateConnectionStatus('error', error.message);
            showAlert('Connection error: ' + error.message, 'danger');
            setTimeout(connectMQTT, 5000);
        }
    }

    mqttClient.onConnectionLost = function(responseObject) {
        if (responseObject.errorCode !== 0) {
            updateConnectionStatus('disconnected', responseObject.errorMessage);
            showAlert('Connection lost. Reconnecting...', 'warning');
            setTimeout(connectMQTT, 5000);
        }
    };

    mqttClient.onMessageArrived = function(message) {
        try {
            const data = message.payloadString;
            
            // Try to parse as JSON for water level data
            try {
                const jsonData = JSON.parse(data);
                if (jsonData.waterLevel !== undefined) {
                    updateWaterLevel(jsonData.waterLevel);
                    updateHistory(jsonData.waterLevel);
                    
                    // Update additional stats if available
                    if (jsonData.capacity) document.getElementById('capacity').textContent = `${jsonData.capacity}L`;
                    if (jsonData.dailyUsage) document.getElementById('dailyUsage').textContent = `${jsonData.dailyUsage}L`;
                    if (jsonData.lastRefill) document.getElementById('lastRefill').textContent = jsonData.lastRefill;
                }
            } catch (jsonError) {
                // If not JSON, treat as water detection status
            const waterStatus = document.getElementById('waterStatus');
            const lastUpdate = document.getElementById('lastUpdate');

                if (waterStatus && lastUpdate) {
                    const isWaterDetected = data === 'Water Detected';
                    waterStatus.innerHTML = `
                        ${data}
                        <span class="text-sm text-${isWaterDetected ? 'success' : 'warning'} text-gradient">
                            <i class="bi bi-water me-2"></i>
                        </span>
                    `;
                    lastUpdate.textContent = 'Last update: Just now';

                    // Update parent card
                    const cardIcon = waterStatus.closest('.card').querySelector('.icon-shape');
                    cardIcon.className = `icon icon-shape bg-gradient-${isWaterDetected ? 'success' : 'warning'} shadow text-center rounded-circle`;

                    // Show alert for water detection status if enabled
                    if (alertSettings.waterDetection.enabled) {
                        showAlert(`${data}`, isWaterDetected ? 'success' : 'warning');
                    }
                }
            }
        } catch (e) {
            console.error("Error processing message:", e);
            showAlert('Error processing message: ' + e.message, 'danger');
        }
    };

    // Initialize components
    window.addEventListener('load', function() {
        initWaterLevelVisual();
        connectMQTT();
    });
</script>
@endpush
@endsection 