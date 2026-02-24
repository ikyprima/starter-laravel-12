<?php

namespace Modules\Admin\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use Inertia\Inertia;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use App\Services\SipdService;
use Modules\Admin\Models\SubSkpd;

class UserController extends Controller
{
    public function index(Request $request)
    {
        return Inertia::render('admin/users/Index');
    }

    public function getData(Request $request)
    {
        if ($request->has('search')) {
            $data = User::with('roles')
                ->where('name', 'like', "%{$request->search}%")
                ->orWhere('email', 'like', "%{$request->search}%")
                ->paginate(10);
            
             return response()->json([
                'status' => true,
                'message' => 'Sukses Ambil Data',
                'data' => $data
            ], 200);

        } else {
            $data = User::with('roles')->paginate(10);
            
            return response()->json([
                'status' => true,
                'message' => 'Sukses Ambil Data',
                'data' => $data
            ], 200);
        }
    }

    public function create()
    {
        return Inertia::render('admin/users/Form', [
            'isEdit' => false,
        ]);
    }

    public function store(Request $request)
    {
        try {
            DB::beginTransaction();

            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255',
                'email' => 'required|string|email|max:255|unique:users',
                'password' => 'required|string|min:8|confirmed',
                'roles' => 'array',
                'kode_sub_skpd' => 'nullable|string'
            ]);

            if ($request->header('X-Inertia')) {
                $validator->validate();
            } else {
                 if ($validator->fails()) {
                    return response()->json(['errors' => $validator->errors()], 422);
                }
            }

            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'kode_sub_skpd' => $request->kode_sub_skpd,
            ]);

            if ($request->has('roles')) {
                $roleNames = collect($request->roles)->pluck('label')->toArray();
                // If roles are passed as objects {value, label}, extract label (role name)
                 // Or if just strings, use as is. 
                 // Based on combobox, likely object {value: id, label: name}
                 // But previously it was just array of strings. 
                 // Let's handle both or just assume strings for now, will refine in Frontend.
                 // Actually, standard Combobox usually returns object or value.
                 // Let's assume we send array of Role Names or IDs.
                 // Spatie syncRoles accepts names or IDs.
                 
                // Simplest is to ensure frontend sends array of names.
                $user->syncRoles($request->roles);
            }

            DB::commit();

            return $request->header('X-Inertia')
                ? redirect()->route('admin.users.index')->with('success', 'User created successfully.')
                : response()->json(['success' => true, 'message' => 'User created successfully.'], 200);

        } catch (QueryException $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function edit(User $user)
    {
        $user->load('roles');
        // Transform roles to format expected by Combobox if needed, or do it in Vue
        
        return Inertia::render('admin/users/Form', [
            'user' => $user,
            'isEdit' => true,
        ]);
    }

    public function update(Request $request, User $user)
    {
        try {
            DB::beginTransaction();
             
             $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255',
                'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
                'password' => 'nullable|string|min:8|confirmed',
                'roles' => 'array',
                'kode_sub_skpd' => 'nullable|string'
            ]);

            if ($request->header('X-Inertia')) {
                $validator->validate();
            }

            $user->update([
                'name' => $request->name,
                'email' => $request->email,
                'kode_sub_skpd' => $request->kode_sub_skpd,
            ]);

            if ($request->filled('password')) {
                $user->update(['password' => Hash::make($request->password)]);
            }

            if ($request->has('roles')) {
                $user->syncRoles($request->roles);
            }

            DB::commit();
            
            return $request->header('X-Inertia')
                ? redirect()->route('admin.users.index')->with('success', 'User updated successfully.')
                : response()->json(['success' => true, 'message' => 'User updated successfully.'], 200);

        } catch (QueryException $e) {
             DB::rollBack();
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function destroy(User $user)
    {
        $user->delete();
        return redirect()->route('admin.users.index')->with('success', 'User deleted successfully.');
    }
    
    // Helper to get roles for Combobox
    public function getRoles(Request $request)
    {
        if ($request->has('q')) {
            $data = Role::where('name', 'like', "%{$request->q}%")->get();
        } else {
            $data = Role::all();
        }
        
        // Return format expected by Combobox: { value, label }
        $formatted = $data->map(function($role) {
            return [
                'value' => $role->name, // Use name for syncing
                'label' => $role->name
            ];
        });

        return response()->json($formatted);
    }
    public function getSubSkpds(Request $request)
    {
        $tahun = session('tahun', date('Y'));
        
        $data = SubSkpd::where('tahun', $tahun);
        
        if ($request->has('q')) {
            $query = $request->q;
            $data->where(function($q) use ($query) {
                $q->where('nama_sub_skpd', 'like', "%{$query}%")
                  ->orWhere('kode_sub_skpd', 'like', "%{$query}%");
            });
        }

        $results = $data->get();

        $formatted = $results->map(function($item) {
            return [
                'value' => $item->kode_sub_skpd,
                'label' => $item->nama_sub_skpd,
                'kode_skpd' => $item->kode_skpd
            ];
        });

        return response()->json($formatted);
    }
}
