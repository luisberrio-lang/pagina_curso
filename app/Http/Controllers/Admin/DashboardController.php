<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Area;
use App\Models\Course;

class DashboardController extends Controller
{
  public function index()
  {
    $areasCount     = Area::count();
    $coursesCount   = Course::count();
    $publishedCount = Course::where('is_published', true)->count();

    // ✅ Para listar cursos por área (SIN filtrar publicados, para poder editar todo)
    $areas = Area::query()
      ->ordered()
      ->with(['courses' => function ($q) {
        $q->orderBy('sort_order')->orderByDesc('id');
      }])
      ->get();

    return view('Admin.dashboard', compact('areasCount','coursesCount','publishedCount','areas'));
  }
}
