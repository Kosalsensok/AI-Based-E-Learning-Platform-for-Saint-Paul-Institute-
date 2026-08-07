<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\Lesson;
use App\Models\Module;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class LessonController extends Controller
{
    public function index(Module $module)
    {
        return response()->json($module->lessons);
    }

    public function store(Request $request, Module $module)
    {
        $data = $request->validate([
            'title'    => 'required|string|max:255',
            'type'     => 'required|in:video,pdf,slides,notes',
            'video_url'=> 'nullable|string',
            'content'  => 'nullable|string',
            'is_free'  => 'boolean'
        ]);

        $lessonData = [
            'title'     => $data['title'],
            'type'      => $data['type'],
            'video_url' => $data['video_url'] ?? null,
            'content'   => $data['content'] ?? null,
            'is_free_preview' => $request->boolean('is_free'),
            'order'     => $module->lessons()->max('order') + 1,
            'course_id' => $module->course_id,
            'downloadable' => false,
        ];

        $module->lessons()->create($lessonData);
        return back()->with('success', 'Lesson created successfully');
    }

    public function update(Request $request, Lesson $lesson)
    {
        $data = $request->validate([
            'title'    => 'required|string|max:255',
            'type'     => 'required|in:video,pdf,slides,notes',
            'video_url'=> 'nullable|string',
            'content'  => 'nullable|string',
            'is_free'  => 'boolean'
        ]);

        $lesson->update([
            'title' => $data['title'],
            'type' => $data['type'],
            'video_url' => $data['video_url'] ?? null,
            'content' => $data['content'] ?? null,
            'is_free_preview' => $request->boolean('is_free'),
        ]);

        return back()->with('success', 'Lesson updated successfully');
    }

    public function destroy(Lesson $lesson)
    {
        $lesson->delete();
        return back()->with('success', 'Lesson deleted successfully');
    }

    public function reorder(Request $request, Module $module)
    {
        $request->validate(['lessons' => 'required|array']);
        
        foreach ($request->lessons as $index => $id) {
            Lesson::where('id', $id)->where('module_id', $module->id)
                ->update(['order' => $index]);
        }
        
        return response()->json(['success' => true]);
    }
}
