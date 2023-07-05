<template>

    <div>
        <!-- Modal -->
        <div class="modal" :class="{mostrar:modal}">
            <div class="modal-dialog">
                <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Otra Observación</h5>
                    <button type="button" class="btn-close" @click="cerrarModal()" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action="" method="post">
                    <div v-if="tipoObservacion != 'General'">
                        <label for="empleados">Al Empleado</label>
                        <select  id="empleados" class="form-control" v-model="formObservaciones.empleado">
                            <option disabled value="">-- Seleccione empleado --</option>
                            <option v-for="(empleado,a ) in empleadosDelCuadre" :key="a">{{ empleado.codusua+'|'+empleado.usuario }}</option>
                        </select>
                    </div>
                    
                    <div>
                        <label for="">Observación<span class="text-danger" v-if="tipoObservacion==='Efectivo'">(opcional)</span></label>
                        <input type="text" class="form-control" v-model="formObservaciones.observacion"> 
                    </div>                  
                    
                    
                    </form>
                    
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" @click="cerrarModal()">Cerrar</button>
                    <button type="button" class="btn btn-primary" @click="guardarObservacion()">Guardar</button>
                </div>
                </div>
            </div>
        </div><!-- fin modal -->
        <div class="card">
            <div class="card-body">
                <div>
                    <label for="" class="text-secondary">Otras Observaciones de Empleado</label>
                    <button class="btn btn-secondary btn-sm" @click="abrirModal('Otra')">+ Agregar Otra</button>  
                </div>
                    
                
                <div class="my-3">
                    <table class="table">
                        <thead border=1>
                            <tr>
                                
                                <th>Usuario</th>                        
                                <th>Observacion</th>
                                <th>Accion</th>
                            </tr>
                        </thead>
                        <tbody border=1>

                            <tr v-for="(observacion, i ) in listaObservacionesOtra" :key="i">
                                
                                <td>{{ observacion.usuario }}</td>                        
                                <td>{{ observacion.observacion }}</td>
                                <td>
                                    <button class="btn btn-danger btn-sm"  @click="eliminarObservacion(observacion.id)">Eliminar</button>
                                    
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
    export default {
        data(){
            return{
                formObservaciones:{
                    observacion:'',                    
                    tipo_observacion:'',                    
                    empleado:'',
                },
                modal:0,                
                listaObservacionesOtra:[],
                empleadosDelCuadre:[],
                tipoObservacion:'',
            }
        },
        methods:{
            
            async listarOtra(){
                const respuesta = await axios.get("cuadres-listar-observaciones/Otra");
                this.listaObservacionesOtra = respuesta.data;
            },
            async listaEmpleadosCuadre(){
                const respuesta = await axios.get("cuadres-lista-empleados-arqueo");
                this.empleadosDelCuadre = respuesta.data;
                
            },
            async eliminarObservacion(id){
                var eliminar = confirm("¿Desea eliminar la observacion seleccionada?");
                if(eliminar){
                    await axios.get("cuadres-eliminar-observacion/"+id);
                    this.listarOtra(); 
                }
                
            },
            async guardarObservacion(){
                this.formObservaciones.tipo_observacion = this.tipoObservacion;
                const respuesta = await axios.post("cuadres-guardar-observacion",this.formObservaciones);
                delete this.formObservaciones.empleado;               
                delete this.formObservaciones.observacion;
               
                this.cerrarModal();
                this.listarOtra();
            },
            abrirModal(tipoObservacion){
                this.modal=1;
                this.tipoObservacion=tipoObservacion;
            },
            cerrarModal(){
                this.modal=0;
            }
        },
        created(){
            
            this.listarOtra();
            this.listaEmpleadosCuadre();
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
