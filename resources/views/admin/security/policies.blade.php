@extends('layouts.app')

@section('content')
<div class="container py-4">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('admin.security.index') }}">Seguridad</a></li>
            <li class="breadcrumb-item active">Editar Políticas</li>
        </ol>
    </nav>

    <div class="card shadow-sm border-0">
        <div class="card-header bg-white py-3">
            <h5 class="mb-0 fw-bold"><i class="bi bi-shield-lock me-2 text-primary"></i>Configuración de Políticas de Acceso</h5>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.security.policies.update') }}" method="POST">
                @csrf
                <div class="row g-4">
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Máximo de intentos de inicio de sesión</label>
                        <p class="small text-muted">Número de fallos permitidos antes de bloquear la cuenta temporalmente.</p>
                        <select name="max_attempts" class="form-select">
                            <option value="3">3 Intentos (Recomendado)</option>
                            <option value="5">5 Intentos</option>
                            <option value="10">10 Intentos</option>
                        </select>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-bold">Duración del bloqueo (Minutos)</label>
                        <p class="small text-muted">Tiempo que el usuario deberá esperar tras superar los intentos.</p>
                        <input type="number" name="lockout_time" class="form-control" value="15">
                    </div>

                    <div class="col-12"><hr></div>

                    <div class="col-md-6">
                        <label class="form-label fw-bold">Expiración de Sesión Inactiva</label>
                        <p class="small text-muted">Cierra la sesión automáticamente si no hay actividad.</p>
                        <div class="input-group">
                            <input type="number" name="session_lifetime" class="form-control" value="120">
                            <span class="input-group-text">Minutos</span>
                        </div>
                    </div>

                    

                <div class="mt-5 pt-3 border-top text-end">
                    <a href="{{ route('admin.security.index') }}" class="btn btn-light px-4">Cancelar</a>
                    <button type="submit" class="btn btn-primary px-4">
                        <i class="bi bi-save me-2"></i>Aplicar Cambios
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
