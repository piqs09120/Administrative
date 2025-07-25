@extends('layouts.app')

@section('title', 'Edit User Role')
@section('page-title', 'Edit User Role')

@section('content')
<div class="container mx-auto px-4 py-8 max-w-lg">
    <div class="card bg-base-100 shadow-xl">
        <div class="card-body">
            <h2 class="card-title mb-4">Edit Role for {{ $user->name }}</h2>
            <form method="POST" action="{{ route('access.users.updateRole', $user->id) }}">
                @csrf
                <div class="form-control mb-4">
                    <label class="label font-semibold">Role</label>
                    <select name="role" class="select select-bordered w-full" required>
                        @foreach($roles as $role)
                            <option value="{{ $role }}" @if($user->role == $role) selected @endif>{{ $role }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="flex justify-end gap-2">
                    <a href="{{ route('access.users') }}" class="btn btn-outline">Cancel</a>
                    <button type="submit" class="btn btn-primary">Update Role</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection 