<?php

namespace App\Http\Controllers\Herramientas;

use App\Http\Controllers\Controller;
use App\Mail\Notification;
use Illuminate\support\facades\Mail;
use Illuminate\Http\Request;
use App\Http\Controllers\RetencionIva\RetencionIvaController;
use App\Models\Proveedor;

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
            $response = Mail::to($proveedor->correo)->cc('gerenciasistemasfh@gmail.com')->send(new Notification($nomRetenido,$comprobante,$nomAgente));
            \Session::flash('message', 'Correo enviado al proveedor '.$datosRetencion->nom_retenido);
            \Session::flash('alert','alert-success');
            return redirect()->back();
        }else{
            \Session::flash('message', 'El Proveedor '.$datosRetencion->nom_retenido.' al cual desea enviar la retencion de IVA no tiene correo registrado por lo tanto no se envio el correo');
            \Session::flash('alert','alert-warning');
            return redirect()->back();
        }
        
    }
}
