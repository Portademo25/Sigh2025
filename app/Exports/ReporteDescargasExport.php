<?php

namespace App\Exports;

use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class ReporteDescargasExport implements FromCollection, WithHeadings, WithMapping
{
    protected $inicio, $fin;

    public function __construct($inicio, $fin) {
        $this->inicio = $inicio;
        $this->fin = $fin;
    }

    public function collection() {
        return DB::table('reporte_descargas')
            ->select('created_at', 'cedula', 'nombre_empleado', 'tipo_reporte', 'detalles')
            ->whereBetween('created_at', [$this->inicio . ' 00:00:00', $this->fin . ' 23:59:59'])
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function headings(): array {
        return ['Fecha', 'Cédula', 'Nombre Empleado', 'Documento', 'Detalles'];
    }

    public function map($descarga): array {
        return [
            $descarga->created_at,
            $descarga->cedula,
            $descarga->nombre_empleado,
            $descarga->tipo_reporte,
            $descarga->detalles,
        ];
    }
}
