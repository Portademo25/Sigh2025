@extends('layouts.app')

@section('content')
<div class="container-fluid px-4 py-4">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card shadow-lg border-0" style="border-radius: 15px; overflow: hidden;">
                
                {{-- HEADER --}}
                <div class="card-header bg-dark text-white d-flex justify-content-between align-items-center py-3">
                    <h5 class="mb-0 fw-bold">
                        <i class="fas fa-cogs me-2 text-info"></i> 
                        Parametrización ARC (Año Fiscal: {{ $anoSeleccionado ?? date('Y') }})
                    </h5>
                    <a href="{{ route('admin.settings.index') }}" class="btn btn-outline-light btn-sm px-3">
                        <i class="fas fa-arrow-left me-1"></i> Volver al Panel
                    </a>
                </div>

                <div class="card-body bg-white">
                    {{-- INDICADORES --}}
                    <div class="row mb-4">
                        <div class="col-md-4 mb-2">
                            <div class="p-3 border rounded shadow-sm bg-light d-flex align-items-center h-100">
                                <div class="display-6 me-3">📑</div>
                                <div>
                                    <small class="text-muted fw-bold text-uppercase">Nóminas en SIGESP</small>
                                    <h3 class="fw-bold mb-0 text-primary">{{ count($nominas) }}</h3>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 mb-2">
                            <div class="p-3 border rounded shadow-sm bg-light d-flex align-items-center h-100">
                                <div class="display-6 me-3">🔓</div>
                                <div>
                                    <small class="text-muted fw-bold text-uppercase">Periodos Abiertos</small>
                                    <h3 class="fw-bold mb-0 text-warning">{{ $periodosAbiertos ?? 0 }}</h3>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 mb-2">
                            <div class="p-3 border rounded shadow-sm bg-light d-flex align-items-center h-100">
                                <div class="display-6 me-3">📅</div>
                                <div>
                                    <small class="text-muted fw-bold text-uppercase">Última Sincronización</small>
                                    <h3 class="fw-bold mb-0 text-success" style="font-size: 1.1rem;">{{ $lastSync ?? 'Sin datos' }}</h3>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- ALERTAS --}}
                    @if(session('success'))
                        <div class="alert alert-success border-0 shadow-sm mb-4">
                            <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
                        </div>
                    @endif

                    <form action="{{ route('admin.settings.arc.store') }}" method="POST" id="formParametrizacion">
                        @csrf
                        <input type="hidden" name="anio" value="{{ $anoSeleccionado ?? date('Y') }}">

                        <div class="row">
                            {{-- COLUMNA NÓMINAS --}}
                            <div class="col-md-5">
                                <div class="card border shadow-sm">
                                    <div class="card-header bg-primary text-white py-2">
                                        <i class="fas fa-list-check me-2"></i> 1. Selección de Nóminas
                                    </div>
                                    <div class="p-3">
                                        <div class="input-group mb-3">
                                            <span class="input-group-text bg-white border-end-0"><i class="fas fa-search text-muted"></i></span>
                                            <input type="text" id="busquedaNomina" class="form-control form-control-sm border-start-0" placeholder="Filtrar nómina por código o nombre...">
                                        </div>
                                        
                                        <div class="border rounded custom-scroll" style="height: 450px; overflow-y: auto; background-color: #fbfbfb;">
                                            <div id="listaNominas">
                                                @foreach($nominas as $nomina)
                                                @php $codNomina = trim($nomina->codnom); @endphp
                                                <div class="item-container border-bottom hover-bg">
                                                    <label class="d-flex align-items-center w-100 py-2 px-3 cursor-pointer mb-0" for="n_{{ $codNomina }}">
                                                        <input class="custom-check check-nomina me-3" 
                                                               type="checkbox" 
                                                               name="nominas[]" 
                                                               value="{{ $codNomina }}" 
                                                               id="n_{{ $codNomina }}"
                                                               {{ in_array($codNomina, $nominasTildadas) ? 'checked' : '' }}>
                                                        <span class="badge bg-secondary font-monospace me-2">{{ $codNomina }}</span>
                                                        <span class="text-uppercase x-small fw-bold text-dark">{{ $nomina->desnom }}</span>
                                                    </label>
                                                </div>
                                                @endforeach
                                            </div>
                                        </div>
                                       <div class="text-end mt-2">
    @php $cantNom = count($nominasTildadas); @endphp
    <span class="badge rounded-pill {{ $cantNom > 0 ? 'bg-primary shadow-sm' : 'bg-secondary' }} px-3 py-2" id="contNom">
        {{ $cantNom }} {{ $cantNom === 1 ? 'seleccionada' : 'seleccionadas' }}
    </span>
