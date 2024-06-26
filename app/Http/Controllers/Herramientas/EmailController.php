<?php

namespace App\Http\Controllers\Herramientas;

use App\Http\Controllers\Controller;
use App\Mail\Notification;
use Illuminate\Support\Facades\Mail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\RetencionIva\RetencionIvaController;
use App\Http\Controllers\Islr\islrController;
use App\Models\Proveedor;
use App\Models\RetencionIvaDetalle;
use App\Models\Parametro;
use App\Models\Islr;

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
        $datosRetencion = $objRetencion->consultarRetencionIva($comprobante,$empresaRif);
        $nomRetenido = $datosRetencion->nom_retenido;
        $nomAgente = $datosRetencion->nom_agente;
        //generamos el archivo de retencion de iva
        $retencion = $objRetencion->mostrarComprobanteRetencionIva($comprobante,$empresaRif,$firma='firma');

        //consultamos los datos del proveedor retenido
        $proveedor = Proveedor::where('rif',$datosRetencion->rif_retenido)->select('correo','tipo_proveedor','ultimo_porcentaje_retener_islr')->first();
        
        

        //buscamos los parametros del corre del sistema vealo para enviarla
        $correo_del_sistema = Parametro::buscarVariable('correo_del_sistema');
        $cc_correo_del_sistema = Parametro::buscarVariable('cc_correo_del_sistema');
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
		$facturas ='"'. implode('","',$facturasArray).'"';
        //fin concatenar numeros de facturas 
        
        //UNA VEZ QUE TENEMOS LOS NUMEROS DE FACTURAS BUSQUEMOS LA RETENCION DE ISLR
        //si el proveedor es de servicio y tiene porcentaje_retener_islr generamos el pdf de islr
        if($proveedor->tipo_proveedor=='servicios' && $proveedor->ultimo_porcentaje_retener_islr >0){
            //generamos el pdf de la retencion de islr
            //buscamos el id de esa retencion con los datos que poseemos
            $sql="SELECT            
            islrs.id,
            islrs.proveedor_rif,
            islrs.empresa_rif
          FROM
            islr_detalles,
            islrs
          WHERE SUBSTRING_INDEX (islr_detalles.nFactura, '/', 1) in( ".$facturas." )
            AND islr_detalles.`islr_id` = islrs.id
            AND islrs.`proveedor_rif` ='".$datosRetencion->rif_retenido."'
            AND islrs.`empresa_rif` ='".$empresaRif."'";
            
            $datosislr = DB::select($sql);
            
            $objRetencionIslr = new islrController();
            foreach($datosislr as $islr){
                $pdfRetencionIslr = $objRetencionIslr->viewPdf($islr->id,$comprobante);
            }
        }

        //buscamos el correo del proveedor
        $correos ='';
        
        if(!empty($proveedor->correo)){
            //si son varios correos lo repetimos y enviamos la cantivad de veces que tenga correo
            $correos = explode(',',$proveedor->correo);
            foreach($correos as $correo){
                //validamos que el correo este bien escrito
              //  if (filter_var($correo, FILTER_VALIDATE_EMAIL)) {
                    
                    //en to() se puede agregar una coleccion de email 
                    //mailer('smtp') el smtp es el por defecto del archivo .env pero este esta en config/email, si en config/email tienes varios servidor de correos configurado puedes seleccionarlo en vez de smtp https://www.youtube.com/watch?v=uYEL36fGFiM
                    
                    //verificamos si los correo se envian con copia a otro correo de la empresa
                    if(empty($cc_correo_del_sistema)){
                        $response = Mail::mailer("smtp")
                        ->to($correo)
                        ->send(new Notification($nomRetenido,$comprobante,$nomAgente,$archivoAdjunto,$facturas,$asunto));
                    }else{
                        $response = Mail::mailer("smtp")
                        ->to($correo)
                        ->cc($cc_correo_del_sistema)                        
                        ->send(new Notification($nomRetenido,$comprobante,$nomAgente,$archivoAdjunto,$facturas,$asunto));
                    }// //fin verificamos si los correo se envian con copia a otro correo de la empresa y envio del correo
                    
                    
                    //actualizamos la bandera de correo enviado en la tabla retenciones_dat
                    RetencionIvaDetalle::where('comprobante',$comprobante)->where('rif_agente',$empresaRif)->update(['correo_enviado'=>1]);                    

            /*  }else{
                    \Session::flash('message', 'El correo '.$correo.' no se envio por no ser un correo valido por favor verifiquelo en el proveedor '.$nomRetenido);
			        \Session::flash('alert','alert-warning');
                }*/
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
