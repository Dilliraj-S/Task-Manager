@extends('layouts.app')

@section('title')
    Calendar - Mobile View
@endsection

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-12">
                <h2>📱 Mobile Calendar</h2>

                <!-- Simple mobile-friendly calendar view -->
                <div class="mobile-calendar">
                    <div class="row mb-3">
                        <div class="col-6">
                            <button class="btn btn-outline-primary btn-sm w-100" onclick="changeMonth(-1)">‹ Previous</button>
                        </div>
                        <div class="col-6">
                            <button class="btn btn-outline-primary btn-sm w-100" onclick="changeMonth(1)">Next ›</button>
                        </div>
                    </div>

                    <div class="text-center mb-3">
                        <h4 id="currentMonth"></h4>
                    </div>

                    <!-- Events list view for mobile -->
                    <div id="mobileEventsList" class="list-group">
                        <!-- Events will be loaded here -->
                    </div>
                </div>

            </div>
        </div>
    </div>

    <script>
        let currentDate = new Date();

        function loadMobileEvents() {
            const startDate = new Date(currentDate.getFullYear(), currentDate.getMonth(), 1);
            const endDate = new Date(currentDate.getFullYear(), currentDate.getMonth() + 1, 0);

            document.getElementById('currentMonth').textContent =
                currentDate.toLocaleDateString('en-US', {
                    month: 'long',
                    year: 'numeric'
                });

            fetch(`{{ route('calendar.events') }}?start=${startDate.toISOString()}&end=${endDate.toISOString()}`)
                .then(response => response.json())
                .then(events => {
                    const eventsList = document.getElementById('mobileEventsList');
                    eventsList.innerHTML = '';

                    if (events.length === 0) {
                        eventsList.innerHTML =
                            '<div class="list-group-item text-center text-muted">No events this month</div>';
                        return;
                    }

                    events.forEach(event => {
                        const eventDate = new Date(event.start);
                        const listItem = document.createElement('div');
                        listItem.className = 'list-group-item list-group-item-action';
                        listItem.style.borderLeft = `4px solid ${event.backgroundColor}`;

                        listItem.innerHTML = `
                    <div class="d-flex w-100 justify-content-between">
                        <h6 class="mb-1">${event.title}</h6>
                        <small class="text-muted">${eventDate.toLocaleDateString()}</small>
                    </div>
                    ${event.extendedProps.project ? `<p class="mb-1 small">📁 ${event.extendedProps.project}</p>` : ''}
                    ${event.extendedProps.priority ? `<small class="text-muted">Priority: ${event.extendedProps.priority}</small>` : ''}
                `;

                        if (event.extendedProps.url) {
                            listItem.addEventListener('click', () => {
                                window.location.href = event.extendedProps.url;
                            });
                            listItem.style.cursor = 'pointer';
                        }

                        eventsList.appendChild(listItem);
                    });
                });
        }

        function changeMonth(direction) {
            currentDate.setMonth(currentDate.getMonth() + direction);
            loadMobileEvents();
        }

        // Load events when page loads
        document.addEventListener('DOMContentLoaded', loadMobileEvents);
    </script>
@endsection
