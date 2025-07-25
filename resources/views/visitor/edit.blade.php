@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-2xl mx-auto">
        <div class="flex items-center mb-6">
            <a href="{{ route('visitor.show', $visitor->id) }}" class="btn btn-ghost btn-sm mr-4">
                <i class="fas fa-arrow-left mr-2"></i>Back
            </a>
            <h1 class="text-3xl font-bold text-gray-800">Edit Visitor Log</h1>
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
                <form action="{{ route('visitor.update', $visitor->id) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="form-control mb-4">
                        <label class="label">
                            <span class="label-text font-semibold">Name *</span>
                        </label>
                        <input type="text" name="name" class="input input-bordered"
                               value="{{ old('name', $visitor->name) }}" required>
                    </div>

                    <div class="form-control mb-4">
                        <label class="label">
                            <span class="label-text font-semibold">Contact</span>
                        </label>
                        <input type="text" name="contact" class="input input-bordered"
                               value="{{ old('contact', $visitor->contact) }}">
                    </div>

                    <div class="form-control mb-4">
                        <label class="label">
                            <span class="label-text font-semibold">Purpose</span>
                        </label>
                        <input type="text" name="purpose" class="input input-bordered"
                               value="{{ old('purpose', $visitor->purpose) }}">
                    </div>

                    <div class="form-control mb-4">
                        <label class="label">
                            <span class="label-text font-semibold">Facility/Department</span>
                        </label>
                        <select name="facility_id" class="select select-bordered">
                            <option value="">Select facility/department</option>
                            @foreach($facilities as $facility)
                                <option value="{{ $facility->id }}" {{ old('facility_id', $visitor->facility_id) == $facility->id ? 'selected' : '' }}>
                                    {{ $facility->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-control mb-4">
                        <label class="label">
                            <span class="label-text font-semibold">Time In *</span>
                        </label>
                        <input type="datetime-local" name="time_in" class="input input-bordered"
                               value="{{ old('time_in', \Carbon\Carbon::parse($visitor->time_in)->format('Y-m-d\TH:i')) }}" required>
                    </div>

                    <div class="form-control mb-6">
                        <label class="label">
                            <span class="label-text font-semibold">Time Out</span>
                        </label>
                        <input type="datetime-local" name="time_out" class="input input-bordered"
                               value="{{ old('time_out', $visitor->time_out ? \Carbon\Carbon::parse($visitor->time_out)->format('Y-m-d\TH:i') : '') }}">
                    </div>

                    <div class="card-actions justify-end">
                        <a href="{{ route('visitor.show', $visitor->id) }}" class="btn btn-ghost">Cancel</a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save mr-2"></i>Update Log
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
