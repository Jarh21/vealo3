<template>
    <div>
        <button class="btn btn-success btn-sm" @click="enviarCorreoRetencion()" :disabled="cargando">
            Correo 
            <i :class="{'fas fa-spinner': cargando, 'fas fa-check-circle text-warning': enviado}"></i>
            <i class='fas fa-paper-plane text-warning' title="Correo enviado" v-if="this.datos.correo_enviado == 1"></i>
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
           
            
        },
    },
  };
  </script>
