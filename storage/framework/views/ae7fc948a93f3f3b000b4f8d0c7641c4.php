<?php $__env->startSection('content'); ?>
<div class="container">
    <div class="card">
        <div class="card-header">
            <h4>Panel de Control SIGESP</h4>
        </div>
        <div class="card-body text-center">
            <div class="alert alert-info">
                <strong>Última Sincronización:</strong> <?php echo e($lastSync); ?>

            </div>

            <p>Este proceso actualizará las empresas, trabajadores, nóminas y periodos históricos desde el servidor central.</p>

            <form action="<?php echo e(route('admin.settings.sigesp.sync')); ?>" method="POST" id="syncForm">
                <?php echo csrf_field(); ?>
                <button type="submit" class="btn btn-primary btn-lg" id="btnSync">
                    <i class="fas fa-sync"></i> Iniciar Sincronización Ahora
                </button>
            </form>

            <div id="loader" style="display:none;" class="mt-3">
                <div class="spinner-border text-primary" role="status"></div>
                <p>Sincronizando datos... por favor no cierre esta ventana.</p>
            </div>
        </div>
    </div>
</div>

<script>
    document.getElementById('syncForm').onsubmit = function() {
        document.getElementById('btnSync').disabled = true;
        document.getElementById('loader').style.display = 'block';
    };
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /var/www/html/Laravel/Sigh2025/resources/views/admin/settings/sigesp.blade.php ENDPATH**/ ?>