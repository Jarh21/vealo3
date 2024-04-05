<?php

namespace App\Http\Controllers\Herramientas;

use App\Http\Controllers\Controller;
use App\Mail\Notification;
use Illuminate\Support\Facades\Mail;
use Illuminate\Http\Request;
use App\Http\Controllers\RetencionIva\RetencionIvaController;
use App\Models\Proveedor;
use App\Models\RetencionIvaDetalle;

class EmailController extends Controller
{
    //use RetencionIvaController;
    //
    public function enviarEmailRetencionIva($comprobante,$empresaRif){
        //return new Notification("Jose Rivero"); copia al: gfdpagos@gmail.com
        $nomRetenido='';
        $nomAgente ='';
        $objRetencion = new RetencionIvaController();
        $retencion = $objRetencion->mostrarComprobanteRetencionIva($comprobante,$empresaRif,$firma='firma');
        $datosRetencion = $objRetencion->consultarRetencionIva($comprobante,$empresaRif);
        $nomRetenido = $datosRetencion->nom_retenido;
        $nomAgente = $datosRetencion->nom_agente;

        //si queremos enviar  las facturas por el correo
        /* $facturasArray =array();
		$facturas ='';

		//extraemos los numero de facturas de datos de factura
		foreach($datosFacturas as $factura){
			$facturasArray[] = $factura->documento;
		}
		$facturas = implode(',',$facturasArray); */

        //buscamos el correo del proveedor
        $proveedor = Proveedor::where('rif',$datosRetencion->rif_retenido)->select('correo')->first();
        if(!empty($proveedor->correo)){
            
            $response = Mail::to($proveedor->correo)->cc('gfdpagos@gmail.com')->send(new Notification($nomRetenido,$comprobante,$nomAgente));
            //actualizamos la bandera de correo enviado en la tabla retenciones_dat
            RetencionIvaDetalle::where('comprobante',$comprobante)->where('rif_agente',$empresaRif)->update(['correo_enviado'=>1]);
            return 1;
        }else{
            return 0;
        }
        
    }
}
