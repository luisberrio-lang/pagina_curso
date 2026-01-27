@extends('layouts.site')

@section('title', 'Editar Curso | Cursos de Ingeniería')

@section('content')
  <div class="glass p-6 md:p-8 rounded-3xl">
  <section class="flex items-end justify-between gap-4">
    <div>
      <h1 class="text-3xl font-extrabold">Editar curso</h1>
      <p class="mt-2 text-white/70">{{ $course->title }}</p>
    </div>
    <a class="btn btn-ghost" href="{{ route('admin.dashboard') }}">Volver al dashboard</a>
  </section>

  @if(session('ok'))
    <div class="mt-6 glass border border-emerald-500/30 bg-emerald-500/10 rounded-2xl p-4 text-emerald-100">
      {{ session('ok') }}
    </div>
  @endif

  <form class="mt-6 grid gap-6" method="POST" action="{{ route('admin.courses.update', $course) }}" enctype="multipart/form-data">
    @csrf
    @method('PUT')
    <input type="hidden" name="from_dashboard" value="1">

    <div class="grid md:grid-cols-2 gap-6">
      <div>
        <label class="text-sm text-white/70">Área</label>
        <select name="area_id" class="w-full rounded-xl bg-black/30 border border-white/10 px-4 py-3 mt-2">
          @foreach($areas as $a)
            <option value="{{ $a->id }}" @selected($course->area_id == $a->id)>{{ $a->name }}</option>
          @endforeach
        </select>

        <label class="mt-4 inline-flex items-center gap-2 text-sm text-white/75">
          <input type="checkbox" name="make_default_area" value="1" class="chk-green">
          Marcar área como default
        </label>
      </div>

      <div>
        <label class="text-sm text-white/70">Portada (1080x1080)</label>
        <input type="file" name="cover" accept="image/*"
               class="w-full rounded-xl bg-black/30 border border-white/10 px-4 py-3 mt-2">
        @if($course->coverUrl())
          <img src="{{ $course->coverUrl() }}" class="mt-3 rounded-2xl border border-white/10 max-h-48 object-cover" />
        @endif
      </div>
    </div>

    <div class="grid md:grid-cols-2 gap-6">
      <div>
        <label class="text-sm text-white/70">Título</label>
        <input name="title" value="{{ old('title', $course->title) }}"
               class="w-full rounded-xl bg-black/30 border border-white/10 px-4 py-3 mt-2" />
      </div>
      <div>
        <label class="text-sm text-white/70">Descripción corta</label>
        <input name="short_desc" value="{{ old('short_desc', $course->short_desc) }}"
               class="w-full rounded-xl bg-black/30 border border-white/10 px-4 py-3 mt-2" />
      </div>
    </div>

    <div>
      <label class="text-sm text-white/70">Contenido del curso</label>
      <div class="wysiwyg mt-2" data-wysiwyg>
        <div class="wysiwyg-toolbar">
          <button type="button" class="chip" data-cmd="bold"><b>B</b></button>
          <button type="button" class="chip" data-cmd="italic"><i>I</i></button>
          <button type="button" class="chip" data-cmd="underline"><u>U</u></button>
          <button type="button" class="chip" data-cmd="insertUnorderedList">Lista</button>
          <button type="button" class="chip" data-cmd="insertOrderedList">1.2.3</button>
        </div>
        <div class="wysiwyg-editor" contenteditable="true" data-wysiwyg-editor data-placeholder="Escribe el contenido del curso...">{!! old('description', $course->description) !!}</div>
        <textarea name="description" class="hidden" data-wysiwyg-input>{!! old('description', $course->description) !!}</textarea>
      </div>
    </div>

    <div class="grid md:grid-cols-2 gap-6">
      <div>
        <label class="text-sm text-white/70">Para quién es</label>
        <textarea name="audience" rows="3"
          class="w-full rounded-xl bg-black/30 border border-white/10 px-4 py-3 mt-2">{{ old('audience', $course->audience) }}</textarea>
      </div>
      <div>
        <label class="text-sm text-white/70">Qué aprenderás (1 por línea)</label>
        <textarea name="learning" rows="3"
          class="w-full rounded-xl bg-black/30 border border-white/10 px-4 py-3 mt-2"
          placeholder="Tema 1&#10;Tema 2&#10;...">{{ old('learning', is_array($course->learning) ? implode("\n", $course->learning) : '') }}</textarea>
      </div>
    </div>

    <div class="grid md:grid-cols-2 gap-6">
      <div>
        <label class="text-sm text-white/70">EXTRAS (1 por línea)</label>
        <textarea name="includes" rows="5"
          class="w-full rounded-xl bg-black/30 border border-white/10 px-4 py-3 mt-2">{{ old('includes', is_array($course->includes) ? implode("\n",$course->includes) : '') }}</textarea>
      </div>
      <div>
        <label class="text-sm text-white/70">BENEFICIOS (1 por línea)</label>
        <textarea name="benefits" rows="5"
          class="w-full rounded-xl bg-black/30 border border-white/10 px-4 py-3 mt-2">{{ old('benefits', is_array($course->benefits) ? implode("\n",$course->benefits) : '') }}</textarea>
      </div>

    </div>
    @php
      $syllabusValue = old('syllabus');
      if ($syllabusValue === null) {
        if (is_array($course->syllabus)) {
          $parts = [];
          foreach ($course->syllabus as $m) {
            $title = e($m['title'] ?? 'Modulo');
            $topics = '';
            if (!empty($m['topics']) && is_array($m['topics'])) {
              $items = '';
              foreach ($m['topics'] as $t) { $items .= '<li>'.e($t).'</li>'; }
              $topics = '<ul>'.$items.'</ul>';
            }
            $parts[] = '<p><strong>'.$title.'</strong></p>'.$topics;
          }
          $syllabusValue = implode('', $parts);
        } else {
          $syllabusValue = $course->syllabus ?? '';
        }
      }
    @endphp

    <div>
      <label class="text-sm text-white/70">Temario</label>
      <div class="wysiwyg mt-2" data-wysiwyg>
        <div class="wysiwyg-toolbar">
          <button type="button" class="chip" data-cmd="bold"><b>B</b></button>
          <button type="button" class="chip" data-cmd="italic"><i>I</i></button>
          <button type="button" class="chip" data-cmd="underline"><u>U</u></button>
          <button type="button" class="chip" data-cmd="insertUnorderedList">Lista</button>
          <button type="button" class="chip" data-cmd="insertOrderedList">1.2.3</button>
        </div>
        <div class="wysiwyg-editor" contenteditable="true" data-wysiwyg-editor data-placeholder="Escribe el temario...">{!! $syllabusValue !!}</div>
        <textarea name="syllabus" class="hidden" data-wysiwyg-input>{!! $syllabusValue !!}</textarea>
      </div>
    </div>
    <div class="p-5 rounded-2xl border border-white/10 bg-white/5">
      <h3 class="font-semibold text-lg">Precios y Descuento</h3>
      <p class="mt-1 text-white/60 text-sm">El % DSCTO se calcula automáticamente.</p>

      <div class="mt-4 grid md:grid-cols-3 gap-6 items-end" data-discount-calc>
        <div>
          <label class="text-sm text-white/70">Precio actual (Pago único / Acceso de por vida)</label>
          <input name="price_anual" value="{{ old('price_anual', $course->price_anual) }}"
                 class="w-full rounded-xl bg-black/30 border border-white/10 px-4 py-3 mt-2"
                 data-price-current />
        </div>

        <div>
          <label class="text-sm text-white/70">Precio anterior</label>
          <input name="price_previous" value="{{ old('price_previous', $course->price_previous) }}"
                 class="w-full rounded-xl bg-black/30 border border-white/10 px-4 py-3 mt-2"
                 data-price-previous />
        </div>

        <div>
          <label class="text-sm text-white/70">% DSCTO (automático)</label>
          <div class="mt-2 px-4 py-3 rounded-xl border border-white/10 bg-black/30 text-center font-semibold"
               data-discount-output>—</div>
        </div>
      </div>
    </div>

    <div class="grid md:grid-cols-2 gap-6 items-end">
      <label class="inline-flex items-center gap-2 text-sm text-white/75">
        <input type="checkbox" name="is_published" value="1" class="chk-green" @checked($course->is_published)>
        Publicado
      </label>

      <label class="inline-flex items-center gap-2 text-sm text-white/75">
        <input type="checkbox" name="is_featured" value="1" class="chk-green" @checked($course->is_featured)>
        Destacado
      </label>
    </div>

    <div class="flex gap-3">
      <button class="btn btn-accent" type="submit">Guardar cambios</button>
      <a class="btn btn-ghost" href="{{ route('admin.dashboard') }}">Cancelar</a>
    </div>
  </form>

  {{-- Muestras --}}
  <section class="mt-10 glass p-6 rounded-3xl border border-white/10">
    <h2 class="text-xl font-semibold">Muestras (imágenes)</h2>
    <p class="mt-1 text-white/60 text-sm">Sube múltiples, ordénalas y elimina.</p>

    <form class="mt-4" method="POST" action="{{ route('admin.courses.images.store', $course) }}" enctype="multipart/form-data">
      @csrf
      <input type="file" name="images[]" multiple accept="image/*"
             class="w-full rounded-xl bg-black/30 border border-white/10 px-4 py-3">
      <button class="btn btn-accent mt-3" type="submit">Subir muestras</button>
    </form>

    <div class="mt-6 grid md:grid-cols-4 gap-4">
      @foreach($course->images as $img)
        <div class="rounded-2xl overflow-hidden border border-white/10 bg-white/5">
          <img src="{{ asset('storage/'.$img->path) }}" class="h-40 w-full object-cover" />
          <div class="p-3 flex gap-2 justify-between">
            <form method="POST" action="{{ route('admin.courses.images.up', [$course, $img]) }}">@csrf
              <button class="chip" type="submit">↑</button>
            </form>
            <form method="POST" action="{{ route('admin.courses.images.down', [$course, $img]) }}">@csrf
              <button class="chip" type="submit">↓</button>
            </form>
            <form method="POST" action="{{ route('admin.courses.images.destroy', [$course, $img]) }}">@csrf @method('DELETE')
              <button class="chip" type="submit">Eliminar</button>
            </form>
          </div>
        </div>
      @endforeach
    </div>
  </section>
  </div>
@endsection



