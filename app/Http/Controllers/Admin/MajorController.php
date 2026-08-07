<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\{Major, Department, Faculty};
use App\Http\Requests\StoreMajorRequest;
use App\Http\Requests\UpdateMajorRequest;
use Inertia\Inertia;

class MajorController extends Controller
{
    public function index()
    {
        return Inertia::render('Admin/Academic/MajorIndex', [
            'majors' => Major::with('department.faculty')->latest()->get(),
            'departments' => Department::with('faculty')->get(),
            'faculties' => Faculty::latest()->get(),
        ]);
    }

    public function store(StoreMajorRequest $request)
    {
        Major::create($request->validated() + ['is_active' => $request->boolean('is_active')]);
        return back()->with('success', 'Major created successfully');
    }

    public function update(UpdateMajorRequest $request, Major $major)
    {
        $major->update($request->validated() + ['is_active' => $request->boolean('is_active')]);
        return back()->with('success', 'Major updated successfully');
    }

    public function destroy(Major $major)
    {
        $major->delete();
        return back()->with('success', 'Major deleted successfully');
    }

    public function departmentsByFaculty(Faculty $faculty)
    {
        return response()->json(
            $faculty->departments()->with('majors')->get(['id','name'])
        );
    }
}
