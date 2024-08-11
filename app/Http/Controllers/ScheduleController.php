<?php

namespace App\Http\Controllers;

use App\Models\Grade;
use App\Models\Group;
use App\Models\Subject;
use App\Models\Working;
use App\Models\Schedule;
use App\Exports\ScheduleExport;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;



class ScheduleController extends Controller
{
    private $populationSize = 5;
    private $maxGenerations = 5;
    private $mutationRate = 0.01;
    private $subjects;
    private $days = 5;
    private $slotsPerDay = 13;
    private $classCombinations;

    private function generateArrayBasedOnHours($input)
    {
        $result = [];

        foreach ($input as $number => $hours) {
            for ($i = 0; $i < $hours; $i++) {
                $result[] = $number;
            }
        }

        return $result;
    }
    public function generateSchedule()
    {


        $this->subjects = Working::all()->pluck('id')->toArray();
        $this->classCombinations = $this->getClassCombinations();
        $population = $this->initializePopulation();
        // dd($population);
        for ($generation = 0; $generation < $this->maxGenerations; $generation++) {
            $population = $this->mutate($population);
            $newPopulation = $this->crossover($population);
            $fitnessScores = $this->calculateFitness($newPopulation);

            $bestSchedule = $this->selectBestSchedule($newPopulation, $fitnessScores);

            if ($this->isSolutionFound($bestSchedule)) {
                return $this->formatSchedule($bestSchedule);
            }

            $population = $newPopulation;
        }

        return $this->formatSchedule($population[0]);
    }

    private function shuffleArray($array)
    {
        $strings = [];
        $integers = [];

        // Pisahkan elemen berdasarkan jenisnya
        foreach ($array as $key => $element) {
            if (is_string($element)) {
                $strings[$key] = $element;
            } elseif (is_null($element)) {
                continue; // Abaikan null agar tidak diacak
            } else {
                $integers[$key] = $element;
            }
        }

        // Acak nilai integer saja
        shuffle($integers);

        // Gabungkan kembali array dengan mempertahankan posisi null dan string
        $shuffledArray = [];

        foreach ($array as $key => $element) {
            if (isset($strings[$key])) {
                $shuffledArray[$key] = $strings[$key];
            } elseif (is_null($element)) {
                $shuffledArray[$key] = null; // Pastikan null tetap di tempatnya
            } else {
                $shuffledArray[$key] = array_shift($integers);
            }
        }

        return $shuffledArray;
    }
    // private function shuffleArray($array)
    // {
    //     $strings = [];
    //     $nonStrings = [];

    //     foreach ($array as $element) {
    //         if (is_string($element)) {
    //             $strings[] = $element;
    //         } else {
    //             $nonStrings[] = $element;
    //         }
    //     }

    //     shuffle($nonStrings);

    //     return array_merge($strings, $nonStrings);
    // }



    private function checkArray($array1, $array2)
    {
        $maxLength = max(count($array1), count($array2));
        $commonKeysValues = [];

        // Menyimpan nilai yang sama dari kedua array, kecuali key 0
        foreach ($array1 as $key => $value) {
            if ($key == 0)
                continue; // Lewati key 0
            if (isset($array2[$key]) && $array2[$key] == $value) {
                $commonKeysValues[$key] = $value;
            }
        }

        // Memindahkan nilai-nilai yang sama dari array1 ke tempat baru
        foreach ($commonKeysValues as $key => $value) {
            $newKey = $key + 1;
            while (isset($array1[$newKey]) || isset($array2[$newKey])) {
                $newKey++;
            }

            // Memindahkan nilai ke tempat baru di array1
            $array1[$newKey] = $value;
            unset($array1[$key]);
        }

        // Menyusun ulang array sehingga panjangnya sama
        $array1 = array_values($array1); // Menghapus kunci yang tidak berurutan

        // Tambahkan elemen default jika panjang kurang dari maxLength
        while (count($array1) < $maxLength) {
            $array1[] = null; // Atau nilai default lainnya
        }

        // Urutkan key secara numerik
        ksort($array1);

        return $array1;
    }
    // private function sortable($collection)
    // {
    //     // Pisahkan elemen pertama jika itu adalah string
    //     $firstElement = is_string($collection[0]) ? array_shift($collection) : null;

    //     // Pisahkan null dan simpan posisi aslinya
    //     $nullPositions = array_keys($collection, null, true);
    //     $filteredArray = array_filter($collection, function ($value) {
    //         return !is_null($value);
    //     });

