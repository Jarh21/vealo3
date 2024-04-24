<template>
    <div class=" d-inline">
        <button class="btn btn-success btn-sm" @click="enviarCorreoRetencion()" :disabled="cargando">
            Correo 
            <i :class="{'fas fa-spinner': cargando, 'fas fa-check-circle text-warning': enviado}"></i>
            <i class='fas fa-check-circle text-warning' title="Correo enviado" v-if="this.datos.correo_enviado == 1"></i>
            <span v-if="enviado"> Enviado</span> <!-- Muestra "Enviado" si la variable 'enviado' es verdadera -->
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
 
    data(){
        return{
            cargando: false,
            enviado: false,
            tieneCorreo :false,
        }
    },
    methods:{
        
        async enviarCorreoRetencion(){
            let comprobante = this.datos.comprobante;
            let rifAgente = this.datos.rifAgente;
            this.cargando = true; 
            let resultado = await axios.get("evio-email-retencion-iva/"+comprobante+"/"+rifAgente);           
            this.cargando = false;
            this.enviado = true; // Establecer la variable 'enviado' como verdadera
           
            
        },
    },
  };
  </script>
