<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class AuthController extends Controller
{
    public function showLogin(Request $request)
    {
        if (Auth::check()) {
            return redirect()->intended(route('admin.photos.index'));
        }
        $tab = $request->query('tab', 'login');
        return view('auth.login', ['tab' => in_array($tab, ['login','register']) ? $tab : 'login']);
    }

    public function login(Request $request)
    {
        $data = $request->validate([
            'login' => ['required','string'],
            'password' => ['required','string'],
        ]);

        $remember = (bool) $request->boolean('remember');

        // Determine if login is email or username
        $field = filter_var($data['login'], FILTER_VALIDATE_EMAIL) ? 'email' : 'username';
        $credentials = [
            $field => $data['login'],
            'password' => $data['password'],
        ];

        if (Auth::attempt($credentials, $remember)) {
            $request->session()->regenerate();
            $user = Auth::user();
            $default = $user && $user->is_admin ? route('admin.photos.index') : route('home');
            if (!$user->is_admin) {
                // Prevent redirecting to protected admin pages for non-admins
                $request->session()->forget('url.intended');
            }
            return redirect()->intended($default)->with('status', 'Selamat datang, '.($user->name ?: ''));
        }

        return back()->withErrors([
            'login' => 'Kredensial tidak valid.',
        ])->onlyInput('login');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('home');
    }

    public function showRegister()
    {
        // Direct users to the combined auth page with register tab
        return redirect()->route('login', ['tab' => 'register']);
    }

    public function register(Request $request)
    {
        $data = $request->validate([
            'name' => ['required','string','max:120'],
            'username' => ['nullable','string','max:60','unique:users,username'],
            'email' => ['required','email','max:160','unique:users,email'],
            'password' => ['required','string','min:4','confirmed'],
        ]);

        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'username' => $data['username'] ?? null,
            'password' => Hash::make($data['password']),
        ]);

        Auth::login($user);
        $request->session()->regenerate();
        return redirect()->intended(route('admin.photos.index'));
    }

    public function showForgotPassword()
    {
        return view('auth.forgot');
    }

    public function sendForgotPassword(Request $request)
    {
        $request->validate(['email' => ['required','email']]);
        // In a real app, dispatch password reset email. Here we just show a generic success.
        return back()->with('status', 'Jika email terdaftar, tautan reset kata sandi telah dikirim.');
    }
}
