<?php
// app/Http/Controllers/AdminController.php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use App\Models\User;

class AdminController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('role:administrador');
    }

    public function dashboard()
    {
        $totalUsers = User::count();
        $totalEmployees = User::role('empleado')->count();
        $recentUsers = User::with('roles')->latest()->take(5)->get();

        return view('admin.dashboard', compact('totalUsers', 'totalEmployees', 'recentUsers'));
    }

    public function gestionEmpleados()
    {
        $empleados = User::role('empleado')->paginate(10);
        return view('admin.empleados', compact('empleados'));
    }

    public function crearEmpleado(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:8|confirmed',
        ]);

        $empleado = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
        ]);

        $empleado->assignRole('empleado');

        return redirect()->route('admin.empleados')->with('success', 'Empleado creado exitosamente');
    }

    public function reportes()
    {
        return view('admin.reportes');
    }
}
