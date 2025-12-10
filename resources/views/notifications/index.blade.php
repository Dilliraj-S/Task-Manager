@extends('layouts.app')
@section('title')
    Notifications
@endsection
@section('content')
    <div class="container">
        <div class="d-flex justify-content-between align-items-center bg-white mb-4 shadow-sm p-3 rounded">
            <h2>Notifications</h2>
            @if($unreadCount > 0)
                <form action="{{ route('notifications.read-all') }}" method="POST" class="d-inline">
                    @csrf
                    <button type="submit" class="btn btn-primary btn-sm">
                        <i class="bi bi-check-all"></i> Mark all as read
                    </button>
                </form>
            @endif
</div>

        @if(session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif

        <div class="row">
            <div class="col-md-12">
                @forelse($notifications as $notification)
                    <div class="card mb-3 {{ $notification->read ? '' : 'border-primary' }}">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-start">
                                <div class="flex-grow-1">
                                    <div class="d-flex align-items-center mb-2">
                                        @if(!$notification->read)
                                            <span class="badge bg-primary me-2">New</span>
                                        @endif
                                        <h5 class="mb-0">{{ $notification->title }}</h5>
                                    </div>
                                    <p class="mb-2">{{ $notification->message }}</p>
                                    <small class="text-muted">
                                        <i class="bi bi-clock"></i> {{ $notification->created_at->diffForHumans() }}
                                        ({{ $notification->created_at->format('M d, Y h:i A') }})
                                    </small>
                                </div>
                                <div class="dropdown">
                                    <button class="btn btn-sm btn-link text-muted" type="button" 
                                        data-bs-toggle="dropdown">
                                        <i class="bi bi-three-dots-vertical"></i>
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-end">
                                        @if(!$notification->read)
                                            <li>
                                                <form action="{{ route('notifications.read', $notification->id) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    <button type="submit" class="dropdown-item">
                                                        <i class="bi bi-check"></i> Mark as read
                                                    </button>
                                                </form>
                                            </li>
                                        @endif
                                        @if($notification->link)
                                            <li>
                                                <a class="dropdown-item" href="{{ $notification->link }}">
                                                    <i class="bi bi-arrow-right"></i> View
                                                </a>
                                            </li>
                                        @endif
                                        <li>
                                            <form action="{{ route('notifications.destroy', $notification->id) }}" method="POST" class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="dropdown-item text-danger" 
                                                    onclick="return confirm('Are you sure you want to delete this notification?')">
                                                    <i class="bi bi-trash"></i> Delete
                                                </button>
                                            </form>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="card">
                        <div class="card-body text-center py-5">
                            <i class="bi bi-bell-slash fs-1 text-muted"></i>
                            <h4 class="mt-3">No notifications</h4>
                            <p class="text-muted">You're all caught up! No new notifications.</p>
                        </div>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
@endsection
