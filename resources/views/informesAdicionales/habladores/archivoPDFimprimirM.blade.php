<!DOCTYPE html>
<html>
<head>
	<title>reporte</title>
	<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="Página Oficial del Grupo Farmadescuento">
    <title>Farmadescuento</title>
    <!-- Bootstrap core CSS -->
   <link href="{{asset('css/bootstrap4.5.2.css')}}" rel="stylesheet">
   
   <link href="{{asset('css/custom-habladores.css')}}" rel="stylesheet">
    
</head>
<body>
    <div class="container">
    <!--    <div class="row row-cols-2 row-cols-md-2">-->
            <table border="1">                
            @php $n=1;$contador=1; @endphp
            
            <?php foreach($habladores as $hablador) { ?>
            <?php
                
                
                if($contador==1){ echo'<tr><td width="500px">';}
                if($contador==2){echo '<td width="500px">';}
               
            ?>
            <!--<tr>
                <td width="500px">
                 INICIO DE LOS HABLADORES -->
                    <div class="text-center separador-habladores mt-2 mb-1">
                        <div class="w-100 h-100 rounded img-zoom-seleccion-tienda">
                            <img class="m-1" src="{{asset($logoEmpresa)}}" style="width: 100px">
                            <p class="h3 font-weight-bold mr-2 ml-2 height-hablador mt-0 pt-0 mb-0">{{$hablador['nombre']}}</p>
                            <div class="bg-navbar-gray">
                                
                                <p class="fuente-precios-reconvertidos font-weight-bold m-0 pt-2">{{$hablador['tipoMoneda']}} {{number_format($hablador['precio'],2,',','.')}}</p>
                            </div>
                            <div class="m-2">
                                <span class="h5 font-weight-bold text-dark d-flex justify-content-start align-items-end float-left mt-2">{{date('d-m-Y')}}</span>
                                <span class="h5 font-weight-bold text-dark d-flex justify-content-end align-items-end float-right mt-2">{{$hablador['codprod']}}</span>
                            </div>
                        </div>
                    </div>
                <!-- FIN DE LOS HABLADORES
                </td>
                <td width="500px"> -->
                <?php
                    if($contador==1){
                        
                        echo"</td>";
                        $contador=2;
                        continue;
                    }    
               
                    if($contador==2){                        
                        echo"</td></tr>";
                        $contador=1;
                        continue;
                    }
                ?>
                
                @php $n++ @endphp
                
                <?php } ?> <!-- end foreach -->
            </table>
 <!--       </div>-->
    </div>
    <style type="{{asset('js/bootstrap.min.js')}}"></style>
    <style type="{{asset('js/bootstrap.bundle.min.js')}}"></style>   
</body>
</html>