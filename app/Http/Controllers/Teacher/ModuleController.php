<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Module;
use Illuminate\Http\Request;

class ModuleController extends Controller
{
    public function index(Course $course)
    {
        return \Inertia\Inertia::render('Teacher/Courses/Modules/Index', [
            'course' => $course->load(['modules.lessons' => fn($q) => $q->orderBy('order')])
        ]);
    }

    public function store(Request $request, Course $course)
    {
        $data = $request->validate(['title' => 'required|string|max:255']);
        $maxOrder = $course->modules()->max('order') ?? 0;
        
        $module = $course->modules()->create([
            'title' => $data['title'],
            'order' => $maxOrder + 1,
        ]);
        
        return back()->with('success', 'Module created successfully');
    }

    public function update(Request $request, Module $module)
    {
        $data = $request->validate(['title' => 'required|string|max:255']);
        $module->update($data);
        return back()->with('success', 'Module updated successfully');
    }

    public function destroy(Module $module)
    {
        $module->delete();
        return back()->with('success', 'Module deleted successfully');
    }

    // Drag-drop reorder
    public function reorder(Request $request, Course $course)
    {
        $request->validate(['modules' => 'required|array']);
        
        foreach ($request->modules as $index => $id) {
            Module::where('id', $id)->where('course_id', $course->id)
                ->update(['order' => $index]);
        }
        
        return response()->json(['success' => true]);
    }
}
