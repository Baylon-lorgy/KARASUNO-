<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Staff Invitation - RainBasin Pro</title>
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
            background: linear-gradient(135deg, #09203f 0%, #537895 100%);
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
            background: linear-gradient(135deg, #09203f 0%, #537895 100%);
            color: white;
            padding: 15px 30px;
            text-decoration: none;
            border-radius: 8px;
            font-weight: bold;
            margin: 20px 0;
        }
        .role-badge {
            background: #e3f2fd;
            color: #1976d2;
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
            border-left: 4px solid #09203f;
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
        <h1>Welcome to RainBasin Pro</h1>
        <p>You've been invited to join our smart garden management system</p>
    </div>
    
    <div class="content">
        <h2>Hello {{ $staffUser->name }},</h2>
        
        <p>You have been invited to join RainBasin Pro as a <strong>{{ $role }}</strong>.</p>
        
        <div class="role-badge">
            Role: {{ $role }}
        </div>
        
        <div class="permissions">
            <h3>Your Permissions:</h3>
            @foreach($permissions as $permission)
                <div class="permission-item">
                    ✅ {{ ucwords(str_replace('_', ' ', $permission)) }}
                </div>
            @endforeach
        </div>
        
        <p>To complete your account setup, please click the button below to set your password and activate your account:</p>
        
        <div style="text-align: center;">
            <a href="{{ $invitationUrl }}" class="btn">
                Complete Account Setup
            </a>
        </div>
        
        <p><strong>Important:</strong> This invitation link will expire once used. If you have any issues, please contact your system administrator.</p>
        
        <p>After setting up your account, you'll need to wait for manager approval before you can access the system.</p>
    </div>
    
    <div class="footer">
        <p>RainBasin Pro - Smart Garden Management System</p>
        <p>This is an automated message. Please do not reply to this email.</p>
    </div>
</body>
</html> 