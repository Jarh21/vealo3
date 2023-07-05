<template>
    <div>
        <!-- The Modal -->
        <div class="modal" :class="{mostrar:modal}">
            <div class="modal-dialog">
                <!-- Modal Header -->
                 

                <div class="modal-content">
                
                    <form >
                        <div class="modal-header">
                            <h4 class="modal-title">Seleccione Empresa</h4>
                            <button type="button" class="btn-close" @click="cerrarModal()"></button>
                        </div>
                        <!-- Modal body -->
                        <div class="modal-body">
                        <ul>
                            <li v-for="(empresa,index) in listaEmpresas" :key="index" ><b @click="cambioDeEmpresa(empresa.rif)"> {{ empresa.nombre +' ' +empresa.rif}}</b></li>
                        </ul>
                                
                            
                        </div>

                        <!-- Modal footer -->
                        <div class="modal-footer">
                            <button type="button" class="btn btn-danger" data-bs-dismiss="modal" @click="cerrarModal">Close</button>
                            <button type="submit" class="btn btn-primary" data-bs-dismiss="modal" >Guardar</button>
                        </div>
                    </form>
                </div>
            </div>
        </div> <!-- fin modal -->

        <span class="btn btn-outline-primary btn-sm" @click="abrirModal();">{{ empresaSeleccionada.empresaNombre }}</span>
        
    </div>    
</template>
<script>
//import axios from 'axios';

    export default {
        data(){
            return{
                
                modal:0,
                empresaSeleccionada:'',
                listaEmpresas:[],
            }
        },
        methods:{

          async obtenerEmpresaSeleccionada(){
            const respuesta = await axios.get('http://localhost/my-app/public/obtener-empresa-seleccionada');
            this.empresaSeleccionada = respuesta.data;
          },
          
          async listarEmpresas(){
            const respuesta = await axios.get('admin/empresa/listar/api');
            this.listaEmpresas = respuesta.data;
          },

          async cambioDeEmpresa(rif){
            await axios.get('admin/empresa/cambair-empresa/'+rif);
            
             this.obtenerEmpresaSeleccionada(); 
             this.cerrarModal();
          },
          
          abrirModal(data={}){
            this.modal=1;            
            
          },

          cerrarModal(){
            this.modal=0;
          },
          
          
        },

        created(){
            this.obtenerEmpresaSeleccionada();
            this.listarEmpresas();
        },
     
    }

</script>
<style>
.mostrar{
    display: list-item;
    opacity: 1;
    background-color: rgba(27, 27, 23, 0.699);
}
</style>