<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\DB;

class CheckUserRole
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next, ...$roles)
    {
        // Check if user is authenticated
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        // Get user role from session or fetch from database
        $userRole = $this->getUserRole();
        
        if (!$userRole) {
            Auth::logout();
            return redirect()->route('login')->with('error', 'User role not found. Please contact administrator.');
        }

        // If no specific roles are required, allow access
        if (empty($roles)) {
            return $next($request);
        }

        // Check if user has any of the required roles
        if (in_array($userRole, $roles)) {
            return $next($request);
        }

        // User doesn't have required role - redirect with error
        return redirect()->route('dashboard')->with('error', 'Access denied. You do not have permission to access this module.');
    }

    /**
     * Get user role from session or database
     */
    private function getUserRole()
    {
        // First try to get from session (check both keys for compatibility)
        if (Session::has('user_role')) {
            return Session::get('user_role');
        }
        
        if (Session::has('role')) {
            return Session::get('role');
        }

        // If not in session, fetch from database
        $user = Auth::user();
        if (!$user) {
            return null;
        }

        // Get role from department_accounts table
        $deptAccount = DB::table('department_accounts')
            ->where('employee_id', $user->employee_id)
            ->first();

        if ($deptAccount && $deptAccount->role) {
            // Store in session for future use (use both keys for compatibility)
            Session::put('user_role', $deptAccount->role);
            Session::put('role', $deptAccount->role);
            return $deptAccount->role;
        }

        return null;
    }
}
