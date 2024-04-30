<template>
    <div class=" d-inline">
        <!-- Modal Editar cantidades-->
        <div class="modal fade" :id="`editarPedido-${datos.comprobante}`" data-backdrop="static" data-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-scrollable">
                <form enctype="multipart/form-data">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="staticBackdropLabel">Envio de Correo</h5>
                        <button type="button" class="close" @click="cerrarModal()">
                        <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">                       
                        
                        
                        <div class="row">
                            <div class="col">
                                <label>Archivos seleccionados:</label>
                                <ul class="list-group">
                                    <li class="list-group-item"><a href="#" @click.prevent="showPreview(file)"><b>Retencion IVA </b>del comprobante <b>{{ this.datos.comprobante }}.pdf</b></a></li>
                                    <div v-if="formulario.archivo.length > 0">
                                        <li class="list-group-item" v-for="(file, index) in formulario.archivo" :key="index">
                                            <a href="#" @click.prevent="showPreview(file)">{{ file.name }}</a>
                                        <button @click.prevent="removerArchivo(index)" class="close"><span aria-hidden="true">&times;</span></button>
                                        </li>
                                    </div>
                                </ul>
                                <input type="file" @change="handleFileChange" ref="fileInput" multiple class="form-control-file">
                            </div>
                            <div class="col">
                                <label>Vista Previa:</label>
                                <div v-if="formulario.archivo.length > 0">
                                    <img width="400px" :src="previewImage" alt="Vista previa del archivo" v-if="previewImage">
                                </div>
                            </div>
                        </div>                        
                        
                        
                        
                    </div>
                    <div class="modal-footer">
                        <!-- <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button> -->
                        <button type="button" class="btn btn-primary" @click="enviarCorreoPost()">Enviar</button>
                    </div>
                </div>
                </form>
            </div>
        </div>
        <!--fin modal Editar Cantidades-->
        <button class="btn btn-success btn-sm" @click="abrirModal()" :disabled="cargando">
            <i class="fas fa-paperclip"></i>
            Correo 
            <i :class="{'fas fa-spinner': cargando, 'fas fa-check-circle text-warning': enviado}"></i>
            <i class='fas fa-check-circle text-warning' title="Correo enviado" v-if="this.datos.correo_enviado == 1"></i>
            
        </button> 
            
    </div>
  </template>
  
  <script>

  export default {
    props: {
        datos: {
            type: Object,
            required: true
        }
    },
    mounted(){
       
            
    },
 
    data(){
        return{
            cargando: false,
            enviado: false,
            tieneCorreo :false,
            formulario:{
                comprobante:'',
                rifAgente:'',
                archivo:[],
            },
            previewImage: null,
        }
    },
    methods:{
        showPreview(file) {
            // Crea una URL de objeto para la imagen
            this.previewImage = URL.createObjectURL(file);
        },
        
        async enviarCorreoRetencion(){
            let comprobante = this.datos.comprobante;
            let rifAgente = this.datos.rifAgente;
            this.cargando = true; 
            let resultado = await axios.get("evio-email-retencion-iva/"+comprobante+"/"+rifAgente);           
            this.cargando = false;
            this.enviado = true; // Establecer la variable 'enviado' como verdadera
           
            
        },
        abrirModal(){
            this.formulario.comprobante = this.datos.comprobante;
            this.formulario.rifAgente = this.datos.rifAgente;
            $(`#editarPedido-${this.datos.comprobante}`).modal('show');
                                    
            
        },
        cerrarModal(){
            $(`#editarPedido-${this.datos.comprobante}`).modal('hide');
            
        },
        async enviarCorreoPost(){
            const formData = new FormData();
         
            formData.append('comprobante', this.formulario.comprobante);
            formData.append('rifAgente', this.formulario.rifAgente);
            this.formulario.archivo.forEach(file => {
                formData.append('archivo[]', file);
            });
            //formData.append('archivo[]', this.formulario.archivo);
            this.cerrarModal();
            this.cargando = true;
            let respuesta = await axios.post("evio-email-retencion-iva-post", formData);
            console.log(respuesta);
            this.cargando = false;
            this.enviado = true;
            
        },
        removerArchivo(index) {
            this.formulario.archivo.splice(index, 1);
            this.$refs.fileInput.value = "";
        },
        
        handleFileChange(event) {
            //este metodo se usa para utilizar el input tipo file
            
            this.formulario.archivo.push(...event.target.files);
            // Puedes hacer algo con el archivo aquí, como subirlo al servidor
            
        },
        
    },
  };
  </script>
