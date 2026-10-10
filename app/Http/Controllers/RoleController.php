<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleController extends Controller
{
    public function index(){
        $roles = Role::orderBy('name', 'ASC')->paginate(1);
        return view('roles.list', [
            'roles' => $roles
        ]);
    }

    public function create(){
        $permissions = Permission::orderBy('name', 'ASC')->get();
        
        return view('roles.create', [
            'permissions' => $permissions
        ]);
    }

    public function store(Request $request){
        
        $validator = Validator::make($request->all(), [
            'name' => 'required|unique:roles|min:3',

        ]);

        if($validator->passes()){
            $role = Role::create([
                'name' => $request->name,
            ]);
            if(!empty($request->permission)){
            foreach($request->permission as $name){
                $role->givePermissionTo($name);
            }
        }
            return redirect()->route('roles.index')->with('Success', 'Role added successfully');
        }

        

        else{
            return redirect()->route('roles.create')->withInput()->withErrors($validator);
        }
    }

    
}
