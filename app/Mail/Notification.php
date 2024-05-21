<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;


class Notification extends Mailable
{
    use Queueable, SerializesModels;
    public $nombre;
    public $comprobante;
    public $nomAgente;
    public $facturas;
    public $archivoAdjunto;
    public $asunto;
    
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($nombre,$comprobante,$nomAgente,$archivoAdjunto,$facturas,$asunto)
    {
        $this->nombre = $nombre;
        $this->comprobante = $comprobante;
        $this->nomAgente = $nomAgente;
        $this->archivoAdjunto = $archivoAdjunto;
        $this->facturas = $facturas;
        $this->asunto = $asunto;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        
        $rutaDirectorio = storage_path('app/pdf/'); // Ruta del directorio donde se encuentra el archivo
        $valoresBusqueda = [$this->comprobante]; // Valores a buscar en el nombre del archivo
        $patron = implode('*', $valoresBusqueda) . '.pdf'; // Patrón de búsqueda con los valores y la extensión del archivo        
        $archivosEncontrados = glob($rutaDirectorio . '*' . $patron);//comparamos el parametro con la ruta del directorio

        if (!empty($archivosEncontrados)) {
            $archivo = $archivosEncontrados[0]; // Tomamos el primer archivo encontrado
            $nombreArchivo = basename($archivo); // Obtenemos el nombre del archivo  
            
                 
                
            // cuando hay archivos adjuntos adicionales
            $email = $this->view('email.retencionIva',['facturas'=>$this->facturas,'asunto'=>$this->asunto])
                //->from("admivent.jarh.deli@gmail.com",$this->nomAgente)
                ->replyTo("admivent.jarh.deli@gmail.com", $this->nomAgente)
                ->subject("Retencion IVA GFD");          

            // Adjuntar el primer archivo almacenado en el servidor que es la retencion de iva
            $email->attachFromStorage('pdf/' . $nombreArchivo);

            //si hay archivos adicionales se adjunta
            if(!empty($this->archivoAdjunto)){
                // Adjuntar los demás archivos del array $archivoAdjunto
                foreach ($this->archivoAdjunto as $archivo) {
                    $email->attach($archivo);
                }
            }
            
            return $email;
                    
            
        } else {
            echo "No se encontraron archivos que coincidan con los valores proporcionados.";
        }   
       
    }
}