    //     // Konversi ke integer dan hitung frekuensi
    //     $array = array_map('intval', $filteredArray);
    //     $frequencies = array_count_values($array);

    //     // Buat array yang diurutkan berdasarkan frekuensi
    //     $sortedArray = [];
    //     foreach ($frequencies as $value => $count) {
    //         for ($i = 0; $i < $count; $i++) {
    //             $sortedArray[] = $value;
    //         }
    //     }

    //     // Masukkan kembali string pertama jika ada
    //     if ($firstElement !== null) {
    //         array_unshift($sortedArray, $firstElement);
    //     }

    //     // Masukkan kembali nilai null ke posisi asli mereka
    //     foreach ($nullPositions as $position) {
    //         array_splice($sortedArray, $position, 0, [6]);
    //     }

    //     return $sortedArray;
    // }
    private function sortable($collection)
    {
        // Extract elements and their original keys
        $numericElements = [];
        $stringElements = [];
        $originalKeys = [];

        foreach ($collection as $key => $value) {
            if (is_numeric($value)) {
                $numericElements[$key] = $value; // Preserve the original key
            } else {
                $stringElements[$key] = $value; // Preserve the original key
            }
        }

        // Count frequencies and sort numeric elements
        $int = array_map('intval', $numericElements);
        $frequencies = array_count_values($int);
        $sortedNumeric = [];
        foreach ($frequencies as $value => $count) {
            for ($i = 0; $i < $count; $i++) {
                $sortedNumeric[] = $value;
            }
        }

        // Place the sorted numeric elements back into their original keys
        $sortedArray = array_replace(array_fill_keys(array_keys($collection), null), $stringElements);
        foreach ($sortedNumeric as $index => $value) {
            $key = array_keys($numericElements)[$index];
            $sortedArray[$key] = $value;
        }

        return $sortedArray;
    }


    private function customSort(Collection $collection)
    {
        return $collection->sort(function ($a, $b) {
            if (is_string($a) && is_string($b)) {
                return strcmp($a, $b);
            }
            if (is_string($a)) {
                return -1;
            }
            if (is_string($b)) {
                return 1;
            }
            return $a <=> $b;
        });
    }
    private function initializePopulation()
    {
        $upacara = 'upacara';
        $pembiasaan = 'pembiasaan';
        $istirahat = 'istirahat';

        $works = Working::all();

        // Array untuk menyimpan subject berdasarkan hours mereka
        $subjects = [];
        $hours1Subjects = [];

        foreach ($works as $work) {
            // Tambahkan subject ke array sebanyak jamnya
            for ($i = 0; $i < $work->hours; $i++) {
                $subjects[] = $work->code; // code adalah identifier untuk subject
            }

            // Pisahkan subject dengan hours 1
            if ($work->hours == 1) {
                $hours1Subjects[] = $work->code;
            }
        }

        $population = [];
        for ($i = 0; $i < $this->populationSize; $i++) {
            $schedule = [];
            for ($class = 0; $class < count($this->classCombinations); $class++) {
                $schedule[$class] = [];
                for ($day = 0; $day < $this->days; $day++) {
                    $schedule[$class][$day] = [];

                    // Memastikan dua slot pertama diisi oleh subjects dengan hours 1
                    $usedSlots = 0;
                    for ($slot = 0; $slot < $this->slotsPerDay; $slot++) {
                        if ($day == 0 && $slot == 0) {
                            $schedule[$class][$day][$slot] = $upacara;
                        } elseif ($day > 0 && $slot == 0) {
                            $schedule[$class][$day][$slot] = $pembiasaan;
                        } elseif ($day == 4 && ($slot == 4 || $slot == 7)) {
                            $schedule[$class][$day][$slot] = $istirahat;
                        } elseif ($day >= 0 && $day < 4 && ($slot == 5 || $slot == 9)) {
                            $schedule[$class][$day][$slot] = $istirahat;
                        } else {
                            if ($usedSlots < 2 && !empty($hours1Subjects)) {
                                // Pilih random subject dengan hours 1
                                $schedule[$class][$day][$slot] = $hours1Subjects[array_rand($hours1Subjects)];
                                $usedSlots++;
                            } else {
                                // Isi slot lain dengan subject dari subjects array
                                $schedule[$class][$day][$slot] = !empty($subjects) ? $subjects[array_rand($subjects)] : null;
                            }
                        }
                    }
                }
            }
            $population[] = $schedule;
        }
        return $population;
    }




