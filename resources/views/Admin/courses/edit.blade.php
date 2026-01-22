@extends('layouts.site')

@section('content')
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
          <input type="checkbox" name="make_default_area" value="1">
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
      <textarea name="description" rows="4"
        class="w-full rounded-xl bg-black/30 border border-white/10 px-4 py-3 mt-2">{{ old('description', $course->description) }}</textarea>
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

    <div class="grid md:grid-cols-3 gap-6 items-end">
      <div>
        <label class="text-sm text-white/70">Precio (Pago único / Acceso de por vida)</label>
        <input name="price_anual" value="{{ old('price_anual', $course->price_anual) }}"
               class="w-full rounded-xl bg-black/30 border border-white/10 px-4 py-3 mt-2" />
      </div>

      <label class="inline-flex items-center gap-2 text-sm text-white/75">
        <input type="checkbox" name="is_published" value="1" @checked($course->is_published)>
        Publicado
      </label>

      <label class="inline-flex items-center gap-2 text-sm text-white/75">
        <input type="checkbox" name="is_featured" value="1" @checked($course->is_featured)>
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
@endsection
