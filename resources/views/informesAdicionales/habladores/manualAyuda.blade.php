@extends('layouts.app')
@section('content')
<div class="container">
    <h3>Manual para generar Habladores</h3>
    <p>A continuacion se muestran paso a paso el inicio del sistema al modulo de informes adicionales donde podran imprimir los habladores.</p>
    <p>El modulo funciona de la siguiente manera: se debe crear un listado con el nombre de los porductos que se les desea generar habladores, una vez generado el listado puede imprimirlo en moneda nacional o extranjera, a continuacion imagenes del sistema.</p>
    <img src="{{asset('imagen/habladores/1inicio_sistema.jpg')}}" alt="Inicio del sistema" width="1000" >
    <p>Esta es la ventana de inicio al sistema</p>
    <hr>
    <img src="{{asset('imagen/habladores/2ir_ modulo.jpg')}}" alt="Inicio del sistema" width="1000" >
    <p>Existen varios modulos del sistema pero solo deben ingresar al de informes adicionales ya que es el que contiene la creacion de los habladores.</p>
    <hr>
    <img src="{{asset('imagen/habladores/3seleccionar_haladores.jpg')}}" alt="Inicio del sistema" width="1000" >
    <hr>
    <img src="{{asset('imagen/habladores/4seleccione_sucursal.jpg')}}" alt="Inicio del sistema" width="1000" >
    <p>Si desea cambiar de sucursal debe hacer click en seleccione sucursal en la parte superior derecha</p>
    <hr>
    <img src="{{asset('imagen/habladores/5escojer sucursal.jpg')}}" alt="Inicio del sistema" width="1000" >
    <hr>
    <img src="{{asset('imagen/habladores/6Crear_nuevo_listado.jpg')}}" alt="Inicio del sistema" width="1000" >
    <hr>
    <img src="{{asset('imagen/habladores/7selecione_tipo_producto.jpg')}}" alt="Inicio del sistema" width="1000" >
    <hr>
    <img src="{{asset('imagen/habladores/8Seleccionar_productos.jpg')}}" alt="Inicio del sistema" width="1000" >
    <p>Nota: para seleccionar el producto solo con dar click sobre el nombre del producto</p>
    <hr>
    <img src="{{asset('imagen/habladores/9Guardamos_Listado.jpg')}}" alt="Inicio del sistema" width="1000" >
    <hr>
    <img src="{{asset('imagen/habladores/10Ver_eliminar_listado.jpg')}}" alt="Inicio del sistema" width="1000" >
    <hr>
    <img src="{{asset('imagen/habladores/11Imprimir_habladores.jpg')}}" alt="Inicio del sistema" width="1000" >
    <hr>
    <img src="{{asset('imagen/habladores/12Modificar_habladores.jpg')}}" alt="Inicio del sistema" width="1000" >
    <p>Nota: Si quiere que los habladores en divisas le agreguen el impuesto IGTF debe marcar esa casilla, si no quiere el IGTF en los precios desmaque dicha casilla</p>
    <hr>
    <img src="{{asset('imagen/habladores/13imprimir_mediado.jpg')}}" alt="Inicio del sistema" width="1000" >
    <p>Habladores tamaño Mediano</p>
    <hr>
    <img src="{{asset('imagen/habladores/14imprimir_pequenio.jpg')}}" alt="Inicio del sistema" width="1000" >
    <p>Habaldores tamaño Pequeño</p>
    <hr>

</div>
@endsection