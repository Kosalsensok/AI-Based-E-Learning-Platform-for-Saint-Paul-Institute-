<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Department;
use App\Models\Faculty;
use App\Http\Requests\StoreDepartmentRequest;
use App\Http\Requests\UpdateDepartmentRequest;
use Inertia\Inertia;

class DepartmentController extends Controller
{
    public function index()
    {
        return Inertia::render('Admin/Academic/DepartmentIndex', [
            'departments' => Department::with('faculty')->latest()->get(),
            'faculties' => Faculty::latest()->get(),
        ]);
    }

    public function store(StoreDepartmentRequest $request)
    {
        Department::create($request->validated() + ['is_active' => $request->boolean('is_active')]);
        return back()->with('success', 'Department created successfully');
    }

    public function update(UpdateDepartmentRequest $request, Department $department)
    {
        $department->update($request->validated() + ['is_active' => $request->boolean('is_active')]);
        return back()->with('success', 'Department updated successfully');
    }

    public function destroy(Department $department)
    {
        $department->delete();
        return back()->with('success', 'Department deleted successfully');
    }
}
