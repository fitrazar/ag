<?php

namespace App\Http\Controllers;

use App\Models\Group;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class GroupController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $group = Group::all();

            return DataTables::of($group)
                ->make();
        }
        return view('dashboard.group.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('dashboard.group.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'number' => 'required|numeric',
        ]);

        Group::create([
            'number' => $validatedData['number'],
            'status' => $request->status == true ? 0 : 1,
        ]);

        return redirect('/dashboard/group')->with('success', 'Kelas Berhasil Ditambahkan!');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Group $group)
    {
        return view('dashboard.group.edit', compact('group'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Group $group)
    {
        $rules = [
            'number' => 'required|numeric',
        ];

        $validatedData = $request->validate($rules);

        Group::findOrFail($group->id)->update([
            'number' => $validatedData['number'],
            'status' => $request->status == true ? 0 : 1,
        ]);

        return redirect('/dashboard/group')->with('success', 'Kelas Berhasil Diupdate');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Group $group)
    {
        Group::destroy($group->id);
        return redirect('/dashboard/group')->with('success', 'Kelas Berhasil Dihapus');
    }
}
