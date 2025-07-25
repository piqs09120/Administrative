@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="flex items-center mb-6">
        <a href="{{ route('document.index') }}" class="btn btn-ghost btn-sm mr-4">
            <i class="fas fa-arrow-left mr-2"></i>Back
        </a>
        <h1 class="text-3xl font-bold text-gray-800">Document Details</h1>
    </div>

    @if(session('success'))
        <div class="alert alert-success mb-4">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-error mb-4">
            {{ session('error') }}
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Document Information -->
        <div class="lg:col-span-2">
            <div class="card bg-base-100 shadow-xl">
                <div class="card-body">
                    <div class="flex justify-between items-start mb-4">
                        <h2 class="card-title text-2xl">{{ $document->title }}</h2>
                        <div class="badge badge-lg badge-{{ $document->status === 'archived' ? 'neutral' : ($document->status === 'pending_release' ? 'warning' : 'success') }}">
                            {{ ucfirst(str_replace('_', ' ', $document->status)) }}
                        </div>
                    </div>

                    @if($document->description)
                        <div class="mb-4">
                            <h3 class="font-semibold text-gray-700 mb-2">Description</h3>
                            <p class="text-gray-600">{{ $document->description }}</p>
                        </div>
                    @endif

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                        <div>
                            <h3 class="font-semibold text-gray-700 mb-1">Uploaded By</h3>
                            <p class="text-gray-600">{{ $document->uploader->name ?? 'Unknown' }}</p>
                        </div>
                        <div>
                            <h3 class="font-semibold text-gray-700 mb-1">Upload Date</h3>
                            <p class="text-gray-600">{{ $document->created_at->format('M d, Y H:i') }}</p>
                        </div>
                        <div>
                            <h3 class="font-semibold text-gray-700 mb-1">File Path</h3>
                            <p class="text-gray-600 text-sm">{{ $document->file_path }}</p>
                        </div>
                        <div>
                            <h3 class="font-semibold text-gray-700 mb-1">Last Updated</h3>
                            <p class="text-gray-600">{{ $document->updated_at->format('M d, Y H:i') }}</p>
                        </div>
                        <div>
                            <strong>Category:</strong> {{ $document->category ?? 'Uncategorized' }}
                        </div>
                        @if(!empty($entities))
                            <div>
                                <strong>Named Entities:</strong>
                                <ul>
                                    @foreach($entities as $entity)
                                        <li>{{ $entity['text'] }} ({{ $entity['label'] }})</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif
                    </div>

                    <div class="card-actions">
                        @if($document->status === 'archived')
                            <form action="{{ route('document.request-release', $document->id) }}" method="POST" class="inline">
                                @csrf
                                <button type="submit" class="btn btn-warning" onclick="return confirm('Request release for this document?')">
                                    <i class="fas fa-paper-plane mr-2"></i>Request Release
                                </button>
                            </form>
                        @endif
                        
                        @if($document->status === 'released')
                            <a href="{{ route('document.download', $document->id) }}" class="btn btn-success">
                                <i class="fas fa-download mr-2"></i>Download Document
                            </a>
                        @endif
                        
                        <a href="{{ route('document.edit', $document->id) }}" class="btn btn-outline">
                            <i class="fas fa-edit mr-2"></i>Edit
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Request History -->
        <div class="lg:col-span-1">
            <div class="card bg-base-100 shadow-xl">
                <div class="card-body">
                    <h3 class="card-title text-lg mb-4">
                        <i class="fas fa-history mr-2"></i>Request History
                    </h3>

                    @if($document->documentRequests->count() > 0)
                        <div class="space-y-3">
                            @foreach($document->documentRequests->sortByDesc('created_at') as $request)
                                <div class="border-l-4 border-{{ $request->status === 'pending' ? 'yellow' : ($request->status === 'approved' ? 'green' : 'red') }}-500 pl-3">
                                    <div class="flex justify-between items-start">
                                        <div>
                                            <p class="font-semibold text-sm">
                                                {{ ucfirst($request->status) }}
                                            </p>
                                            <p class="text-xs text-gray-500">
                                                Requested by: {{ $request->requester->name ?? 'Unknown' }}
                                            </p>
                                            <p class="text-xs text-gray-500">
                                                {{ $request->created_at->format('M d, Y H:i') }}
                                            </p>
                                        </div>
                                        <div class="badge badge-sm badge-{{ $request->status === 'pending' ? 'warning' : ($request->status === 'approved' ? 'success' : 'error') }}">
                                            {{ ucfirst($request->status) }}
                                        </div>
                                    </div>
                                    
                                    @if($request->approved_by)
                                        <p class="text-xs text-gray-500 mt-1">
                                            {{ $request->status === 'approved' ? 'Approved' : 'Denied' }} by: {{ $request->approver->name ?? 'Unknown' }}
                                        </p>
                                    @endif
                                    
                                    @if($request->remarks)
                                        <p class="text-xs text-gray-600 mt-1 italic">
                                            "{{ $request->remarks }}"
                                        </p>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="fas fa-inbox text-3xl text-gray-300 mb-2"></i>
                            <p class="text-gray-500 text-sm">No request history</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 