@extends('layouts.app')
@section('content')
    <div class="container-fluid">
        <h3>Editar Usuarios <a href="{{route('admin.user.index')}}" class="btn btn-warning float-right"><i class="fas fa-chevron-left"></i>Regresar</a></h3>
            @if(session('info'))
            <div class="alert alert-info">
                <strong>{{session('info')}}</strong>
            </div>
            @endif
        <form action="{{route('admin.user.update',$user)}}" method="post">
            @method('PUT')
            @csrf
            <div class="row">
                <div class="col">
                    <label for="">Usuario</label>
                    <input type="text" value="{{$user->name}}" class="form-control" name="name">
                    <label for="">Email</label>
                    <input type="text" value="{{$user->email}}" class="form-control" name="email">
                    <label for="">Password</label>
                    <input type="text" class="form-control" name="password">
                    <hr>
                    <label for="">Empresas a las que tiene acceso</label>
                    <br>
                   
                        @foreach($empresas as $empresa)
                        <input type="checkbox" name="empresas[]" id="{{$empresa->id}}" value="{{ $empresa->rif }}"
                            @if(isset($accesoEmpresas))
                                @foreach($accesoEmpresas as $acceso)
                                   @if($acceso->empresa_rif == $empresa->rif)
                                    checked
                                   @endif
                                @endforeach
                            @endif
                        ><label for="{{$empresa->id}}">{{$empresa->nombre}}</label>  <br>
                        @endforeach
                    <button type="submit" class="btn btn-primary float-end my-3">Actualizar</button>
                </div>
                <div class="col">
                    <div class="card">
                        <div class="card-header">
                            Roles de usuarios
                        </div>
                        <div class="card-body">                            
                            @foreach($roles as $role)
                                <div>
                                    <input id="rol" name="roles[]" value="{{$role->id}}" type="checkbox"
                                    @foreach($rolesActuales as $rolActual)
                                        @if($role->name==$rolActual)
                                            checked
                                        @endif
                                    @endforeach>
                                    <label for="rol">{{$role->name}}</label>
                                </div>
                            @endforeach
                        </div>
                    </div>
                    
                </div>
                <div class="col">
                    <div class="card">
                        <div class="card-header">
                            Permioso Actuales
                        </div>
                        <div class="card-body">
                            <ul>
                            @foreach($permisosActuales as $permisos)
                                <li>{{$permisos->name}}</li>
                            @endforeach
                            </ul>    
                        </div>
                    </div>
                </div>
            </div>
            
        </form>
    </div>
@endsection