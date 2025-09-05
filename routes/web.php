<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ReservationController;
use App\Http\Controllers\GuestController;
use App\Http\Controllers\AccessController;
use App\Http\Controllers\DocumentController;
use App\Http\Controllers\LegalController;
use App\Http\Controllers\VisitorController;
use App\Http\Controllers\FacilitiesController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;


/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// Landing page route
Route::get('/', [App\Http\Controllers\LandingController::class, 'index'])->name('landing');

// Redirect authenticated users to dashboard
Route::get('/home', function () {
    if (Auth::check()) {
        return redirect()->route('dashboard');
    }
    return redirect()->route('landing');
})->name('home');

// Test route for debugging access logs (temporary - remove in production)
Route::get('/test-access-logs', function () {
    try {
        $totalLogs = \App\Models\AccessLog::count();
        $sampleLogs = \App\Models\AccessLog::take(5)->get();
        
        return response()->json([
            'success' => true,
            'total_logs' => $totalLogs,
            'sample_logs' => $sampleLogs,
            'database_connection' => config('database.default'),
            'database_name' => config('database.connections.mysql.database'),
            'user_authenticated' => auth()->check(),
            'current_user' => auth()->user()
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'error' => $e->getMessage(),
            'file' => $e->getFile(),
            'line' => $e->getLine()
        ]);
    }
});

// Authentication Routes (single, non-conflicting)
Route::get('/login', [App\Http\Controllers\AuthController::class, 'showLogin'])->name('login');
Route::post('/loginuser', [App\Http\Controllers\userController::class, 'login'])->name('user.login');
Route::post('/logout', [App\Http\Controllers\userController::class, 'logout'])->name('logout');

// (Optional) OTP endpoints retained but with unique URIs if needed in future
Route::get('/login/otp', [App\Http\Controllers\AuthController::class, 'showOTP'])->name('login.otp');
Route::post('/verify-otp', [App\Http\Controllers\AuthController::class, 'verifyOTP'])->name('verify.otp');

// Guest routes
Route::post('/guest/create', [App\Http\Controllers\userController::class, 'create'])->name('guest.create');
Route::post('/guest/profile-setup/{guestID}', [App\Http\Controllers\userController::class, 'profilesetup'])->name('guest.profilesetup');
Route::post('/guest/logout', [App\Http\Controllers\userController::class, 'guestlogout'])->name('guest.logout');
Route::post('/guest/login', [App\Http\Controllers\userController::class, 'guestlogin'])->name('guest.login');

// Legal Documents - accessible to Legal Officers, Administrators, and Super Admins
Route::middleware(['auth', 'role:Legal Officer,Administrator,Super Admin'])->group(function () {
    Route::get('/legal/documents', [LegalController::class, 'legalDocuments'])->name('legal.legal_documents');
});

// Temporary: Test legal cases route without auth
Route::get('/test/legal/cases', [LegalController::class, 'caseDeck'])->name('test.legal.cases');

// Test route for debugging file upload
Route::post('/test-upload', function(Request $request) {
    \Log::info('Test upload received', [
        'has_file' => $request->hasFile('document_file'),
        'file_name' => $request->file('document_file') ? $request->file('document_file')->getClientOriginalName() : 'no file',
        'all_data' => $request->all()
    ]);
    
    if ($request->hasFile('document_file')) {
        $file = $request->file('document_file');
        $fileName = time() . '_' . $file->getClientOriginalName();
        $filePath = $file->storeAs('test_uploads', $fileName, 'public');
        
        return response()->json([
            'success' => true,
            'message' => 'File uploaded successfully',
            'file_path' => $filePath,
            'file_name' => $fileName
        ]);
    }
    
    return response()->json([
        'success' => false,
        'message' => 'No file received',
        'data' => $request->all()
    ]);
})->name('test.upload');

// Test route for AI analysis
Route::get('/debug-ai', function() {
    try {
        $geminiService = app(\App\Services\GeminiService::class);
        $testText = "This is a memorandum of agreement between Company A and Company B regarding the purchase of office supplies.";
        $result = $geminiService->analyzeDocument($testText);
        
        return response()->json([
            'success' => true,
            'test_text' => $testText,
            'ai_result' => $result,
            'api_key_set' => !empty(env('GEMINI_API_KEY'))
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ]);
    }
})->name('debug.ai');

