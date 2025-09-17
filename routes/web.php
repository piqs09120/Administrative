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

// Landing page routes (kept but root is redirected to login)
Route::get('/', function () { return redirect()->route('login'); });
Route::get('/landing', [App\Http\Controllers\LandingController::class, 'index'])->name('landing');
Route::get('/visitor-management', [App\Http\Controllers\LandingController::class, 'visitorManagement'])->name('visitor.management.landing');
Route::get('/facilities-reservation', [App\Http\Controllers\LandingController::class, 'facilitiesReservation'])->name('facilities.reservation.landing');
// Public endpoint to accept registration from landing page (AJAX)
Route::post('/visitor/public-store', [App\Http\Controllers\VisitorController::class, 'publicStore'])->name('visitor.public_store');

// Temporary: New Request route without auth for testing
Route::get('/facility_reservations/new-request', [App\Http\Controllers\FacilityReservationController::class, 'newRequest'])->name('facility_reservations.new_request');

// Facilities Monitoring API (read-only)
Route::get('/api/facilities/monitoring', [App\Http\Controllers\FacilityReservationController::class, 'monitoringSummary'])->name('facilities.monitoring.summary');
Route::get('/api/facilities/monitoring/export-pdf', [App\Http\Controllers\FacilityReservationController::class, 'exportMonitoringPdf'])->name('facilities.monitoring.export_pdf');
Route::get('/api/facilities/stats', [App\Http\Controllers\FacilitiesController::class, 'stats'])->name('facilities.stats');

// Equipment details API
Route::get('/api/facilities/equipment-details', [App\Http\Controllers\FacilityReservationController::class, 'equipmentDetails'])->name('facilities.equipment.details');

