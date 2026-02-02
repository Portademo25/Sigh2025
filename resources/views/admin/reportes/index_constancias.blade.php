@extends('layouts.app')



@section('content')
<div class="container-fluid">
    <div class="card shadow mb-4">
    <div class="card-header py-3 bg-primary d-flex justify-content-between align-items-center">
        <h6 class="m-0 font-weight-bold text-white">
            <i class="fas fa-history mr-2"></i>Historial de Constancias Emitidas (Auditoría)
        </h6>

        <a href="{{ route('admin.reportes.menu') }}" class="btn btn-light btn-sm shadow-sm text-primary font-weight-bold">
            <i class="fas fa-arrow-left mr-1"></i> Volver al Menú
        </a>
    </div>

    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-hover" id="dataTable" width="100%" cellspacing="0">
                <thead class="bg-light">
                    <tr>
                        <th>Fecha/Hora</th>
                        <th>Cédula</th>
                        <th>Trabajador</th>
                        <th>Unidad</th>
                        <th>Sueldo (Bs.)</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($reporte as $item)
                    <tr>
                        <td class="align-middle">{{ \Carbon\Carbon::parse($item->fecha_generacion)->format('d/m/Y h:i A') }}</td>
                        <td class="align-middle">{{ number_format($item->cedula, 0, '', '.') }}</td>
                        <td class="align-middle">
                            <div class="font-weight-bold">{{ $item->nombre_completo }}</div>
                            <small class="text-muted">{{ $item->cargo }}</small>
                        </td>
                        <td class="align-middle small">{{ $item->unidad }}</td>
                        <td class="align-middle text-right font-weight-bold">
                            {{ number_format($item->sueldo_integral, 2, ',', '.') }}
                        </td>
                        <td class="text-center align-middle">
                            <a href="{{ route('constancia.verificar', $item->token) }}"
                               target="_blank"
                               class="btn btn-sm btn-info shadow-sm">
                               <i class="fas fa-search"></i> Validar
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center text-muted">No hay registros de constancias generadas aún.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4 d-flex justify-content-center">
            {{ $reporte->links() }}
        </div>
    </div>
</div>
</div>
@endsection
