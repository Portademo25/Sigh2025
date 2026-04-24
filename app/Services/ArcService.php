<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;

class ArcService
{
    /**
     * Obtiene los códigos de conceptos configurados como asignaciones (sin ceros a la izquierda)
     */
    public function getAsignacionesConfiguradas()
    {
        $anioActual = date('Y');
        $config = DB::table('arc_parametros')->where('anio', $anioActual)->first()
                  ?? DB::table('arc_parametros')->orderBy('id', 'desc')->first();

        if (!$config) return [];

        $clasificacion = json_decode($config->clasificacion);

        // Usamos la misma lógica que tu reporte ARC para normalizar los códigos
        return collect($clasificacion->mapa->asignaciones ?? [])
            ->map(fn($c) => ltrim(trim($c), '0'))
            ->toArray();
    }

    /**
     * Calcula el sueldo mensual basado en el último periodo pagado
     */
    public function obtenerSueldoActual($codper, $codnom)
{
    $anioActual = date('Y');

    // 1. Obtenemos la configuración desde arc_parametros
    $config = DB::table('arc_parametros')->where('anio', $anioActual)->first();
    if (!$config) return 0.00;

    $mapa = json_decode($config->clasificacion)->mapa ?? (object)[];

    // Normalizamos los IDs (ej: de "0000000001|A" a "1")
    $asignaciones = collect($mapa->asignaciones ?? [])->map(function($c) {
        return ltrim(trim(explode('|', $c)[0]), '0');
    })->toArray();

    if (empty($asignaciones)) return 0.00;

    // 2. Buscamos el ID del último periodo pagado (la última quincena)
    $ultimoPeriodo = DB::connection('sigesp')->table('sno_hsalida')
        ->where('codper', $codper)
        ->where('codnom', trim($codnom))
        ->where('anocur', $anioActual)
        ->orderBy('codperi', 'desc')
        ->value('codperi');

    if (!$ultimoPeriodo) return 0.00;

    // 3. Sumamos los conceptos de esa quincena específica
    $montoQuincena = DB::connection('sigesp')->table('sno_hsalida')
        ->where('codper', $codper)
        ->where('codnom', trim($codnom))
        ->where('anocur', $anioActual)
        ->where('codperi', $ultimoPeriodo)
        ->get()
        ->filter(function($m) use ($asignaciones) {
            // Comparamos el código del concepto de SIGESP (sin ceros) con nuestra lista
            return in_array(ltrim(trim($m->codconc), '0'), $asignaciones);
        })
        ->sum('valsal');

    // 4. Retornamos mensualizado (Quincena * 2) como tenías originalmente
    return round((float)$montoQuincena * 2, 2);
}

    /**
     * Data completa para el reporte ARC
     */
public function obtenerDataReporte($anio, $codper)
{
    $config = DB::table('arc_parametros')->where('anio', $anio)->first();
    if (!$config) throw new \Exception("Configuración no encontrada.");

    $clasificacion = json_decode($config->clasificacion);
    $mapa = $clasificacion->mapa ?? (object)[];

    // 1. LIMPIEZA DE NÓMINAS: Extraer solo el código antes del "|" (ej: "00000000500 | A" -> "00000000500")
    $nominasRaw = json_decode($config->nominas) ?? [];
    $nominas = collect($nominasRaw)->map(function($n) {
        return trim(explode('|', $n)[0]);
    })->unique()->toArray();

    // 2. CONCEPTOS: Limpiar los IDs de asignaciones y patronales (ej: "0000000030|A" -> "0000000030")
    // Esto asegura que coincidan con el 'codconc' de la base de datos de SIGESP
    $asignacionesIds = collect($mapa->asignaciones ?? [])->map(function($c) {
        return trim(explode('|', $c)[0]);
    })->toArray();

    $patronalesIds = collect($mapa->patronales ?? [])->map(function($c) {
        return trim(explode('|', $c)[0]);
    })->toArray();

    // 3. CONSULTA A SIGESP: Obtenemos todos los movimientos del año para ese trabajador
    $movimientos = DB::connection('sigesp')
        ->table('sno_hsalida as hs')
        ->join('sno_hperiodo as hp', function($join) {
            $join->on('hs.codnom', '=', 'hp.codnom')
                 ->on('hs.codperi', '=', 'hp.codperi');
        })
        ->leftJoin('sno_concepto as c', function($join) {
            $join->on('hs.codnom', '=', 'c.codnom')
                 ->on('hs.codconc', '=', 'c.codconc');
        })
        ->select(
            DB::raw("EXTRACT(MONTH FROM hp.fecdesper) as mes"),
            'hs.codconc',
            'c.nomcon',
            'hs.valsal'
        )
        ->where('hs.codper', $codper)
        ->where('hs.anocur', $anio)
        ->whereIn('hs.codnom', $nominas) // Filtramos por las nóminas seleccionadas en el Dashboard
        ->get();

    // 4. PROCESAMIENTO MENSUAL: Agrupamos y sumamos los montos según la configuración
    return collect(range(1, 12))->map(function($mes) use ($movimientos, $asignacionesIds, $patronalesIds) {
        // Filtramos movimientos que pertenecen al mes actual del ciclo
        $dataMes = $movimientos->filter(fn($m) => (int)$m->mes === (int)$mes);

        // Suma de asignaciones (Sueldo, bonos, etc.)
        $totalAsig = $dataMes->filter(fn($m) => in_array(trim($m->codconc), $asignacionesIds))
            ->sum(fn($i) => abs($i->valsal));

        // Filtramos los movimientos que corresponden a conceptos patronales (Ley/Aportes)
        $movsPatronales = $dataMes->filter(fn($m) => in_array(trim($m->codconc), $patronalesIds));

        // Generamos el detalle dinámico para el cuadro inferior del PDF
        $detalle = $movsPatronales->groupBy(fn($m) => trim($m->codconc))
            ->map(function($grupo) {
                return (object)[
                    'monto' => round($grupo->sum(fn($i) => abs($i->valsal)), 2),
                    'nombre' => $grupo->first()->nomcon ?? 'Concepto ' . $grupo->first()->codconc
                ];
            })->toArray();

        return (object)[
            'mes' => (int)$mes,
            'remuneracion' => round($totalAsig, 2),
            'detalle' => $detalle,
            'total_patronales' => round($movsPatronales->sum(fn($i) => abs($i->valsal)), 2)
        ];
    });
}
}
