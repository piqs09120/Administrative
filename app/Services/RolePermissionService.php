<?php

namespace App\Services;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\DB;

class RolePermissionService
{
    /**
     * Role permissions mapping
     */
    const ROLE_PERMISSIONS = [
        'Super Admin' => [
            'dashboard',
            'legal',
            'document',
            'visitor',
            'facilities',
            'access'
        ],
        'Administrator' => [
            'dashboard',
            'legal',
            'document',
            'visitor',
            'facilities',
            'access'
        ],
        'Legal Officer' => [
            'dashboard',
            'legal'
        ],
        'Receptionist' => [
            'visitor'
        ]
    ];

    /**
     * Get current user's role
     */
    public function getUserRole()
    {
        \Log::info('RolePermissionService: getUserRole called', [
            'session_has_user_role' => Session::has('user_role'),
            'session_user_role' => Session::get('user_role'),
            'session_emp_id' => Session::get('emp_id'),
            'auth_check' => Auth::check()
        ]);

        if (Session::has('user_role')) {
            $sessionRole = Session::get('user_role');
            \Log::info('RolePermissionService: Role found in session', ['role' => $sessionRole]);
            
            // Check if the session role needs normalization
            $normalizedSessionRole = $this->normalizeRoleName($sessionRole);
            if ($normalizedSessionRole && $normalizedSessionRole !== $sessionRole) {
                \Log::info('RolePermissionService: Updating session with normalized role', [
                    'old_session_role' => $sessionRole,
                    'new_normalized_role' => $normalizedSessionRole
                ]);
                Session::put('user_role', $normalizedSessionRole);
                return $normalizedSessionRole;
            }
            
            return $sessionRole;
        }

        if (!Auth::check()) {
            \Log::warning('RolePermissionService: User not authenticated');
            return null;
        }

        $user = Auth::user();
        $employeeId = $user->employee_id ?? Session::get('emp_id');
        
        \Log::info('RolePermissionService: Getting role for user', [
            'user_id' => $user->id,
            'auth_employee_id' => $user->employee_id,
            'session_emp_id' => Session::get('emp_id'),
            'final_employee_id' => $employeeId,
            'email' => $user->email
        ]);
        
        if (!$employeeId) {
            \Log::error('RolePermissionService: No employee ID found in user object or session');
            return null;
        }
        
        $deptAccount = DB::table('department_accounts')
            ->where('employee_id', $employeeId)
            ->first();

        \Log::info('RolePermissionService: Department account query result', [
            'found' => $deptAccount ? true : false,
            'dept_account' => $deptAccount ? (array) $deptAccount : null,
            'query_employee_id' => $employeeId
        ]);

        if ($deptAccount && $deptAccount->role) {
            // Normalize the role name to match our permission keys
            $normalizedRole = $this->normalizeRoleName($deptAccount->role);
            \Log::info('RolePermissionService: Normalizing role', [
                'original_role' => $deptAccount->role,
                'normalized_role' => $normalizedRole,
                'dept_account_id' => $deptAccount->Dept_no ?? 'unknown'
            ]);
            
            if ($normalizedRole) {
                \Log::info('RolePermissionService: Storing normalized role in session', ['role' => $normalizedRole]);
                Session::put('user_role', $normalizedRole);
                return $normalizedRole;
            } else {
                \Log::error('RolePermissionService: Role normalization failed', [
                    'original_role' => $deptAccount->role,
                    'available_roles' => array_keys(self::ROLE_PERMISSIONS),
                    'dept_account_id' => $deptAccount->Dept_no ?? 'unknown',
                    'employee_id' => $employeeId
                ]);
            }
        }

        \Log::warning('RolePermissionService: No role found for user', [
            'user_id' => $user->id,
            'employee_id' => $employeeId,
            'dept_account_exists' => $deptAccount ? true : false,
            'dept_account_role' => $deptAccount ? $deptAccount->role : null
        ]);
        return null;
    }

