@extends('layouts.dashboard')

@section('title', __('Notifications'))

@section('content')
<div class="row">
    <div class="col-12">
        <div class="dashboard-card">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h4 class="mb-0">
                    <i class="fas fa-bell me-2"></i>{{ __('Notifications') }}
                </h4>
                <div>
                    @if(auth()->user()->unreadNotifications()->count() > 0)
                        <button type="button" class="btn btn-outline-primary btn-sm" onclick="markAllAsRead()">
                            <i class="fas fa-check-double me-1"></i>{{ __('Mark All as Read') }}
                        </button>
                    @endif
                </div>
            </div>

            @if($notifications->count() > 0)
                <div class="list-group list-group-flush">
                    @foreach($notifications as $notification)
                        <div class="list-group-item notification-item {{ $notification->read_at ? 'read' : 'unread' }}" 
                             data-notification-id="{{ $notification->id }}">
                            <div class="d-flex align-items-start">
                                <div class="notification-icon me-3">
                                    @if($notification->data['type'] ?? '' == 'offer')
                                        <i class="fas fa-tags text-primary"></i>
                                    @elseif($notification->data['type'] ?? '' == 'coupon')
                                        <i class="fas fa-ticket-alt text-success"></i>
                                    @elseif($notification->data['type'] ?? '' == 'transaction')
                                        <i class="fas fa-exchange-alt text-info"></i>
                                    @elseif($notification->data['type'] ?? '' == 'loyalty')
                                        <i class="fas fa-star text-warning"></i>
                                    @else
                                        <i class="fas fa-bell text-secondary"></i>
                                    @endif
                                </div>
                                <div class="flex-grow-1">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <div>
                                            <h6 class="mb-1">{{ $notification->data['title'] ?? __('Notification') }}</h6>
                                            <p class="mb-1 text-muted">{{ $notification->data['message'] ?? '' }}</p>
                                            <small class="text-muted">
                                                <i class="fas fa-clock me-1"></i>{{ $notification->created_at->diffForHumans() }}
                                            </small>
                                        </div>
                                        <div class="notification-actions">
                                            @if(!$notification->read_at)
                                                <button type="button" class="btn btn-sm btn-link text-success" 
                                                        onclick="markAsRead('{{ $notification->id }}')" 
                                                        title="{{ __('Mark as Read') }}">
                                                    <i class="fas fa-check"></i>
                                                </button>
                                            @endif
                                            <button type="button" class="btn btn-sm btn-link text-danger" 
                                                    onclick="deleteNotification('{{ $notification->id }}')" 
                                                    title="{{ __('Delete') }}">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="d-flex justify-content-center mt-4">
                    {{ $notifications->links() }}
                </div>
            @else
                <div class="text-center py-5">
                    <i class="fas fa-bell fa-3x text-muted mb-3"></i>
                    <h5 class="text-muted">{{ __('No notifications found') }}</h5>
                    <p class="text-muted">{{ __('You don\'t have any notifications at the moment.') }}</p>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function markAsRead(notificationId) {
        fetch(`/notifications/${notificationId}/read`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const notificationElement = document.querySelector(`[data-notification-id="${notificationId}"]`);
                notificationElement.classList.remove('unread');
                notificationElement.classList.add('read');
                
                // Remove the mark as read button
                const markReadButton = notificationElement.querySelector('.text-success');
                if (markReadButton) {
                    markReadButton.remove();
                }
                
                showNotification(data.message, 'success');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showNotification('{{ __('An error occurred. Please try again.') }}', 'error');
        });
    }

    function markAllAsRead() {
        if (confirm('{{ __('Are you sure you want to mark all notifications as read?') }}')) {
            fetch('{{ route("notifications.read-all") }}', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Reload the page to reflect changes
                    location.reload();
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showNotification('{{ __('An error occurred. Please try again.') }}', 'error');
            });
        }
    }

    function deleteNotification(notificationId) {
        if (confirm('{{ __('Are you sure you want to delete this notification?') }}')) {
            fetch(`/notifications/${notificationId}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const notificationElement = document.querySelector(`[data-notification-id="${notificationId}"]`);
                    notificationElement.remove();
                    showNotification(data.message, 'success');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showNotification('{{ __('An error occurred. Please try again.') }}', 'error');
            });
        }
    }
</script>

<style>
.notification-item {
    border-left: 3px solid transparent;
    transition: all 0.3s ease;
}

.notification-item.unread {
    border-left-color: #007bff;
    background-color: rgba(0, 123, 255, 0.05);
}

.notification-item:hover {
    background-color: rgba(0, 0, 0, 0.02);
}

.notification-icon {
    font-size: 1.2rem;
    width: 40px;
    height: 40px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 50%;
    background-color: rgba(0, 0, 0, 0.05);
}

.notification-item.unread .notification-icon {
    background-color: rgba(0, 123, 255, 0.1);
}

.notification-actions {
    opacity: 0;
    transition: opacity 0.3s ease;
}

.notification-item:hover .notification-actions {
    opacity: 1;
}

[dir="rtl"] .notification-item {
    border-left: none;
    border-right: 3px solid transparent;
}

[dir="rtl"] .notification-item.unread {
    border-right-color: #007bff;
}
</style>
@endpush