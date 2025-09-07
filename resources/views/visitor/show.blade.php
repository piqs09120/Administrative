@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="flex items-center mb-6">
        <a href="{{ route('visitor.index') }}" class="btn btn-ghost btn-sm mr-4">
            <i class="fas fa-arrow-left mr-2"></i>Back
        </a>
        <h1 class="text-3xl font-bold text-gray-800">Visitor Details</h1>
    </div>

    @if(session('success'))
        <div class="alert alert-success mb-4">
            {{ session('success') }}
        </div>
    @endif

    <div class="card bg-base-100 shadow-xl">
        <div class="card-body">
            <h2 class="card-title text-xl mb-4">{{ $visitor->name }}</h2>
            <div class="mb-2"><span class="font-semibold">Email:</span> {{ $visitor->email ?? '-' }}</div>
            <div class="mb-2"><span class="font-semibold">Contact:</span> {{ $visitor->contact ?? '-' }}</div>
            <div class="mb-2"><span class="font-semibold">Purpose:</span> {{ $visitor->purpose ?? '-' }}</div>
            <div class="mb-2"><span class="font-semibold">Department:</span> {{ $visitor->department ?? '-' }}</div>
            <div class="mb-2"><span class="font-semibold">Host:</span> {{ $visitor->host_employee ?? '-' }}</div>
            <div class="mb-2"><span class="font-semibold">Company:</span> {{ $visitor->company ?? '-' }}</div>
            <div class="mb-2"><span class="font-semibold">ID Type:</span> {{ $visitor->id_type ?? '-' }}</div>
            <div class="mb-2"><span class="font-semibold">ID Number:</span> {{ $visitor->id_number ?? '-' }}</div>
            <div class="mb-2"><span class="font-semibold">Vehicle Plate:</span> {{ $visitor->vehicle_plate ?? '-' }}</div>
            <div class="mb-2"><span class="font-semibold">Time In:</span> {{ \Carbon\Carbon::parse($visitor->time_in)->format('M d, Y H:i') }}</div>
            <div class="mb-2"><span class="font-semibold">Time Out:</span>
                @if($visitor->time_out)
                    {{ \Carbon\Carbon::parse($visitor->time_out)->format('M d, Y H:i') }}
                @else
                    <span class="badge badge-warning">IN</span>
                @endif
            </div>
            <div class="card-actions mt-4">
                <a href="{{ route('visitor.edit', $visitor->id) }}" class="btn btn-outline">
                    <i class="fas fa-edit mr-2"></i>Edit
                </a>
                <form action="{{ route('visitor.destroy', $visitor->id) }}" method="POST" class="inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-error" onclick="return confirm('Delete this visitor log?')">
                        <i class="fas fa-trash mr-2"></i>Delete
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
