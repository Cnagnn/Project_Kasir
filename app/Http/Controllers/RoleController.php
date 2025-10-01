<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\User;
use App\Models\Categories;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Redirect;

class RoleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
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
        $validated = $request->validate([
            'name' => 'required',
        ]);

        $role = Role::withTrashed()->where('name', $request->name)->first();

        if ($role && $role->trashed()) {
            // Jika produk ditemukan dan statusnya terhapus (trashed)
            $role->restore(); // Pulihkan datanya
            
            // Beri pesan bahwa data lama dipulihkan
            $message = 'Peran yang sebelumnya dihapus telah berhasil dipulihkan.';

        }
        else if ($role) {
            // Jika produk ditemukan tapi TIDAK terhapus (sudah aktif)
            // Ini berarti ada duplikasi data aktif, kembalikan error.
            return back()->with('failed', 'Peran dengan nama ini sudah ada.')->withInput();
        
        } 
        else {
            // Jika produk sama sekali tidak ditemukan, buat baris baru
            Role::create($validated);
            
            // Beri pesan bahwa data baru berhasil dibuat
            $message = 'Peran baru berhasil ditambahkan.';
        }

        // 4. Redirect kembali dengan pesan sukses
        return back()->with('success', $message);
    
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
}
