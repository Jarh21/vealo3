<template>
    <div>
        <!-- Modal Editar cantidades-->
        <div class="modal fade" id="editarPedido" data-backdrop="static" data-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="staticBackdropLabel">Editar Producto</h5>
                        <button type="button" class="close" @click="cerrarModalEditar()">
                        <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form >
                            <div v-for="(pedido,id) in listaEditarPedidos" :key="id">
                                {{ pedido.nom_corto }} pidio {{ pedido.cantidad }}<input type="text"  name="" id="">
                            </div>                            
                            
                        </form>
                    </div>
                    <div class="modal-footer">
                        <!-- <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button> -->
                        <button type="button" class="btn btn-warning" >Editar</button>
                    </div>
                </div>
            </div>
        </div>
        <!--fin modal Editar Cantidades-->
        <!-- Modal ingresar Cantidades-->
        <div class="modal fade" id="agregarPedido" data-backdrop="static" data-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="staticBackdropLabel">{{ datosPedidos.producto }}</h5>
                        <button type="button" class="close" @click="cerrarModal()">
                        <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form >
                            <input type="hidden" v-model="datosPedidos.visualizadorPrecioId">
                            <input type="hidden" v-model="datosPedidos.producto">
                            <input type="hidden" v-model="datosPedidos.drogueria">
                            <input type="hidden" v-model="datosPedidos.costo">
                            <div v-for="(empresa,index) in empresas" :key="index">
                                 {{ empresa.nom_corto }}
                                <input type="text" v-model="datosPedidos.cantidad[index]" onfocus style="width: 40px;">                               
                                

                               
                            </div>
                            
                        </form>
                    </div>
                    <div class="modal-footer">
                        <!-- <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button> -->
                        <button type="button" class="btn btn-primary" @click="guardarPedidoDetallado()">Guardar</button>
                    </div>
                </div>
            </div>
        </div><!--  fin del modal ingresar cantidades-->
        <table id="articulos" class="table">
            <thead>
                <tr>
                    <th>Nº</th>
                    <th>Descripción</th>
                    <th>Costo</th>
                    <th>Drogueria</th>                    
                    <th>Accion</th>
                </tr>
            </thead>
            <tbody>
               <tr v-for="datos in listasPrecios">
                    <td>{{ datos.keycodigo }}</td>
                    <td>{{ datos.descripcion }}</td>
                    <td>{{ datos.costo }}</td>
                    <td>{{ datos.drogueria }}</td>
                    <td >
                        <a  class="btn btn-primary btn-sm" @click="abrirModal(datos.descripcion, datos.drogueria,datos.costo,datos.keycodigo)">Pedir </a>
                        <a class="btn btn-warning btn-sm" @click="abrirModalEditar(datos.keycodigo)">Editar</a>
                    </td>
                    
               </tr> 
            </tbody>
            
        </table>
    </div>
</template>
<script>
import axios from 'axios';
import DataTable from 'datatables.net-bs4';
import jszip from 'jszip';
import pdfmake from 'pdfmake';
import 'datatables.net-buttons-bs4';
import 'datatables.net-buttons/js/buttons.colVis.mjs';
import 'datatables.net-buttons/js/buttons.html5.mjs';
import 'datatables.net-buttons/js/buttons.print.mjs';
import RegistroPorFarmacia from './RegistroPorFarmacia.vue';
window.JSZip = jszip

    export default{
        mounted(){
            
            this.visualizarPrecios();
            this.listarEmpresas();
        },
        /* components:{
            RegistroPorFarmacia
        }, */
        data(){
            return {
                listasPrecios:[],
                empresas:[],
                datosPedidos: {
                    visualizadorPrecioId:'',
                    producto:'',
                    drogueria:'',
                    costo:'',

                    cantidad:[]
                },
                listaEditarPedidos:[],
            }
        },
        methods:{

            async listarEmpresas(){
            const respuesta = await axios.get('http://localhost/vealo3/public/admin/empresa/listar/api');
            this.empresas = respuesta.data;
            
            },
            
            tabla(){ //asi se llama datatabes en vue
                this.$nextTick(()=>{
                    $('#articulos').DataTable({
                        dom: 'Bfrtip',
                        buttons: [
                            {
                                "extend":"copyHtml5",
                                "text":"Copiar",
                                "className":"btn btn-secondary mx-1"
                            },
                            {
                                "extend":"excelHtml5",
                                "text":"Excel",
                                "className":"btn btn-success mx-1"
                            },
                            {
                                "extend":"pdfHtml5",
                                "text":"PDF",
                                "className":"btn btn-danger mx-1"
                            },
                        ]
                    });
                });
            },
            async visualizarPrecios(){
                await axios.get('listado-precios-droguerias').then(respuesta=>{
                    this.listasPrecios = respuesta.data
                   
                    this.tabla()
                });
            },
            

            async guardarPedidoDetallado(){                
                
                 await axios.post("guardar-pedido-detallado",this.datosPedidos);
                 this.cerrarModal();
                
            },

            async editarPedidos(id){
               let resultado = await axios.get("ApiListarEditarPedidos/"+id);
               this.listaEditarPedidos = resultado.data 
               console.log(this.listaEditarPedidos);
               
            },

            abrirModal(nombreProducto,drogueria,costo,visualizadorPrecioId){
                
                this.datosPedidos.cantidad=[];                
                this.datosPedidos.producto='';
                this.datosPedidos.drogueria = '';
                this.datosPedidos.costo = '';
                this.datosPedidos.visualizadorPrecioId = '';                 
                this.datosPedidos.producto = nombreProducto;              
                this.datosPedidos.drogueria = drogueria;
                this.datosPedidos.costo = costo;
                this.datosPedidos.visualizadorPrecioId = visualizadorPrecioId; 
                $('#agregarPedido').modal('show')
                
            },
            cerrarModal(){
                
                this.listaEmpresas=[];
                /* $('#'+this.datosProducto.keycodigo).modal('hide') */
                $('#agregarPedido').modal('hide')
            },
            
            abrirModalEditar(keycodigo){
               let resul = this.editarPedidos(keycodigo)
               
                $('#editarPedido').modal('show')
            },

            cerrarModalEditar(){
                $('#editarPedido').modal('hide')
            }
        }
    }
</script>
