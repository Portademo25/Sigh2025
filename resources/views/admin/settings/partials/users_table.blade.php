<div class="table-responsive">
    <table class="table table-hover align-middle mb-0">
        <thead class="bg-light">
            <tr>
                <th>Trabajador</th>
                <th>Cédula</th>
                <th>Correo Electrónico</th>
                <th class="text-center">Acciones</th>
            </tr>
        </thead>
        <tbody>
            @forelse($users as $user)
            <tr>
                <td>
                    <div class="fw-bold">{{ $user->name }}</div>
                    <small class="text-muted text-uppercase">{{ $user->codper }}</small>
                </td>
                <td>{{ $user->cedula }}</td>
                <td>
                    <span id="email-display-{{ $user->id }}" class="text-lowercase">{{ $user->email }}</span>
                </td>
                <td class="text-center">
                    <button class="btn btn-outline-primary btn-sm px-3 shadow-sm d-inline-flex align-items-center"
        onclick="editEmail('{{ $user->id }}', '{{ $user->email }}', '{{ $user->name }}')">
    <i class="bi bi-envelope-plus me-2"></i> Editar Correo
</button>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="4" class="text-center p-4 text-muted">No se encontraron trabajadores registrados.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="d-flex justify-content-center mt-3" id="user-pagination">
    {{ $users->links() }}
</div>
