<?php

namespace App\Http\Controllers;

use App\Models\Grade;
use App\Models\Group;
use App\Models\Major;
use App\Models\Subject;
use App\Models\Teacher;
use Illuminate\Http\Request;
use App\Exports\SubjectExport;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;
use Yajra\DataTables\Facades\DataTables;

class SubjectController extends Controller
{
    protected $scheduleController;

    public function __construct(ScheduleController $scheduleController)
    {
        $this->scheduleController = $scheduleController;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $subjects = Subject::all();

            return DataTables::of($subjects)
                ->make();
        }
        return view('dashboard.subject.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $grades = Grade::where('status', 1)->pluck('name', 'id');
        $groups = Group::where('status', 1)->pluck('number', 'id');

        $rombels = [];
        foreach ($grades as $gradeId => $gradeName) {
            foreach ($groups as $groupId => $groupName) {
                $rombels[] = [
                    'id' => "{$gradeId} {$groupId}",
                    'name' => "{$gradeName} {$groupName}"
                ];
            }
        }
        return view('dashboard.subject.create', [
            'teachers' => Teacher::get(),
            'rombels' => $rombels
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|string',
            'description' => 'nullable',
        ]);

        Subject::create([
            'name' => $validatedData['name'],
            'description' => $validatedData['description'],
        ]);

        return redirect('/dashboard/subject')->with('success', 'Mapel Berhasil Ditambahkan!');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Subject $subject)
    {
        $grades = Grade::where('status', 1)->pluck('name', 'id');
        $groups = Group::where('status', 1)->pluck('number', 'id');

        $rombels = [];
        foreach ($grades as $gradeId => $gradeName) {
            foreach ($groups as $groupId => $groupName) {
                $rombels[] = [
                    'id' => "{$gradeId} {$groupId}",
                    'name' => "{$gradeName} {$groupName}"
                ];
            }
        }
        $teachers = Teacher::get();
        return view('dashboard.subject.edit', compact('subject', 'rombels', 'teachers'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Subject $subject)
    {
        $rules = [
            'name' => 'required|string',
            'description' => 'nullable',
        ];

        $validatedData = $request->validate($rules);
        Subject::findOrFail($subject->id)->update([
            'name' => $validatedData['name'],
            'description' => $validatedData['description'],
        ]);

        return redirect('/dashboard/subject')->with('success', 'Mapel Berhasil Diupdate');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Subject $subject)
    {
        Subject::destroy($subject->id);
        return redirect('/dashboard/subject')->with('success', 'Mapel Berhasil Dihapus');
    }

    public function exportExcel()
    {
        return Excel::download(new SubjectExport, 'Data Mapel.xlsx');
    }

    public function exportPdf(Request $request)
    {
        $query = Subject::query();


        $query->orderBy('code');

        $subjects = $query->get();

        $title = 'Data Mapel.pdf';
        $data = Pdf::loadview('dashboard.subject.pdf', [
            'subjects' => $subjects,
            'title' => $title
        ]);
        return $data->stream($title);
    }

    // public function generateSchedule()
    // {
    //     $schedule = $this->scheduleController->generateSchedule();

    //     $decode_schedule = $this->scheduleController->decodeSchedule($schedule);
    //     // dd($decode_schedule);

    //     return view('dashboard.schedule.result', compact('decode_schedule'));
    //     // if (is_array($schedule)) {
    //     //
    //     // } else {
    //     //     return back()->with('error', $schedule);
    //     // }
    // }
}
