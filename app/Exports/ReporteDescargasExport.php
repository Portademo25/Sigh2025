<?php

namespace App\Exports;

use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class ReporteDescargasExport implements FromCollection, WithHeadings, WithMapping
{
    protected $inicio;
    protected $fin;

    public function __construct($inicio, $fin)
    {
        $this->inicio = $inicio;
        $this->fin = $fin;
    }

    public function collection()
    {
        return DB::table('reporte_descargas')
            ->whereBetween('created_at', [$this->inicio . ' 00:00:00', $this->fin . ' 23:59:59'])
            ->orderBy('created_at', 'desc')
            ->get();
    }

    // Definimos los encabezados del Excel
    public function headings(): array
    {
        return [
            'Fecha y Hora',
            'Cédula',
            'Trabajador',
            'Tipo de Reporte',
            'Detalles / Periodo',
        ];
    }

    // Mapeamos los datos de la tabla a las columnas del Excel
    public function map($reporte): array
    {
        return [
            \Carbon\Carbon::parse($reporte->created_at)->format('d/m/Y h:i A'),
            $reporte->cedula,
            $reporte->nombre_trabajador, // El campo que añadimos
            $reporte->tipo_reporte,
            $reporte->detalles,
        ];
    }
}