<?php

namespace App\Exports;

use App\Models\SupportPlan;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class SupportPlanExport implements FromCollection, WithHeadings, WithMapping, WithTitle, ShouldAutoSize, WithStyles, WithEvents
{
    /**
     * The support plan to export.
     *
     * @var SupportPlan
     */
    protected SupportPlan $supportPlan;

    /**
     * Create a new export instance.
     *
     * @param SupportPlan $supportPlan
     * @return void
     */
    public function __construct(SupportPlan $supportPlan)
    {
        $this->supportPlan = $supportPlan;
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        // No se usa, pero es requerido por la interfaz
        return collect();
    }

    /**
     * @return array
     */
    public function headings(): array
    {
        return [
            'Nombre del Alumno/a',
            'Fecha de Nacimiento',
            'Nivel Educativo',
            'Curso',
            'Año Escolar',
            'Tutor',
            'Equipo de Evaluación',
            'Profesor/a',
            'Fecha de Revisión',
            'Fecha de Elaboración',
            'Fecha de Acuerdo Familiar',
            'Datos Adicionales',
        ];
    }

    /**
     * @param SupportPlan $supportPlan
     * @return array
     */
    public function map($supportPlan): array
    {
        return [
            $supportPlan->student_name,
            $supportPlan->birth_date ? $supportPlan->birth_date->format('d/m/Y') : '',
            $supportPlan->educational_level,
            $supportPlan->course,
            $supportPlan->school_year,
            $supportPlan->tutor_name,
            $supportPlan->assessment_team,
            $supportPlan->teacher_name,
            $supportPlan->revision_date ? $supportPlan->revision_date->format('d/m/Y') : '',
            $supportPlan->elaboration_date ? $supportPlan->elaboration_date->format('d/m/Y') : '',
            $supportPlan->family_agreement_date ? $supportPlan->family_agreement_date->format('d/m/Y') : '',
            $supportPlan->other_data,
        ];
    }

    /**
     * @return string
     */
    public function title(): string
    {
        return 'Pla de suport individualitzat';
    }

    /**
     * @param Worksheet $sheet
     * @return array
     */
    public function styles(Worksheet $sheet): array
    {
        return [];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet;
                $supportPlan = $this->supportPlan;

                // Limpiar hoja
                $sheet->getDelegate()->getParent()->removeSheetByIndex(0);

                // Título
                $sheet->setCellValue('A1', 'Pla de suport individualitzat (educació bàsica: educació primària)');
                $sheet->mergeCells('A1:F1');
                $sheet->getStyle('A1')->applyFromArray([
                    'font' => ['bold' => true, 'size' => 16, 'name' => 'Arial'],
                    'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER],
                ]);

                $row = 3;
                // DADES PERSONALS
                $sheet->setCellValue('A'.$row, 'DADES PERSONALS');
                $sheet->mergeCells('A'.$row.':F'.$row);
                $sheet->getStyle('A'.$row.':F'.$row)->applyFromArray([
                    'font' => ['bold' => true, 'size' => 13, 'name' => 'Arial', 'color' => ['rgb' => 'FFFFFF']],
                    'fill' => [
                        'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                        'startColor' => ['rgb' => '6ab0e6'],
                    ],
                    'borders' => ['allBorders' => ['borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN]],
                ]);
                $row++;
                $sheet->fromArray([
                    ["Nom de l'alumne/a:", $supportPlan->student_name, 'Data de naixement:', $supportPlan->birth_date ? $supportPlan->birth_date->format('d/m/Y') : '', 'Nom dels tutors i/o tutores legals:', $supportPlan->tutor_name],
                    ['Lloc de naixement:', '', 'Telèfon:', '', '', ''],
                    ['Adreça:', '', "Llengua d'ús habitual:", $supportPlan->usual_language, 'Altres llengües que coneix:', ''],
                ], null, 'A'.$row);
                $sheet->getStyle('A'.($row).':F'.($row+2))->applyFromArray([
                    'font' => ['name' => 'Arial', 'size' => 12],
                    'borders' => ['allBorders' => ['borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN]],
                ]);
                $row += 3;
                // Fila vacía
                $sheet->fromArray([['', '', '', '', '', '']], null, 'A'.$row);
                $sheet->getStyle('A'.$row.':F'.$row)->applyFromArray([
                    'borders' => ['allBorders' => ['borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN]],
                ]);
                $row++;
                // DADES ESCOLARS
                $sheet->setCellValue('A'.$row, 'DADES ESCOLARS');
                $sheet->mergeCells('A'.$row.':F'.$row);
                $sheet->getStyle('A'.$row.':F'.$row)->applyFromArray([
                    'font' => ['bold' => true, 'size' => 13, 'name' => 'Arial', 'color' => ['rgb' => 'FFFFFF']],
                    'fill' => [
                        'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                        'startColor' => ['rgb' => '6ab0e6'],
                    ],
                    'borders' => ['allBorders' => ['borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN]],
                ]);
                $row++;
                $sheet->fromArray([
                    ['Curs:', $supportPlan->course, 'Grup:', '', 'Tutor/a:', $supportPlan->tutor_name],
                    ["Data d'incorporació al centre:", '', "Data d'arribada a Catalunya:", '', "Data d'incorporació al sistema educatiu català:", ''],
                    ['Centres on ha estat matriculat anteriorment:', '', 'Escolarització prèvia:', '', 'Retenció de curs:', ''],
                    ["Altres informacions d'interès:", $supportPlan->other_data, '', '', '', ''],
                ], null, 'A'.$row);
                $sheet->getStyle('A'.$row.':F'.($row+3))->applyFromArray([
                    'font' => ['name' => 'Arial', 'size' => 12],
                    'borders' => ['allBorders' => ['borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN]],
                ]);
            }
        ];
    }
}
