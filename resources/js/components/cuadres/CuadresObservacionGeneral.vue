<template>
    <div>
        <!-- Modal -->
        <div class="modal " :class="{mostrar:modal}">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Observacion General</h5>
                    <button type="button" class="btn-close" @click="cerrarModal()" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action="" method="post">
                        
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
                <div >
                    <label for="" class="text-primary">Observaciones del Cuadre</label>
                    <button class="btn btn-primary btn-sm" @click="abrirModal('General')">+ Agregar General</button>
                </div>
                
                

                <div class="my-3">
                    <table class="table">
                        <thead border=1>
                            <tr>                        
                                <th>Observacion</th>
                                <th>Accion</th>
                            </tr>
                        </thead>
                        <tbody border=1>

                            <tr v-for="(observacion, i ) in listaObservacionesGeneral" :key="i">
                                
                                <td>{{ observacion.observacion }}</td>
                                <td>
                                    <button class="btn btn-danger btn-sm" @click="eliminarObservacion(observacion.id)">Eliminar</button>
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
import axios from 'axios';

    export default {
        data(){
            return{
                formObservaciones:{
                    observacion:'',
                    tipo_observacion:'',
                    
                },
                modal:0,
                listaObservacionesGeneral:[],              
               
                tipoObservacion:'',
            }
        },
        methods:{
            async listarGeneral(){
                const respuesta = await axios.get("cuadres-listar-observaciones/General");
                this.listaObservacionesGeneral = respuesta.data;
            },
            async eliminarObservacion(id){
                var eliminar = confirm("¿Desea eliminar la observacion general seleccionada?");
                if(eliminar){
                    await axios.get("cuadres-eliminar-observacion/"+id);
                    this.listarGeneral();
                }
                
            },
            
            async guardarObservacion(){
                this.formObservaciones.tipo_observacion = this.tipoObservacion;
                const respuesta = await axios.post("cuadres-guardar-observacion",this.formObservaciones);
                
                delete this.formObservaciones.observacion;
                
                this.cerrarModal();
                this.listarGeneral();
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
            this.listarGeneral();            
            
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
