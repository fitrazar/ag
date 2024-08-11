@extends('layouts.app')
@section('title', 'Data Jadwal')
@section('content')
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Jadwal</h1>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Data Jadwal</h6>
        </div>

        <div class="card-body">

            <a href="{{ route('dashboard.schedule.exportPdf') }}" class="btn btn-primary" target="_blank">Export PDF</a>
            <a href="{{ route('dashboard.schedule.exportExcel') }}" class="btn btn-success" target="_blank">Export Excel</a>

            @php
                // Mapping nama hari menjadi angka
                $daysMap = [
                    'Senin' => 0,
                    'Selasa' => 1,
                    'Rabu' => 2,
                    'Kamis' => 3,
                    'Jumat' => 4,
                ];

                $slotsMap = [
                    0 => 13, // Senin sampai Kamis
                    1 => 13,
                    2 => 13,
                    3 => 13,
                    4 => 11, // Jumat
                ];

                $breakSlotsMap = [
                    0 => [5, 9], // Senin sampai Kamis: Istirahat di slot ke-6 dan ke-10
                    1 => [5, 9],
                    2 => [5, 9],
                    3 => [5, 9],
                    4 => [4, 7], // Jumat: Istirahat di slot ke-5 dan ke-8
                ];
            @endphp

            {{-- <h5>{{ $dayName }}</h5> --}}
            <div class="table-responsive mb-4 mt-4">
                <table id="example" class="table table-bordered" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>Hari</th>
                            <th>Jam ke</th>
                            <th>Waktu</th>
                            @foreach ($classes2 as $class)
                                <th>{{ $class }}</th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($daysMap as $dayName => $dayIndex)
                            @php
                                $no = 1;
                                $slots = $slotsMap[$dayIndex];
                                $timeSlots = getTimeSlots($dayIndex, $slots);
                                $breakSlots = $breakSlotsMap[$dayIndex];
                            @endphp
                            @for ($slot = 0; $slot < $slots; $slot++)
                                @php
                                    $timeIn = explode(' - ', $timeSlots[$slot])[0];
                                    $timeIn = \Carbon\Carbon::parse($timeIn)->format('H:i:s');
                                @endphp
                                <tr>
                                    @if ($slot == 0)
                                        <td rowspan="{{ $slots }}" class="text-center fw-bold"
                                            style="writing-mode: vertical-lr;">
                                            <span style="text-orientation: upright;">
                                                {{ $dayName }}
                                            </span>
                                        </td>
                                    @endif
                                    <td>{{ $no++ }}</td>
                                    <td>{{ $timeSlots[$slot] }}</td>
                                    {{-- @if ($slot == 6)
                                            @dd($schedules)
                                        @endif --}}
                                    @php
                                        $classSchedule = $schedules[$dayName][$timeIn]->first() ?? null;
                                        $subjectName = $classSchedule
                                            ? $classSchedule->name ?? $subjects[$classSchedule->working_id ?? 1]
                                            : '-';
                                    @endphp
                                    @if ($subjectName == 'upacara' || $subjectName == 'pembiasaan' || $subjectName == 'istirahat')
                                        <td colspan="{{ count($classes2) }}" class="align-middle fw-bold text-center">
                                            {{ ucfirst($subjectName) }}
                                        </td>
                                    @else
                                        @foreach ($classes2 as $class)
                                            @php
                                                $classSchedule =
                                                    $schedules[$dayName][$timeIn]->where('class', $class)->first() ??
                                                    null;
                                                $subjectName = $classSchedule
                                                    ? $classSchedule->name ?? $subjects[$classSchedule->working_id]
                                                    : '-';
                                            @endphp
                                            <td>{{ $subjectName }}</td>
                                        @endforeach
                                    @endif
                                </tr>
                            @endfor
                        @endforeach
                    </tbody>
                </table>
            </div>

        </div>
    </div>

@endsection

@php
    function getTimeSlots($day, $slots)
    {
        $timeSlots = [
            '0' => ['06:45 - 07:30', '06:45 - 07:15'],
            '1' => ['07:30 - 08:15', '07:15 - 08:00'],
            '2' => ['08:15 - 09:00', '08:00 - 08:45'],
            '3' => ['09:00 - 09:40', '08:45 - 09:25'],
            '4' => ['09:40 - 10:20', '09:25 - 10:05'],
            '5' => ['10:20 - 10:35', '10:05 - 10:20'],
            '6' => ['10:35 - 11:15', '10:20 - 11:00'],
            '7' => ['11:15 - 11:55', '11:00 - 11:40', '11:15 - 12:45'],
            '8' => ['11:55 - 12:25', '11:40 - 12:20', '12:45 - 13:25'],
            '9' => ['12:25 - 13:05', '12:20 - 12:50', '13:25 - 14:05'],
            '10' => ['13:05 - 13:45', '12:50 - 13:30', '14:05 - 14:45'],
            '11' => ['13:45 - 14:25', '13:30 - 14:10'],
            '12' => ['14:25 - 15:05', '14:10 - 14:50'],
        ];

        $result = [];
        for ($i = 0; $i < $slots; $i++) {
            $result[$i] =
                $day == 0
                    ? $timeSlots[$i][0]
                    : ($day == 4 && isset($timeSlots[$i][2])
                        ? $timeSlots[$i][2]
                        : $timeSlots[$i][1]);
        }
        return $result;
    }

    function isBreakTime($day, $slot)
    {
        if ($day == 4) {
            return $slot == 4 || $slot == 7;
        }
        return $slot == 5 || $slot == 9;
    }
@endphp
