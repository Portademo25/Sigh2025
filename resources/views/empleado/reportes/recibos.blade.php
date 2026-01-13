@extends('layouts.app')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4><i class="bi bi-file-earmark-pdf text-danger"></i> Historial de Recibos de Pago</h4>
        <a href="{{ route('empleado.dashboard') }}" class="btn btn-outline-secondary btn-sm">Volver</a>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="ps-4">Periodo</th>
                        <th>Desde</th>
                        <th>Hasta</th>
                        <th>Monto Neto</th>
                        <th class="text-center">Acción</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($recibos as $recibo)
                    <tr>
                        <td class="ps-4">
                            <span class="fw-bold">Quincena: {{ $recibo->codperi }}</span>
                            <div class="small text-muted">Nómina: {{ $recibo->codnom }}</div>
                        </td>
                        <td>{{ \Carbon\Carbon::parse($recibo->fecdesper)->format('d/m/Y') }}</td>
                        <td>{{ \Carbon\Carbon::parse($recibo->fechasper)->format('d/m/Y') }}</td>
                        <td class="fw-bold text-success">
                           {{ number_format($recibo->monnetres, 2, ',', '.') }}
                        </td>
                        <td class="text-center">
                           <a href="{{ route('empleado.reportes.recibo_pdf', ['codnom' => $recibo->codnom, 'codperi' => $recibo->codperi]) }}"
                                  class="btn btn-danger">
                                     <i class="fas fa-file-pdf"></i> Descargar
                                        </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="text-center py-4 text-muted">No se encontraron recibos de pago registrados.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="card-footer bg-white py-3">
            {{ $recibos->links() }}
        </div>
    </div>
</div>
@endsection
