<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ProgressController extends Controller
{
    public function index(Request $request)
    {
        $tab = $request->query('tab', 'student');

        return Inertia::render('Admin/ProgressTrackingModule/Index', [
            'activeTab' => $tab,
        ]);
    }
}
