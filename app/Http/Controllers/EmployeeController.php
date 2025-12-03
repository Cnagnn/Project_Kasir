<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Role;

class EmployeeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
        $employee = User::with('role')->get();
        $role = Role::all();
        // dd($employee);

        return view('employee',[
            'employee' => $employee,
            'role' => $role,
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
        //validate form
        $validated = $request->validate([
            'name' => 'required|string|max:255|min:3',
            'role_id' => 'required|exists:roles,id',
            'email' => 'required|email:rfc,dns|unique:users,email|max:255',
            'phone' => 'required|numeric|digits_between:10,15',
            'password' => 'required|string|min:6|max:255',
        ], [
            'name.required' => 'Nama pegawai wajib diisi.',
            'name.min' => 'Nama pegawai minimal 3 karakter.',
            'name.max' => 'Nama pegawai maksimal 255 karakter.',
            'role_id.required' => 'Peran wajib dipilih.',
            'role_id.exists' => 'Peran yang dipilih tidak valid.',
            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'email.unique' => 'Email sudah terdaftar.',
            'phone.required' => 'Nomor telepon wajib diisi.',
            'phone.numeric' => 'Nomor telepon harus berupa angka.',
            'phone.digits_between' => 'Nomor telepon harus antara 10-15 digit.',
            'password.required' => 'Password wajib diisi.',
            'password.min' => 'Password minimal 6 karakter.',
        ]);

        User::create([
            'name' => $request->name,
            'role_id' => $request->role_id,
            'email' => $request->email,
            'phone' => $request->phone,
            'password' => bcrypt($request->password),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return back()->with(['success' => 'Data Pegawai Berhasil Disimpan!']);
    
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
        $user = User::findOrFail($request->employee_id);
        
        $request->validate([
            'employee_name' => 'required|string|max:255|min:3',
            'role_id' => 'required|exists:roles,id',
        ], [
            'employee_name.required' => 'Nama pegawai wajib diisi.',
            'employee_name.min' => 'Nama pegawai minimal 3 karakter.',
            'employee_name.max' => 'Nama pegawai maksimal 255 karakter.',
            'role_id.required' => 'Peran wajib dipilih.',
            'role_id.exists' => 'Peran yang dipilih tidak valid.',
        ]);

        // Validasi khusus: Jika user adalah Manager terakhir, tidak boleh diganti role-nya
        if ($user->role->name === 'Manager') {
            $managerCount = User::whereHas('role', function($query) {
                $query->where('name', 'Manager');
            })->count();
            
            if ($managerCount <= 1 && $request->role_id != $user->role_id) {
                return back()->with('error', 'Tidak dapat mengubah role Manager terakhir! Sistem harus memiliki minimal 1 Manager.');
            }
        }

        $user->update([
            'name' => $request->employee_name,
            'role_id' => $request->role_id,
        ]);

        return back()->with('success', 'Data Pegawai Berhasil Diperbarui');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $user = User::findOrFail($id);
            $userName = $user->name;
            
            // Cek apakah user adalah Manager
            if ($user->role->name === 'Manager') {
                // Hitung jumlah Manager yang ada
                $managerCount = User::whereHas('role', function($query) {
                    $query->where('name', 'Manager');
                })->count();
                
                // Jika hanya ada 1 Manager, tidak boleh dihapus
                if ($managerCount <= 1) {
                    return back()->with('error', 'Tidak dapat menghapus Manager terakhir! Sistem harus memiliki minimal 1 Manager.');
                }
            }
            
            $user->delete();
            
            return back()->with('success', "Data pegawai '{$userName}' berhasil dihapus!");
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal menghapus data pegawai: ' . $e->getMessage());
        }
    }

    public function search(Request $request)
    {
        // Ambil keyword pencarian dari query string (?query=...)
        $query = $request->input('query');

        // Lakukan pencarian di database
        $employees = User::where('name', 'LIKE', "%{$query}%")
            ->with('role') // Eager load category untuk efisiensi
            ->take(10) // Batasi hasil agar tidak terlalu banyak
            ->get();

            // dd($products);
        // Kembalikan hasil dalam format JSON
        return response()->json($employees);
    }

    public function detail(string $id)
    {
        //
        $user = User::where('id', $id)->first();
        $role = Role::all();
        // dd($user);
        return view('employee_detail', [
            'user' => $user,
            'role' => $role
        ]);
    }
}
