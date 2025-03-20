<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Sensor Data Report</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 40px;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
        }
        .report-info {
            margin-bottom: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f5f5f5;
        }
        .statistics {
            margin-top: 30px;
            padding: 20px;
            background-color: #f9f9f9;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Rainwater Catch Basin Monitoring Report</h1>
    </div>

    <div class="report-info">
        <p><strong>Report Period:</strong> {{ ucfirst($period) }}</p>
        <p><strong>Date Range:</strong> {{ $start_date }} to {{ $end_date }}</p>
    </div>

    <div class="statistics">
        <h2>Statistics Summary</h2>
        <table>
            <tr>
                <th>Metric</th>
                <th>Value</th>
            </tr>
            <tr>
                <td>Average Water Level</td>
                <td>{{ number_format($statistics['average_water_level'], 2) }} cm</td>
            </tr>
            <tr>
                <td>Maximum Water Level</td>
                <td>{{ number_format($statistics['max_water_level'], 2) }} cm</td>
            </tr>
            <tr>
                <td>Minimum Water Level</td>
                <td>{{ number_format($statistics['min_water_level'], 2) }} cm</td>
            </tr>
            <tr>
                <td>Total Readings</td>
                <td>{{ $statistics['total_readings'] }}</td>
            </tr>
            <tr>
                <td>Alerts Triggered</td>
                <td>{{ $statistics['alerts_triggered'] }}</td>
            </tr>
        </table>
    </div>

    <h2>Detailed Readings</h2>
    <table>
        <thead>
            <tr>
                <th>Date & Time</th>
                <th>Water Level (cm)</th>
                <th>Sensor Reading</th>
            </tr>
        </thead>
        <tbody>
            @foreach($water_levels->sortBy('created_at') as $reading)
            <tr>
                <td>{{ $reading->created_at->format('Y-m-d H:i:s') }}</td>
                <td>{{ number_format($reading->level, 2) }}</td>
                <td>-</td>
            </tr>
            @endforeach

            @foreach($sensor_data->sortBy('created_at') as $reading)
            <tr>
                <td>{{ $reading->created_at->format('Y-m-d H:i:s') }}</td>
                <td>-</td>
                <td>{{ $reading->value }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        <p>Generated on: {{ now()->format('Y-m-d H:i:s') }}</p>
    </div>
</body>
</html> 