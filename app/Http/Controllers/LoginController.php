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
        //
        // dd('halo');
        // 1. Validasi Input
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);
        // dd($credentials);
        //dd("halo");
        // Menambahkan 'remember' ke credentials jika user mencentangnya
        // $remember = $request->has('remember');

        // 2. Coba Lakukan Autentikasi
        if (Auth::attempt($credentials)) {
            // Jika berhasil
            $request->session()->regenerate(); // Regenerate session untuk keamanan
            // $user = Auth::user();
            // dd($user);
            // Redirect ke halaman yang seharusnya dituju setelah login, atau ke dashboard
            return redirect()->intended('/dashboard'); 
        }

        // 3. Jika Gagal
        // Kembali ke halaman login dengan pesan error
        return back()->withErrors([
            'email' => 'Email atau password yang Anda masukkan salah.',
        ])->onlyInput('email'); // Mengembalikan input email agar user tidak perlu mengetik ulang
    }
}
