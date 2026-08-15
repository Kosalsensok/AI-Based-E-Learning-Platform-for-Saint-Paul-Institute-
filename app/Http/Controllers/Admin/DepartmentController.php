<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Department;
use App\Models\Faculty;
use App\Http\Requests\StoreDepartmentRequest;
use App\Http\Requests\UpdateDepartmentRequest;
use Illuminate\Support\Facades\Cache;
use Inertia\Inertia;

class DepartmentController extends Controller
{
    private function clearCache(): void
    {
        Cache::forget('academic_structure');
        Cache::forget('academic_structure.departments');
        Cache::forget('academic.summary_stats');
    }

    public function index()
    {
        return Inertia::render('Admin/Academic/DepartmentIndex', [
            'departments' => Department::with(['faculty', 'majors'])->latest()->get(),
            'faculties' => Faculty::latest()->get(),
        ]);
    }

    public function store(StoreDepartmentRequest $request)
    {
        Department::create($request->validated() + ['is_active' => $request->boolean('is_active')]);
        $this->clearCache();
        return back()->with('success', 'Department created successfully');
    }

    public function update(UpdateDepartmentRequest $request, Department $department)
    {
        $department->update($request->validated() + ['is_active' => $request->boolean('is_active')]);
        $this->clearCache();
        return back()->with('success', 'Department updated successfully');
    }

    public function destroy(Department $department)
    {
        $department->delete();
        $this->clearCache();
        return back()->with('success', 'Department deleted successfully');
    }
}
