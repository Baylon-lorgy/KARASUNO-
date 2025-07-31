# Audit Logs and User Management API Documentation

## Audit Logs API

### GET /api/audit-logs
Retrieves audit logs with filtering and search capabilities.

**Query Parameters:**
- `user` (optional): Filter by user ID
- `action` (optional): Filter by action type
- `date` (optional): Filter by specific date (YYYY-MM-DD)
- `search` (optional): Search in description and details
- `page` (optional): Page number for pagination
- `limit` (optional): Number of records per page (default: 50)

**Response:**
```json
[
  {
    "id": 1,
    "user_id": 1,
    "user_name": "John Doe",
    "action": "login",
    "description": "User logged in successfully",
    "details": "Login from IP 192.168.1.100",
    "severity": "low",
    "ip_address": "192.168.1.100",
    "user_agent": "Mozilla/5.0...",
    "timestamp": "2024-01-15T10:30:00Z",
    "created_at": "2024-01-15T10:30:00Z"
  },
  {
    "id": 2,
    "user_id": 1,
    "user_name": "John Doe",
    "action": "watering_start",
    "description": "Started watering system",
    "details": "Manual watering for 3 minutes",
    "severity": "medium",
    "ip_address": "192.168.1.100",
    "user_agent": "Mozilla/5.0...",
    "timestamp": "2024-01-15T10:35:00Z",
    "created_at": "2024-01-15T10:35:00Z"
  },
  {
    "id": 3,
    "user_id": 2,
    "user_name": "Jane Smith",
    "action": "user_create",
    "description": "Created new user account",
    "details": "Created user: admin@example.com with role: manager",
    "severity": "high",
    "ip_address": "192.168.1.101",
    "user_agent": "Mozilla/5.0...",
    "timestamp": "2024-01-15T11:00:00Z",
    "created_at": "2024-01-15T11:00:00Z"
  }
]
```

### GET /api/audit-actions
Retrieves available audit action types.

**Response:**
```json
[
  "login",
  "logout",
  "watering_start",
  "watering_stop",
  "schedule_create",
  "schedule_delete",
  "settings_change",
  "user_create",
  "user_delete",
  "user_update",
  "permission_change",
  "system_error",
  "security_alert"
]
```

## User Management API

### GET /api/users
Retrieves users with filtering capabilities.

**Query Parameters:**
- `role` (optional): Filter by role
- `status` (optional): Filter by status (active, inactive, pending)
- `search` (optional): Search in name and email
- `page` (optional): Page number for pagination
- `limit` (optional): Number of records per page (default: 20)

**Response:**
```json
[
  {
    "id": 1,
    "name": "John Doe",
    "email": "john@example.com",
    "role": "admin",
    "status": "active",
    "permissions": ["dashboard_view", "watering_control", "user_management", "audit_logs"],
    "last_login": "2024-01-15T10:30:00Z",
    "created_at": "2024-01-01T00:00:00Z",
    "updated_at": "2024-01-15T10:30:00Z"
  },
  {
    "id": 2,
    "name": "Jane Smith",
    "email": "jane@example.com",
    "role": "manager",
    "status": "active",
    "permissions": ["dashboard_view", "watering_control", "sensor_history"],
    "last_login": "2024-01-15T09:15:00Z",
    "created_at": "2024-01-05T00:00:00Z",
    "updated_at": "2024-01-15T09:15:00Z"
  }
]
```

### POST /api/users
Creates a new user account.

**Request Body:**
```json
{
  "name": "New User",
  "email": "newuser@example.com",
  "password": "securepassword123",
  "role": "user",
  "permissions": ["dashboard_view", "watering_control"]
}
```

**Response:**
```json
{
  "success": true,
  "message": "User created successfully",
  "user": {
    "id": 3,
    "name": "New User",
    "email": "newuser@example.com",
    "role": "user",
    "status": "active",
    "permissions": ["dashboard_view", "watering_control"],
    "created_at": "2024-01-15T12:00:00Z"
  }
}
```

### PUT /api/users/{id}
Updates an existing user account.

**Request Body:**
```json
{
  "name": "Updated User",
  "email": "updated@example.com",
  "role": "manager",
  "password": "newpassword123",
  "permissions": ["dashboard_view", "watering_control", "sensor_history"]
}
```

