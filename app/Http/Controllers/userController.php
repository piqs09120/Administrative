<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\DeptAccount;
use App\Models\Guest;
use Illuminate\Support\Facades\Hash;
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

        // Log the successful login
        \App\Http\Controllers\AccessController::logAction(
            $deptAccount->Dept_no,
            'Login',
            'User logged in successfully',
            $request->ip()
        );

        return redirect()->route('dashboard');
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

} 