<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws ValidationException
     */
    public function store(Request $request): RedirectResponse
    {

        // ADD THIS LINE TEMPORARILY:
        //dd($request->all());

        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'username' => ['required', 'string', 'max:255', 'unique:users'],
            'school_id' => ['required', 'string', 'max:255', 'unique:users'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $user = User::create([
            'name' => $request->name,
            'username' => $request->username,
            'school_id' => $request->school_id,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'employee', // Default role
            'is_approved' => false, // Ensure this is false!
        ]);

        // 1. Send Notification to SMO
        $smo = \App\Models\User::where('role', 'smo')->first();
        if ($smo) {
            \App\Models\Notification::create([
                'user_id' => $smo->id,
                'title' => 'New User Access Request',
                'message' => "{$user->name} ({$user->school_id}) has registered and is waiting for approval.",
                'icon' => 'user-plus',
                'type' => 'warning'
            ]);
        }

        event(new Registered($user));

        return redirect()->route('login')->with('status', 'Your account has been created! Please wait for the SMO In-Charge to approve your access.');
    }
}
