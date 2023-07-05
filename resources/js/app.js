/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

require('./bootstrap');

window.Vue = require('vue').default;

/**
 * The following block of code may be used to automatically register your
 * Vue components. It will recursively scan this directory for the Vue
 * components and automatically register them with their "basename".
 *
 * Eg. ./components/ExampleComponent.vue -> <example-component></example-component>
 */

// const files = require.context('./', true, /\.vue$/i)
// files.keys().map(key => Vue.component(key.split('/').pop().split('.')[0], files(key).default))

Vue.component('example-component', require('./components/ExampleComponent.vue').default);
Vue.component('roles-permisos', require('./components/roles/ListarRoles.vue').default);
Vue.component('permisos-listar',require('./components/roles/ListarPermisos.vue').default); 
Vue.component('cambio-empresa',require('./components/CambioEmpresa.vue').default);
Vue.component('observacion-cuadre-efectivo',require('./components/cuadres/CuadresObservacionEfectivo.vue').default); 
Vue.component('observacion-cuadre-general',require('./components/cuadres/CuadresObservacionGeneral.vue').default);
Vue.component('observacion-cuadre-otra',require('./components/cuadres/CuadresObservacionOtra.vue').default);
Vue.component('observacion-cuadre-tarjeta',require('./components/cuadres/CuadresObservacionTarjeta.vue').default); 
Vue.component('cuadre-cierre-de-lotes',require('./components/cuadres/CuadresRegistroPuntoDeVenta.vue').default);
Vue.component('cuadre-registrar-transferencias',require('./components/cuadres/CuadresTransferencia.vue').default);
Vue.component('cuadre-prestamos-efectivo',require('./components/cuadres/CuadresPrestamosEfectivo.vue').default);
/**
 * Next, we will create a fresh Vue application instance and attach it to
 * the page. Then, you may begin adding components to this application
 * or customize the JavaScript scaffolding to fit your unique needs.
 */

const app = new Vue({
    el: '#app',
});


