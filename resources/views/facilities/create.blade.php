@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-2xl mx-auto">
        <div class="flex items-center mb-6">
            <a href="{{ route('facilities.index') }}" class="btn btn-ghost btn-sm mr-4">
                <i class="fas fa-arrow-left mr-2"></i>Back
            </a>
            <h1 class="text-3xl font-bold text-gray-800">Add New Facility</h1>
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
                <form action="{{ route('facilities.store') }}" method="POST">
                    @csrf
                    
                    <div class="form-control mb-4">
                        <label class="label">
                            <span class="label-text font-semibold">Facility Name *</span>
                        </label>
                        <input type="text" name="name" class="input input-bordered" 
                               value="{{ old('name') }}" placeholder="Enter facility name" required>
                    </div>

                    <div class="form-control mb-4">
                        <label class="label">
                            <span class="label-text font-semibold">Location</span>
                        </label>
                        <input type="text" name="location" class="input input-bordered" 
                               value="{{ old('location') }}" placeholder="Enter facility location">
                    </div>

                    <div class="form-control mb-4">
                        <label class="label">
                            <span class="label-text font-semibold">Description</span>
                        </label>
                        <textarea name="description" class="textarea textarea-bordered" 
                                  placeholder="Enter facility description">{{ old('description') }}</textarea>
                    </div>

                    <div class="form-control mb-6">
                        <label class="label">
                            <span class="label-text font-semibold">Status *</span>
                        </label>
                        <select name="status" class="select select-bordered" required>
                            <option value="">Select status</option>
                            <option value="available" {{ old('status') === 'available' ? 'selected' : '' }}>Available</option>
                            <option value="unavailable" {{ old('status') === 'unavailable' ? 'selected' : '' }}>Unavailable</option>
                        </select>
                    </div>

                    <div class="card-actions justify-end">
                        <a href="{{ route('facilities.index') }}" class="btn btn-ghost">Cancel</a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save mr-2"></i>Create Facility
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
