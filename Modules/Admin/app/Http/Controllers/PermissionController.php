<?php

namespace Modules\Admin\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Permission;
use Inertia\Inertia;

class PermissionController extends Controller
{
    public function index(Request $request)
    {
        return Inertia::render('admin/permissions/Index');
    }

    public function getData(Request $request)
    {
        $query = Permission::query();

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
        return Inertia::render('admin/permissions/Form', [
            'permission' => new Permission(),
            'isEdit' => false,
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:permissions',
        ]);

        Permission::create(['name' => $request->name]);

        return $request->header('X-Inertia')
            ? redirect()->route('admin.permissions.index')->with('success', 'Permission created successfully.')
            : response()->json(['success' => true, 'message' => 'Permission created successfully.'], 200);
    }

    public function edit(Permission $permission)
    {
        return Inertia::render('admin/permissions/Form', [
            'permission' => $permission,
            'isEdit' => true,
        ]);
    }

    public function update(Request $request, Permission $permission)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:permissions,name,' . $permission->id,
        ]);

        $permission->update(['name' => $request->name]);

        return $request->header('X-Inertia')
            ? redirect()->route('admin.permissions.index')->with('success', 'Permission updated successfully.')
            : response()->json(['success' => true, 'message' => 'Permission updated successfully.'], 200);
    }

    public function destroy(Permission $permission)
    {
        $permission->delete();
        return redirect()->route('admin.permissions.index')->with('success', 'Permission deleted successfully.');
    }
}
