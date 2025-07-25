@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-2xl mx-auto">
        <div class="flex items-center mb-6">
            <a href="{{ route('document.show', $document->id) }}" class="btn btn-ghost btn-sm mr-4">
                <i class="fas fa-arrow-left mr-2"></i>Back
            </a>
            <h1 class="text-3xl font-bold text-gray-800">Edit Document</h1>
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
                <form action="{{ route('document.update', $document->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    
                    <div class="form-control mb-4">
                        <label class="label">
                            <span class="label-text font-semibold">Document Title *</span>
                        </label>
                        <input type="text" name="title" class="input input-bordered" 
                               value="{{ old('title', $document->title) }}" placeholder="Enter document title" required>
                    </div>

                    <div class="form-control mb-6">
                        <label class="label">
                            <span class="label-text font-semibold">Description</span>
                        </label>
                        <textarea name="description" class="textarea textarea-bordered" 
                                  placeholder="Enter document description (optional)">{{ old('description', $document->description) }}</textarea>
                    </div>

                    <div class="card-actions justify-end">
                        <a href="{{ route('document.show', $document->id) }}" class="btn btn-ghost">Cancel</a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save mr-2"></i>Update Document
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection 