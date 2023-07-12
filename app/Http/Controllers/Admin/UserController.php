<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Crypt;
//use Spatie\Permission\Models\Permission;

class UserController extends Controller
{
    public function index(){
        return view('admin.user.index',['users'=>User::all()]);
    }

    public function edit($id){
        $user = User::find($id);
        $rolesActuales = $user->getRoleNames();        
        $permisosActuales = $user->getAllPermissions();
        
        $roles = Role::all();
        return view('admin.user.edit',compact('user','roles','rolesActuales','permisosActuales'));
    }

    public function update(Request $request, $id){
        $user = User::find($id);
        if(!empty($request->password)){

           $user->password = bcrypt($request->password); 
           
           $user->update();
        }
        //asigna los roles con la erramienta de roles y permisos instalada laravel permision      
        $user->roles()->sync($request->roles);
        
        return redirect()->route('admin.user.edit',$user)->with('info','se asigno los roles correctamente');
    }

    public function delete($id){
        $user = User::find($id);
        $user->roles()->detach();
        $user->delete();
        return redirect()->route('admin.user.index');
    }
}
