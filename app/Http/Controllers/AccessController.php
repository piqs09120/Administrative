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

    public function departmentAccounts()
    {
        try {
            // Get department accounts from the database
            $departmentAccounts = \App\Models\DeptAccount::orderBy('dept_name', 'asc')->get();
            
            // Group accounts by department for better organization
            $departments = $departmentAccounts->groupBy('dept_name');
            
            // Get statistics
            $totalAccounts = $departmentAccounts->count();
            $activeAccounts = $departmentAccounts->where('status', 'active')->count();
            $inactiveAccounts = $departmentAccounts->where('status', 'inactive')->count();
            
            $stats = [
                'total' => $totalAccounts,
                'active' => $activeAccounts,
                'inactive' => $inactiveAccounts
            ];
            
            return view('access.department_accounts', compact('departmentAccounts', 'departments', 'stats'));
            
        } catch (\Exception $e) {
            \Log::error('Error loading department accounts: ' . $e->getMessage());
            session()->flash('error', 'Error loading department accounts: ' . $e->getMessage());
            
            // Return empty data on error
            return view('access.department_accounts', [
                'departmentAccounts' => collect([]),
                'departments' => collect([]),
                'stats' => ['total' => 0, 'active' => 0, 'inactive' => 0]
            ]);
        }
    }

    public function storeDepartmentAccount(Request $request)
    {
        try {
            $request->validate([
                'employee_name' => 'required|string|max:255',
                'dept_name' => 'required|string|max:255',
                'email' => 'nullable|email|max:255',
                'role' => 'nullable|string|max:255',
                'status' => 'required|in:active,inactive',
                'phone' => 'nullable|string|max:20',
            ]);

            // Create new department account
            $deptAccount = new \App\Models\DeptAccount();
            $deptAccount->employee_name = $request->employee_name;
            $deptAccount->dept_name = $request->dept_name;
            $deptAccount->email = $request->email;
            $deptAccount->role = $request->role;
            $deptAccount->status = $request->status;
            $deptAccount->phone = $request->phone;
            $deptAccount->save();

            return redirect()->route('access.department_accounts')->with('success', 'Department account created successfully!');
            
        } catch (\Exception $e) {
            \Log::error('Error creating department account: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Error creating department account: ' . $e->getMessage())->withInput();
        }
    }

    public function departmentLogs()
    {
        try {
            // Get department-specific logs with user information
            $logs = AccessLog::with('user')
                ->whereHas('user', function($query) {
                    $query->whereNotNull('dept_name');
                })
                ->latest()
                ->get();
            
            // If no logs exist, create some sample department logs for demonstration
            if ($logs->count() === 0) {
                $this->createSampleDepartmentLogs();
                $logs = AccessLog::with('user')
                    ->whereHas('user', function($query) {
                        $query->whereNotNull('dept_name');
                    })
                    ->latest()
                    ->get();
            }
            
            return view('access.account_logs', compact('logs'));
            
        } catch (\Exception $e) {
            \Log::error('Error loading department logs: ' . $e->getMessage());
            $logs = collect([]);
            session()->flash('error', 'Error loading department logs: ' . $e->getMessage());
            return view('access.account_logs', compact('logs'));
        }
    }

    public function auditLogs()
    {
        try {
            // Get all system audit logs with user information
            $logs = AccessLog::with('user')
                ->latest()
                ->get();
            
            // If no logs exist, create some sample audit logs for demonstration
            if ($logs->count() === 0) {
                $this->createSampleAuditLogs();
                $logs = AccessLog::with('user')
                    ->latest()
                    ->get();
            }
            
            return view('access.audit_logs', compact('logs'));
            
        } catch (\Exception $e) {
            \Log::error('Error loading audit logs: ' . $e->getMessage());
            $logs = collect([]);
            session()->flash('error', 'Error loading audit logs: ' . $e->getMessage());
            return view('access.audit_logs', compact('logs'));
        }
    }

    /**
     * Create sample department logs for demonstration
     */
    private function createSampleDepartmentLogs()
    {
        try {
            // Get some sample users from DeptAccount
            $users = \App\Models\DeptAccount::take(5)->get();
            
            if ($users->count() > 0) {
                foreach ($users as $index => $user) {
                    AccessLog::create([
                        'user_id' => $user->Dept_no,
                        'action' => 'Login',
                        'description' => 'Login Successful',
                        'ip_address' => '127.0.0.1'
                    ]);
                }
            }
        } catch (\Exception $e) {
            \Log::error('Error creating sample department logs: ' . $e->getMessage());
        }
    }

    /**
     * Create sample audit logs for demonstration
     */
    private function createSampleAuditLogs()
    {
        try {
            // Get some sample users from DeptAccount
            $users = \App\Models\DeptAccount::take(3)->get();
            
            if ($users->count() > 0) {
                $sampleActions = [
                    'Table added' => 'Table Management',
                    'Login' => 'Authentication',
                    'Document_uploaded' => 'Document Management',
                    'Access_control_check' => 'Security',
                    'Profile_updated' => 'User Management'
                ];
                
                foreach ($users as $index => $user) {
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
            \Log::error('Error creating sample audit logs: ' . $e->getMessage());
        }
    }
}