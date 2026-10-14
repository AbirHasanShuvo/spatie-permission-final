<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleController extends Controller
{
    public function index()
    {
        $roles = Role::orderBy('name', 'ASC')->paginate(25);
        return view('roles.list', [
            'roles' => $roles
        ]);
    }

    public function create()
    {
        $permissions = Permission::orderBy('name', 'ASC')->get();

        return view('roles.create', [
            'permissions' => $permissions
        ]);
    }

    public function store(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'name' => 'required|unique:roles|min:3',

        ]);

        if ($validator->passes()) {
            $role = Role::create([
                'name' => $request->name,
            ]);
            if (!empty($request->permission)) {
                foreach ($request->permission as $name) {
                    $role->givePermissionTo($name);
                }
            }
            return redirect()->route('roles.index')->with('Success', 'Role added successfully');
        } else {
            return redirect()->route('roles.create')->withInput()->withErrors($validator);
        }
    }

    public function edit($id)
    {
        $role = Role::findOrFail($id);
        $haspermissions = $role->permissions->pluck('name');
        $permissions = Permission::orderBy('name', 'ASC')->get();

        return view('roles.edit', [
            'permissions' => $permissions,
            'haspermissions' => $haspermissions,
            'role' => $role
        ]);
    }

    public function update($id, Request $request)
    {
        $role = Role::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'name' => 'required|min:3|unique:roles,name,' . $id,
        ]);

        if ($validator->fails()) {
            return redirect()->route('roles.edit', $id)
                ->withInput()
                ->withErrors($validator);
        }

        $role->name = $request->name;
        $role->save();
        $role->syncPermissions($request->permission ?? []);

        return redirect()->route('roles.index')->with('Success', 'Role updated successfully');
    }

    public function destroy(Request $request)
    {
        $id = $request->id;
        $role = Role::findOrFail($id);

        if ($role == null) {
            session()->flash('error', 'role not found');
            return response()->json([
                'status' => 'false'
            ]);
        }
        $role->delete();
        session()->flash('success', 'role deleted successfully');
        return response()->json(
            ['status' => 'true']
        );
    }
}