// Test route for RBAC system
Route::get('/debug-rbac', function() {
    try {
        $roleService = app(\App\Services\RolePermissionService::class);
        $userRole = $roleService->getUserRole();
        $userModules = $roleService->getUserModules();
        $roleDescription = $roleService->getRoleDescription();
        
        // Get department accounts to see actual role values
        $deptAccounts = \Illuminate\Support\Facades\DB::table('department_accounts')
            ->select('employee_id', 'employee_name', 'role')
            ->get();
        
        // Get current user info
        $currentUser = auth()->user();
        $currentUserDeptAccount = null;
        if ($currentUser) {
            $currentUserDeptAccount = \Illuminate\Support\Facades\DB::table('department_accounts')
                ->where('employee_id', $currentUser->employee_id)
                ->first();
        }
        
        // Test role normalization directly
        $testResults = [];
        if ($currentUserDeptAccount && $currentUserDeptAccount->role) {
            $testResults['legal_officer'] = $roleService->testRoleNormalization('Legal officer');
            $testResults['legal_officer_lower'] = $roleService->testRoleNormalization('legal officer');
            $testResults['legal_officer_underscore'] = $roleService->testRoleNormalization('legal_officer');
        }
        
        return response()->json([
            'success' => true,
            'user_role' => $userRole,
            'user_modules' => $userModules,
            'role_description' => $roleDescription,
            'available_roles' => $roleService->getAvailableRoles(),
            'session_data' => [
                'user_role' => session('user_role'),
                'emp_id' => session('emp_id'),
                'auth_user' => auth()->user() ? auth()->user()->only(['id', 'name', 'email', 'role', 'employee_id']) : null
            ],
            'department_accounts' => $deptAccounts,
            'auth_check' => auth()->check(),
            'current_user_employee_id' => auth()->user() ? auth()->user()->employee_id : null,
            'current_user_dept_account' => $currentUserDeptAccount,
            'role_permissions' => $roleService::ROLE_PERMISSIONS,
            'test_results' => $testResults
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ]);
    }
})->name('debug.rbac');

// Test route for analyze-upload endpoint
Route::post('/test-analyze', function(Request $request) {
    \Log::info('Test analyze endpoint hit', [
        'has_file' => $request->hasFile('document_file'),
        'file_name' => $request->file('document_file') ? $request->file('document_file')->getClientOriginalName() : 'no file',
        'all_data' => $request->all()
    ]);
    
    return response()->json([
        'success' => true,
        'message' => 'Test endpoint working',
        'has_file' => $request->hasFile('document_file'),
        'file_name' => $request->file('document_file') ? $request->file('document_file')->getClientOriginalName() : 'no file'
    ]);
})->name('test.analyze');

