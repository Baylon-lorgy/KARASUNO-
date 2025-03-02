@extends('layouts.admindashboardlayout')

@section('title', 'Water Schedule - Rainwater Catch Basin')

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
</style>
<div class="container-fluid py-4">
    <!-- Header Card -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-body p-3">
                    <div class="row align-items-center">
                        <div class="col-md-8">
                            <div class="numbers">
                                <h5 class="font-weight-bolder mb-0">
                                    Water Schedule
                                    <span class="text-gradient text-primary">
                                        <i class="bi bi-calendar2-check"></i>
                                    </span>
                                </h5>
                                <p class="text-sm mb-0 text-muted">Manage your garden's watering schedule</p>
                            </div>
                        </div>
                        <div class="col-md-4 text-md-end mt-3 mt-md-0">
                            <button type="button" class="btn btn-primary btn-sm mb-0 d-flex align-items-center ms-auto" data-bs-toggle="modal" data-bs-target="#addScheduleModal">
                                <i class="bi bi-plus-lg me-2"></i>
                                New Schedule
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Alerts -->
    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <div class="d-flex align-items-center">
            <i class="bi bi-check-circle-fill me-2"></i>
            <span>{{ session('success') }}</span>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    <!-- Schedule Cards -->
    <div class="row">
        @foreach($todaySchedules as $schedule)
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card schedule-card h-100">
                <div class="card-body p-3">
                    <div class="schedule-status {{ strtolower($schedule['status']) }}">
                        <span class="badge bg-gradient-{{ $schedule['status'] === 'Completed' ? 'success' : ($schedule['status'] === 'Upcoming' ? 'info' : 'warning') }}">
                            {{ $schedule['status'] }}
                        </span>
                    </div>
                    <div class="schedule-time mt-3">
                        <h3 class="text-gradient text-primary mb-1">{{ $schedule['time'] }}</h3>
                        <p class="text-sm mb-0">
                            <i class="bi bi-clock me-1"></i>
                            Duration: {{ $schedule['duration'] }} minutes
                        </p>
                    </div>
                    <div class="schedule-zone mt-3">
                        <h6 class="text-sm mb-1">Zone</h6>
                        <p class="text-sm mb-0">
                            <i class="bi bi-geo-alt me-1"></i>
                            {{ $schedule['zone'] }}
                        </p>
                    </div>
                    <div class="schedule-actions mt-4">
                        <button type="button" class="btn btn-link text-primary px-3 mb-0" data-bs-toggle="modal" data-bs-target="#editScheduleModal">
                            <i class="bi bi-pencil me-2"></i>
                            Edit
                        </button>
                        <button type="button" class="btn btn-link text-danger px-3 mb-0" onclick="deleteSchedule(1)">
                            <i class="bi bi-trash me-2"></i>
                            Delete
                        </button>
                    </div>
                </div>
            </div>
        </div>
        @endforeach

        <!-- Empty State -->
        @if(empty($todaySchedules))
        <div class="col-12">
            <div class="card">
                <div class="card-body p-5 text-center">
                    <div class="icon icon-shape bg-gradient-primary shadow-primary text-white mx-auto mb-3" style="width: 64px; height: 64px; border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                        <i class="bi bi-calendar-x" style="font-size: 1.5rem;"></i>
                    </div>
                    <h5 class="text-gradient text-primary">No Schedules Yet</h5>
                    <p class="text-muted mb-4">Create your first watering schedule to get started</p>
                    <button type="button" class="btn btn-primary mb-0" data-bs-toggle="modal" data-bs-target="#addScheduleModal">
                        <i class="bi bi-plus-lg me-2"></i>
                        Add Schedule
                    </button>
                </div>
            </div>
        </div>
        @endif
    </div>
</div>

