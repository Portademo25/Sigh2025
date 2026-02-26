<?php $__env->startSection('content'); ?>
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8 position-relative">
            <div class="d-flex justify-content-end mb-3">
                <a href="<?php echo e(route('rrhh.dashboard')); ?>" class="btn btn-light shadow-sm rounded-pill px-4 fw-bold text-secondary border">
                    <i class="fas fa-arrow-left me-2"></i> Volver al Panel
                </a>
            </div>

            <div class="card border-0 shadow-lg rounded-4 overflow-hidden">
                <div class="card-header bg-warning py-3 text-center border-0">
                    <h4 class="fw-bold text-white mb-0">
                        <i class="fas fa-coins me-2"></i>Gestión Local de Cestaticket
                    </h4>
                </div>

                <div class="card-body p-5">
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <div class="p-3 bg-light rounded-3 border-start border-4 border-primary h-100">
                                <small class="text-muted d-block text-uppercase fw-bold" style="font-size: 0.7rem;">Valor en Sistema Local</small>
                                <span class="h3 fw-bold text-primary">Bs. <?php echo e(number_format($montoActual, 2, ',', '.')); ?></span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="p-3 bg-light rounded-3 border-start border-4 border-secondary h-100">
                                <small class="text-muted d-block text-uppercase fw-bold" style="font-size: 0.7rem;">Último Ajuste</small>
                                <span class="d-block mt-1 fw-bold text-dark"><?php echo e($ultimaActualizacion); ?></span>
                            </div>
                        </div>
                    </div>

                    <form action="<?php echo e(route('rrhh.cestaticket.update')); ?>" method="POST" id="formCestaticket">
                        <?php echo csrf_field(); ?>
                        <div class="mb-4">
                            <label class="form-label fw-bold text-secondary">Nuevo Monto de Alimentación (Bs.)</label>
                            <div class="input-group input-group-lg">
                                <span class="input-group-text bg-white border-end-0"><i class="fas fa-edit text-warning"></i></span>
                                <input type="number" step="0.01" name="monto_cestaticket"
                                       class="form-control border-start-0 shadow-none"
                                       placeholder="Ingrese el nuevo monto" required>
                            </div>
                            <div class="form-text mt-2 text-center italic">
                                Este cambio se guardará primero en la base de datos local del sistema.
                            </div>
                        </div>

                        <div class="d-grid">
                            <button type="button" class="btn btn-warning btn-lg fw-bold text-white shadow-sm py-3" onclick="confirmarCambio()">
                                <i class="fas fa-save me-2"></i>Actualizar Parámetro Local
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    function confirmarCambio() {
        const monto = document.getElementsByName('monto_cestaticket')[0].value;
        if (!monto || monto <= 0) {
            Swal.fire('Atención', 'Debe ingresar un monto válido.', 'info');
            return;
        }

        Swal.fire({
            title: '¿Actualizar Valor Local?',
            text: `El nuevo monto de Bs. ${monto} se guardará en el sistema.`,
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#ffc107',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Sí, guardar',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById('formCestaticket').submit();
            }
        })
    }
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /var/www/html/Laravel/Sigh2025/resources/views/rrhh/cestaticket/index.blade.php ENDPATH**/ ?>