<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>RainBasin Pro Notification</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: #333;
            margin: 0;
            padding: 0;
            background-color: #f8fafc;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            background-color: #ffffff;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        .header {
            background: linear-gradient(135deg, #09203f 0%, #537895 100%);
            color: white;
            padding: 30px;
            text-align: center;
        }
        .header h1 {
            margin: 0;
            font-size: 24px;
            font-weight: 600;
        }
        .header p {
            margin: 10px 0 0 0;
            opacity: 0.9;
            font-size: 14px;
        }
        .urgency-banner {
            padding: 15px 30px;
            text-align: center;
            font-weight: bold;
            font-size: 16px;
            letter-spacing: 1px;
        }
        .urgency-critical {
            background-color: #dc2626;
            color: white;
            animation: pulse 2s infinite;
        }
        .urgency-high {
            background-color: #ea580c;
            color: white;
        }
        .urgency-medium {
            background-color: #2563eb;
            color: white;
        }
        .urgency-low {
            background-color: #16a34a;
            color: white;
        }
        .content {
            padding: 30px;
        }
        .notification-icon {
            font-size: 48px;
            text-align: center;
            margin-bottom: 20px;
        }
        .notification-title {
            font-size: 20px;
            font-weight: 600;
            margin-bottom: 15px;
            color: #1f2937;
        }
        .notification-message {
            font-size: 16px;
            line-height: 1.6;
            margin-bottom: 25px;
            color: #4b5563;
        }
        .action-section {
            background-color: #f8fafc;
            border-left: 4px solid {{ $urgencyColor }};
            padding: 20px;
            margin: 25px 0;
            border-radius: 8px;
        }
        .action-text {
            font-weight: 600;
            color: #1f2937;
            margin-bottom: 10px;
        }
        .action-description {
            font-size: 14px;
            color: #6b7280;
        }
        .button {
            display: inline-block;
            background: linear-gradient(135deg, #09203f 0%, #537895 100%);
            color: white;
            padding: 12px 24px;
            text-decoration: none;
            border-radius: 8px;
            font-weight: 600;
            margin: 20px 0;
            text-align: center;
        }
        .footer {
            background-color: #f8fafc;
            padding: 20px 30px;
            text-align: center;
            border-top: 1px solid #e5e7eb;
        }
        .footer-text {
            font-size: 14px;
            color: #6b7280;
            margin-bottom: 10px;
        }
        .footer-links {
            font-size: 12px;
            color: #9ca3af;
        }
        .footer-links a {
            color: #6b7280;
            text-decoration: none;
            margin: 0 10px;
        }
        .timestamp {
            font-size: 12px;
            color: #9ca3af;
            text-align: center;
            margin-top: 15px;
        }
        @keyframes pulse {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.8; }
        }
        @media (max-width: 600px) {
            .container {
                margin: 10px;
                border-radius: 8px;
            }
            .header, .content, .footer {
                padding: 20px;
            }
            .notification-title {
                font-size: 18px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header -->
        <div class="header">
            <h1>🌱 RainBasin Pro</h1>
            <p>Smart Garden Management System</p>
        </div>

        <!-- Urgency Banner -->
        <div class="urgency-banner urgency-{{ $notification['urgency'] }}">
            {{ $urgencyIcon }} {{ $urgencyLevel }}
        </div>

        <!-- Content -->
        <div class="content">
            <div class="notification-icon">
                {{ $urgencyIcon }}
            </div>

            <div class="notification-title">
                {{ $notification['title'] }}
            </div>

            <div class="notification-message">
                {{ $notification['message'] }}
            </div>

            <!-- Action Section -->
            <div class="action-section">
                <div class="action-text">
                    {{ $actionText }}
                </div>
                <div class="action-description">
                    @if($notification['urgency'] === 'critical')
                        This is a critical system alert that requires your immediate attention. Please check your dashboard right away.
                    @elseif($notification['urgency'] === 'high')
                        This notification contains important information that should be addressed soon.
                    @elseif($notification['urgency'] === 'medium')
                        This notification requires your attention when convenient.
                    @else
                        This is an informational update about your RainBasin Pro system.
                    @endif
                </div>
            </div>

            <!-- Action Button -->
            <div style="text-align: center;">
                <a href="{{ url('/admin/dashboard') }}" class="button">
                    View Dashboard
                </a>
            </div>

            <!-- Notification Details -->
            <div style="margin-top: 30px; padding: 15px; background-color: #f8fafc; border-radius: 8px;">
                <div style="font-size: 14px; color: #6b7280; margin-bottom: 10px;">
                    <strong>Notification Details:</strong>
                </div>
                <div style="font-size: 12px; color: #9ca3af;">
                    <div><strong>Type:</strong> {{ ucfirst(str_replace('_', ' ', $notification['type'])) }}</div>
                    <div><strong>Priority:</strong> {{ ucfirst($notification['priority']) }}</div>
                    <div><strong>Urgency:</strong> {{ ucfirst($notification['urgency']) }}</div>
                    <div><strong>Time:</strong> {{ $notification['timestamp']->format('M j, Y g:i A') }}</div>
                </div>
            </div>
        </div>

        <!-- Footer -->
        <div class="footer">
            <div class="footer-text">
                {{ $footerText }}
            </div>
            <div class="footer-links">
                <a href="{{ url('/admin/dashboard') }}">Dashboard</a> |
                <a href="{{ url('/admin/waterschedule') }}">Water Control</a> |
                <a href="{{ url('/admin/sensor-history') }}">History</a>
            </div>
            <div class="timestamp">
                Sent on {{ $notification['timestamp']->format('M j, Y \a\t g:i A') }}
            </div>
        </div>
    </div>
</body>
</html> 