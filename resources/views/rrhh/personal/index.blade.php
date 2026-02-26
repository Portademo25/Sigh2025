@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center bg-dark text-white p-3 rounded-top shadow-sm">
        <div class="d-flex align-items-center">
            <i class="fas fa-file-invoice fa-lg me-3 text-info"></i>
            <h5 class="mb-0 fw-bold">Control de Planillas ARC</h5>
        </div>
        <a href="{{ route('rrhh.dashboard') }}" class="btn btn-outline-light btn-sm px-3">
            <i class="fas fa-arrow-circle-left me-1"></i> Regresar al Menú
        </a>
    </div>

    <div class="bg-white p-3 border-bottom shadow-sm">
        <form action="{{ route('rrhh.personal.index') }}" method="GET" class="row g-2">
            <div class="col-md-10">
                <input type="text" name="search" class="form-control"
                       placeholder="Buscar por Cédula o Nombre..." value="{{ $search }}">
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-primary w-100 fw-bold">
                    <i class="fas fa-search me-1"></i> Buscar Empleado
                </button>
            </div>
        </form>
    </div>

    <div class="card border-0 shadow-sm rounded-0 rounded-bottom">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-striped table-hover align-middle mb-0">
                    <thead class="bg-primary text-white">
                        <tr>
                            <th class="ps-4">Cédula</th>
                            <th>Nombre Completo</th>
                            <th class="text-center">Año Fiscal</th>
                            <th class="text-center">Acción</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($personal as $p)
                        <tr>
                            <td class="ps-4 fw-bold">
                                <span class="badge bg-primary px-3 py-2">{{ $p->cedper }}</span>
                            </td>
                            <td class="text-uppercase fw-semibold text-secondary">
                                {{ $p->nomper }} {{ $p->apeper }}
                            </td>
                            <td style="width: 150px;">
                                <select class="form-select form-select-sm border-primary text-center fw-bold text-primary" id="ano_{{ $p->cedper }}">
                                    @php $currentYear = date('Y'); @endphp
                                    @for($i=0; $i<5; $i++)
                                        <option value="{{ $currentYear - $i }}">{{ $currentYear - $i }}</option>
                                    @endfor
                                </select>
                            </td>
                            <td class="text-center">
                                <button type="button"
                                        onclick="generarARC('{{ $p->cedper }}')"
                                        class="btn btn-success btn-sm px-4 fw-bold shadow-sm">
                                    <i class="fas fa-file-pdf me-1"></i> Generar ARC
                                </button>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <div class="card-footer bg-white d-flex justify-content-center py-3">
            {{ $personal->appends(['search' => $search])->links('pagination::bootstrap-5') }}
        </div>
    </div>
</div>

<script>
function generarARC(cedula) {
    const selectAno = document.getElementById('ano_' + cedula);
    if (!selectAno) return;
    const ano = selectAno.value;

    let url = "{{ route('rrhh.arc.generar', [':ced', ':ano']) }}";
    url = url.replace(':ced', cedula).replace(':ano', ano);

    // MÉTODO DEL ENLACE FANTASMA (Infallible para descargas)
    const link = document.createElement('a');
    link.href = url;
    link.setAttribute('download', `ARC_${cedula}_${ano}.pdf`);
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
}
</script>

<style>
    .table thead th { font-size: 0.85rem; letter-spacing: 0.5px; border: none; }
    .table tbody td { font-size: 0.9rem; border-color: #f0f0f0; }
    .bg-primary { background-color: #0d6efd !important; }
    .btn-success { background-color: #198754; border: none; }
</style>
@endsection
