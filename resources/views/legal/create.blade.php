@extends('layouts.app')

@section('content')
<div class="flex justify-center items-center min-h-screen bg-base-200">
    <div class="w-full max-w-xl">
        <div class="card shadow-xl bg-base-100">
            <div class="card-body">
                <div class="flex items-center mb-4">
                    <!-- Folder Plus SVG from Lucide -->
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-7 h-7 text-primary mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M22 19a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5.17a2 2 0 0 1 1.41.59l1.83 1.82A2 2 0 0 0 13.83 6H20a2 2 0 0 1 2 2v11z"/><path stroke-linecap="round" stroke-linejoin="round" d="M12 10v6m3-3h-6"/></svg>
                    <h2 class="card-title text-2xl font-bold">Add New Legal Case</h2>
                </div>
                @if(session('success'))
                    <div class="alert alert-success mb-2">{{ session('success') }}</div>
                @endif
                @if($errors->any())
                    <div class="alert alert-error mb-2">
                        <ul class="list-disc ml-5">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
                <form action="{{ route('legal.store') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                    @csrf
                    <div class="form-control">
                        <label for="title" class="label">
                            <span class="label-text font-semibold">Case Title <span class="text-error">*</span></span>
                        </label>
                        <input type="text" id="title" name="title" class="input input-bordered" placeholder="Enter case title" required />
                    </div>
                    <div class="form-control">
                        <label for="description" class="label">
                            <span class="label-text font-semibold">Case Description</span>
                        </label>
                        <textarea id="description" name="description" class="textarea textarea-bordered" rows="3" placeholder="Describe the case..."></textarea>
                    </div>
                    <div class="form-control">
                        <label for="document_file" class="label">
                            <span class="label-text font-semibold">Upload Document <span class="text-error">*</span></span>
                        </label>
                        <input type="file" id="document_file" name="document_file" class="file-input file-input-bordered w-full" required accept=".pdf,.doc,.docx,.txt" />
                        <span class="text-xs text-base-content/60 mt-1">Accepted: PDF, DOC, DOCX, TXT (max 10MB)</span>
                    </div>
                    <div class="flex justify-end">
                        <button type="submit" class="btn btn-primary gap-2">
                            <!-- Upload SVG from Lucide -->
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><path stroke-linecap="round" stroke-linejoin="round" d="M7 10l5-5 5 5M12 5v12"/></svg>
                            Add Case
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection