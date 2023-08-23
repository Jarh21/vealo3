<template>
    <div>
        <!-- Modal -->
        <div class="modal fade" :id="datosProducto.keycodigo" data-backdrop="static" data-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="staticBackdropLabel">{{ datosProducto.descripcion }}</h5>
                        <button type="button" class="close" @click="cerrarModal()">
                        <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form >
                            <input type="text" :value="datosProducto.descripcion">
                            <span v-for="(empresa,index) in listaEmpresas" :key="index">
                                {{ empresa.nom_corto }}<input type="text" v-model="datosPedidos.empresasRif[index]" style="width: 40px;">
                            </span>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <!-- <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button> -->
                        <button type="button" class="btn btn-primary" @click="guardarPedidoDetallado()">Guardar</button>
                    </div>
                </div>
            </div>
        </div><!--  fin del modal -->
        <a  class="btn btn-primary btn-sm" @click="abrirModal()">Pedir {{ datosProducto.descripcion }}</a>
    </div>
</template>
<script>
import axios from 'axios';
import { ref } from 'vue';



    export default{
        mounted(){

            this.listarEmpresas();
            
        },
        props:{
            datosProducto:{},
        },
        data(){
            return {
               
                listaEmpresas:[],
                datosPedidos: {
                    producto:'',
                    empresasRif:[],
                    cantidad:0
                },
            }
        },
        methods:{
            
            async listarEmpresas(){
            const respuesta = await axios.get('http://localhost/vealo3/public/admin/empresa/listar/api');
            this.listaEmpresas = respuesta.data;
            
            },

            async guardarPedidoDetallado(){
                 await axios.post("guardar-pedido-detallado",this.datosPedidos);
                 console.log(this.datosPedidos);
                $('#agregarPedido').modal('hide')
            },

            abrirModal(){
                
                
                this.datosPedidos.empresasRif=[];
                this.datosPedidos.producto='';
               /*  this.listarEmpresas(); */
                this.datosPedidos.producto = this.datosProducto.descripcion
                console.log(this.datosPedidos.producto);
                /* this.datosPedidos = {producto:datos.descripcion} */
                /* $('#agregarPedido').modal('show') */
                $('#'+this.datosProducto.keycodigo).modal('show')
                //datosProducto.keycodigo.modal()
            },
            cerrarModal(){
                this.datosPedidos.empresasRif=[];
                $('#'+this.datosProducto.keycodigo).modal('hide')
            }
        }
    }
</script>