@extends('layouts.app')

@section('content')
<div class="container mt-4">
    {{-- Encabezado con Botón de Regreso --}}
    <div class="row mb-4">
        <div class="col-12 d-flex justify-content-between align-items-center">
            <div>
                <h2 class="text-secondary font-weight-bold mb-0">
                    <i class="fas fa-cogs mr-2"></i>Tablero de Administración
                </h2>
                <p class="text-muted mb-0">Gestión de seguridad, estados de cuenta y monitoreo de red.</p>
            </div>
            <a href="{{ route('home') }}" class="btn btn-outline-secondary shadow-sm">
                <i class="fas fa-chevron-left mr-1"></i> Volver al Menú
            </a>
        </div>
        <div class="col-12">
            <hr>
        </div>
    </div>

    <div class="row">
        {{-- Tarjeta 1: Usuarios Bloqueados --}}
        <div class="col-md-4 mb-4">
            <div class="card text-white bg-danger shadow-sm h-100">
                <div class="card-header font-weight-bold">Usuarios Bloqueados</div>
                <div class="card-body">
                    <h5 class="card-title display-4" style="font-size: 1.8rem;">{{ $lockedUsersCount }} Cuentas</h5>
                    <p class="card-text">Usuarios que excedieron intentos de login.</p>
                </div>
                <div class="card-footer bg-transparent border-0">
                    <a href="{{ route('admin.users.locked') }}" class="btn btn-sm btn-light btn-block">Ir a Desbloquear</a>
                </div>
            </div>
        </div>

        {{-- Tarjeta 2: Historial de Conexiones --}}
        <div class="col-md-4 mb-4">
            <div class="card text-white bg-primary shadow-sm h-100">
                <div class="card-header font-weight-bold">Historial de Conexiones</div>
                <div class="card-body">
                    <h5 class="card-title display-4" style="font-size: 1.8rem;">{{ $totalUsers }} Totales</h5>
                    <p class="card-text">Auditoría detallada de IPs y accesos.</p>
                </div>
                <div class="card-footer bg-transparent border-0">
                    <a href="{{ route('admin.users.connections') }}" class="btn btn-sm btn-light btn-block">Ver Historial</a>
                </div>
            </div>
        </div>

        {{-- Tarjeta 3: Usuarios Activos --}}
        <div class="col-md-4 mb-4">
          <div class="card text-white bg-success shadow-sm h-100">
               <div class="card-header font-weight-bold">Usuarios en Línea</div>
                  <div class="card-body">
                      <h5 class="card-title display-4" style="font-size: 1.8rem;">{{ $activeUsersCount }} Activos</h5>
                      <p class="card-text">Sesiones concurrentes detectadas ahora.</p>
                 </div>
                 <div class="card-footer bg-transparent border-0">
                      <a href="{{ route('admin.users.active') }}" class="btn btn-sm btn-light btn-block">Ver quiénes son</a>
                 </div>
             </div>
        </div>
    </div>

    {{-- Conexiones Recientes --}}
    <div class="card shadow-sm mt-2">
        <div class="card-header bg-white font-weight-bold">
            <i class="fas fa-history mr-2 text-primary"></i>Últimas 5 Conexiones
        </div>
        <div class="card-body p-0">
            <ul class="list-group list-group-flush">
                @forelse ($recentConnections as $connection)
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <span>
                            <i class="fas fa-user-circle mr-2 text-muted"></i>
                            <strong>{{ $connection->user->name ?? 'Usuario Eliminado' }}</strong>
                            <small class="text-muted">({{ $connection->user->email ?? 'N/A' }})</small>
                        </span>
                        <span class="badge badge-light border p-2" style="color: black;">
                            IP: {{ $connection->ip_address }} | {{ $connection->created_at->diffForHumans() }}
                        </span>
                    </li>
                @empty
                    <li class="list-group-item text-center text-muted">No hay registros recientes.</li>
                @endforelse
            </ul>
        </div>
    </div>
</div>
@endsection
