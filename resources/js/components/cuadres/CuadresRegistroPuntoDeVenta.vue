<template>
    <div>
        <!-- Modal -->
        <div class="modal" :class="{mostrar:modal}">
            <div class="modal-dialog">
                <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Observacion de Efectivo</h5>
                    <button type="button" class="btn-close" @click="cerrarModal()" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action="" method="post">
                    <div >
                        <label for="empleados">A que Banco Pertenece el Punto de Venta</label>
                        <select name="" id="" class="form-control">
                            <option value="" v-for="(banco,i) in listaBancos" :key="i">{{ banco.nombre }}</option>
                        </select>
                    </div>
                    <div>
                        <label for="">Indique el Numero de Terminal</label>
                       <input type="text" class="form-control">
                        
                    </div>
                    <div >
                        <label for="">Indique el Numero de Afiliación</label>
                        <input type="text" class="form-control">
                    </div>
                    <div>
                        <label for="">Es Prestado</label>
                        Si<input type="radio" name="" id="">No<input type="radio" name="" id="">
                    </div>                    
                    
                    
                    </form>
                    
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" @click="cerrarModal()">Cerrar</button>
                    <button type="button" class="btn btn-primary" @click="guardarPunto()">Guardar</button>
                </div>
                </div>
            </div>
        </div><!-- fin modal -->
        <div class="card">
            <div class="card-body">
                <label for="">Punto de Venta</label><a class="btn btn-secondary btn-sm  m-2" @click="abrirModal()">+ Agrgar Punto</a>
                <select name="" id="" class="form-control" v-model="formPunto.puntoSelec">
                    <option v-for="(punto,c) in puntos" :key="c">{{ punto.numero_de_afiliacion }}</option>
                </select>
                
            </div>
        </div>
        
    </div>
</template>
<script>
    export default {
        data(){
            return{
                formPunto:{
                   puntoSelec:'',
                },
                modal:0,                
                puntos:[],
                listaBancos:[],               
                
            }
        },
        methods:{
            
            async listarPuntos(){
                const respuesta = await axios.get("cuadres-listar-observaciones/Efectivo");
                this.puntos = respuesta.data;
            }, 
            async listadeBancos(){
                const res = await axios.get("banco-lista-bancos");
                this.listaBancos = res.data;
                
            },       
            
            async guardarPunto(){
                /* this.formObservaciones.tipo_observacion = this.tipoObservacion;
                const respuesta = await axios.post("cuadres-guardar-observacion",this.formObservaciones);
                this.cerrarModal();
                this.listarEfectivo(); */
            },
            abrirModal(tipoObservacion){
                this.modal=1;
                this.tipoObservacion=tipoObservacion;
            },
            cerrarModal(){
                this.modal=0;
            }
        },
        created(){
            this.listadeBancos();
            /*this.listarPuntos();*/
        }
        
    }
</script>
<style>
.mostrar{
    display: list-item;
    opacity: 1;
    background-color: rgba(27, 27, 23, 0.699);
}
</style>