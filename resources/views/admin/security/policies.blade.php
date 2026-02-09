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
            <form action="{{ route('admin.security.policies.save') }}" method="POST" enctype="multipart/form-data">
                @csrf

                <div class="row">
                    <div class="col-md-6">
                        <h6 class="fw-bold mb-3 text-secondary"><i class="bi bi-person-badge me-2"></i>Acceso y Sesión</h6>

                        <div class="mb-3">
                            <label class="form-label fw-bold small">Máximo de Intentos Fallidos</label>
                            <select name="intentos_maximos" class="form-select">
                                <option value="3" {{ ($config['intentos_maximos'] ?? '') == '3' ? 'selected' : '' }}>3 Intentos (Recomendado)</option>
                                <option value="5" {{ ($config['intentos_maximos'] ?? '') == '5' ? 'selected' : '' }}>5 Intentos</option>
                                <option value="10" {{ ($config['intentos_maximos'] ?? '') == '10' ? 'selected' : '' }}>10 Intentos</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold small">Duración del Bloqueo (Minutos)</label>
                            <input type="number" name="duracion_bloqueo" class="form-control"
                                   value="{{ $config['duracion_bloqueo'] ?? 15 }}" min="1">
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold small">Tiempo de Expiración de Sesión (Minutos)</label>
                            <input type="number" name="expiracion_sesion" class="form-control"
                                   value="{{ $config['expiracion_sesion'] ?? 120 }}" min="1">
                        </div>
                    </div>

                    <div class="col-md-6 border-start">
                        <h6 class="fw-bold mb-3 text-secondary"><i class="bi bi-safe2 me-2"></i>Certificado SSL (Cifrado)</h6>

                        <div class="alert {{ request()->isSecure() ? 'alert-light-success text-success' : 'alert-light-warning text-warning' }} border-0 small py-2">
                            <i class="bi {{ request()->isSecure() ? 'bi-patch-check-fill' : 'bi-exclamation-triangle-fill' }} me-1"></i>
                            Estado actual: <strong>{{ request()->isSecure() ? 'Conexión Segura (HTTPS)' : 'Conexión no cifrada' }}</strong>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold small">Archivo de Certificado (.crt / .pem)</label>
                            <input type="file" name="ssl_certificate" class="form-control form-control-sm">
                            @if(isset($config['ssl_certificate_path']))
                                <div class="form-text text-success"><i class="bi bi-check-circle-fill"></i> Certificado cargado anteriormente</div>
                            @endif
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold small">Llave Privada (.key)</label>
                            <input type="file" name="ssl_key" class="form-control form-control-sm">
                            @if(isset($config['ssl_key_path']))
                                <div class="form-text text-success"><i class="bi bi-check-circle-fill"></i> Llave privada cargada anteriormente</div>
                            @endif
                        </div>

                        <div class="p-2 bg-light rounded small text-muted">
                            <i class="bi bi-info-circle me-1"></i> Nota: La instalación completa del SSL depende de la configuración de su servidor web (Nginx/Apache).
                        </div>
                    </div>
                </div>

                <div class="text-end mt-4 pt-3 border-top">
                    <button type="submit" class="btn btn-primary px-5">
                        <i class="bi bi-save me-1"></i> Guardar Todos los Cambios
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
