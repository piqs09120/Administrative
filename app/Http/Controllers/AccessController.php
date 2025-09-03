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

    public function users(Request $request)
    {
        // Dynamic filter options
        $roleOptions = \App\Models\DeptAccount::whereNotNull('role')
            ->distinct()->orderBy('role')->pluck('role')->filter()->values();
        $departmentOptions = \App\Models\DeptAccount::whereNotNull('dept_name')
            ->distinct()->orderBy('dept_name')->pluck('dept_name')->filter()->values();

        // Build filters from request
        $search = trim((string) $request->get('q'));
        $role = (string) $request->get('role');
        $department = (string) $request->get('department');
        $status = (string) $request->get('status');

        $query = \App\Models\DeptAccount::query();

        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('employee_name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('employee_id', 'like', "%{$search}%");
            });
        }
        if ($role !== '') {
            $query->where('role', $role);
        }
        if ($department !== '') {
            $query->where('dept_name', $department);
        }
        if ($status !== '') {
            $query->where('status', $status);
        }

        $accounts = $query->orderBy('employee_name')
            ->paginate(10)
            ->appends($request->query());
        // Build rows for the table with an optional related Laravel user id
        $rows = $accounts->getCollection()->map(function ($acc) {
            $relatedUserId = null;
            try {
                if (!empty($acc->employee_id)) {
                    $related = User::where('email', $acc->employee_id . '@soliera.local')->first();
                    if ($related) { $relatedUserId = $related->id; }
                }
            } catch (\Throwable $e) { /* ignore lookup errors */ }

            return [
                'id' => $acc->Dept_no ?? $acc->id,
                'name' => $acc->employee_name ?? ($acc->name ?? 'Unknown User'),
                'email' => $acc->email ?? '—',
                'role' => $acc->role ?? 'Staff',
                'department' => $acc->dept_name ?? '—',
                'status' => ucfirst($acc->status ?? 'inactive'),
                'last_login' => $acc->last_login ?? now()->subDays(rand(1, 30))->format('Y-m-d H:i:s'),
                'created_at' => ($acc->created_at ?? now())->format('Y-m-d'),
                'laravel_user_id' => $relatedUserId,
            ];
        });

        $users = $rows; // array rows for blade loop

        return view('access.users', [
            'users' => $users,
            'roleOptions' => $roleOptions,
            'departmentOptions' => $departmentOptions,
            'filters' => [
                'q' => $search,
                'role' => $role,
                'department' => $department,
                'status' => $status,
            ],
        ]);
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
            ]);

            // Create new department account
            $deptAccount = new \App\Models\DeptAccount();
            $deptAccount->employee_name = $request->employee_name;
            $deptAccount->dept_name = $request->dept_name;
            $deptAccount->email = $request->email;
            $deptAccount->role = $request->role;
            $deptAccount->status = $request->status;
            $deptAccount->save();

            return redirect()->route('access.department_accounts')->with('success', 'Department account created successfully!');
            
        } catch (\Exception $e) {
            \Log::error('Error creating department account: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Error creating department account: ' . $e->getMessage())->withInput();
        }
    }

    // View a single department account (JSON)
    public function showDepartmentAccount($id)
    {
        try {
            $account = \App\Models\DeptAccount::findOrFail($id);
            return response()->json([
                'success' => true,
                'account' => $account,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Account not found',
            ], 404);
        }
    }

    // Update a department account (JSON)
    public function updateDepartmentAccount(Request $request, $id)
    {
        try {
            $request->validate([
                'employee_name' => 'sometimes|string|max:255',
                'dept_name' => 'sometimes|string|max:255',
                'email' => 'nullable|email|max:255',
                'role' => 'nullable|string|max:255',
                'status' => 'nullable|in:active,inactive',
            ]);

            $account = \App\Models\DeptAccount::findOrFail($id);
            $account->fill($request->only(['employee_name','dept_name','email','role','status']));
            $account->save();

            return response()->json([
                'success' => true,
                'account' => $account,
                'message' => 'Account updated successfully',
            ]);
        } catch (\Illuminate\Validation\ValidationException $ve) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $ve->errors(),
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Unable to update account',
            ], 500);
        }
    }

    // Toggle active/inactive status (JSON)
    public function toggleDepartmentAccountStatus($id)
    {
        try {
            $account = \App\Models\DeptAccount::findOrFail($id);
            $account->status = ($account->status === 'active') ? 'inactive' : 'active';
            $account->save();

            return response()->json([
                'success' => true,
                'status' => $account->status,
                'message' => 'Status updated',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Unable to toggle status',
            ], 500);
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

    public function createUser()
    {
        $departments = \App\Models\DeptAccount::distinct()->pluck('dept_name')->filter()->sort()->values();
        $roles = \App\Models\DeptAccount::distinct()->pluck('role')->filter()->sort()->values();
        
        return view('access.create_user', compact('departments', 'roles'));
    }

    public function storeUser(Request $request)
    {
        $request->validate([
            'employee_name' => 'required|string|max:255',
            'employee_id' => 'required|string|max:255|unique:department_accounts,employee_id',
            'email' => 'nullable|email|max:255',
            'dept_name' => 'required|string|max:255',
            'role' => 'required|string|max:255',
            'status' => 'required|in:active,inactive',
            'password' => 'required|string|min:6|confirmed',
        ]);

        try {
            $deptAccount = new \App\Models\DeptAccount();
            $deptAccount->employee_name = $request->employee_name;
            $deptAccount->employee_id = $request->employee_id;
            $deptAccount->email = $request->email;
            $deptAccount->dept_name = $request->dept_name;
            $deptAccount->role = $request->role;
            $deptAccount->status = $request->status;
            $deptAccount->password = \Illuminate\Support\Facades\Hash::make($request->password);
            $deptAccount->save();

            // Create corresponding Laravel User for authentication
            $laravelUser = \App\Models\User::create([
                'name' => $request->employee_name,
                'email' => $request->employee_id . '@soliera.local',
                'password' => \Illuminate\Support\Facades\Hash::make($request->password),
                'role' => $request->role,
                'employee_id' => $request->employee_id,
                'department' => $request->dept_name,
                'email_verified_at' => now(),
            ]);

            return redirect()->route('access.users')->with('success', 'User created successfully!');
            
        } catch (\Exception $e) {
            \Log::error('Error creating user: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Error creating user: ' . $e->getMessage())->withInput();
        }
    }

    public function exportUsers(Request $request)
    {
        try {
            // Apply same filters as the users page
            $search = trim((string) $request->get('q'));
            $role = (string) $request->get('role');
            $department = (string) $request->get('department');
            $status = (string) $request->get('status');

            $query = \App\Models\DeptAccount::query();

            if ($search !== '') {
                $query->where(function ($q) use ($search) {
                    $q->where('employee_name', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%")
                      ->orWhere('employee_id', 'like', "%{$search}%");
                });
            }
            if ($role !== '') {
                $query->where('role', $role);
            }
            if ($department !== '') {
                $query->where('dept_name', $department);
            }
            if ($status !== '') {
                $query->where('status', $status);
            }

            $accounts = $query->orderBy('employee_name')->get();

            $filename = 'users_export_' . now()->format('Y-m-d_H-i-s') . '.xlsx';
            
            return \Maatwebsite\Excel\Facades\Excel::download(new \App\Exports\UserExport($accounts), $filename);
            
        } catch (\Exception $e) {
            \Log::error('Error exporting users: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Error exporting users: ' . $e->getMessage());
        }
    }

    public function exportAccountLogs()
    {
        try {
            // Get the same logs as the departmentLogs method
            $logs = AccessLog::with('user')
                ->whereHas('user', function($query) {
                    $query->whereNotNull('dept_name');
                })
                ->latest()
                ->get();

            $filename = 'account_logs_export_' . now()->format('Y-m-d_H-i-s') . '.xlsx';
            
            return \Maatwebsite\Excel\Facades\Excel::download(new \App\Exports\AccountLogExport($logs), $filename);
            
        } catch (\Exception $e) {
            \Log::error('Error exporting account logs: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Error exporting account logs: ' . $e->getMessage());
        }
    }

    public function exportAuditLogs()
    {
        try {
            // Get all audit logs with user information
            $logs = AccessLog::with('user')
                ->latest()
                ->get();

            $filename = 'audit_logs_export_' . now()->format('Y-m-d_H-i-s') . '.xlsx';
            
            return \Maatwebsite\Excel\Facades\Excel::download(new \App\Exports\AuditLogExport($logs), $filename);
            
        } catch (\Exception $e) {
            \Log::error('Error exporting audit logs: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Error exporting audit logs: ' . $e->getMessage());
        }
    }
}