    private function getClassCombinations()
    {
        $grades = Grade::all();
        $groups = Group::all();

        $combinations = [];
        $id = 0;
        foreach ($grades as $grade) {
            foreach ($groups as $group) {
                $combinations[] = [
                    'id' => $id,
                    'name' => "{$grade->name} {$group->number}",
                ];
                $id++;
            }
        }
        return $combinations;
    }

    private function mutate($population)
    {
        $F = 0.5; // Scaling factor
        $CR = 0.9; // Crossover rate

        $newPopulation = [];

        foreach ($population as $i => $target) {
            $a = $b = $c = $i;
            while ($a == $i)
                $a = array_rand($population);
            while ($b == $i || $b == $a)
                $b = array_rand($population);
            while ($c == $i || $c == $a || $c == $b)
                $c = array_rand($population);

            $mutant = $target; // Start with a copy of the target

            for ($class = 0; $class < count($this->classCombinations); $class++) {
                for ($day = 0; $day < $this->days; $day++) {
                    for ($slot = 0; $slot < $this->slotsPerDay; $slot++) {
                        $currentValue = $target[$class][$day][$slot];

                        if (is_numeric($currentValue) && !is_string($currentValue)) {
                            if (mt_rand() / mt_getrandmax() < $CR) {
                                $mutantGene = $population[$a][$class][$day][$slot] + $F * ($population[$b][$class][$day][$slot] - $population[$c][$class][$day][$slot]);
                                $mutantGene = round($mutantGene);
                                $mutantGene = max(min($mutantGene, max($this->subjects)), min($this->subjects));
                                $mutant[$class][$day][$slot] = $mutantGene;
                            } else {
                                $mutant[$class][$day][$slot] = $currentValue;
                            }
                        }
                    }
                }
            }

            $mutantFitness = $this->calculateFitness([$mutant])[0];
            $targetFitness = $this->calculateFitness([$target])[0];

            if ($mutantFitness >= $targetFitness) {
                $newPopulation[] = $mutant;
            } else {
                $newPopulation[] = $target;
            }
        }

        return $newPopulation;
    }




    private function crossover($population)
    {
        $newPopulation = [];
        for ($i = 0; $i < $this->populationSize; $i++) {
            $parent1 = $population[array_rand($population)];
            $parent2 = $population[array_rand($population)];
            $child = [];
            for ($class = 0; $class < count($this->classCombinations); $class++) {
                $child[$class] = [];
                for ($day = 0; $day < $this->days; $day++) {
                    $child[$class][$day] = [];
                    for ($slot = 0; $slot < $this->slotsPerDay; $slot++) {
                        if (($day == 0 && $slot == 0) || ($day == 0 && $slot == 1) || ($day > 0 && $slot == 0)) {
                            $child[$class][$day][$slot] = $parent1[$class][$day][$slot];
                        } else {
                            $child[$class][$day][$slot] = mt_rand(0, 1) ? $parent1[$class][$day][$slot] : $parent2[$class][$day][$slot];
                        }
                    }
                }
            }
            $newPopulation[] = $child;
        }
        return $newPopulation;
    }



    private function calculateFitness($population)
    {
        $fitnessScores = [];
        foreach ($population as $schedule) {
            $score = 0;

            for ($class = 0; $class < count($this->classCombinations); $class++) {
                for ($day = 0; $day < $this->days; $day++) {
                    // Check for schedule conflicts within each day
                    if (count(array_unique($schedule[$class][$day])) == count($schedule[$class][$day])) {
                        $score += 1;
                    }

                    // Check for PE classes not in afternoon slots
                    $peSubjects = Subject::where('name', 'like', '%olahraga%')->pluck('id')->toArray();
                    for ($slot = 7; $slot < $this->slotsPerDay; $slot++) {
                        if (in_array($schedule[$class][$day][$slot], $peSubjects)) {
                            $score -= 1;
                        }
                    }
                }
            }

            // Add more fitness criteria checks here

            $fitnessScores[] = $score;
        }
        return $fitnessScores;
    }

    private function selectBestSchedule($population, $fitnessScores)
    {
        $maxFitness = max($fitnessScores);
        $bestIndex = array_search($maxFitness, $fitnessScores);
        return $population[$bestIndex];
    }

