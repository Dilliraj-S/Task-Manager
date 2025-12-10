@extends('layouts.app')

@section('title')
Calendar
@endsection

@section('head')
<!-- FullCalendar v5 is already loaded in layout -->
<style>
.fc-event {
    cursor: pointer;
    font-size: 12px;
    border: none !important;
    border-radius: 4px;
}

.fc-event:hover {
    opacity: 0.8;
    transform: scale(1.02);
    transition: all 0.2s ease;
}

.calendar-controls {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    padding: 1.5rem;
    border-radius: 10px;
    margin-bottom: 1.5rem;
    box-shadow: 0 4px 15px rgba(102, 126, 234, 0.3);
}

.calendar-controls .btn-outline-primary {
    border-color: white;
    color: white;
}

.calendar-controls .btn-outline-primary:hover {
    background-color: white;
    color: #667eea;
}

.calendar-controls .btn-outline-primary.active {
    background-color: white;
    color: #667eea;
    border-color: white;
}

.calendar-controls .btn-primary {
    background-color: white;
    border-color: white;
    color: #667eea;
}

.calendar-controls .btn-outline-secondary {
    border-color: rgba(255,255,255,0.5);
    color: white;
}

.calendar-controls .btn-outline-secondary:hover {
    background-color: rgba(255,255,255,0.2);
    border-color: white;
}

.legend {
    display: flex;
    gap: 1rem;
    flex-wrap: wrap;
    margin-top: 1rem;
}

