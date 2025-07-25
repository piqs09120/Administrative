@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <h1 class="text-3xl font-bold mb-6">Access Logs</h1>
    <div class="card bg-base-100 shadow-xl">
        <div class="card-body">
            <div class="overflow-x-auto">
                <table class="table w-full">
                    <thead>
                        <tr>
                            <th>User</th>
                            <th>Action</th>
                            <th>Description</th>
                            <th>IP Address</th>
                            <th>Timestamp</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($logs as $log)
                            <tr>
                                <td>{{ $log->user->name ?? 'Unknown' }}</td>
                                <td>{{ $log->action }}</td>
                                <td>{{ $log->description }}</td>
                                <td>{{ $log->ip_address }}</td>
                                <td>{{ $log->created_at->format('M d, Y H:i') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
