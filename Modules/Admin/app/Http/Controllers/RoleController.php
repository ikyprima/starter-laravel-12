<?php

namespace Modules\Admin\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Role;
use App\Models\Permission;
use Inertia\Inertia;

class RoleController extends Controller
{
    public function index(Request $request)
    {
        return Inertia::render('admin/roles/Index');
    }

    public function getData(Request $request)
    {
        $query = Role::query()->with('permissions');

        if ($request->has('search')) {
            $query->where('name', 'like', "%{$request->search}%");
        }

        $data = $query->paginate(10);

        return response()->json([
            'status' => true,
            'message' => 'Sukses Ambil Data',
            'data' => $data
        ], 200);
    }

    public function create()
    {
        $permissions = Permission::all()->groupBy(function($item) {
             return explode(' ', $item->name)[1] ?? 'other';
        });

        return Inertia::render('admin/roles/Form', [
            'permissions' => $permissions,
            'role' => new Role(),
            'isEdit' => false,
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:roles',
            'permissions' => 'array'
        ]);

        $role = Role::create(['name' => $request->name]);

        if ($request->has('permissions')) {
            $role->syncPermissions($request->permissions);
        }

        return $request->header('X-Inertia')
            ? redirect()->route('admin.roles.index')->with('success', 'Role created successfully.')
            : response()->json(['success' => true, 'message' => 'Role created successfully.'], 200);
    }

    public function edit(Role $role)
    {
        $permissions = Permission::all()->groupBy(function($item) {
             return explode(' ', $item->name)[1] ?? 'other';
        });
        
        $role->load('permissions');

        return Inertia::render('admin/roles/Form', [
            'permissions' => $permissions,
            'role' => $role,
            'isEdit' => true,
        ]);
    }

    public function update(Request $request, Role $role)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:roles,name,' . $role->id,
            'permissions' => 'array'
        ]);

        $role->update(['name' => $request->name]);

        if ($request->has('permissions')) {
            $role->syncPermissions($request->permissions);
        }

        return $request->header('X-Inertia')
            ? redirect()->route('admin.roles.index')->with('success', 'Role updated successfully.')
            : response()->json(['success' => true, 'message' => 'Role updated successfully.'], 200);
    }

    public function destroy(Role $role)
    {
        $role->delete();
        return redirect()->route('admin.roles.index')->with('success', 'Role deleted successfully.');
    }
}
