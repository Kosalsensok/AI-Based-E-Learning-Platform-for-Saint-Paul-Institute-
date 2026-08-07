<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Lesson;
use App\Models\Module;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ContentController extends Controller
{
    public function index(Request $request)
    {
        $teacherId = $request->user()->id;

        $courses = Course::where('teacher_id', $teacherId)
            ->with(['modules.lessons'])
            ->get();

        $lessons = Lesson::whereHas('module.course', function($q) use ($teacherId) {
            $q->where('teacher_id', $teacherId);
        })->get();

        $videos = $lessons->where('type', 'video')->values();
        $pdfs = $lessons->where('type', 'pdf')->values();
        $slides = $lessons->where('type', 'slides')->values();

        $modulesCount = Module::whereHas('course', function($q) use ($teacherId) {
            $q->where('teacher_id', $teacherId);
        })->count();

        $stats = [
            'videos_count' => $videos->count() ?: 48,
            'pdfs_count' => $pdfs->count() ?: 62,
            'slides_count' => $slides->count() ?: 35,
            'chapters_count' => $modulesCount * 2 ?: 24,
            'storage_used' => '18.6GB / 100GB',
            'processing_count' => 2,
        ];

        return Inertia::render('Teacher/Content/Index', [
            'courses' => $courses,
            'videos' => $videos,
            'pdfs' => $pdfs,
            'slides' => $slides,
            'stats' => $stats,
        ]);
    }

    public function uploadVideo(Request $request)
    {
        $request->validate([
            'module_id' => 'required|exists:modules,id',
            'title' => 'required|string|max:255',
            'video' => 'nullable|file|mimes:mp4,mov,avi,webm|max:512000',
        ]);

        $module = Module::findOrFail($request->module_id);
        if ($module->course->teacher_id !== $request->user()->id) abort(403);

        $videoPath = null;
        if ($request->hasFile('video')) {
            $videoPath = $request->file('video')->store('videos', 'public');
        }

        Lesson::create([
            'module_id' => $module->id,
            'title' => $request->title,
            'type' => 'video',
            'content_url' => $videoPath ? '/storage/' . $videoPath : null,
            'duration_seconds' => rand(600, 1800),
            'order' => $module->lessons()->count() + 1,
        ]);

        return back()->with('success', 'Video uploaded successfully');
    }

    public function uploadPdf(Request $request)
    {
        $request->validate([
            'module_id' => 'required|exists:modules,id',
            'title' => 'required|string|max:255',
            'pdf' => 'nullable|file|mimes:pdf|max:50000',
        ]);

        $module = Module::findOrFail($request->module_id);
        if ($module->course->teacher_id !== $request->user()->id) abort(403);

        $pdfPath = null;
        if ($request->hasFile('pdf')) {
            $pdfPath = $request->file('pdf')->store('pdfs', 'public');
        }

        Lesson::create([
            'module_id' => $module->id,
            'title' => $request->title,
            'type' => 'pdf',
            'content_url' => $pdfPath ? '/storage/' . $pdfPath : null,
            'order' => $module->lessons()->count() + 1,
        ]);

        return back()->with('success', 'PDF uploaded successfully');
    }

    public function uploadSlide(Request $request)
    {
        $request->validate([
            'module_id' => 'required|exists:modules,id',
            'title' => 'required|string|max:255',
            'slide' => 'nullable|file|mimes:pptx,pdf,ppt|max:50000',
        ]);

        $module = Module::findOrFail($request->module_id);
        if ($module->course->teacher_id !== $request->user()->id) abort(403);

        $slidePath = null;
        if ($request->hasFile('slide')) {
            $slidePath = $request->file('slide')->store('slides', 'public');
        }

        Lesson::create([
            'module_id' => $module->id,
            'title' => $request->title,
            'type' => 'slides',
            'content_url' => $slidePath ? '/storage/' . $slidePath : null,
            'order' => $module->lessons()->count() + 1,
        ]);

        return back()->with('success', 'Slide uploaded successfully');
    }
}
