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

// Redirect root to login if not authenticated, else to dashboard
Route::get('/', function () {
    return redirect()->route('login');
});

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
    Route::post('/profile', [ProfileController::class, 'update'])->name('password.update');

    // Hotel Management
    Route::resource('reservations', ReservationController::class);
    Route::resource('guests', GuestController::class);
    Route::resource('orders', OrderController::class);
    Route::resource('inventory', InventoryController::class);
    
    // Finance and Reports
    Route::get('/finance/reports', function () {
        return view('finance.reports');
    })->name('finance.reports');
    
    
    
    // Access Protection
    Route::prefix('access')->group(function () {
        Route::get('/logs', [AccessController::class, 'logs'])->name('access.logs');
        Route::get('/users', [AccessController::class, 'users'])->name('access.users');
        Route::get('/roles', [AccessController::class, 'roles'])->name('access.roles');
        Route::get('/security', [AccessController::class, 'security'])->name('access.security');
    });
    
    
    
    // Settings
    Route::get('/settings', function () {
        return view('settings');
    })->name('settings');
    
    // Profile
    
    Route::resource('legal', LegalController::class);
    Route::resource('document', DocumentController::class);
    Route::resource('visitor', VisitorController::class);
    Route::resource('facilities', FacilitiesController::class);
    
    // Document Management Routes
    Route::post('/document/{id}/request-release', [DocumentController::class, 'requestRelease'])->name('document.request-release');
    Route::get('/document/{id}/download', [DocumentController::class, 'download'])->name('document.download');
    Route::post('/document/{id}/analyze', [DocumentController::class, 'analyze'])->name('document.analyze');
    // Use a unique name for the upload analysis endpoint to avoid name collisions
    Route::post('/document/analyze-upload', [DocumentController::class, 'analyzeUpload'])->name('document.analyze-upload');
    
    // Legal Approval Routes
    Route::post('/legal/{id}/approve', [LegalController::class, 'approveRequest'])->name('legal.approve');
    Route::post('/legal/{id}/deny', [LegalController::class, 'denyRequest'])->name('legal.deny');
    Route::get('/legal/pending', [LegalController::class, 'pendingRequests'])->name('legal.pending');
    Route::get('/legal/approved', [LegalController::class, 'approvedRequests'])->name('legal.approved');
    Route::get('/legal/denied', [LegalController::class, 'deniedRequests'])->name('legal.denied');
    
    // Legal Document Categories
    Route::get('/legal/category/{category}', [LegalController::class, 'categoryDocuments'])->name('legal.category');

    

    // Facility Reservation Approval Workflow
    Route::resource('facility_reservations', App\Http\Controllers\FacilityReservationController::class);
    Route::post('/facility_reservations/{id}/approve', [App\Http\Controllers\FacilityReservationController::class, 'approve'])->name('facility_reservations.approve');
    Route::post('/facility_reservations/{id}/deny', [App\Http\Controllers\FacilityReservationController::class, 'deny'])->name('facility_reservations.deny');
    
    // Legal Review Routes
    Route::get('/facility_reservations/{id}/legal-review', [App\Http\Controllers\FacilityReservationController::class, 'legalReview'])->name('facility_reservations.legal_review');
    Route::post('/facility_reservations/{id}/legal-approve', [App\Http\Controllers\FacilityReservationController::class, 'legalApprove'])->name('facility_reservations.legal_approve');
    Route::post('/facility_reservations/{id}/legal-flag', [App\Http\Controllers\FacilityReservationController::class, 'legalFlag'])->name('facility_reservations.legal_flag');
    
    // Visitor Coordination Routes
    Route::post('/facility_reservations/{id}/extract-visitors', [App\Http\Controllers\FacilityReservationController::class, 'extractVisitorData'])->name('facility_reservations.extract_visitors');
    Route::post('/facility_reservations/{id}/approve-visitors', [App\Http\Controllers\FacilityReservationController::class, 'approveVisitors'])->name('facility_reservations.approve_visitors');

    // Visitor Export Routes
    Route::get('/visitor/export/excel', [App\Http\Controllers\VisitorController::class, 'exportExcel'])->name('visitor.export.excel');
    Route::get('/visitor/export/pdf', [App\Http\Controllers\VisitorController::class, 'exportPdf'])->name('visitor.export.pdf');

    // Visitor AJAX Routes for Real-time Functionality
    Route::prefix('visitor')->name('visitor.')->group(function () {
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

    // Notifications
    Route::get('/notifications', [App\Http\Controllers\NotificationController::class, 'index'])->name('notifications.index');
    Route::post('/notifications/{id}/mark-as-read', [App\Http\Controllers\NotificationController::class, 'markAsRead'])->name('notifications.markAsRead');
    Route::post('/notifications/mark-all-as-read', [App\Http\Controllers\NotificationController::class, 'markAllAsRead'])->name('notifications.markAllAsRead');

    // User Role Management
    Route::get('/access/users/{user}/edit-role', [App\Http\Controllers\AccessController::class, 'editRole'])->name('access.users.editRole');
    Route::post('/access/users/{user}/update-role', [App\Http\Controllers\AccessController::class, 'updateRole'])->name('access.users.updateRole');
});

require __DIR__.'/auth.php';