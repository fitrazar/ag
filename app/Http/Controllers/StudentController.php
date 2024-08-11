<?php

namespace App\Http\Controllers;

use App\Models\Grade;
use App\Models\Group;
use App\Models\Major;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Yajra\DataTables\Facades\DataTables;

class StudentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $students = Student::all();

            return DataTables::of($students)
                ->make();
        }
        return view('dashboard.student.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $grades = Grade::where('status', 1)->pluck('name', 'id');
        $majors = Major::where('status', 1)->pluck('acronym', 'id');
        $groups = Group::where('status', 1)->pluck('number', 'id');

        $rombels = [];
        foreach ($grades as $gradeId => $gradeName) {
            foreach ($majors as $majorId => $majorName) {
                foreach ($groups as $groupId => $groupName) {
                    $rombels[] = [
                        'id' => "{$gradeId} {$majorId} {$groupId}",
                        'name' => "{$gradeName} {$majorName} {$groupName}"
                    ];
                }
            }
        }
        return view('dashboard.student.create', compact('rombels'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'nisn' => 'required|numeric|unique:students,nisn',
            'name' => 'required|string',
            'gender' => 'required|in:Laki - Laki,Perempuan',
            'phone' => 'nullable|numeric',
            'address' => 'nullable',
            'rombel' => 'required',
            'photo' => 'nullable|image|max:4098',
        ]);

        list($gradeId, $majorId, $groupId) = explode(' ', $validatedData['rombel']);
        $fileFilename = NULL;
        if ($request->hasFile('photo')) {
            $fileFilename = time() . '.' . $request->file('photo')->getClientOriginalExtension();
            $request->file('photo')->storeAs('student/photo', $fileFilename);
        }

        Student::create([
            'grade_id' => $gradeId,
            'major_id' => $majorId,
            'group_id' => $groupId,
            'nisn' => $validatedData['nisn'],
            'name' => $validatedData['name'],
            'gender' => $validatedData['gender'],
            'phone' => $validatedData['phone'] ?? NULL,
            'address' => $validatedData['address'] ?? NULL,
            'photo' => $fileFilename ?? NULL,
        ]);

        return redirect('/dashboard/student')->with('success', 'Siswa Berhasil Ditambahkan!');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Student $student)
    {
        $grades = Grade::pluck('name', 'id');
        $majors = Major::pluck('acronym', 'id');
        $groups = Group::pluck('number', 'id');

        $rombels = [];
        foreach ($grades as $gradeId => $gradeName) {
            foreach ($majors as $majorId => $majorName) {
                foreach ($groups as $groupId => $groupName) {
                    $rombels[] = [
                        'id' => "{$gradeId} {$majorId} {$groupId}",
                        'name' => "{$gradeName} {$majorName} {$groupName}"
                    ];
                }
            }
        }
        return view('dashboard.student.edit', compact('student', 'rombels'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Student $student)
    {
        $rules = [
            'name' => 'required|string',
            'gender' => 'required|in:Laki - Laki,Perempuan',
            'phone' => 'nullable|numeric',
            'address' => 'nullable',
            'rombel' => 'required',
            'photo' => 'nullable|image|max:4098',
        ];

        if ($request->nisn != $student->nisn) {
            $rules['nisn'] = 'required|unique:students';
        }

        $validatedData = $request->validate($rules);
        $validatedData['photo'] = $request->oldImage;
        if ($request->file('photo')) {
            $path = 'student/photo';
            if ($request->oldImage) {
                Storage::delete($path . '/' . $request->oldImage);
            }
            $validatedData['photo'] = time() . '.' . $request->file('photo')->getClientOriginalExtension();
            $photoPath = $request->file('photo')->storeAs('student/photo', $validatedData['photo']);
        }

        list($gradeId, $majorId, $groupId) = explode(' ', $validatedData['rombel']);

        Student::findOrFail($student->id)->update([
            'grade_id' => $gradeId,
            'major_id' => $majorId,
            'group_id' => $groupId,
            'nisn' => $validatedData['nisn'],
            'name' => $validatedData['name'],
            'gender' => $validatedData['gender'],
            'phone' => $validatedData['phone'] ?? NULL,
            'address' => $validatedData['address'] ?? NULL,
            'photo' => $validatedData['photo']
        ]);

        return redirect('/dashboard/student')->with('success', 'Siswa Berhasil Diupdate');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Student $student)
    {
        if ($student->photo) {
            Storage::delete('student/photo/' . $student->photo);
        }
        Student::destroy($student->id);

        return redirect('/dashboard/student')->with('success', 'Siswa Berhasil Dihapus!');
    }
}
