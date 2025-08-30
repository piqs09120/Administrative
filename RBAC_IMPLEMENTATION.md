# Role-Based Access Control (RBAC) Implementation

## Overview

This document describes the implementation of Role-Based Access Control (RBAC) for the Soliera web application. The system uses the existing `department_accounts` table to determine user roles and restricts access to modules based on these roles.

## Role Definitions

### Available Roles

1. **Super Admin**
   - Full access to all modules and features
   - Can manage all aspects of the system

2. **Administrator**
   - Full access to all modules and features
   - Can manage users, roles, and system settings

3. **Legal Officer**
   - Access to Dashboard and Legal Management only
   - Cannot access Visitor Management, Document Management, or User Management

4. **Receptionist**
   - Access to Dashboard and Visitor Management only
   - Cannot access Legal Management, Document Management, or User Management

## Implementation Details

### Files Modified

1. **`app/Http/Middleware/CheckUserRole.php`** - New middleware for role checking
2. **`app/Services/RolePermissionService.php`** - Service class for managing role permissions
3. **`app/Http/Controllers/AuthController.php`** - Updated to store user role in session
4. **`resources/views/partials/sidebarr.blade.php`** - Updated to show only accessible modules
5. **`resources/views/dashboard.blade.php`** - Added role display and module access info
6. **`routes/web.php`** - Added role middleware to protected routes
7. **`app/Http/Kernel.php`** - Registered the role middleware

### How It Works

#### 1. Role Detection After Login

- When a user logs in via OTP verification, their role is fetched from the `department_accounts` table
- The role is stored in the session as `user_role`
- The role is also stored in the Laravel `users` table for consistency

#### 2. Sidebar Module Rendering

- The sidebar dynamically renders modules based on the logged-in user's role
- Uses `@if(isset($sidebarModules['module_name']))` to conditionally show modules
- Modules are filtered through the `RolePermissionService::getSidebarModules()` method

#### 3. Access Restriction

- Routes are protected using the `role` middleware
- Example: `Route::middleware(['auth', 'role:Legal Officer,Administrator,Super Admin'])`
- If a user tries to access a restricted module, they are redirected to the dashboard with an error message

#### 4. Scalable Structure

- New roles can be easily added to the `ROLE_PERMISSIONS` constant in `RolePermissionService`
- New modules can be added to the `getSidebarModules()` method
- The system is designed to be easily extensible

## Usage Examples

### Adding Role Middleware to Routes

```php
// Single role
Route::middleware(['auth', 'role:Legal Officer'])->group(function () {
    Route::get('/legal/cases', [LegalController::class, 'index']);
});

// Multiple roles
Route::middleware(['auth', 'role:Administrator,Super Admin'])->group(function () {
    Route::get('/admin/users', [AdminController::class, 'users']);
});
```

### Checking Role Access in Controllers

```php
use App\Services\RolePermissionService;

public function index(RolePermissionService $roleService)
{
    if (!$roleService->hasModuleAccess('legal')) {
        return redirect()->route('dashboard')->with('error', 'Access denied');
    }
    
    // Continue with controller logic
}
```

### Checking Role Access in Views

```php
@php
    $roleService = app(\App\Services\RolePermissionService::class);
@endphp

@if($roleService->hasModuleAccess('legal'))
    <div class="legal-module">
        <!-- Legal module content -->
    </div>
@endif
```

## Testing the RBAC System

### Debug Route

Visit `/debug-rbac` to see:
- Current user role
- Accessible modules
- Role description
- Session data
- Available roles

### Testing Different Roles

1. **Login as Legal Officer**: Should only see Dashboard and Legal Management in sidebar
2. **Login as Receptionist**: Should only see Dashboard and Visitor Management in sidebar
3. **Login as Administrator/Super Admin**: Should see all modules in sidebar

### Testing Access Restrictions

1. **Legal Officer trying to access `/visitor`**: Should be redirected to dashboard with error
2. **Receptionist trying to access `/legal`**: Should be redirected to dashboard with error
3. **Administrator accessing any module**: Should have full access

## Security Features

- **Session-based role checking**: Roles are stored in session for performance
- **Database fallback**: If session is lost, role is fetched from database
- **Route protection**: All sensitive routes are protected by middleware
- **UI consistency**: Sidebar only shows accessible modules
- **Error handling**: Clear error messages for access violations

## Future Enhancements

1. **Role Hierarchy**: Implement role inheritance (e.g., Super Admin > Administrator > Legal Officer)
2. **Permission Granularity**: Add specific permissions within modules (e.g., read, write, delete)
3. **Dynamic Role Management**: Allow administrators to create custom roles
4. **Audit Logging**: Log all role-based access attempts
5. **API Protection**: Extend RBAC to API endpoints

## Troubleshooting

### Common Issues

1. **Role not showing in sidebar**: Check if role is properly stored in session
2. **Access denied errors**: Verify route middleware configuration
3. **Session issues**: Check if `user_role` is properly set after login

### Debug Steps

1. Check `/debug-rbac` route for current role information
2. Verify session data in browser developer tools
3. Check database for correct role assignment
4. Verify middleware registration in `Kernel.php`

## Notes

- The existing business logic remains unchanged
- Only access control layers have been added
- The system is backward compatible
- No modifications were made to the login process at `project.test/login`
