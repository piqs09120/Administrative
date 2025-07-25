@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-2xl mx-auto">
        <div class="flex items-center mb-6">
            <a href="{{ route('facility_reservations.index') }}" class="btn btn-ghost btn-sm mr-4">
                <i class="fas fa-arrow-left mr-2"></i>Back
            </a>
            <h1 class="text-3xl font-bold text-gray-800">Reserve a Facility</h1>
        </div>

        @if($errors->any())
            <div class="alert alert-error mb-6">
                <ul>
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="card bg-base-100 shadow-xl">
            <div class="card-body">
                <form action="{{ route('facility_reservations.store') }}" method="POST">
                    @csrf

                    <div class="form-control mb-4">
                        <label class="label">
                            <span class="label-text font-semibold">Facility *</span>
                        </label>
                        <select name="facility_id" class="select select-bordered" required>
                            <option value="">Select facility</option>
                            @foreach($facilities as $facility)
                                <option value="{{ $facility->id }}"
                                    {{ (request('facility') == $facility->id || old('facility_id') == $facility->id) ? 'selected' : '' }}>
                                    {{ $facility->name }} ({{ $facility->location }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-control mb-4">
                        <label class="label">
                            <span class="label-text font-semibold">Start Time *</span>
                        </label>
                        <input type="datetime-local" name="start_time" class="input input-bordered"
                               value="{{ old('start_time') }}" required>
                    </div>

                    <div class="form-control mb-4">
                        <label class="label">
                            <span class="label-text font-semibold">End Time *</span>
                        </label>
                        <input type="datetime-local" name="end_time" class="input input-bordered"
                               value="{{ old('end_time') }}" required>
                    </div>

                    <div class="form-control mb-6">
                        <label class="label">
                            <span class="label-text font-semibold">Purpose</span>
                        </label>
                        <textarea name="purpose" class="textarea textarea-bordered"
                                  placeholder="Enter purpose for reservation">{{ old('purpose') }}</textarea>
                    </div>

                    <div class="card-actions justify-end">
                        <a href="{{ route('facility_reservations.index') }}" class="btn btn-ghost">Cancel</a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-calendar-plus mr-2"></i>Submit Reservation
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
