<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class GradesExport implements FromCollection, WithMapping, WithHeadings, WithStyles
{
    /**
     * grade
     *
     * @var mixed
     */
    protected $grades;

    /**
     * __construct
     *
     * @param  mixed $grades
     * @return void
     */
    public function __construct($grades)
    {
        $this->grades = $grades;
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        return $this->grades;
    }

    public function map($grades): array
    {
        return [
            $grades->exam->title,
            $grades->exam_session->title,
            $grades->student->name,
            $grades->exam->classroom->title,
            $grades->exam->lesson->title,
            $grades->grade,
        ];
    }

    public function headings(): array
    {
        return [
            'Ujian',
            'Sesi',
            'Nama Siswa',
            'Kelas',
            'Pelajaran',
            'Nilai'
        ];
    }

    /**
     * Apply styles to the worksheet.
     *
     * @param  Worksheet  $sheet
     * @return array
     */
    public function styles(Worksheet $sheet)
    {
        // Mengatur font heading menjadi bold
        $sheet->getStyle('A1:F1')->applyFromArray([
            'font' => [
                'bold' => true,
            ],
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
            ],
            'borders' => [
                'outline' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    'color' => ['argb' => '000000'],
                ],
            ],
        ]);

        // Mengatur border untuk semua sel
        $sheet->getStyle('A1:F' . ($sheet->getHighestRow()))->applyFromArray([
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    'color' => ['argb' => '000000'],
                ],
            ],
        ]);

        // Mengatur ukuran kolom agar otomatis sesuai dengan isi
        foreach (range('A', 'F') as $columnID) {
            $sheet->getColumnDimension($columnID)->setAutoSize(true);
        }

        return [];
    }
}