.legend-item {
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.legend-color {
    width: 16px;
    height: 16px;
    border-radius: 3px;
}

.fc-toolbar-title {
    font-size: 1.5rem !important;
}

.fc-button-primary {
    background-color: #0d6efd !important;
    border-color: #0d6efd !important;
}

.fc-button-primary:hover {
    background-color: #0b5ed7 !important;
    border-color: #0a58ca !important;
}

.quick-add-section {
    background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
    border: 1px solid #dee2e6;
    border-radius: 10px;
    padding: 1.5rem;
    margin-bottom: 1.5rem;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
}

.fc {
    border-radius: 10px;
    box-shadow: 0 4px 20px rgba(0,0,0,0.1);
    overflow: hidden;
    background: white;
}

.fc-header-toolbar {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white !important;
    padding: 1rem;
    margin-bottom: 0 !important;
}

.fc-toolbar-title {
    color: white !important;
}

.fc-button-primary {
    background-color: rgba(255,255,255,0.2) !important;
    border-color: rgba(255,255,255,0.3) !important;
    color: white !important;
}

.fc-button-primary:hover {
    background-color: rgba(255,255,255,0.3) !important;
    border-color: rgba(255,255,255,0.5) !important;
}

.fc-button-primary:not(:disabled).fc-button-active {
    background-color: white !important;
    color: #667eea !important;
}

.fc-daygrid-event {
    border-radius: 4px;
    font-size: 11px;
    font-weight: 500;
}

.fc-day-today {
    background-color: rgba(102, 126, 234, 0.1) !important;
}

.main-calendar-container {
    background: white;
    border-radius: 15px;
    box-shadow: 0 8px 30px rgba(0,0,0,0.1);
    overflow: hidden;
    padding: 0;
}
</style>
@endsection

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2>📅 Calendar</h2>
                <div class="btn-group" role="group">
                    <a href="{{ route('tasks.index') }}" class="btn btn-outline-primary">📋 My Tasks</a>
                    <a href="{{ route('reminders.create') }}" class="btn btn-outline-success">➕ Add Reminder</a>
                    <a href="{{ route('routines.create') }}" class="btn btn-outline-info">🔄 Add Routine</a>
                </div>
            </div>

            <!-- Calendar Controls -->
            <div class="calendar-controls">
                <div class="row align-items-center">
                    <div class="col-md-6">
                        <div class="btn-group" role="group">
                            <button type="button" id="monthView" class="btn btn-outline-primary {{ $view === 'month' ? 'active' : '' }}">Month</button>
                            <button type="button" id="weekView" class="btn btn-outline-primary {{ $view === 'week' ? 'active' : '' }}">Week</button>
                            <button type="button" id="dayView" class="btn btn-outline-primary {{ $view === 'day' ? 'active' : '' }}">Day</button>
                        </div>
                    </div>
                    <div class="col-md-6 text-md-end">
                        <button id="todayBtn" class="btn btn-primary">Today</button>
                        <button id="prevBtn" class="btn btn-outline-secondary">‹ Prev</button>
                        <button id="nextBtn" class="btn btn-outline-secondary">Next ›</button>
                    </div>
                </div>

                <!-- Legend -->
                <div class="legend">
                    <div class="legend-item">
                        <div class="legend-color" style="background-color: #dc3545;"></div>
                        <span>High Priority Tasks</span>
                    </div>
                    <div class="legend-item">
                        <div class="legend-color" style="background-color: #ffc107;"></div>
                        <span>Medium Priority Tasks</span>
                    </div>
                    <div class="legend-item">
                        <div class="legend-color" style="background-color: #6c757d;"></div>
                        <span>Low Priority Tasks</span>
                    </div>
                    <div class="legend-item">
                        <div class="legend-color" style="background-color: #dc3545;"></div>
                        <span>🔔 Reminders</span>
                    </div>
                    <div class="legend-item">
                        <div class="legend-color" style="background-color: #6f42c1;"></div>
                        <span>🔄 Routines</span>
                    </div>
                    <div class="legend-item">
                        <div class="legend-color" style="background-color: #28a745;"></div>
                        <span>🚀 Project Starts</span>
                    </div>
                    <div class="legend-item">
                        <div class="legend-color" style="background-color: #fd7e14;"></div>
                        <span>🎯 Project Deadlines</span>
                    </div>
                </div>
            </div>

            <!-- Quick Add Section -->
            <div class="quick-add-section">
                <h5>Quick Add</h5>
                <div class="row">
                    <div class="col-md-4">
                        <form id="quickTaskForm" class="d-flex gap-2">
                            @csrf
                            <input type="date" id="quickTaskDate" class="form-control form-control-sm" required>
                            <input type="text" id="quickTaskTitle" class="form-control form-control-sm" placeholder="Quick task..." required>
                            <button type="submit" class="btn btn-sm btn-primary">Add Task</button>
                        </form>
                    </div>
                    <div class="col-md-4">
                        <form id="quickReminderForm" class="d-flex gap-2">
                            @csrf
                            <input type="datetime-local" id="quickReminderDate" class="form-control form-control-sm" required>
                            <input type="text" id="quickReminderTitle" class="form-control form-control-sm" placeholder="Quick reminder..." required>
                            <button type="submit" class="btn btn-sm btn-success">Add Reminder</button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Calendar Container -->
            <div class="main-calendar-container">
                <div id="calendar"></div>
            </div>
        </div>
    </div>
</div>

<!-- Event Details Modal -->
<div class="modal fade" id="eventModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="eventModalTitle">Event Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="eventModalBody">
                <!-- Event details will be loaded here -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <a href="#" id="eventEditBtn" class="btn btn-primary" style="display: none;">Edit</a>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    var calendarEl = document.getElementById('calendar');
    var calendar = new FullCalendar.Calendar(calendarEl, {
        initialView: '{{ $view === "month" ? "dayGridMonth" : ($view === "week" ? "timeGridWeek" : "timeGridDay") }}',
        initialDate: '{{ $currentDate->format("Y-m-d") }}',
        headerToolbar: {
            left: '',
            center: 'title',
            right: ''
        },
        height: 600,
        contentHeight: 500,
        aspectRatio: 1.8,
        events: {
            url: '{{ route("calendar.events") }}',
            method: 'GET',
            extraParams: {
                _token: '{{ csrf_token() }}'
            },
            failure: function() {
                console.error('Error fetching calendar events');
                showErrorMessage('Failed to load calendar events. Please refresh the page.');
            }
        },
        eventSources: [{
            url: '{{ route("calendar.events") }}',
            method: 'GET',
            extraParams: {
                _token: '{{ csrf_token() }}'
            }
        }],
        eventClick: function(info) {
            showEventDetails(info.event);
            info.jsEvent.preventDefault();
        },
        dateClick: function(info) {
            // Set quick add forms to clicked date
            document.getElementById('quickTaskDate').value = info.dateStr;
            
            let datetime = new Date(info.date);
            datetime.setHours(9, 0); // Default to 9 AM
            document.getElementById('quickReminderDate').value = formatDatetimeLocal(datetime);
        },
        eventRender: function(info) {
            // Add tooltips (v5 syntax)
            info.el.title = info.event.title + 
                (info.event.extendedProps.project ? '\nProject: ' + info.event.extendedProps.project : '') +
                (info.event.extendedProps.assignee ? '\nAssignee: ' + info.event.extendedProps.assignee : '');
        },
        loading: function(bool) {
            if (bool) {
                document.getElementById('calendar').style.opacity = '0.5';
            } else {
                document.getElementById('calendar').style.opacity = '1';
            }
        }
    });

    calendar.render();

    // View switching
    document.getElementById('monthView').addEventListener('click', function() {
        calendar.changeView('dayGridMonth');
        updateActiveView('month');
    });

    document.getElementById('weekView').addEventListener('click', function() {
        calendar.changeView('timeGridWeek');
        updateActiveView('week');
    });

    document.getElementById('dayView').addEventListener('click', function() {
        calendar.changeView('timeGridDay');
        updateActiveView('day');
    });

    // Navigation
    document.getElementById('todayBtn').addEventListener('click', function() {
        calendar.today();
    });

    document.getElementById('prevBtn').addEventListener('click', function() {
        calendar.prev();
    });

    document.getElementById('nextBtn').addEventListener('click', function() {
        calendar.next();
    });

    // Quick forms
    document.getElementById('quickTaskForm').addEventListener('submit', function(e) {
        e.preventDefault();
        let date = document.getElementById('quickTaskDate').value;
        let title = document.getElementById('quickTaskTitle').value;
        
        fetch('{{ route("quick-add.task") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({
                title: title,
                due_date: date
            })
        }).then(response => response.json())
        .then(data => {
            if (data.success) {
                calendar.refetchEvents();
                document.getElementById('quickTaskTitle').value = '';
                showSuccessMessage('Task added successfully!');
            } else {
                showErrorMessage('Error adding task');
            }
        }).catch(error => {
            showErrorMessage('Error adding task');
        });
    });

    document.getElementById('quickReminderForm').addEventListener('submit', function(e) {
        e.preventDefault();
        let date = document.getElementById('quickReminderDate').value;
        let title = document.getElementById('quickReminderTitle').value;
        
        // Create quick reminder
        fetch('{{ route("quick-add.reminder") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({
                title: title,
                date: date.split('T')[0],
                time: date.split('T')[1],
                description: 'Quick reminder from calendar'
            })
        }).then(response => response.json())
        .then(data => {
            if (data.success) {
                calendar.refetchEvents();
                document.getElementById('quickReminderTitle').value = '';
                showSuccessMessage('Reminder added successfully!');
            } else {
                showErrorMessage('Error adding reminder');
            }
        }).catch(error => {
            showErrorMessage('Error adding reminder');
        });
    });

    function updateActiveView(view) {
        document.querySelectorAll('.btn-group .btn').forEach(btn => btn.classList.remove('active'));
        document.getElementById(view + 'View').classList.add('active');
    }

    function showEventDetails(event) {
        const modal = new bootstrap.Modal(document.getElementById('eventModal'));
        document.getElementById('eventModalTitle').textContent = event.title;
        
        let details = '<div class="event-details">';
        details += `<p><strong>Type:</strong> ${event.extendedProps.type}</p>`;
        
        if (event.extendedProps.project) {
            details += `<p><strong>Project:</strong> ${event.extendedProps.project}</p>`;
        }
        
        if (event.extendedProps.assignee) {
            details += `<p><strong>Assignee:</strong> ${event.extendedProps.assignee}</p>`;
        }
        
        if (event.extendedProps.priority) {
            details += `<p><strong>Priority:</strong> ${event.extendedProps.priority}</p>`;
        }
        
        if (event.extendedProps.status) {
            details += `<p><strong>Status:</strong> ${event.extendedProps.status.replace('_', ' ')}</p>`;
        }
        
        if (event.extendedProps.description) {
            details += `<p><strong>Description:</strong> ${event.extendedProps.description}</p>`;
        }
        
        details += `<p><strong>Date:</strong> ${event.start.toLocaleDateString()}`;
        if (event.start.getHours() !== 0 || event.start.getMinutes() !== 0) {
            details += ` at ${event.start.toLocaleTimeString()}`;
        }
        details += '</p>';
        
        details += '</div>';
        
        document.getElementById('eventModalBody').innerHTML = details;
        
        // Show edit button if URL is available
        if (event.extendedProps.url) {
            document.getElementById('eventEditBtn').style.display = 'inline-block';
            document.getElementById('eventEditBtn').href = event.extendedProps.url;
        } else {
            document.getElementById('eventEditBtn').style.display = 'none';
        }
        
        modal.show();
    }

    function formatDatetimeLocal(date) {
        return date.getFullYear() + '-' +
               String(date.getMonth() + 1).padStart(2, '0') + '-' +
               String(date.getDate()).padStart(2, '0') + 'T' +
               String(date.getHours()).padStart(2, '0') + ':' +
               String(date.getMinutes()).padStart(2, '0');
    }

    function showSuccessMessage(message) {
        const alert = document.createElement('div');
        alert.className = 'alert alert-success alert-dismissible fade show position-fixed';
        alert.style.top = '20px';
        alert.style.right = '20px';
        alert.style.zIndex = '9999';
        alert.innerHTML = `
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        `;
        document.body.appendChild(alert);
        
        setTimeout(() => {
            alert.remove();
        }, 5000);
    }

    function showErrorMessage(message) {
        const alert = document.createElement('div');
        alert.className = 'alert alert-danger alert-dismissible fade show position-fixed';
        alert.style.top = '20px';
        alert.style.right = '20px';
        alert.style.zIndex = '9999';
        alert.innerHTML = `
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        `;
        document.body.appendChild(alert);
        
        setTimeout(() => {
            alert.remove();
        }, 5000);
    }
});
</script>
@endsection