</div>
                                    </div>
                                </div>
                            </div>

                            {{-- COLUMNA CONCEPTOS --}}
                            <div class="col-md-7">
                                <div class="card border shadow-sm">
                                    <div class="card-header bg-success text-white py-2">
                                        <i class="fas fa-tags me-2"></i> 2. Conceptos Relacionados
                                    </div>
                                    <div class="p-3">
                                        <div class="input-group mb-3">
                                            <span class="input-group-text bg-white border-end-0"><i class="fas fa-filter text-muted"></i></span>
                                            <input type="text" id="busquedaConcepto" class="form-control form-control-sm border-start-0" placeholder="Buscar por código, nombre o tipo (Asig, Dedu, Patr)...">
                                        </div>

                                        <div class="border rounded custom-scroll" style="height: 450px; overflow-y: auto; background-color: #fbfbfb;">
                                            <div id="listaConceptos">
                                                @foreach($conceptos as $c)
@php 
    $sig = trim($c->sigcon); 
    $codConc = trim($c->codconc);
    // Creamos la llave única para comparar y para el valor del envío
    $valorCheck = $codConc . '|' . $sig; 
@endphp
<div class="item-container border-bottom hover-bg">
    <label class="d-flex align-items-center w-100 py-2 px-3 cursor-pointer mb-0" for="c_{{ $codConc }}_{{ $sig }}">
        <input class="custom-check check-concepto me-3" 
               type="checkbox" 
               name="conceptos[]" 
               value="{{ $valorCheck }}" 
               id="c_{{ $codConc }}_{{ $sig }}"
               {{ in_array($valorCheck, $conceptosTildados) ? 'checked' : '' }}>
        
        <span class="badge bg-dark me-2 font-monospace">{{ $codConc }}</span>
        
        <span class="badge {{ $sig == 'A' ? 'bg-success' : ($sig == 'D' ? 'bg-danger' : 'bg-info') }} me-2 text-center" style="min-width: 50px; font-size: 0.65rem;">
            {{ $sig == 'A' ? 'ASIG' : ($sig == 'D' ? 'DEDU' : 'PATR') }}
        </span>
        
        <span class="text-uppercase x-small fw-bold text-dark text-truncate" style="max-width: 350px;" title="{{ $c->nomcon }}">
            {{ $c->nomcon }}
        </span>
    </label>
</div>
@endforeach
                                            </div>
                                        </div>
                                        <div class="text-end mt-2">
    @php $cantConc = count($conceptosTildados); @endphp
    <span class="badge rounded-pill {{ $cantConc > 0 ? 'bg-success shadow-sm' : 'bg-secondary' }} px-3 py-2" id="contConc">
        {{ $cantConc }} {{ $cantConc === 1 ? 'seleccionado' : 'seleccionados' }}
    </span>