    private function isSolutionFound($schedule)
    {
        $fitnessScore = $this->calculateFitness([$schedule])[0];
        return $fitnessScore >= 50;
    }

    private function isSlotTaken($day, $timeIn)
    {
        return DB::table('schedules')
            ->where('day', $day)
            ->where('time_in', $timeIn)
            ->exists();
    }

    private function manipulateArray($array)
    {
        $duplicates = array();
        $positions = array();

        foreach (array_slice($array, 1) as $key => $value) {
            if (in_array($value, $duplicates)) {
                $positions[$value][] = $key + 1;
            } elseif (in_array($value, $array)) {
                $duplicates[] = $value;
                $positions[$value] = [$key + 1];
            }
        }

        // Step 2: Tukar nilai di key 5 dan 9 dengan salah satu nilai yang berulang
        foreach ([5, 9] as $key) {
            $value = $array[$key];
            foreach ($positions as $dup_value => $keys) {
                if (!in_array($key, $keys)) {
                    $first_key = array_shift($positions[$dup_value]);
                    $array[$first_key] = $value;
                    $array[$key] = null;
                    break;
                }
            }
        }

        // Step 3: Urutkan kembali array
        $filtered_array = array_filter($array, function ($v) {
            return $v !== null;
        });

        return array_merge(array_slice($array, 0, 1), $filtered_array);


    }

