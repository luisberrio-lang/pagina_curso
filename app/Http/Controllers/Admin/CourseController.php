<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Area;
use App\Models\Course;
use App\Support\SafeHtml;
use App\Support\UniqueSlug;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Throwable;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\ImageManager;

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
    $newCover = null;

    $data['is_published'] = $r->boolean('is_published');
    $data['is_featured']  = $r->boolean('is_featured');

    // Portada: guarda directo en public_html/storage/courses/covers
    if ($r->hasFile('cover')) {
      $data['cover_path'] = $newCover = $this->saveCover($r->file('cover'));
    }

    $data['slug'] = filled($data['slug'] ?? null)
      ? $data['slug']
      : UniqueSlug::for(Course::query(), $data['title']);

    $data['learning']     = $this->normalizeList($r->input('learning'));
    $data['benefits']     = $this->normalizeList($r->input('benefits'));
    $data['includes']     = $this->normalizeList($r->input('includes'));
    $data['requirements'] = $this->normalizeList($r->input('requirements'));

    $data['description'] = SafeHtml::sanitize($r->input('description')) ?: null;
    $data['syllabus'] = SafeHtml::sanitize($r->input('syllabus')) ?: null;

    try {
      $course = DB::transaction(fn () => Course::create($data));
    } catch (Throwable $exception) {
      $this->deleteCover($newCover);
      throw $exception;
    }

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
    $oldCover = $course->cover_path;
    $newCover = null;

    $data['is_published'] = $r->boolean('is_published');
    $data['is_featured']  = $r->boolean('is_featured');

    // Portada: reemplaza y guarda directo en public_html/storage/courses/covers
    if ($r->hasFile('cover')) {
      $data['cover_path'] = $newCover = $this->saveCover($r->file('cover'));
    }

    $data['slug'] = filled($data['slug'] ?? null) ? $data['slug'] : $course->slug;

    $data['learning']     = $this->normalizeList($r->input('learning'));
    $data['benefits']     = $this->normalizeList($r->input('benefits'));
    $data['includes']     = $this->normalizeList($r->input('includes'));
    $data['requirements'] = $this->normalizeList($r->input('requirements'));

    $data['description'] = SafeHtml::sanitize($r->input('description')) ?: null;
    $data['syllabus'] = SafeHtml::sanitize($r->input('syllabus')) ?: null;

    try {
      DB::transaction(fn () => $course->update($data));
    } catch (Throwable $exception) {
      $this->deleteCover($newCover);
      throw $exception;
    }

    if ($newCover && $oldCover !== $newCover) {
      $this->deleteCover($oldCover);
    }

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
    $cover = $course->cover_path;
    $samples = $course->images()->pluck('path')->all();

    DB::transaction(fn () => $course->delete());

    $this->deleteCover($cover);
    Storage::disk('public')->delete($samples);

    return back()->with('ok', 'Curso eliminado.');
  }

  private function saveCover($file): string
  {
    $filename = Str::random(40) . '.webp';

    $manager = new ImageManager(new Driver());
    $image = $manager->read($file->getRealPath())
      ->resize(900, 500)
      ->toWebp(quality: 75);

    $stored = Storage::disk('public')->put('courses/covers/' . $filename, (string) $image);

    if (! $stored) {
      throw new \RuntimeException('No se pudo guardar la portada del curso.');
    }

    return 'courses/covers/' . $filename;
  }

  private function deleteCover(?string $path): void
  {
    if (!$path) {
      return;
    }

    Storage::disk('public')->delete($path);
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

      'price_anual'   => 'nullable|numeric|min:0.01|decimal:0,2',
      'price_previous' => 'nullable|numeric|min:0.01|decimal:0,2',

      'cover'         => 'nullable|image|mimes:jpg,jpeg,png,webp|max:4096',
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
