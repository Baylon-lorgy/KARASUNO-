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
</style>

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
    </div>
</div>

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/paho-mqtt/1.0.1/mqttws31.min.js"></script>
<script>
    // MQTT Client setup
    const mqttConfig = @json($mqttConfig);
    const clientId = mqttConfig.clientId || "web_" + Math.random().toString(16).substr(2, 8);
    
    const mqttClient = new Paho.MQTT.Client(
        mqttConfig.host,
        Number(mqttConfig.port),
        clientId
    );

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
            },
            onFailure: function(message) {
                updateConnectionStatus('error', message.errorMessage);
                setTimeout(connectMQTT, 5000);
            }
        };

        try {
            mqttClient.connect(options);
        } catch (error) {
            updateConnectionStatus('error', error.message);
            setTimeout(connectMQTT, 5000);
        }
    }

    mqttClient.onConnectionLost = function(responseObject) {
        if (responseObject.errorCode !== 0) {
            updateConnectionStatus('disconnected', responseObject.errorMessage);
            setTimeout(connectMQTT, 5000);
        }
    };

    mqttClient.onMessageArrived = function(message) {
        try {
            const status = message.payloadString;
            const waterStatus = document.getElementById('waterStatus');
            const lastUpdate = document.getElementById('lastUpdate');

            if (waterStatus && lastUpdate) {
                const isWaterDetected = status === 'Water Detected';
                waterStatus.innerHTML = `
                    ${status}
                    <span class="text-sm text-${isWaterDetected ? 'success' : 'warning'} text-gradient">
                        <i class="bi bi-water me-2"></i>
                    </span>
                `;
                lastUpdate.textContent = 'Last update: Just now';

                // Update parent card
                const cardIcon = waterStatus.closest('.card').querySelector('.icon-shape');
                cardIcon.className = `icon icon-shape bg-gradient-${isWaterDetected ? 'success' : 'warning'} shadow text-center rounded-circle`;
            }
        } catch (e) {
            console.error("Error processing message:", e);
        }
    };

    // Connect when the page loads
    window.addEventListener('load', connectMQTT);
</script>
@endpush
@endsection 