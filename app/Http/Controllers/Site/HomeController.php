<?php

namespace App\Http\Controllers\Site;

use App\Http\Controllers\Controller;
use App\Models\Area;
use App\Models\Course;

class HomeController extends Controller
{
    public function index()
    {
        $areas = Area::query()->orderBy('sort_order')->orderBy('name')->get();
        $defaultArea = Area::query()->where('is_default', true)->first() ?? $areas->first();
        $featured = Course::query()
            ->where('is_published', true)
            ->where('is_featured', true)
            ->with('area')
            ->take(6)
            ->get();

        return view('site.home', compact('areas', 'defaultArea', 'featured'));
    }
}
