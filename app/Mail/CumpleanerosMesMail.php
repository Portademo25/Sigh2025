<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class CumpleanerosMesMail extends Mailable
{
    use Queueable, SerializesModels;

    // 1. Declarar la variable como pública para que el Blade la vea automáticamente
    public $cumpleaneros;
    public $logoFona;
    public $mesActual;

    /**
     * Create a new message instance.
     */
    public function __construct($cumpleaneros)
    {
        // 2. Recibir los datos desde el Comando
        $this->cumpleaneros = $cumpleaneros;

        // 3. Preparar datos adicionales (Logo y Nombre del Mes)
        $this->logoFona = $this->cargarLogo('logo_fona.png');
        $this->mesActual = $this->getNombreMes(date('m'));
    }

    public function build()
    {
        // 4. Usamos build() para mayor compatibilidad con tu versión de lógica
        return $this->view('emails.cumpleaneros')
                    ->subject('🎂 Reporte: Cumpleañeros del Mes - Gestión Humana')
                    ->with([
                        'cumpleaneros' => $this->cumpleaneros,
                        'logoFona' => $this->logoFona,
                        'mesActual' => $this->mesActual,
                    ]);
    }

    // Método para el logo en Base64 (Igual que en tus controladores)
    private function cargarLogo($nombreArchivo)
    {
        $path = public_path('images/' . $nombreArchivo);
        if (!file_exists($path)) return '';
        $type = pathinfo($path, PATHINFO_EXTENSION);
        $data = file_get_contents($path);
        return 'data:image/' . $type . ';base64,' . base64_encode($data);
    }

    private function getNombreMes($mes)
    {
        $meses = [
            '01' => 'Enero', '02' => 'Febrero', '03' => 'Marzo', '04' => 'Abril',
            '05' => 'Mayo', '06' => 'Junio', '07' => 'Julio', '08' => 'Agosto',
            '09' => 'Septiembre', '10' => 'Octubre', '11' => 'Noviembre', '12' => 'Diciembre'
        ];
        return $meses[$mes] ?? '';
    }
}
