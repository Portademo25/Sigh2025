@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    <div class="row mb-4">
    <div class="col-12">
        {{-- Navegación Breadcrumb --}}
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-2">
                <li class="breadcrumb-item">
                    <a href="{{ route('admin.settings.index') }}" class="text-decoration-none">
                        <i class="bi bi-house-door me-1"></i>Configuración del Sistema
                    </a>
                </li>
                <li class="breadcrumb-item active" aria-current="page">Centro de Seguridad</li>
            </ol>
        </nav>

        {{-- Título y Descripción --}}
        <div class="d-flex align-items-center">
            <h2 class="fw-bold mb-0">
                <i class="bi bi-shield-check text-primary me-2"></i>Centro de Seguridad
            </h2>
        </div>
        <p class="text-muted mt-2">Gestione la integridad del portal, el acceso de los empleados y la auditoría de procesos.</p>
    </div>
</div>

    <div class="row g-4">
        {{-- Columna Izquierda: Disponibilidad y Políticas --}}
        <div class="col-lg-4">
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-white py-3 fw-bold">
                    <i class="bi bi-broadcast me-2"></i>Disponibilidad del Sistema
                </div>
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <div>
                            <h6 class="mb-0 fw-bold">Modo Mantenimiento</h6>
                            <small class="text-muted">Desactiva el acceso al portal para usuarios no administrativos.</small>
                        </div>
                        <div class="form-check form-switch">
                            <form action="{{ route('admin.security.toggle-maintenance') }}" method="POST">
                                @csrf
                                <button type="submit" class="btn {{ ($config['site_offline'] ?? '0') == '1' ? 'btn-danger' : 'btn-outline-secondary' }} btn-sm">
                                    @if(($config['site_offline'] ?? '0') == '1')
                                        <i class="bi bi-pause-btn-fill me-1"></i> Desactivar Mantenimiento
                                    @else
                                        <i class="bi bi-play-btn-fill me-1"></i> Activar Mantenimiento
                                    @endif
                                </button>
                            </form>
                        </div>
                    </div>

                    @if(($config['site_offline'] ?? '0') == '1')
                        <div class="alert alert-warning py-2 small mb-0">
                            <i class="bi bi-exclamation-triangle me-1"></i> El sistema se encuentra actualmente fuera de línea para los usuarios.
                        </div>
                    @endif
                </div>
            </div>

            <div class="card shadow-sm border-0">
                <div class="card-header bg-white py-3 fw-bold">
                    <i class="bi bi-lock me-2"></i>Políticas de Acceso
                </div>
                <div class="card-body">
                    <ul class="list-group list-group-flush small">
                        <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                            Bloqueo por intentos fallidos
                            <span class="badge bg-primary rounded-pill">{{ $config['intentos_maximos'] ?? '3' }} intentos</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                            Cierre de sesión automático
                            <span class="badge bg-secondary rounded-pill">{{ $config['duracion_bloqueo'] ?? '15' }} min</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                            Cierre de sesión automático por inactividad
                            <span class="badge bg-secondary rounded-pill"> {{ $config['expiracion_sesion'] ?? '120' }} min</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                            Cifrado de datos (SSL)
                             @if(request()->isSecure())
                                <span class="text-success"><i class="bi bi-shield-fill-check"></i> Activo</span>
                             @else
                                <span class="text-warning"><i class="bi bi-shield-slash"></i> No seguro</span>
                             @endif
                        </li>
                    </ul>
                    <a href="{{ route('admin.security.policies') }}" class="btn btn-light btn-sm w-100 mt-3 border">
                        Editar Políticas
                    </a>
                </div>
            </div>
        </div>

        {{-- Columna Derecha: Tabla Dinámica de Logs --}}
        <div class="col-lg-8">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                    <span class="fw-bold"><i class="bi bi-journal-text me-2"></i>Eventos Críticos de Seguridad</span>
                    {{-- Puedes enlazar esto a una ruta de exportación más adelante --}}
                    <a href="{{ route('admin.security.download') }}" class="btn btn-sm btn-outline-secondary">
    <i class="bi bi-download me-1"></i> Descargar Log Completo
</a>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr class="small text-uppercase">
                                    <th class="ps-3">Evento</th>
                                    <th>Usuario</th>
                                    <th>Dirección IP</th>
                                    <th>Fecha y Hora</th>
                                    <th class="text-end pe-3">Gravedad</th>
                                </tr>
                            </thead>
                            <tbody class="small">
                                @forelse($eventos as $log)
                                    <tr>
                                        <td class="ps-3">
                                            @if($log->severity == 'Alta' || $log->severity == 'Crítica')
                                                <i class="bi bi-exclamation-octagon text-danger me-2"></i>
                                            @elseif($log->severity == 'Media')
                                                <i class="bi bi-exclamation-triangle text-warning me-2"></i>
                                            @else
                                                <i class="bi bi-info-circle text-info me-2"></i>
                                            @endif
                                            {{ $log->event }}
                                        </td>
                                        <td>{{ $log->user_identifier ?? 'Invitado' }}</td>
                                        <td><code>{{ $log->ip_address }}</code></td>
                                        <td>
                                            {{ $log->created_at->format('d/m/Y h:i A') }}
                                            <br><small class="text-muted">({{ $log->created_at->diffForHumans() }})</small>
                                        </td>
                                        <td class="text-end pe-3">
                                            <span class="badge bg-{{ $log->getSeverityColor() }}">
                                                {{ $log->severity }}
                                            </span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center py-4 text-muted">
                                            <i class="bi bi-check-circle me-1"></i> No hay eventos registrados.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="card-footer bg-white text-center py-3">
                    <p class="small text-muted mb-0">Mostrando los últimos {{ $eventos->count() }} eventos de seguridad registrados localmente.</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Footer: Mantenimiento de BD --}}
   <div class="row mt-4">
    <div class="col-md-12">
        <div class="card shadow-sm border-0 bg-dark text-white">
            <div class="card-body d-flex justify-content-between align-items-center">
                <div>
                    <h5 class="mb-1"><i class="bi bi-terminal me-2"></i>Mantenimiento de Base de Datos</h5>
                    <p class="small mb-0 opacity-75">Optimice las tablas locales y purgue archivos temporales para mejorar el rendimiento.</p>
                </div>
                <div class="btn-group">
                    <form action="{{ route('admin.security.optimize') }}" method="POST" class="d-inline form-mantenimiento">
                        @csrf
                        <button type="submit" class="btn btn-outline-light btn-sm rounded-start btn-mantenimiento">
                            <i class="bi bi-gear-fill me-1"></i> Optimizar Tablas
                        </button>
                    </form>

                    <form action="{{ route('admin.security.cache') }}" method="POST" class="d-inline form-mantenimiento">
                        @csrf
                        <button type="submit" class="btn btn-outline-light btn-sm rounded-end btn-mantenimiento">
                            <i class="bi bi-trash3-fill me-1"></i> Limpiar Caché
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
  </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    document.querySelectorAll('.form-mantenimiento').forEach(form => {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const btn = this.querySelector('.btn-mantenimiento');
            const textoOriginal = btn.innerHTML;

            Swal.fire({
                title: '¿Ejecutar mantenimiento?',
                text: "El sistema podría tardar unos segundos en responder.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#0d6efd',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Sí, ejecutar',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Deshabilitar botón y mostrar spinner
                    btn.disabled = true;
                    btn.innerHTML = `<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Procesando...`;
                    
                    // Enviar el formulario
                    this.submit();
                }
            });
        });
    });
</script>
@endsection

