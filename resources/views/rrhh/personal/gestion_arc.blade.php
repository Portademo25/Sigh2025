@extends('layouts.app')

@section('content')
<div class="container-fluid px-4 py-4">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card shadow-lg border-0" style="border-radius: 12px; overflow: hidden;">
                
                {{-- HEADER --}}
                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center py-3">
                    <h5 class="mb-0 fw-bold">
                        <i class="fas fa-cogs me-2"></i> Configuración y Parametrización de ARC
                    </h5>
                    <span class="badge bg-white text-primary fw-bold px-3 py-2">Ejercicio {{ $anoSeleccionado }}</span>
                </div>

                <div class="card-body">
                    <form action="{{ route('rrhh.personal.listar_nomina_arc') }}" method="POST" id="formGestion">
                        @csrf
                        <input type="hidden" name="anio" value="{{ $anoSeleccionado }}">

                        {{-- FILTRO DE AÑO --}}
                        <div class="row mb-4 bg-light p-3 mx-1 rounded border">
                            <div class="col-md-4">
                                <label class="form-label fw-bold text-dark"><i class="fas fa-calendar-alt me-1 text-primary"></i> 1. Año Fiscal:</label>
                                <select id="selectorAnio" class="form-select border-primary shadow-sm">
                                    @foreach($years as $year)
                                        <option value="{{ $year }}" {{ $year == $anoSeleccionado ? 'selected' : '' }}>
                                            {{ $year }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-8 d-flex align-items-center">
                                <div class="alert alert-info mb-0 w-100 py-2 border-0 shadow-sm">
                                    <i class="fas fa-info-circle me-2"></i>
                                    Seleccione las nóminas y conceptos (Asignaciones, Deducciones o Patronales) para el cálculo de ingresos brutos.
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            {{-- COLUMNA NÓMINAS --}}
                            <div class="col-md-5">
                                <label class="form-label fw-bold text-primary"><i class="fas fa-list-ol me-1"></i> 2. Nóminas a procesar:</label>
                                <div class="input-group mb-2 shadow-sm">
                                    <span class="input-group-text bg-white border-end-0"><i class="fas fa-search text-muted"></i></span>
                                    <input type="text" id="busquedaNomina" class="form-control border-start-0" placeholder="Buscar nómina...">
                                </div>

                                <div class="border rounded custom-scroll" style="height: 450px; overflow-y: auto; background-color: #fff;">
                                    <div id="listaNominas">
                                        @foreach($nominas as $nomina)
                                            @php $codNomina = trim($nomina->codnom); @endphp
                                            <div class="item-nomina border-bottom hover-bg">
                                                <div class="form-check d-flex align-items-center py-2 px-3 mb-0">
                                                    <input class="form-check-input ms-0 check-nomina"
                                                           type="checkbox"
                                                           name="nominas[]"
                                                           value="{{ $codNomina }}"
                                                           id="check_n_{{ $codNomina }}"
                                                           {{ in_array($codNomina, $nominasTildadas) ? 'checked' : '' }}>

                                                    <label class="form-check-label ms-3 w-100 cursor-pointer" for="check_n_{{ $codNomina }}">
                                                        <span class="badge bg-secondary font-monospace me-2">{{ $codNomina }}</span>
                                                        <span class="text-uppercase small fw-bold text-dark">{{ $nomina->desnom }}</span>
                                                    </label>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                                <div class="text-end mt-2">
                                    <span class="badge rounded-pill bg-primary px-3 py-2 shadow-sm" id="contadorNominas">
                                        {{ count($nominasTildadas) }} seleccionadas
                                    </span>
                                </div>
                            </div>

                            {{-- COLUMNA CONCEPTOS --}}
                            <div class="col-md-7">
                                <label class="form-label fw-bold text-success"><i class="fas fa-tags me-1"></i> 3. Conceptos Seleccionados:</label>
                                <div class="input-group mb-2 shadow-sm">
                                    <span class="input-group-text bg-white border-end-0"><i class="fas fa-filter text-muted"></i></span>
                                    <input type="text" id="busquedaConcepto" class="form-control border-start-0" placeholder="Filtrar por código, nombre o tipo...">
                                </div>

                                <div class="border rounded custom-scroll" style="height: 450px; overflow-y: auto; background-color: #fff;">
                                    <div id="listaConceptos">
                                        @foreach($conceptos as $c)
                                            @php 
                                                $sig = trim($c->sigcon);
                                                $cod = trim($c->codconc);
                                                $valorCheck = $cod . '|' . $sig; 
                                            @endphp
                                            <div class="item-concepto border-bottom hover-bg">
                                                <div class="form-check d-flex align-items-center py-1 px-3 mb-0">
                                                    <input class="form-check-input check-concepto ms-0" 
                                                           type="checkbox" 
                                                           name="conceptos[]"
                                                           value="{{ $valorCheck }}"
                                                           id="conc_{{ $cod }}_{{ $sig }}"
                                                           {{ in_array($valorCheck, $conceptosTildados) ? 'checked' : '' }}>

                                                    <label class="form-check-label d-flex align-items-center cursor-pointer ms-3 w-100 py-1" for="conc_{{ $cod }}_{{ $sig }}">
                                                        <small class="badge bg-dark me-2 font-monospace" style="min-width: 50px;">{{ $cod }}</small>

                                                        <span class="badge {{ $sig == 'A' ? 'bg-success' : ($sig == 'D' ? 'bg-danger' : 'bg-info') }} me-2 text-center" 
                                                              style="min-width: 50px; font-size: 0.65rem; {{ $sig == 'P' ? 'color: #000;' : '' }}">
                                                            {{ $sig == 'A' ? 'ASIG' : ($sig == 'D' ? 'DEDU' : 'PATR') }}
                                                        </span>

                                                        <span class="text-uppercase fw-bold text-dark text-truncate" style="font-size: 0.75rem; max-width: 380px;" title="{{ $c->nomcon }}">
                                                            {{ $c->nomcon }}
                                                        </span>
                                                    </label>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                                <div class="text-end mt-2">
                                    <span class="badge rounded-pill bg-success px-3 py-2 shadow-sm" id="contadorConceptos">
                                        {{ count($conceptosTildados) }} seleccionados
                                    </span>
                                </div>
                            </div>
                        </div>

                        <div class="card-footer bg-white border-top text-end py-4 mt-3">
                            <a href="{{ route('rrhh.dashboard') }}" class="btn btn-outline-secondary btn-lg px-4 me-2" style="border-radius: 8px;">
                                <i class="fas fa-times me-1"></i> Cancelar
                            </a>
                            <button type="submit" class="btn btn-primary btn-lg px-5 shadow-sm border-0" style="border-radius: 8px;">
                                <i class="fas fa-save me-1"></i> Guardar Configuración
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .hover-bg:hover { background-color: #f8fafc; transition: 0.2s; }
    .cursor-pointer { cursor: pointer; user-select: none; }
    .font-monospace { font-family: 'Courier New', monospace; font-size: 0.85rem; }
    .custom-scroll::-webkit-scrollbar { width: 6px; }
    .custom-scroll::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 10px; }
    .item-concepto, .item-nomina { transition: background 0.15s ease-in-out; }
    .form-check-input { width: 1.2rem; height: 1.2rem; margin-top: 0 !important; cursor: pointer; }
    .bg-info { background-color: #0dcaf0 !important; }
</style>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {

        function actualizarContadores() {
            // Nóminas
            const nN = document.querySelectorAll('.check-nomina:checked').length;
            const elN = document.getElementById('contadorNominas');
            if (elN) {
                elN.textContent = `${nN} ${nN === 1 ? 'seleccionada' : 'seleccionadas'}`;
                elN.className = `badge rounded-pill px-3 py-2 shadow-sm ${nN > 0 ? 'bg-primary' : 'bg-secondary'}`;
            }

            // Conceptos
            const nC = document.querySelectorAll('.check-concepto:checked').length;
            const elC = document.getElementById('contadorConceptos');
            if (elC) {
                elC.textContent = `${nC} ${nC === 1 ? 'seleccionado' : 'seleccionados'}`;
                elC.className = `badge rounded-pill px-3 py-2 shadow-sm ${nC > 0 ? 'bg-success' : 'bg-secondary'}`;
            }
        }

        // Delegación de eventos para los ganchitos
        document.getElementById('formGestion').addEventListener('change', function(e) {
            if (e.target.classList.contains('check-nomina') || e.target.classList.contains('check-concepto')) {
                actualizarContadores();
            }
        });

        // Cambio de Año Fiscal
        document.getElementById('selectorAnio').addEventListener('change', function() {
            window.location.href = "{{ route('rrhh.personal.gestion_arc') }}?ano=" + this.value;
        });

        // Buscadores Dinámicos
        function activarBuscador(inputId, containerId, itemClass) {
            const input = document.getElementById(inputId);
            const container = document.getElementById(containerId);
            
            input.addEventListener('keyup', function() {
                const term = this.value.toLowerCase().trim();
                const items = container.querySelectorAll(itemClass);
                
                items.forEach(item => {
                    const text = item.textContent.toLowerCase();
                    item.style.display = text.includes(term) ? 'block' : 'none';
                });
            });
        }
        
        activarBuscador('busquedaNomina', 'listaNominas', '.item-nomina');
        activarBuscador('busquedaConcepto', 'listaConceptos', '.item-concepto');

        // Ejecutar al cargar para sincronizar con datos de DB
        actualizarContadores();
    });
</script>
@endpush
@endsection