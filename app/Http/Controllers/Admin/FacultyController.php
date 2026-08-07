<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Faculty;
use App\Http\Requests\StoreFacultyRequest;
use App\Http\Requests\UpdateFacultyRequest;
use Inertia\Inertia;

class FacultyController extends Controller
{
    public function index()
    {
        return Inertia::render('Admin/Academic/FacultyIndex', [
            'faculties' => Faculty::query()->latest()->get(),
        ]);
    }

    public function store(StoreFacultyRequest $request)
    {
        Faculty::create($request->validated() + ['is_active' => $request->boolean('is_active')]);
        return back()->with('success', 'Faculty created successfully');
    }

    public function update(UpdateFacultyRequest $request, Faculty $faculty)
    {
        $faculty->update($request->validated() + ['is_active' => $request->boolean('is_active')]);
        return back()->with('success', 'Faculty updated successfully');
    }

    public function destroy(Faculty $faculty)
    {
        $faculty->delete();
        return back()->with('success', 'Faculty deleted successfully');
    }
}
