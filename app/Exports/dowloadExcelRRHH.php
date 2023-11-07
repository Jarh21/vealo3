<?php

namespace App\Exports;

use App\Models\Rrhh;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class dowloadExcelRRHH implements FromCollection, WithHeadings
{
    public function collection()
    {
        return Rrhh::select('nombres','fecha_ingreso','rif','empresa_rif')->get();
    }

    public function headings(): array
    {
        return [
            'Nombres',
            'Fecha ingreso',
            'Rif empleado',
            'empresa',
        ];
    }
}