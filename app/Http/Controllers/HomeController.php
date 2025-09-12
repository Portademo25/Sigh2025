<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;


class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    // verificar el role del usuario autenticado y redirigir a la vista correspondiente 
    public function index()
    {   
        if (Auth::check()){
            $user = Auth::user();
           
            if ($user->hasRole('Administrador')) {
                return redirect()->route('admin.dashboard');
            } elseif ($user->hasRole('Telento Humano')) {
                return redirect()->route('talenthuman.dashboard');
            } elseif ($user->hasRole('Empleado')) {
                return redirect()->route('employee.dashboard');
            }else{
                return view('home');
            }
        }
        return redirect('/login');
    }

    public function adminDashboard()
    {
        return view('admin.dashboard');
    }

    public function talenthumanDashboard()
    {
        return view('talenthuman.dashboard');
    }

    public function employeeDashboard()
    {
        return view('employee.dashboard');
    }

}
