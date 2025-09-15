<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Providers\RouteServiceProvider;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Illuminate\Support\Facades\Session;
use App\Models\AccessLog;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        $request->session()->regenerate();

        return redirect()->intended(RouteServiceProvider::HOME);
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        // Log a logout action into AccessLog (DeptAccount-based)
        try {
            $currentUser = Auth::user();
            $userName = 'Unknown User';
            $userRole = 'No role';
            $deptNo = null;
            
            if ($currentUser) {
                $userName = $currentUser->name ?? $currentUser->employee_name ?? 'Unknown User';
                $userRole = $currentUser->role ?? 'No role';
                
                // Try to find DeptAccount record
                $empId = Session::get('emp_id') ?? $currentUser->employee_id;
                if ($empId) {
                    $deptAccount = \App\Models\DeptAccount::where('employee_id', $empId)->first();
                    if ($deptAccount) {
                        $deptNo = $deptAccount->Dept_no;
                        $userName = $deptAccount->employee_name;
                        $userRole = $deptAccount->role;
                    }
                }
                
                // If still no DeptAccount, try to create one or use a fallback
                if (!$deptAccount) {
                    // Create a temporary DeptAccount entry for audit logging
                    $deptAccount = \App\Models\DeptAccount::create([
                        'Dept_id' => 'TEMP_' . time(),
                        'dept_name' => $currentUser->department ?? 'Administrative',
                        'employee_name' => $userName,
                        'employee_id' => $empId ?? 'temp_' . time(),
                        'role' => $userRole,
                        'email' => $currentUser->email,
                        'status' => 'active',
                        'password' => bcrypt('temp')
                    ]);
                    $deptNo = $deptAccount->Dept_no;
                }
            }
            
            // Log the logout action
            if ($deptNo) {
                AccessLog::create([
                    'user_id' => $deptNo,
                    'action' => 'Logout',
                    'description' => 'User logged out successfully',
                    'ip_address' => $request->ip(),
                ]);
            }
        } catch (\Throwable $e) {
            \Log::error('Error logging logout: ' . $e->getMessage());
            // ignore logging error
        }

        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
} 