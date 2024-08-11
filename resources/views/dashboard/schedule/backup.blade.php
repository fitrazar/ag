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

        @if (session()->has('success'))
            <div class="alert alert-success d-flex justify-content-center m-3" role="alert">
                {{ session('success') }}
            </div>
        @endif

        <div class="card-body">

            <a href="{{ route('dashboard.schedule.exportPdf') }}" class="btn btn-primary" target="_blank">Export PDF</a>
            <a href="{{ route('dashboard.schedule.exportExcel') }}" class="btn btn-success" target="_blank">Export Excel</a>

            @for ($day = 0; $day < 5; $day++)
                <h5>{{ ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat'][$day] }}</h5>
                <div class="table-responsive mb-4">
                    <table id="example" class="table table-bordered" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Waktu</th>
                                @foreach ($classes as $class)
                                    <th>{{ $class['grade'] }} {{ $class['major'] }} {{ $class['group'] }}</th>
                                @endforeach
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $slots = $day == 4 ? 11 : 13;
                                $timeSlots = getTimeSlots($day, $slots);
                            @endphp
                            @foreach ($timeSlots as $slot => $time)
                                <tr>
                                    <td>{{ $slot + 1 }}</td>
                                    <td>{{ $time }}</td>
                                    @foreach ($formattedSchedule as $classIndex => $days)
                                        @php
                                            $subjectId = $days[$day][$slot];
                                            $subjectName = $subjects[$subjectId] ?? '-';
                                            $color = $subjectColors[$subjectId] ?? '#000000';
                                            $teacherName = $subjectTeachers[$subjectId] ?? '-';
                                        @endphp
                                        <td class="{{ isBreakTime($day, $slot) ? 'text-muted' : '' }}">
                                            @if (isBreakTime($day, $slot))
                                                Istirahat
                                            @else
                                                <span class="badge"
                                                    style="background-color: {{ $color }}">{{ $subjectName }}</span>
                                                <br>
                                                <small class="text-muted">{{ $teacherName }}</small>
                                            @endif
                                        </td>
                                    @endforeach
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endfor
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
