@extends('layouts.app')

@section('content')
<div class="container">
    <h2>Tablero de Administración</h2>
    <hr>

    <div class="row">
        {{-- Tarjeta 1: Usuarios Bloqueados --}}
        <div class="col-md-4 mb-4">
            <div class="card text-white bg-danger">
                <div class="card-header">Usuarios Bloqueados</div>
                <div class="card-body">
                    <h5 class="card-title">{{ $lockedUsersCount }} Cuentas</h5>
                    <p class="card-text">Pendientes de desbloqueo manual.</p>
                    <a href="{{ route('admin.users.locked') }}" class="btn btn-sm btn-light">Ir a Desbloquear</a>
                </div>
            </div>
        </div>

        {{-- Tarjeta 2: Historial de Conexiones --}}
        <div class="col-md-4 mb-4">
            <div class="card text-white bg-primary">
                <div class="card-header">Historial de Conexiones</div>
                <div class="card-body">
                    <h5 class="card-title">{{ $totalUsers }} Usuarios Totales</h5>
                    <p class="card-text">Ver detalle de IPs y tiempos de conexión.</p>
                    <a href="{{ route('admin.users.connections') }}" class="btn btn-sm btn-light">Ver Historial</a>
                </div>
            </div>
        </div>

        {{-- Tarjeta 3: Más datos... --}}
        <div class="col-md-4 mb-4">
            <div class="card text-white bg-success">
                <div class="card-header">Usuarios Activos</div>
                <div class="card-body">
                    <h5 class="card-title">Próximamente</h5>
                    <p class="card-text">Sección para ver usuarios activos en tiempo real.</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Conexiones Recientes en el Dashboard --}}
    <div class="card mt-4">
        <div class="card-header">Últimas 5 Conexiones</div>
        <div class="card-body">
            <ul class="list-group">
                @foreach ($recentConnections as $connection)
                    <li class="list-group-item">
                        **{{ $connection->user->name ?? 'Usuario Eliminado' }}** ({{ $connection->user->email ?? 'N/A' }}) se conectó desde IP **{{ $connection->ip_address }}** el {{ $connection->created_at->format('d/m/Y H:i:s') }}.
                    </li>
                @endforeach
            </ul>
        </div>
    </div>

</div>
@endsection
