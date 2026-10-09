<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Spatie\Permission\Models\Permission;

class PermissionController extends Controller
{
    // show permission page
    public function index(){
        return view('permissions.list');
    }

    public function create(){
        return view('permissions.create');
    }

    public function store(Request $request){
        $validator = Validator::make($request->all(), [
            'name' => 'required|unique:permissions|min:3',

        ]);

        if($validator->passes()){
            Permission::create([
                'name' => $request->name,
            ]);

            return redirect()->route('permissions.index')->with('Success', 'Permission added successfully');
        }
        else{
            return redirect()->route('permissions.create')->withInput()->withErrors($validator);
        }
    }

    public function edit(){

    }

    public function update(){

    }

    public function destroy(){

    }

    


}
