<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Account Approved - RainBasin Pro</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            background: linear-gradient(135deg, #059669 0%, #10b981 100%);
            color: white;
            padding: 30px;
            text-align: center;
            border-radius: 10px 10px 0 0;
        }
        .content {
            background: #f8fafc;
            padding: 30px;
            border-radius: 0 0 10px 10px;
        }
        .btn {
            display: inline-block;
            background: linear-gradient(135deg, #059669 0%, #10b981 100%);
            color: white;
            padding: 15px 30px;
            text-decoration: none;
            border-radius: 8px;
            font-weight: bold;
            margin: 20px 0;
        }
        .status-badge {
            background: #d1fae5;
            color: #065f46;
            padding: 8px 16px;
            border-radius: 20px;
            font-weight: bold;
            display: inline-block;
            margin: 10px 0;
        }
        .permissions {
            background: white;
            padding: 20px;
            border-radius: 8px;
            margin: 20px 0;
            border-left: 4px solid #059669;
        }
        .permission-item {
            margin: 8px 0;
            padding: 5px 0;
        }
        .footer {
            text-align: center;
            margin-top: 30px;
            color: #666;
            font-size: 12px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Account Approved!</h1>
        <p>Your RainBasin Pro account is now active</p>
    </div>
    
    <div class="content">
        <h2>Hello {{ $staffUser->name }},</h2>
        
        <p>Great news! Your RainBasin Pro account has been approved and is now active.</p>
        
        <div class="status-badge">
            ✅ Account Status: Active
        </div>
        
        <div class="permissions">
            <h3>Your Access Permissions:</h3>
            @foreach($permissions as $permission)
                <div class="permission-item">
                    ✅ {{ ucwords(str_replace('_', ' ', $permission)) }}
                </div>
            @endforeach
        </div>
        
        <p>You can now log in to the system and access your assigned features:</p>
        
        <ul>
            <li><strong>Dashboard:</strong> View real-time sensor data and system status</li>
            <li><strong>Watering Control:</strong> Manage watering schedules and manual controls</li>
            <li><strong>Sensor History:</strong> Access historical data and generate reports</li>
            @if($staffUser->role === 'manager' || $staffUser->role === 'admin')
                <li><strong>User Management:</strong> Manage staff accounts and permissions</li>
                <li><strong>System Settings:</strong> Configure system parameters</li>
            @endif
            @if($staffUser->role === 'admin')
                <li><strong>Audit Logs:</strong> View system audit trails</li>
            @endif
        </ul>
        
        <div style="text-align: center;">
            <a href="{{ url('/login') }}" class="btn">
                Login to RainBasin Pro
            </a>
        </div>
        
        <p>If you have any questions or need assistance, please contact your system administrator.</p>
    </div>
    
    <div class="footer">
        <p>RainBasin Pro - Smart Garden Management System</p>
        <p>This is an automated message. Please do not reply to this email.</p>
    </div>
</body>
</html> 