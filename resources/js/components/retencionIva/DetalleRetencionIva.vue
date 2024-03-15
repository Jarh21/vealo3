<template>
    <div>
        <!-- Modal Editar cantidades-->
        <div class="modal fade" id="editarPedido" data-backdrop="static" data-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <form>
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="staticBackdropLabel">Editar Factura comprobante:{{ objFactura.comprobante }}</h5>
                        <button type="button" class="close" @click="cerrarModalEditar()">
                        <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                       
                            <div class="row border">
                            
                                <div class="col  mb-2">
                                    <label for="">Fecha Documento</label>
                                    <input type="hidden" v-model="objFactura.keycodigo">
                                    <input type="date" id='fecha_docu' v-model='objFactura.fecha_docu' class="form-control">
                                    <label for="nfactura" id="label_factura_afectada">Documento</label>
                                    <input type="text" required v-model="objFactura.nfactura" class="form-control">                           
                                   <label for="">Factura Afectada</label>
                                   <input type="text" v-model="objFactura.fact_afectada" class="form-control">
                                    
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
                                    <input type="text" v-model="objFactura.comprasmasiva" ref="comprasmasiva" class="form-control">
                                    <label for="">Excento</label>
                                    <input type="text" v-model="objFactura.sincredito" ref="sincredito" class="form-control">
                                    <label for="">% Alicuota</label>
                                    <input type="text" v-model="objFactura.porc_alic" ref="porc_alic" class="form-control" value="">
                                    <label for="">% Retencion</label>                              
                                    <select class="form-control" v-model="objFactura.porc_reten">
                                        <option value="">-- selecciones % --</option>
                                        <option v-for="(porcentaje, i) in porcentajes" :key="i" :value= "porcentaje.porcentaje" :selected ="objFactura.porc_reten === porcentaje.porcentaje">{{porcentaje.porcentaje}}</option>
                                    </select>

                                </div>
                                <div class="col">
                                    <label for="">Base Imponible</label>
                                    <input type="text" v-model='objFactura.base_impon' id='base_impon' class="form-control" readonly>
                                    <label for="">Impuesto Iva</label>
                                    <input type="text" v-model='objFactura.iva' id='iva' class="form-control" readonly>
                                    <label for="">IVA Retenido</label>
                                    <input type="text" v-model='objFactura.iva_retenido' id='iva_retenido' class="form-control" readonly>     
                                    <label for="">Tipo de Operacion</label>
						            <label for="compra">Compra</label><input type="radio" v-model="objFactura.compra_venta"  id="compra" value="C" :checked="objFactura.compra_venta === 'C'">
						            <label for="venta">Venta</label><input type="radio"  v-model="objFactura.compra_venta"  id="venta" value="V" :checked="objFactura.compra_venta === 'V'">                               
                                    <button type="button" id='calcular' name='calcular' @click="calcularMontos();" class="btn btn-sm btn-warning float-right mt-2">Calcular</button> 
                                </div>
                                
                            </div>
                            
                            
                            
                            
                        
                    </div>
                    <div class="modal-footer">
                        <!-- <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button> -->
                        <button type="button" class="btn btn-primary" @click="updateFactura()">Guardar</button>
                    </div>
                </div>
                </form>
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
                    <td>{{ factura.iva_retenido }} al {{ factura.porc_reten}}%</td>
                    <td >
                        
                        <a class="btn btn-primary btn-sm" @click="abrirModalEditar(factura)">Editar Fact</a>
                    </td>
                    
               </tr> 
            </tbody>
            
        </table>
        <div class="row">
            <div class="col">
                <h4 class="">Total Retenido : {{total_retenido}}</h4> 
            </div>
        </div>
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
            this.consultarRetencion();
            this.consultarPorcentajeRetencionIva();          
        },
    data(){
        return{
            facturas:[],
            porcentajes:[],
            objFactura:{
                keycodigo:'',
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
                fact_afectada:'',
                compra_venta:'',
            },
            datosProveedor:0,
            total_retenido :0,
            
        }
    },
    methods:{
        async listarFacturas(){
            let nuemro = this.comprobante;
            let resultado = await axios.get("./listar-detalle-retencion/"+nuemro);
            this.facturas = resultado.data
            
            
            for (const valores of this.facturas) {
                await this.buscarProveedor(valores.rif_retenido);
                if(valores.porc_reten == this.datosProveedor){
                    console.log("no hay cambio en los porcentajes de retencion del proveedor con las facturas");
                }else{
                    alert("¡¡¡Error el porcentaje de retencion del proveedor es distinto al de las facturas, modifiquelos y recalcule los montos en editar!!!")
                }
                
            }
            
        },
        async consultarRetencion(){
            let comprobante = this.comprobante;
            let resultado = await axios.get("consultar-retencion/"+comprobante);
            this.total_retenido = resultado.data.total;
        },
        async consultarPorcentajeRetencionIva(){
            let resultado = await axios.get('../../admin/configuracion/lista-porce-retencionIva')
            
            this.porcentajes = resultado.data;
        },
        async buscarProveedor(rif){
            this.datosProveedor =0;
            let resultado = await axios.get('../../proveedor/buscar/'+rif)
            this.datosProveedor = resultado.data.porcentaje_retener;
            
        },

        tabla(){ //asi se llama datatabes en vue
            this.$nextTick(()=>{
                $('#facturas').DataTable({
                    dom: 'Bfrtip',
                    
                });
            });
        },
        abrirModalEditar(datos){
            this.objFactura.keycodigo='';
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
            this.objFactura.fact_afectada='';
            this.porce_proveedor='';
            this.objFactura.compra_venta='';

            this.objFactura.keycodigo= datos.keycodigo;
            this.objFactura.comprobante=datos.comprobante;
            this.objFactura.fecha_docu=datos.fecha_docu;
            this.objFactura.nfactura=datos.documento;
            this.objFactura.fact_afectada=datos.fact_afectada;
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
            this.porce_proveedor = datos.porc_reten;
            this.objFactura.compra_venta = datos.estatus;
            $('#editarPedido').modal('show')
        },
        cerrarModalEditar(){
            $('#editarPedido').modal('hide');
            this.listarFacturas();
            this.consultarRetencion();
        },
        calcularMontos(){
            /*validamos que le monto total no este vacio*/
			if(this.objFactura.comprasmasiva == ''){
				alert("Debe ingresar el monto Total de la Factura");
				this.$refs.comprasmasiva.focus();
				return;
			}
			/*validamos que iva alicuota no este vacio*/
			if(this.objFactura.porc_alic == ''){
				alert("Debe ingresar el valor del IVA, alicuota, no de be ser vacío");
				this.$refs.porc_alic.focus();
				return;
			}
			
			/*validamos que iva alicuota no este vacio*/
			if(this.objFactura.sincredito == ''){
				alert("Debe ingresar el valor del monto exento de lo contrario colocar 0");
				this.$refs.sincredito.focus();
				return;
			}
			/*validamos que le monto porcentaje re retencion 100 o 75 no este vacio*/
			if(this.objFactura.porc_reten == ''){
				alert("Debe ingresar el Porcentaje de retención del Impuesto del proveedor 100 o 75, si selecciona el proveedor este dato se cargara automaticamente");
				this.$refs.porc_reten.focus();
				return;
			}
					

			let TC=parseFloat(this.objFactura.comprasmasiva);	
			let CSC=parseFloat(this.objFactura.sincredito);
			let ALI=parseFloat(this.objFactura.porc_alic);
			let RET=parseFloat(this.objFactura.porc_reten);

			/* //validamos que si la factura no tiene excento se coloque en 0 para el calculo /
			if(document.getElementById('sincredito').value == ''){				
				CSC = 0;
			}else{				
				CSC=parseFloat(document.getElementById('sincredito').value);
			} */

			//Validamos que el monto excento no sea mayor que el monto total de la factura /	
			if(CSC >= TC) {
				alert("El Monto Excento"+CSC+" no debe ser mayor o igual al Total de la Compra !"+TC);				
				return;
			}

			let Resultado=((TC-CSC)/(1+(ALI/100)));
			let IVA = (Resultado*(ALI/100)).toFixed(2);	
			this.objFactura.base_impon = (Resultado).toFixed(2);
			this.objFactura.iva = IVA;
			this.objFactura.iva_retenido = ((IVA*RET)/100).toFixed(2); 
        },
       async updateFactura(){
            await axios.post("update-detalle-retencion",this.objFactura);
            alert("¡Registro de factura actualizado con exito!");
            this.cerrarModalEditar();
        }
    },
  };
  </script>
