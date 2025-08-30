<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon;

class AuthController extends Controller
{
    /**
     * Handle user login with employee_id
     */
    public function loginUser(Request $request)
    {
        $request->validate([
            'email' => 'required|string', // This is actually employee_id
            'password' => 'required|string',
        ]);

        $employeeId = $request->email;
        $password = $request->password;

        // Check if user exists in department_accounts table
        $user = DB::table('department_accounts')
            ->where('employee_id', $employeeId)
            ->first();

        if (!$user) {
            return back()->withErrors([
                'email' => 'Employee ID not found.',
            ])->withInput();
        }

        // Verify password
        // Accept either hashed passwords or plain-text (as currently stored in department_accounts)
        $validPassword = false;
        try {
            $validPassword = Hash::check($password, $user->password);
        } catch (\Throwable $e) {
            $validPassword = false;
        }
        if (!$validPassword && $password !== ($user->password ?? '')) {
            return back()->withErrors([
                'password' => 'Invalid password.',
            ])->withInput();
        }

        // Generate OTP
        $otp = $this->generateOTP();
        $otpExpiry = Carbon::now()->addMinutes(2);

        // Store OTP in session for verification
        Session::put('login_otp', [
            'otp' => $otp,
            'expiry' => $otpExpiry,
            'employee_id' => $employeeId,
            'user_data' => $user
        ]);

        // For demo purposes, show OTP in alert
        // In production, you would send this via SMS/Email
        Session::flash('otp_info', "Your OTP is: {$otp} (Valid for 2 minutes)");

        return redirect()->route('login.otp')->with('success', 'Please check your device for OTP');
    }

    /**
     * Show OTP verification page
     */
    public function showOTP()
    {
        if (!Session::has('login_otp')) {
            return redirect()->route('login')->with('error', 'Please login first');
        }

        return view('auth.loginotp');
    }

    /**
     * Verify OTP and complete login
     */
    public function verifyOTP(Request $request)
    {
        $request->validate([
            'otp1' => 'required|string|max:1',
            'otp2' => 'required|string|max:1',
            'otp3' => 'required|string|max:1',
            'otp4' => 'required|string|max:1',
            'otp5' => 'required|string|max:1',
            'otp6' => 'required|string|max:1',
        ]);

        // Combine OTP digits
        $enteredOTP = $request->otp1 . $request->otp2 . $request->otp3 . 
                      $request->otp4 . $request->otp5 . $request->otp6;

        // Get stored OTP data
        $otpData = Session::get('login_otp');

        if (!$otpData) {
            return redirect()->route('login')->with('error', 'OTP session expired. Please login again.');
        }

        // Check if OTP is expired
        if (Carbon::now()->isAfter($otpData['expiry'])) {
            Session::forget('login_otp');
            return redirect()->route('login')->with('error', 'OTP expired. Please login again.');
        }

        // Verify OTP
        if ($enteredOTP !== $otpData['otp']) {
            return back()->withErrors(['otp' => 'Invalid OTP. Please try again.'])->withInput();
        }

        // OTP is valid - create user session
        $user = $otpData['user_data'];

        // Create or update user in users table for Laravel authentication
        $laravelUser = $this->createOrUpdateLaravelUser($user);

        // Login the user
        Auth::login($laravelUser);

        // Persist employee_id in session for consistent identity mapping
        Session::put('emp_id', $user->employee_id);
        
        // Store user role in session for RBAC
        Session::put('user_role', $user->role);

        // Clear OTP session
        Session::forget('login_otp');

        // Redirect based on user role
        $redirectRoute = $this->getRedirectRouteByRole($user->role);
        return redirect()->intended($redirectRoute)->with('success', 'Welcome back, ' . $user->name . '!');
    }

    /**
     * Generate 6-digit OTP
     */
    private function generateOTP()
    {
        return str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
    }

    /**
     * Create or update user in Laravel users table
     */
    private function createOrUpdateLaravelUser($departmentUser)
    {
        $user = \App\Models\User::updateOrCreate(
            ['email' => $departmentUser->employee_id . '@soliera.local'],
            [
                'name' => $departmentUser->employee_name ?? ($departmentUser->name ?? 'Unknown User'),
                'email' => $departmentUser->employee_id . '@soliera.local',
                'password' => Hash::make(Str::random(16)), // Random password for Laravel auth
                'role' => $departmentUser->role ?? 'employee',
                'employee_id' => $departmentUser->employee_id ?? null,
                'department' => $departmentUser->dept_name ?? ($departmentUser->department ?? 'general'),
                'email_verified_at' => now(),
            ]
        );

        return $user;
    }

    /**
     * Get redirect route based on user role
     */
    private function getRedirectRouteByRole($role)
    {
        // Role-based redirection after login
        switch ($role) {
            case 'Legal Officer':
                return route('legal.case_deck');
            case 'Receptionist':
                return route('visitor.index');
            case 'Administrator':
            case 'Super Admin':
            default:
                return route('dashboard');
        }
    }

    /**
     * Logout user
     */
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('success', 'You have been logged out successfully.');
    }

    /**
     * Show login page
     */
    public function showLogin()
    {
        return view('auth.login');
    }
}
