<template>
    <div>
        
        <table id="articulos" class="table">
            <thead>
                <tr>
                    <th>Nº</th>
                    <th>Descripción</th>
                    <th>Costo</th>
                    <th>Drogueria</th>                    
                    <th>&nbsp;</th>
                </tr>
            </thead>
            <tbody>
               <tr v-for="datos in listasPrecios">
                    <td>{{ datos.keycodigo }}</td>
                    <td>{{ datos.descripcion }}</td>
                    <td>{{ datos.costo }}</td>
                    <td>{{ datos.drogueria }}</td>
                    <td>
                       <RegistroPorFarmacia :datosProducto="datos"></RegistroPorFarmacia>
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
            
        },
        components:{
            RegistroPorFarmacia
        },
        data(){
            return {
                listasPrecios:[],
                
                datosPedidos: {
                    producto:'',
                    empresasRif:[],
                    cantidad:0
                },
            }
        },
        methods:{
            
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
                    $('#articulos').DataTable().destroy();
                    this.tabla()
                });
            },
            

            async guardarPedidoDetallado(){
                 await axios.post("guardar-pedido-detallado",this.datosPedidos);
                 console.log(this.datosPedidos);
                $('#agregarPedido').modal('hide')
            },


            
        }
    }
</script>