<template>
    <div>
        <form class="text-left" >
	    	
	        <div class="form-row">
	            <div class="form-group col-6">
	            	<div class="row">
                        <div class="col">
                            <label for="fecha" class="font-weight-bolder">Fecha a buscar</label>
                            <input type="date"  class="form-control" v-model="datosForm.fecha" @change="listarAsesores()" id="fecha" name="fecha">
                        </div>
                        <div class="col">
                            <label for="">Asesores</label>
                            <select name="" class="form-control js-example-basic-single" v-model="datosForm.asesor" id="" multiple style="height: 200px;">
                                <option v-for="(datos,i) in asesores" :key="i" v-bind:value="datos.codusua">
                                    {{ datos.usuario }}
                                </option>
                            </select>
                            
                        </div>
                        
                    </div>	     	
	            </div>
                <div class="col">
                    <button type="button" class="btn btn-primary float-left" @click="buscarMovPago()">Buscar</button>
                </div>

			</div>
			
		</form>
		
		<table class="table" id="recaudos">
			<thead>
                <tr>
                    <th>Fecha</th>
                    <th>codusua</th>
                    <th>Usuario</th>
                    <th>Divisas</th>
                    <th>Tasa</th>
                    <th>Bolivares</th>
                    <th>Cod Arqueo</th>
                </tr>
				
			</thead>
			<tbody>
                <tr v-for="(asesor,v) in listadoMovpago" :key="v">
                    <td>{{ asesor.FECHA }}</td><td>{{ asesor.codusua }}</td><td>{{ asesor.usuario }}</td><td>{{ asesor.DOLARES }}</td><td>{{ asesor.tasa }}</td><td>{{ asesor.Bolivares }}</td><td>{{ asesor.codarq }}</td>
                </tr>
				
			</tbody>
            <thead v-show="listadoMovpago.length > 0">
                <tr>
                    <td class="text-success"><h4>Total $</h4></td>
                    <td>{{ totalDolaresStr }}</td>
                    <td>Monto Recibido <input type="text" v-model="dolaresRecibidos"></td>
                    <td>
                         <b v-bind:class="{ 'text-danger': parseFloat(diferenciaUsd) < 0, 'text-success': parseFloat(diferenciaUsd) >= 0  }">Diferencia: {{ diferenciaUsd }}</b>
                    </td>
                </tr>
                
            </thead>
			
		</table>
		
    </div>
</template>

<script>
    import axios from 'axios';
    import DataTable from 'datatables.net-bs4';
    import jszip from 'jszip';
    import 'datatables.net-buttons-bs4';
    import 'datatables.net-buttons/js/buttons.colVis.mjs';
    import 'datatables.net-buttons/js/buttons.html5.mjs';
    import 'datatables.net-buttons/js/buttons.print.mjs';

    window.JSZip = jszip

    export default{

        mounted(){        
            this.tabla();
            this.$refs.miTabla = this.$el.querySelector('#recaudos');
        },

        data(){
            return {
              
               listadoMovpago:[],
               asesores:[],
               datosForm:{
                    fecha:'',
                    asesor:[],
                },
                totalDolares:0,
                totalDolaresStr:0,                
                dolaresRecibidos:0,                                
                diferenciaUsd:0,
                
            }
        },
        watch: {
            listadoMovpago: {
                handler(newVal, oldVal) {
                    this.totalDolares = 0;
                    newVal.forEach((item) => {
                       
                        this.totalDolares += parseFloat(item.DOLARES);
                        
                    });
                    
                    this.totalDolaresStr = this.totalDolares.toLocaleString('en-US', { style: 'currency', currency: 'USD' });
                    
                },
                deep: true
            },
            dolaresRecibidos:{
                handler(){
                    if (this.listadoMovpago.length > 0 || this.dolaresRecibidos > 0) {
                        
                        this.diferenciaUsd =  (this.dolaresRecibidos - this.totalDolares);
                    }
                }           
            },           
        },
        methods:{
            tabla() {
                this.$nextTick(() => {
                if (this.listadoMovpago.length > 0) { // Verifica que haya datos en la tabla
                    $('#recaudos').DataTable({
                    dom: 'Bfrtip',
                    
                    buttons: [
                        {
                        extend: 'copyHtml5',
                        text: 'Copiar',
                        className: 'btn btn-secondary mx-1'
                        },
                        {
                        extend: 'excelHtml5',
                        text: 'Excel',
                        className: 'btn btn-success mx-1'
                        }
                    ]
                    });
                }
                });
            },
            

            async listarAsesores(){
                let fecha = this.datosForm.fecha;
                let resultado = await axios.get("/vealo3/public/divisas/listado-asesores/"+fecha);
                this.asesores = resultado.data;
                
               
            },

            
            async buscarMovPago(){
                this.listadoMovpago = [];
                if(this.datosForm.asesor !='' &&  this.datosForm.fecha !=''){
                    let resultado = await axios.post("/vealo3/public/divisas/reporte-recaudo-movpago",this.datosForm);
                    // Manejar la respuesta del controlador aquí
                    /* .then(response => {
                        console.log(response.data);
                    }); */
                    this.listadoMovpago = resultado.data;
                    
                    
                }
                
            },
        },    
    }
</script>