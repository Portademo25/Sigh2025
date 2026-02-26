@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h2 class="mb-0">📜 Historial de Conexiones</h2>
                    <a href="{{ route('admin.users.index') }}" class="btn btn-secondary btn-sm">
                        ← Tablero de Administración
                    </a>
                </div>

                <div class="card-body">
                    <form action="{{ route('admin.users.connections') }}" method="GET" class="mb-4">
                        <div class="input-group">
                            <input type="text" name="search" class="form-control"
                                   placeholder="Buscar por nombre, email o IP..."
                                   value="{{ $search }}">
                            <button class="btn btn-primary" type="submit">Buscar</button>
                            @if($search)
                                <a href="{{ route('admin.users.connections') }}" class="btn btn-outline-secondary">Limpiar</a>
                            @endif
                        </div>
                    </form>

                    <table class="table table-striped table-hover">
                        <thead>
                            <tr>
                                <th>Usuario</th>
                                <th>Email</th>
                                <th>IP de Conexión</th>
                                <th>Fecha y Hora</th>
                            </tr>
                        </thead>
                        <tbody>
                          @foreach ($connections as $connection)
                <tr>
    <td>{{ $connection->user->name ?? 'Usuario Eliminado' }}</td>
    <td>{{ $connection->user->email ?? 'N/A' }}</td>
    <td><code>{{ $connection->ipconexion }}</code></td>
    <td>
        {{-- Combinamos fecha y hora --}}
        {{ \Carbon\Carbon::parse($connection->fechaconexion)->format('d/m/Y') }}
        <span class="text-muted">|</span>
        {{ $connection->horaconexion }}
    </td>
</tr>
@endforeach
                        </tbody>
                    </table>

                    <div class="d-flex justify-content-center mt-3">
                        {{ $connections->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
