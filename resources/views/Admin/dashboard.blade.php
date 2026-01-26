@extends('layouts.site')

@section('content')
  <section class="flex flex-col md:flex-row md:items-end md:justify-between gap-4">
    <div>
      <h1 class="text-3xl font-extrabold">Dashboard Administrador</h1>
      <p class="mt-2 text-white/75">
        Crea y edita cursos (se refleja en <b>Programas/Cursos</b>).
      </p>
    </div>

    <div class="flex flex-wrap gap-3">
      <a href="{{ route('admin.areas.index') }}" class="btn btn-ghost">Gestionar Áreas</a>
      <a href="{{ route('admin.courses.index') }}" class="btn btn-ghost">Gestionar Cursos</a>
      <a href="{{ route('courses.index') }}" class="btn btn-accent" target="_blank">Ver en la web</a>
    </div>
  </section>

  @if(session('ok'))
    <div class="mt-6 glass border border-emerald-500/30 bg-emerald-500/10 rounded-2xl p-4 text-emerald-100">
      {{ session('ok') }}
    </div>
  @endif

  {{-- KPIs --}}
  <section class="mt-8 grid md:grid-cols-3 gap-6">
    <div class="glass p-6 rounded-2xl border border-white/10">
      <div class="text-white/70 text-sm">Áreas</div>
      <div class="mt-2 text-4xl font-extrabold">{{ $areasCount }}</div>
    </div>
    <div class="glass p-6 rounded-2xl border border-white/10">
      <div class="text-white/70 text-sm">Cursos</div>
      <div class="mt-2 text-4xl font-extrabold">{{ $coursesCount }}</div>
    </div>
    <div class="glass p-6 rounded-2xl border border-white/10">
      <div class="text-white/70 text-sm">Publicados</div>
      <div class="mt-2 text-4xl font-extrabold">{{ $publishedCount }}</div>
      <div class="mt-2 text-white/60 text-sm">Solo publicados aparecen al público.</div>
    </div>
  </section>

  {{-- FORM CREAR CURSO --}}
  <section class="mt-10 glass p-6 rounded-3xl border border-white/10">
    <h2 class="text-xl font-semibold">Crear curso</h2>
    <p class="mt-1 text-white/65 text-sm">Ordenado, rápido y funcional. (Imagen recomendada 1080x1080).</p>

    <form class="mt-6 grid gap-6" method="POST" action="{{ route('admin.courses.store') }}" enctype="multipart/form-data">
      @csrf
      <input type="hidden" name="from_dashboard" value="1">

      <div class="grid md:grid-cols-2 gap-6">
        {{-- Combo área --}}
        <div>
          <label class="text-sm text-white/70">Seleccionar Área</label>
          <select name="area_id" class="w-full rounded-xl bg-black/30 border border-white/10 px-4 py-3 mt-2">
            @foreach($areas as $a)
              <option value="{{ $a->id }}" @selected(old('area_id') == $a->id)>
                {{ $a->name }} @if($a->is_default) (Default) @endif
              </option>
            @endforeach
          </select>
          @error('area_id') <p class="text-red-300 text-sm mt-2">{{ $message }}</p> @enderror

          <label class="mt-4 inline-flex items-center gap-2 text-sm text-white/75">
            <input type="checkbox" name="make_default_area" value="1">
            Marcar área como default
          </label>
        </div>

        {{-- Imagen --}}
        <div>
          <label class="text-sm text-white/70">IMG 1080x1080 (Portada)</label>
          <input type="file" name="cover" accept="image/*"
                 class="w-full rounded-xl bg-black/30 border border-white/10 px-4 py-3 mt-2">
          @error('cover') <p class="text-red-300 text-sm mt-2">{{ $message }}</p> @enderror
        </div>
      </div>

      <div class="grid md:grid-cols-2 gap-6">
        <div>
          <label class="text-sm text-white/70">Título</label>
          <input name="title" value="{{ old('title') }}"
                 class="w-full rounded-xl bg-black/30 border border-white/10 px-4 py-3 mt-2" />
          @error('title') <p class="text-red-300 text-sm mt-2">{{ $message }}</p> @enderror
        </div>

        <div>
          <label class="text-sm text-white/70">Descripción corta (tarjeta)</label>
          <input name="short_desc" value="{{ old('short_desc') }}"
                 class="w-full rounded-xl bg-black/30 border border-white/10 px-4 py-3 mt-2" />
          @error('short_desc') <p class="text-red-300 text-sm mt-2">{{ $message }}</p> @enderror
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
          <div class="wysiwyg-editor" contenteditable="true" data-wysiwyg-editor data-placeholder="Escribe el contenido del curso...">{!! old('description') !!}</div>
          <textarea name="description" class="hidden" data-wysiwyg-input>{!! old('description') !!}</textarea>
        </div>
        @error('description') <p class="text-red-300 text-sm mt-2">{{ $message }}</p> @enderror
      </div>

      <div class="grid md:grid-cols-2 gap-6">
        <div>
          <label class="text-sm text-white/70">Para quién es</label>
          <textarea name="audience" rows="3"
                    class="w-full rounded-xl bg-black/30 border border-white/10 px-4 py-3 mt-2">{{ old('audience') }}</textarea>
          @error('audience') <p class="text-red-300 text-sm mt-2">{{ $message }}</p> @enderror
        </div>
        <div>
          <label class="text-sm text-white/70">Qué aprenderás (1 por línea)</label>
          <textarea name="learning" rows="3"
                    class="w-full rounded-xl bg-black/30 border border-white/10 px-4 py-3 mt-2"
                    placeholder="Tema 1&#10;Tema 2&#10;...">{{ old('learning') }}</textarea>
        </div>
      </div>

      <div class="grid md:grid-cols-2 gap-6">
        <div>
          <label class="text-sm text-white/70">EXTRAS (1 por línea)</label>
          <textarea name="includes" rows="5"
                    class="w-full rounded-xl bg-black/30 border border-white/10 px-4 py-3 mt-2"
                    placeholder="Acceso inmediato&#10;Archivos descargables&#10;...">{{ old('includes') }}</textarea>
        </div>

        <div>
          <label class="text-sm text-white/70">BENEFICIOS (1 por línea)</label>
          <textarea name="benefits" rows="5"
                    class="w-full rounded-xl bg-black/30 border border-white/10 px-4 py-3 mt-2"
                    placeholder="Soporte por WhatsApp&#10;Actualizaciones&#10;...">{{ old('benefits') }}</textarea>
        </div>

      </div>
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
          <div class="wysiwyg-editor" contenteditable="true" data-wysiwyg-editor data-placeholder="Escribe el temario...">{!! old('syllabus') !!}</div>
          <textarea name="syllabus" class="hidden" data-wysiwyg-input>{!! old('syllabus') !!}</textarea>
        </div>
      </div>
      <div class="p-5 rounded-2xl border border-white/10 bg-white/5">
        <h3 class="font-semibold text-lg">Precios y Descuento</h3>
        <p class="mt-1 text-white/60 text-sm">El % DSCTO se calcula automáticamente.</p>

        <div class="mt-4 grid md:grid-cols-3 gap-6 items-end" data-discount-calc>
          <div>
            <label class="text-sm text-white/70">Precio actual (Pago único / Acceso de por vida)</label>
            <input name="price_anual" value="{{ old('price_anual') }}"
                   class="w-full rounded-xl bg-black/30 border border-white/10 px-4 py-3 mt-2"
                   placeholder="Ej: 49.90" data-price-current />
            @error('price_anual') <p class="text-red-300 text-sm mt-2">{{ $message }}</p> @enderror
          </div>

          <div>
            <label class="text-sm text-white/70">Precio anterior</label>
            <input name="price_previous" value="{{ old('price_previous') }}"
                   class="w-full rounded-xl bg-black/30 border border-white/10 px-4 py-3 mt-2"
                   placeholder="Ej: 79.90" data-price-previous />
            @error('price_previous') <p class="text-red-300 text-sm mt-2">{{ $message }}</p> @enderror
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
          <input type="checkbox" name="is_published" value="1" {{ old('is_published') ? 'checked' : '' }}>
          Publicado (se muestra en la web)
        </label>

        <label class="inline-flex items-center gap-2 text-sm text-white/75">
          <input type="checkbox" name="is_featured" value="1" {{ old('is_featured') ? 'checked' : '' }}>
          Destacado (aparece en Inicio)
        </label>
      </div>

      <div class="flex gap-3">
        <button class="btn btn-accent" type="submit">Guardar cambios</button>
        <a class="btn btn-ghost" href="{{ route('admin.dashboard') }}">Cancelar</a>
      </div>
    </form>
  </section>

  {{-- LISTADO POR ÁREA --}}
  <section class="mt-10">
    <h2 class="text-xl font-semibold">Cursos por área</h2>
    <p class="mt-1 text-white/65 text-sm">Aquí ves lo que ya está creado. Edita desde el botón <b>Editar</b>.</p>

    <div class="mt-6 grid gap-6">
      @foreach($areas as $a)
        <div class="glass p-6 rounded-3xl border border-white/10">
          <div class="flex items-center justify-between gap-4">
            <div class="flex items-center gap-3">
              <h3 class="text-lg font-bold">{{ $a->name }}</h3>
              @if($a->is_default)
                <span class="chip chip-accent">Default</span>
              @endif
            </div>

            @if(!$a->is_default)
              <form method="POST" action="{{ route('admin.areas.default', $a) }}">
                @csrf
                <button class="chip" type="submit">Hacer default</button>
              </form>
            @endif
          </div>

          <div class="mt-4 grid md:grid-cols-2 gap-4">
            @forelse($a->courses as $c)
              <div class="p-4 rounded-2xl border border-white/10 bg-white/5">
                <div class="flex items-start justify-between gap-3">
                  <div>
                    <div class="font-semibold">{{ $c->title }}</div>
                    <div class="text-sm text-white/65 mt-1">
                      Precio: <b>{{ $c->price_anual !== null ? number_format((float)$c->price_anual, 2) : '—' }}</b>
                      <span class="text-white/50"> (Pago único)</span>
                    </div>
                    <div class="text-xs text-white/55 mt-1">
                      Estado: {!! $c->is_published ? '<span class="chip chip-accent">Publicado</span>' : '<span class="chip">Borrador</span>' !!}
                    </div>
                  </div>

                  <div class="flex gap-2">
                    <a class="chip" href="{{ route('admin.courses.edit', $c) }}">Editar</a>
                    <a class="chip" target="_blank" href="{{ route('courses.show', $c->slug) }}">Ver</a>
                  </div>
                </div>
              </div>
            @empty
              <div class="text-white/60">No hay cursos en esta área.</div>
            @endforelse
          </div>
        </div>
      @endforeach
    </div>
  </section>
@endsection




