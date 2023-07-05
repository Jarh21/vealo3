@extends('layouts.app')
@section('content')
    <div class="container">
        <h3>Configuracion General Datos de la Empresa</h3>
        <form action="#" method="post">
            @csrf
            <label for="">Nombre de la Empresa</label>
            <input type="text" class="form-control">
            <button type="submit" class="btn btn-primary">Guardar</button>
        </form>
    </div>
@endSection