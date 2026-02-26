<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: sans-serif; font-size: 9px; margin: 0; padding: 0; }
        .header { text-align: center; font-weight: bold; margin-bottom: 5px; text-transform: uppercase; }
        .title { text-align: center; font-size: 12px; font-weight: bold; background: #e0e0e0; padding: 5px; border: 1px solid black; }
        table { width: 100%; border-collapse: collapse; margin-bottom: -1px; }
        th, td { border: 1px solid black; padding: 3px; }
        .section-title { background: #f2f2f2; font-weight: bold; text-align: center; text-transform: uppercase; }
        .text-center { text-align: center; }
        .footer { margin-top: 10px; }
        .signature-box { height: 50px; }
    </style>
</head>
<body>

    <div class="header">
        REPUBLICA BOLIVARIANA DE VENEZUELA<br>
        MINISTERIO DEL PODER POPULAR PARA EL PROCESO SOCIAL DE TRABAJO<br>
        INSTITUTO VENEZOLANO DE LOS SEGUROS SOCIALES<br>
        DIRECCIÓN GENERAL DE AFILIACIÓN Y PRESTACIONES EN DINERO
    </div>

    <div class="title">CONSTANCIA DE TRABAJO PARA EL IVSS</div>

    <table>
        <tr><td colspan="4" class="section-title">DATOS DEL EMPLEADOR O EMPLEADORA</td></tr>
        <tr>
            <td colspan="2"><b>RAZÓN SOCIAL:</b><br><?php echo e($empresa['nombre'] ?? 'NOMBRE DE LA INSTITUCION'); ?></td>
            <td colspan="2"><b>NÚMERO PATRONAL:</b><br><?php echo e($empresa['patronal'] ?? '000-00000-0'); ?></td>
        </tr>
        <tr>
            <td colspan="2" rowspan="2"><b>DIRECCIÓN:</b><br><?php echo e($empresa['direccion'] ?? ''); ?></td>
            <td><b>TELÉFONO:</b><br><?php echo e($empresa['telefono'] ?? ''); ?></td>
            <td><b>RIF:</b><br><?php echo e($empresa['rif'] ?? ''); ?></td>
        </tr>
        <tr>
            <td colspan="2"><b>CORREO ELECTRÓNICO:</b><br><?php echo e($empresa['email'] ?? ''); ?></td>
        </tr>
    </table>

    <table>
        <tr><td colspan="4" class="section-title">DATOS DEL TRABAJADOR O TRABAJADORA</td></tr>
        <tr>
            <td colspan="3"><b>APELLIDOS Y NOMBRES:</b><br><?php echo e($trabajador->nomper); ?> <?php echo e($trabajador->apeper); ?></td>
            <td><b>CÉDULA DE IDENTIDAD N°:</b><br><?php echo e($trabajador->codper); ?></td>
        </tr>
        <tr>
            <td colspan="2"><b>FECHA DE INGRESO:</b><br><?php echo e(\Carbon\Carbon::parse($trabajador->fecingper)->format('d/m/Y')); ?></td>
            <td colspan="2"><b>FECHA DE RETIRO:</b><br>---</td>
        </tr>
    </table>

    <table>
        <tr><td colspan="14" class="section-title">SALARIOS DEVENGADOS</td></tr>
        <tr class="text-center" style="font-size: 8px;">
            <th>AÑO</th>
            <th>ENE</th><th>FEB</th><th>MAR</th><th>ABR</th><th>MAY</th><th>JUN</th>
            <th>JUL</th><th>AGO</th><th>SEP</th><th>OCT</th><th>NOV</th><th>DIC</th>
            <th>TOTALES</th>
        </tr>
        <tr class="text-center">
            <td><b><?php echo e($ano); ?></b></td>
            <?php $totalAnual = 0; ?>
            <?php for($i = 1; $i <= 12; $i++): ?>
                <?php
                    $monto = $detalles->get($i)->monto ?? 0;
                    $totalAnual += $monto;
                ?>
                <td><?php echo e($monto > 0 ? number_format($monto, 2, ',', '.') : '-'); ?></td>
            <?php endfor; ?>
            <td><b><?php echo e(number_format($totalAnual, 2, ',', '.')); ?></b></td>
        </tr>
    </table>

    <table style="margin-top: 10px;">
        <tr>
            <td style="width: 60%; vertical-align: top;">
                <b>DECLARACIÓN JURADA:</b><br>
                CERTIFICO BAJO FE DE JURAMENTO, QUE LA INFORMACIÓN QUE ANTECEDE ES CIERTA EN TODAS SUS PARTES.
            </td>
            <td class="text-center">
                <div class="signature-box"></div>
                ___________________________<br>
                FIRMA Y SELLO
            </td>
        </tr>
    </table>

    <div style="margin-top: 5px; text-align: right; font-size: 8px;">
        FORMA: 14-100 | Fecha de Emisión: <?php echo e(date('d/m/Y')); ?>

    </div>

</body>
</html>
<?php /**PATH /var/www/html/Laravel/Sigh2025/resources/views/empleado/reportes/ivss_14100.blade.php ENDPATH**/ ?>