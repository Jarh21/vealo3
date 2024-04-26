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
    /* public function enviarEmailRetencionIva($comprobante,$empresaRif){
        //return new Notification("Jose Rivero"); copia al: gfdpagos@gmail.com
        $nomRetenido='';
        $nomAgente ='';
        $objRetencion = new RetencionIvaController();
        $retencion = $objRetencion->mostrarComprobanteRetencionIva($comprobante,$empresaRif,$firma='firma');
        $datosRetencion = $objRetencion->consultarRetencionIva($comprobante,$empresaRif);
        $nomRetenido = $datosRetencion->nom_retenido;
        $nomAgente = $datosRetencion->nom_agente;
        $archivoAdjunto ='';
        //si queremos enviar  las facturas por el correo
        /* $facturasArray =array();
		$facturas ='';

		//extraemos los numero de facturas de datos de factura
		foreach($datosFacturas as $factura){
			$facturasArray[] = $factura->documento;
		}
		$facturas = implode(',',$facturasArray); */

        //buscamos el correo del proveedor
     /*   $correos ='';
        $proveedor = Proveedor::where('rif',$datosRetencion->rif_retenido)->select('correo')->first();
        if(!empty($proveedor->correo)){

            $correos = explode(',',$proveedor->correo);
            foreach($correos as $correo){
                //validamos que el correo este bien escrito
                if (filter_var($correo, FILTER_VALIDATE_EMAIL)) {
                    //en to() se puede agregar una coleccion de email 
                    //mailer('smtp') el smtp es el por defecto del archivo .env pero este esta en config/email, si en config/email tienes varios servidor de correos configurado puedes seleccionarlo en vez de smtp https://www.youtube.com/watch?v=uYEL36fGFiM
                    $response = Mail::mailer("smtp")->to($correo)->cc('gfdpagos@gmail.com')->send(new Notification($nomRetenido,$comprobante,$nomAgente,$archivoAdjunto));
                    //actualizamos la bandera de correo enviado en la tabla retenciones_dat
                    RetencionIvaDetalle::where('comprobante',$comprobante)->where('rif_agente',$empresaRif)->update(['correo_enviado'=>1]);
                    
                }else{
                    \Session::flash('message', 'El correo '.$correo.' no se envio por no ser un correo valido por favor verifiquelo en el proveedor '.$nomRetenido);
			        \Session::flash('alert','alert-warning');
                }
            }
           
            return 1;
        }else{
            return 0;
        }
        
    } */

    public function enviarEmailRetencionIvaPost(Request $request){
        
        //return new Notification("Jose Rivero"); copia al: gfdpagos@gmail.com
        $comprobante=$request->comprobante;
        $empresaRif= $request->rifAgente;
        $archivoAdjunto =array();
        $nomRetenido='';
        $nomAgente ='';
        $objRetencion = new RetencionIvaController();
        //$retencion = $objRetencion->mostrarComprobanteRetencionIva($comprobante,$empresaRif,$firma='firma');
        $datosRetencion = $objRetencion->consultarRetencionIva($comprobante,$empresaRif);
        $nomRetenido = $datosRetencion->nom_retenido;
        $nomAgente = $datosRetencion->nom_agente;

        //cargar el nuevo archivo
       
        if($request->hasfile('archivo')){
            foreach($request->file('archivo') as $key=>$archivo){
                $file = $archivo;
                $destinatinoPath ='imagen/';
                $filename = 'archivo'.time().'-'. $key.'.' . $file->getClientOriginalExtension();
                $uploadsuccess = $archivo->move($destinatinoPath,$filename);    
                $archivoAdjunto[]= $destinatinoPath.$filename;
            }
            

        }        


        //si queremos enviar  los numeros de facturas por el correo
        /* $facturasArray =array();
		$facturas ='';

		//extraemos los numero de facturas de datos de factura
		foreach($datosFacturas as $factura){
			$facturasArray[] = $factura->documento;
		}
		$facturas = implode(',',$facturasArray); */

        

        //buscamos el correo del proveedor
        $correos ='';
        $proveedor = Proveedor::where('rif',$datosRetencion->rif_retenido)->select('correo')->first();
        if(!empty($proveedor->correo)){
            //si son varios correos lo repetimos y enviamos la cantivad de veces que tenga correo
            $correos = explode(',',$proveedor->correo);
            foreach($correos as $correo){
                //validamos que el correo este bien escrito
                if (filter_var($correo, FILTER_VALIDATE_EMAIL)) {
                    
                    //en to() se puede agregar una coleccion de email 
                    //mailer('smtp') el smtp es el por defecto del archivo .env pero este esta en config/email, si en config/email tienes varios servidor de correos configurado puedes seleccionarlo en vez de smtp https://www.youtube.com/watch?v=uYEL36fGFiM
                    
                    $response = Mail::mailer("smtp")->to($correo)->cc('gfdpagos@gmail.com')->send(new Notification($nomRetenido,$comprobante,$nomAgente,$archivoAdjunto));
                    
                    //actualizamos la bandera de correo enviado en la tabla retenciones_dat
                    RetencionIvaDetalle::where('comprobante',$comprobante)->where('rif_agente',$empresaRif)->update(['correo_enviado'=>1]);
                    
                }else{
                    \Session::flash('message', 'El correo '.$correo.' no se envio por no ser un correo valido por favor verifiquelo en el proveedor '.$nomRetenido);
			        \Session::flash('alert','alert-warning');
                }
            }
           
            return 1;
        }else{
            return 0;
        }
        
    }
}