// All main app routes require authentication
Route::middleware(['auth'])->group(function () {
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    // Profile Management
    Route::get('/profile/edit', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::get('/profile', [App\Http\Controllers\ProfileController::class, 'show'])->name('profile.show');
    Route::post('/profile/send-verification', [ProfileController::class, 'send'])->name('verification.send');
    Route::put('/profile', [ProfileController::class, 'update'])->name('password.update');

    // Hotel Management
    Route::resource('reservations', ReservationController::class);
    Route::resource('guests', GuestController::class);
    // Route::resource('orders', OrderController::class); // Commented out - controller doesn't exist
    // Route::resource('inventory', InventoryController::class); // Commented out - controller doesn't exist
    
    // Finance and Reports
    Route::get('/finance/reports', function () {
        return view('finance.reports');
    })->name('finance.reports');
    
    
    
    // Access Protection
    Route::prefix('access')->group(function () {
    Route::get('/logs', [AccessController::class, 'logs'])->name('access.logs');
    Route::get('/audit-logs', [AccessController::class, 'auditLogs'])->name('access.audit_logs');
    Route::get('/users', [AccessController::class, 'users'])->name('access.users');
    Route::get('/users/{id}', [AccessController::class, 'showUser'])->name('access.users.show');
    Route::get('/users/create', [AccessController::class, 'createUser'])->name('access.users.create');
    Route::post('/users', [AccessController::class, 'storeUser'])->name('access.users.store');
    Route::get('/users/export', [AccessController::class, 'exportUsers'])->name('access.users.export');
    Route::get('/roles', [AccessController::class, 'roles'])->name('access.roles');
    Route::get('/security', [AccessController::class, 'security'])->name('access.security');
    Route::get('/department-accounts', [AccessController::class, 'departmentAccounts'])->name('access.department_accounts');
    Route::post('/department-accounts', [AccessController::class, 'storeDepartmentAccount'])->name('access.department_accounts.store');
    Route::get('/department-accounts/{id}', [AccessController::class, 'showDepartmentAccount'])->name('access.department_accounts.show');
    Route::put('/department-accounts/{id}', [AccessController::class, 'updateDepartmentAccount'])->name('access.department_accounts.update');
    Route::post('/department-accounts/{id}/toggle', [AccessController::class, 'toggleDepartmentAccountStatus'])->name('access.department_accounts.toggle');
    Route::get('/department-logs', [AccessController::class, 'departmentLogs'])->name('access.department_logs');
    Route::get('/account-logs/export', [AccessController::class, 'exportAccountLogs'])->name('access.account_logs.export');
    Route::get('/audit-logs/export', [AccessController::class, 'exportAuditLogs'])->name('access.audit_logs.export');
});
    
    
    
    // Settings
    Route::get('/settings', function () {
        return view('settings');
    })->name('settings');
    
    // Profile
    
    Route::resource('legal', LegalController::class);
    
    // Legal Management Sub-modules - Administrator and Super Admin only
    Route::middleware(['auth', 'role:Administrator,Super Admin'])->group(function () {
        Route::get('/legal', [LegalController::class, 'caseDeck'])->name('legal.case_deck');
        Route::get('/legal/cases', [LegalController::class, 'caseDeck'])->name('legal.legal_cases');
    });
    
    // Legal Documents route is now properly protected above
    
    // Legal Case Management - Administrator and Super Admin only (Legal Officers excluded)
    Route::middleware(['auth', 'role:Administrator,Super Admin'])->group(function () {
        Route::get('/legal/cases/create', [LegalController::class, 'create'])->name('legal.create');
        Route::post('/legal/cases', [LegalController::class, 'store'])->name('legal.store');
        Route::get('/legal/cases/{id}', [LegalController::class, 'show'])->name('legal.cases.show');
        Route::get('/legal/cases/{id}/edit', [LegalController::class, 'edit'])->name('legal.cases.edit');
        Route::put('/legal/cases/{id}', [LegalController::class, 'update'])->name('legal.cases.update');
        Route::delete('/legal/cases/{id}', [LegalController::class, 'destroy'])->name('legal.cases.destroy');
    });
    
    // Document Management - Administrator, Super Admin only
    Route::middleware(['auth', 'role:Administrator,Super Admin'])->group(function () {
        Route::resource('document', DocumentController::class)->where(['document' => '[0-9]+']);
        Route::resource('facilities', FacilitiesController::class);
        Route::get('/facilities/{id}/ajax', [FacilitiesController::class, 'showAjax'])->name('facilities.showAjax');
        Route::get('/facilities-calendar', [FacilitiesController::class, 'calendar'])->name('facilities.calendar');
        Route::post('/facilities-check-availability', [FacilitiesController::class, 'checkAvailability'])->name('facilities.checkAvailability');
        Route::get('/my-reservations', [App\Http\Controllers\FacilityReservationController::class, 'userHistory'])->name('facility_reservations.user_history');
        Route::get('/admin-analytics', [App\Http\Controllers\FacilityReservationController::class, 'adminAnalytics'])->name('facility_reservations.admin_analytics');
        Route::post('/payments/process', [App\Http\Controllers\PaymentController::class, 'processPayment'])->name('payments.process');
        Route::get('/payments/details/{reservationId}', [App\Http\Controllers\PaymentController::class, 'getPaymentDetails'])->name('payments.details');
        Route::get('/payments/history', [App\Http\Controllers\PaymentController::class, 'paymentHistory'])->name('payments.history');
    });
    
    // Visitor Management - Receptionist, Administrator, Super Admin only
    Route::middleware(['auth', 'role:Receptionist,Administrator,Super Admin'])->group(function () {
        Route::resource('visitor', VisitorController::class);
    });
    
    // Document Management Routes
    Route::post('/document/{id}/request-release', [DocumentController::class, 'requestRelease'])->name('document.requestRelease');
    Route::get('/document/{id}/download', [DocumentController::class, 'download'])->name('document.download');
    Route::post('/document/{id}/analyze', [DocumentController::class, 'analyze'])->name('document.analyze');
Route::post('/document/{id}/analyze-ajax', [DocumentController::class, 'analyzeAjax'])->name('document.analyzeAjax');
    // Use a unique name for the upload analysis endpoint to avoid name collisions
    Route::post('/document/analyze-upload', [DocumentController::class, 'analyzeUpload'])->name('document.analyzeUpload');
    // OCR test route for debugging document analysis
    Route::post('/document/test-ocr', [DocumentController::class, 'testOcrExtraction'])->name('document.testOcr');
    // OCR test page for debugging
    Route::get('/document/test-ocr', function() {
        return view('document.test_ocr');
    })->name('document.testOcrPage');
    // Bulk upload route for legal management
    Route::post('/document/bulk-upload', [DocumentController::class, 'bulkUpload'])->name('document.bulkUpload');
    
    // Document Archive Routes
    Route::post('/document/{id}/archive', [DocumentController::class, 'archive'])->name('document.archive');
    Route::post('/document/{id}/unarchive', [DocumentController::class, 'unarchive'])->name('document.unarchive');
    Route::get('/document/archived', [DocumentController::class, 'archived'])->name('document.archived');

    // Legal Approval Routes
    Route::post('/legal/{id}/approve', [LegalController::class, 'approveRequest'])->name('legal.approve');
    Route::post('/legal/{id}/deny', [LegalController::class, 'denyRequest'])->name('legal.deny');
    Route::get('/legal/pending', [LegalController::class, 'pendingRequests'])->name('legal.pending');
    Route::get('/legal/approved', [LegalController::class, 'approvedRequests'])->name('legal.approved');
    Route::get('/legal/denied', [LegalController::class, 'deniedRequests'])->name('legal.denied');
    
    // Legal Case Approval Routes - Administrator and Super Admin only
    Route::middleware(['auth', 'role:Administrator,Super Admin'])->group(function () {
        Route::post('/legal/cases/{id}/approve', [LegalController::class, 'approveCase'])->name('legal.cases.approve');
        Route::post('/legal/cases/{id}/decline', [LegalController::class, 'declineCase'])->name('legal.cases.decline');
    });

// Super Admin Routes
Route::get('/hr1', function () { return view('superadmin.hr1'); })->name('hr1.index');
Route::get('/hr2', function () { return view('superadmin.hr2'); })->name('hr2.index');
Route::get('/hr3', function () { return view('superadmin.hr3'); })->name('hr3.index');
Route::get('/hr4', function () { return view('superadmin.hr4'); })->name('hr4.index');
Route::get('/financials', function () { return view('superadmin.financials'); })->name('financials.index');
Route::get('/logistic1', function () { return view('superadmin.logistic1'); })->name('logistic1.index');
Route::get('/logistic2', function () { return view('superadmin.logistic2'); })->name('logistic2.index');
Route::get('/hotel', function () { return view('superadmin.hotel'); })->name('hotel.index');
Route::get('/restaurant', function () { return view('superadmin.restaurant'); })->name('restaurant.index');
Route::get('/superadmin/users', function () { return view('superadmin.users'); })->name('superadmin.users');
    
    // Legal Document Categories
    Route::get('/legal/category/{category}', [LegalController::class, 'categoryDocuments'])->name('legal.category');

    // Legal Document Management Routes
    Route::get('/legal/documents/{id}', [DocumentController::class, 'showLegalDocument'])->name('legal.documents.show');
    Route::get('/legal/documents/{id}/edit', [DocumentController::class, 'editLegalDocument'])->name('legal.documents.edit');
    Route::put('/legal/documents/{id}', [DocumentController::class, 'updateLegalDocument'])->name('legal.documents.update');
    Route::delete('/legal/documents/{id}', [DocumentController::class, 'deleteLegalDocument'])->name('legal.documents.destroy');
    
   // Legal Document Download Route
   Route::get('/legal/documents/{id}/download', [DocumentController::class, 'downloadLegalDocument'])->name('legal.documents.download');

    // Facility Reservation Approval Workflow
    Route::resource('facility_reservations', App\Http\Controllers\FacilityReservationController::class);
    Route::post('/facility_reservations/{id}/approve', [App\Http\Controllers\FacilityReservationController::class, 'approve'])->name('facility_reservations.approve');
    Route::post('/facility_reservations/{id}/deny', [App\Http\Controllers\FacilityReservationController::class, 'deny'])->name('facility_reservations.deny');
    
    // Legal Review Routes
    Route::get('/facility_reservations/{id}/legal-review', [App\Http\Controllers\FacilityReservationController::class, 'legalReview'])->name('facility_reservations.legal_review');
    Route::post('/facility_reservations/{id}/legal-approve', [App\Http\Controllers\FacilityReservationController::class, 'legalApprove'])->name('facility_reservations.legal_approve');
    Route::post('/facility_reservations/{id}/legal-flag', [App\Http\Controllers\FacilityReservationController::class, 'legalFlag'])->name('facility_reservations.legal_flag');
    
    // Workflow Action Routes
    Route::post('/facility_reservations/{id}/availability-check', [App\Http\Controllers\FacilityReservationController::class, 'performAvailabilityCheck'])->name('facility_reservations.availability_check');
    Route::post('/facility_reservations/{id}/conflict-resolution', [App\Http\Controllers\FacilityReservationController::class, 'performConflictResolution'])->name('facility_reservations.conflict_resolution');
    
    // Visitor Coordination Routes
    // Route::post('/facility_reservations/{id}/extract-visitors', [App\Http\Controllers\FacilityReservationController::class, 'extractVisitorData'])->name('facility_reservations.extract_visitors');
    // Route::post('/facility_reservations/{id}/approve-visitors', [App\Http\Controllers\FacilityReservationController::class, 'approveVisitors'])->name('facility_reservations.approve_visitors');

    // Visitor Export Routes
    Route::get('/visitor/export/excel', [App\Http\Controllers\VisitorController::class, 'exportExcel'])->name('visitor.export.excel');
    Route::get('/visitor/export/pdf', [App\Http\Controllers\VisitorController::class, 'exportPdf'])->name('visitor.export.pdf');

    // Visitor AJAX Routes for Real-time Functionality
    Route::prefix('visitor')->name('visitor.')->group(function () {
        // New route for managing visitors from facility reservations
        Route::get('/manage-reservation-visitors/{reservation}', [App\Http\Controllers\VisitorController::class, 'manageReservationVisitors'])->name('manage_reservation_visitors');
        Route::post('/perform-extraction/{reservation}', [App\Http\Controllers\VisitorController::class, 'performExtractionFromReservation'])->name('perform_extraction_from_reservation');
        Route::post('/perform-approval/{reservation}', [App\Http\Controllers\VisitorController::class, 'performApprovalFromReservation'])->name('perform_approval_from_reservation');

        Route::post('/search', [App\Http\Controllers\VisitorController::class, 'searchVisitors'])->name('search');
        Route::get('/details/{id}', [App\Http\Controllers\VisitorController::class, 'getVisitorDetails'])->name('details');
        Route::post('/checkin', [App\Http\Controllers\VisitorController::class, 'checkIn'])->name('checkin');
        Route::post('/checkout/{id}', [App\Http\Controllers\VisitorController::class, 'checkOut'])->name('checkout');
        Route::get('/current', [App\Http\Controllers\VisitorController::class, 'getCurrentVisitors'])->name('current');
        Route::get('/scheduled', [App\Http\Controllers\VisitorController::class, 'getScheduledVisits'])->name('scheduled');
        Route::get('/stats', [App\Http\Controllers\VisitorController::class, 'getVisitorStats'])->name('stats');
        
        // Quick Actions
        Route::get('/quick/view-all', [App\Http\Controllers\VisitorController::class, 'viewAllVisitors'])->name('quick.viewAll');
        Route::post('/quick/schedule', [App\Http\Controllers\VisitorController::class, 'scheduleVisit'])->name('quick.schedule');
        Route::post('/quick/emergency', [App\Http\Controllers\VisitorController::class, 'emergencyEvacuation'])->name('quick.emergency');
        Route::get('/quick/directory', [App\Http\Controllers\VisitorController::class, 'buildingDirectory'])->name('quick.directory');
    });

    // Visitor Logs Routes
    Route::prefix('visitor-logs')->name('visitor.logs.')->group(function () {
        Route::get('/', [App\Http\Controllers\VisitorLogController::class, 'index'])->name('index');
        Route::get('/analytics', [App\Http\Controllers\VisitorLogController::class, 'getAnalytics'])->name('analytics');
        Route::get('/logs', [App\Http\Controllers\VisitorLogController::class, 'getLogs'])->name('logs');
        Route::post('/search', [App\Http\Controllers\VisitorLogController::class, 'search'])->name('search');
        Route::post('/generate-report', [App\Http\Controllers\VisitorLogController::class, 'generateReport'])->name('generate-report');
        Route::get('/export', [App\Http\Controllers\VisitorLogController::class, 'exportLogs'])->name('export');
    });

    // Notifications
    Route::get('/notifications', [App\Http\Controllers\NotificationController::class, 'index'])->name('notifications.index');
    Route::post('/notifications/{id}/mark-as-read', [App\Http\Controllers\NotificationController::class, 'markAsRead'])->name('notifications.markAsRead');
    Route::post('/notifications/mark-all-as-read', [App\Http\Controllers\NotificationController::class, 'markAllAsRead'])->name('notifications.markAllAsRead');

    // User Role Management - Administrator, Super Admin only
    Route::middleware(['auth', 'role:Administrator,Super Admin'])->group(function () {
        Route::get('/access/users/{user}/edit-role', [App\Http\Controllers\AccessController::class, 'editRole'])->name('access.users.editRole');
        Route::post('/access/users/{user}/update-role', [App\Http\Controllers\AccessController::class, 'updateRole'])->name('access.users.updateRole');
    });
});

require __DIR__.'/auth.php';