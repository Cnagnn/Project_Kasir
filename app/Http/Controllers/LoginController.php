<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class LoginController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function showLoginForm()
    {
        //
        return view('login');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

    public function loginProcess(Request $request)
    {
        // 1. Validasi Input dengan pesan kustom
        $request->validate([
            'email' => [
                'required',
                'email',
                'max:255'
            ],
            'password' => [
                'required',
                'string',
                'min:6'
            ],
        ], [
            'email.required' => 'Email harus diisi.',
            'email.email' => 'Format email tidak valid.',
            'email.max' => 'Email terlalu panjang.',
            'password.required' => 'Password harus diisi.',
            'password.min' => 'Password minimal 6 karakter.',
        ]);

        // 2. Cek apakah email terdaftar dengan eager loading
        $user = User::with(['role'])->where('email', $request->email)->first();
        
        if (!$user) {
            return back()->withErrors([
                'email' => 'Email tidak terdaftar dalam sistem.',
            ])->onlyInput('email');
        }

        // 3. Coba Lakukan Autentikasi
        $credentials = $request->only('email', 'password');
        $remember = $request->has('remember');

        if (Auth::attempt($credentials, $remember)) {
            // Jika berhasil
            $request->session()->regenerate();
            
            // Log aktivitas login (opsional)
            // Log::info('User login successful', ['user_id' => Auth::id(), 'email' => $request->email]);
            
            return redirect()->intended('/dashboard')->with('success', 'Selamat datang kembali!'); 
        }

        // 4. Jika Gagal (password salah)
        return back()->withErrors([
            'password' => 'Password yang Anda masukkan salah.',
        ])->onlyInput('email');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        
        return redirect('/')->with('success', 'Anda telah berhasil logout.');
    }
}
