<?php

namespace App\Http\Controllers\Site;

use App\Http\Controllers\Controller;
use App\Models\Area;
use App\Models\Course;

class CatalogController extends Controller
{
  public function index(?Area $area = null)
  {
    $areas = Area::query()->ordered()->get();
    $selected = $area ?? Area::defaultArea() ?? $areas->first();

    if (!$selected) {
      return view('site.courses.index', [
        'areas' => $areas,
        'selected' => null,
        'courses' => collect(),
        'firstCourse' => null,
      ]);
    }

    $courses = Course::query()
      ->where('is_published', true)
      ->where('area_id', $selected->id)
      ->with('area')
      ->orderBy('sort_order')
      ->latest('id')
      ->get();

    $firstCourse = $courses->first();

    return view('site.courses.index', compact('areas','selected','courses','firstCourse'));
  }

  public function show(Course $course)
  {
    abort_unless($course->is_published, 404);

    $course->load(['area','images']);

    return view('site.courses.show', compact('course'));
  }
}
