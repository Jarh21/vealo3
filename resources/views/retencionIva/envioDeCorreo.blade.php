@extends('layouts.app')
@section('content')

    @foreach($retenciones_dat as $retencion)
                <!-- Modal prueba-->
                    
        
            <form action="{{route('retencion.iva.envioemailpost')}}" method="post">
            
                <h5 class="modal-title" id="exampleModalLabel">{{$retencion->comprobante}}</h5>
        
                
                    @csrf
                    <input type="text" name="comprobante" value="{{$retencion->comprobante}}">
                    <input type="text" name="rifAgente" value="{{$retencion->rif_agente}}">
                    <input type="file" name="archivo" id="" multiple>
                <button type="submit">enviar</button>
                
            
        </form>
        <!-- fin modal prueba -->
    @endforeach
@endsection
@section('js')

<script type="text/javascript">


</script>

@endsection                         