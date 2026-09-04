<?php

namespace App\Http\Controllers;

use App\Mail\MailerController;
use App\Models\Users;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\Rule;
use Inertia\Inertia;

class AuthController extends Controller
{
    public function get_login(Request $request)
    {
        if (Auth::check()) {
            return redirect()->intended('/');
        }

        return Inertia::render('User/Login')->with('showHeader', false);
    }

    public function get_register(Request $request)
    {
        if (Auth::check()) {
            return redirect()->intended('/');
        }

        return Inertia::render('User/Register')->with('showHeader', false);
    }

    public function login_auth(Request $request)
    {
        // Validate the input: will throw error if fails so no need return
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        // Find the user first and only get the user's email, auth, and is_suspended status
        $user = Users::where('email', $credentials['email'])
            ->select('user_id', 'username', 'email', 'enabled_auth', 'is_suspended')
            ->first();

        // Return error if does not exist
        if (!$user) {
            return redirect()
                ->back()
                ->with('error', 'This email credentials does not exist.');
        }

        // Return error if already suspended by admins
        if ($user->is_suspended) {
            return redirect()
                ->back()
                ->with('error', 'Your account has been suspended.');
        }

        // If user enabled authentication, generate an OTP and send to email.
        if ($user->enabled_auth) {
            $code = $this->generate_otp($user->user_id);
            $mail_data = [
                'username' => $user->username,
                'code' => $code,
                'type' => 'Login_Auth',
            ];
            Mail::to($user->email)->send(new MailerController($mail_data));
            return Inertia::render('User/TwoFactorAuth')->with('showHeader', false);
        } else {
            // Otherwise, simply send an email for login notification
            $mail_data = [
                'username' => $user->username,
                'type' => 'Login',
            ];
            Mail::to($user->email)->send(new MailerController($mail_data));
            return $this->login_process($credentials, $request);
        }
    }

    public function login_process($credentials, $request)
    {
        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();
            return redirect()->intended('/')->with('success', 'Welcome, ' . Auth::user()->username . '!');
        }

        return redirect()
            ->back()
            ->with('error', 'Your email / password is incorrect.');
    }

    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/')->with('success', 'You have been logged out.');
    }

    public function register(Request $request)
    {
        // Define rules and message
        $rules = [
            'username' => ['required', 'string'],
            'gender' => ['required', 'in:Male,Female'],
            'email' => ['required', 'email', Rule::unique('users', 'email')->whereNull('deleted_at'),],
            'password' => ['required', 'min:8'],
            'c_password' => ['required', 'same:password'],
        ];

        $messages = [
            'username.required' => 'Username is a required field.',
            'gender.required' => 'Gender is a required field.',
            'email.required' => 'Email is a required field.',
            'email.email' => 'Email is invalid.',
            'email.unique' => 'Email already exists.',
            'password.required' => 'Password is a required field.',
            'password.min' => 'Password must be at least 8 characters.',
            'c_password.required' => 'Confirm password is a required field.',
            'c_password.same' => 'Password does not match with confirm password.',
        ];

        // Validate the input
        $validator = $request->validate($rules, $messages);

        // Create a new user
        $user = Users::create([
            'username' => $validator['username'],
            'gender' => $validator['gender'],
            'email' => $validator['email'],
            'password' => Hash::make($validator['password']),
        ]);

        // Send an email : Registration notification
        $mail_data = [
            'type' => 'Registration',
        ];
        Mail::to($user->email)->send(new MailerController($mail_data));

        return redirect('/user/login')->with('success', 'Account created successfully!');
    }

    public function authenticate(Request $request)
    {
        // Validate the input
        $credentials = $request->validate([
            'otp' => ['required', 'size:6'],
        ]);

        $cached_otp = Cache::get(Auth::user()->user_id); // get the cached otp from previous generation

        if ($credentials['otp'] !== $cached_otp) {
            return redirect()
                ->back()
                ->with('error', 'Your OTP code is incorrect. Please try again.');
        }

        return true;
    }

    private function generate_otp($uid)
    {
        $code = rand(100000, 1000000);

        // Stores in a temporary cache
        Cache::put($uid, $code, now()->addMinutes(5));

        return $code;
    }
}
