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
        $defaultArea = $areas->firstWhere('is_default', true) ?? $areas->first();
        $featured = Course::query()
            ->commerciallyAvailable()
            ->where('is_featured', true)
            ->with('area')
            ->take(6)
            ->get();

        return view('site.home', compact('areas', 'defaultArea', 'featured'));
    }
}
