<template>
    <div>
        
            
            <!-- The Modal -->
            <div class="modal fade" id="modalRoles" data-backdrop="static" data-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
                <div class="modal-dialog modal-lg ">
                    <div class="modal-content">

                        <!-- Modal Header -->
                        <div class="modal-header">
                            <h4 class="modal-title">{{tituloModal}}</h4>
                            <button type="button" @click="cerrarModal()"><span aria-hidden="true">&times;</span></button>
                        </div>
                        <form >
                        <!-- Modal body -->
                        <div class="modal-body ">
                        
                                <input v-model="formularioRolesPermisos.rol" type="text" class="form-control" placeholder="Escriba el nombre del Rol" >
                                <div class="d-flex flex-wrap">
                                    <div class=" bd-highlight" v-for="(permiso, index) in permisos" :key="index">
                                        <span class="ml-1">
                                            <input type="checkbox"  v-model="formularioRolesPermisos.selectPermisos" v-bind:value="permiso.name">                         
                                        
                                        <label for="">{{ permiso.name }}</label>
                                        </span>
                                            
                                    </div>                                     
                                </div>
                                                           
                        </div>

                        <!-- Modal footer -->
                        <div class="modal-footer">
                            <button type="button" class="btn btn-danger" data-bs-dismiss="modal" @click="cerrarModal">Close</button>
                            <button type="submit" class="btn btn-primary" data-bs-dismiss="modal" @click="crear">Guardar</button>
                        </div>
                        </form>
                    </div>
                </div>
            </div> <!-- fin modal -->
        
        
        <div class="container-fluid">
            <h3>Roles de Usuarios </h3><a  class="btn btn-primary mx-3" @click="modificar=false; abrirModal();"> + Agregar Nuevo Rol</a>
            <div class="card">
                <div class="card-body">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Id</th>
                                <th>Nombre</th>
                                <th>Permisos</th>
                                <th>Opciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="(rolPermiso, index) in rolesPermisos" :key="'a'+index">
                                <td>{{rolPermiso['rol'].id}}</td>
                                <td>{{rolPermiso['rol'].name}}</td>                    
                                <td >
                                    <span v-for="(permisos, i) in rolPermiso['permisos']" :key ="i" >
                                        
                                        <a  href="" class="btn btn-secondary btn-sm m-1">{{ permisos.name }}{{ permisos.acceso }}</a>
                                    </span>                              
                                    
                                </td>
                                <td>
                                    <a :href="'roles/editarRole/'+rolPermiso['rol'].id" class="btn btn-warning btn-sm" >Editar</a>                                    
                                    <a href="#" v-if="rolPermiso['permisos'].length===0" class="btn btn-danger btn-sm" @click="eliminarRole(rolPermiso['rol'].id)">Eliminar</a>
                                    <a href="#" v-else class="btn btn-secondary btn-sm" @click="revocarPermisosRolId(rolPermiso['rol'].id)">Revocar todo</a>
                                </td>           
                                
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>        
        </div>
        
    </div>
    
</template>
<script>
//import axios from 'axios';

    export default {
        data(){
            return{
                formularioRolesPermisos:{
                    rol:'',
                    selectPermisos:[],
                },
                modificar:true,
                modal:0,
                tituloModal:'',
                rolesPermisos:[],
                permisos:[],
                errors: []
            }
        },
        methods:{

          async listar(){
           const respuesta = await axios.get('roles/api');
            this.rolesPermisos=respuesta.data; 
                  
          },

          async allPermisos(){
            const resultado = await axios.get('roles/all-permisos/api');
            this.permisos = resultado.data;
            
          },

          async crear(){  
                   
                const respuesta = await axios.post('roles/guardarApi',this.formularioRolesPermisos);            
                this.cerrarModal();
                this.listar();            
          },

          async revocarPermisosRolId(id){
            
            var respuesta = await axios.get('roles/revocarPermisosRolId/'+id);
            this.listar();
          },

          async eliminarRole(id){
            var accion = await axios.get('roles/eliminarRole/'+id);
            this.listar();
          },          
          
          abrirModal(data={}){
            $('#modalRoles').modal('show')           
            if(this.modificar== true){                   
                this.tituloModal="Modificar Rol";
                this.formularioRolesPermisos.rol=data.name;
                
            }else{
                
                this.tituloModal="Crear Rol";
                this.formularioRolesPermisos.rol='';
            }
            
            
          },

          cerrarModal(){
            $('#modalRoles').modal('hide');
          },
          
          
        },
        created(){
            this.listar();
            this.allPermisos();
        },
     
    }

</script>
<style>
.mostrar{
    display: list-item;
    opacity: 1;
    background-color: rgba(27, 27, 23, 0.699);
}
</style>