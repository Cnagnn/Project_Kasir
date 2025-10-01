<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        if (!Auth::check()) {
            return redirect('/login');
        }

        $user = Auth::user();

        // dd($user->Role->name);
        // dd(
        //     'Peran User dari DB:', $user->role->name,
        //     'Peran yang Dibutuhkan Rute:', $roles
        // );

        if($user->role && in_array($user->role->name, $roles)){
            return $next($request);
        }

        abort(403, 'Anda tidak memiliki hak akses ke halaman ini.');
        
    }
}
