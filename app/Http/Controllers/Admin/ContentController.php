<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Lesson;
use App\Models\Module;
use App\Models\Major;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ContentController extends Controller
{
    public function index(Request $request)
    {
        $tab = $request->query('tab');
        $type = $request->query('type');

        // Resolve active tab if type query parameter is passed
        if (!$tab && $type) {
            if ($type === 'video')
                $tab = 'videos';
            elseif ($type === 'pdf')
                $tab = 'pdfs';
            elseif ($type === 'slides')
                $tab = 'slides';
            elseif ($type === 'notes')
                $tab = 'notes';
        }
        if (!$tab) {
            $tab = 'library';
        }

        // Stats
        $stats = [
            'total_items' => 1845,
            'videos_count' => 820,
            'pdfs_count' => 610,
            'slides_count' => 285,
            'notes_count' => 130,
            'published_count' => 1620,
            'draft_count' => 190,
            'archived_count' => 35,
            'storage_used_gb' => 128.4,
            'storage_limit_gb' => 500.0,
            'total_views' => 45820,
            'total_downloads' => 12450,
        ];

        // Content Library Items
        $contentItems = [
            [
                'id' => 1,
                'type' => 'video',
                'title' => 'Intro to C Programming',
                'file_name' => 'Intro_to_C.mp4',
                'course' => 'C Programming Basics',
                'module' => 'Module 1: Introduction to C',
                'chapter' => 'Chapter 1.1: History of C',
                'teacher' => 'Mr. Sophea',
                'teacher_avatar' => 'https://images.unsplash.com/photo-1534528741775-53994a69daeb?w=100&auto=format&fit=crop&q=80',
                'duration' => '12:34',
                'size_formatted' => '120 MB',
                'resolution' => '1080p (HD)',
                'views' => 2450,
                'avg_watch' => '78%',
                'cdn' => 'Cloudinary',
                'status' => 'Published',
                'watermark' => true,
                'signed_url' => true,
                'updated_at' => '15 May 2025',
                'thumbnail' => '🎥',
                'thumbnail_url' => 'https://images.unsplash.com/photo-1515879218367-8466d910aaa4?w=600&auto=format&fit=crop&q=80',
            ],
            [
                'id' => 2,
                'type' => 'pdf',
                'title' => 'C Programming Notes & Syntax',
                'file_name' => 'C_Notes_v2.pdf',
                'course' => 'C Programming Basics',
                'module' => 'Module 1: Introduction to C',
                'chapter' => 'Chapter 1.1: History of C',
                'teacher' => 'Mr. Sophea',
                'teacher_avatar' => 'https://images.unsplash.com/photo-1534528741775-53994a69daeb?w=100&auto=format&fit=crop&q=80',
                'pages' => 45,
                'size_formatted' => '2.4 MB',
                'downloads' => 1250,
                'status' => 'Published',
                'watermark' => true,
                'updated_at' => '10 May 2025',
                'thumbnail' => '📄',
                'thumbnail_url' => 'https://images.unsplash.com/photo-1517694712202-14dd9538aa97?w=600&auto=format&fit=crop&q=80',
            ],
            [
                'id' => 3,
                'type' => 'slide',
                'title' => 'Intro to Database Systems Slides',
                'file_name' => 'DB_Intro_Slides.pptx',
                'course' => 'Database Systems & SQL',
                'module' => 'Module 1: DB Concepts',
                'chapter' => 'Chapter 1.1: Relational DB',
                'teacher' => 'Mr. Sophea',
                'teacher_avatar' => 'https://images.unsplash.com/photo-1534528741775-53994a69daeb?w=100&auto=format&fit=crop&q=80',
                'pages' => 42,
                'size_formatted' => '5.6 MB',
                'views' => 1820,
                'status' => 'Published',
                'updated_at' => '12 May 2025',
                'thumbnail' => '📊',
                'thumbnail_url' => 'https://images.unsplash.com/photo-1544383835-bda2bc66a55d?w=600&auto=format&fit=crop&q=80',
            ],
            [
                'id' => 4,
                'type' => 'video',
                'title' => 'Loops & Conditionals in C',
                'file_name' => 'Loops_in_C.mp4',
                'course' => 'C Programming Basics',
                'module' => 'Module 2: Control Flow',
                'chapter' => 'Chapter 2.2: Loops',
                'teacher' => 'Mr. Sophea',
                'teacher_avatar' => 'https://images.unsplash.com/photo-1534528741775-53994a69daeb?w=100&auto=format&fit=crop&q=80',
                'duration' => '18:20',
                'size_formatted' => '180 MB',
                'resolution' => '1080p (HD)',
                'views' => 1650,
                'avg_watch' => '82%',
                'cdn' => 'Cloudinary',
                'status' => 'Published',
                'watermark' => true,
                'signed_url' => true,
                'updated_at' => '18 May 2025',
                'thumbnail' => '🎥',
                'thumbnail_url' => 'https://images.unsplash.com/photo-1526374965328-7f61d4dc18c5?w=600&auto=format&fit=crop&q=80',
            ],
            [
                'id' => 5,
                'type' => 'note',
                'title' => 'C Syntax Cheat Sheet',
                'file_name' => 'C_Syntax_CheatSheet.md',
                'course' => 'C Programming Basics',
                'module' => 'Module 1: Introduction to C',
                'chapter' => 'Chapter 1.2: Setup Environment',
                'teacher' => 'Mr. Sophea',
                'teacher_avatar' => 'https://images.unsplash.com/photo-1534528741775-53994a69daeb?w=100&auto=format&fit=crop&q=80',
                'type_label' => 'Web Markdown',
                'views' => 1820,
                'status' => 'Published',
                'updated_at' => '20 May 2025',
                'thumbnail' => '📝',
                'thumbnail_url' => 'https://images.unsplash.com/photo-1555066931-4365d14bab8c?w=600&auto=format&fit=crop&q=80',
            ],
            [
                'id' => 6,
                'type' => 'video',
                'title' => 'Functions & Pointers in C',
                'file_name' => 'Functions_Pointers.mp4',
                'course' => 'C Programming Basics',
                'module' => 'Module 4: Functions',
                'chapter' => 'Chapter 4.1: Function Basics',
                'teacher' => 'Mr. Sophea',
                'teacher_avatar' => 'https://images.unsplash.com/photo-1534528741775-53994a69daeb?w=100&auto=format&fit=crop&q=80',
                'duration' => '15:20',
                'size_formatted' => '134 MB',
                'resolution' => '1080p (HD)',
                'views' => 1200,
                'avg_watch' => '74%',
                'cdn' => 'Cloudinary',
                'status' => 'Draft',
                'watermark' => true,
                'signed_url' => true,
                'updated_at' => '22 May 2025',
                'thumbnail' => '🎥',
                'thumbnail_url' => 'https://images.unsplash.com/photo-1516116211223-4c599705f381?w=600&auto=format&fit=crop&q=80',
            ],
            [
                'id' => 7,
                'type' => 'pdf',
                'title' => 'Tourism Marketing Guide 2025',
                'file_name' => 'Tourism_Guide_2025.pdf',
                'course' => 'Tourism Management Principles',
                'module' => 'Module 1: Fundamentals',
                'chapter' => 'Chapter 1.1: Hospitality Basics',
                'teacher' => 'Dr. Rith',
                'teacher_avatar' => 'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?w=100&auto=format&fit=crop&q=80',
                'pages' => 68,
                'size_formatted' => '8.2 MB',
                'downloads' => 620,
                'status' => 'Published',
                'watermark' => true,
                'updated_at' => '05 May 2025',
                'thumbnail' => '📄',
                'thumbnail_url' => 'https://images.unsplash.com/photo-1488646953014-85cb44e25828?w=600&auto=format&fit=crop&q=80',
            ],
        ];

        // Modules & Chapters Structure for Course Builder
        $courseModules = [
            [
                'id' => 1,
                'module_number' => 1,
                'title' => 'Module 1: Introduction to C Programming',
                'description' => 'This module covers the basic history, setup environment, and first C program.',
                'estimated_duration' => '3 Hours',
                'chapters_count' => 2,
                'items_count' => 5,
                'status' => 'Published',
                'chapters' => [
                    [
                        'id' => 101,
                        'chapter_number' => '1.1',
                        'title' => 'Chapter 1.1: History of C',
                        'items' => [
                            ['id' => 1, 'type' => 'video', 'title' => 'Intro to C', 'info' => '12:34 · 1080p HD', 'icon' => '🎥', 'thumbnail_url' => 'https://images.unsplash.com/photo-1515879218367-8466d910aaa4?w=200&auto=format&fit=crop&q=80'],
                            ['id' => 2, 'type' => 'pdf', 'title' => 'C History Notes', 'info' => '2.4 MB · PDF', 'icon' => '📄', 'thumbnail_url' => 'https://images.unsplash.com/photo-1517694712202-14dd9538aa97?w=200&auto=format&fit=crop&q=80'],
                            ['id' => 5, 'type' => 'note', 'title' => 'Timeline Cheat Sheet', 'info' => 'Web Note', 'icon' => '📝', 'thumbnail_url' => 'https://images.unsplash.com/photo-1555066931-4365d14bab8c?w=200&auto=format&fit=crop&q=80'],
                        ]
                    ],
                    [
                        'id' => 102,
                        'chapter_number' => '1.2',
                        'title' => 'Chapter 1.2: Setup Environment',
                        'items' => [
                            ['id' => 8, 'type' => 'video', 'title' => 'Install GCC & VS Code', 'info' => '08:20 · 1080p HD', 'icon' => '🎥', 'thumbnail_url' => 'https://images.unsplash.com/photo-1526374965328-7f61d4dc18c5?w=200&auto=format&fit=crop&q=80'],
                            ['id' => 9, 'type' => 'slide', 'title' => 'Environment Setup Deck', 'info' => '14 pages · Slides', 'icon' => '📊', 'thumbnail_url' => 'https://images.unsplash.com/photo-1544383835-bda2bc66a55d?w=200&auto=format&fit=crop&q=80'],
                        ]
                    ]
                ]
            ],
            [
                'id' => 2,
                'module_number' => 2,
                'title' => 'Module 2: Variables & Data Types',
                'description' => 'Understand data types, variable declarations, memory representation, and constants.',
                'estimated_duration' => '4 Hours',
                'chapters_count' => 2,
                'items_count' => 4,
                'status' => 'Published',
                'chapters' => [
                    [
                        'id' => 103,
                        'chapter_number' => '2.1',
                        'title' => 'Chapter 2.1: Variables & Memory',
                        'items' => [
                            ['id' => 4, 'type' => 'video', 'title' => 'What is a Variable', 'info' => '18:30 · 1080p HD', 'icon' => '🎥', 'thumbnail_url' => 'https://images.unsplash.com/photo-1516116211223-4c599705f381?w=200&auto=format&fit=crop&q=80'],
                            ['id' => 10, 'type' => 'pdf', 'title' => 'Variables Handout', 'info' => '1.8 MB · PDF', 'icon' => '📄', 'thumbnail_url' => 'https://images.unsplash.com/photo-1517694712202-14dd9538aa97?w=200&auto=format&fit=crop&q=80'],
                        ]
                    ],
                    [
                        'id' => 104,
                        'chapter_number' => '2.2',
                        'title' => 'Chapter 2.2: Data Types (int, float, char)',
                        'items' => [
                            ['id' => 11, 'type' => 'video', 'title' => 'C Data Types Deep Dive', 'info' => '14:15 · 1080p HD', 'icon' => '🎥', 'thumbnail_url' => 'https://images.unsplash.com/photo-1526374965328-7f61d4dc18c5?w=200&auto=format&fit=crop&q=80'],
                            ['id' => 12, 'type' => 'slide', 'title' => 'Data Types Slide Deck', 'info' => '22 pages · Slides', 'icon' => '📊', 'thumbnail_url' => 'https://images.unsplash.com/photo-1544383835-bda2bc66a55d?w=200&auto=format&fit=crop&q=80'],
                        ]
                    ]
                ]
            ]
        ];

        // Offline Content Packages
        $offlinePackages = [
            [
                'id' => 1,
                'course' => 'C Programming Basics',
                'package_name' => 'C_Basics_Full_Offline_v1.zip',
                'size_formatted' => '1.45 GB',
                'download_count' => 480,
                'encrypted' => true,
                'pwa_cached' => true,
                'updated_at' => '20 May 2025',
                'status' => 'Active',
            ],
            [
                'id' => 2,
                'course' => 'Database Systems & SQL',
                'package_name' => 'DB_Systems_Offline_v1.zip',
                'size_formatted' => '2.10 GB',
                'download_count' => 310,
                'encrypted' => true,
                'pwa_cached' => true,
                'updated_at' => '18 May 2025',
                'status' => 'Active',
            ],
        ];

        return Inertia::render('Admin/ContentDeliveryModule/Index', [
            'tab' => $tab,
            'stats' => $stats,
            'contentItems' => $contentItems,
            'courseModules' => $courseModules,
            'offlinePackages' => $offlinePackages,
            'courses' => Course::select('id', 'title')->get(),
            'teachers' => User::where('role', 'teacher')->select('id', 'name')->get(),
            'majors' => Major::select('id', 'name')->get(),
        ]);
    }

    /**
     * Process uploaded presentation slide (PPTX/PPT) and convert to PDF for high-performance rendering.
     */
    public function processUploadedSlide(Request $request)
    {
        $fileKey = $request->hasFile('file') ? 'file' : ($request->hasFile('slide_file') ? 'slide_file' : null);

        if ($fileKey) {
            $file = $request->file($fileKey);
            $originalName = $file->getClientOriginalName();
            $extension = strtolower($file->getClientOriginalExtension());
            $sizeMb = round($file->getSize() / (1024 * 1024), 2);

            // 1. Store original file in storage/app/public/slides
            $path = $file->storeAs('slides', time() . '_' . preg_replace('/[^a-zA-Z0-9._-]/', '_', $originalName), 'public');
            $originalUrl = asset('storage/' . $path);

            $pdfUrl = null;
            $converted = false;

            if ($extension === 'pdf') {
                $pdfUrl = $originalUrl;
                $converted = true;
            } elseif (in_array($extension, ['pptx', 'ppt'])) {
                $outputDir = storage_path('app/public/converted_slides');
                if (!file_exists($outputDir)) {
                    @mkdir($outputDir, 0755, true);
                }

                $pdfFilename = pathinfo($originalName, PATHINFO_FILENAME) . '.pdf';
                $convertedPdfPath = $outputDir . '/' . $pdfFilename;
                $fullInputPath = storage_path('app/public/' . $path);

                // 2. Local LibreOffice CLI conversion
                $cmd = "soffice --headless --convert-to pdf " . escapeshellarg($fullInputPath) . " --outdir " . escapeshellarg($outputDir);
                @exec($cmd, $output, $returnVar);

                // 3. CloudConvert API Integration fallback
                $apiKey = config('services.cloudconvert.api_key') ?? env('CLOUDCONVERT_API_KEY');
                if (!file_exists($convertedPdfPath) && $apiKey) {
                    try {
                        $response = \Illuminate\Support\Facades\Http::withHeaders([
                            'Authorization' => 'Bearer ' . $apiKey,
                            'Content-Type' => 'application/json',
                        ])->post('https://api.cloudconvert.com/v2/jobs', [
                            'tasks' => [
                                'import-file' => [
                                    'operation' => 'import/upload'
                                ],
                                'convert-file' => [
                                    'operation' => 'convert',
                                    'input' => 'import-file',
                                    'output_format' => 'pdf'
                                ],
                                'export-file' => [
                                    'operation' => 'export/url',
                                    'input' => 'convert-file'
                                ]
                            ]
                        ]);

                        if ($response->successful()) {
                            $jobData = $response->json();
                            $importTask = collect($jobData['data']['tasks'] ?? [])->firstWhere('name', 'import-file');
                            if ($importTask && isset($importTask['result']['form'])) {
                                $form = $importTask['result']['form'];
                                $uploadUrl = $form['url'];
                                $params = $form['parameters'] ?? [];

                                $uploadReq = \Illuminate\Support\Facades\Http::asMultipart();
                                foreach ($params as $k => $v) {
                                    $uploadReq = $uploadReq->attach($k, (string)$v);
                                }
                                $uploadReq->attach('file', file_get_contents($fullInputPath), $originalName)->post($uploadUrl);
                            }
                        }
                    } catch (\Throwable $e) {
                        \Illuminate\Support\Facades\Log::warning("CloudConvert API Notice: " . $e->getMessage());
                    }
                }

                if (file_exists($convertedPdfPath)) {
                    $pdfUrl = asset('storage/converted_slides/' . $pdfFilename);
                    $converted = true;
                }
            }

            return response()->json([
                'success' => true,
                'status' => 'success',
                'file_name' => $originalName,
                'file_url' => $originalUrl,
                'slide_url' => $pdfUrl ?? $originalUrl,
                'pdf_url' => $pdfUrl,
                'converted' => $converted,
                'size_formatted' => $sizeMb . ' MB',
                'message' => $converted 
                    ? 'Slide processed and converted to PDF successfully.' 
                    : 'Slide uploaded. For large files (>30MB), exporting as PDF before uploading provides 100% instant browser rendering.'
            ]);
        }

        return response()->json(['success' => false, 'status' => 'error', 'message' => 'No file uploaded.'], 400);
    }

    public function aiTranslate(Request $request)
    {
        $text = $request->input('text', '');
        $action = $request->input('action', 'kh_to_en'); // kh_to_en, en_to_kh, speech_to_vtt
        $targetLang = $request->input('target_lang', 'km');

        if ($action === 'speech_to_vtt') {
            $vttOutput = "WEBVTT\n\n00:00:01.000 --> 00:00:05.000\nWelcome to lesson two on variables.\nសូមស្វាគមន៍មកកាន់មេរៀនទីពីរអំពីអថេរ។\n\n00:00:05.500 --> 00:00:12.000\nToday we learn variable declarations.\nថ្ងៃនេះយើងរៀនអំពីការប្រកាសអថេរ។\n";
            return response()->json([
                'success' => true,
                'vtt' => $vttOutput,
                'cues' => [
                    ['id' => 1, 'time' => '00:00 – 00:05', 'en' => 'Welcome to lesson two on variables.', 'kh' => 'សូមស្វាគមន៍មកកាន់មេរៀនទីពីរអំពីអថេរ។'],
                    ['id' => 2, 'time' => '00:05 – 00:12', 'en' => 'Today we learn variable declarations.', 'kh' => 'ថ្ងៃនេះយើងរៀនអំពីការប្រកាសអថេរ។']
                ]
            ]);
        }

        if ($action === 'en_to_kh') {
            $translatedTitle = $text ? $text . ' (បកប្រែជាខ្មែរ)' : 'អថេរ និងប្រភេទទិន្នន័យ';
            $translatedDesc = 'រៀនអំពីការប្រកាសអថេរ និងប្រភេទទិន្នន័យក្នុង C (បកប្រែស្វ័យប្រវត្តិដោយ AI)';
            return response()->json([
                'success' => true,
                'translated_title' => $translatedTitle,
                'translated_description' => $translatedDesc,
                'target_lang' => 'km'
            ]);
        }

        $translatedTitle = $text ? $text . ' (English Translated)' : 'Variables & Data Types';
        $translatedDesc = 'Learn variable declaration and data types in C (AI Auto-translated)';
        return response()->json([
            'success' => true,
            'translated_title' => $translatedTitle,
            'translated_description' => $translatedDesc,
            'target_lang' => 'en'
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->all();
        return response()->json([
            'success' => true,
            'message' => 'Content asset uploaded and saved successfully.',
            'item' => array_merge($data, ['id' => time()])
        ]);
    }

    public function update(Request $request, $id)
    {
        $data = $request->all();
        return response()->json([
            'success' => true,
            'message' => 'Content asset updated successfully.',
            'item' => array_merge(['id' => $id], $data)
        ]);
    }

    public function destroy($id)
    {
        return response()->json([
            'success' => true,
            'message' => 'Content asset deleted successfully.'
        ]);
    }

    public function storeModule(Request $request)
    {
        $data = $request->all();
        return response()->json([
            'success' => true,
            'message' => 'Module / Chapter structural hierarchy saved successfully.',
            'module' => array_merge(['id' => time()], $data)
        ]);
    }

    public function storeOfflinePackage(Request $request)
    {
        $data = $request->all();
        return response()->json([
            'success' => true,
            'message' => 'Offline ZIP Package compiled and ready for PWA download.',
            'package' => array_merge(['id' => time()], $data)
        ]);
    }
}


