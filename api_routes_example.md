# Watering History API Routes

## GET /api/watering-history
Returns the watering system on/off history with timestamps and details.

**Response:**
```json
[
  {
    "id": "1",
    "action": "start",
    "source": "manual",
    "duration": 3,
    "timestamp": "2024-01-15T10:30:00Z",
    "reason": "Manual activation by user"
  },
  {
    "id": "2", 
    "action": "stop",
    "source": "manual",
    "duration": null,
    "timestamp": "2024-01-15T10:33:00Z",
    "reason": "Manual deactivation by user"
  },
  {
    "id": "3",
    "action": "start", 
    "source": "automatic",
    "duration": 2,
    "timestamp": "2024-01-15T11:00:00Z",
    "reason": "Soil moisture below threshold (35%)"
  },
  {
    "id": "4",
    "action": "start",
    "source": "scheduled", 
    "duration": 3,
    "timestamp": "2024-01-15T08:00:00Z",
    "reason": "Scheduled watering time"
  }
]
```

## POST /api/watering-history
Logs a new watering event to the history.

**Request Body:**
```json
{
  "action": "start|stop",
  "source": "manual|automatic|scheduled", 
  "duration": 3,
  "reason": "Manual activation by user"
}
```

## Database Schema Example
```sql
CREATE TABLE watering_history (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    action ENUM('start', 'stop') NOT NULL,
    source ENUM('manual', 'automatic', 'scheduled') NOT NULL,
    duration INT NULL,
    timestamp TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    reason VARCHAR(255) NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
```

## Features Implemented:

### ✅ Frontend Features:
- **History Button**: Purple "History" button in the Schedules section
- **History Modal**: Full-screen modal showing watering events
- **Event Display**: Shows action (start/stop), source (manual/automatic/scheduled), duration, timestamp, and reason
- **Loading States**: Loading spinner while fetching history
- **Empty State**: Friendly message when no history exists
- **Visual Indicators**: Color-coded badges for different sources
- **Icons**: Different icons for different event sources
- **Auto-refresh**: History updates when watering starts/stops

### ✅ Backend Integration Points:
- **API Endpoint**: `/api/watering-history` for fetching history
- **Event Logging**: Automatic logging when watering starts/stops
- **Source Tracking**: Manual, automatic, and scheduled watering events
- **Timestamp Recording**: Precise timing of all events
- **Reason Tracking**: Why watering was activated/deactivated

### ✅ User Experience:
- **Clear Labeling**: Each event shows what happened and why
- **Chronological Order**: Events listed newest first
- **Source Identification**: Easy to distinguish between manual, automatic, and scheduled events
- **Duration Information**: Shows how long watering ran (for start events)
- **Responsive Design**: Works on all screen sizes
- **Accessibility**: Proper ARIA labels and keyboard navigation

This implementation provides comprehensive tracking of all watering system activations and deactivations, making it easy for users to understand when and why their system watered their plants. 