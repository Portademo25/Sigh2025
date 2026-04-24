@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="card shadow border-0">
       <div class="card-header bg-dark text-white py-3">
    <div class="d-flex align-items-center justify-content-between">
        <h5 class="mb-0">
            <i class="bi bi-file-earmark-bar-graph me-2"></i> Control de Planillas ARC
        </h5>

        <a href="{{ route('admin.reportes.menu') }}" class="btn btn-sm btn-outline-light">
            <i class="bi bi-arrow-left-circle me-1"></i> Regresar al Menú
        </a>
    </div>
</div>
        <div class="card-body">
            <form action="{{ route('admin.reportes.arc') }}" method="GET" class="row g-3 mb-4">
                <div class="col-md-8">
                    <input type="text" name="buscar" class="form-control" placeholder="Buscar por Cédula o Nombre..." value="{{ request('buscar') }}">
                </div>
                <div class="col-md-4">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="bi bi-search"></i> Buscar Empleado
                    </button>
                </div>
            </form>

            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Cédula</th>
                            <th>Nombre Completo</th>
                            <th>Año Fiscal</th>
                            <th class="text-center">Acción</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($personal as $p)
                        <tr>
                            <td>{{ $p->cedper }}</td>
                            <td>{{ $p->nomper }} {{ $p->apeper }}</td>
                            <td>
                                <select id="ano_{{ $p->cedper }}" class="form-select form-select-sm d-inline-block w-auto">
                                    @foreach($anios as $a)
                                        <option value="{{ $a }}">{{ $a }}</option>
                                    @endforeach
                                </select>
                            </td>
                            <td class="text-center">
                            <button type="button"
        onclick="descargarArcAdmin(event, '{{ $p->cedper }}')"
        class="btn btn-sm btn-success">
    <i class="bi bi-file-earmark-pdf"></i> Generar ARC
</button>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>


<script>
function descargarArcAdmin(event, cedula) {
    // 1. Prevenir que se refresque (por seguridad extra)
    if (event) {
        event.preventDefault();
        event.stopPropagation();
    }

    const select = document.getElementById('ano_' + cedula);
    if (!select) return;

    const ano = select.value;

    // 2. Construir la URL con el nombre de la ruta que ajustamos en web.php
    let url = "{{ route('admin.arc.generar', [':ced', ':ano']) }}";
    url = url.replace(':ced', cedula).replace(':ano', ano);

    // 3. Abrir en pestaña nueva
    window.open(url, '_blank');
}
</script>
@endsection
