<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\DeptAccount;
use App\Models\Guest;
use App\Models\OtpCode;
use App\Notifications\OtpCodeNotification;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;

class userController extends Controller
{
   public function login(Request $request)
{
    $form = $request->validate([
        'employee_id' => 'required',
        'password' => 'required',
    ]);

    // Always clear any existing session before logging in new user
    Auth::logout();
    $request->session()->invalidate();
    $request->session()->regenerateToken();

    // Find the department account by employee ID
    $deptAccount = DeptAccount::where('employee_id', $form['employee_id'])->first();

    // Validate password: accept either hashed or plain text (temporary)
    $validPassword = false;
    if ($deptAccount) {
        try {
            $validPassword = Hash::check($form['password'], $deptAccount->password);
        } catch (\Throwable $e) {
            $validPassword = false;
        }
        if (!$validPassword) {
            $validPassword = $deptAccount->password === $form['password'];
        }
    }

    if ($deptAccount && $validPassword) {
        // Generate and send OTP
        $otp = OtpCode::createForEmployee(
            $deptAccount->employee_id, 
            $deptAccount->email, 
            $request->ip()
        );

        // Send OTP email
        try {
            $deptAccount->notify(new OtpCodeNotification($otp->otp_code, $deptAccount->employee_name));
        } catch (\Exception $e) {
            \Log::error('Failed to send OTP email: ' . $e->getMessage());
            return back()->withErrors([
                'employee_id' => 'Failed to send OTP. Please try again.',
            ])->onlyInput('employee_id');
        }

        // Store employee data in session for OTP verification
        session(['otp_employee_id' => $deptAccount->employee_id]);
        session(['otp_user_data' => $deptAccount->toArray()]);

        return redirect('/verify-otp')->with('success', 'OTP sent to your email address.');
    }

    // Log failed login attempt
    if ($deptAccount) {
        \App\Http\Controllers\AccessController::logAction(
            $deptAccount->Dept_no,
            'Login_failed',
            'Invalid password provided',
            $request->ip()
        );
    }

    return back()->withErrors([
        'employee_id' => 'Invalid Employee ID or password.',
    ])->onlyInput('employee_id');
}

public function logout(Request $request)
{
    // Log the logout action before clearing the session
    if (Auth::check()) {
        $deptNo = null;
        $empId = Session::get('emp_id');
        if ($empId) {
            $deptNo = DeptAccount::where('employee_id', $empId)->value('Dept_no');
        }
        \App\Http\Controllers\AccessController::logAction(
            $deptNo ?? (string) Auth::id(),
            'Logout',
            'User logged out successfully',
            $request->ip()
        );
    }

    Auth::logout();
    $request->session()->invalidate();
    $request->session()->regenerateToken();

    return redirect()->route('login');
}



// for guest
public function create(Request $request)
{
    $form = $request->validate([
        'guest_name'     => 'required|string|max:255',
        'guest_email'    => 'required|email|unique:core1_guest,guest_email',
        'guest_address'  => 'required|string|max:255',
        'guest_mobile'   => 'required|string|max:20',
        'guest_password' => 'required|string|confirmed',
        'guest_birthday' => 'required|date',
    ]);

    // Hash password before saving
    $form['guest_password'] = Hash::make($form['guest_password']);

    $guestAccount = Guest::create($form);

    // Auto login the new guest
    Auth::guard('guest')->login($guestAccount);

    // Store session data
    session(['guestSession' => $guestAccount]);

    return redirect('/photoupload');
}

public function profilesetup(Request $request, Guest $guestID){
    $form = $request->validate([
        'guest_photo' => 'required',
    ]);

    $filename = time() . '_' . $request->file('guest_photo')->getClientOriginalName();  
    $filepath = 'images/profiles/' .$filename;  
    $request->file('guest_photo')->move(public_path('images/profiles/'), $filename);
    $form['guest_photo'] = $filepath;

    $guestID->update($form);

    return redirect('/guestdashboard');
}

public function guestlogout(){
      Auth::guard('guest')->logout();

      return redirect('/loginguest');
}

/**
 * Show OTP verification form
 */
public function showOtpForm()
{
    if (!session('otp_employee_id')) {
        \Log::info('No OTP session found, redirecting to login');
        return redirect()->route('login')->with('error', 'No active OTP session. Please login first.');
    }
    
    \Log::info('Showing OTP form for employee: ' . session('otp_employee_id'));
    
    return view('auth.verify-otp');
}

/**
 * Verify OTP code
 */
public function verifyOtp(Request $request)
{
    \Log::info('=== OTP VERIFICATION START ===');
    \Log::info('OTP Verification attempt for employee: ' . session('otp_employee_id'));
    \Log::info('Request data: ' . json_encode($request->all()));
    \Log::info('Request method: ' . $request->method());
    \Log::info('Request URL: ' . $request->fullUrl());

    // Check if otp_code is present in request
    if (!$request->has('otp_code')) {
        \Log::error('OTP code not found in request data');
        return back()->withErrors([
            'otp_code' => 'OTP code is required.',
        ])->onlyInput('otp_code');
    }

    $request->validate([
        'otp_code' => 'required|string|size:6',
        'employee_id' => 'required'
    ]);
    
    \Log::info('Validation passed. OTP Code: ' . $request->otp_code);

    $employeeId = session('otp_employee_id');
    $otpCode = $request->otp_code;

    // Check if session data exists
    if (!$employeeId) {
        \Log::error('No OTP session found during verification');
        return redirect()->route('login')->withErrors([
            'otp_code' => 'Session expired. Please login again.'
        ]);
    }

    // Check if user data exists in session
    $userData = session('otp_user_data');
    if (!$userData) {
        \Log::error('No user data found in OTP session');
        return redirect()->route('login')->withErrors([
            'otp_code' => 'Session expired. Please login again.'
        ]);
    }

    \Log::info('Verifying OTP: ' . $otpCode . ' for employee: ' . $employeeId);

    // Check if OTP exists in database
    $existingOtp = OtpCode::where('employee_id', $employeeId)
        ->where('otp_code', $otpCode)
        ->where('is_used', false)
        ->first();
    
    \Log::info('Existing OTP found: ' . ($existingOtp ? 'Yes' : 'No'));
    if ($existingOtp) {
        \Log::info('OTP details: expires_at=' . $existingOtp->expires_at . ', is_used=' . ($existingOtp->is_used ? 'true' : 'false'));
    }

    // Verify OTP
    $verificationResult = OtpCode::verify($employeeId, $otpCode);
    \Log::info('OTP verification result: ' . ($verificationResult ? 'SUCCESS' : 'FAILED'));
    
    if ($verificationResult) {
        \Log::info('OTP verification successful for employee: ' . $employeeId);
        // Get user data from session
        $userData = session('otp_user_data');
        $deptAccount = DeptAccount::find($userData['Dept_no']);

        if ($deptAccount) {
            // Map department account → Laravel users table for the default guard
            $updateData = [
                'name' => $deptAccount->employee_name ?? 'User',
                'password' => Hash::make(Str::random(16)),
                'email_verified_at' => now(),
            ];

            // Only include columns that actually exist on users table
            try {
                if (\Illuminate\Support\Facades\Schema::hasColumn('users', 'role')) {
                    $updateData['role'] = $deptAccount->role ?? 'employee';
                }
                if (\Illuminate\Support\Facades\Schema::hasColumn('users', 'employee_id')) {
                    $updateData['employee_id'] = $deptAccount->employee_id;
                }
                if (\Illuminate\Support\Facades\Schema::hasColumn('users', 'department')) {
                    $updateData['department'] = $deptAccount->dept_name ?? 'general';
                }
            } catch (\Throwable $e) { /* proceed with minimal fields */ }

            $laravelUser = \App\Models\User::updateOrCreate(
                ['email' => $deptAccount->employee_id . '@soliera.local'],
                $updateData
            );

            // Login using the standard web guard so middleware('auth') works
            Auth::login($laravelUser);
            $request->session()->regenerate();

            // Persist employee_id for UI display (navbar pulls from this)
            Session::put('emp_id', $deptAccount->employee_id);
            
            // Store user role in session for RBAC system
            Session::put('user_role', $deptAccount->role);

            // Log the successful login
            \App\Http\Controllers\AccessController::logAction(
                $deptAccount->Dept_no,
                'Login',
                'User logged in successfully with OTP',
                $request->ip()
            );

            // Clear OTP session data
            session()->forget(['otp_employee_id', 'otp_user_data']);

            // Redirect based on user role
            $redirectRoute = $this->getRedirectRouteByRole($deptAccount->role);
            \Log::info('Redirecting to: ' . $redirectRoute . ' for role: ' . $deptAccount->role);
            \Log::info('User authenticated: ' . (Auth::check() ? 'YES' : 'NO'));
            \Log::info('User ID: ' . (Auth::id() ?? 'NULL'));
            \Log::info('Session user_role: ' . Session::get('user_role', 'NOT_SET'));
            
            return redirect($redirectRoute)->with('success', 'Login successful!');
        } else {
            \Log::error('DeptAccount not found for user data: ' . json_encode($userData));
            return back()->withErrors([
                'otp_code' => 'User account not found. Please login again.',
            ])->onlyInput('otp_code');
        }
    } else {
        \Log::error('OTP verification failed for employee: ' . $employeeId . ' with code: ' . $otpCode);
        return back()->withErrors([
            'otp_code' => 'Invalid or expired OTP code.',
        ])->onlyInput('otp_code');
    }
}

/**
 * Resend OTP code
 */
public function resendOtp(Request $request)
{
    \Log::info('Resend OTP requested for employee: ' . session('otp_employee_id'));
    
    $employeeId = session('otp_employee_id');
    
    if (!$employeeId) {
        \Log::error('No OTP session found during resend');
        return response()->json([
            'success' => false,
            'message' => 'No active OTP session found.'
        ], 400);
    }

    $deptAccount = DeptAccount::where('employee_id', $employeeId)->first();
    
    if (!$deptAccount) {
        \Log::error('DeptAccount not found for employee: ' . $employeeId);
        return response()->json([
            'success' => false,
            'message' => 'User not found.'
        ], 404);
    }

    // Generate new OTP
    $otp = OtpCode::createForEmployee(
        $deptAccount->employee_id, 
        $deptAccount->email, 
        $request->ip()
    );

    \Log::info('New OTP generated: ' . $otp->otp_code . ' for employee: ' . $employeeId);

    // Send OTP email
    try {
        $deptAccount->notify(new OtpCodeNotification($otp->otp_code, $deptAccount->employee_name));
        
        \Log::info('OTP email sent successfully to: ' . $deptAccount->email);
        
        return response()->json([
            'success' => true,
            'message' => 'New OTP sent to your email.'
        ]);
    } catch (\Exception $e) {
        \Log::error('Failed to resend OTP email: ' . $e->getMessage());
        
        return response()->json([
            'success' => false,
            'message' => 'Failed to send OTP. Please try again.'
        ], 500);
    }
}

public function guestlogin(Request $request){
    $form = $request->validate([
        'guest_email' => 'required',
        'guest_password' => 'required',
    ]);

    if(Auth::guard('guest')->attempt(['guest_email' => $form['guest_email'], 'password' => $form['guest_password']])){
       $request->session()->regenerate();

       return redirect('/guestdashboard');
    }
}

/**
 * Get redirect route based on user role
 */
private function getRedirectRouteByRole($role)
{
    // Role-based redirection after login (case-insensitive)
    $roleLower = strtolower($role);
    
    if (strpos($roleLower, 'legal') !== false && strpos($roleLower, 'officer') !== false) {
        return route('legal.case_deck');
    } elseif (strpos($roleLower, 'receptionist') !== false) {
        return route('visitor.index');
    } elseif (strpos($roleLower, 'administrator') !== false || strpos($roleLower, 'admin') !== false) {
        return route('dashboard');
    } elseif (strpos($roleLower, 'super') !== false && strpos($roleLower, 'admin') !== false) {
        return route('dashboard');
    } else {
        // Default fallback
        return route('dashboard');
    }
}

} 