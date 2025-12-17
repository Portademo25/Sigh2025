<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Setting; // Si creaste el modelo Setting

class SettingsController extends Controller
{
    /**
     * Muestra la vista de configuraciones.
     */
    public function index()
    {
        // Por ahora cargamos la vista directamente. 
        // Más adelante buscaremos los valores de la DB aquí.
        return view('admin.settings.index');
    }

    /**
     * Guarda o actualiza las configuraciones.
     */
    public function update(Request $request)
    {
        // Aquí procesaremos los datos del formulario
        // Por ahora, solo simularemos el guardado y regresaremos con éxito
        
        return redirect()->back()->with('success', 'Configuraciones actualizadas correctamente.');
    }
}