@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card shadow">
                <div class="card-header bg-dark text-white d-flex justify-content-between">
                    <h4 class="mb-0">⚙️ Configuración del Sistema</h4>
                    <a href="{{ route('admin.dashboard') }}" class="btn btn-outline-light btn-sm">Regresar</a>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.settings.update') }}" method="POST">
                        @csrf
                        <div class="row">
                            <div class="col-md-4 border-end">
                                <div class="nav flex-column nav-pills" id="v-pills-tab" role="tablist">
                                    <button class="nav-link" data-bs-toggle="pill" data-bs-target="#security"><a href="{{ route('admin.security.index') }}" class="btn btn-light">🛡️ Seguridad</a></button>
                                    <button class="nav-link" data-bs-toggle="pill" data-bs-target="#general">💻 General</button>
                                    <button class="nav-link" data-bs-toggle="pill" data-bs-target="#roles"><a href="{{ route('admin.settings.roles') }}" class="btn btn-light">👥 Roles y Permisos</a></button>
                                    <button class="nav-link" data-bs-toggle="pill" data-bs-target="#sigesp"><a href="{{ route('admin.settings.sigesp') }}" class="btn btn-light">🔗 Sincronización con SIGESP</a></button>
                                    <button class="nav-link" data-bs-toggle="pill" data-bs-target="#mail"><a href="{{ route('admin.settings.mail') }}" class="btn btn-light"> Configuracion de para Enviar Correo</a></button>
                                </div>
                            </div>

                            <div class="col-md-8">
                                {{-- <div class="tab-content p-3">
                                    <div class="tab-pane fade show active" id="security">
                                        <h5>Parámetros de Acceso</h5>
                                        <hr>
                                        <div class="mb-3">
                                            <label class="form-label">Intentos máximos de Login antes de bloquear</label>
                                            <input type="number" name="max_attempts" class="form-control"
                                                   value="{{ $settings['max_attempts'] ?? '3' }}">
                                            <small class="text-muted">Actualmente configurado en 3 intentos.</small>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Minutos para considerar usuario "Activo"</label>
                                            <input type="number" name="active_threshold" class="form-control" value="5">
                                        </div>
                                       <div class="mb-3">
                                              <label class="form-label">Nombre de la Aplicación</label>
                                              <input type="text" name="app_name" class="form-control"
                                              value="{{ $settings['app_name'] ?? config('app.name') }}">
                                        </div>

                                    </div> --}}

                                    {{-- <div class="tab-pane fade" id="general">
                                        <h5>Información del Sitio</h5>
                                        <hr>
                                        <div class="mb-3">
                                            <label class="form-label">Nombre de la Institución/App</label>
                                            <input type="text" name="app_name" class="form-control" value="{{ config('app.name') }}">
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Correo de Soporte Técnico</label>
                                            <input type="email" name="support_email" class="form-control" placeholder="soporte@ejemplo.com">
                                        </div>
                                    </div>

                                    <div class="tab-pane fade" id="roles">
                                        <h5>Gestión de Accesos</h5>
                                        <hr>
                                        <p>Configura qué puede hacer cada rol en el sistema.</p>
                                        <a href="" class="btn btn-outline-primary btn-sm">Gestionar Roles con Spatie</a>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="card-footer bg-transparent text-end border-top-0">
                            <button type="submit" class="btn btn-primary">Guardar Cambios</button>
                        </div> --}}
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
