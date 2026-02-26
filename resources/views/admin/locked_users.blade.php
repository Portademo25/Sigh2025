@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h2 class="mb-0">🔒 Usuarios Bloqueados (Acceso de Administrador)</h2>
                    <a href="{{ route('admin.users.index') }}" class="btn btn-secondary btn-sm"  style="">
                        ← Tablero de Administración
                    </a>
                </div>

                <div class="card-body">
                    {{-- Mensaje de éxito después de desbloquear --}}
                    @if (session('success'))
                        <div class="alert alert-success" role="alert">
                            {{ session('success') }}
                        </div>
                    @endif

                    @if ($lockedUsers->isEmpty())
                        <div class="alert alert-info">
                            No hay usuarios bloqueados actualmente.
                        </div>
                    @else
                        <table class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Nombre</th>
                                    <th>Email</th>
                                    <th>Estado</th>
                                    <th>Acción</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($lockedUsers as $user)
                                <tr>
                                    <td>{{ $user->id }}</td>
                                    <td>{{ $user->name }}</td>
                                    <td>{{ $user->email }}</td>
                                    <td>
                                        <span class="badge bg-danger text-white">Bloqueado</span>
                                    </td>
                                    <td>
                                        <form action="{{ route('admin.users.unlock', $user) }}" method="POST" onsubmit="return confirm('¿Estás seguro de que quieres DESBLOQUEAR a {{ $user->name }}?');">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-success">
                                                Desbloquear
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>


                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
