<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\AccessLog;
use App\Models\User;

class AccessController extends Controller
{
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            if (!auth()->check() || strtolower(auth()->user()->role) !== 'administrator') {
                abort(403, 'Only Administrators can access user management.');
            }
            return $next($request);
        })->only(['users', 'editRole', 'updateRole']);
    }

    public function users()
    {
        $users = [
            [
                'id' => 1,
                'name' => 'John Smith',
                'email' => 'john.smith@hotel.com',
                'role' => 'Administrator',
                'department' => 'Management',
                'status' => 'Active',
                'last_login' => '2024-01-10 14:30:00',
                'created_at' => '2023-06-15'
            ],
            [
                'id' => 2,
                'name' => 'Sarah Johnson',
                'email' => 'sarah.johnson@hotel.com',
                'role' => 'Front Desk Manager',
                'department' => 'Reception',
                'status' => 'Active',
                'last_login' => '2024-01-10 13:45:00',
                'created_at' => '2023-08-20'
            ],
            [
                'id' => 3,
                'name' => 'Mike Wilson',
                'email' => 'mike.wilson@hotel.com',
                'role' => 'Kitchen Manager',
                'department' => 'Restaurant',
                'status' => 'Active',
                'last_login' => '2024-01-10 12:15:00',
                'created_at' => '2023-09-10'
            ],
            [
                'id' => 4,
                'name' => 'Emily Davis',
                'email' => 'emily.davis@hotel.com',
                'role' => 'Housekeeping Supervisor',
                'department' => 'Housekeeping',
                'status' => 'Inactive',
                'last_login' => '2024-01-08 16:20:00',
                'created_at' => '2023-07-05'
            ]
        ];
        
        return view('access.users', compact('users'));
    }
    
    public function roles()
    {
        $roles = [
            [
                'name' => 'Administrator',
                'description' => 'Full system access with all permissions',
                'users_count' => 2,
                'permissions' => ['All Permissions'],
                'created_at' => '2023-06-01'
            ],
            [
                'name' => 'Front Desk Manager',
                'description' => 'Manage reservations, guests, and front desk operations',
                'users_count' => 3,
                'permissions' => ['Reservations', 'Guests', 'Rooms', 'Billing'],
                'created_at' => '2023-06-01'
            ],
            [
                'name' => 'Kitchen Manager',
                'description' => 'Manage restaurant operations, menu, and orders',
                'users_count' => 2,
                'permissions' => ['Menu Management', 'Orders', 'Inventory', 'Staff Schedule'],
                'created_at' => '2023-06-01'
            ],
            [
                'name' => 'Housekeeping Supervisor',
                'description' => 'Manage room cleaning and maintenance schedules',
                'users_count' => 4,
                'permissions' => ['Room Status', 'Maintenance', 'Staff Schedule'],
                'created_at' => '2023-06-01'
            ]
        ];
        
        return view('access.roles', compact('roles'));
    }
    
    public function logs()
    {
        try {
            // Get the actual logs with DeptAccount user relationship
            $logs = AccessLog::with('user')->latest()->get();
            
            // If no logs exist, create some sample logs for demonstration
            if ($logs->count() === 0) {
                $this->createSampleLogs();
                $logs = AccessLog::with('user')->latest()->get();
            }
            
            // Debug: Log the results
            \Log::info('Access Logs Debug', [
                'total_logs' => $logs->count(),
                'logs_retrieved' => $logs->count(),
                'first_log' => $logs->first(),
                'first_log_user' => $logs->first() ? $logs->first()->user : null,
                'database_connection' => config('database.default'),
                'database_name' => config('database.connections.mysql.database')
            ]);
            
            return view('access.logs', compact('logs'));
        } catch (\Exception $e) {
            // Debug: Log any errors
            \Log::error('Access Logs Error: ' . $e->getMessage(), [
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);
            
            // Return view with empty logs and error message
            $logs = collect([]);
            session()->flash('error', 'Error loading access logs: ' . $e->getMessage());
            return view('access.logs', compact('logs'));
        }
    }

    /**
     * Create sample access logs for demonstration
     */
    private function createSampleLogs()
    {
        try {
            // Get some sample users from DeptAccount
            $users = \App\Models\DeptAccount::take(3)->get();
            
            if ($users->count() > 0) {
                $sampleActions = [
                    'Login' => 'User logged in successfully',
                    'Document_uploaded' => 'Document uploaded and processed',
                    'Access_control_check' => 'User passed authorization check',
                    'Logout' => 'User logged out successfully',
                    'Profile_updated' => 'User profile information updated'
                ];
                
                foreach ($users as $user) {
                    foreach ($sampleActions as $action => $description) {
                        AccessLog::create([
                            'user_id' => $user->Dept_no,
                            'action' => $action,
                            'description' => $description,
                            'ip_address' => '127.0.0.1'
                        ]);
                    }
                }
            }
        } catch (\Exception $e) {
            \Log::error('Error creating sample logs: ' . $e->getMessage());
        }
    }

    /**
     * Static method to log user actions (can be called from other controllers)
     */
    public static function logAction($userId, $action, $description = '', $ipAddress = null)
    {
        try {
            AccessLog::create([
                'user_id' => $userId,
                'action' => $action,
                'description' => $description,
                'ip_address' => $ipAddress ?? request()->ip()
            ]);
        } catch (\Exception $e) {
            \Log::error('Error logging action: ' . $e->getMessage());
        }
    }
    
    public function security()
    {
        // Role restrictions removed - all users can access security settings
        $securitySettings = [
            'password_policy' => [
                'min_length' => 8,
                'require_uppercase' => true,
                'require_lowercase' => true,
                'require_numbers' => true,
                'require_symbols' => true,
                'expiry_days' => 90
            ],
            'session_settings' => [
                'timeout_minutes' => 30,
                'max_concurrent_sessions' => 3,
                'remember_me_days' => 30
            ],
            'security_features' => [
                'two_factor_auth' => true,
                'ip_whitelist' => false,
                'failed_login_lockout' => true,
                'audit_logging' => true
            ]
        ];
        return view('access.security', compact('securitySettings'));
    }

    public function editRole(User $user)
    {
        $roles = ['Administrator', 'Manager', 'Staff', 'Legal', 'Reception', 'Housekeeping', 'Restaurant'];
        return view('access.edit_role', compact('user', 'roles'));
    }

    public function updateRole(Request $request, User $user)
    {
        $request->validate([
            'role' => 'required|string|max:255',
        ]);
        $user->role = $request->role;
        $user->save();
        return redirect()->route('access.users')->with('success', 'User role updated successfully!');
    }
}