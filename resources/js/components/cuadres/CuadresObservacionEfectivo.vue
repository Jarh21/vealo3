<template>

    <div>
        <!-- Modal -->
        <div class="modal" :class="{mostrar:modal}">
            <div class="modal-dialog">
                <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Observacion de Efectivo</h5>
                    <button type="button" class="btn-close" @click="cerrarModal()" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action="" method="post">
                    <div >
                        <label for="empleados">Al Empleado</label>
                        <select  id="empleados" class="form-control" v-model="formObservaciones.empleado">
                            <option disabled value="">-- Seleccione empleado --</option>
                            <option v-for="(empleado,a ) in empleadosDelCuadre" :key="a">{{ empleado.codusua+'|'+empleado.usuario }}</option>
                        </select>
                    </div>
                    <div>
                        <label for="">A su efectivo</label>
                        <select name="suma_resta" id="" class="form-control" v-model="formObservaciones.suma_resta">
                            <option value="" disabled>--Sumar o Restar--</option>
                            <option value="s">Sumar</option>
                            <option value="r">Restar</option>
                        </select>
                        
                    </div>
                    <div >
                        <label for="">Monto</label>
                        <input type="text" class="form-control" v-model="formObservaciones.monto"> 
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
                    <label for="" class="text-success">Observaciones de Efectivo</label>        
                    <button class="btn btn-success btn-sm" @click="abrirModal('Efectivo')">+ Agregar Efectivo</button>
                </div>          


                <div class="my-3">
                    <table class="table">
                        <thead border=1>
                            <tr>                        
                                <th>Usuario</th>
                                <th>Tipo</th>                        
                                <th>Monto</th>
                                <th>Observacion</th>
                                <th>Accion</th>
                            </tr>
                        </thead>
                        <tbody border=1>

                            <tr v-for="(observacion, i ) in listaObservacionesEfectivo" :key="i">
                                
                                <td>{{ observacion.usuario }}</td>
                                <td>{{ observacion.sumarOrestar }}</td>
                                <td>{{ observacion.monto }}</td>
                                <td>{{ observacion.observacion }}</td>
                                <td><button class="btn btn-danger btn-sm" @click="eliminarObservacion(observacion.id)">Eliminar</button></td>
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
                    monto:'',
                    tipo_observacion:'',
                    suma_resta:'',
                    empleado:'',
                },
                modal:0,
                
                listaObservacionesEfectivo:[],               
                empleadosDelCuadre:[],
                tipoObservacion:'',
            }
        },
        methods:{
            
            async listarEfectivo(){
                const respuesta = await axios.get("cuadres-listar-observaciones/Efectivo");
                this.listaObservacionesEfectivo = respuesta.data;
            },           
            async listaEmpleadosCuadre(){
                const respuesta = await axios.get("cuadres-lista-empleados-arqueo");
                this.empleadosDelCuadre = respuesta.data;
                
            },
            async eliminarObservacion(id){
                var eliminar = confirm("¿Desea eliminar la observacion del efectivo seleccionada ?");
                if(eliminar){
                    await axios.get("cuadres-eliminar-observacion/"+id);
                    this.listarEfectivo();
                }
                
            },
            async guardarObservacion(){
                this.formObservaciones.tipo_observacion = this.tipoObservacion;
                const respuesta = await axios.post("cuadres-guardar-observacion",this.formObservaciones);
                delete this.formObservaciones.empleado;                
                delete this.formObservaciones.observacion;              
                delete this.formObservaciones.monto;
                delete this.formObservaciones.suma_resta;
            
                this.cerrarModal();
                this.listarEfectivo();
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
            
            this.listarEfectivo();           
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
