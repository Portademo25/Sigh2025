<?php
// app/Http/Controllers/EmpleadoController.php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class EmpleadoController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('role:empleado');
    }
    public function dashboard()
    {
        $user = Auth::user();
        return view('empleado.dashboard', compact('user'));
    }

    public function perfil()
    {
        $user = Auth::user();
        return view('empleado.perfil', compact('user'));
    }

    public function actualizarPerfil(Request $request)
    {
        $user = Auth::user();

        
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'current_password' => 'nullable|required_with:new_password',
            'new_password' => 'nullable|min:8|confirmed',
        ]);

        $user->name = $request->name;
        $user->email = $request->email;

        if ($request->filled('new_password')) {
            if (!Hash::check($request->current_password, $user->password)) {
                return back()->withErrors(['current_password' => 'La contraseña actual no es correcta']);
            }
            $user->password = Hash::make($request->new_password);
        }

        $user->save();

        return back()->with('success', 'Perfil actualizado exitosamente');
    }

    public function misTareas()
    {
        return view('empleado.tareas');
    }
}
