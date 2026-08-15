<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Faculty;
use App\Http\Requests\StoreFacultyRequest;
use App\Http\Requests\UpdateFacultyRequest;
use Illuminate\Support\Facades\Cache;
use Inertia\Inertia;

class FacultyController extends Controller
{
    private function clearCache(): void
    {
        Cache::forget('academic_structure');
        Cache::forget('academic_structure.faculties');
        Cache::forget('academic.summary_stats');
    }

    public function index()
    {
        return Inertia::render('Admin/Academic/FacultyIndex', [
            'faculties' => Faculty::with(['departments.majors'])->latest()->get(),
        ]);
    }

    public function store(StoreFacultyRequest $request)
    {
        Faculty::create($request->validated() + ['is_active' => $request->boolean('is_active')]);
        $this->clearCache();
        return back()->with('success', 'Faculty created successfully');
    }

    public function update(UpdateFacultyRequest $request, Faculty $faculty)
    {
        $faculty->update($request->validated() + ['is_active' => $request->boolean('is_active')]);
        $this->clearCache();
        return back()->with('success', 'Faculty updated successfully');
    }

    public function destroy(Faculty $faculty)
    {
        $faculty->delete();
        $this->clearCache();
        return back()->with('success', 'Faculty deleted successfully');
    }
}
