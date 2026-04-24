<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SecurityLog extends Model
{
    use HasFactory;

    // Definimos los campos que se pueden llenar masivamente
    protected $fillable = [
        'event',
        'user_identifier',
        'ip_address',
        'user_agent',
        'severity',
        'details'
    ];

    /**
     * El atributo 'details' debe convertirse automáticamente 
     * de JSON a Array al leerlo, y viceversa al guardarlo.
     */
    protected $casts = [
        'details' => 'array',
    ];

    /**
     * Helper para obtener el color del badge según la gravedad
     * Útil para la vista del Centro de Seguridad.
     */
    public function getSeverityColor()
    {
        return match (strtolower($this->severity)) {
            'alta', 'crítica' => 'danger',  // Rojo
            'media'           => 'warning', // Amarillo
            'baja'            => 'info',    // Azul/Cian
            default           => 'secondary',
        };
    }
}