    private function formatSchedule($schedule)
    {
        $formattedSchedule = [];
        foreach ($schedule as $class => $days) {
            $formattedSchedule[$class] = [];
            foreach ($days as $day => $slots) {
                ksort($slots);
                foreach ($slots as $slot => $subjectId) {
                    $formattedSchedule[$class][$day][$slot] = $subjectId;
                }
            }
        }

        // dd($formattedSchedule);

        // Mengambil kode dari tabel working dan menggabungkan dengan subject name
        $subjects = Working::with('subject')->orderBy('code')->get()->mapWithKeys(function ($working) {
            return [$working->id => $working->code];
        })->toArray();
        $subjects['upacara'] = 'Upacara';
        $subjects['pembiasaan'] = 'Pembiasaan';
        $subjects['istirahat'] = 'Istirahat';

        // Mengambil data grade dan group
        $grades = Grade::pluck('name', 'id')->toArray();
        $groups = Group::pluck('number', 'id')->toArray();

        $classes = [];
        foreach ($grades as $gradeId => $gradeName) {
            foreach ($groups as $groupId => $groupNumber) {
                $classes[] = [
                    'grade' => $gradeName,
                    'group' => $groupNumber
                ];
            }
        }

        // Mengambil nama teacher dari tabel working dan menghubungkannya dengan subject
        $workingTeachers = Working::with('teacher')->get()->mapWithKeys(function ($working) {
            return [$working->id => $working->teacher ? $working->teacher->name : '-'];
        });
        $workingTeachers['upacara'] = '-';
        $workingTeachers['pembiasaan'] = '-';
        $workingTeachers['istirahat'] = '-';

        DB::table('schedules')->truncate();

        // Insert data baru ke tabel schedule


        foreach ($formattedSchedule as $classIndex => $days) {
            foreach ($days as $day => $slots) {

                $slots = $this->sortable($slots);
                // $newSlots = ['upacara', 'pembiasaan']; // Mulai dengan upacara dan pembiasaan
                $newSlots = []; // Mulai dengan upacara dan pembiasaan

                $workingHours = Working::pluck('hours', 'id')->toArray();
                $adjustedSlots = [];

                foreach ($slots as $slot) {
                    if (!isset($workingHours[$slot])) {
                        continue;
                    }

                    $hours = $workingHours[$slot];

                    if ($hours == 1 && (!isset($adjustedSlots[$slot]) || $adjustedSlots[$slot] < $hours)) {
                        if ($day == 4 && (count($newSlots) == 3 || count($newSlots) == 6)) {
                            $newSlots[] = 'istirahat';
                        } elseif ($day >= 0 && $day < 4 && (count($newSlots) == 4 || count($newSlots) == 8)) {
                            $newSlots[] = 'istirahat';
                        }
                        $newSlots[] = $slot;
                        $adjustedSlots[$slot] = isset($adjustedSlots[$slot]) ? $adjustedSlots[$slot] + 1 : 1;
                    }
                }

                // Cek apakah adjustedSlots sudah memiliki 2 elemen dengan hours 1
                $hours1Count = count(array_filter($adjustedSlots, function ($count, $slot) use ($workingHours) {
                    return isset($workingHours[$slot]) && $workingHours[$slot] == 1 && $count > 0;
                }, ARRAY_FILTER_USE_BOTH));

                // Jika belum, tambahkan elemen lain yang memiliki hours 1 ke adjustedSlots
                while ($hours1Count < 2) {
                    $added = false;
                    foreach ($slots as $slot) {
                        if (isset($workingHours[$slot]) && $workingHours[$slot] == 1 && !isset($adjustedSlots[$slot])) {
                            if ($day == 4 && (count($newSlots) == 3 || count($newSlots) == 6)) {
                                $newSlots[] = 'istirahat';
                            } elseif ($day >= 0 && $day < 4 && (count($newSlots) == 4 || count($newSlots) == 8)) {
                                $newSlots[] = 'istirahat';
                            }
                            $newSlots[] = $slot;
                            $adjustedSlots[$slot] = 1;
                            $hours1Count++;
                            $added = true;

                            if ($hours1Count == 2) {
                                break;
                            }
                        }
                    }

                    // Jika masih kurang, tambahkan elemen dengan hours 1 langsung
                    if (!$added) {
                        foreach ($workingHours as $slotId => $hours) {
                            if ($hours == 1 && !in_array($slotId, $newSlots)) {
                                if ($day == 4 && (count($newSlots) == 3 || count($newSlots) == 6)) {
                                    $newSlots[] = 'istirahat';
                                } elseif ($day >= 0 && $day < 4 && (count($newSlots) == 4 || count($newSlots) == 8)) {
                                    $newSlots[] = 'istirahat';
                                }
                                $newSlots[] = $slotId;
                                $adjustedSlots[$slotId] = 1;
                                $hours1Count++;

                                if ($hours1Count == 2) {
                                    break;
                                }
                            }
                        }
                    }

                    // Jika sudah ada 2 elemen dengan hours 1, keluar dari loop
                    if ($hours1Count == 2) {
                        break;
                    }
                }

                // Memasukkan elemen dengan hours 2 atau lebih
                foreach ($slots as $slot) {
                    if (isset($workingHours[$slot]) && $workingHours[$slot] >= 2) {
                        $remainingHours = $workingHours[$slot] - (isset($adjustedSlots[$slot]) ? $adjustedSlots[$slot] : 0);
                        for ($i = 0; $i < $remainingHours; $i++) {
                            if ($day == 4 && (count($newSlots) == 3 || count($newSlots) == 6)) {
                                $newSlots[] = 'istirahat';
                            } elseif ($day >= 0 && $day < 4 && (count($newSlots) == 4 || count($newSlots) == 8)) {
                                $newSlots[] = 'istirahat';
                            }
                            if (count($newSlots) < 12) {
                                $newSlots[] = $slot;
                                $adjustedSlots[$slot] = isset($adjustedSlots[$slot]) ? $adjustedSlots[$slot] + 1 : 1;
                            }
                        }
                    }
                }

                // Pastikan panjang array adalah 14
                while (count($newSlots) < 12) {
                    foreach ($workingHours as $slot => $hours) {
                        if (!isset($adjustedSlots[$slot]) || $adjustedSlots[$slot] < $hours) {
                            if ($day == 4 && (count($newSlots) == 3 || count($newSlots) == 6)) {
                                $newSlots[] = 'istirahat';
                            } elseif ($day >= 0 && $day < 4 && (count($newSlots) == 4 || count($newSlots) == 8)) {
                                $newSlots[] = 'istirahat';
                            }
                            $newSlots[] = $slot;
                            $adjustedSlots[$slot] = isset($adjustedSlots[$slot]) ? $adjustedSlots[$slot] + 1 : 1;

                            if (count($newSlots) == 12) {
                                break;
                            }
                        }
                    }
                }

                // Jika masih kurang, tambahkan elemen random dari workingHours
                while (count($newSlots) < 12) {
                    $randomSlot = array_rand($workingHours);
                    if (!in_array($randomSlot, $newSlots)) {
                        $newSlots[] = $randomSlot;
                    }
                }


                // Final array of slots
                ksort($slots);
                $newSlots = $this->shuffleArray($newSlots);
                $elementsToReplace = array_slice($newSlots, 0);

                array_splice($slots, 1, count($elementsToReplace), $elementsToReplace);

                $slots = array_slice($slots, 0, 13);
                $slots = $this->sortable($slots);
                // dd($slots);

                // $slots = $this->manipulateArray($slots);
                // dd($this->shuffleArray($newSlots), $slots);

                foreach ($slots as $slot => $workingId) {
                    $subjectName = $subjects[$workingId] ?? '-';
                    if ($subjectName != '-') {

                        $timeIn = $this->getTimeIn($day, $slot);
                        $timeOut = $this->getTimeOut($day, $slot);
                        $classInfo = $classes[$classIndex];
                        $class = "{$classInfo['grade']} {$classInfo['group']}";

                        // Cek apakah ada bentrok dengan kelas lain
                        $conflict = DB::table('schedules')
                            ->where('day', ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat'][$day])
                            ->where('time_in', $timeIn)
                            ->where('time_out', $timeOut)
                            ->where('working_id', $workingId)
                            ->exists();

                        if ($conflict) {
                            // Cari waktu lain yang tidak bentrok
                            $newSlot = $this->findAvailableSlot($day, $slots, $workingId, $classes);
                            if ($newSlot !== null) {
                                $timeIn = $this->getTimeIn($day, $newSlot);
                                $timeOut = $this->getTimeOut($day, $newSlot);
                            } else {
                                continue; // Tidak ada slot yang tersedia, skip penyimpanan
                            }
                        }

                        // Masukkan jadwal ke database
                        DB::table('schedules')->insert([
                            'working_id' => $workingId == 'upacara' || $workingId == 'pembiasaan' || $workingId == 'istirahat' ? NULL : $workingId,
                            'name' => $workingId == 'upacara' || $workingId == 'pembiasaan' || $workingId == 'istirahat' ? $workingId : NULL,
                            'class' => $class,
                            'day' => ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat'][$day],
                            'time_in' => $timeIn,
                            'time_out' => $timeOut,
                        ]);
                    }
                }
            }
        }


        // Mengambil dan mengurutkan jadwal berdasarkan hari, waktu, dan kode subject
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

        $classes2 = DB::table('schedules')
            ->select('class')
            ->distinct()
            ->get()
            ->pluck('class');

        return view('dashboard.schedule.result', compact('schedules', 'subjects', 'classes2', 'workingTeachers'));
    }

    private function findAvailableSlot($day, $slots, $workingId, $classes)
    {
        foreach ($slots as $slot => $slotWorkingId) {
            $timeIn = $this->getTimeIn($day, $slot);
            $timeOut = $this->getTimeOut($day, $slot);

            $conflict = DB::table('schedules')
                ->where('day', ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat'][$day])
                ->where('time_in', $timeIn)
                ->where('time_out', $timeOut)
                ->where('working_id', $workingId)
                ->exists();

            if (!$conflict) {
                return $slot; // Slot ini tersedia
            }
        }

        return null; // Tidak ada slot yang tersedia
    }

    private function getTimeOut($day, $slot)
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

        // Pilih waktu berdasarkan hari dan slot
        $timeInOut = $timeSlots[$slot];
        if ($day == 0) {
            return explode(" - ", $timeInOut[0])[1];
        } elseif ($day == 4 && isset($timeInOut[2])) {
            return explode(" - ", $timeInOut[2])[1];
        } else {
            return explode(" - ", $timeInOut[1])[1];
        }
    }
    private function getTimeIn($day, $slot)
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

        // Pilih waktu berdasarkan hari dan slot
        $timeInOut = $timeSlots[$slot];
        if ($day == 0) {
            return explode(" - ", $timeInOut[0])[0];
        } elseif ($day == 4 && isset($timeInOut[2])) {
            return explode(" - ", $timeInOut[2])[0];
        } else {
            return explode(" - ", $timeInOut[1])[0];
        }
    }

    private function isBreakTime($day, $slot)
    {
        if ($day == 4) {
            return $slot == 4 || $slot == 7;
        }
        return $slot == 5 || $slot == 9;
    }

    public function exportPdf()
    {

        $title = 'Data Jadwal.pdf';

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

        $pdf = Pdf::loadView('dashboard.schedule.pdf', compact('schedules', 'classes', 'subjects'))->setPaper('a4', 'landscape');
        return $pdf->download($title);
    }

    public function exportExcel()
    {
        return Excel::download(new ScheduleExport, 'Data Jadwal.xlsx');
    }

}
