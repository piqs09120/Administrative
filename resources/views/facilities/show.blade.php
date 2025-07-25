@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="flex items-center mb-6">
        <a href="{{ route('facilities.index') }}" class="btn btn-ghost btn-sm mr-4">
            <i class="fas fa-arrow-left mr-2"></i>Back
        </a>
        <h1 class="text-3xl font-bold text-gray-800">Facility Details</h1>
    </div>

    @if(session('success'))
        <div class="alert alert-success mb-4">
            {{ session('success') }}
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Facility Information -->
        <div class="lg:col-span-2">
            <div class="card bg-base-100 shadow-xl">
                <div class="card-body">
                    <div class="flex justify-between items-start mb-4">
                        <h2 class="card-title text-2xl">{{ $facility->name }}</h2>
                        <div class="badge badge-lg badge-{{ $facility->status === 'available' ? 'success' : 'error' }}">
                            {{ ucfirst($facility->status) }}
                        </div>
                    </div>

                    @if($facility->location)
                        <div class="mb-4">
                            <h3 class="font-semibold text-gray-700 mb-2">Location</h3>
                            <p class="text-gray-600">
                                <i class="fas fa-map-marker-alt mr-2"></i>{{ $facility->location }}
                            </p>
                        </div>
                    @endif

                    @if($facility->description)
                        <div class="mb-4">
                            <h3 class="font-semibold text-gray-700 mb-2">Description</h3>
                            <p class="text-gray-600">{{ $facility->description }}</p>
                        </div>
                    @endif

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                        <div>
                            <h3 class="font-semibold text-gray-700 mb-1">Total Reservations</h3>
                            <p class="text-gray-600">{{ $facility->reservations->count() }}</p>
                        </div>
                        <div>
                            <h3 class="font-semibold text-gray-700 mb-1">Last Updated</h3>
                            <p class="text-gray-600">{{ $facility->updated_at->format('M d, Y H:i') }}</p>
                        </div>
                    </div>

                    <div class="card-actions">
                        @if($facility->status === 'available')
                            <a href="{{ route('facility_reservations.create') }}?facility={{ $facility->id }}" class="btn btn-primary">
                                <i class="fas fa-calendar-plus mr-2"></i>Reserve This Facility
                            </a>
                        @endif
                        <a href="{{ route('facilities.edit', $facility->id) }}" class="btn btn-outline">
                            <i class="fas fa-edit mr-2"></i>Edit Facility
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Reservations -->
        <div class="lg:col-span-1">
            <div class="card bg-base-100 shadow-xl">
                <div class="card-body">
                    <h3 class="card-title text-lg mb-4">
                        <i class="fas fa-calendar mr-2"></i>Recent Reservations
                    </h3>

                    @if($facility->reservations->count() > 0)
                        <div class="space-y-3">
                            @foreach($facility->reservations->take(5) as $reservation)
                                <div class="border-l-4 border-{{ $reservation->status === 'pending' ? 'yellow' : ($reservation->status === 'approved' ? 'green' : 'red') }}-500 pl-3">
                                    <div class="flex justify-between items-start">
                                        <div>
                                            <p class="font-semibold text-sm">
                                                {{ $reservation->reserver->name ?? 'Unknown' }}
                                            </p>
                                            <p class="text-xs text-gray-500">
                                                {{ $reservation->start_time->format('M d, H:i') }} - {{ $reservation->end_time->format('H:i') }}
                                            </p>
                                        </div>
                                        <div class="badge badge-sm badge-{{ $reservation->status === 'pending' ? 'warning' : ($reservation->status === 'approved' ? 'success' : 'error') }}">
                                            {{ ucfirst($reservation->status) }}
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="fas fa-calendar-times text-3xl text-gray-300 mb-2"></i>
                            <p class="text-gray-500 text-sm">No reservations yet</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
