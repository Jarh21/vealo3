@extends('layouts.app')

@section('content')

<div class="container">
    <div>
    <h3>Lista Habladores {{$nombreDeLaLista}}</h3>
    <a href="{{route('habladores.index')}}" class="float-end float-right"><< Regresar</a>
    </div>
    
    Para Agregar mas productos a esta lista haga click<a href="{{route('habladores.editarLista',$nombreDeLaLista)}}" class=""> Aquí </a>
    <div>
        @if(Session::has('message'))
			<div class="alert alert-danger">
				{!! Session::get('message') !!}
			</div>
   			
		@endif
        <form action="{{route('imprimirHabladores')}}" method="post" target="_blank" id="formhabladores" name="formhabladores">
            @csrf
            <input type="hidden" name="nombre_lista" value="{{$nombreDeLaLista}}">
        <table class="table table-striped" id="lista">
            <thead>
                <tr>
                    <th>Codprod</th>
                    <th>Nombre</th>                    
                    <th>Precio</th>
                    <th>Divisa</th>
                    <th name="todos" id="todos"><input type="checkbox" id="checkTodosHablador"> Selec todos</th>
                    <th>Accion</th>
                </tr>
            </thead>
            <tbody>
                @foreach($habladores as $hablador)
                @if(isset($hablador['codprod']))
                @php $idcheck = 'habladores'.$hablador['codprod']; @endphp
                <tr class="" onclick="selectCelda('{{$idcheck}}');">
                    <td>{{$hablador['codprod']}}</td>
                    <td>{{$hablador['nombre']}}</td>                    
                    <td>{{number_format($hablador['precio'],2,',','.')}}-{{$hablador['tipoIva']}}</td>
                    <td>{{number_format($hablador['divisa'],2,',','.')}} {{$hablador['abreviaturaMonedaSecundaria']}}</td>
                    <td><input type="checkbox" id="habladores{{$hablador['codprod']}}" class="productos" name="habladores[]" value=">{{$hablador['codprod']}}|{{$hablador['nombre']}}|{{$hablador['precio']}}|{{$hablador['divisa']}}"></td>
                    @php $id = $hablador['id']; @endphp
                    <td><a class="btn btn-danger btn-sm text-white" onclick="Eliminar('{{$nombreDeLaLista}}','{{$id}}');">Eliminar</a></td>
                </tr>
                @endif
                @endforeach
            </tbody>
            <tfooter>
                <div class="">
                    
                        <label for="">Moneda a imprimir</label>
                        <select name="moneda_base" id="" class="">
                            @foreach($tipoMonedas as $moneda)
                            <option value="{{$moneda->is_moneda_base}}">{{$moneda->nombre_singular}}</option>
                            @endforeach
                        </select>
                        <label for="igtf" disabled>IGTF</label>
                        <!-- <input type="checkbox" name="igtf" id="igtf" checked > -->
                        <select name="igtf" id="igtf">
                            <option value="">--</option>
                            <option value="on">Si</option>
                            <option value="off">No</option>
                        </select>
                    
                        <label for="" class="ml-3">Tamaño</label>
                        
                        <input type="radio" name="tamanio" id="pequenio" value="s"><label for="pequenio">S</label>
                        <input type="radio" name="tamanio" id="mediano" value="m" checked><label for="mediano" class="h5">M</label>
                        <input type="radio" name="tamanio" id="grande" value="xl"><label for="grande" class="h4">XL</label>
                        <button type="submit" class="btn btn-primary btn-sm">Imprimir</button>
                    
                    
                </div>
                

            </tfooter>
        </table>

        </form>
    </div>
</div>

@endsection
@section('js')

    <script type="text/javascript">
            function Eliminar(lista,id){
            let eliminar = confirm("¿Desea eliminar el producto seleccionado?");
            if(eliminar){
                window.location="{{url('informes/eliminar-producto-listado-hablador')}}/"+lista+"/"+id; 
               
            }
        
        }
    </script>
    <script type="text/javascript">
		$('document').ready(function(){
  			
  			$("#checkTodosHablador").change(function () {
      			 $("form#formhabladores input:checkbox").prop('checked', $(this).prop("checked")); 
                
                  
  			});
		});		
	</script>
    <script type="text/javascript">

        $(document).ready(function() {			
            

                $('#lista').DataTable({
                    language: {
                    "decimal": "",
                    "emptyTable": "No hay información",
                    },
                scrollY: 400,
                select: true,
                paging: false,
                searching: true,
                ordering:  true,
                columnDefs:[{
                        "targets": [4,5],
                        "orderable": false
                    }],
                });	
            

        } );
    </script>

    <script type="text/javascript">
        function selectCelda(id){
            let checkbox = document.getElementById(id);
            if(checkbox.checked == true){
                checkbox.checked = false;

            }else{
                checkbox.checked =true;
            }
            
        }
    </script>    
@endsection
