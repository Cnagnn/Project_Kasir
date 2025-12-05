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
        $roles = Role::all();
        return view('role', [
            'roles' => $roles
        ]);
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
            'name' => 'required|string|max:255',
        ], [
            'name.required' => 'Nama role harus diisi.',
            'name.max' => 'Nama role maksimal 255 karakter.',
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
    public function update(Request $request)
    {
        //
        // dd($id);
        $role = Role::where('id', $request->role_id)->first();
        // dd($role);
        // dd($request);
        $request->validate([
            'role_id' => 'required',
            // 'email' => 'required',
            // 'phone' => 'required',
            'role_name' => 'required',
        ]);

        $role->update([
            'name' => $request->role_name,
        ]);

        return back()->with('success', 'Data Peran Berhasil Diperbarui');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
        // dd($id);
        $role = Role::where('id', $id)->first();
        // dd($role);

        // Eager loading untuk menghindari N+1 query
        $employee = User::with(['role'])->where('role_id', $id)->get();
        // dd($employee);

        if($employee->isNotEmpty()){
            return redirect()->back()->with('failed', 'Masih Terdapat User Di Role Ini !');
        }
        else{
            $role->delete();
            return redirect()->back()->with('success', 'Data Role Berhasil Dihapus.');
        }
    }

    public function search(Request $request)
    {
        // Ambil keyword pencarian dari query string (?query=...)
        $query = $request->input('query');

        // Lakukan pencarian di database
        $roles = Role::where('name', 'LIKE', "%{$query}%")
            ->take(10) // Batasi hasil agar tidak terlalu banyak
            ->get();

            // dd($products);
        // Kembalikan hasil dalam format JSON
        return response()->json($roles);
    }
}
