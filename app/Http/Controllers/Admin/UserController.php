<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\UsuariosEmpresaAcceso;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\DB;
//use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Hash;
//use Spatie\Permission\Models\Permission;

class UserController extends Controller
{
    public function index(){
        return view('admin.user.index',['users'=>User::all()]);
    }

    public function register(){
        
        $roles = Role::all();
        return view('admin.user.register',compact('roles'));
    }

    public function save(Request $request){

        $user = new User();
        $user->name = $request->name;
        $user->email = $request->email;
        $user->password = Hash::make($request->password);
        $user->save();
        //si selecciono un rol se guarda
        if($request->roles){
        
            //asigna los roles con la erramienta de roles y permisos instalada laravel permision      
            $user->roles()->sync($request->roles);
        }

        //registramos en la tabla de permisos por empresas a todas las empresas por defeceto
        $empresas = DB::select("select * from empresas");
        foreach($empresas as $empresa){
            $empresaAcceso = new UsuariosEmpresaAcceso();
            $empresaAcceso->empresa_rif = $empresa->rif;
            $empresaAcceso->user_id = $user->id;
            $empresaAcceso->save();
        }

       return redirect()->route('admin.user.index');
    }

    public function edit($id){
        $user = User::find($id);
        $rolesActuales = $user->getRoleNames();        
        $permisosActuales = $user->getAllPermissions();
        $empresas = DB::select("select * from empresas");
        $roles = Role::all();
        $accesoEmpresas = UsuariosEmpresaAcceso::where('user_id',$id)->get();
        return view('admin.user.edit',compact('user','roles','rolesActuales','permisosActuales','empresas','accesoEmpresas'));
    }

    public function update(Request $request, $id){
        $empresas = $request->empresas;
        $user = User::find($id);
        if(!empty($request->password)){

           $user->password = Hash::make($request->password); 
           
           $user->update();
        }
        //asigna los roles con la erramienta de roles y permisos instalada laravel permision      
        $user->roles()->sync($request->roles);

        //eliminamos todos los registros seleccionados de las empresas a las que tiene acceso el usuario
        UsuariosEmpresaAcceso::where('user_id',$id)->delete();

        //registramos los nuevos accesos al sistema 
        if(!empty($empresas)){
            foreach($empresas as $empresa){
                $empresaAcceso = new UsuariosEmpresaAcceso();
                $empresaAcceso->empresa_rif = $empresa;
                $empresaAcceso->user_id = $id;
                $empresaAcceso->save();
            }   
        } 
        
        
        return redirect()->route('admin.user.edit',$user)->with('info','se asigno los roles correctamente');
    }

    public function delete($id){
        $user = User::find($id);
        $user->roles()->detach();
        $user->delete();
        return redirect()->route('admin.user.index');
    }
}