    /**
     * Test method to debug role normalization
     */
    public function testRoleNormalization($testRole)
    {
        \Log::info('RolePermissionService: Testing role normalization', [
            'test_role' => $testRole,
            'available_roles' => array_keys(self::ROLE_PERMISSIONS)
        ]);
        
        $result = $this->normalizeRoleName($testRole);
        
        \Log::info('RolePermissionService: Test result', [
            'test_role' => $testRole,
            'normalized_result' => $result
        ]);
        
        return $result;
    }

    /**
     * Normalize role name to match permission keys
     */
    private function normalizeRoleName($role)
    {
        if (!$role) return null;
        
        $role = trim($role);
        $availableRoles = array_keys(self::ROLE_PERMISSIONS);
        
        \Log::info('RolePermissionService: Normalizing role', [
            'original_role' => $role,
            'available_roles' => $availableRoles
        ]);
        
        // Exact match
        if (in_array($role, $availableRoles)) {
            return $role;
        }
        
        // Case-insensitive match
        foreach ($availableRoles as $availableRole) {
            if (strtolower($role) === strtolower($availableRole)) {
                return $availableRole;
            }
        }
        
        // Partial match (e.g., "legal_officer" matches "Legal Officer")
        $normalizedRole = str_replace(['_', '-'], ' ', strtolower($role));
        foreach ($availableRoles as $availableRole) {
            if (strtolower($availableRole) === $normalizedRole) {
                return $availableRole;
            }
        }
        
        // More flexible matching - check if role contains key words
        $roleLower = strtolower($role);
        foreach ($availableRoles as $availableRole) {
            $availableRoleLower = strtolower($availableRole);
            
            // Check if the role contains key words from available roles
            $words = explode(' ', $availableRoleLower);
            $matchCount = 0;
            foreach ($words as $word) {
                if (strlen($word) > 2 && strpos($roleLower, $word) !== false) {
                    $matchCount++;
                }
            }
            
            // If more than half the words match, consider it a match
            if ($matchCount > 0 && $matchCount >= count($words) / 2) {
                \Log::info('RolePermissionService: Partial word match found', [
                    'original_role' => $role,
                    'matched_role' => $availableRole,
                    'match_count' => $matchCount,
                    'total_words' => count($words)
                ]);
                return $availableRole;
            }
        }
        
        // Special case for "Legal officer" -> "Legal Officer"
        if (strtolower($role) === 'legal officer') {
            \Log::info('RolePermissionService: Special case match for Legal officer', [
                'original_role' => $role,
                'matched_role' => 'Legal Officer',
                'normalization_working' => true
            ]);
            return 'Legal Officer';
        }
        
        // Special case for "Receptionist" -> "Receptionist"
        if (strtolower($role) === 'receptionist') {
            \Log::info('RolePermissionService: Special case match for Receptionist', [
                'original_role' => $role,
                'matched_role' => 'Receptionist'
            ]);
            return 'Receptionist';
        }
        
        // Special case for "Administrator" -> "Administrator"
        if (strtolower($role) === 'administrator') {
            \Log::info('RolePermissionService: Special case match for Administrator', [
                'original_role' => $role,
                'matched_role' => 'Administrator',
                'normalization_working' => true
            ]);
            return 'Administrator';
        }
        
        // Additional check for "Administrator" with different casing
        if (strtolower($role) === 'administrator' || strtolower($role) === 'admin') {
            \Log::info('RolePermissionService: Administrator role detected', [
                'original_role' => $role,
                'matched_role' => 'Administrator',
                'casing_check' => true
            ]);
            return 'Administrator';
        }
        
        // Special case for "Super Admin" -> "Super Admin"
        if (strtolower($role) === 'super admin') {
            \Log::info('RolePermissionService: Special case match for Super Admin', [
                'original_role' => $role,
                'matched_role' => 'Super Admin'
            ]);
            return 'Super Admin';
        }
        
        \Log::warning('RolePermissionService: Could not normalize role', [
            'original_role' => $role,
            'available_roles' => $availableRoles
        ]);
        
        // Fallback: Try to find a close match
        $roleLower = strtolower($role);
        foreach ($availableRoles as $availableRole) {
            $availableRoleLower = strtolower($availableRole);
            if (strpos($availableRoleLower, $roleLower) !== false || strpos($roleLower, $availableRoleLower) !== false) {
                \Log::info('RolePermissionService: Fallback match found', [
                    'original_role' => $role,
                    'matched_role' => $availableRole,
                    'fallback_type' => 'partial_string_match'
                ]);
                return $availableRole;
            }
        }
        
        // Last resort: return the original role if it's close enough
        \Log::warning('RolePermissionService: Using original role as fallback', [
            'original_role' => $role
        ]);
        return $role;
    }

