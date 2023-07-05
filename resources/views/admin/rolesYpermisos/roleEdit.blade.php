@extends('layouts.app')
@section('content')
    <div class="container">
        <div>
           <h3>Editar el Rol {{$datosRole['role']->name}} <a href="{{route('admin.role.index')}}" class="btn btn-close btn-sm float-end"></a></h3> 
        </div>
        @if(session('infoRol'))
            <div class="alert alert-success">
                {{session('infoRol')}}
            </div>
        @endif
        <form action="{{route('admim.role.update',$datosRole['role']->id)}}" method="post">
            @method('put')
            @csrf
        <div class="row">
        
            <div class="col">
                <h4>Asignados</h4>
                <div class="card">
                    <div class="card-body">
                        <ul>
                            @foreach($datosRole['asignados'] as $asignado)
                            <li>
                                <input type="checkbox" name="permisos_por_asignar[]" value="{{$asignado->id}}" checked>
                                <label for="">{{$asignado->id.' '.$asignado->name}}</label>
                                
                            </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
            <div class="col">
                <h4>Por Asignar</h4>
                <div class="card">
                    <div class="card-body">
                        <ul>
                            @foreach($datosRole['porAsignar'] as $porAsignar)
                            <li>
                                <input type="checkbox" name="permisos_por_asignar[]" value="{{$porAsignar->id}}" id="{{$porAsignar->id}}">
                                <label for="{{$porAsignar->id}}">{{$porAsignar->id.' '.$porAsignar->name}}</label>
                            </li>    
                            @endforeach
                        </ul>
                    </div>        
                        
                    
                </div>
                <button type="submit" class="btn btn-primary float-end mt-3">Guardar</button>
            </div>
        </div>
        </form>
    </div>
@endsection