@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8 flex justify-center">
    <div class="card bg-base-100 shadow-xl w-full max-w-lg">
        <div class="card-body items-center text-center">
            <div class="avatar mb-4">
                <div class="w-24 rounded-full ring ring-primary ring-offset-base-100 ring-offset-2">
                    <img src="https://ui-avatars.com/api/?name={{ urlencode($user->name) }}&background=random" alt="Avatar" />
                </div>
            </div>
            <h2 class="card-title text-2xl mb-2">{{ $user->name }}</h2>
            <p class="text-gray-500 mb-2">{{ $user->email }}</p>
            <div class="badge badge-info mb-4">{{ ucfirst($user->role) }}</div>
            <div class="w-full text-left">
                <div class="mb-2"><span class="font-semibold">Department:</span> {{ $user->department ?? '-' }}</div>
                <div class="mb-2"><span class="font-semibold">Status:</span> <span class="badge badge-success">Active</span></div>
                <div class="mb-2"><span class="font-semibold">Joined:</span> {{ $user->created_at->format('M d, Y') }}</div>
                <div class="mb-2"><span class="font-semibold">Last Updated:</span> {{ $user->updated_at->format('M d, Y H:i') }}</div>
            </div>
            <div class="card-actions mt-6">
                <a href="{{ route('profile.edit') }}" class="btn btn-primary">Edit Profile</a>
            </div>
        </div>
    </div>
</div>
@endsection 