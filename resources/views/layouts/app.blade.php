<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title> @yield('title') | Task Manager </title>
    <link rel="shortcut icon" href="{{ asset('assets/img/logo-circle.png') }}" type="image/x-icon">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Noto+Sans:ital,wght@0,100..900;1,100..900&family=Roboto:ital,wght@0,100;0,300;0,400;0,500;0,700;0,900;1,100;1,300;1,400;1,500;1,700;1,900&display=swap"
        rel="stylesheet">
    <link rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.8.1/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/fullcalendar@5.10.1/main.min.css" />
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.10.1/main.min.js"></script>

    <style>
        :root {
            --brand-bg: #0f172a;
            --brand-surface: #111827;
            --brand-accent: #06b6d4;
            --brand-accent-2: #7c3aed;
            --text-primary: #e5e7eb;
            --text-muted: #9ca3af;
            --card-bg: #111827;
            --body-bg: #0b1020;
        }

        * {
            font-family: "Noto Sans", sans-serif !important;
        }

        body {
            display: flex;
            height: 100vh;
            margin: 0;
            overflow: hidden;
            background: radial-gradient(circle at 20% 20%, rgba(124, 58, 237, 0.15), transparent 35%),
                radial-gradient(circle at 80% 0%, rgba(6, 182, 212, 0.15), transparent 40%),
                var(--body-bg);
            color: var(--text-primary);
        }

        a {
            text-decoration: none;
        }

        .btn {
            padding: .45rem .85rem !important;
            font-size: .9rem !important;
            border-radius: 10px;
            border: none;
        }

        .btn-primary {
            background: linear-gradient(135deg, var(--brand-accent), var(--brand-accent-2));
            box-shadow: 0 10px 25px rgba(6, 182, 212, 0.25);
        }

        .btn-secondary {
            background: #1f2937;
            color: #e5e7eb;
        }

        .sidebar {
            width: 260px;
            background: linear-gradient(180deg, var(--brand-bg), var(--brand-surface));
            color: var(--text-primary);
            flex-shrink: 0;
            display: flex;
            flex-direction: column;
            border-right: 1px solid rgba(255, 255, 255, 0.05);
        }

        .sidebar .nav-link {
            color: var(--text-primary);
            display: flex;
            align-items: center;
            padding: 12px 14px;
            margin: 2px 8px;
            border-radius: 10px;
            transition: all 0.2s ease;
        }

        .sidebar .nav-link .bi {
            margin-right: 10px;
        }

        .sidebar .nav-link:hover,
        .sidebar .nav-link.active {
            background: rgba(6, 182, 212, 0.15);
            color: #e0f2fe;
        }

        .content {
            flex-grow: 1;
            display: flex;
            flex-direction: column;
            overflow-y: auto;
            background: transparent;
        }

        .topnav {
            flex-shrink: 0;
            width: 100%;
            background: rgba(17, 24, 39, 0.85);
            backdrop-filter: blur(6px);
            border-bottom: 1px solid rgba(255, 255, 255, 0.06);
        }

        .navbar-brand {
            font-weight: 700;
            color: #e0f2fe;
        }

        .navbar-nav .nav-link {
            color: var(--text-muted);
        }

        .navbar-nav .nav-link:hover {
            color: #e0f2fe;
        }

        .card {
            border: 1px solid rgba(255, 255, 255, 0.05);
            background: var(--card-bg);
            border-radius: 14px;
            color: var(--text-primary);
            box-shadow: 0 20px 45px rgba(0, 0, 0, 0.35);
        }

        /* Ensure readable text when a view uses a light surface */
        .bg-white,
        .bg-light,
        .card.bg-white,
        .card.bg-light {
            color: #0f172a;
        }

        .bg-white .card-title,
        .bg-light .card-title,
        .bg-white .card-text,
        .bg-light .card-text,
        .bg-white .form-label,
        .bg-light .form-label,
        .bg-white label,
        .bg-light label {
            color: #0f172a;
        }

        footer {
            background: rgba(17, 24, 39, 0.85);
            border-top: 1px solid rgba(255, 255, 255, 0.06);
            color: var(--text-muted);
            flex-shrink: 0;
        }

        main {
            flex-grow: 1;
        }

        .notification-dropdown .dropdown-item {
            padding: 0.75rem 1rem;
            white-space: normal;
        }

        .notification-item {
            border-left: 3px solid transparent;
            transition: all 0.2s;
        }

        .notification-item.unread {
            background-color: rgba(6, 182, 212, 0.1);
            border-left-color: #06b6d4;
        }

        .notification-item:hover {
            background-color: rgba(255, 255, 255, 0.05);
        }

        .notification-item.read {
            opacity: 0.7;
        }

        #notification-badge {
            font-size: 0.65rem;
            padding: 0.2em 0.4em;
        }

        /* Dark modals */
        .modal-content {
            background: #0f172a;
            color: #e5e7eb;
            border: 1px solid rgba(255,255,255,0.08);
        }
        .modal-header, .modal-footer {
            border-color: rgba(255,255,255,0.08);
        }
        .modal-title {
            color: #e5e7eb;
        }
        .modal-body label,
        .modal-body .form-label {
            color: #e5e7eb;
        }
        .modal-body .form-control,
        .modal-body .form-select,
        .modal-body textarea {
            background: #111827;
            color: #e5e7eb;
            border: 1px solid rgba(255,255,255,0.1);
        }
        .modal-body .form-control:focus,
        .modal-body .form-select:focus,
        .modal-body textarea:focus {
            border-color: #06b6d4;
            box-shadow: 0 0 0 0.2rem rgba(6,182,212,0.25);
        }
    </style>
