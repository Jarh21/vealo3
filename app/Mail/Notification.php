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
    
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($nombre,$comprobante,$nomAgente)
    {
        $this->nombre = $nombre;
        $this->comprobante = $comprobante;
        $this->nomAgente = $nomAgente;
        
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
            return $this->view('email.retencionIva')->from("admivent.jarh.deli@gmail.com",$this->nomAgente)->subject("Retencion IVA GFD")->attachFromStorage('pdf/'.$nombreArchivo);
        } else {
            echo "No se encontraron archivos que coincidan con los valores proporcionados.";
        }   
       
    }
}
