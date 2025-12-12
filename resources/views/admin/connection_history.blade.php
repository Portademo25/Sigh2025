@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h2 class="mb-0">📜 Historial Completo de Conexiones</h2>
                </div>

                <div class="card-body">
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
                                <td>{{ $connection->ip_address }}</td>
                                <td>{{ $connection->created_at->format('d/m/Y H:i:s') }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>

                    {{-- Renderizar los enlaces de paginación --}}
                    <div class="d-flex justify-content-center">
                        {{ $connections->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
