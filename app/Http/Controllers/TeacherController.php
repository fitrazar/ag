<?php

namespace App\Http\Controllers;

use App\Models\Teacher;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Yajra\DataTables\Facades\DataTables;

class TeacherController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $teachers = Teacher::all();

            return DataTables::of($teachers)
                ->make();
        }
        return view('dashboard.teacher.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('dashboard.teacher.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'number' => 'required|numeric|unique:teachers,number',
            'name' => 'required|string',
            'gender' => 'required|in:Laki - Laki,Perempuan',
            'phone' => 'nullable|numeric',
            'address' => 'nullable',
            'photo' => 'nullable|image|max:4098',
        ]);

        $fileFilename = NULL;
        if ($request->hasFile('photo')) {
            $fileFilename = time() . '.' . $request->file('photo')->getClientOriginalExtension();
            $request->file('photo')->storeAs('teacher/photo', $fileFilename);
        }

        Teacher::create([
            'number' => $validatedData['number'],
            'name' => $validatedData['name'],
            'gender' => $validatedData['gender'],
            'phone' => $validatedData['phone'] ?? NULL,
            'address' => $validatedData['address'] ?? NULL,
            'photo' => $fileFilename ?? NULL,
        ]);

        return redirect('/dashboard/teacher')->with('success', 'Guru Berhasil Ditambahkan!');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Teacher $teacher)
    {
        return view('dashboard.teacher.edit', compact('teacher'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Teacher $teacher)
    {
        $rules = [
            'name' => 'required|string',
            'gender' => 'required|in:Laki - Laki,Perempuan',
            'phone' => 'nullable|numeric',
            'address' => 'nullable',
            'photo' => 'nullable|image|max:4098',
        ];

        if ($request->number != $teacher->number) {
            $rules['number'] = 'required|unique:teachers';
        }

        $validatedData = $request->validate($rules);
        $validatedData['photo'] = $request->oldImage;
        if ($request->file('photo')) {
            $path = 'teacher/photo';
            if ($request->oldImage) {
                Storage::delete($path . '/' . $request->oldImage);
            }
            $validatedData['photo'] = time() . '.' . $request->file('photo')->getClientOriginalExtension();
            $photoPath = $request->file('photo')->storeAs('teacher/photo', $validatedData['photo']);
        }


        Teacher::findOrFail($teacher->id)->update($validatedData);

        return redirect('/dashboard/teacher')->with('success', 'Guru Berhasil Diupdate');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Teacher $teacher)
    {
        if ($teacher->photo) {
            Storage::delete('teacher/photo/' . $teacher->photo);
        }
        Teacher::destroy($teacher->id);

        return redirect('/dashboard/teacher')->with('success', 'Guru Berhasil Dihapus!');
    }
}
