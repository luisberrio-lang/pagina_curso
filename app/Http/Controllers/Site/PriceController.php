<?php

namespace App\Http\Controllers\Site;

use App\Http\Controllers\Controller;
use App\Models\Area;

class PriceController extends Controller
{
  public function index()
  {
    $areas = Area::query()->ordered()->get();
    $selected = Area::defaultArea();

    // selector por query ?area=slug
    if (request('area')) {
      $picked = Area::query()->where('slug', request('area'))->first();
      if ($picked) $selected = $picked;
    }

    $plansAll = config('plans', []);
    $plans = $plansAll[$selected->slug] ?? [];

    return view('site.price', compact('areas','selected','plans'));
  }
}
