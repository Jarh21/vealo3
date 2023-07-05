@extends('layouts.informes')
@section('css')
<link rel="stylesheet" type="text/css" href="{{asset('css/select2.min.css')}}">
@endsection
@section('content')
<div class="content">
<h3>Definicion de los porcentajes segun el grupo del vendedor</h3>
</div>
<div class="content">
    <form action="{{route('guardarEmpleadosComisionEspecial')}}" method="post">
        @csrf
        <input type="hidden" name="id_parametro_edit" value="{{$parametroSeleccionado->id ?? ''}}">
        <div class="row">
            <div class="col-6">
                <div class="input-group mb-3">
                    <span class="mr-2">Tipos de Vendedores</span>           
                   
                    <select name="grupo_usuario" id="grupo_usuario" class="form-control" required>
                        <option value="">Seleccione el grupo</option>
                        @foreach($grupos as $grupo)
                        <option value="{{$grupo->keycodigo}}" 
                            @if(isset($parametroSeleccionado->codgrupo))
                                @if($parametroSeleccionado->codgrupo==$grupo->keycodigo)
                                selected
                                @endif
                            @endif
                        >
                        {{$grupo->nombre}}
                        </option>
                        @endforeach
                    </select>
                </div>
            </div>
            
        </div>
        <div class="row">
            <div class="col">
                <div class="input-group mb-3">
                    <span class="mr-2" >Porcentaje Calculo Comision</span>
                    <input type="number" class="form-control" name="porcentaje_calculo_comision" id="porcentaje_calculo_comision" value="{{$parametroSeleccionado->porcentaje_calculo_comision ?? ''}}">
                </div>
            </div>
            <div class="col">
                <div class="input-group mb-3">
                    <span class="mr-2" title=" se aplica al total de facrturas cobradas, se descuenta este porcentaje del monto total y al restante es al que se calcula la comision del vendedor.">Porcentaje % de deduccion gastos operativos</span>
                    <input type="number" class="form-control" name="porcentaje_descuento_comision" id="porcentaje_descuento_comision" value="{{$parametroSeleccionado->porcentaje_descuento_comision ?? ''}}">
                </div>
            </div>
        </div>       
        
        <div class="input-group mb-3">
            <span class="mr-2" >Empleados especiales</span>
            <select name="vendedores_especiales_id[]" class="js-example-basic-single" style="width: 75%"; multiple="multiple" title="al no seleccionar ningun vendedor la configuracion se aplica a todos, de lo contrario, se aplica solo a los vendedores seleccionados">
                <option value=""></option>
                @if(isset($usuarios))
                    @if(isset($parametroSeleccionado->vendedores_especiales_id))
                        <?php 
                            $listaVendedores= explode(',',$parametroSeleccionado->vendedores_especiales_id);
                        ?>
                    @endif
                    @foreach($usuarios as $usuario)
                        <option value="{{$usuario->keycodigo}}"
                         @if(isset($parametroSeleccionado->vendedores_especiales_id))
                           @foreach($listaVendedores as $vendedor)
                             @if($usuario->keycodigo==$vendedor)selected @endif
                           @endforeach                        
                         @endif
                        >
                        {{$usuario->keycodigo}} {{$usuario->nombre}}
                        </option>		
                    @endforeach
                @endif
            </select>
        </div>
        <div>
            <button type="submit" class="btn btn-primary float-right">Guardar</button>
        </div>
    </form>
</div>
<div class="content">
    @if(isset($parametros))
        <table class="table">
            <thead>
                <tr>
                    <th>Codigo grupo</th>
                    <th>% calculo comision</th>
                    <th>% descuento gastos operativos</th>
                    <th>Vendedores Especiales</th>                    
                    <th>Opciones</th>
                </tr>
            </thead>
            <tbody>
                @foreach($parametros as $parametro)
                <tr>
                    <td>
                        @foreach($grupos as $grupo)
                            @if($grupo->keycodigo == $parametro->codgrupo)
                            {{$grupo->nombre}}
                            @endif
                        @endforeach
                        
                    </td>
                    <td>{{$parametro->porcentaje_calculo_comision}}</td>
                    <td>{{$parametro->porcentaje_descuento_comision}}</td>
                    <td>
                        <?php
                            if(!empty($parametro->vendedores_especiales_id)){
                                echo"solo aplica para: ";
                                $vendedoresEspeciales = explode(',',$parametro->vendedores_especiales_id);
                                foreach($vendedoresEspeciales as $vendedor):
                                   foreach($usuarios as $usuario):
                                        if($usuario->keycodigo == $vendedor){
                                            echo $usuario->nombre.', ';
                                        }
                                    endforeach ;
                                endforeach;    
                                
                            }else{
                                echo" No hay, Aplica a todos";
                            }
                           
                        
                        ?>
                    </td>
                    
                    <td>
                        <a href="{{route('eliminarEmpleadosComisionEspecial',$parametro->id)}}" class="text-danger"><i class="fa fa-trash mx-2"></i></a>
                        <a href="{{route('editarEmpleadosComisionEspecial',$parametro->id)}}"><i class="fa fa-edit mx-2"></i></a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    
    @endif
</div>
@endsection
@section('js')
<script src="{{asset('js/select2.min.js')}}"></script>
<script type="text/javascript">
    $(document).ready(function() {
        // select 2
		$('.js-example-basic-single').select2({
	    	placeholder: 'Seleccione los vendedores',    	
	    	maximumSelectionLength:100,
	    });
    });
</script>
@endsection