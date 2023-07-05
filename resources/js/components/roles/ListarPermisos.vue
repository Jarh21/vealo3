<template>
    <div class="container">
        <div class="modal" :class="{mostrar:modal}">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" >Modal title</h5>
                        <button type="button" class="btn-close" @click="cerrarModal()"></button>
                    </div>
                    <div class="modal-body">
                        <form>
                            <input type="text" class="form-control" v-model="formPermiso.nuevoPermiso" placeholder="ingrese el apodo de la tura al cual se le agrega el permiso">
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" @click="cerrarModal()">Cerrar</button>
                        <button type="button" class="btn btn-primary" @click="guardarPermiso">Guardar</button>
                    </div>
                </div>
            </div>
        </div>
        <h3>Listado de Permisos <a class="btn btn-primary" @click="abrirModal()">+</a></h3>
        <div class="card">
            <div class="card-body">
                <table class="table">
                    <thead>
                        <tr>
                            <td>ID</td>
                            <td>Nombre</td>
                            <td>Acción</td>
                        </tr>
                    </thead>
                    <tbody>

                        <tr v-for="(permiso, i) in permisos" :key="i">
                            <td>{{ permiso.id }}</td>
                            <td>{{ permiso.name }}</td>
                            <td><button class="btn btn-danger btn-sm" @click="eliminarPermiso(permiso.id)">Eliminar</button></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

    </div>

    
</template>
<script>
//import axios from 'axios';

    export default{
        data(){
            return{
                formPermiso:{
                    nuevoPermiso:'',
                },
                modal:0,
                permisos:[],
            }
        },
        methods:{
            async allPermisos(){
                const resultado = await axios.get('permisos/listar');
                this.permisos = resultado.data;            
            },
            async guardarPermiso(){
                await axios.post('permisos/guardar-permisos',this.formPermiso);
                this.cerrarModal();
                this.allPermisos();
                
            },
            async eliminarPermiso(id){
               
                const resultado = await axios.get('permisos/eliminar-permisos/'+id);
                this.allPermisos();    
            },
            abrirModal(){
                this.modal=1; 
                this.nuevoPermiso='';            
                
            },

            cerrarModal(){
                this.modal=0;
            },

        },
       
        created(){
            this.allPermisos();
        }

    }
    
</script>
<style>
.mostrar{
    display: list-item;
    opacity: 1;
    background-color: rgba(27, 27, 23, 0.699);
}
</style>