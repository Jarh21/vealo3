<template>
    <div>
        <!-- Modal -->
        <div class="modal" :class="{mostrar:modal}">
            <div class="modal-dialog">
                <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Observacion en Tarjeta</h5>
                    <button type="button" class="btn-close" @click="cerrarModal()" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action="" method="post">
                    <div >
                        <label for="empleados">Al Empleado corregir monto</label>
                        <select  id="empleados" class="form-control" v-model="formObservaciones.empleado">
                            <option disabled value="">-- Seleccione empleado --</option>
                            <option v-for="(empleado,a ) in empleadosDelCuadre" :key="a">{{ empleado.codusua+'|'+empleado.usuario }}</option>
                        </select>
                    </div>
                    <div >
                        <label for="">Monto</label>
                        <input type="text" class="form-control" v-model="formObservaciones.monto"> 
                    </div>
                   
                    
                    <div v-if="tipoObservacion === 'Tarjeta'">
                        <div class="row">
                            <div class="col">
                                <label for="">Tarjeta Nº</label>
                                <input type="text" class="form-control" v-model="formObservaciones.tarjeta">
                            </div>
                            <div class="col">
                                <label for="aprobacion">Aprobacion Nº</label>
                                <input type="text" id="aprobacion" class="form-control" v-model="formObservaciones.aprobacion">
                            </div>
                        </div>
                        <div class="row">
                            <div class="col">
                                <label for="">Del Punto Bancario</label>
                                <select  id="" class="form-control" v-model="formObservaciones.bancos">
                                    <option  v-for="(banco,i) in listaBancos" :key="i" >{{banco.nombre}}</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div>
                        <label for="">Observación</label>
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
                <div class="">
                    <label for="" class="text-warning">Observaciones deTarjeta</label>
                
                    <button class="btn btn-warning btn-sm" @click="abrirModal('Tarjeta')">+ Agregar Tarjeta</button>     
                </div>        

                <div class="my-3">
                    <table class="table">
                        <thead border=1>
                            <tr>
                                
                                <th>Usuario</th>
                                <th>Numero</th>
                                <th>Aprobacion</th>
                                <th>Monto</th>
                                <th>Banco</th>
                                <th>Observacion</th>
                                <th>Accion</th>

                            </tr>
                        </thead>
                        <tbody border=1>

                            <tr v-for="(observacion, i ) in listaObservacionesTarjeta" :key="i">
                                
                                <td>{{ observacion.usuario }}</td>
                                <td>{{ observacion.numero }}</td>
                                <td>{{ observacion.aprobacion }}</td>                                
                                <td>{{ observacion.monto }}</td>
                                <td>{{ observacion.banco}}</td>
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
                    tarjeta:'',
                    aprobacion:'',
                    monto:'',
                    tipo_observacion:'',                    
                    empleado:'',   
                    bancos:'',                 

                },
                modal:0,
                
                listaObservacionesTarjeta:[],                
                empleadosDelCuadre:[],
                listaBancos:[],
                tipoObservacion:'',
            }
        },
        methods:{
           
            async listarTarjeta(){
                const respuesta = await axios.get("cuadres-listar-observaciones/Tarjeta");
                this.listaObservacionesTarjeta = respuesta.data;
            },
            async listadeBnacos(){
                const res = await axios.get("banco-lista-bancos");
                this.listaBancos = res.data;
                
            },
            async listaEmpleadosCuadre(){
                const respuesta = await axios.get("cuadres-lista-empleados-arqueo");
                this.empleadosDelCuadre = respuesta.data;
                
            },
            async eliminarObservacion(id){
                var eliminar = confirm("¿Desea eliminar la observacion de tarjetas seleccionada?");
                if (eliminar) {
                    await axios.get("cuadres-eliminar-observacion/"+id);
                    this.listarTarjeta();
                }
                
            },
            async guardarObservacion(){
                this.formObservaciones.tipo_observacion = this.tipoObservacion;
                const respuesta = await axios.post("cuadres-guardar-observacion",this.formObservaciones);
                delete this.formObservaciones.empleado;
                delete this.formObservaciones.tarjeta;
                delete this.formObservaciones.observacion;
                delete this.formObservaciones.aprobacion;
                delete this.formObservaciones.monto;
                delete this.formObservaciones.bancos;
                
                this.cerrarModal();
                this.listarTarjeta();
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
            
            this.listarTarjeta();            
            this.listaEmpleadosCuadre();
            this.listadeBnacos();
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
