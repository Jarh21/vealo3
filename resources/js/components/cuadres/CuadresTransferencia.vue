<template>
    <div>
        <!-- Modal -->
        <div class="modal " :class="{mostrar:modal}">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Registro de Transferencias</h5>
                    <button type="button" class="btn-close" @click="cerrarModal()" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action="" method="post">
                        
                    <div>
                        <label for="">Banco Emisor</label>                        
                        <select class="form-control" v-model="formTransferencias.banco_emisor">
                            <option value="" disabled>-- Selecciones --</option>
                            <option :value="banco.id " v-for="(banco,a) in listaBancos" :key="a">{{ banco.nombre }}</option>
                        </select> 
                    </div>                    
                    <div>
                        <label for="">Banco Receptor</label>                        
                        <select  class="form-control" v-model="formTransferencias.banco_receptor">
                            <option value="" disabled>-- Selecciones --</option>
                            <option :value="banco.id " v-for="(banco,a) in listaBancos" :key="a">{{ banco.nombre }}</option>
                        </select>
                    </div>
                    <div>
                        <label for="">Fecha Transferencia</label>
                        <input type="date"  class="form-control" v-model="formTransferencias.fecha_transferencia">
                    </div>
                    <div>
                        <label for="">Descripción</label>
                        <input type="text" class="form-control" v-model="formTransferencias.descripcion">
                    </div>
                    <div>
                        <label for="">Numero Transferencia</label>
                        <input type="text" class="form-control" v-model="formTransferencias.numero_transferencia">
                    </div>
                    <div>
                        <label for="">Monto</label>
                        <input type="text" class="form-control" v-model="formTransferencias.monto">
                    </div>
                    
                    </form>
                    
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" @click="cerrarModal()">Cerrar</button>
                    <button type="button" class="btn btn-primary" @click="guardarTransferencia()">Guardar</button>
                </div>
                </div>
            </div>
        </div><!-- fin modal -->
        <div class="card">
            <div class="card-body">
                
                <b class="my-3">Transferencias: Pago de Facturas</b>
                <table>
                    <tr>
                        <th style="border: 2px solid gainsboro; text-align: center;">Cliente</th>
                        <th style="border: 2px solid gainsboro; text-align: center;">Pago</th>                        
                        <th style="border: 2px solid gainsboro; text-align: center;">Entidad</th>
                        <th style="border: 2px solid gainsboro; text-align: center;">Referencia</th>
                        <th style="border: 2px solid gainsboro; text-align: center;">Factura</th>
                        <th style="border: 2px solid gainsboro; text-align: center;">Monto</th>
                    </tr>
                    <tr v-for="(transferenciaSiace, f) in listaTransferenciaSiace" :key="f">
                        <td style="border: 2px solid gainsboro;"><span class="mx-2">{{ transferenciaSiace.cliente }}</span></td>
                        <td style="border: 2px solid gainsboro;"><span class="mx-2">{{ transferenciaSiace.tipo }}</span></td>                        
                        <td style="border: 2px solid gainsboro;"><span class="mx-2">{{ transferenciaSiace.entidad }}</span></td>
                        <td style="border: 2px solid gainsboro;"><span class="mx-2">{{ transferenciaSiace.numero }}</span></td>
                        <td style="border: 2px solid gainsboro;"><span class="mx-2">{{ transferenciaSiace.fiscalcomp }}</span></td>
                        <td style="border: 2px solid gainsboro; text-align: right;">{{ transferenciaSiace.monto }}</td>
                    </tr>
                </table>
                <div class="my-3">
                    <b >Registrar Transferencia </b><button class="btn btn-primary btn-sm" @click="abrirModal()">+ Nuevo</button>
                </div>
                
                <table v-if="transferencias.length > 0">
                    <thead>
                        <tr>                            
                            <th>Banco Receptor</th>
                            <th>Descripción</th>
                            <th>Nº Referencia</th>
                            <th>Monto</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody >
                        <tr v-for="(transferencia,i) in transferencias" :key="i">
                            
                            <td style="border:  2px solid gainsboro;"><span class="mx-2">{{ transferencia.banco_receptor }}</span></td>
                            <td style="border:  2px solid gainsboro;"><span class="mx-2">{{ transferencia.descripcion }}</span></td>
                            <td style="border:  2px solid gainsboro;"><span class="mx-2">{{ transferencia.numero_transferencia }}</span></td>
                            <td style="border:  2px solid gainsboro;"><span class="mx-2">{{ transferencia.monto }}</span></td>
                            <td><button class="btn btn-danger btn-sm"  @click="eliminarTransferencia(transferencia.id)">Eliminar</button></td>
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
        data(){
            return{
                formTransferencias:{
                    banco_emisor:'',
                    banco_receptor:'',
                    descripcion:'',
                    numero_transferencia:'',
                    monto:'',
                    fecha_transferencia:'',
                    
                },
                modal:0,
                transferencias:[],               
                listaBancos:[],
                listaTransferenciaSiace:[],
            }
        },
        methods:{
            
             async eliminarTransferencia(id){
                var eliminar = confirm("¿Desea Eliminar la transferencia seleccionada?");
                if(eliminar){
                   await axios.get("cuadres-eliminar-transferencias/"+id);
                    this.listarTransferencia(); 
                }
                
            }, 
            async listadeBnacos(){
                const res = await axios.get("banco-lista-bancos");
                this.listaBancos = res.data;
                
            },
            async listarTransferencia(){
                const res = await axios.get("cuadres-listar-transferencias");
                this.transferencias=res.data;
            },
            async listarTransferenciasSiace(){
                const resp = await axios.get("cuadres-listar-transferencias-siace");
                this.listaTransferenciaSiace = resp.data;
            },

            async guardarTransferencia(){
                
                const respuesta = await axios.post("cuadres-registro-transferencias",this.formTransferencias);
                
                delete this.formTransferencias.banco_emisor;
                delete this.formTransferencias.banco_receptor;
                delete this.formTransferencias.descripcion;
                delete this.formTransferencias.numero_transferencia;
                delete this.formTransferencias.monto;
                delete this.formTransferencias.fecha_transferencia;
                

                this.cerrarModal();
                this.listarTransferencia();
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
            this.listadeBnacos();
            this.listarTransferencia();            
            this.listarTransferenciasSiace();
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