<?php $__env->startSection('content'); ?>
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card border-success">
                <div class="card-header bg-success text-white d-flex justify-content-between align-items-center">
                    <h2 class="mb-0">🟢 Usuarios Activos (En línea)</h2>
                    <a href="<?php echo e(route('admin.dashboard')); ?>" class="btn btn-light btn-sm">
                        ← Dashboard
                    </a>
                </div>

                <div class="card-body">
                    <p class="text-muted">Mostrando usuarios con actividad en los últimos 5 minutos.</p>

                    <table class="table table-hover">
                       
                        <thead>
                            <tr>
                                 <th>Usuario</th>
                                  <th>Email</th>
                                  <th>Última actividad</th>
                                  <th>Acciones</th> 
                            </tr>
                        </thead>
                    <tbody>
                        <?php $__empty_1 = true; $__currentLoopData = $users; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                           <tr>
                            <td><?php echo e($user->name); ?></td>
                             <td><?php echo e($user->email); ?></td>
                              <td><?php echo e($user->last_seen_at->diffForHumans()); ?></td>
                                          <td>
            
                                <?php if($user->id !== Auth::id()): ?>
                      <form action="<?php echo e(route('admin.users.kick', $user)); ?>" method="POST" onsubmit="return confirm('¿Expulsar a este usuario inmediatamente?')">
                             <?php echo csrf_field(); ?>
                                <button type="submit" class="btn btn-sm btn-outline-danger">
                                   Expulsar (Kick)
                               </button>
                        </form>
                       <?php else: ?>
                           <span class="badge bg-secondary">Tú</span>
                       <?php endif; ?>
                             </td>
                         </tr>
                     <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
        
                    <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /var/www/html/Sigh2025/resources/views/admin/active_users.blade.php ENDPATH**/ ?>