<template>
    <div>
        <!-- Modal ingresar Cantidades-->
        <div class="modal-dialog modal-dialog-scrollable ">
            <div class="modal-content">
                <div class="modal-header">
                    
                    <p>Tasa actual: {{ tasaDelDia.tasa_segunda_actualizacion}}</p>
                    <form action="" method="post">

                        <label for="fecha">Fecha</label>
                        <input type="date" v-model="datosForm.fecha" id="fecha" required>
                        <label for="">Tasa</label>
                        <input type="text" v-model="datosForm.tasa" required style="width: 80px;">
                        <a @click="guardarTasa()"><i class="fas fa-save text-primary"></i></a>
                    </form>
                    
                </div>
                <div class="modal-body">
                    <a class="btn btn-info btn-sm" @click="ListarTodasLasTasas()">Listar todas las tasas</a>
                    <table id="idTabla" class="table">
                        <thead>
                            <th>Fecha</th>
                            <th>Tasa</th>
                            
                        </thead>
                        <tbody>
                        
                            <tr v-for="(tasa,a) in listadoTasas" :key="a" @click="seleccionar(tasa.fecha,tasa.tasa_segunda_actualizacion)">
                                <td>{{ tasa.fecha }}</td>
                                <td>{{ tasa.tasa_segunda_actualizacion }}</td>
                                
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                    
                </div>
            </div>
        </div>        
    </div>
    
</template>

<script>
import axios from 'axios';


import 'datatables.net-bs4'; // Si estás utilizando Bootstrap 4
    export default{
        mounted(){
            
            this.consultarUltimaTasa();
            
        },

        data(){
            return {
               tasaDelDia:'',
               listadoTasas:[],
               datosForm:{
                    fecha:'',
                    tasa:'',
                },
                
            }
        },
        methods:{
            tabla(){ //asi se llama datatabes en vue
                /* $('#exampleModal').on('shown.bs.modal', () => {
                    this.$nextTick(() => {
                        $('#tasas').DataTable({
                        // Configuración del DataTable
                        });
                    });
                }); */
                this.$nextTick(()=>{
                     if (!$.fn.DataTable.isDataTable('#idTabla')) { 
                        $('#idTabla').DataTable({
                        // Configuración del DataTable
                        });
                     } 
                });
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
                    /*this.tabla()*/
                
                
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