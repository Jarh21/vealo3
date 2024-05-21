<?php

namespace App\Http\Controllers\Herramientas;

use App\Http\Controllers\Controller;
use App\Mail\Notification;
use Illuminate\Support\Facades\Mail;
use Illuminate\Http\Request;
use App\Http\Controllers\RetencionIva\RetencionIvaController;
use App\Models\Proveedor;
use App\Models\RetencionIvaDetalle;
use App\Models\Parametro;

class EmailController extends Controller
{
    //use RetencionIvaController;
    //
    
    public function enviarEmailRetencionIvaPost(Request $request){
        
        //return new Notification("Jose Rivero"); copia al: gfdpagos@gmail.com
        $comprobante=$request->comprobante;
        $empresaRif= $request->rifAgente;
        $asunto = $request->asunto;
        $archivoAdjunto =array();
        $nomRetenido='';
        $nomAgente ='';
        $objRetencion = new RetencionIvaController();
        //generamos el archivo de retencion de iva
        $retencion = $objRetencion->mostrarComprobanteRetencionIva($comprobante,$empresaRif,$firma='firma');
        //consultamos los datos del proveedor retenido
        $datosRetencion = $objRetencion->consultarRetencionIva($comprobante,$empresaRif);
        $nomRetenido = $datosRetencion->nom_retenido;
        $nomAgente = $datosRetencion->nom_agente;

        //buscamos los parametros del corre del sistema vealo para enviarla
        $correo_del_sistema = Parametro::buscarVariable('correo_del_sistema');
        $password_correo_del_sistema = Parametro::buscarVariable('password_correo_del_sistema');

        //si en configuracion general esta registrado el correo y la clave de aplicaciones de gmail usamos eso de lo contrario usamos los del archivo .env
        if(!empty($correo_del_sistema) and !empty($password_correo_del_sistema)){
            // Establecer los parámetros SMTP en la configuración
            config([
                'mail.mailers.smtp.username' => $correo_del_sistema,
                'mail.mailers.smtp.password' => $password_correo_del_sistema,
                'mail.from.address'=>$correo_del_sistema,
            ]);        
        }        

        //cargar el nuevo archivo       
        if($request->hasfile('archivo')){
            foreach($request->file('archivo') as $key=>$archivo){
                $file = $archivo;
                $destinatinoPath ='imagen/';
                $filename = 'archivo'.time().'-'. $key.'.' . $file->getClientOriginalExtension();
                $uploadsuccess = $archivo->move($destinatinoPath,$filename);    
                $archivoAdjunto[]= $destinatinoPath.$filename;
            }
        }//fin de cargar el nuevo archivo

        //si queremos enviar  los numeros de facturas por el correo
        $datosFacturas = RetencionIvaDetalle::where('comprobante',$comprobante)->where('rif_agente',$empresaRif)->select('documento')->get();
        $facturasArray =array();
		$facturas ='';
		//extraemos los numero de facturas de datos de factura
		foreach($datosFacturas as $factura){
			$facturasArray[] = $factura->documento;
		}
		$facturas = implode(', ',$facturasArray);
        //fin concatenar numeros de facturas       

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
                    
                    $response = Mail::mailer("smtp")
                        ->to($correo)
                        //->cc('gfdpagos@gmail.com')                        
                        ->send(new Notification($nomRetenido,$comprobante,$nomAgente,$archivoAdjunto,$facturas,$asunto));
                    
                    //actualizamos la bandera de correo enviado en la tabla retenciones_dat
                    RetencionIvaDetalle::where('comprobante',$comprobante)->where('rif_agente',$empresaRif)->update(['correo_enviado'=>1]);                    

                }else{
                    \Session::flash('message', 'El correo '.$correo.' no se envio por no ser un correo valido por favor verifiquelo en el proveedor '.$nomRetenido);
			        \Session::flash('alert','alert-warning');
                }
            }
            //eliminar los archivos basura, los que ya se enviaron al correo
            if(!empty($archivoAdjunto)){
                foreach($archivoAdjunto as $archivoEliminar){
                    if(file_exists($archivoEliminar)){
                        unlink($archivoEliminar);
                    }
                    
                }
            }
            return 1;
        }else{
            return 0;
        }
        
    }
}
