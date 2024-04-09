<template>
    <div>
        <!-- Modal ingresar Cantidades-->
        
            <div class="">
                <div class="">
                    
                    <h3>Ultima Tasa Registrada: {{ tasaDelDia.tasa_segunda_actualizacion}}</h3>
                    <form action="" method="post">
                <div class="row mb-3">
                    <div class="col">
                        <label for="fecha">Fecha</label>
                        <input type="date" class="form-control" v-model="datosForm.fecha" id="fecha" required>
                    </div>
                    <div class="col">
                        <label for="">Tasa</label>
                        <input type="text" class="form-control" v-model="datosForm.tasa" required >
                        <a class="btn btn-primary float-right mt-2 text-white"  @click="guardarTasa()"><i class="fas fa-save "></i> Guardar </a>
                    </div>
                    
                </div>
                       
                        
                        
                    </form>
                    
                </div>
                
                <div class="">
                    <p v-if="cargando">Cargando...</p>
                    <table id="cotizaciones" v-if="!cargando" class="table">
                        <thead>
                            <tr>
                                <th>Fecha</th>
                                <th>Tasa</th>
                            </tr>
                            
                            
                        </thead>
                        <tbody>
                        
                            <tr v-for="(tasa,a) in listadoTasas" :key="a" @click="seleccionar(tasa.fecha,tasa.tasa_segunda_actualizacion)">
                                <td><span style="cursor: pointer" alert="click para editar">{{ tasa.fecha }}</span></td>
                                <td><span style="cursor: pointer" alert ="click para editar">{{ tasa.tasa_segunda_actualizacion }}</span></td>
                                
                            </tr>
                        </tbody>
                    </table>
                </div>
                
            </div>
               
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
            this.ListarTodasLasTasas();
            this.consultarUltimaTasa();
            
        },

        data(){
            return {
                cargando: false, // Inicialmente se muestra el indicador de carga
               tasaDelDia:'',
               listadoTasas:[],
               datosForm:{
                    fecha:'',
                    tasa:'',
                },
                dataTableInitialized:false,
            }
        },
        methods:{
            tabla(){ //asi se llama datatabes en vue
                if (!this.dataTableInitialized) {
                    this.$nextTick(()=>{
                    $('#cotizaciones').DataTable({
                        dom: 'Bfrtip',
                        ordering: false,
                        scrollY: 300,
                        paging: false,
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
                            
                        ]
                    });
                });
                this.dataTableInitialized = true;
            }
                
            },
            abrirModal(){
        
                $('#exampleModal').modal('show')
               
            },
            cerrarModal(){                
                
                $('#exampleModal').modal('hide')
            },

            seleccionar(fecha,tasa){
                this.datosForm.fecha = '';
                this.datosForm.tasa = '';
                this.datosForm.fecha = fecha;
                this.datosForm.tasa = tasa;
            },

            async consultarUltimaTasa(){
                let resultado = await axios.get("/vealo3/public/herraminetas/valorTasaActual");
                this.tasaDelDia = resultado.data
                this.datosForm.fecha = '';
                this.datosForm.tasa = '';
            },

            async ListarTodasLasTasas(){
                let resultado = await axios.get("/vealo3/public/herraminetas/listarTodasLasTasas");
                    this.listadoTasas = resultado.data
                    
                    this.tabla()
                   
                
            },

            async guardarTasa(){
                if(this.datosForm.fecha !='' &&  this.datosForm.tasa !=''){
                    
                    await axios.post("/vealo3/public/herraminetas/guardarTasa",this.datosForm);
                    this.consultarUltimaTasa();
                    this.ListarTodasLasTasas();
                    
                }
                
            },
        },
      /*   updated(){
            this.tabla();
        } */
                    
            
        
    }
</script>