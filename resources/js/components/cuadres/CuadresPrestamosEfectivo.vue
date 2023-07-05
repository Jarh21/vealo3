<template>
    <div>
        <!-- modal -->
        <div class="modal" :class="{mostrarPrestamo:modal}">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Registro de Prestamos en Efectivo</h5>
                        <button type="button" class="btn-close" @click="cerrarModal()" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form action="">
                            <div class="row">
                                <div class="col">
                                    <label for="">Rif</label>
                                    <input type="text" class="form-control" v-model="formPrestamo.rif">
                                </div>
                                <div class="col">
                                    <label for="">Nombre</label>
                                    <input type="text" class="form-control" v-model="formPrestamo.nombre">
                                </div>
                            </div>
                            <div class="row">
                                <div class="col">
                                    <label for="">Descripción</label>
                                    <input type="text" class="form-control" v-model="formPrestamo.descripcion">
                                </div>
                            </div>
                            <div class="row">    
                                <div class="col">
                                    <label for="">Monto</label>
                                    <input type="numeric" class="form-control" v-model="formPrestamo.monto">
                                </div>
                            </div>
                            
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" @click="cerrarModal()">Cerrar</button>
                        <button type="button" class="btn btn-primary" @click="guardarPrestamo()">Guardar</button>
                    </div>
                </div>

            </div>
        </div>
        <!-- fin modal -->
        <div class="card">
            <div class="card-body">
                <b>Agregar Prestamos en Efectivo</b>
                <button class="btn btn-primary btn-sm" @click="abrirModal()">+ Agregar</button>
                <table class="table">
                    <thead>
                        <tr>
                            <th>Rif</th>
                            <th>Nombre</th>
                            <th>Descripción</th>
                            <th>Monto</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="(prestamoEfectivo, e) in prestamosEfectivo" :key="e">
                            <td>{{ prestamoEfectivo.rif }}</td>
                            <td>{{ prestamoEfectivo.nombre }}</td>
                            <td>{{ prestamoEfectivo.descripcion }}</td>
                            <td>{{ prestamoEfectivo.monto }}</td>
                            <td><button class="btn btn-danger btn-sm" @click="eliminarPrestamo(prestamoEfectivo.id)">Eliminar</button></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</template>
<script>
import axios from 'axios';
    export default {
        data() {
            return{
                formPrestamo:{
                    rif:'',
                    nombre:'',
                    descripcion:'',
                    monto:'',
                },
               modal:0,
               prestamosEfectivo:[], 
            }
            
        },
        methods:{
            async listarPrestamosEfectivos(){
                const res = await axios.get("cuadres-listar-prestamo-efectivo");
                this.prestamosEfectivo = res.data;
            },
            async guardarPrestamo(){
                await axios.post("cuadres-guardar-prestamo-efectivo",this.formPrestamo);
                delete this.formPrestamo.rif ;
                delete this.formPrestamo.nombre ;
                delete this.formPrestamo.descripcion ;
                delete this.formPrestamo.monto ;
                this.cerrarModal();
                this.listarPrestamosEfectivos();

            },
            async eliminarPrestamo(id){
                var eliminar = confirm("¿Desea eliminar el prestamo seleccionado?");
                if(eliminar){
                    await axios.get("cuadres-eliminar-prestamo-efectivo/"+id);
                    this.listarPrestamosEfectivos();  
                }
                
            },
            abrirModal(){
                this.modal=1;
            },
            cerrarModal(){
                this.modal=0;
            }
        },
        created(){
            this.listarPrestamosEfectivos();
        }
    }
    
</script>
<style>
    .mostrarPrestamo{
        display: list-item;
        opacity: 1;
        background-color: rgba(56, 56, 35, 0.699);
    }
</style>