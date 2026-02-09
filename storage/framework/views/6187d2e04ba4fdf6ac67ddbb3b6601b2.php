<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <style>
        @page { margin: 0.5cm 1cm; }
        body { font-family: 'Helvetica', sans-serif; font-size: 9px; margin: 0; }
        .header-table { width: 100%; margin-bottom: 5px; }
        .inst-text { text-align: center; font-weight: bold; font-size: 8px; text-transform: uppercase; }
        .title { text-align: center; font-size: 11px; font-weight: bold; text-decoration: underline; margin: 10px 0; }
        .info-table { width: 100%; border-collapse: collapse; margin-bottom: 10px; }
        .info-table td { padding: 2px 0; }
        .data-table { width: 100%; border-collapse: collapse; }
        .data-table th { border-top: 1px solid #000; border-bottom: 1px solid #000; padding: 4px; text-align: left; }
        .data-table td { padding: 2px 4px; vertical-align: top; }
        .text-right { text-align: right; }
        .bold { font-weight: bold; }
        .footer { font-size: 8px; margin-top: 20px; border-top: 1px solid #ccc; padding-top: 5px; }
    </style>
</head>
<body>
    <table class="header-table">
        <tr>
            <td width="20%"><img src="<?php echo e(public_path('images/logo_ministerio.png')); ?>" style="height: 55px;"></td>
            <td width="60%" class="inst-text">
                REPUBLICA BOLIVARIANA DE VENEZUELA <br>
                MINISTERIO DEL PODER POPULAR PARA RELACIONES INTERIORES, JUSTICIA Y PAZ <br>
                SUPERINTENDENCIA NACIONAL ANTIDROGAS <br>
                FONDO NACIONAL ANTIDROGAS
            </td>
            <td width="20%" class="text-right"><img src="<?php echo e(public_path('images/logo_fona.png')); ?>" style="height: 55px;"></td>
        </tr>
    </table>

    <div class="title">COMPROBANTE DE PAGO</div>

    <div class="bold" style="margin-bottom: 8px;">
        Periodo <?php echo e($resumen->codperi); ?> Del <?php echo e(\Carbon\Carbon::parse($resumen->fecdesper)->format('d/m/Y')); ?> al <?php echo e(\Carbon\Carbon::parse($resumen->fechasper)->format('d/m/Y')); ?>

    </div>

    <table class="info-table">
        <tr>
            <td width="18%" class="bold">NOMBRES Y APELLIDOS:</td>
            <td width="52%"><?php echo e($user->name); ?> <?php echo e($user->apellido); ?></td>
            <td width="10%" class="bold">C.I. Nro.</td>
            <td width="20%">V<?php echo e(number_format($user->codper, 0, '', '.')); ?></td>
        </tr>
        <tr>
            <td class="bold">TIPO DE PERSONAL:</td>
            <td colspan="3"><?php echo e($resumen->desnom); ?></td>
        </tr>
        <tr>
            <td class="bold">FECHA DE INGRESO:</td>
            <td colspan="3"><?php echo e(\Carbon\Carbon::parse($resumen->fecingper)->format('d/m/Y')); ?></td>
        </tr>
        <tr>
            <td class="bold">DEPENDENCIA:</td>
            <td colspan="3"><?php echo e($resumen->desuniadm ?? 'OFICINA DE TECNOLOGIA DE LA INFORMACION Y LA COMUNICACION'); ?></td>
        </tr>
        <tr>
            <td class="bold">CUENTA BANCARIA:</td>
            <td colspan="3"><?php echo e($resumen->ctabanper); ?></td>
        </tr>
    </table>

   <table class="data-table" style="width: 100%; border-collapse: collapse;">
    <thead>
        <tr>
            <th style="border-top: 1px solid #000; border-bottom: 1px solid #000;">CONCEPTO</th>
            <th style="border-top: 1px solid #000; border-bottom: 1px solid #000;">DESCRIPCIÓN</th>
            <th style="border-top: 1px solid #000; border-bottom: 1px solid #000; text-align: right;">ASIGNACIONES</th>
            <th style="border-top: 1px solid #000; border-bottom: 1px solid #000; text-align: right;">DEDUCCIONES</th>
        </tr>
    </thead>
    <tbody>
        
        <?php $__empty_1 = true; $__currentLoopData = $asignaciones; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $asig): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
        <tr>
            <td><?php echo e(str_pad($asig->codcon, 10, "0", STR_PAD_LEFT)); ?></td>
            <td><?php echo e($asig->nomcon ?? 'CONCEPTO SIN NOMBRE'); ?></td>
            <td class="text-right"><?php echo e(number_format($asig->valcalcur, 2, ',', '.')); ?></td>
            <td></td>
        </tr>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
        
        <?php endif; ?>

        
    
<?php $__currentLoopData = $deducciones; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $deduc): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
<tr>
    <td><?php echo e(str_pad($deduc->codcon, 10, "0", STR_PAD_LEFT)); ?></td>
    <td><?php echo e($deduc->nomcon); ?></td>
    <td></td>
    <td style="text-align: right;">
        
        <?php echo e(number_format($deduc->valcalcur, 2, ',', '.')); ?>

    </td>
</tr>
<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </tbody>
    <tfoot>
        <tr class="bold" style="border-top: 1px solid #000;">
    <td colspan="2" class="text-right">TOTALES:</td>
    <td class="text-right">
        <?php echo e(number_format($asignaciones->sum('valcalcur'), 2, ',', '.')); ?>

    </td>
    <td class="text-right">
        
        -<?php echo e(number_format(abs($deducciones->sum('valcalcur')), 2, ',', '.')); ?>

    </td>
    </tr>
        <tr class="bold">
            <td colspan="3" class="text-right">NETO A COBRAR:</td>
            <td class="text-right" style="border-bottom: 3px double #000;">
                <?php echo e(number_format($resumen->monnetres, 2, ',', '.')); ?>

            </td>
        </tr>
    </tfoot>
</table>

    <div class="footer">
        Sigh Sistema de Gestion Humana | Fecha de Impresión: <?php echo e(date('d/m/Y h:i:s a')); ?>

    </div>
</body>
</html>
<?php /**PATH /var/www/html/Sigh2025/resources/views/empleado/reportes/recibo_pdf.blade.php ENDPATH**/ ?>