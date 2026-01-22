<div class="table-responsive">
    <table class="table table-hover align-middle mb-0">
        <thead class="bg-light text-muted small text-uppercase">
            <tr>
                <th class="ps-4" style="width: 150px;">Cédula</th>
                <th>Datos del Trabajador</th>
                <th>Correo Institucional</th>
                <th class="text-end pe-4">Acciones</th>
            </tr>
        </thead>
        <tbody>
            @forelse($users as $user)
            <tr>
                <td class="ps-4 fw-bold">{{ $user->cedula }}</td>
                <td>
                    <div class="fw-bold">{{ $user->name }}</div>
                    <div class="small text-muted">{{ $user->cargo ?? 'Personal' }}</div>
                </td>
                <td>
                    <span id="email-display-{{ $user->id }}" class="text-primary border-bottom border-primary border-opacity-25">
                        {{ $user->email }}
                    </span>
                </td>
                <td class="text-end pe-4">
                    <button type="button"
                            class="btn btn-sm btn-light border"
                            onclick="window.editEmail({{ $user->id }}, '{{ $user->email }}', '{{ $user->name }}')">
                        <i class="bi bi-pencil-square me-1"></i> Editar
                    </button>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="4" class="text-center py-4 text-muted">No se encontraron resultados</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="card-footer bg-white border-top-0 d-flex justify-content-between align-items-center py-3" id="user-pagination">
    <div class="small text-muted">
        Mostrando {{ $users->firstItem() }} - {{ $users->lastItem() }} de {{ $users->total() }}
    </div>
    <div>
        {!! $users->links() !!}
    </div>
</div>
