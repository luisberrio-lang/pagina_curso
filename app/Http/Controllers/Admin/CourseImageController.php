<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\CourseImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CourseImageController extends Controller
{
  public function store(Request $r, Course $course)
  {
    $r->validate(['images.*' => 'required|image|max:4096']);

    $maxSort = (int)$course->images()->max('sort_order');

    foreach ($r->file('images', []) as $file) {
      $path = $file->store('courses/samples', 'public');
      $maxSort++;
      $course->images()->create(['path' => $path, 'sort_order' => $maxSort]);
    }

    return back()->with('ok','Muestras subidas');
  }

  public function destroy(Course $course, CourseImage $image)
  {
    abort_unless($image->course_id === $course->id, 404);
    Storage::disk('public')->delete($image->path);
    $image->delete();
    return back()->with('ok','Muestra eliminada');
  }

  public function moveUp(Course $course, CourseImage $image)
  {
    abort_unless($image->course_id === $course->id, 404);
    $prev = $course->images()->where('sort_order', '<', $image->sort_order)->orderByDesc('sort_order')->first();
    if ($prev) {
      [$prev->sort_order, $image->sort_order] = [$image->sort_order, $prev->sort_order];
      $prev->save(); $image->save();
    }
    return back();
  }

  public function moveDown(Course $course, CourseImage $image)
  {
    abort_unless($image->course_id === $course->id, 404);
    $next = $course->images()->where('sort_order', '>', $image->sort_order)->orderBy('sort_order')->first();
    if ($next) {
      [$next->sort_order, $image->sort_order] = [$image->sort_order, $next->sort_order];
      $next->save(); $image->save();
    }
    return back();
  }
}
