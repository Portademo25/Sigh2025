<?php

namespace App\Exports;

use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Carbon\Carbon;

class ReporteDescargasExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize
{
    protected $inicio;
    protected $fin;

    public function __construct($inicio, $fin)
    {
        $this->inicio = Carbon::parse($inicio)->startOfDay();
        $this->fin = Carbon::parse($fin)->endOfDay();
    }

    public function collection()
    {
        return DB::table('reporte_descargas')
            ->whereBetween('created_at', [$this->inicio, $this->fin])
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Headings modificados para auditoría unificada
     */
    public function headings(): array
    {
        return [
            'Fecha y Hora',
            'Cédula de Identidad',
            'Nombre del Trabajador',
            'Tipo de Documento',
            'Detalles del Movimiento',
        ];
    }

    public function map($reporte): array
    {
        return [
            Carbon::parse($reporte->created_at)->format('d/m/Y h:i A'),
            $reporte->cedula,
            $reporte->nombre_empleado,
            $reporte->tipo_reporte, // Aquí saldrá: "Planilla ARC", "Recibo de Pago", etc.
            $reporte->detalles ?? 'Sin detalles adicionales',
        ];
    }
}
