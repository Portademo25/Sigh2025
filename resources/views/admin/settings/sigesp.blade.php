@extends('layouts.app')

@section('content')
<div class="container">
    <div class="card shadow">
        <div class="card-header bg-primary text-white d-flex justify-content-between">
            <h4 class="mb-0">🔄 Sincronización SIGESP</h4>
            <span class="badge bg-light text-dark">Última sincronización: {{ $lastSync }}</span>
        </div>
        <div class="card-body">
            <p class="text-muted">Seleccione la tabla que desea actualizar desde el servidor SIGESP.</p>
            <div class="alert alert-info">
                 <strong>Estado:</strong>
                             {{ \App\Models\Setting::where('key', 'sigesp_last_sync')->value('value') ?? 'No se ha sincronizado aún' }}
                    </div>
            <div class="row mt-4">
                <div class="col-md-4 mb-3">
                    <div class="card h-100 border-start border-primary border-4">
                        <div class="card-body">
                            <h5>Personal</h5>
                            <p class="small">Sincroniza datos básicos, cargos y estatus de empleados.</p>
                            <form action="{{ route('admin.settings.sigesp.sync') }}" method="POST">
                                @csrf
                                <input type="hidden" name="tabla" value="personal">
                                <button type="submit" class="btn btn-primary btn-sm w-100">Sincronizar Ahora</button>
                            </form>
                        </div>
                    </div>
                </div>

                <div class="col-md-4 mb-3">
                    <div class="card h-100 border-start border-success border-4">
                        <div class="card-body">
                            <h5>Nóminas</h5>
                            <p class="small">Importa periodos, tipos de nómina y montos procesados.</p>
                            <form action="{{ route('admin.settings.sigesp.sync') }}" method="POST">
                                @csrf
                                <input type="hidden" name="tabla" value="nominas">
                                <button type="submit" class="btn btn-success btn-sm w-100">Sincronizar Ahora</button>
                            </form>
                        </div>
                    </div>
                </div>

                <div class="col-md-4 mb-3">
                    <div class="card h-100 border-start border-info border-4">
                        <div class="card-body">
                            <h5>Conceptos / Asignaciones</h5>
                            <p class="small">Actualiza el catálogo de conceptos de ley y deducciones.</p>
                            <form action="{{ route('admin.settings.sigesp.sync') }}" method="POST">
                                @csrf
                                <input type="hidden" name="tabla" value="conceptos">
                                <button type="submit" class="btn btn-info btn-sm w-100 text-white">Sincronizar Ahora</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <div class="alert alert-warning mt-4">
                <i class="fas fa-exclamation-triangle"></i> <strong>Nota:</strong> Este proceso puede tardar varios minutos dependiendo de la conexión con el servidor central de SIGESP. No cierre la ventana mientras la sincronización esté en curso.
            </div>
        </div>
    </div>
</div>
@endsection