// Redirect authenticated users to dashboard; unauthenticated to login
Route::get('/home', function () {
    if (Auth::check()) {
        return redirect()->route('dashboard');
    }
    return redirect()->route('login');
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

// Test route for legal case review (temporary - remove in production)
Route::get('/test-legal-case-review', function () {
    try {
        $case = \App\Models\LegalCase::with(['assignedTo', 'createdBy', 'documents'])->first();
        
        if (!$case) {
            return response()->json([
                'success' => false,
                'message' => 'No legal cases found in database'
            ]);
        }
        
        return response()->json([
            'success' => true,
            'case' => [
                'id' => $case->id,
                'case_title' => $case->case_title,
                'case_number' => $case->case_number,
                'status' => $case->status,
                'documents_count' => $case->documents->count(),
                'assigned_to' => $case->assignedTo ? $case->assignedTo->employee_name : 'Not assigned',
                'created_by' => $case->createdBy ? $case->createdBy->employee_name : 'Unknown'
            ],
            'message' => 'Legal case review test successful'
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

// OTP Authentication Routes
Route::get('/verify-otp', [App\Http\Controllers\userController::class, 'showOtpForm'])->name('otp.verify');
Route::post('/verify-otp', [App\Http\Controllers\userController::class, 'verifyOtp'])->name('otp.verify.submit');
Route::post('/resend-otp', [App\Http\Controllers\userController::class, 'resendOtp'])->name('otp.resend');

// Debug route for OTP verification
Route::post('/debug-verify-otp', function(Request $request) {
    \Log::info('Debug OTP verification', [
        'otp_code' => $request->otp_code,
        'employee_id' => session('otp_employee_id'),
        'session_data' => session()->all()
    ]);
    
    return response()->json([
        'success' => true,
        'message' => 'Debug info logged',
        'otp_code' => $request->otp_code,
        'employee_id' => session('otp_employee_id')
    ]);
});

Route::get('/refresh-csrf', function() {
    return response()->json(['csrf_token' => csrf_token()]);
});

// Debug route removed for security

// Temporary debug route for OTP testing (REMOVE IN PRODUCTION)
Route::get('/debug-otp-db', function() {
    if (app()->environment('local')) {
        $otps = \App\Models\OtpCode::orderBy('created_at', 'desc')->limit(5)->get();
        return response()->json([
            'otps' => $otps->map(function($otp) {
                return [
                    'employee_id' => $otp->employee_id,
                    'otp_code' => $otp->otp_code,
                    'expires_at' => $otp->expires_at,
                    'is_used' => $otp->is_used,
                    'created_at' => $otp->created_at
                ];
            })
        ]);
    }
    return response()->json(['error' => 'Not available in production']);
});

// Debug route for testing OTP verification
Route::post('/debug-verify-otp', function(\Illuminate\Http\Request $request) {
    if (app()->environment('local')) {
        $otpCode = $request->input('otp_code');
        $employeeId = session('otp_employee_id');
        
        \Log::info('Debug OTP verification', [
            'otp_code' => $otpCode,
            'employee_id' => $employeeId,
            'session_data' => session()->all()
        ]);
        
        if (!$employeeId) {
            return response()->json(['error' => 'No OTP session found']);
        }
        
        $otp = \App\Models\OtpCode::where('employee_id', $employeeId)
            ->where('otp_code', $otpCode)
            ->where('is_used', false)
            ->first();
            
        return response()->json([
            'otp_found' => $otp ? true : false,
            'otp_details' => $otp ? [
                'expires_at' => $otp->expires_at,
                'is_used' => $otp->is_used,
                'is_expired' => $otp->expires_at < now()
            ] : null,
            'verification_result' => \App\Models\OtpCode::verify($employeeId, $otpCode)
        ]);
    }
    return response()->json(['error' => 'Not available in production']);
});

// (Optional) OTP endpoints retained but with unique URIs if needed in future
Route::get('/login/otp', [App\Http\Controllers\AuthController::class, 'showOTP'])->name('login.otp');

// Guest routes
Route::post('/guest/create', [App\Http\Controllers\userController::class, 'create'])->name('guest.create');
Route::post('/guest/profile-setup/{guestID}', [App\Http\Controllers\userController::class, 'profilesetup'])->name('guest.profilesetup');
Route::post('/guest/logout', [App\Http\Controllers\userController::class, 'guestlogout'])->name('guest.logout');
Route::post('/guest/login', [App\Http\Controllers\userController::class, 'guestlogin'])->name('guest.login');

// Legal Documents - accessible to Legal Officers, Administrators, and Super Admins
Route::middleware(['auth', 'role:Legal Officer,Administrator,Super Admin'])->group(function () {
    Route::get('/legal/documents', [LegalController::class, 'legalDocuments'])->name('legal.legal_documents');
    // Legal Cases - accessible to all legal roles
    Route::get('/legal/cases', [LegalController::class, 'caseDeck'])->name('legal.legal_cases');
    // Internal legal document creation (draft/publish)
    Route::get('/legal/documents/create', [LegalController::class, 'createInternalDocument'])->name('legal.documents.create');
    Route::post('/legal/documents', [LegalController::class, 'storeInternalDocument'])->name('legal.documents.store');
    // Drafting workspace
    Route::get('/legal/documents/draft', [LegalController::class, 'draftingWorkspace'])->name('legal.documents.draft');
    Route::post('/legal/documents/draft', [LegalController::class, 'saveDraft'])->name('legal.documents.save_draft');
    Route::post('/legal/documents/submit-review', [LegalController::class, 'submitForReview'])->name('legal.documents.submit_review');
    Route::get('/legal/documents/export/{id}', [LegalController::class, 'exportDocument'])->name('legal.documents.export');
    // Review actions for internal/legal documents
    Route::post('/legal/documents/{id}/approve', [LegalController::class, 'approveDocument'])->name('legal.documents.approve');
    Route::post('/legal/documents/{id}/reject', [LegalController::class, 'rejectDocument'])->name('legal.documents.reject');
    Route::post('/legal/documents/{id}/request-revision', [LegalController::class, 'requestRevisionDocument'])->name('legal.documents.request_revision');
    // Archiving & retention
    Route::post('/legal/documents/{id}/archive', [LegalController::class, 'archiveDocument'])->name('legal.documents.archive');
    // Monitoring API
    Route::get('/legal/monitoring/summary', [LegalController::class, 'monitoringSummary'])->name('legal.monitoring.summary');
    Route::get('/legal/monitoring/list', [LegalController::class, 'monitoringList'])->name('legal.monitoring.list');
    // E-signature actions
    Route::post('/legal/documents/{id}/send-esign', [LegalController::class, 'sendForESign'])->name('legal.documents.send_esign');
    Route::post('/legal/esign/webhook', [LegalController::class, 'esignWebhook'])->name('legal.esign.webhook');
    // Department submission flow
    Route::get('/legal/documents/submit', [LegalController::class, 'submitForm'])->name('legal.documents.submit_form');
    Route::post('/legal/documents/submit', [LegalController::class, 'storeSubmission'])->name('legal.documents.store_submission');
    // Reports export
    Route::get('/legal/reports/export', [LegalController::class, 'exportReports'])->name('legal.reports.export');
    // Execution/Monitoring stubs
    Route::post('/legal/documents/{id}/mark-signed', [LegalController::class, 'markSigned'])->name('legal.documents.mark_signed');
    Route::post('/legal/documents/{id}/set-renewal', [LegalController::class, 'setRenewal'])->name('legal.documents.set_renewal');
    // Signature requests
    Route::post('/legal/documents/{id}/signatures/request', [LegalController::class, 'requestSignature'])->name('legal.documents.signatures.request');
    Route::post('/legal/documents/{id}/signatures/remind', [LegalController::class, 'remindSignature'])->name('legal.documents.signatures.remind');
    Route::post('/legal/documents/{id}/signatures/cancel', [LegalController::class, 'cancelSignature'])->name('legal.documents.signatures.cancel');
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
    // Dashboard chart data endpoints (scoped to dashboard only)
    Route::get('/dashboard/facility-stats', [DashboardController::class, 'facilityStats'])->name('dashboard.facility_stats');
    Route::get('/dashboard/user-mgmt-stats', [DashboardController::class, 'userMgmtStats'])->name('dashboard.user_mgmt_stats');
    Route::get('/dashboard/active-users', [DashboardController::class, 'activeUsersCount'])->name('dashboard.active_users');
    // Legal monitoring proxies for dashboard (accessible to any authenticated user)
    Route::get('/dashboard/legal/summary', [LegalController::class, 'monitoringSummary'])->name('dashboard.legal_summary');
    Route::get('/dashboard/legal/list', [LegalController::class, 'monitoringList'])->name('dashboard.legal_list');
    
    // Profile Management
    Route::get('/profile/edit', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::get('/profile', [App\Http\Controllers\ProfileController::class, 'show'])->name('profile.show');
    Route::post('/profile/send-verification', [ProfileController::class, 'send'])->name('verification.send');
    Route::put('/profile', [ProfileController::class, 'update'])->name('password.update');

    // Calendar legacy route: redirect old /reservations/calendar → new path to avoid conflict with reservations.show
    Route::get('/reservations/calendar/{facilityId?}', function ($facilityId = null) {
        $params = request()->only(['start_date','end_date']);
        $target = $facilityId
            ? route('facility_reservations.calendar', ['facilityId' => $facilityId])
            : route('facility_reservations.calendar');
        if (!empty($params)) {
            $target .= '?' . http_build_query($params);
        }
        return redirect()->to($target);
    })->name('reservations.calendar');

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
    
    // AI Document Builder - Must be before resource route to avoid conflicts
    Route::middleware(['auth', 'role:Administrator,Super Admin'])->group(function () {
        Route::get('/legal/ai-builder', [LegalController::class, 'aiDocumentBuilder'])->name('legal.ai_builder');
        Route::post('/legal/ai-generate-section', [LegalController::class, 'aiGenerateSection'])->name('legal.ai_generate_section');
        Route::post('/legal/ai-generate-document', [LegalController::class, 'aiGenerateDocument'])->name('legal.ai_generate_document');
        Route::post('/legal/save-ai-document', [LegalController::class, 'saveAiDocument'])->name('legal.save_ai_document');
        Route::post('/legal/submit-ai-document', [LegalController::class, 'submitAiDocument'])->name('legal.submit_ai_document');
    });
    
    Route::resource('legal', LegalController::class);
    
    // Legal Management Sub-modules - Administrator and Super Admin only
    Route::middleware(['auth', 'role:Administrator,Super Admin'])->group(function () {
        Route::get('/legal', [LegalController::class, 'caseDeck'])->name('legal.case_deck');
        
        // Legal Documents Management
        Route::get('/legal/documents', [LegalController::class, 'legalDocuments'])->name('legal.legal_documents');
        Route::get('/legal/documents/create', [LegalController::class, 'createInternalDocument'])->name('legal.documents.create');
        Route::post('/legal/documents', [LegalController::class, 'storeInternalDocument'])->name('legal.store_internal_document');
        Route::get('/legal/submit', [LegalController::class, 'submitForm'])->name('legal.submit_form');
        Route::post('/legal/submit', [LegalController::class, 'storeSubmission'])->name('legal.documents.store_submission');
        
        // Document Actions
        Route::post('/legal/documents/{id}/approve', [LegalController::class, 'approveDocument'])->name('legal.documents.approve');
        Route::post('/legal/documents/{id}/reject', [LegalController::class, 'rejectDocument'])->name('legal.documents.reject');
        Route::post('/legal/documents/{id}/revision', [LegalController::class, 'requestRevisionDocument'])->name('legal.documents.revision');
        Route::post('/legal/documents/{id}/archive', [LegalController::class, 'archiveDocument'])->name('legal.documents.archive');
        Route::post('/legal/documents/{id}/signed', [LegalController::class, 'markSigned'])->name('legal.documents.signed');
        Route::post('/legal/documents/{id}/renewal', [LegalController::class, 'setRenewal'])->name('legal.documents.renewal');
        Route::post('/legal/documents/{id}/signature', [LegalController::class, 'requestSignature'])->name('legal.documents.signature');
        Route::post('/legal/documents/{id}/remind', [LegalController::class, 'remindSignature'])->name('legal.documents.remind');
        Route::post('/legal/documents/{id}/cancel-signature', [LegalController::class, 'cancelSignature'])->name('legal.documents.cancel_signature');
        Route::get('/legal/documents/{id}/export', [LegalController::class, 'exportDocument'])->name('legal.documents.export');
        Route::post('/legal/documents/{id}/esign', [LegalController::class, 'sendForESign'])->name('legal.documents.esign');
        Route::post('/legal/documents/esign-webhook', [LegalController::class, 'esignWebhook'])->name('legal.documents.esign_webhook');
        
        // Legal Document Management Routes
        Route::get('/legal/documents/{id}', [DocumentController::class, 'showLegalDocument'])->name('legal.documents.show');
        Route::get('/legal/documents/{id}/edit', [DocumentController::class, 'editLegalDocument'])->name('legal.documents.edit');
        Route::put('/legal/documents/{id}', [DocumentController::class, 'updateLegalDocument'])->name('legal.documents.update');
        Route::delete('/legal/documents/{id}', [DocumentController::class, 'deleteLegalDocument'])->name('legal.documents.delete');
        Route::get('/legal/documents/{id}/download', [DocumentController::class, 'downloadLegalDocument'])->name('legal.documents.download');
        Route::post('/legal/documents/{id}/archive', [DocumentController::class, 'archive'])->name('legal.documents.archive_doc');
        Route::post('/legal/documents/{id}/approve-doc', [DocumentController::class, 'approveLegalDocument'])->name('legal.documents.approve_doc');
        Route::post('/legal/documents/{id}/decline-doc', [DocumentController::class, 'declineLegalDocument'])->name('legal.documents.decline_doc');
        
        // Document Drafting
        Route::get('/legal/documents/draft', [LegalController::class, 'draftingWorkspace'])->name('legal.documents.draft');
        Route::post('/legal/documents/draft/save', [LegalController::class, 'saveDraft'])->name('legal.documents.save_draft');
        Route::post('/legal/documents/draft/submit', [LegalController::class, 'submitForReview'])->name('legal.documents.submit_draft');
        
        // General Document Routes
        Route::post('/documents', [LegalController::class, 'store'])->name('document.store');
        Route::post('/documents/bulk-upload', [LegalController::class, 'bulkUpload'])->name('document.bulkUpload');
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
        Route::get('/document/view', [DocumentController::class, 'view'])->name('document.view');
        Route::resource('document', DocumentController::class)->where(['document' => '[0-9]+']);
        Route::resource('facilities', FacilitiesController::class);
        Route::get('/facilities/{id}/ajax', [FacilitiesController::class, 'showAjax'])->name('facilities.showAjax');
        Route::get('/facilities-calendar', [FacilitiesController::class, 'calendar'])->name('facilities.calendar');
        Route::post('/facilities-check-availability', [FacilitiesController::class, 'checkAvailability'])->name('facilities.checkAvailability');
        // Reservation Calendar (facility staff) - avoid conflict with resource('reservations')
        Route::get('/facility-reservations/calendar/{facilityId?}', [\App\Http\Controllers\FacilityReservationController::class, 'calendar'])->name('facility_reservations.calendar');
        // Realtime stats for dashboard polling
        Route::get('/reservations/realtime-stats', [\App\Http\Controllers\FacilityReservationController::class, 'realtimeStats'])->name('facility_reservations.realtime_stats');
        Route::get('/my-reservations', [App\Http\Controllers\FacilityReservationController::class, 'userHistory'])->name('facility_reservations.user_history');
        Route::get('/admin-analytics', [App\Http\Controllers\FacilityReservationController::class, 'adminAnalytics'])->name('facility_reservations.admin_analytics');
        
        // Debug route for analytics
        Route::get('/admin-analytics-debug', function() {
            try {
                $controller = new \App\Http\Controllers\FacilityReservationController(
                    app(\App\Services\GeminiService::class),
                    app(\App\Services\DocumentTextExtractorService::class),
                    app(\App\Services\FacilityCalendarService::class),
                    app(\App\Services\SecureDocumentRepository::class),
                    app(\App\Services\VisitorService::class),
                    app(\App\Services\ReservationWorkflowService::class)
                );
                
                $overview = $controller->getOverviewStats();
                return response()->json([
                    'success' => true,
                    'overview' => $overview,
                    'message' => 'Analytics data loaded successfully'
                ]);
            } catch (\Exception $e) {
                return response()->json([
                    'success' => false,
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ]);
            }
        })->name('facility_reservations.admin_analytics_debug');
        
        // Simple test route
        Route::get('/test-analytics', function() {
            return response()->json([
                'message' => 'Test route working',
                'reservations' => \App\Models\FacilityReservation::count(),
                'facilities' => \App\Models\Facility::count()
            ]);
        });

        // Simple analytics test route
        Route::get('/test-analytics-page', function() {
            $analytics = [
                'overview' => [
                    'total_reservations' => \App\Models\FacilityReservation::count(),
                    'approved_reservations' => \App\Models\FacilityReservation::where('status', 'approved')->count(),
                    'pending_reservations' => \App\Models\FacilityReservation::where('status', 'pending')->count(),
                    'denied_reservations' => \App\Models\FacilityReservation::where('status', 'denied')->count(),
                    'total_facilities' => \App\Models\Facility::count(),
                    'active_users' => \App\Models\FacilityReservation::distinct('reserved_by')->count('reserved_by'),
                    'this_month_reservations' => \App\Models\FacilityReservation::whereMonth('created_at', now()->month)->count(),
                    'approval_rate' => 100
                ],
                'facility_usage' => collect(),
                'reservation_trends' => collect([
                    ['month' => now()->subMonths(5)->format('Y-m'), 'count' => 0],
                    ['month' => now()->subMonths(4)->format('Y-m'), 'count' => 0],
                    ['month' => now()->subMonths(3)->format('Y-m'), 'count' => 0],
                    ['month' => now()->subMonths(2)->format('Y-m'), 'count' => 0],
                    ['month' => now()->subMonth()->format('Y-m'), 'count' => 0],
                    ['month' => now()->format('Y-m'), 'count' => \App\Models\FacilityReservation::whereMonth('created_at', now()->month)->count()]
                ]),
                'user_activity' => collect(),
                'conflict_analysis' => [
                    'potential_conflicts' => 0,
                    'resolved_conflicts' => 0,
                    'conflict_rate' => 0
                ],
                'revenue_analytics' => [
                    'total_revenue' => 0,
                    'monthly_revenue' => 0,
                    'average_booking_value' => 0
                ],
                'peak_hours' => collect([
                    ['hour' => 9, 'count' => 0],
                    ['hour' => 10, 'count' => 0],
                    ['hour' => 11, 'count' => 0],
                    ['hour' => 12, 'count' => 0],
                    ['hour' => 13, 'count' => 0],
                    ['hour' => 14, 'count' => 0],
                    ['hour' => 15, 'count' => 0],
                    ['hour' => 16, 'count' => 0],
                    ['hour' => 17, 'count' => 0]
                ]),
                'monthly_comparison' => [
                    'current_month' => \App\Models\FacilityReservation::whereMonth('created_at', now()->month)->count(),
                    'last_month' => \App\Models\FacilityReservation::whereMonth('created_at', now()->subMonth()->month)->count(),
                    'growth_rate' => 0
                ]
            ];

            $recentReservations = \App\Models\FacilityReservation::with(['facility:id,name', 'reserver:id,name'])
                ->orderBy('created_at', 'desc')
                ->limit(5)
                ->get();

            $pendingReservations = \App\Models\FacilityReservation::with(['facility:id,name', 'reserver:id,name'])
                ->where('status', 'pending')
                ->orderBy('created_at', 'asc')
                ->limit(10)
                ->get();

            return view('facility_reservations.admin_analytics', compact('analytics', 'recentReservations', 'pendingReservations'));
        });
        Route::post('/payments/process', [App\Http\Controllers\PaymentController::class, 'processPayment'])->name('payments.process');
        Route::get('/payments/details/{reservationId}', [App\Http\Controllers\PaymentController::class, 'getPaymentDetails'])->name('payments.details');
        Route::get('/payments/history', [App\Http\Controllers\PaymentController::class, 'paymentHistory'])->name('payments.history');
    });
    
    // Visitor Management - Receptionist, Administrator, Super Admin only
    Route::middleware(['auth', 'role:Receptionist,Administrator,Super Admin'])->group(function () {
        Route::resource('visitor', VisitorController::class);
        Route::get('/visitor/{id}/details', [App\Http\Controllers\VisitorController::class, 'getDetails'])->name('visitor.details');
        
        // Visitor Logs Routes
        Route::prefix('visitor-logs')->name('visitor.logs.')->group(function () {
            Route::get('/', [App\Http\Controllers\VisitorLogController::class, 'index'])->name('index');
            Route::get('/analytics', [App\Http\Controllers\VisitorLogController::class, 'getAnalytics'])->name('analytics');
            Route::get('/logs', [App\Http\Controllers\VisitorLogController::class, 'getLogs'])->name('logs');
            Route::post('/search', [App\Http\Controllers\VisitorLogController::class, 'search'])->name('search');
            Route::post('/generate-report', [App\Http\Controllers\VisitorLogController::class, 'generateReport'])->name('generate-report');
            Route::get('/export', [App\Http\Controllers\VisitorLogController::class, 'exportLogs'])->name('export');
        });

    });


    // Visitor AJAX Routes for Real-time Functionality - Moved outside middleware for now
    Route::prefix('visitor')->name('visitor.')->group(function () {
        // New route for managing visitors from facility reservations
        Route::get('/manage-reservation-visitors/{reservation}', [App\Http\Controllers\VisitorController::class, 'manageReservationVisitors'])->name('manage_reservation_visitors');
        Route::post('/perform-extraction/{reservation}', [App\Http\Controllers\VisitorController::class, 'performExtractionFromReservation'])->name('perform_extraction_from_reservation');
        Route::post('/perform-approval/{reservation}', [App\Http\Controllers\VisitorController::class, 'performApprovalFromReservation'])->name('perform_approval_from_reservation');

        Route::post('/search', [App\Http\Controllers\VisitorController::class, 'searchVisitors'])->name('search');
        Route::get('/details/{id}', [App\Http\Controllers\VisitorController::class, 'getVisitorDetails'])->name('details');
        Route::post('/checkin', [App\Http\Controllers\VisitorController::class, 'checkIn'])->name('checkin');
        Route::post('/checkin-existing/{id}', [App\Http\Controllers\VisitorController::class, 'checkInExisting'])->name('checkin_existing');
        Route::post('/checkout/{id}', [App\Http\Controllers\VisitorController::class, 'checkOut'])->name('checkout');
        Route::get('/current', [App\Http\Controllers\VisitorController::class, 'getCurrentVisitors'])->name('current');
        Route::get('/scheduled', [App\Http\Controllers\VisitorController::class, 'getScheduledVisits'])->name('scheduled');
        Route::get('/stats', [App\Http\Controllers\VisitorController::class, 'getVisitorStats'])->name('stats');
        
        // Monitoring Routes
        Route::get('/monitoring', [App\Http\Controllers\VisitorController::class, 'getCheckinMonitoring'])->name('monitoring');
        Route::get('/monitoring/stats', [App\Http\Controllers\VisitorController::class, 'getCheckinStats'])->name('monitoring.stats');
        Route::get('/monitoring/visitors', [App\Http\Controllers\VisitorController::class, 'getMonitoringVisitors'])->name('monitoring.visitors');
        
        // Visitor Pass Routes
        Route::get('/{id}/pass', [App\Http\Controllers\VisitorController::class, 'getVisitorPass'])->name('pass');
        Route::get('/{id}/pass/download', [App\Http\Controllers\VisitorController::class, 'downloadVisitorPass'])->name('pass.download');

        // Approve/Decline newly registered visitors
        Route::post('/{id}/approve', [App\Http\Controllers\VisitorController::class, 'approveVisitor'])->name('approve');
        Route::post('/{id}/decline', [App\Http\Controllers\VisitorController::class, 'declineVisitor'])->name('decline');
        
        // Debug route
        Route::get('/debug/visitors', function() {
            $visitors = \App\Models\Visitor::whereNotNull('pass_id')->take(5)->get();
            return response()->json([
                'total_visitors' => \App\Models\Visitor::count(),
                'visitors_with_passes' => \App\Models\Visitor::whereNotNull('pass_id')->count(),
                'sample_visitors' => $visitors->map(function($v) {
                    return [
                        'id' => $v->id,
                        'name' => $v->name,
                        'pass_id' => $v->pass_id,
                        'status' => $v->status
                    ];
                })
            ]);
        });
        
        // Quick Actions
        Route::get('/quick/view-all', [App\Http\Controllers\VisitorController::class, 'viewAllVisitors'])->name('quick.viewAll');
        Route::post('/quick/schedule', [App\Http\Controllers\VisitorController::class, 'scheduleVisit'])->name('quick.schedule');
        Route::post('/quick/emergency', [App\Http\Controllers\VisitorController::class, 'emergencyEvacuation'])->name('quick.emergency');
        Route::get('/quick/directory', [App\Http\Controllers\VisitorController::class, 'buildingDirectory'])->name('quick.directory');
    });
    
    // Document Management Routes
    Route::post('/document/{id}/request-release', [DocumentController::class, 'requestRelease'])->name('document.requestRelease');
    Route::get('/document/{id}/download', [DocumentController::class, 'download'])->name('document.download');
    Route::post('/document/{id}/analyze', [DocumentController::class, 'analyze'])->name('document.analyze');
Route::post('/document/{id}/analyze-ajax', [DocumentController::class, 'analyzeAjax'])->name('document.analyzeAjax');
    // Use a unique name for the upload analysis endpoint to avoid name collisions
    Route::post('/document/analyze-upload', [DocumentController::class, 'analyzeUpload'])->name('document.analyzeUpload');
    // DMS: simple monitoring & reports (document module only)
    Route::get('/document/monitoring/summary', function(\Illuminate\Http\Request $request){
        $q = \App\Models\Document::query()->whereNotIn('source',["legal_management","legal_submission","ai_builder"]);
        if ($dept = $request->get('department')) { $q->where('department',$dept); }
        if ($status = $request->get('status')) { $q->where('status',$status); }
        $now = now();
        return response()->json([
            'success' => true,
            'data' => [
                'total' => (clone $q)->count(),
                'active' => (clone $q)->where('status','active')->count(),
                'archived' => (clone $q)->where('status','archived')->count(),
                'expiring' => (clone $q)->whereNotNull('retention_until')->whereBetween('retention_until',[$now,$now->copy()->addDays(60)])->count(),
            ]
        ]);
    })->name('document.monitoring.summary');
    Route::get('/document/reports/basic', function(){
        $docs = \App\Models\Document::whereNotIn('source',["legal_management","legal_submission","ai_builder"]) ->get();
        $byDept = $docs->groupBy('department')->map->count();
        $byStatus = $docs->groupBy('status')->map->count();
        return response()->json(['success'=>true,'by_department'=>$byDept,'by_status'=>$byStatus]);
    })->name('document.reports.basic');

    // DMS reports page (view only)
    Route::get('/document/reports', [DocumentController::class, 'reports'])->name('document.reports');
    // Document receiving interface
    Route::get('/document/receive', [DocumentController::class, 'receive'])->name('document.receive');
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

    // Document Disposal Routes
    Route::post('/document/{id}/dispose', [DocumentController::class, 'dispose'])->name('document.dispose');
    
    
    // Document Access Control and Analytics Routes
    Route::get('/document/analytics', [App\Http\Controllers\DocumentAnalyticsController::class, 'index'])->name('document.analytics');
    Route::get('/document/{id}/access-analytics', [App\Http\Controllers\DocumentAnalyticsController::class, 'documentAccess'])->name('document.access_analytics');
    Route::get('/document/analytics/department-stats', [App\Http\Controllers\DocumentAnalyticsController::class, 'departmentStats'])->name('document.department_stats');
    Route::get('/document/analytics/confidentiality-stats', [App\Http\Controllers\DocumentAnalyticsController::class, 'confidentialityStats'])->name('document.confidentiality_stats');
    Route::get('/document/analytics/access-trends', [App\Http\Controllers\DocumentAnalyticsController::class, 'accessTrends'])->name('document.access_trends');
    
    // Document Access Control Routes
    Route::post('/document/{id}/track-access', [App\Http\Controllers\DocumentAccessController::class, 'trackAccess'])->name('document.track_access');
    Route::get('/document/{id}/download-secure', [App\Http\Controllers\DocumentAccessController::class, 'download'])->name('document.download_secure');
    Route::get('/document/{id}/access-analytics', [App\Http\Controllers\DocumentAccessController::class, 'getAccessAnalytics'])->name('document.get_access_analytics');

    // Legal Approval Routes
    Route::post('/legal/{id}/approve', [LegalController::class, 'approveRequest'])->name('legal.approve');
    Route::post('/legal/{id}/deny', [LegalController::class, 'denyRequest'])->name('legal.deny');
    Route::get('/legal/pending', [LegalController::class, 'pendingRequests'])->name('legal.pending');
    Route::get('/legal/approved', [LegalController::class, 'approvedRequests'])->name('legal.approved');
    Route::get('/legal/denied', [LegalController::class, 'deniedRequests'])->name('legal.denied');
    
    // Legal Case Approval Routes - Administrator and Super Admin only
    Route::middleware(['auth', 'role:Administrator,Super Admin'])->group(function () {
        Route::get('/legal/cases/{id}/review', [LegalController::class, 'reviewCase'])->name('legal.cases.review');
        Route::get('/legal/cases/{id}/compliance', [LegalController::class, 'complianceAssessment'])->name('legal.cases.compliance');
        Route::post('/legal/cases/{id}/approve', [LegalController::class, 'approveCase'])->name('legal.cases.approve');
        Route::post('/legal/cases/{id}/decline', [LegalController::class, 'declineCase'])->name('legal.cases.decline');
        Route::post('/legal/cases/{id}/escalate', [LegalController::class, 'escalateCase'])->name('legal.cases.escalate');
        Route::post('/legal/cases/{id}/hold', [LegalController::class, 'holdCase'])->name('legal.cases.hold');
        Route::post('/legal/cases/{id}/investigate', [LegalController::class, 'startInvestigation'])->name('legal.cases.investigate');
        Route::post('/legal/cases/{id}/evidence', [LegalController::class, 'addEvidence'])->name('legal.cases.evidence');
        Route::post('/legal/cases/{id}/notes', [LegalController::class, 'addNotes'])->name('legal.cases.notes');
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

    
    // Visitor Coordination Routes
    // Route::post('/facility_reservations/{id}/extract-visitors', [App\Http\Controllers\FacilityReservationController::class, 'extractVisitorData'])->name('facility_reservations.extract_visitors');
    // Route::post('/facility_reservations/{id}/approve-visitors', [App\Http\Controllers\FacilityReservationController::class, 'approveVisitors'])->name('facility_reservations.approve_visitors');

    // Visitor Export Routes
    Route::get('/visitor/export/excel', [App\Http\Controllers\VisitorController::class, 'exportExcel'])->name('visitor.export.excel');
    Route::get('/visitor/export/pdf', [App\Http\Controllers\VisitorController::class, 'exportPdf'])->name('visitor.export.pdf');
    Route::post('/visitor/export/report', [App\Http\Controllers\VisitorController::class, 'exportReport'])->name('visitor.export.report');



    // Notifications
    Route::get('/notifications', [App\Http\Controllers\NotificationController::class, 'index'])->name('notifications.index');
    Route::post('/notifications/{id}/mark-as-read', [App\Http\Controllers\NotificationController::class, 'markAsRead'])->name('notifications.markAsRead');
    Route::post('/notifications/mark-all-as-read', [App\Http\Controllers\NotificationController::class, 'markAllAsRead'])->name('notifications.markAllAsRead');

    // User Role Management - Administrator, Super Admin only
    Route::middleware(['auth', 'role:Administrator,Super Admin'])->group(function () {
        Route::get('/access/users/{user}/edit-role', [App\Http\Controllers\AccessController::class, 'editRole'])->name('access.users.editRole');
        Route::post('/access/users/{user}/update-role', [App\Http\Controllers\AccessController::class, 'updateRole'])->name('access.users.updateRole');
    });

    // Facility Reservation Approval Workflow
    Route::resource('facility_reservations', App\Http\Controllers\FacilityReservationController::class);
    Route::post('/facility_reservations/store-request', [App\Http\Controllers\FacilityReservationController::class, 'storeRequest'])->name('facility_reservations.store_request');
    Route::post('/facility_reservations/{id}/approve', [App\Http\Controllers\FacilityReservationController::class, 'approve'])->name('facility_reservations.approve');
    Route::post('/facility_reservations/{id}/deny', [App\Http\Controllers\FacilityReservationController::class, 'deny'])->name('facility_reservations.deny');
        Route::post('/facility_reservations/{id}/approve-request', [App\Http\Controllers\FacilityReservationController::class, 'approveRequest'])->name('facility_reservations.approve_request');
        Route::post('/facility_reservations/{id}/complete', [App\Http\Controllers\FacilityReservationController::class, 'completeRequest'])->name('facility_reservations.complete');
        Route::get('/facility_reservations/{id}/show-request', [App\Http\Controllers\FacilityReservationController::class, 'showRequest'])->name('facility_reservations.show_request');
        Route::post('/facilities/{id}/free', [App\Http\Controllers\FacilityReservationController::class, 'freeFacility'])->name('facilities.free');
    
    // Monthly Reports Routes
    Route::get('/facility_reservations/monthly-reports', [App\Http\Controllers\FacilityReservationController::class, 'monthlyReports'])->name('facility_reservations.monthly_reports');
    Route::get('/facility_reservations/generate-monthly-report', [App\Http\Controllers\FacilityReservationController::class, 'generateMonthlyReport'])->name('facility_reservations.generate_monthly_report');
    Route::post('/facility_reservations/monthly-report-summary', [App\Http\Controllers\FacilityReservationController::class, 'getMonthlyReportSummary'])->name('facility_reservations.monthly_report_summary');

    // Legal Review Routes
    Route::get('/facility_reservations/{id}/legal-review', [App\Http\Controllers\FacilityReservationController::class, 'legalReview'])->name('facility_reservations.legal_review');
    Route::post('/facility_reservations/{id}/legal-approve', [App\Http\Controllers\FacilityReservationController::class, 'legalApprove'])->name('facility_reservations.legal_approve');
    Route::post('/facility_reservations/{id}/legal-flag', [App\Http\Controllers\FacilityReservationController::class, 'legalFlag'])->name('facility_reservations.legal_flag');
    
    // Workflow Action Routes
    Route::post('/facility_reservations/{id}/availability-check', [App\Http\Controllers\FacilityReservationController::class, 'performAvailabilityCheck'])->name('facility_reservations.availability_check');
    Route::post('/facility_reservations/{id}/conflict-resolution', [App\Http\Controllers\FacilityReservationController::class, 'performConflictResolution'])->name('facility_reservations.conflict_resolution');
});

// Test route for OTP verification
Route::get('/test-otp-route', function() {
    return response()->json([
        'message' => 'OTP route is working',
        'session_employee_id' => session('otp_employee_id'),
        'timestamp' => now()
    ]);
});

require __DIR__.'/auth.php';