<!-- Add Schedule Modal -->
<div class="modal fade" id="addScheduleModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="bi bi-calendar-plus me-2"></i>
                    Add New Schedule
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('admin.water.schedule.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-4">
                        <label class="form-label">Time</label>
                        <div class="input-group">
                            <span class="input-group-text">
                                <i class="bi bi-clock"></i>
                            </span>
                            <input type="time" class="form-control" name="time" required>
                        </div>
                    </div>
                    <div class="mb-4">
                        <label class="form-label">Duration (minutes)</label>
                        <div class="input-group">
                            <span class="input-group-text">
                                <i class="bi bi-hourglass-split"></i>
                            </span>
                            <input type="number" class="form-control" name="duration" min="1" max="120" required>
                        </div>
                    </div>
                    <div class="mb-4">
                        <label class="form-label">Zone</label>
                        <div class="input-group">
                            <span class="input-group-text">
                                <i class="bi bi-geo-alt"></i>
                            </span>
                            <select class="form-select" name="zone_id" required>
                                <option value="" selected disabled>Select a zone...</option>
                                @foreach($zones as $zone)
                                <option value="{{ $zone['id'] }}">{{ $zone['name'] }} - {{ $zone['description'] }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-plus-lg me-2"></i>
                        Add Schedule
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Schedule Modal -->
<div class="modal fade" id="editScheduleModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="bi bi-pencil-square me-2"></i>
                    Edit Schedule
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="editScheduleForm" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="mb-4">
                        <label class="form-label">Time</label>
                        <div class="input-group">
                            <span class="input-group-text">
                                <i class="bi bi-clock"></i>
                            </span>
                            <input type="time" class="form-control" name="time" id="edit_time" required>
                        </div>
                    </div>
                    <div class="mb-4">
                        <label class="form-label">Duration (minutes)</label>
                        <div class="input-group">
                            <span class="input-group-text">
                                <i class="bi bi-hourglass-split"></i>
                            </span>
                            <input type="number" class="form-control" name="duration" id="edit_duration" min="1" max="120" required>
                        </div>
                    </div>
                    <div class="mb-4">
                        <label class="form-label">Zone</label>
                        <div class="input-group">
                            <span class="input-group-text">
                                <i class="bi bi-geo-alt"></i>
                            </span>
                            <select class="form-select" name="zone_id" id="edit_zone_id" required>
                                @foreach($zones as $zone)
                                <option value="{{ $zone['id'] }}">{{ $zone['name'] }} - {{ $zone['description'] }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-check-lg me-2"></i>
                        Update Schedule
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('styles')
<style>
/* Schedule Card Styles */
.schedule-card {
    border: none;
    border-radius: var(--border-radius);
    box-shadow: var(--shadow-sm);
    transition: var(--transition);
    background: rgba(255, 255, 255, 0.8);
    backdrop-filter: blur(6px);
}

.schedule-card:hover {
    transform: translateY(-5px);
    box-shadow: var(--shadow-lg);
}

.schedule-status {
    display: flex;
    justify-content: flex-end;
}

.schedule-time h3 {
    font-size: 1.75rem;
    font-weight: 600;
}

.schedule-zone {
    padding-top: 0.5rem;
    border-top: 1px solid rgba(0,0,0,0.05);
}

.schedule-actions {
    display: flex;
    justify-content: flex-end;
    gap: 1rem;
}

/* Modal Styles */
.modal-content {
    border: none;
    border-radius: var(--border-radius);
    box-shadow: var(--shadow-lg);
}

.modal-header {
    border-bottom: 1px solid rgba(0,0,0,0.05);
    padding: 1.5rem;
}

.modal-body {
    padding: 1.5rem;
}

.modal-footer {
    border-top: 1px solid rgba(0,0,0,0.05);
    padding: 1.5rem;
}

.input-group-text {
    background: none;
    border-right: none;
}

.input-group .form-control,
.input-group .form-select {
    border-left: none;
}

.input-group:focus-within {
    box-shadow: var(--shadow-sm);
}

/* Alert Styles */
.alert {
    border: none;
    border-radius: var(--border-radius);
    padding: 1rem;
    margin-bottom: 1rem;
    backdrop-filter: blur(6px);
}

.alert-success {
    background: rgba(40, 167, 69, 0.1);
    border: 1px solid rgba(40, 167, 69, 0.2);
    color: #28a745;
}

/* Empty State Styles */
.icon-shape {
    width: 64px;
    height: 64px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
}

/* Animation */
@keyframes fadeIn {
    from { opacity: 0; transform: translateY(10px); }
    to { opacity: 1; transform: translateY(0); }
}

.fade-in {
    animation: fadeIn 0.3s ease forwards;
}
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Auto-hide alerts after 5 seconds
    const alerts = document.querySelectorAll('.alert-dismissible');
    alerts.forEach(alert => {
        setTimeout(() => {
            const bsAlert = new bootstrap.Alert(alert);
            bsAlert.close();
        }, 5000);
    });

    // Add fade-in animation to schedule cards
    const scheduleCards = document.querySelectorAll('.schedule-card');
    scheduleCards.forEach((card, index) => {
        card.style.animation = `fadeIn 0.3s ease forwards ${index * 0.1}s`;
        card.style.opacity = '0';
    });
});

function deleteSchedule(id) {
    if (confirm('Are you sure you want to delete this schedule?')) {
        fetch(`{{ route('admin.water.schedule.destroy', '') }}/${id}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        }).then(response => {
            if (response.ok) {
                window.location.reload();
            }
        });
    }
}

// Show success message if exists
@if(session('success'))
    document.addEventListener('DOMContentLoaded', function() {
        const toast = document.createElement('div');
        toast.className = 'alert alert-success alert-dismissible fade show position-fixed bottom-0 end-0 m-3';
        toast.innerHTML = `
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        `;
        document.body.appendChild(toast);
        setTimeout(() => toast.remove(), 3000);
    });
@endif
</script>
@endpush 