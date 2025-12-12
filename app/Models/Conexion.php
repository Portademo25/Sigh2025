<?php

// app/Models/Conexion.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Conexion extends Model
{
    // Asume que tu tabla se llama 'conexiones'
    protected $table = 'conexion';

    protected $fillable = [
        'fechaconexion',
        'horaconexion',
        'ipconexion',
        'user_id',
        // Agrega cualquier otro campo que registre tu tabla (ej: 'session_id')
    ];

    // Laravel manejará automáticamente 'created_at' y 'updated_at'

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