</div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="card-footer bg-white border-top text-end py-4 mt-3">
                            <p class="text-muted small float-start mt-2">
                                <i class="fas fa-info-circle me-1 text-primary"></i> 
                                Se guardarán únicamente los elementos marcados para el reporte ARC <strong>{{ $anoSeleccionado }}</strong>.
                            </p>
                            <button type="submit" class="btn btn-primary btn-lg px-5 shadow-sm border-0 btn-save" style="border-radius: 10px;">
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
    .custom-check { width: 1.25rem; height: 1.25rem; cursor: pointer; }
    .item-container { display: flex; align-items: center; transition: background 0.2s; }
    .hover-bg:hover { background-color: #f1f5f9; }
    .cursor-pointer { cursor: pointer; user-select: none; }
    .font-monospace { font-family: 'Courier New', monospace; font-size: 0.85rem; }
    .x-small { font-size: 0.72rem; }
    .custom-scroll::-webkit-scrollbar { width: 7px; }
    .custom-scroll::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 10px; }
    .text-truncate { white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
    .badge { letter-spacing: 0.5px; }
    .bg-info { background-color: #0dcaf0 !important; color: #000; } /* Mejor visibilidad para PATR */
.bg-danger { background-color: #ef4444 !important; }
.bg-success { background-color: #10b981 !important; }
</style>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        
        /**
         * 1. ACTUALIZACIÓN DE CONTADORES
         * Esta función barre el DOM buscando los inputs marcados.
         * Se ejecuta al cargar la página y cada vez que el usuario hace click.
         */
        function actualizarContadores() {
            // Seleccionamos específicamente por la clase que definimos en el Blade
            const nNom = document.querySelectorAll('.check-nomina:checked').length;
            const nConc = document.querySelectorAll('.check-concepto:checked').length;
            
            const badgeNom = document.getElementById('contNom');
            const badgeConc = document.getElementById('contConc');

            // Actualizar Badge de Nóminas
            if(badgeNom) {
                badgeNom.textContent = `${nNom} nómina${nNom !== 1 ? 's' : ''} seleccionada${nNom !== 1 ? 's' : ''}`;
                // Cambia el color si hay selecciones
                if(nNom > 0) {
                    badgeNom.classList.replace('bg-secondary', 'bg-primary');
                    badgeNom.classList.add('shadow-sm');
                } else {
                    badgeNom.classList.replace('bg-primary', 'bg-secondary');
                    badgeNom.classList.remove('shadow-sm');
                }
            }

            // Actualizar Badge de Conceptos
            if(badgeConc) {
                badgeConc.textContent = `${nConc} concepto${nConc !== 1 ? 's' : ''} seleccionado${nConc !== 1 ? 's' : ''}`;
                // Cambia el color si hay selecciones
                if(nConc > 0) {
                    badgeConc.classList.replace('bg-secondary', 'bg-success');
                    badgeConc.classList.add('shadow-sm');
                } else {
                    badgeConc.classList.replace('bg-success', 'bg-secondary');
                    badgeConc.classList.remove('shadow-sm');
                }
            }
        }

        /**
         * 2. FILTRO DINÁMICO
         * Optimizado para buscar en el texto y en el valor (código)
         */
        function activarFiltro(inputId, containerId) {
            const input = document.getElementById(inputId);
            const container = document.getElementById(containerId);
            if(input && container) {
                input.addEventListener('keyup', function() {
                    const term = this.value.toLowerCase().trim();
                    const items = container.querySelectorAll('.item-container');
                    
                    items.forEach(item => {
                        // El filtro ahora es más sensible a los códigos y tipos
                        const text = item.textContent.toLowerCase();
                        item.style.display = text.includes(term) ? 'block' : 'none';
                    });
                });
            }
        }
        
        activarFiltro('busquedaNomina', 'listaNominas');
        activarFiltro('busquedaConcepto', 'listaConceptos');

        /**
         * 3. EVENTOS DE FORMULARIO
         */
        const form = document.getElementById('formParametrizacion');
        if(form) {
            // Escuchar cambios en los ganchitos
            form.addEventListener('change', function(e) {
                if(e.target.classList.contains('custom-check')) {
                    actualizarContadores();
                }
            });

            // Prevenir doble envío y mostrar feedback al guardar
            form.addEventListener('submit', function() {
                const btn = this.querySelector('.btn-save');
                if(btn) {
                    btn.disabled = true;
                    btn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i> Guardando cambios...';
                }
            });
        }

        /**
         * 4. EJECUCIÓN INICIAL (El "Fix" para el tipeado)
         * Esto fuerza al script a contar lo que ya viene marcado de la DB
         * apenas se abre la página.
         */
        actualizarContadores();
    });
</script>
@endpush
@endsection