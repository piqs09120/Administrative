@extends('layouts.app')

@section('title', 'User Management')
@section('page-title', 'User Management & Access Control')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <h2 class="text-2xl font-bold text-base-content">User Management</h2>
            <p class="text-base-content/60">Manage user accounts, roles, and access permissions</p>
        </div>
        <div class="flex gap-2">
            <button class="btn btn-primary">
                <i class="fas fa-plus"></i>
                Add New User
            </button>
            <button class="btn btn-outline">
                <i class="fas fa-download"></i>
                Export Users
            </button>
        </div>
    </div>
    
    {{-- <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div class="stat bg-base-100 rounded-lg shadow-sm">
            <div class="stat-figure text-primary">
                <i class="fas fa-users text-2xl"></i>
            </div>
            <div class="stat-title">Total Users</div>
            <div class="stat-value text-primary">24</div>
            <div class="stat-desc">All system users</div>
        </div>
        
        <div class="stat bg-base-100 rounded-lg shadow-sm">
            <div class="stat-figure text-success">
                <i class="fas fa-user-check text-2xl"></i>
            </div>
            <div class="stat-title">Active Users</div>
            <div class="stat-value text-success">22</div>
            <div class="stat-desc">Currently active</div>
        </div>
        
        <div class="stat bg-base-100 rounded-lg shadow-sm">
            <div class="stat-figure text-warning">
                <i class="fas fa-user-clock text-2xl"></i>
            </div>
            <div class="stat-title">Inactive Users</div>
            <div class="stat-value text-warning">2</div>
            <div class="stat-desc">Temporarily disabled</div>
        </div>
        
        <div class="stat bg-base-100 rounded-lg shadow-sm">
            <div class="stat-figure text-info">
                <i class="fas fa-user-plus text-2xl"></i>
            </div>
            <div class="stat-title">New This Month</div>
            <div class="stat-value text-info">3</div>
            <div class="stat-desc">Recently added</div>
        </div>
    </div> --}}
    
    <!-- Search and Filters -->
    <div class="card bg-base-100 shadow-sm">
        <div class="card-body">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div class="form-control">
                    <label class="label">
                        <span class="label-text">Search Users</span>
                    </label>
                    <input type="text" placeholder="Name or email..." class="input input-bordered" />
                </div>
                
                <div class="form-control">
                    <label class="label">
                        <span class="label-text">Role</span>
                    </label>
                    <select class="select select-bordered">
                        <option value="">All Roles</option>
                        <option value="administrator">Administrator</option>
                        <option value="manager">Manager</option>
                        <option value="staff">Staff</option>
                    </select>
                </div>
                
                <div class="form-control">
                    <label class="label">
                        <span class="label-text">Department</span>
                    </label>
                    <select class="select select-bordered">
                        <option value="">All Departments</option>
                        <option value="management">Management</option>
                        <option value="reception">Reception</option>
                        <option value="restaurant">Restaurant</option>
                        <option value="housekeeping">Housekeeping</option>
                    </select>
                </div>
                
                <div class="form-control">
                    <label class="label">
                        <span class="label-text">Status</span>
                    </label>
                    <select class="select select-bordered">
                        <option value="">All Status</option>
                        <option value="active">Active</option>
                        <option value="inactive">Inactive</option>
                        <option value="suspended">Suspended</option>
                    </select>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Users Table -->
    <div class="card bg-base-100 shadow-sm">
        <div class="card-body">
            <div class="overflow-x-auto">
                <table class="table table-zebra">
                    <thead>
                        <tr>
                            {{-- <th>User</th>
                            <th>Role</th>
                            <th>Department</th>
                            <th>Status</th>
                            <th>Last Login</th>
                            <th>Actions</th> --}}
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($users as $user)
                        <tr class="hover">
                            {{-- <td>
                                <div class="flex items-center gap-3">
                                    <div class="avatar">
                                        <div class="w-12 h-12 rounded-full bg-primary/10 flex items-center justify-center">
                                            <span class="text-primary font-medium">
                                                {{ substr($user['name'], 0, 1) }}{{ substr(explode(' ', $user['name'])[1] ?? '', 0, 1) }}
                                            </span>
                                        </div>
                                    </div>
                                    <div>
                                        <div class="font-bold">{{ $user['name'] }}</div>
                                        <div class="text-sm text-base-content/60">{{ $user['email'] }}</div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div class="flex items-center gap-2">
                                    @if($user['role'] == 'Administrator')
                                        <i class="fas fa-crown text-warning"></i>
                                    @elseif(str_contains($user['role'], 'Manager'))
                                        <i class="fas fa-user-tie text-primary"></i>
                                    @else
                                        <i class="fas fa-user text-secondary"></i>
                                    @endif
                                    {{ $user['role'] }}
                                </div>
                            </td>
                            <td>{{ $user['department'] }}</td>
                            <td>
                                @if($user['status'] == 'Active')
                                    <div class="badge badge-success">Active</div>
                                @else
                                    <div class="badge badge-error">Inactive</div>
                                @endif
                            </td>
                            <td>
                                <div class="text-sm">
                                    {{ date('M j, Y', strtotime($user['last_login'])) }}
                                    <br>
                                    <span class="text-base-content/60">{{ date('H:i', strtotime($user['last_login'])) }}</span>
                                </div>
                            </td>
                            <td>
                                <div class="dropdown dropdown-end">
                                    <div tabindex="0" role="button" class="btn btn-ghost btn-xs">
                                        <i class="fas fa-ellipsis-v"></i>
                                    </div>
                                    <ul tabindex="0" class="dropdown-content z-[1] menu p-2 shadow bg-base-100 rounded-box w-52">
                                        <li><a>
                                            <i class="fas fa-eye"></i> View Profile
                                        </a></li>
                                        <li><a>
                                            <i class="fas fa-edit"></i> Edit User
                                        </a></li>
                                        <li><a href="{{ route('access.users.editRole', $user['id']) }}">
                                            <i class="fas fa-user-tag"></i> Edit Role
                                        </a></li>
                                        <li><a>
                                            <i class="fas fa-key"></i> Reset Password
                                        </a></li>
                                        <li><a>
                                            <i class="fas fa-history"></i> View Activity
                                        </a></li>
                                        <li><hr class="my-1"></li>
                                        @if($user['status'] == 'Active')
                                            <li><a class="text-warning">
                                                <i class="fas fa-pause"></i> Suspend User
                                            </a></li>
                                        @else
                                            <li><a class="text-success">
                                                <i class="fas fa-play"></i> Activate User
                                            </a></li>
                                        @endif
                                    </ul>
                                </div>
                            </td> --}}
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            
            <!-- Pagination -->
            <div class="flex justify-between items-center mt-4">
                <div class="text-sm text-base-content/60">
                    Showing 1 to 4 of 24 users
                </div>
                <div class="join">
                    <button class="join-item btn btn-sm">«</button>
                    <button class="join-item btn btn-sm btn-active">1</button>
                    <button class="join-item btn btn-sm">2</button>
                    <button class="join-item btn btn-sm">3</button>
                    <button class="join-item btn btn-sm">»</button>
                </div>
            </div>
        </div>
    </div>
    
    {{--
    <div class="card bg-base-100 shadow-sm mt-6">
        <div class="card-body">
            <h3 class="text-lg font-bold mb-4">Recent User Activity</h3>
            <div class="space-y-3">
                <div class="alert alert-success">
                    <i class="fas fa-sign-in-alt"></i>
                    <span>John Smith logged in</span>
                    <span class="ml-auto text-xs text-base-content/60">2 minutes ago from 192.168.1.100</span>
                </div>
                <div class="alert alert-info">
                    <i class="fas fa-user-plus"></i>
                    <span>New user account created: Emily Davis</span>
                    <span class="ml-auto text-xs text-base-content/60">1 hour ago by John Smith</span>
                </div>
                <div class="alert alert-warning">
                    <i class="fas fa-key"></i>
                    <span>Password reset requested by Mike Wilson</span>
                    <span class="ml-auto text-xs text-base-content/60">3 hours ago</span>
                </div>
            </div>
        </div>
    </div>
    --}}
</div>
@endsection