**Response:**
```json
{
  "success": true,
  "message": "User updated successfully",
  "user": {
    "id": 3,
    "name": "Updated User",
    "email": "updated@example.com",
    "role": "manager",
    "status": "active",
    "permissions": ["dashboard_view", "watering_control", "sensor_history"],
    "updated_at": "2024-01-15T12:30:00Z"
  }
}
```

### DELETE /api/users/{id}
Deletes a user account.

**Response:**
```json
{
  "success": true,
  "message": "User deleted successfully"
}
```

### GET /api/roles
Retrieves available user roles.

**Response:**
```json
[
  "admin",
  "manager", 
  "user",
  "viewer"
]
```

## Database Schema

### Audit Logs Table
```sql
CREATE TABLE audit_logs (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    user_id BIGINT NULL,
    user_name VARCHAR(255) NOT NULL,
    action VARCHAR(50) NOT NULL,
    description TEXT NOT NULL,
    details TEXT NULL,
    severity ENUM('low', 'medium', 'high', 'critical') DEFAULT 'low',
    ip_address VARCHAR(45) NULL,
    user_agent TEXT NULL,
    timestamp TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    INDEX idx_user_id (user_id),
    INDEX idx_action (action),
    INDEX idx_severity (severity),
    INDEX idx_timestamp (timestamp),
    INDEX idx_ip_address (ip_address)
);
```

### Users Table
```sql
CREATE TABLE users (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    role ENUM('admin', 'manager', 'user', 'viewer') DEFAULT 'user',
    status ENUM('active', 'inactive', 'pending') DEFAULT 'active',
    permissions JSON NULL,
    last_login TIMESTAMP NULL,
    email_verified_at TIMESTAMP NULL,
    remember_token VARCHAR(100) NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    INDEX idx_email (email),
    INDEX idx_role (role),
    INDEX idx_status (status),
    INDEX idx_last_login (last_login)
);
```

### User Permissions Table
```sql
CREATE TABLE user_permissions (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    user_id BIGINT NOT NULL,
    permission VARCHAR(100) NOT NULL,
    granted_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    granted_by BIGINT NULL,
    
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (granted_by) REFERENCES users(id) ON DELETE SET NULL,
    UNIQUE KEY unique_user_permission (user_id, permission)
);
```

## Features Implemented:

### ✅ Audit Logs Features:
- **Comprehensive Logging**: Track all system activities, user actions, and security events
- **Multi-level Filtering**: Filter by user, action type, date, and search terms
- **Severity Classification**: Low, medium, high, and critical severity levels
- **Security Information**: IP addresses, user agents, and timestamps
- **Real-time Monitoring**: Immediate logging of all system events
- **Search Capability**: Full-text search across descriptions and details
- **Visual Indicators**: Color-coded severity and action types
- **Export Functionality**: Download audit logs for compliance

### ✅ User Management Features:
- **Role-based Access Control**: Admin, Manager, User, and Viewer roles
- **Granular Permissions**: Individual permission management for each user
- **User Status Management**: Active, inactive, and pending statuses
- **Account Creation**: Complete user registration with validation
- **Account Updates**: Edit user information, roles, and permissions
- **Account Deletion**: Secure user removal with confirmation
- **Last Login Tracking**: Monitor user activity and access patterns
- **Password Management**: Secure password handling and updates
- **Bulk Operations**: Manage multiple users efficiently

### ✅ Security Features:
- **Audit Trail**: Complete record of all user actions and system changes
- **Permission Validation**: Server-side permission checking for all operations
- **Session Management**: Track user sessions and login history
- **IP Address Logging**: Record user IP addresses for security monitoring
- **User Agent Tracking**: Monitor device and browser information
- **Failed Login Detection**: Identify potential security threats
- **Account Lockout**: Automatic account protection for suspicious activity

### ✅ User Experience:
- **Intuitive Interface**: Clean, modern design consistent with dashboard
- **Real-time Updates**: Immediate feedback for all user actions
- **Responsive Design**: Works seamlessly on all device sizes
- **Accessibility**: Proper ARIA labels and keyboard navigation
- **Loading States**: Clear indication of data loading and processing
- **Error Handling**: User-friendly error messages and recovery options
- **Confirmation Dialogs**: Prevent accidental deletions and changes

This implementation provides enterprise-grade user management and comprehensive audit logging for complete system security and compliance monitoring. 