    /**
     * Check if user has access to a specific module
     */
    public function hasModuleAccess($module)
    {
        $userRole = $this->getUserRole();
        if (!$userRole) {
            return false;
        }

        return in_array($module, self::ROLE_PERMISSIONS[$userRole] ?? []);
    }

    /**
     * Get modules accessible to current user
     */
    public function getUserModules()
    {
        $userRole = $this->getUserRole();
        if (!$userRole) {
            return [];
        }

        return self::ROLE_PERMISSIONS[$userRole] ?? [];
    }

    /**
     * Get role description
     */
    public function getRoleDescription()
    {
        $userRole = $this->getUserRole();
        if (!$userRole) {
            return 'Unknown Role';
        }

        $descriptions = [
            'Super Admin' => 'Full system access and control',
            'Administrator' => 'System administration and management',
            'Legal Officer' => 'Legal case management and documentation',
            'Receptionist' => 'Visitor management and front desk operations'
        ];

        return $descriptions[$userRole] ?? 'Role description not available';
    }

    /**
     * Get available roles
     */
    public function getAvailableRoles()
    {
        return array_keys(self::ROLE_PERMISSIONS);
    }

    /**
     * Check if user can access a specific route
     */
    public function canAccessRoute($routeName)
    {
        $userRole = $this->getUserRole();
        if (!$userRole) {
            return false;
        }

        $module = $this->extractModuleFromRoute($routeName);
        return $this->hasModuleAccess($module);
    }

    /**
     * Extract module name from route
     */
    private function extractModuleFromRoute($routeName)
    {
        $routeMap = [
            'dashboard' => 'dashboard',
            'legal.' => 'legal',
            'document.' => 'document',
            'visitor.' => 'visitor',
            'facilities.' => 'facilities',
            'access.' => 'access'
        ];

        foreach ($routeMap as $prefix => $module) {
            if (str_starts_with($routeName, $prefix)) {
                return $module;
            }
        }

        return 'dashboard'; // Default fallback
    }

    /**
     * Get sidebar modules for current user
     */
    public function getSidebarModules()
    {
        $userRole = $this->getUserRole();
        \Log::info('RolePermissionService: getSidebarModules called', [
            'user_role' => $userRole,
            'has_role' => $userRole ? true : false,
            'auth_check' => Auth::check(),
            'session_user_role' => Session::get('user_role'),
            'user_id' => Auth::id()
        ]);
        
        if (!$userRole) {
            \Log::warning('RolePermissionService: No user role found for sidebar modules');
            return [];
        }

        $modules = [];
        $userModules = self::ROLE_PERMISSIONS[$userRole] ?? [];
        
        \Log::info('RolePermissionService: User modules from permissions', [
            'user_role' => $userRole,
            'available_permissions' => self::ROLE_PERMISSIONS,
            'user_modules' => $userModules
        ]);

        foreach ($userModules as $module) {
            $modules[$module] = true;
        }

        \Log::info('RolePermissionService: Final sidebar modules', [
            'user_role' => $userRole,
            'modules' => $modules
        ]);

        return $modules;
    }
}
