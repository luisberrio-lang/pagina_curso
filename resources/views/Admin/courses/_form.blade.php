@php
  $isEdit = isset($course) && $course->exists;
@endphp

<div class="glass p-6 rounded-3xl border border-white/10">
  {{-- Barra superior fija del editor --}}
  <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
    <div>
      <h1 class="text-2xl md:text-3xl font-extrabold">
        {{ $isEdit ? 'Editar Curso' : 'Crear Curso' }}
        <span class="text-cyan-300">{{ $isEdit ? $course->title : '' }}</span>
      </h1>
      <p class="mt-1 text-white/70">
        Editor completo: Básico + Precio + Imágenes + Contenido + Publicación.
      </p>
    </div>

    <div class="flex flex-wrap gap-2">
      <button type="submit" class="btn btn-accent" data-save-btn>
        Guardar cambios
      </button>

      @if($isEdit)
        <a class="btn btn-ghost" target="_blank" href="{{ route('courses.show', $course) }}">Ver en la web</a>
      @endif

      <a class="btn btn-ghost" href="{{ route('admin.courses.index') }}">Volver</a>
    </div>
  </div>

  {{-- Tabs --}}
  <div class="mt-6 flex flex-wrap gap-2" data-tabs>
    <button type="button" class="chip chip-accent" data-tab="basic">Básico</button>
    <button type="button" class="chip" data-tab="price">Precio</button>
    <button type="button" class="chip" data-tab="images">Imágenes</button>
    <button type="button" class="chip" data-tab="content">Contenido</button>
    <button type="button" class="chip" data-tab="publish">Publicación</button>
    <span class="ml-auto text-xs text-white/60 hidden" data-unsaved>• Cambios sin guardar</span>
  </div>

  {{-- BASIC --}}
  <section class="mt-6" data-panel="basic">
    <div class="grid md:grid-cols-2 gap-5">
      <div>
        <label class="block text-sm text-white/80 mb-2">Área</label>
        <select name="area_id" class="input w-full">
          @foreach($areas as $a)
            <option value="{{ $a->id }}" @selected(old('area_id', $course->area_id ?? '') == $a->id)>{{ $a->name }}</option>
          @endforeach
        </select>
        @error('area_id') <p class="mt-2 text-red-300 text-sm">{{ $message }}</p> @enderror
      </div>

      <div>
        <label class="block text-sm text-white/80 mb-2">Título</label>
        <input name="title" class="input w-full" value="{{ old('title', $course->title ?? '') }}" />
        @error('title') <p class="mt-2 text-red-300 text-sm">{{ $message }}</p> @enderror
      </div>

      <div class="md:col-span-2">
        <label class="block text-sm text-white/80 mb-2">Resumen corto (para tarjeta)</label>
        <textarea name="short_desc" rows="2" class="input w-full">{{ old('short_desc', $course->short_desc ?? '') }}</textarea>
        @error('short_desc') <p class="mt-2 text-red-300 text-sm">{{ $message }}</p> @enderror
      </div>
    </div>
  </section>

  {{-- PRICE (VISIBLE SOLO EN PROGRAMAS/CURSOS) --}}
  <section class="mt-6 hidden" data-panel="price">
    <div class="grid md:grid-cols-2 gap-5">
      <div>
        <label class="block text-sm text-white/80 mb-2">Precio actual (visible solo en Programas/Cursos)</label>
        <input name="price" type="number" step="0.01" class="input w-full"
               value="{{ old('price', $course->price ?? '') }}" placeholder="Ej: 99.90" />
        @error('price') <p class="mt-2 text-red-300 text-sm">{{ $message }}</p> @enderror
      </div>

      <div>
        <label class="block text-sm text-white/80 mb-2">Precio anterior (opcional)</label>
        <input name="old_price" type="number" step="0.01" class="input w-full"
               value="{{ old('old_price', $course->old_price ?? '') }}" placeholder="Ej: 149.90" />
        @error('old_price') <p class="mt-2 text-red-300 text-sm">{{ $message }}</p> @enderror
      </div>

      <div class="md:col-span-2 text-white/70 text-sm">
        Nota: este precio **NO se muestra** en Inicio, Precio ni FAQ; solo en <b>Programas/Cursos</b> (tarjetas y detalle).
      </div>
    </div>
  </section>

  {{-- IMAGES --}}
  <section class="mt-6 hidden" data-panel="images">
    <div class="grid lg:grid-cols-2 gap-6">
      <div class="p-5 rounded-2xl border border-white/10 bg-white/5">
        <h3 class="font-semibold text-lg">Portada</h3>
        <p class="mt-1 text-white/70 text-sm">Sube una portada. Se mostrará en catálogo y detalle.</p>

        <div class="mt-4">
          <input type="file" name="cover" accept="image/*" class="input w-full" data-cover-input>
          @error('cover') <p class="mt-2 text-red-300 text-sm">{{ $message }}</p> @enderror
        </div>

        <div class="mt-4 rounded-2xl overflow-hidden border border-white/10 bg-black/30">
          <img data-cover-preview
               src="{{ $isEdit && method_exists($course,'coverUrl') ? $course->coverUrl() : '' }}"
               class="w-full h-52 object-cover {{ $isEdit && method_exists($course,'coverUrl') && $course->coverUrl() ? '' : 'hidden' }}">
          <div data-cover-empty class="p-10 text-center text-white/50 {{ $isEdit && method_exists($course,'coverUrl') && $course->coverUrl() ? 'hidden' : '' }}">
            Vista previa de portada
          </div>
        </div>
      </div>

      <div class="p-5 rounded-2xl border border-white/10 bg-white/5">
        <h3 class="font-semibold text-lg">Muestras (múltiples)</h3>
        <p class="mt-1 text-white/70 text-sm">Sube varias. Luego podrás ordenar y eliminar.</p>

        {{-- Si ya tienes CourseImageController con store --}}
        @if($isEdit)
          <div class="mt-4">
            <input type="file" name="samples[]" accept="image/*" multiple class="input w-full">
          </div>

          <div class="mt-4 text-white/60 text-sm">
            (Tip) Para ordenarlas usa los botones ↑ ↓ que aparecen en cada imagen.
          </div>

          {{-- Aquí renderizas las imágenes existentes si ya las tienes en $images --}}
          @isset($images)
            <div class="mt-4 grid grid-cols-2 gap-3">
              @foreach($images as $img)
                <div class="rounded-xl overflow-hidden border border-white/10 bg-black/30">
                  <img class="w-full h-32 object-cover" src="{{ $img->url ?? $img->path ?? '' }}" alt="">
                  <div class="p-2 flex items-center justify-between gap-2">
                    <div class="flex gap-2">
                      <form method="POST" action="{{ route('admin.courses.images.up', [$course, $img]) }}">
                        @csrf
                        <button class="chip" type="submit">↑</button>
                      </form>
                      <form method="POST" action="{{ route('admin.courses.images.down', [$course, $img]) }}">
                        @csrf
                        <button class="chip" type="submit">↓</button>
                      </form>
                    </div>
                    <form method="POST" action="{{ route('admin.courses.images.destroy', [$course, $img]) }}"
                          onsubmit="return confirm('¿Eliminar imagen?');">
                      @csrf
                      @method('DELETE')
                      <button class="chip" type="submit">Eliminar</button>
                    </form>
                  </div>
                </div>
              @endforeach
            </div>
          @endisset
        @else
          <div class="mt-4 text-white/60 text-sm">
            Guarda el curso primero para subir muestras.
          </div>
        @endif
      </div>
    </div>
  </section>

  {{-- CONTENT --}}
  <section class="mt-6 hidden" data-panel="content">
    <div class="grid gap-6">
      <div>
        <label class="block text-sm text-white/80 mb-2">Descripción completa</label>
        <textarea name="description" rows="5" class="input w-full">{{ old('description', $course->description ?? '') }}</textarea>
      </div>

      <div>
        <label class="block text-sm text-white/80 mb-2">Para quién es</label>
        <textarea name="audience" rows="2" class="input w-full">{{ old('audience', $course->audience ?? '') }}</textarea>
      </div>

      <div>
        <label class="block text-sm text-white/80 mb-2">Requisito clave (tal como indicaste)</label>
        <input name="requirement_key" class="input w-full"
               value="{{ old('requirement_key', $course->requirement_key ?? '') }}"
               placeholder="Ej: Conocimientos básicos de electricidad / PC / etc." />
      </div>

      {{-- Listas dinámicas --}}
      @php
        $learns   = old('learns',   $course->learns   ?? []);
        $benefits = old('benefits', $course->benefits ?? []);
        $extras   = old('extras',   $course->extras   ?? []);
      @endphp

      <div class="grid md:grid-cols-3 gap-6">
        <div class="p-5 rounded-2xl border border-white/10 bg-white/5" data-repeater>
          <div class="flex items-center justify-between">
            <h3 class="font-semibold">Qué aprenderás</h3>
            <button type="button" class="chip chip-accent" data-add>+ Agregar</button>
          </div>
          <div class="mt-3 space-y-2" data-list>
            @foreach($learns as $v)
              <div class="flex gap-2">
                <input class="input w-full" name="learns[]" value="{{ $v }}">
                <button type="button" class="chip" data-remove>×</button>
              </div>
            @endforeach
          </div>
        </div>

        <div class="p-5 rounded-2xl border border-white/10 bg-white/5" data-repeater>
          <div class="flex items-center justify-between">
            <h3 class="font-semibold">Beneficios</h3>
            <button type="button" class="chip chip-accent" data-add>+ Agregar</button>
          </div>
          <div class="mt-3 space-y-2" data-list>
            @foreach($benefits as $v)
              <div class="flex gap-2">
                <input class="input w-full" name="benefits[]" value="{{ $v }}">
                <button type="button" class="chip" data-remove>×</button>
              </div>
            @endforeach
          </div>
        </div>

        <div class="p-5 rounded-2xl border border-white/10 bg-white/5" data-repeater>
          <div class="flex items-center justify-between">
            <h3 class="font-semibold">Qué incluye (extras)</h3>
            <button type="button" class="chip chip-accent" data-add>+ Agregar</button>
          </div>
          <div class="mt-3 space-y-2" data-list>
            @foreach($extras as $v)
              <div class="flex gap-2">
                <input class="input w-full" name="extras[]" value="{{ $v }}">
                <button type="button" class="chip" data-remove>×</button>
              </div>
            @endforeach
          </div>
        </div>
      </div>
    </div>
  </section>

  {{-- PUBLISH --}}
  <section class="mt-6 hidden" data-panel="publish">
    <div class="grid md:grid-cols-2 gap-6">
      <div class="p-5 rounded-2xl border border-white/10 bg-white/5">
        <h3 class="font-semibold text-lg">Estado</h3>
        <p class="mt-1 text-white/70 text-sm">Publicado / Borrador</p>

        <label class="mt-4 inline-flex items-center gap-3">
          <input type="checkbox" name="is_published" value="1"
                 class="rounded border-white/20 bg-white/5"
                 @checked(old('is_published', $course->is_published ?? false))>
          <span>Publicado</span>
        </label>
      </div>

      <div class="p-5 rounded-2xl border border-white/10 bg-white/5">
        <h3 class="font-semibold text-lg">Destacado</h3>
        <p class="mt-1 text-white/70 text-sm">Para aparecer en Inicio (Cursos destacados)</p>

        <label class="mt-4 inline-flex items-center gap-3">
          <input type="checkbox" name="is_featured" value="1"
                 class="rounded border-white/20 bg-white/5"
                 @checked(old('is_featured', $course->is_featured ?? false))>
          <span>Marcar como destacado</span>
        </label>
      </div>
    </div>
  </section>
</div>
