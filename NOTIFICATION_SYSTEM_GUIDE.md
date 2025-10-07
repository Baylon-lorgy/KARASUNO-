# Comprehensive Notification System Guide

## Overview
The RainBasin Pro system includes a comprehensive notification system that covers all aspects of the smart garden management platform. This guide outlines all notification types, their priorities, and usage examples.

## Notification Categories

### 1. Staff Management Notifications

#### Staff Invitation Events
- **`staff_invitation_pending`** - When a staff invitation is sent and pending approval
- **`staff_invitation_approved`** - When a staff invitation is approved
- **`staff_invitation_rejected`** - When a staff invitation is rejected
- **`staff_account_activated`** - When a staff account is activated
- **`staff_account_deactivated`** - When a staff account is deactivated

**Usage Example:**
```javascript
// In your staff invitation controller
await handleStaffInvitationEvent('pending', {
    email: 'staff@example.com',
    name: 'John Doe',
    role: 'manager'
});
```

### 2. Watering System Notifications

#### Manual Watering Events
- **`watering_started`** - When manual watering is initiated
- **`watering_stopped`** - When manual watering is stopped

#### Scheduled Watering Events
- **`watering_schedule_started`** - When a scheduled watering begins
- **`watering_schedule_stopped`** - When a scheduled watering stops

**Usage Example:**
```javascript
// In your watering controller
await handleWateringEvent('schedule_started', {
    name: 'Morning Schedule',
    id: 'schedule_123',
    duration: '30 minutes'
});
```

### 3. Schedule Management Notifications

#### Schedule CRUD Events
- **`schedule_created`** - When a new watering schedule is created
- **`schedule_modified`** - When an existing schedule is modified
- **`schedule_deleted`** - When a schedule is deleted

**Usage Example:**
```javascript
// In your schedule controller
await handleScheduleEvent('created', {
    name: 'Evening Watering',
    id: 'schedule_456',
    time: '18:00',
    days: ['Monday', 'Wednesday', 'Friday']
});
```

### 4. System Health Notifications

#### Water Level Alerts
- **`water_level_low`** - When water level drops below threshold (20-30%)
- **`water_level_critical`** - When water level is critically low (<20%)

#### Sensor Status
- **`sensor_offline`** - When a sensor goes offline
- **`sensor_online`** - When a sensor comes back online

#### Maintenance Events
- **`maintenance_due`** - When system maintenance is due
- **`maintenance_completed`** - When maintenance is completed

**Usage Example:**
```javascript
// In your sensor monitoring system
await handleSystemEvent('water_level_critical', {
    level: 15,
    sensor_id: 'water_level_001',
    location: 'Main Tank'
});
```

### 5. Environmental Notifications

#### Weather & Power
- **`weather_alert`** - Weather-related alerts (rain, storm, etc.)
- **`power_outage`** - Power outage detection

**Usage Example:**
```javascript
// In your weather monitoring system
await handleSystemEvent('weather_alert', {
    description: 'Heavy rain expected - watering suspended',
    severity: 'medium',
    duration: '2 hours'
});
```

### 6. System Operations Notifications

#### Backup & Updates
- **`backup_completed`** - When system backup completes successfully
- **`backup_failed`** - When backup fails
- **`update_available`** - When system update is available
- **`update_installed`** - When update is installed

#### Data Operations
- **`data_export_completed`** - When data export completes
- **`data_export_failed`** - When data export fails

**Usage Example:**
```javascript
// In your backup system
await handleSystemEvent('backup_completed', {
    size: '2.5GB',
    duration: '15 minutes',
    timestamp: new Date().toISOString()
});
```

### 7. Security & Compliance Notifications

#### Security Alerts
- **`security_alert`** - General security alerts
- **`audit_log_alert`** - Critical security events from audit logs

#### Compliance Reports
- **`compliance_report_due`** - When compliance report is due
- **`compliance_report_generated`** - When compliance report is generated

**Usage Example:**
```javascript
// In your audit system
await handleSystemEvent('audit_log_alert', {
    description: 'Multiple failed login attempts detected',
    severity: 'high',
    ip_address: '192.168.1.100'
});
```

### 8. Legacy Notifications (Maintained for Compatibility)

#### Sensor & System
- **`sensor_threshold`** - Sensor threshold alerts
- **`water_availability`** - Water availability status
- **`system_alert`** - General system alerts
- **`system_info`** - General system information
- **`user_activity`** - User activity notifications

## Notification Priorities

### Priority Levels
1. **`critical`** - Requires immediate attention (red, pulsing)
2. **`high`** - Important but not urgent (red)
3. **`medium`** - Moderate importance (yellow/orange)
4. **`low`** - Informational (blue/green)

