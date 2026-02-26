<?php
namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class DocumentoGeneradoMail extends Mailable
{
    use Queueable, SerializesModels;

    public $personal;
    public $tipoDocumento;
    public $pdfContent;
    public $nombreArchivo;

    public function __construct($personal, $tipoDocumento, $pdfContent, $nombreArchivo)
    {
        $this->personal = $personal;
        $this->tipoDocumento = $tipoDocumento;
        $this->pdfContent = $pdfContent;
        $this->nombreArchivo = $nombreArchivo;
    }

    public function build()
    {
        return $this->subject("Su {$this->tipoDocumento} ha sido generado")
                    ->view('emails.documento_trabajador') // Crea esta vista luego
                    ->attachData($this->pdfContent, $this->nombreArchivo, [
                        'mime' => 'application/pdf',
                    ]);
    }
}
