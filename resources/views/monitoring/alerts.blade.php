@extends('layouts.app')

@section('title', 'System Alerts')
@section('page-title', 'System Alerts & Notifications')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <h2 class="text-2xl font-bold text-base-content">System Alerts & Notifications</h2>
            <p class="text-base-content/60">Monitor real-time alerts and system notifications</p>
        </div>
        <div class="flex gap-2">
            <button class="btn btn-primary">
                <i class="fas fa-cog"></i>
                Alert Settings
            </button>
            <button class="btn btn-outline">
                <i class="fas fa-check-double"></i>
                Mark All Read
            </button>
        </div>
    </div>
    
    <!-- Alert Statistics -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div class="stat bg-base-100 rounded-lg shadow-sm">
            <div class="stat-figure text-error">
                <i class="fas fa-exclamation-circle text-2xl"></i>
            </div>
            <div class="stat-title">Critical Alerts</div>
            <div class="stat-value text-error">1</div>
            <div class="stat-desc">Requires immediate attention</div>
        </div>
        
        <div class="stat bg-base-100 rounded-lg shadow-sm">
            <div class="stat-figure text-warning">
                <i class="fas fa-exclamation-triangle text-2xl"></i>
            </div>
            <div class="stat-title">Warnings</div>
            <div class="stat-value text-warning">1</div>
            <div class="stat-desc">Needs monitoring</div>
        </div>
        
        <div class="stat bg-base-100 rounded-lg shadow-sm">
            <div class="stat-figure text-info">
                <i class="fas fa-info-circle text-2xl"></i>
            </div>
            <div class="stat-title">Info Alerts</div>
            <div class="stat-value text-info">1</div>
            <div class="stat-desc">General notifications</div>
        </div>
        
        <div class="stat bg-base-100 rounded-lg shadow-sm">
            <div class="stat-figure text-success">
                <i class="fas fa-check-circle text-2xl"></i>
            </div>
            <div class="stat-title">Resolved</div>
            <div class="stat-value text-success">1</div>
            <div class="stat-desc">Successfully handled</div>
        </div>
    </div>
    
    <!-- Alert Filters -->
    <div class="card bg-base-100 shadow-sm">
        <div class="card-body">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div class="form-control">
                    <label class="label">
                        <span class="label-text">Alert Type</span>
                    </label>
                    <select class="select select-bordered">
                        <option value="">All Types</option>
                        <option value="critical">Critical</option>
                        <option value="warning">Warning</option>
                        <option value="info">Information</option>
                        <option value="success">Success</option>
                    </select>
                </div>
                
                <div class="form-control">
                    <label class="label">
                        <span class="label-text">Status</span>
                    </label>
                    <select class="select select-bordered">
                        <option value="">All Status</option>
                        <option value="active">Active</option>
                        <option value="acknowledged">Acknowledged</option>
                        <option value="resolved">Resolved</option>
                    </select>
                </div>
                
                <div class="form-control">
                    <label class="label">
                        <span class="label-text">Location</span>
                    </label>
                    <select class="select select-bordered">
                        <option value="">All Locations</option>
                        <option value="kitchen">Kitchen</option>
                        <option value="rooms">Guest Rooms</option>
                        <option value="common">Common Areas</option>
                        <option value="systems">Building Systems</option>
                    </select>
                </div>
                
                <div class="form-control">
                    <label class="label">
                        <span class="label-text">Time Range</span>
                    </label>
                    <select class="select select-bordered">
                        <option value="today">Today</option>
                        <option value="week">This Week</option>
                        <option value="month">This Month</option>
                        <option value="all">All Time</option>
                    </select>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Alerts List -->
    <div class="space-y-4">
        @foreach($alerts as $alert)
        <div class="card bg-base-100 shadow-sm border-l-4 
            @if($alert['type'] == 'critical') border-error
            @elseif($alert['type'] == 'warning') border-warning
            @elseif($alert['type'] == 'info') border-info
            @else border-success
            @endif">
            <div class="card-body">
                <div class="flex items-start justify-between">
                    <div class="flex items-start gap-4">
                        <div class="w-12 h-12 rounded-lg flex items-center justify-center
                            @if($alert['type'] == 'critical') bg-error/10
                            @elseif($alert['type'] == 'warning') bg-warning/10
                            @elseif($alert['type'] == 'info') bg-info/10
                            @else bg-success/10
                            @endif">
                            @if($alert['type'] == 'critical')
                                <i class="fas fa-exclamation-circle text-error text-xl"></i>
                            @elseif($alert['type'] == 'warning')
                                <i class="fas fa-exclamation-triangle text-warning text-xl"></i>
                            @elseif($alert['type'] == 'info')
                                <i class="fas fa-info-circle text-info text-xl"></i>
                            @else
                                <i class="fas fa-check-circle text-success text-xl"></i>
                            @endif
                        </div>
                        
                        <div class="flex-1">
                            <div class="flex items-center gap-2 mb-1">
                                <h3 class="font-semibold text-base-content">{{ $alert['title'] }}</h3>
                                <div class="badge badge-sm
                                    @if($alert['status'] == 'active') badge-error
                                    @elseif($alert['status'] == 'acknowledged') badge-warning
                                    @else badge-success
                                    @endif">
                                    {{ ucfirst($alert['status']) }}
                                </div>
                            </div>
                            
                            <p class="text-base-content/70 mb-2">{{ $alert['message'] }}</p>
                            
                            <div class="flex items-center gap-4 text-sm text-base-content/60">
                                <div class="flex items-center gap-1">
                                    <i class="fas fa-clock"></i>
                                    <span>{{ date('M j, Y H:i', strtotime($alert['time'])) }}</span>
                                </div>
                                <div class="flex items-center gap-1">
                                    <i class="fas fa-map-marker-alt"></i>
                                    <span>{{ $alert['location'] }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="flex gap-2">
                        @if($alert['status'] == 'active')
                            <button class="btn btn-sm btn-warning">
                                <i class="fas fa-eye"></i>
                                Acknowledge
                            </button>
                            <button class="btn btn-sm btn-success">
                                <i class="fas fa-check"></i>
                                Resolve
                            </button>
                        @elseif($alert['status'] == 'acknowledged')
                            <button class="btn btn-sm btn-success">
                                <i class="fas fa-check"></i>
                                Resolve
                            </button>
                        @endif
                        
                        <div class="dropdown dropdown-end">
                            <div tabindex="0" role="button" class="btn btn-sm btn-ghost">
                                <i class="fas fa-ellipsis-v"></i>
                            </div>
                            <ul tabindex="0" class="dropdown-content z-[1] menu p-2 shadow bg-base-100 rounded-box w-52">
                                <li><a><i class="fas fa-eye"></i> View Details</a></li>
                                <li><a><i class="fas fa-edit"></i> Add Note</a></li>
                                <li><a><i class="fas fa-share"></i> Forward Alert</a></li>
                                <li><hr class="my-1"></li>
                                <li><a class="text-error"><i class="fas fa-trash"></i> Delete</a></li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endforeach
    </div>
    
    <!-- Alert Configuration -->
    <div class="card bg-base-100 shadow-sm">
        <div class="card-body">
            <h3 class="card-title">Alert Configuration</h3>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-4">
                <div>
                    <h4 class="font-medium mb-3">Temperature Monitoring</h4>
                    <div class="space-y-3">
                        <div class="flex items-center justify-between">
                            <span class="text-sm">Kitchen Freezer Alert</span>
                            <input type="checkbox" class="toggle toggle-primary" checked />
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-sm">Room Temperature Alert</span>
                            <input type="checkbox" class="toggle toggle-primary" checked />
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-sm">Hot Water System Alert</span>
                            <input type="checkbox" class="toggle toggle-primary" />
                        </div>
                    </div>
                </div>
                
                <div>
                    <h4 class="font-medium mb-3">System Monitoring</h4>
                    <div class="space-y-3">
                        <div class="flex items-center justify-between">
                            <span class="text-sm">Energy Usage Alerts</span>
                            <input type="checkbox" class="toggle toggle-primary" checked />
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-sm">Security System Alerts</span>
                            <input type="checkbox" class="toggle toggle-primary" checked />
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-sm">Maintenance Reminders</span>
                            <input type="checkbox" class="toggle toggle-primary" checked />
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection