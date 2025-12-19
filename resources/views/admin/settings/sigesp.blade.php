@extends('layouts.app') {{-- O tu layout principal --}}

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-8 mx-auto">
            <div class="card card-primary card-outline shadow">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-sync"></i> Sincronización SIGESP</h3>
                    <div class="card-tools">
                        <span class="badge badge-info" style="color: black;">Última sincronización: {{ $lastSync }}</span>
                    </div>
                </div>
                <div class="card-body">
                    <p class="text-muted">Estado actual de las tablas locales:</p>

                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead class="bg-light">
                                <tr>
                                    <th>Módulo</th>
                                    <th class="text-center">Registros</th>
                                    <th>Estado</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($status as $s)
                                <tr>
                                    <td>{{ $s['nombre'] }}</td>
                                    <td class="text-center"><strong>{{ number_format($s['total']) }}</strong></td>
                                    <td>
                                        @if($s['total'] > 0)
                                            <span class="badge badge-success" style="color: black;">Sincronizado</span>
                                        @else
                                            <span class="badge badge-danger" style="color: black;" >Vacío</span>
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-4 text-center">
                       <form action="{{ route('admin.settings.sigesp.sync') }}" method="POST" id="syncForm">
                          @csrf  {{-- <--- ESTA LÍNEA ES OBLIGATORIA --}}

                          <button type="submit" class="btn btn-primary btn-lg" id="btnSync">
                               <i class="fas fa-cloud-download-alt"></i> Iniciar Sincronización Masiva
                          </button>
                        </form>
                        <div id="loader" style="display:none;" class="mt-3">
                            <div class="spinner-border text-primary" role="status"></div>
                            <p class="text-primary mt-2">Procesando datos desde SIGESP, por favor espere...</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.getElementById('syncForm').addEventListener('submit', function() {
        document.getElementById('btnSync').style.display = 'none';
        document.getElementById('loader').style.display = 'block';
    });
</script>
@stop
