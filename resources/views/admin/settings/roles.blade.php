@extends('layouts.app')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="fw-bold"><i class="bi bi-shield-lock-fill"></i> Control de Roles y Accesos</h3>
            <p class="text-muted small">Cambia los privilegios de los usuarios registrados en el sistema.</p>
        </div>
        <a href="{{ route('admin.settings.index') }}" class="btn btn-secondary btn-sm">
            <i class="bi bi-house"></i> Volver al Panel
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success border-0 shadow-sm mb-3">
            <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
        </div>
    @endif

    @if($errors->has('error'))
        <div class="alert alert-danger border-0 shadow-sm mb-3">
            <i class="bi bi-exclamation-octagon-fill me-2"></i> {{ $errors->first('error') }}
        </div>
    @endif

    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="ps-4 py-3">Usuario</th>
                            <th>Cédula</th>
                            <th>Nivel de Acceso</th>
                            <th class="text-center">Acción</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($usuarios as $user)
                        <tr>
                            <td class="ps-4">
                                <div class="fw-bold">{{ $user->name }}</div>
                                <div class="text-muted small">{{ $user->email }}</div>
                            </td>
                            <td>{{ $user->cedula }}</td>
                            <td>
                                @if($user->rol_id == 1)
                                    <span class="badge rounded-pill bg-danger-subtle text-danger border border-danger">
                                        Administrador
                                    </span>
                                @else
                                    <span class="badge rounded-pill bg-primary-subtle text-primary border border-primary">
                                        Empleado (Consulta)
                                    </span>
                                @endif
                            </td>
                            <td class="text-center">
                                <form action="{{ route('admin.settings.roles.update', $user) }}" method="POST" class="d-flex justify-content-center gap-2">
                                    @csrf
                                    <select name="rol_id" class="form-select form-select-sm" style="width: 140px;">
                                        <option value="1" {{ $user->rol_id == 1 ? 'selected' : '' }}>Admin</option>
                                        <option value="2" {{ $user->rol_id == 2 ? 'selected' : '' }}>Empleado</option>
                                    </select>
                                   <button type="submit" class="btn btn-dark btn-sm btn-save-role" title="Actualizar Rol">
    <i class="bi bi-save"></i>
</button>
                                </form>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card-footer bg-white border-0 py-3">
            {{ $usuarios->links() }}
        </div>
    </div>
</div>

{{-- Importación de SweetAlert2 desde CDN --}}
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Seleccionamos todos los botones que tienen la clase .btn-dark (el botón de guardar)
        // Opcional: Agrégale la clase 'btn-save-role' al botón en tu tabla para mayor precisión
        document.querySelectorAll('.btn-dark').forEach(button => {
            button.addEventListener('click', function(e) {
                // Solo actuamos si el botón está dentro de un formulario de actualización de rol
                const form = this.closest('form');
                if (!form) return;

                e.preventDefault(); // Detenemos el envío automático

                // Obtenemos el nombre del usuario desde la fila de la tabla
                const userName = this.closest('tr').querySelector('.fw-bold').innerText;

                Swal.fire({
                    title: '¿Confirmar cambio de rol?',
                    text: `Estás a punto de cambiar los privilegios de ${userName}.`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#212529',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Sí, actualizar',
                    cancelButtonText: 'Cancelar',
                    reverseButtons: true
                }).then((result) => {
                    if (result.isConfirmed) {
                        // Si el usuario confirma, mostramos un estado de carga y enviamos
                        Swal.fire({
                            title: 'Actualizando...',
                            text: 'Por favor espere',
                            allowOutsideClick: false,
                            didOpen: () => {
                                Swal.showLoading();
                            }
                        });
                        form.submit();
                    }
                });
            });
        });
    });
</script>
@endsection
