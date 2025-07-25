@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="flex items-center mb-6">
        <a href="{{ route('facility_reservations.index') }}" class="btn btn-ghost btn-sm mr-4">
            <i class="fas fa-arrow-left mr-2"></i>Back
        </a>
        <h1 class="text-3xl font-bold text-gray-800">Reservation Details</h1>
    </div>

    @if(session('success'))
        <div class="alert alert-success mb-4">
            {{ session('success') }}
        </div>
    @endif

    <div class="card bg-base-100 shadow-xl">
        <div class="card-body">
            <h2 class="card-title text-xl mb-4">
                {{ $reservation->facility->name ?? 'N/A' }}
            </h2>
            <div class="mb-2">
                <span class="font-semibold">Reserved By:</span>
                {{ $reservation->reserver->name ?? 'N/A' }}
            </div>
            <div class="mb-2">
                <span class="font-semibold">Start:</span>
                {{ \Carbon\Carbon::parse($reservation->start_time)->format('M d, Y H:i') }}
            </div>
            <div class="mb-2">
                <span class="font-semibold">End:</span>
                {{ \Carbon\Carbon::parse($reservation->end_time)->format('M d, Y H:i') }}
            </div>
            <div class="mb-2">
                <span class="font-semibold">Purpose:</span>
                {{ $reservation->purpose ?? '-' }}
            </div>
            <div class="mb-2">
                <span class="font-semibold">Status:</span>
                <span class="badge badge-{{ $reservation->status === 'pending' ? 'warning' : ($reservation->status === 'approved' ? 'success' : ($reservation->status === 'denied' ? 'error' : 'neutral')) }}">
                    {{ ucfirst($reservation->status) }}
                </span>
            </div>
            @if($reservation->status !== 'pending')
                <div class="mb-2">
                    <span class="font-semibold">{{ ucfirst($reservation->status) }} By:</span>
                    {{ $reservation->approver->name ?? 'N/A' }}
                </div>
                <div class="mb-2">
                    <span class="font-semibold">Remarks:</span>
                    {{ $reservation->remarks ?? '-' }}
                </div>
            @endif

            @if($reservation->status === 'pending')
                <div class="card-actions mt-4">
                    <form action="{{ route('facility_reservations.approve', $reservation->id) }}" method="POST" class="inline">
                        @csrf
                        <input type="text" name="remarks" class="input input-bordered input-sm mr-2" placeholder="Remarks (optional)">
                        <button type="submit" class="btn btn-success btn-sm">
                            <i class="fas fa-check mr-1"></i>Approve
                        </button>
                    </form>
                    <form action="{{ route('facility_reservations.deny', $reservation->id) }}" method="POST" class="inline">
                        @csrf
                        <input type="text" name="remarks" class="input input-bordered input-sm mr-2" placeholder="Remarks (optional)">
                        <button type="submit" class="btn btn-error btn-sm">
                            <i class="fas fa-times mr-1"></i>Deny
                        </button>
                    </form>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