</head>

<body>
    <div class="sidebar d-flex flex-column p-3">
        <h4 class="mb-4 text-center">
            <a href="{{ route('dashboard') }}">
                <span class="fw-bold fs-4 text-white">Task Manager</span>
            </a>
        </h4>
        <ul class="nav flex-column">
            <li class="nav-item">
                <a class="nav-link {{ request()->is('/') ? 'active' : '' }}" href="{{ route('dashboard') }}">
                    <i class="bi bi-house-door"></i> Home
                </a>
            </li>
            {{-- <li class="nav-item">
                <a class="nav-link {{ request()->is('mail*') ? 'active' : '' }}" href="{{ route('mail.inbox') }}">
                    <i class="bi bi-inbox"></i> Inbox
                </a>
            </li> --}}
            <li class="nav-item">
                <a class="nav-link {{ request()->is('projects*') ? 'active' : '' }}"
                    href="{{ route('projects.index') }}">
                    <i class="bi bi-folder"></i> Projects
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->is('tasks*') ? 'active' : '' }}" href="{{ route('tasks.index') }}">
                    <i class="bi bi-check2-square"></i> Tasks
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->is('routines*') ? 'active' : '' }}"
                    href="{{ route('routines.index') }}">
                    <i class="bi bi-calendar-check"></i> Routines
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->is('notes*') ? 'active' : '' }}" href="{{ route('notes.index') }}">
                    <i class="bi bi-sticky"></i> Notes
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->is('reminders*') ? 'active' : '' }}"
                    href="{{ route('reminders.index') }}">
                    <i class="bi bi-bell"></i> Reminders
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->is('files*') ? 'active' : '' }}" href="{{ route('files.index') }}">
                    <i class="bi bi-file"></i> Files
                </a>
            </li>
        </ul>
    </div>
    <div class="content d-flex flex-column">
        <header class="topnav mb-4">
            <nav class="navbar navbar-expand-lg navbar-light">
                <div class="container-fluid">
                    <a class="navbar-brand" href="{{ route('dashboard') }}">
                        <span class="fw-normal" id="currentDateTime"></span>
                    </a>
                    <button class="navbar-toggler" type="button" data-bs-toggle="collapse"
                        data-bs-target="#navbarNavDropdown" aria-controls="navbarNavDropdown" aria-expanded="false"
                        aria-label="Toggle navigation">
                        <span class="navbar-toggler-icon"></span>
                    </button>
                    <div class="collapse navbar-collapse justify-content-end" id="navbarNavDropdown">
                        <ul class="navbar-nav align-items-center">
                            <!-- Notifications Dropdown -->
                            <li class="nav-item dropdown me-3">
                                <a class="nav-link position-relative" href="#" id="notificationDropdown" role="button"
                                    data-bs-toggle="dropdown" aria-expanded="false">
                                    <i class="bi bi-bell fs-5"></i>
                                    <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" 
                                        id="notification-badge" style="display: none;">
                                        0
                                    </span>
                                </a>
                                <ul class="dropdown-menu dropdown-menu-end notification-dropdown" 
                                    aria-labelledby="notificationDropdown" style="width: 350px; max-height: 500px; overflow-y: auto;">
                                    <li class="dropdown-header d-flex justify-content-between align-items-center">
                                        <span>Notifications</span>
                                        <button class="btn btn-sm btn-link text-decoration-none p-0" 
                                            id="mark-all-read-btn" style="font-size: 0.75rem;">
                                            Mark all as read
                                        </button>
                                    </li>
                                    <li><hr class="dropdown-divider"></li>
                                    <div id="notifications-list">
                                        <li class="px-3 py-4 text-center text-muted">
                                            <i class="bi bi-hourglass-split"></i> Loading...
                                        </li>
                                    </div>
                                    <li><hr class="dropdown-divider"></li>
                                    <li class="text-center">
                                        <a class="dropdown-item" href="{{ route('notifications.index') }}">
                                            View all notifications
                                        </a>
                                    </li>
                                </ul>
                            </li>
                            
                            <!-- User Dropdown -->
                            <li class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button"
                                    data-bs-toggle="dropdown" aria-expanded="false">
                                    @php
                                        $avatarUrl = Auth::user()->avatar ? Storage::url(Auth::user()->avatar) : null;
                                    @endphp
                                    @if($avatarUrl)
                                        <img src="{{ $avatarUrl }}" alt="avatar" class="rounded-circle me-2" style="width:32px;height:32px;object-fit:cover;border:1px solid rgba(255,255,255,0.2);">
                                    @else
                                        <span class="avatar-placeholder me-2 rounded-circle d-inline-flex align-items-center justify-content-center"
                                            style="width:32px;height:32px;background:#1f2937;color:#e5e7eb;font-weight:700;">
                                            {{ strtoupper(substr(Auth::user()->name,0,1)) }}
                                        </span>
                                    @endif
                                    {{ Auth::user()->name }}
                                </a>
                                <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
                                    <li><a class="dropdown-item" href="{{ route('settings.edit') }}">Settings</a></li>
                                    <li>
                                        <form method="POST" action="{{ route('logout') }}" id="logout-form">
                                            @csrf
                                            <button type="submit" class="dropdown-item">Logout</button>
                                        </form>
                                    </li>
                                </ul>
                            </li>
                        </ul>
                    </div>
                </div>
            </nav>
        </header>
        <main>
            @yield('content')
        </main>
        <footer class="mt-auto py-3 text-center">
            <div class="container">
                <span class="text-white">&copy; {{ date('Y') }} Built by Dilliraj</span>
            </div>
        </footer>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function updateDateTime() {
            const now = new Date();
            const dayNames = ["Sunday", "Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday"];
            const day = dayNames[now.getDay()];
            const date = now.toLocaleDateString(['en-US'], {
                day: 'numeric',
                month: 'long',
                year: 'numeric'
            });
            const time = now.toLocaleTimeString();

            document.getElementById('currentDateTime').innerText = `${day}, ${date}  ${time}`;
        }

        updateDateTime();
        setInterval(updateDateTime, 1000);

        // Notifications System
        let notificationCheckInterval;
        let lastNotificationCheck = null;

        function loadNotifications() {
            fetch('{{ route('notifications.index') }}', {
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => response.json())
            .then(data => {
                updateNotificationBadge(data.unread_count);
                renderNotifications(data.notifications);
                lastNotificationCheck = new Date();
            })
            .catch(error => {
                console.error('Error loading notifications:', error);
            });
        }

        function updateNotificationBadge(count) {
            const badge = document.getElementById('notification-badge');
            if (count > 0) {
                badge.textContent = count > 99 ? '99+' : count;
                badge.style.display = 'block';
            } else {
                badge.style.display = 'none';
            }
        }

        function renderNotifications(notifications) {
            const list = document.getElementById('notifications-list');
            
            if (notifications.length === 0) {
                list.innerHTML = `
                    <li class="px-3 py-4 text-center text-muted">
                        <i class="bi bi-bell-slash fs-4"></i>
                        <p class="mb-0 mt-2">No notifications</p>
                    </li>
                `;
                return;
            }

            list.innerHTML = notifications.slice(0, 10).map(notif => {
                const readClass = notif.read ? 'read' : 'unread';
                const iconMap = {
                    'task_assigned': 'bi-check-circle text-primary',
                    'task_comment': 'bi-chat-dots text-info',
                    'task_updated': 'bi-pencil text-warning',
                    'project_invite': 'bi-person-plus text-success',
                };
                const icon = iconMap[notif.type] || 'bi-bell text-secondary';
                
                return `
                    <li class="notification-item ${readClass}">
                        <a class="dropdown-item d-flex align-items-start" 
                           href="${notif.link || '#'}" 
                           data-notification-id="${notif.id}">
                            <div class="flex-shrink-0 me-2">
                                <i class="bi ${icon}"></i>
                            </div>
                            <div class="flex-grow-1">
                                <div class="fw-bold">${notif.title}</div>
                                <div class="small text-muted">${notif.message}</div>
                                <div class="small text-muted mt-1">${notif.created_at}</div>
                            </div>
                            ${!notif.read ? '<span class="badge bg-primary rounded-pill" style="font-size: 0.5rem;">New</span>' : ''}
                        </a>
                    </li>
                `;
            }).join('');

            // Add click handlers for marking as read
            list.querySelectorAll('a[data-notification-id]').forEach(link => {
                link.addEventListener('click', function(e) {
                    const notificationId = this.dataset.notificationId;
                    if (!this.closest('.notification-item').classList.contains('read')) {
                        markNotificationAsRead(notificationId);
                    }
                });
            });
        }

        function markNotificationAsRead(notificationId) {
            fetch(`/notifications/${notificationId}/read`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    updateNotificationBadge(data.unread_count);
                    const item = document.querySelector(`[data-notification-id="${notificationId}"]`).closest('.notification-item');
                    if (item) {
                        item.classList.remove('unread');
                        item.classList.add('read');
                        item.querySelector('.badge')?.remove();
                    }
                }
            })
            .catch(error => console.error('Error marking notification as read:', error));
        }

        function markAllAsRead() {
            fetch('{{ route('notifications.read-all') }}', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    updateNotificationBadge(0);
                    loadNotifications();
                }
            })
            .catch(error => console.error('Error marking all as read:', error));
        }

        // Initialize notifications
        document.addEventListener('DOMContentLoaded', function() {
            loadNotifications();
            
            // Check for new notifications every 30 seconds
            notificationCheckInterval = setInterval(loadNotifications, 30000);
            
            // Mark all as read button
            const markAllBtn = document.getElementById('mark-all-read-btn');
            if (markAllBtn) {
                markAllBtn.addEventListener('click', function(e) {
                    e.preventDefault();
                    markAllAsRead();
                });
            }

            // Request browser notification permission
            if ('Notification' in window && Notification.permission === 'default') {
                Notification.requestPermission();
            }
        });

        // Show browser notification when new notification arrives
        function showBrowserNotification(title, message, link) {
            if ('Notification' in window && Notification.permission === 'granted') {
                const notification = new Notification(title, {
                    body: message,
                    icon: '{{ asset('assets/img/logo-circle.png') }}',
                    badge: '{{ asset('assets/img/logo-circle.png') }}'
                });

                notification.onclick = function() {
                    window.focus();
                    if (link) {
                        window.location.href = link;
                    }
                    notification.close();
                };
            }
        }
    </script>
</body>

</html>