### Priority Mapping
```javascript
const priorityMapping = {
    critical: ['power_outage', 'audit_log_alert'],
    high: ['water_level_critical', 'sensor_offline', 'staff_account_deactivated', 'backup_failed'],
    medium: ['water_level_low', 'maintenance_due', 'weather_alert', 'staff_invitation_pending'],
    low: ['watering_started', 'schedule_created', 'backup_completed', 'update_installed']
};
```

## Notification Display Features

### Visual Indicators
- **Icons**: Each notification type has a specific icon
- **Colors**: Priority-based color coding
- **Animations**: Critical notifications pulse
- **Badge**: Unread notification count

### Notification Actions
- **Mark as read**: Individual and bulk actions
- **Delete**: Remove notifications
- **Refresh**: Manual refresh of notifications
- **Settings**: Configure notification preferences

## Integration Examples

### 1. Staff Invitation Flow
```javascript
// When sending invitation
await handleStaffInvitationEvent('pending', {
    email: 'newstaff@company.com',
    name: 'Jane Smith',
    role: 'technician'
});

// When admin approves
await handleStaffInvitationEvent('approved', {
    email: 'newstaff@company.com',
    name: 'Jane Smith',
    role: 'technician'
});
```

### 2. Watering Schedule Flow
```javascript
// When schedule is created
await handleScheduleEvent('created', {
    name: 'Daily Morning',
    id: 'schedule_789',
    time: '06:00',
    duration: '45 minutes'
});

// When schedule starts
await handleWateringEvent('schedule_started', {
    name: 'Daily Morning',
    id: 'schedule_789',
    duration: '45 minutes'
});
```

### 3. System Monitoring Flow
```javascript
// Monitor water level
if (waterLevel < 20) {
    await handleSystemEvent('water_level_critical', {
        level: waterLevel,
        sensor_id: 'main_tank',
        location: 'Main Water Tank'
    });
}

// Monitor sensor status
if (!sensorOnline) {
    await handleSystemEvent('sensor_offline', {
        sensor_id: 'temp_sensor_001',
        location: 'Greenhouse A',
        last_seen: sensorLastSeen
    });
}
```

## Email Notification Integration

### Automatic Email Triggers
Notifications with priority `medium`, `high`, or `critical` automatically trigger email notifications.

### Email Templates
Each notification type can have custom email templates:
- **Staff invitations**: Professional invitation emails
- **System alerts**: Technical alert emails
- **Maintenance**: Maintenance reminder emails
- **Security**: Security alert emails

## Real-time Features

### WebSocket Integration
- Real-time notification delivery
- Live notification count updates
- Instant badge updates

### Push Notifications
- Browser push notifications for critical alerts
- Mobile app notifications (if applicable)
- Desktop notifications

## Configuration Options

### Notification Settings
Users can configure:
- **Email preferences**: Which notifications to receive via email
- **Real-time preferences**: Which notifications to show in real-time
- **Sound alerts**: Enable/disable notification sounds
- **Desktop notifications**: Enable/disable browser notifications

### Admin Controls
- **Global notification settings**: System-wide notification configuration
- **Notification templates**: Customize notification messages
- **Priority thresholds**: Adjust when notifications are sent
- **Retention policies**: How long to keep notifications

## Best Practices

### 1. Notification Timing
- Avoid notification spam
- Group related notifications
- Use appropriate priorities
- Consider user time zones

### 2. Message Clarity
- Use clear, actionable messages
- Include relevant context
- Provide next steps when appropriate
- Use consistent terminology

### 3. Performance
- Batch notifications when possible
- Use efficient database queries
- Implement proper indexing
- Monitor notification system performance

### 4. Security
- Sanitize notification content
- Validate notification data
- Implement rate limiting
- Audit notification access

## Future Enhancements

### Planned Features
1. **Notification channels**: Slack, Teams, Discord integration
2. **Advanced filtering**: Filter notifications by type, priority, date
3. **Notification analytics**: Track notification engagement
4. **Custom notification rules**: User-defined notification triggers
5. **Notification scheduling**: Schedule notifications for specific times
6. **Multi-language support**: Internationalized notification messages

### Integration Opportunities
1. **IoT device notifications**: Direct device status updates
2. **Weather service integration**: Real-time weather alerts
3. **Maintenance scheduling**: Automated maintenance reminders
4. **Compliance automation**: Automated compliance reporting
5. **Security monitoring**: Enhanced security alert system

This comprehensive notification system ensures that all stakeholders are informed of important events in the RainBasin Pro system, from staff management to system health monitoring. 