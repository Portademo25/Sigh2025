@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0">Dashboard - Administrativo</h4>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="card text-white bg-success mb-3">
                                <div class="card-body">
                                    <h5 class="card-title">Gestión de Usuarios</h5>
                                    <p class="card-text">Administrar usuarios del sistema</p>
                                    <a href="{{ route('admin.users.index') }}" class="btn btn-light">Ir a</a>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card text-white bg-info mb-3">
                                <div class="card-body">
                                    <h5 class="card-title">Reportes</h5>
                                    <p class="card-text">Ver reportes del sistema</p>
                                    <a href="#" class="btn btn-light">Ir a</a>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card text-white bg-warning mb-3">
                                <div class="card-body">
                                    <h5 class="card-title">Configuración</h5>
                                    <p class="card-text">Configurar el sistema</p>
                                    <a href="{{ route('admin.settings.index') }}" class="btn btn-light">Ir a</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
