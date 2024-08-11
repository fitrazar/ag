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

<table style="border: 1px solid black">
    <thead>
        <tr>
            <th align="center" valign="center">Hari</th>
            <th align="center" valign="center">Jam ke</th>
            <th align="center" valign="center">Waktu</th>
            @foreach ($classes as $class)
                <th align="center" valign="center">{{ $class }}</th>
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
                        <td rowspan="{{ $slots }}" align="center" valign="center" style="font-weight: bold;">
                            {{ $dayName }}
                        </td>
                    @endif
                    <td align="center" valign="center">{{ $no++ }}</td>
                    <td align="center" valign="center">{{ $timeSlots[$slot] }}</td>
                    @if (in_array($slot, $breakSlots))
                        <td colspan="{{ count($classes) }}" align="center" valign="center" style="font-weight: bold;">
                            Istirahat
                        </td>
                    @else
                        @php
                            $classSchedule = $schedules[$dayName][$timeIn]->first() ?? null;
                            // dd($classSchedule);
                            $subjectName = $classSchedule
                                ? $classSchedule->name ?? $subjects[$classSchedule->working_id]
                                : '-';
                        @endphp
                        @if ($subjectName == 'upacara' || $subjectName == 'pembiasaan')
                            <td colspan="{{ count($classes) }}" align="center" valign="center"
                                style="font-weight: bold;">
                                {{ ucfirst($subjectName) }}
                            </td>
                        @else
                            @foreach ($classes as $class)
                                @php
                                    $classSchedule =
                                        $schedules[$dayName][$timeIn]->where('class', $class)->first() ?? null;
                                    $subjectName = $classSchedule
                                        ? $classSchedule->name ?? $subjects[$classSchedule->working_id]
                                        : '-';
                                @endphp
                                <td align="center" valign="center">{{ $subjectName }}</td>
                            @endforeach
                        @endif
                    @endif
                </tr>
            @endfor
        @endforeach
    </tbody>
</table>
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
