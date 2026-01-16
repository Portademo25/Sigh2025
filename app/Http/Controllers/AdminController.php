<?php
// app/Http/Controllers/AdminController.php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class AdminController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('role:admin');
    }

    public function dashboard()
    {
        $totalUsers = User::count();
        $totalEmployees = User::role('empleado')->count();
        $recentUsers = User::with('roles')->latest()->take(5)->get();

        return view('admin.dashboard', compact('totalUsers', 'totalEmployees', 'recentUsers'));
    }

  public function Graficas()
{
    echo "<h1>Si puedes leer esto, estoy en el archivo correcto</h1>";
    exit; //
    // 1. Intentamos obtener la distribución de los datos
    // Usamos DB::table o DB::connection('mysql')->table si tienes varias conexiones
    $distribucion = DB::table('reporte_descargas')
        ->select('tipo_reporte', DB::raw('count(*) as total'))
        ->groupBy('tipo_reporte')
        ->get();

    // 2. Extraemos los labels y los datos
    $labelsPie = $distribucion->pluck('tipo_reporte')->toArray();
    $datosPie = $distribucion->pluck('total')->toArray();

    // --- AQUÍ APLICAMOS EL DD ---
    dd([
        'Total_Registros_Encontrados' => $distribucion->count(),
        'Contenido_Distribucion' => $distribucion,
        'Labels_Procesados' => $labelsPie,
        'Datos_Procesados' => $datosPie,
        'Sql_Ejecutado' => DB::table('reporte_descargas')
                            ->select('tipo_reporte', DB::raw('count(*) as total'))
                            ->groupBy('tipo_reporte')
                            ->toSql()
    ]);
    // ----------------------------

    // El resto del código no se ejecutará mientras el dd esté arriba
    $totalHoy = DB::table('reporte_descargas')->whereDate('created_at', today())->count();
    $usuariosActivos = DB::table('reporte_descargas')->distinct('cedula')->count();
    $ultimasDescargas = DB::table('reporte_descargas')->orderBy('created_at', 'desc')->limit(5)->get();

    return view('admin.dashboard', compact('labelsPie', 'datosPie', 'totalHoy', 'usuariosActivos', 'ultimasDescargas'));
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
