<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Constancia del Trabajador <?php echo e($ls_nombres); ?> <?php echo e($ls_apellidos); ?></title>
    <style>
       @page {
        /* Ajustamos el margen superior a 1cm para que el cintillo quede bien arriba */
        margin: 1cm 2cm 3cm 2cm;
    }
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            line-height: 1.5;
            text-align: justify;
        }
        .cintillo-superior {
        width: 100%;
        margin: 0;
        padding: 0;
    }

        .header-table { width: 100%; margin-bottom: 40px; }
        .titulo {
            text-align: center;
            font-weight: bold;
             font-size: 16px;
             margin-top: -50px;
                margin-bottom: 30px;

        }
        .contenido {
            margin-bottom: 30px;
            line-height: 2; /* Espaciado amplio para lectura fácil */
        }
        .firma {
            text-align: center;
            margin-top: 100px;
            font-weight: bold;
        }
        .resolucion {
            font-size: 10px;
            font-weight: normal;
            margin-top: 5px;
        }
        .footer {
            position: fixed;
            top: 930px;
            bottom: 0;
            left: 0;
            width: 100%;
            text-align: center;
            font-size: 9px;
            color: #444;
            border-top: none;

        }
        .validacion-text {
            font-size: 9px;
            text-align: center;
            margin-top: 40px;
            font-style: italic;
        }
        .seccion-firma {
        text-align: center;
        margin-top: 20px; /* Reducimos el margen superior para que la firma quepa bien */
        position: relative;
    }

    .img-firma {
        width: 5cm; /* Ajusta el ancho según el tamaño de tu imagen */
        height: auto;
        margin-bottom: -15px; /* Esto hace que la firma se acerque más al nombre */
    }

    .negrita { font-weight: bold; }


    </style>
</head>
<body>
    <div class="cintillo-superior">
        <img src="<?php echo e(public_path('images/cintillo-web-fullhdmejor.png')); ?>" class="img-cintillo" style="width: 20cm; margin-top: -150px;  margin-left: -40px;">
    </div>
    


    
    <div class="titulo">CONSTANCIA DE TRABAJO</div>

    
  <div style="text-align: justify; line-height: 1.8; font-size: 13px;">
    Quien suscribe, <strong>DIRECTOR EJECUTIVO del FONDO NACIONAL ANTIDROGAS</strong>, hace constar por medio de la presente que el (la) ciudadano (a) <strong><?php echo e($ls_nombres); ?> <?php echo e($ls_apellidos); ?></strong>, Titular de la Cédula de Identidad V.- <strong><?php echo e($ls_cedula); ?></strong>, presta sus servicios en esta Institución desde el día <strong><?php echo e($ld_fecha_ingreso); ?></strong>, ejerciendo funciones como <strong><?php echo e($ls_cargo); ?></strong>, adscrito a la <strong><?php echo e($ls_unidad_administrativa); ?></strong>, percibiendo una remuneración mensual de <strong><?php echo e($li_mensual_inte_sueldo); ?></strong>.
    <br><br>
    Adicionalmente, percibe el beneficio de Alimentación mensual, por la cantidad de <strong><?php echo e($ls_monto_alimentacion); ?></strong>.
    <br><br>
    Constancia que se expide a petición de la parte interesada, en Caracas a los <strong> <?php echo e($ls_dia); ?> </strong> días del mes de <strong><?php echo e($ls_mes); ?></strong> del <strong><?php echo e($ls_ano); ?></strong>.
</div>

<div class="seccion-firma">
    <img src="<?php echo e(public_path('images/Firma_director.jpg')); ?>" class="img-firma">

    <br>
    <span class="negrita"><?php echo e($ls_director_nombre ?? 'Santiago León Sandoval Bastardo'); ?></span><br>
    <span class="negrita">DIRECTOR EJECUTIVO</span><br>
    <span style="font-size: 11px;">
        Resolución Ministerial 0117-2024<br>
        Gaceta ordinaria 42.979 de fecha 07/10/2024
    </span>
</div>
    <div class="validacion-text" style="margin-left: 16px; text-align: center; font-size: 9px; margin-top: 10px;">
        Esta constancia ha sido impresa electrónicamente, los datos reflejados están sujetos a confirmación a través del siguiente</br> número de teléfono: <strong> (0212) 2325522 - 2329541 / Ext 8170,</strong> valido por tres <strong>(3) meses.</strong></br> Av. Francisco de Miranda, Edificio 407 Los Ruices. Caracas Venezuela
    </div>

    <div style="margin-top: 20px;">
        <img src="data:image/svg+xml;base64,<?php echo e($qrCode); ?>" style="width: 60px; height: 60px; margin-left: auto; margin-right: auto; margin-top: -80px; display: block;">
        <p style="font-size: 7px; margin-top: -40px;">VERIFICAR AQUÍ</p>
    </div>
    <div class="footer">
        <img src="<?php echo e(public_path('images/cintillo_footer.jpg')); ?>" class="img-footer" style="width: 21cm;">
    </div>

</body>
</html>

<?php /**PATH /var/www/html/Laravel/Sigh2025/resources/views/rrhh/personal/constancia_pdf.blade.php ENDPATH**/ ?>