<template>
    <div>
        <!-- Modal Editar cantidades-->
        <div class="modal fade" id="editarPedido" data-backdrop="static" data-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="staticBackdropLabel">Editar Factura comprobante:{{ objFactura.comprobante }}</h5>
                        <button type="button" class="close" @click="cerrarModalEditar()">
                        <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form name="factura_manual" id="factura_manual" action="#" method="post">
                            <div class="row border">
                            
                                <div class="col  mb-2">
                                    <label for="">Fecha Documento</label>
                                    <input type="hidden" v-model="objFactura.comprobante">
                                    <input type="date" id='fecha_docu' v-model='objFactura.fecha_docu' class="form-control">
                                    <label for="nfactura" id="label_factura_afectada">Documento</label>
                                    <input type="text" required v-model="objFactura.nfactura" class="form-control">                           
                                   
                                    
                                </div>
                                <div class="col">
                                    <label for="">Serie</label>
                                    <input type="text" v-model='objFactura.serie' class="form-control">
                                    <label for="">Numero de Control</label>
                                    <input type="text" id='control_fact' v-model='objFactura.control_fact' class="form-control">
                                    <label for="">Tipo Transacción</label>
                                    <input type="text" id='tipo_trans' v-model='objFactura.tipo_trans' class="form-control">
                                </div>

                            </div>
                            <div class="row border" style="background-color:#F6F5F3">
                                <div class="col mb-2">
                                    <label for="">Total Compra + Iva</label>
                                    <input type="text" v-model="objFactura.comprasmasiva" id="comprasmasiva" class="form-control">
                                    <label for="">Excento</label>
                                    <input type="text" v-model="objFactura.sincredito" id="sincredito" class="form-control">
                                    <label for="">% Alicuota</label>
                                    <input type="text" v-model="objFactura.porc_alic" id="porc_alic" class="form-control" value="">
                                    <label for="">% Retencion</label>
                                    <input type="text" v-model="objFactura.porc_reten" id="porc_reten" class="form-control">

                                </div>
                                <div class="col">
                                    <label for="">Base Imponible</label>
                                    <input type="text" v-model='objFactura.base_impon' id='base_impon' class="form-control" readonly>
                                    <label for="">Impuesto Iva</label>
                                    <input type="text" v-model='objFactura.iva' id='iva' class="form-control" readonly>
                                    <label for="">IVA Retenido</label>
                                    <input type="text" v-model='objFactura.iva_retenido' id='iva_retenido' class="form-control" readonly>                                   
                                    <button type="button" id='calcular' name='calcular' class="btn btn-sm btn-warning float-right mt-2">Calcular</button> 
                                </div>
                                
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
      <p>Datos de Facturas</p>
        <table id="facturas" class="table">
            <thead>
                <tr>
                    <th>Tipo Docu</th>
                    <th>Nº Factura</th>
                    <th>Serie</th>
                    <th>Base Imponible</th>
                    <th>Impuesto Iva</th>                    
                    <th>Iva Retenido</th>
                    <th>Accion</th>
                </tr>
            </thead>
            <tbody>
               <tr v-for="factura in facturas">
                    <td>{{ factura.tipo_docu }}</td>
                    <td>{{ factura.documento }}</td>
                    <td>{{ factura.serie }}</td>
                    <td>{{ factura.base_impon }}</td>
                    <td>{{ factura.iva }}</td>
                    <td>{{ factura.iva_retenido }}</td>
                    <td >
                        
                        <a class="btn btn-warning btn-sm" @click="abrirModalEditar(factura)">Editar Fact</a>
                    </td>
                    
               </tr> 
            </tbody>
            
        </table>
    </div>
  </template>
  
  <script>
  export default {
    props: {
        comprobante: {
        type: Number,
        required: true
      }
    },
    mounted(){
            
            this.listarFacturas();          
        },
    data(){
        return{
            facturas:[],
            objFactura:{
                comprobante:'',
                fecha_docu:'',
                nfactura:'',
                serie:'',
                control_fact:'',
                tipo_trans:'',
                comprasmasiva:'',
                sincredito:'',
                porc_alic:'',
                porc_reten:'',
                base_impon:'',
                iva:'',
                iva_retenido:'',
            },
        }
    },
    methods:{
        async listarFacturas(){
            let nuemro = this.comprobante;
            let resultado = await axios.get("./listar-detalle-retencion/"+nuemro);
            this.facturas = resultado.data 
            
            
        },
        tabla(){ //asi se llama datatabes en vue
            this.$nextTick(()=>{
                $('#facturas').DataTable({
                    dom: 'Bfrtip',
                    
                });
            });
        },
        abrirModalEditar(datos){
            this.objFactura.comprobante='';
            this.objFactura.fecha_docu='';
            this.objFactura.nfactura='';
            this.objFactura.serie='';
            this.objFactura.control_fact='';
            this.objFactura.tipo_trans='';
            this.objFactura.comprasmasiva='';
            this.objFactura.sincredito='',
            this.objFactura.porc_alic='';
            this.objFactura.porc_reten='';
            this.objFactura.base_impon='';
            this.objFactura.iva='';
            this.objFactura.iva_retenido='';

            //this.objFactura.comprobante= datos.comprobante;
            this.objFactura.comprobante=datos.comprobante;
            this.objFactura.fecha_docu=datos.fecha_docu;
            this.objFactura.nfactura=datos.documento;
            this.objFactura.serie=datos.serie;
            this.objFactura.control_fact=datos.control_fact;
            this.objFactura.tipo_trans=datos.tipo_trans;
            this.objFactura.comprasmasiva=datos.comprasmasiva;
            this.objFactura.sincredito=datos.sincredito,
            this.objFactura.porc_alic=datos.porc_alic;
            this.objFactura.porc_reten=datos.porc_reten;
            this.objFactura.base_impon=datos.base_impon;
            this.objFactura.iva=datos.iva;
            this.objFactura.iva_retenido=datos.iva_retenido;
            $('#editarPedido').modal('show')
        },
        cerrarModalEditar(){
                $('#editarPedido').modal('hide')
            }
    },
  };
  </script>