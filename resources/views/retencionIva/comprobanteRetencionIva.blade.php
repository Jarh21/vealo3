<?php 
 // use DateTime;
 date_default_timezone_set('UTC');
date_default_timezone_set("America/Caracas");
  $ofecha = new DateTime($retencionIva->fecha);
  $fecha = $ofecha->format('d-m-Y');
  
?>
<!-- /******************************************************************** */ -->
<style type="text/css">
  html {
	  margin: 10pt 30pt;
  }

.Estilo1 {
	font-family: Verdana, Arial, Helvetica, sans-serif;
	font-size: 10px;
	font-weight: bold;
}
.Estilo2 {font-size: 10px; font-family: Verdana, Arial, Helvetica, sans-serif;}


</style>

<span class="Estilo2">{{now()}}</span>
<table width="755"  border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td>
      <table width="755" border="0" cellspacing="0" cellpadding="0">
        <tr>
          <td><img src="{{url('/imagen/logo1712263417-Logo_Pequenio.gif')}}">{{url('/imagen/logo1712263417-Logo_Pequenio.gif')}}</td>
          <td align="center" valign="bottom"><span class="Estilo1">NRO. DE COMPROBANTE </span></td>
          <td align="center" valign="bottom"><span class="Estilo1">FECHA DE EMISION </span></td>
        </tr>
        <tr>
          <td>&nbsp;</td>
          <td align="center"><span class="Estilo2">{{$retencionIva->comprobante}}</span></td>
          <td align="center"><span class="Estilo2">{{$fecha}}</span></td>
        </tr>
        <tr>
          <td>&nbsp;</td>
          <td align="center">&nbsp;</td>
          <td align="center">&nbsp;</td>
        </tr>
        <tr>
          <td width="250"><span class="Estilo1">NOMBRE O RAZON SOCIAL DEL AGENTE DE RETENCION</span></td>
          <td width="270" align="center"><span class="Estilo1">REGISTRO DE INFORMACION FISCAL DEL AGENTE DE RETENCION (RIF)</span></td>
          <td width="242" align="center"><span class="Estilo1">PERIODO FISCAL</span></td>
        </tr>
        <tr>
          <td class="Estilo2">{{$retencionIva->nom_agente}}</td>
          <td align="center" class="Estilo2">{{$retencionIva->rif_agente}}</td>		
          <td align="center" class="Estilo2">A&Ntilde;O: {{$datosModificados['anio']}} / MES: {{$datosModificados['mes']}}</td>
        </tr>
        <tr>
          <td>&nbsp;</td>
          <td>&nbsp;</td>
          <td>&nbsp;</td>
        </tr>
        <tr>
          <td valign="bottom"><span class="Estilo1">DIRECCION FISCAL DEL AGENTE DE RETENCION</span></td>
          <td>&nbsp;</td>
          <td>&nbsp;</td>
        </tr>
        <tr>
          <td colspan="2" valign="top"><span class="Estilo2">{{$datosEmpresa->direccion}}</span></td>
          <td>&nbsp;</td>
        </tr>
        <tr>
          <td>&nbsp;</td>
          <td>&nbsp;</td>
          <td>&nbsp;</td>
        </tr>
        <tr>
          <td><span class="Estilo1">NOMBRE O RAZON SOCIAL DEL  RETENIDO </span></td>
          <td align="center"><span class="Estilo1">REGISTRO DE INFORMACION FISCAL DEL RETENIDO (RIF) </span></td>
          <td align="center"><span class="Estilo1">Nro. EGRESO / CHEQUE</span></td>
        </tr>
        <tr>
          <td class="Estilo2">{{$retencionIva->nom_retenido}}</td>
          <td align="center" class="Estilo2">{{$retencionIva->rif_retenido}}</td>
          <td align="center"><span class="Estilo2">{{$retencionIva->cheque}}</span></td>
        </tr>
        @if($direccion_proveedor_en_comprobante_retencioniva=='on')
        <tr>
          <td>&nbsp;</td>
          <td>&nbsp;</td>
          <td>&nbsp;</td>
        </tr>
        <tr>
          <td valign="bottom"><span class="Estilo1">DIRECCION FISCAL DEL SUJETO RETENIDO</span></td>
          <td>&nbsp;</td>
          <td>&nbsp;</td>
        </tr>
        <tr>
          <td colspan="2" valign="top"><span class="Estilo2">{{$datosProveedor->direccion}}</span></td>
          <td>&nbsp;</td>
        </tr>
        @endif
      </table>
    </td>
  </tr>
  <tr>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td>
      <table width="755" border="0" cellpadding="0" cellspacing="1" bgcolor="#000000">

        <tr align="center">
          <td colspan="10" bgcolor="#FFFFFF">&nbsp;</td>
          <td colspan="5" bgcolor="#FFFFFF"><span class="Estilo1">COMPRAS INTERNAS O IMPORTACIONES</span></td>
          </tr>
        <tr align="center">
          <td bgcolor="#FFFFFF"  width="15"><span class="Estilo1">Nro.<br /> Operac</span></td>
          <td bgcolor="#FFFFFF" width="40"><span class="Estilo1">Fecha<br />de la Factura</span></td>
          <td bgcolor="#FFFFFF"><span class="Estilo1">Numero de <br />Factura</span></td>
          <td bgcolor="#FFFFFF"><span class="Estilo1">Nro. Control <br />de Factura</span></td>
          <td bgcolor="#FFFFFF"><span class="Estilo1">Nro. Nota <br />Debito</span></td>
          <td bgcolor="#FFFFFF"><span class="Estilo1">Nro. Nota <br />Credito</span></td>
          <td bgcolor="#FFFFFF"><span class="Estilo1">Tipo de  <br />Transaccion</span></td>
          <td bgcolor="#FFFFFF"><span class="Estilo1">Nro. Factura <br /> Afectada</span></td>
          <td bgcolor="#FFFFFF"><span class="Estilo1">Total compras <br />incluyendo IVA</span></td>
          <td bgcolor="#FFFFFF"><span class="Estilo1">Compras sin<br />derecho a Credito</span></td>
          <td bgcolor="#FFFFFF"><span class="Estilo1">Base <br />Imponible</span></td>
          <td bgcolor="#FFFFFF" width="15"><span class="Estilo1">% <br />Alicuota</span></td>
          <td bgcolor="#FFFFFF"><span class="Estilo1">Impuesto <br />IVA</span></td>
          <td bgcolor="#FFFFFF"><span class="Estilo1">IVA <br />RETENIDO</span></td>
          <td bgcolor="#FFFFFF" width="20"><span class="Estilo1">% <br />Retencion</span></td>
        </tr>
        <?php 
          //contadore de totales
          $contador=1;
          $totalCompras=0;
          $totalSinCredito=0;
          $totalBase=0;
          $totalIva=0;
          $totalIvaRetener=0;
          $totalCancelar =0;
          $debitoCredito ='';
        ?>
        @foreach($datosFacturas as $datosFactura)
        @php 
          if($datosFactura->tipo_docu=='NC'){
            $debitoCredito ='-';
          } @endphp
        <tr align="center">
          <td bgcolor="#FFFFFF"><span class="Estilo2">{{$contador}}</span></td>
          <td bgcolor="#FFFFFF"><span class="Estilo2">{{date("d-m-Y", strtotime($datosFactura->fecha_docu))}}</span></td>
          <td bgcolor="#FFFFFF"><span class="Estilo2">@if($datosFactura->tipo_docu == 'FA')@if(!empty($datosFactura->serie)){{'SERIE'.$datosFactura->serie}}@endif{{$datosFactura->documento}}@endif</span></td>
          <td bgcolor="#FFFFFF"><span class="Estilo2">@if($datosFactura->control_fact=='0')N/A @else{{$datosFactura->control_fact}}@endif</span></td>
          <td bgcolor="#FFFFFF"><span class="Estilo2">@if($datosFactura->tipo_docu == 'ND'){{$datosFactura->documento}}@endif </span></td> <!-- nota debito -->
          <td bgcolor="#FFFFFF"><span class="Estilo2">@if($datosFactura->tipo_docu == 'NC'){{$datosFactura->documento}}@endif </span></td> <!-- nota credito -->
          <td bgcolor="#FFFFFF"><span class="Estilo2">{{$datosFactura->tipo_trans}}</span></td>
          <td bgcolor="#FFFFFF"><span class="Estilo2">{{$datosFactura->fact_afectada ?? ''}}</span></td>
          <td align="right" bgcolor="#FFFFFF"><span class="Estilo2">&nbsp;{{$debitoCredito}}{{number_format($datosFactura->comprasmasiva,2,',','.')}}&nbsp;</span></td>
          <td align="right" bgcolor="#FFFFFF"><span class="Estilo2">&nbsp;{{$debitoCredito}}{{number_format($datosFactura->sincredito,2,',','.')}}&nbsp;</span></td>
          <td align="right" bgcolor="#FFFFFF"><span class="Estilo2">&nbsp;{{$debitoCredito}}{{number_format($datosFactura->base_impon,2,',','.')}}&nbsp;</span></td>
          <td align="right" bgcolor="#FFFFFF"><span class="Estilo2">&nbsp;{{$datosFactura->porc_alic}}&nbsp;</span></td>
          <td align="right" bgcolor="#FFFFFF"><span class="Estilo2">&nbsp;{{$debitoCredito}}{{number_format($datosFactura->iva,2,',','.')}}&nbsp;</span></td>
          <td align="right" bgcolor="#FFFFFF"><span class="Estilo2">&nbsp;{{$debitoCredito}}{{number_format($datosFactura->iva_retenido,2,',','.')}}&nbsp;</span></td>
          <td align="right" bgcolor="#FFFFFF"><span class="Estilo2">&nbsp;{{number_format($datosFactura->porc_reten,2,',','.')}}&nbsp;</span></td>
        </tr>   
        <?php 
          $contador++; 
          $totalCompras += $datosFactura->comprasmasiva;
          $totalSinCredito += $datosFactura->sincredito;
          $totalBase += $datosFactura->base_impon;
          $totalIva += $datosFactura->iva;
          $totalIvaRetener += $datosFactura->iva_retenido;
        ?>   
        @endforeach
        <?php $totalCancelar =  $totalCompras - $totalIvaRetener; ?>
        <tr align="center" bgcolor="#FFFFFF">
          <td colspan="8" align="right"><span class="Estilo1">T O T A L E S&nbsp;&nbsp;&nbsp;</span></td>
          <td align="right"><span class="Estilo1">&nbsp;{{$debitoCredito}}{{number_format($totalCompras,2,',','.')}}&nbsp;</span></td>
          <td align="right"><span class="Estilo1">&nbsp;{{$debitoCredito}}{{number_format($totalSinCredito,2,',','.')}}&nbsp;</span></td>
          <td align="right"><span class="Estilo1">&nbsp;{{$debitoCredito}}{{number_format($totalBase,2,',','.')}}&nbsp;</span></td>
          <td>&nbsp;</td>
          <td align="right"><span class="Estilo1">&nbsp;{{$debitoCredito}}{{number_format($totalIva,2,',','.')}}&nbsp;</span></td>
          <td align="right"><span class="Estilo1">&nbsp;{{$debitoCredito}}{{number_format($totalIvaRetener,2,',','.')}}&nbsp;</span></td>

          <td>&nbsp;</td>
        </tr>
        
        
      </table>
      @if($total_a_cancelar_en_comprobante_retencioniva == 'on')
      <table>
        <tr>
          <td colspan=10 width=755px></td>
          <td colspan=3><span class="Estilo1">TOTAL A CANCELAR</span></td>        
          <td colspan=2><span class="Estilo1">{{number_format($totalCancelar,2,',','.')}}</span></td>
        </tr>
      </table>
      @endif      
    </td>
  </tr>
  <tr>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td>
        <table width="770" border="0" cellspacing="0" cellpadding="0">
      <tr>
        <td width="390" height="50" valign="bottom" class="Estilo2">RECIBIDO POR: ____________________________ </td>

        <td width="390" height="50" valign="bottom">
          
        @if(!empty($firma))
        <img id='imagenFirma' src="{{asset($datosEmpresa->firma)}}">
        @else
          <span class="Estilo2">AGENTE DE RETENCION: ____________________________ </span>
        @endif  
        </td>
      </tr>
      <tr valign="bottom">
        <td height="40"><span class="Estilo2">FECHA DE ENTREGA: __________________________ </span></td>
        <td height="50"><span class="Estilo2">SELLO AGENTE  DE RETENCION:</span></td>
      </tr>
    </table>
	</td>
  </tr>
</table>
<table width="755" border="0" >
  <tr>
      <td>&nbsp;</td>
  </tr>
  @if(!empty($datosEmpresa->providencia_iva))
        <tr>
          <td class="Estilo2">Providencia: {{$datosEmpresa->providencia_iva}}</td><br>
        </tr>
  @endif
  <tr>
      <td class="Estilo2"><em>"<strong>Art&iacute;culo 11.</strong> La Administraci&oacute;n Tributaria podr&aacute; designar como responsables del pago del Impuesto, en calidad de agentes de retenci&oacute;n, a quienes por sus funciones p&uacute;blicas o por raz&oacute;n de sus actividades privadas intervengan en operaciones gravadas con el impuesto establecido en esta Ley." G.O. 38.438 Ley del IVA</em></td>
  </tr>
</table>

<script type="text/javascript">


</script>