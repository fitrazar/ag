<?php

namespace App\Http\Controllers;

use App\Models\Major;
use App\Models\Student;
use App\Models\Subject;
use App\Models\Teacher;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        $totalStudent = Student::count();
        $totalTeacher = Teacher::count();
        $totalSubject = Subject::count();
        $totalMajor = Major::count();

        if ($request->ajax()) {
            $subjects = Subject::all();

            return DataTables::of($subjects)
                ->make();
        }
        return view('dashboard', compact('totalStudent', 'totalTeacher', 'totalSubject', 'totalMajor'));
    }
}
