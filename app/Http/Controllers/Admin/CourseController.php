<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Area;
use App\Models\Course;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class CourseController extends Controller
{
  public function index()
  {
    $courses = Course::query()
      ->with('area')
      ->orderByDesc('id')
      ->paginate(20);

    return view('Admin.courses.index', compact('courses'));
  }

  public function create()
  {
    $areas = Area::query()->ordered()->get();
    return view('Admin.courses.create', compact('areas'));
  }

  public function store(Request $r)
  {
    $data = $this->validated($r);

    // ✅ checkboxes boolean reales
    $data['is_published'] = $r->boolean('is_published');
    $data['is_featured']  = $r->boolean('is_featured');

    // ✅ Portada
    if ($r->hasFile('cover')) {
      $data['cover_path'] = $r->file('cover')->store('courses/covers', 'public');
    }

    // ✅ slug seguro (NO undefined)
    $data['slug'] = isset($data['slug']) && $data['slug'] ? $data['slug'] : Str::slug($data['title']);

    // ✅ Listas (textarea o inputs dinámicos)
    $data['learning']     = $this->normalizeList($r->input('learning'));
    $data['benefits']     = $this->normalizeList($r->input('benefits'));
    $data['includes']     = $this->normalizeList($r->input('includes'));
    $data['requirements'] = $this->normalizeList($r->input('requirements'));

    // ✅ Temario (HTML básico)
    $data['syllabus'] = $r->input('syllabus');

    $course = Course::create($data);

    // ✅ Marcar área default
    if ($r->boolean('make_default_area') && $course->area_id) {
      Area::query()->update(['is_default' => false]);
      Area::where('id', $course->area_id)->update(['is_default' => true]);
    }

    return $r->boolean('from_dashboard')
      ? redirect()->route('admin.dashboard')->with('ok', 'Curso creado correctamente.')
      : redirect()->route('admin.courses.index')->with('ok', 'Curso creado correctamente.');
  }

  public function edit(Course $course)
  {
    $areas = Area::query()->ordered()->get();
    $course->load(['area','images']);
    return view('Admin.courses.edit', compact('course','areas'));
  }

  public function update(Request $r, Course $course)
  {
    $data = $this->validated($r, $course->id);

    $data['is_published'] = $r->boolean('is_published');
    $data['is_featured']  = $r->boolean('is_featured');

    // ✅ Portada (reemplazo)
    if ($r->hasFile('cover')) {
      if ($course->cover_path && Storage::disk('public')->exists($course->cover_path)) {
        Storage::disk('public')->delete($course->cover_path);
      }
      $data['cover_path'] = $r->file('cover')->store('courses/covers', 'public');
    }

    // ✅ slug seguro
    $data['slug'] = isset($data['slug']) && $data['slug'] ? $data['slug'] : Str::slug($data['title']);

    // ✅ Listas (textarea o inputs dinámicos)
    $data['learning']     = $this->normalizeList($r->input('learning'));
    $data['benefits']     = $this->normalizeList($r->input('benefits'));
    $data['includes']     = $this->normalizeList($r->input('includes'));
    $data['requirements'] = $this->normalizeList($r->input('requirements'));

    // ✅ Temario (HTML básico)
    $data['syllabus'] = $r->input('syllabus');

    $course->update($data);

    // ✅ Marcar área default
    if ($r->boolean('make_default_area') && $course->area_id) {
      Area::query()->update(['is_default' => false]);
      Area::where('id', $course->area_id)->update(['is_default' => true]);
    }

    return $r->boolean('from_dashboard')
      ? redirect()->route('admin.dashboard')->with('ok', 'Cambios guardados correctamente.')
      : redirect()->route('admin.courses.index')->with('ok', 'Curso actualizado correctamente.');
  }

  public function destroy(Course $course)
  {
    if ($course->cover_path && Storage::disk('public')->exists($course->cover_path)) {
      Storage::disk('public')->delete($course->cover_path);
    }
    $course->delete();
    return back()->with('ok', 'Curso eliminado.');
  }

  private function validated(Request $r, ?int $ignoreId = null): array
  {
    $uniqueSlug = 'unique:courses,slug';
    if ($ignoreId) $uniqueSlug .= ',' . $ignoreId;

    return $r->validate([
      'area_id'       => 'required|exists:areas,id',
      'title'         => 'required|string|max:160',
      'slug'          => ['nullable','string','max:180', $uniqueSlug],
      'short_desc'    => 'nullable|string|max:255',

      'description'   => 'nullable|string',
      'audience'      => 'nullable|string',
      'whatsapp_message' => 'nullable|string|max:255',
      'syllabus'      => 'nullable|string',

      'sort_order'    => 'nullable|integer|min:0|max:9999',

      // ✅ Precio único (usar price_anual)
      'price_anual'   => 'nullable|numeric|min:0',
      'price_previous' => 'nullable|numeric|min:0',

      'cover'         => 'nullable|image|max:4096',
    ]);
  }

  private function normalizeList($value): array
  {
    if (is_array($value)) {
      $lines = $value;
    } else {
      $lines = preg_split("/\r\n|\n|\r/", (string)$value) ?: [];
    }

    $lines = array_map(fn($s) => trim((string)$s), $lines);
    return array_values(array_filter($lines, fn($s) => $s !== ''));
  }
}
