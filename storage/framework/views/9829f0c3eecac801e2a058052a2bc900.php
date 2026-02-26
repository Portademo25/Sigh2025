<?php $__env->startSection('content'); ?>
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-danger text-white fw-bold">
                    Generar Forma 14-100 (IVSS)
                </div>
                <div class="card-body p-4 text-center">
                    <p class="text-muted">Selecciona el año fiscal para el desglose de salarios:</p>

                    <form action="#" id="formIvss">
                        <div class="mb-4">
                            <select class="form-select form-select-lg" id="selectAno">
                                <?php $__currentLoopData = $anos; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $a): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e((int)$a->ano); ?>"><?php echo e((int)$a->ano); ?></option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                        </div>

                        <div class="d-grid gap-2">
                            <button type="button" onclick="descargarPDF()" class="btn btn-danger btn-lg">
                                <i class="bi bi-file-pdf"></i> Descargar Planilla
                            </button>
                            <a href="<?php echo e(route('empleado.reportes.menu')); ?>" class="btn btn-link text-muted">Cancelar</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    function descargarPDF() {
        const ano = document.getElementById('selectAno').value;
        // Redirige a la ruta de descarga pasando el año
        window.location.href = "<?php echo e(url('/reportes/ivss/descargar')); ?>/" + ano;
    }
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /var/www/html/Laravel/Sigh2025/resources/views/empleado/reportes/ivss_index.blade.php ENDPATH**/ ?>