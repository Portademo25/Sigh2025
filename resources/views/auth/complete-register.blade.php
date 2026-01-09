@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card-header">
    <h5>Bienvenido(a), {{ trim($empleado->nomper) }} {{ trim($empleado->apeper) }}</h5>
</div>
            <div class="card shadow">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Activar Cuenta: {{ $empleado->nomper }} {{ $empleado->apeper }}</h5>
                </div>
                <div class="card-body">
                    <p class="text-muted small">Por seguridad, confirma los siguientes datos de tu expediente para crear tu contraseña.</p>

                    <form method="POST" action="{{ route('auth.store_user') }}">
                        @csrf

                        <div class="mb-3">
                            <label class="form-label">Número de Cédula</label>
                            <input type="text" name="cedula_check" class="form-control @error('cedula_check') is-invalid @enderror" placeholder="Ej: 15666777" required>
                            @error('cedula_check')
                                <span class="invalid-feedback"><strong>{{ $message }}</strong></span>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Fecha de Ingreso a la Institución</label>
                            <input type="date" name="fecha_ingreso_check" class="form-control @error('fecha_ingreso_check') is-invalid @enderror" required>
                            <div class="form-text">Tal como aparece en su expediente o constancia.</div>
                            @error('fecha_ingreso_check')
                                <span class="invalid-feedback"><strong>{{ $message }}</strong></span>
                            @enderror
                        </div>

                        <hr>

                        <div class="mb-3">
                            <label class="form-label">Nueva Contraseña</label>
                            <input type="password" name="password" class="form-control @error('password') is-invalid @enderror" required>
                            @error('password')
                                <span class="invalid-feedback"><strong>{{ $message }}</strong></span>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Confirmar Contraseña</label>
                            <input type="password" name="password_confirmation" class="form-control" required>
                        </div>

                        <div class="d-grid">
                            <button type="submit" class="btn btn-success">Activar mi Usuario</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
