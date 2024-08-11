<?php

namespace App\Exports;

use App\Models\Subject;
use App\Models\Working;
use App\Models\Schedule;
use Illuminate\Support\Facades\DB;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ScheduleExport implements FromView, WithStyles, ShouldAutoSize, WithTitle
{

    public function title(): string
    {
        return 'Jadwal Pelajaran';
    }

    public function view(): View
    {
        $title = 'Data Jadwal.xlsx';

        $subjects = Working::with('subject')->orderBy('code')->get()->mapWithKeys(function ($working) {
            return [$working->id => $working->code];
        })->toArray();
        $subjects['upacara'] = 'Upacara';
        $subjects['pembiasaan'] = 'Pembiasaan';

        $classes = DB::table('schedules')
            ->select('class')
            ->distinct()
            ->get()
            ->pluck('class');
        $schedules = Schedule::leftJoin('workings', 'schedules.working_id', '=', 'workings.id')
            ->leftJoin('subjects', 'workings.subject_id', '=', 'subjects.id')
            ->select(
                'schedules.id',
                'schedules.working_id',
                'schedules.name',
                'schedules.class',
                'schedules.day',
                'schedules.time_in',
                'schedules.time_out',
                'subjects.name as subject_name',
                'workings.code'
            )
            ->orderBy('schedules.day')
            ->orderBy('schedules.time_in')
            ->orderBy('workings.code')
            ->get()
            ->groupBy(['day', 'time_in']);


        return view('dashboard.schedule.excel', compact('schedules', 'classes', 'subjects'));
    }

    public function styles(Worksheet $sheet)
    {
        $totalCount = Schedule::select('day', DB::raw('COUNT(DISTINCT time_in) as count_per_day'))
            ->groupBy('day')
            ->get()
            ->sum('count_per_day');

        $totalCount += 10;

        $styleArray = [
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                ],
            ],
            'font' => [
                'size' => 12,
                'name' => 'Times New Roman'
            ],
        ];
        $sheet->getStyle('A2:O' . $totalCount)->applyFromArray($styleArray);
        $sheet->getStyle('A1:O1')->applyFromArray([
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                ],
            ],
            'font' => [
                'bold' => true,
                'size' => 13,
                'name' => 'Times New Roman'
            ],
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
            ],
        ]);
    }

}
