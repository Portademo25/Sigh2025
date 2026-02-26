@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card border-success">
                <div class="card-header bg-success text-white d-flex justify-content-between align-items-center">
                    <h2 class="mb-0">🟢 Usuarios Activos (En línea)</h2>
                    <a href="{{ route('admin.users.index') }}" class="btn btn-light btn-sm">
                        ← Tablero de Administración
                    </a>
                </div>

                <div class="card-body">
                    <p class="text-muted">Mostrando usuarios con actividad en los últimos 5 minutos.</p>

                    <table class="table table-hover">
                       {{-- Dentro de la tabla de usuarios activos --}}
                        <thead>
                            <tr>
                                 <th>Usuario</th>
                                  <th>Email</th>
                                  <th>Última actividad</th>
                                  <th>Acciones</th> {{-- Nueva columna --}}
                            </tr>
                        </thead>
                    <tbody>
                        @forelse ($users as $user)
                           <tr>
                            <td>{{ $user->name }}</td>
                             <td>{{ $user->email }}</td>
                              <td>{{ $user->last_seen_at->diffForHumans() }}</td>
                                          <td>
            {{-- No permitirse expulsarse a uno mismo --}}
                                @if($user->id !== Auth::id())
                      <form action="{{ route('admin.users.kick', $user) }}" method="POST" onsubmit="return confirm('¿Expulsar a este usuario inmediatamente?')">
                             @csrf
                                <button type="submit" class="btn btn-sm btn-outline-danger">
                                   Expulsar (Kick)
                               </button>
                        </form>
                       @else
                           <span class="badge bg-secondary">Tú</span>
                       @endif
                             </td>
                         </tr>
                     @empty
        {{-- ... --}}
                    @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
