<?php

namespace App\Exports;

use App\Models\Constancia;
use Maatwebsite\Excel\Concerns\FromCollection;

class ConstanciasExport implements FromCollection
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return Constancia::all();
    }
}
