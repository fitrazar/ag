<?php

namespace App\Http\Controllers;

use App\Models\Grade;
use App\Models\Group;
use App\Models\Subject;
use App\Models\Teacher;
use App\Models\Working;
use Illuminate\Http\Request;
use App\Exports\SubjectExport;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;
use Yajra\DataTables\Facades\DataTables;

class WorkingController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $workings = Working::all();

            return DataTables::of($workings)
                ->make();
        }
        return view('dashboard.working.index');
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
        return view('dashboard.working.create', [
            'teachers' => Teacher::all(),
            'subjects' => Subject::all(),
            'rombels' => $rombels
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'subject_id' => 'required',
            'teacher_id' => 'required',
            'rombel' => 'required',
            'code' => 'required',
            'hours' => 'required',
        ]);

        list($gradeId, $groupId) = explode(' ', $validatedData['rombel']);

        Working::create([
            'subject_id' => $validatedData['subject_id'],
            'code' => $validatedData['code'],
            'teacher_id' => $validatedData['teacher_id'],
            'hours' => $validatedData['hours'],
            'grade_id' => $gradeId,
            'group_id' => $groupId,
        ]);

        return redirect('/dashboard/working')->with('success', 'Penugasan Berhasil Ditambahkan!');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Working $working)
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
        $subjects = Subject::get();
        return view('dashboard.working.edit', compact('working', 'rombels', 'teachers', 'subjects'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Working $working)
    {
        $rules = [
            'subject_id' => 'required',
            'hours' => 'required',
            'code' => 'required',
            'teacher_id' => 'required',
            'rombel' => 'required',
            'description' => 'nullable',
        ];

        $validatedData = $request->validate($rules);
        list($gradeId, $groupId) = explode(' ', $validatedData['rombel']);
        Working::findOrFail($working->id)->update([
            'subject_id' => $validatedData['subject_id'],
            'code' => $validatedData['code'],
            'teacher_id' => $validatedData['teacher_id'],
            'hours' => $validatedData['hours'],
            'grade_id' => $gradeId,
            'group_id' => $groupId,
        ]);

        return redirect('/dashboard/working')->with('success', 'Penugasan Berhasil Diupdate');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Working $working)
    {
        Working::destroy($working->id);
        return redirect('/dashboard/working')->with('success', 'Penugasan Berhasil Dihapus');
    }

    public function exportExcel()
    {
        return Excel::download(new SubjectExport, 'Data Penugasan.xlsx');
    }

    public function exportPdf(Request $request)
    {
        $query = Working::query();


        $query->orderBy('code');

        $workings = $query->get();

        $title = 'Data Penugasan.pdf';
        $data = Pdf::loadview('dashboard.working.pdf', [
            'workings' => $workings,
            'title' => $title
        ]);
        return $data->stream($title);
    }
}
