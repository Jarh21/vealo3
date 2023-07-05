<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\DB;

class RolesPermisosController extends Controller
{
    public function roleIndex(){
        //listar los roles
        $roles = Role::all()->toArray();        
        //buscar los permisos correspondiente al rol
        foreach($roles as $role){
            //$permisos = DB::select('SELECT permissions.name FROM roles,role_has_permissions,permissions WHERE role_has_permissions.role_id=:rolId AND permissions.id=role_has_permissions.permission_id group by permissions.name',['rolId'=>$role['id']]);            
            $permisos = DB::select("
            SELECT
            permissions.id,
            permissions.name,
            (SELECT 1 AS acceso FROM role_has_permissions WHERE role_id=:rolId AND role_has_permissions.permission_id=permissions.id)acceso
            FROM 
            permissions
            ",[$role['id']]);
            $registroPermisos = json_decode(json_encode($permisos), true);//pasar de objeto a array            
            $rolPermiso = array('rol'=>$role,'permisos'=>$registroPermisos);
            $rolesPermisos[]=$rolPermiso;
        }
        
        return view('admin.rolesYpermisos.roleIndex',['rolesPermisos'=>$rolesPermisos]);
    }

    public function roleIndexApi(){
        //$roles = Role::get();        
        $roles = Role::all()->toArray();
         foreach($roles as $role){
            $permisos = DB::select('SELECT permissions.name FROM roles,role_has_permissions,permissions WHERE role_has_permissions.role_id=:rolId AND permissions.id=role_has_permissions.permission_id group by permissions.name',['rolId'=>$role['id']]);
            /* $permisos = DB::select("
            SELECT
            permissions.id,
            permissions.name,
            (SELECT 1 AS acceso FROM role_has_permissions WHERE role_id=:rolId AND role_has_permissions.permission_id=permissions.id)acceso
            FROM 
            permissions
            ",[$role['id']]); */
            $registroPermisos = json_decode(json_encode($permisos), true);//pasar de objeto a array            
            $rolPermiso = array('rol'=>$role,'permisos'=>$registroPermisos);
            $rolesPermisos[]=$rolPermiso;
        } 
        return $rolesPermisos;
    }   

    public function guardarRolApi(Request $request){    
        //crear roles con sus respectivos permisos   
        $role = Role::create(['name' => $request->rol])->syncPermissions($request->selectPermisos);    
    }       

    public function revocarPermisosRolId($role){
        //$role = Role::find($rolId);
        $permisos = DB::delete('DELETE FROM role_has_permissions WHERE role_has_permissions.role_id=?',[$role]);        

    }

    public function editarRol($rolId){
        
        $datosRole= self::rolesPermisosAsignadoYporAsignar($rolId);
        return view('admin.rolesYpermisos.roleEdit',compact('datosRole'));
    }

    public function updateRol(Request $request,$roleId){
        
        $role = Role::find($roleId);
        $role->syncPermissions($request->permisos_por_asignar);
        return redirect()->route('admin.role.editar',$roleId)->with('infoRol','se asigno los permisos correctamente');
    }

    private function rolesPermisosAsignadoYporAsignar($roleId){
        
        //buscamos los datos del role, los permisos asignados y los que no tiene asignados
        $idAsignados=[];
        $role = Role::find($roleId);
        $asignados = DB::select(' SELECT permissions.id, permissions.name FROM roles, role_has_permissions, permissions WHERE role_has_permissions.role_id =:rolId AND permissions.id = role_has_permissions.permission_id AND roles.id = role_has_permissions.role_id',['rolId'=>$roleId]);
        foreach($asignados as $asignado){
            
                $idAsignados[] = $asignado->id;
        }
        $porAsignar = Permission::select('id','name')->whereNotIn('id',$idAsignados)->get();
        return ['role'=>$role,'asignados'=>$asignados,'porAsignar'=>$porAsignar];
    }

    public function eliminarRole($id){
        Role::where('id',$id)->delete();
    }

    public function permisosIndex(){
        return view('admin.rolesYpermisos.permisosIndex');
    }

    public function permisosListar(){
        return Permission::get();
    }

    public function guardarPermisos(Request $request){
        $permission = Permission::create(['name' => $request->nuevoPermiso]);
    }

    public function allPermisosApi($rolId=''){
        //listar todos los permisos
        if(empty($rolId)){
            return Permission::getPermissions(); 
        }else{
            return $permisos = DB::select("
            SELECT
            permissions.id,
            permissions.name,
            (SELECT 1 AS acceso FROM role_has_permissions WHERE role_id=:rolId AND role_has_permissions.permission_id=permissions.id)acceso
            FROM 
            permissions
            ",[$rolId]);
        }
       
    }
    

    public function eliminarPermisos($id){
        Permission::where('id',$id)->delete();
    }
}
