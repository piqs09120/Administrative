@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="flex justify-between items-center mb-4">
        <h1 class="text-3xl font-bold">All Notifications</h1>
        @if(Auth::user() && Auth::user()->unreadNotifications->count() > 0)
            <form method="POST" action="{{ route('notifications.markAllAsRead') }}">
                @csrf
                <button type="submit" class="btn btn-link btn-sm text-primary">Mark all as read</button>
            </form>
        @endif
    </div>
    <div class="card bg-base-100 shadow-xl">
        <div class="card-body">
            <div class="space-y-2">
                @forelse($notifications as $notification)
                    <div class="alert {{ $notification->read_at ? 'alert-secondary' : 'alert-info' }} py-2 flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            @php
                                $icon = 'fa-info-circle';
                                if(isset($notification->data['type'])) {
                                    if($notification->data['type'] === 'approved') $icon = 'fa-check-circle';
                                    elseif($notification->data['type'] === 'denied') $icon = 'fa-times-circle';
                                    elseif($notification->data['type'] === 'warning') $icon = 'fa-exclamation-triangle';
                                }
                            @endphp
                            <i class="fas {{ $icon }}"></i>
                            @if(isset($notification->data['url']))
                                <a href="{{ $notification->data['url'] }}" class="text-sm font-semibold hover:underline">
                                    {!! $notification->data['remarks'] ?? $notification->data['status'] ?? 'You have a new notification.' !!}
                                </a>
                            @else
                                <span class="text-sm">{!! $notification->data['remarks'] ?? $notification->data['status'] ?? 'You have a new notification.' !!}</span>
                            @endif
                            <span class="ml-2 text-xs text-gray-400">{{ $notification->created_at->diffForHumans() }}</span>
                        </div>
                        <div class="flex gap-2 items-center">
                            @if(!$notification->read_at)
                                <form method="POST" action="{{ route('notifications.markAsRead', $notification->id) }}">
                                    @csrf
                                    <button type="submit" class="btn btn-xs btn-link text-primary">Mark as read</button>
                                </form>
                            @endif
                        </div>
                    </div>
                @empty
                    <div class="text-center text-gray-400 text-sm py-4">No notifications</div>
                @endforelse
            </div>
            <div class="mt-4">
                {{ $notifications->links() }}
            </div>
        </div>
    </div>
</div>
@endsection 