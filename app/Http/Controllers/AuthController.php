<?php

namespace App\Http\Controllers;

use App\Models\Users;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Inertia\Inertia;

class AuthController extends Controller
{
    public function get_login(Request $request) {
        if (Auth::check()) {
            return redirect()->intended('/');
        }
        
        return Inertia::render('User/Login')->with('showHeader', false);
    }

    public function get_register(Request $request) {
        if (Auth::check()) {
            return redirect()->intended('/');
        }
        
        return Inertia::render('User/Register')->with('showHeader', false);
    }

    public function login(Request $request) {
        // Validate the input
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();
            return redirect()->intended('/')->with('success', 'Welcome, ' . Auth::user()->first_name . ' ' . Auth::user()->last_name . '!');
        }

        return redirect()
            ->back()
            ->with('error', 'Your email / password is incorrect.');
    }

    public function logout(Request $request) {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/')->with('success', 'You have been logged out.');
    }

    public function register(Request $request) {
        // Validate the input
        $validator = $request->validate([
            'username' => ['required', 'string', 'max:16'],
            'gender' => ['required', 'in:Male,Female'],
            'email' => ['required', 'email', 'unique:users,email'],
            'password' => ['required', 'min:8'],
        ]);

        // Create a new user
        $user = Users::create([
            'username' => $request->username,
            'gender' => $request->gender,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        return redirect('/login')->with('success', 'Account created successfully. Please try login.');
    }
}
