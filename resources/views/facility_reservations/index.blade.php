@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold text-gray-800">Facility Reservations</h1>
        <a href="{{ route('facility_reservations.create') }}" class="btn btn-primary">
            <i class="fas fa-calendar-plus mr-2"></i>Reserve Facility
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success mb-4">
            {{ session('success') }}
        </div>
    @endif

    <div class="overflow-x-auto">
        <table class="table table-zebra w-full">
            <thead>
                <tr>
                    <th>Facility</th>
                    <th>Reserved By</th>
                    <th>Start</th>
                    <th>End</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($reservations as $reservation)
                    <tr>
                        <td>{{ $reservation->facility->name ?? 'N/A' }}</td>
                        <td>{{ $reservation->reserver->name ?? 'N/A' }}</td>
                        <td>{{ \Carbon\Carbon::parse($reservation->start_time)->format('M d, Y H:i') }}</td>
                        <td>{{ \Carbon\Carbon::parse($reservation->end_time)->format('M d, Y H:i') }}</td>
                        <td>
                            <span class="badge badge-{{ $reservation->status === 'pending' ? 'warning' : ($reservation->status === 'approved' ? 'success' : ($reservation->status === 'denied' ? 'error' : 'neutral')) }}">
                                {{ ucfirst($reservation->status) }}
                            </span>
                        </td>
                        <td>
                            <a href="{{ route('facility_reservations.show', $reservation->id) }}" class="btn btn-sm btn-outline">
                                <i class="fas fa-eye mr-1"></i>View
                            </a>
                            @if($reservation->status === 'pending')
                                <form action="{{ route('facility_reservations.approve', $reservation->id) }}" method="POST" style="display:inline;">
                                    @csrf
                                    <button type="submit" class="btn btn-success btn-sm">Approve</button>
                                </form>
                                <form action="{{ route('facility_reservations.deny', $reservation->id) }}" method="POST" style="display:inline;">
                                    @csrf
                                    <button type="submit" class="btn btn-error btn-sm">Deny</button>
                                </form>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center text-gray-500">No reservations found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
