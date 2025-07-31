<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>{{ $title ?? 'RainBasin Pro Report' }}</title>
    <style>
        body { 
            font-family: DejaVu Sans, sans-serif; 
            font-size: 12px; 
            margin: 0;
            padding: 30px 40px;
            line-height: 1.6;
            max-width: 100%;
            box-sizing: border-box;
        }
        .header { 
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 40px;
            padding: 25px 0;
            border-bottom: 2px solid #e2e8f0;
            flex-wrap: wrap;
            gap: 20px;
        }
        .header-left {
            display: flex;
            align-items: center;
            gap: 20px;
            flex: 1;
            min-width: 300px;
        }
        .header-right {
            text-align: right;
            flex-shrink: 0;
        }
        .footer { 
            position: fixed;
            bottom: 30px;
            left: 40px;
            right: 40px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 10px;
            color: #6b7280;
            border-top: 1px solid #e2e8f0;
            padding: 15px 0;
            background: white;
        }
        .logo { 
            width: 60px; 
            height: 60px;
            display: block;
            flex-shrink: 0;
        }
        .company-info {
            display: flex;
            flex-direction: column;
            gap: 8px;
            flex: 1;
        }
        .company-name {
            font-size: 22px;
            font-weight: bold;
            color: #09203f;
            margin: 0;
            line-height: 1.2;
        }
        .company-tagline {
            font-size: 13px;
            color: #537895;
            margin: 0;
            line-height: 1.3;
        }
        .report-info {
            display: flex;
            flex-direction: column;
            gap: 6px;
            font-size: 11px;
            line-height: 1.4;
        }
        .report-info-item {
            margin: 0;
            padding: 0;
        }
        
        /* Page numbering */
        @page {
            counter-increment: page;
            margin-bottom: 60px;
            margin-top: 30px;
            margin-left: 40px;
            margin-right: 40px;
        }
        
        .pagenum:before {
            content: counter(page);
        }
        
        .page-break {
            page-break-before: always;
        }
        .content { 
            margin: 30px 0 60px 0;
            line-height: 1.6;
            max-width: 100%;
        }
        
        /* Responsive adjustments */
        @media print {
            .header {
                margin-bottom: 30px;
            }
            .content {
                margin: 25px 0 50px 0;
            }
            .summary-section {
                padding: 25px;
                margin: 30px 0;
            }
            .data-section {
                margin: 30px 0;
            }
        }
        
        /* Content sections with better spacing */
        .content p {
            margin: 0 0 15px 0;
            text-align: justify;
        }
        
        .content h3 {
            margin: 0 0 20px 0;
            color: #09203f;
            font-size: 16px;
            font-weight: bold;
        }
        .signature { 
            margin: 60px 0 40px 0;
            display: flex; 
            justify-content: space-between;
            gap: 40px;
        }
        .sign-box { 
            text-align: center; 
            flex: 1;
            padding: 20px;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            background: #f8fafc;
        }
        .sign-line {
            border-top: 1px solid #000;
            margin: 10px 0;
            height: 1px;
        }
        .company-name {
            font-size: 24px;
            font-weight: bold;
            color: #09203f;
            margin-bottom: 10px;
        }
        .report-title {
            font-size: 18px;
            font-weight: bold;
            color: #537895;
            margin-bottom: 20px;
        }
        .info-section {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 5px;
            margin: 20px 0;
            border-left: 4px solid #09203f;
        }
        .info-item {
            margin: 5px 0;
        }
        .info-label {
            font-weight: bold;
            color: #09203f;
        }
        .summary-section {
            margin: 35px 0;
        }
        .summary-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(100px, 1fr));
            gap: 15px;
            margin-top: 20px;
        }
        .summary-item {
            text-align: center;
            padding: 15px 10px;
            background: white;
            border-radius: 8px;
            border: 1px solid #e2e8f0;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
            transition: all 0.2s ease;
        }
        .summary-value {
            font-size: 18px;
            font-weight: bold;
            color: #09203f;
            margin-bottom: 5px;
            line-height: 1.2;
        }
        .summary-label {
            font-size: 9px;
            color: #6b7280;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            line-height: 1.3;
        }
        .data-section {
            margin: 35px 0;
        }
        .data-table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
            font-size: 10px;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 8px rgba(0,0,0,0.05);
        }
        .data-table th,
        .data-table td {
            border: 1px solid #e2e8f0;
            padding: 10px 8px;
            text-align: center;
            line-height: 1.4;
        }
        .data-table th {
            background: #f1f5f9;
            font-weight: bold;
            color: #09203f;
            font-size: 9px;
            padding: 12px 8px;
        }
        .data-table tr:nth-child(even) {
            background: #f8fafc;
        }
        .data-table tr:hover {
            background: #f1f5f9;
        }
        .status-available {
            color: #059669;
            font-weight: bold;
            padding: 4px 8px;
            border-radius: 4px;
            background: #ecfdf5;
        }
        .status-unavailable {
            color: #dc2626;
            font-weight: bold;
            padding: 4px 8px;
            border-radius: 4px;
            background: #fef2f2;
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="header-left">
            <!-- Company Logo -->
            <div class="logo">
                @if(file_exists(public_path('images/Logo.png')))
                    <img src="{{ public_path('images/Logo.png') }}" alt="RainBasin Pro Logo" style="width: 60px; height: 60px; object-fit: contain;">
                @else
                    <!-- Fallback SVG Logo -->
                    <svg width="60" height="60" viewBox="0 0 60 60">
                        <circle cx="30" cy="30" r="6" fill="#09203f"/>
                        <path d="M30 12 L33 18 L27 18 Z" fill="#09203f"/>
                        <path d="M30 48 L33 42 L27 42 Z" fill="#09203f"/>
                        <path d="M12 30 L18 33 L18 27 Z" fill="#09203f"/>
                        <path d="M48 30 L42 33 L42 27 Z" fill="#09203f"/>
                        <ellipse cx="30" cy="24" rx="3" ry="6" fill="#537895" opacity="0.7"/>
                    </svg>
                @endif
            </div>
            
            <div class="company-info">
                <div class="company-name">RainBasin Pro</div>
                <div class="company-tagline">Smart Garden Management System</div>
            </div>
        </div>
        
        <div class="header-right">
            <div class="report-info">
                <div class="report-info-item">
                    <strong>Generated:</strong> {{ $generated_at->format('M j, Y g:i A') }}
                </div>
                <div class="report-info-item">
                    <strong>Client:</strong> {{ $client_name ?? 'Client Company' }}
                </div>
                <div class="report-info-item">
                    <strong>Period:</strong> {{ \Carbon\Carbon::parse($start_date ?? now())->format('M j, Y') }} - {{ \Carbon\Carbon::parse($end_date ?? now())->format('M j, Y') }}
                </div>
            </div>
        </div>
    </div>

    <div class="content">
        <p><strong>Dear {{ $client_name ?? 'Client' }},</strong></p>
        
        <p>This report contains the daily sensor data analysis for your RainBasin Pro smart garden management system. The data represents daily averages and ranges for each monitored parameter.</p>

        @if(isset($stats))
        <div class="data-section">
            <h3 style="margin: 0 0 15px 0; color: #09203f; font-size: 16px;">Report Summary</h3>
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Metric</th>
                        <th>Value</th>
                        <th>Unit</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>Days Monitored</td>
                        <td>{{ number_format($stats['total_days']) }}</td>
                        <td>days</td>
                    </tr>
                    <tr>
                        <td>Average Water Level</td>
                        <td>{{ number_format($stats['avg_water_level'], 1) }}</td>
                        <td>%</td>
                    </tr>
                    <tr>
                        <td>Average Soil Moisture</td>
                        <td>{{ number_format($stats['avg_soil_moisture'], 1) }}</td>
                        <td>%</td>
                    </tr>
                    <tr>
                        <td>Average Temperature</td>
                        <td>{{ number_format($stats['avg_temperature'], 1) }}</td>
                        <td>°C</td>
                    </tr>
                    <tr>
                        <td>Average Humidity</td>
                        <td>{{ number_format($stats['avg_humidity'], 1) }}</td>
                        <td>%</td>
                    </tr>
                    <tr>
                        <td>Water Available Days</td>
                        <td>{{ number_format($stats['water_available_days']) }}</td>
                        <td>days</td>
                    </tr>
                </tbody>
            </table>
        </div>
        @endif

        @if(isset($sensor_data) && count($sensor_data) > 0)
        <div class="data-section">
            <h3 style="margin: 0 0 15px 0; color: #09203f; font-size: 16px;">Daily Sensor Readings</h3>
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Water Level (%)</th>
                        <th>Soil Moisture (%)</th>
                        <th>Temperature (°C)</th>
                        <th>Humidity (%)</th>
                        <th>Water Status</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($sensor_data->take(15) as $record)
                    <tr>
                        <td>{{ $record['date'] }}</td>
                        <td>{{ $record['water_level'] }}%</td>
                        <td>{{ $record['soil_moisture'] }}%</td>
                        <td>{{ $record['temperature'] }}°C</td>
                        <td>{{ $record['humidity'] }}%</td>
                        <td class="{{ $record['water_status'] === 'Available' ? 'status-available' : 'status-unavailable' }}">
                            {{ $record['water_status'] }}
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            @if(count($sensor_data) > 15)
            <p style="text-align: center; font-size: 10px; color: #6b7280; margin-top: 10px;">
                Showing first 15 days of {{ count($sensor_data) }} total days
            </p>
            @endif
        </div>
        @else
        <div class="data-section">
            <h3 style="margin: 0 0 15px 0; color: #09203f; font-size: 16px;">No Data Available</h3>
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Status</th>
                        <th>Message</th>
                        <th>Period</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>No Data</td>
                        <td>No daily sensor data was collected during the selected period</td>
                        <td>{{ \Carbon\Carbon::parse($start_date ?? now())->format('M j, Y') }} - {{ \Carbon\Carbon::parse($end_date ?? now())->format('M j, Y') }}</td>
                    </tr>
                </tbody>
            </table>
            <p style="text-align: center; font-size: 10px; color: #6b7280; margin-top: 10px;">
                This could be due to system maintenance or no data collection during this timeframe.
            </p>
        </div>
        @endif
    </div>

    <div class="signature">
        <div class="sign-box">
            <div class="sign-line"></div>
            <p><strong>System Administrator</strong></p>
            <p>RainBasin Pro</p>
            <p>Date: {{ $generated_at->format('M j, Y') }}</p>
        </div>
        <div class="sign-box">
            <div class="sign-line"></div>
            <p><strong>Client Representative</strong></p>
            <p>{{ $client_name ?? 'Client Company' }}</p>
            <p>Date: _________________</p>
        </div>
    </div>

    <div class="footer">
        <div style="text-align: left;">
            <span style="font-size: 10px; color: #6b7280;">
                RainBasin Pro - Smart Garden Management System
            </span>
        </div>
        <div style="text-align: center;">
            <span style="font-size: 10px; color: #6b7280;">
                Confidential Report &copy; {{ $generated_at->year }}
            </span>
        </div>
        <div style="text-align: right;">
            <span style="font-size: 10px; color: #6b7280;">
                Page <span class="pagenum"></span>
            </span>
        </div>
    </div>
</body>
</html> 