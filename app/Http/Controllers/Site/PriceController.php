<?php

namespace App\Http\Controllers\Site;

use App\Http\Controllers\Controller;
use App\Models\Area;
use App\Models\Course;

class PriceController extends Controller
{
  public function index()
  {
    $areas = Area::query()->ordered()->get();
    $selected = Area::defaultArea() ?? $areas->first();

    // selector por query ?area=slug
    if (request('area')) {
      $picked = Area::query()->where('slug', request('area'))->first();
      if ($picked) $selected = $picked;
    }

    if (!$selected) {
      return view('site.price', [
        'areas' => $areas,
        'selected' => null,
        'courses' => collect(),
      ]);
    }

    $courses = Course::query()
      ->where('is_published', true)
      ->where('area_id', $selected->id)
      ->with('area')
      ->orderBy('sort_order')
      ->latest('id')
      ->get();

    return view('site.price', compact('areas','selected','courses'));
  }
}
