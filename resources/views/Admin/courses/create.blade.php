@extends('layouts.site')

@section('content')
  <h1 class="text-3xl font-extrabold">Crear curso</h1>

  <form class="mt-6 grid gap-6" method="POST" action="{{ route('admin.courses.store') }}" enctype="multipart/form-data">
    @csrf

    <div>
      <label class="text-sm text-white/70">Área</label>
      <select name="area_id" class="w-full rounded-xl bg-black/30 border border-white/10 px-4 py-3 mt-2">
        @foreach($areas as $a)
          <option value="{{ $a->id }}">{{ $a->name }}</option>
        @endforeach
      </select>
    </div>

    <div class="grid md:grid-cols-2 gap-6">
      <div>
        <label class="text-sm text-white/70">Título</label>
        <input name="title" class="w-full rounded-xl bg-black/30 border border-white/10 px-4 py-3 mt-2" />
      </div>
      <div>
        <label class="text-sm text-white/70">Descripción corta</label>
        <input name="short_desc" class="w-full rounded-xl bg-black/30 border border-white/10 px-4 py-3 mt-2" />
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
    </div>

    <div class="grid md:grid-cols-2 gap-6">
      <div>
        <label class="text-sm text-white/70">Para quién es</label>
        <textarea name="audience" rows="3" class="w-full rounded-xl bg-black/30 border border-white/10 px-4 py-3 mt-2">{{ old('audience') }}</textarea>
      </div>
      <div>
        <label class="text-sm text-white/70">Qué aprenderás (1 por línea)</label>
        <textarea name="learning" rows="3" class="w-full rounded-xl bg-black/30 border border-white/10 px-4 py-3 mt-2"
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
    <div>
      <label class="text-sm text-white/70">Portada</label>

      <input type="file" name="cover" accept="image/*"
             class="w-full rounded-xl bg-black/30 border border-white/10 px-4 py-3 mt-2">
    </div>

    <div class="p-5 rounded-2xl border border-white/10 bg-white/5">
      <h3 class="font-semibold text-lg">Precios y Descuento</h3>
      <p class="mt-1 text-white/60 text-sm">El % DSCTO se calcula automáticamente.</p>

      <div class="mt-4 grid md:grid-cols-3 gap-6 items-end" data-discount-calc>
        <div>
          <label class="text-sm text-white/70">Precio actual (Pago único / Acceso de por vida)</label>
          <input name="price_anual"
                 class="w-full rounded-xl bg-black/30 border border-white/10 px-4 py-3 mt-2"
                 placeholder="Ej: 49.90" data-price-current />
        </div>

        <div>
          <label class="text-sm text-white/70">Precio anterior</label>
          <input name="price_previous"
                 class="w-full rounded-xl bg-black/30 border border-white/10 px-4 py-3 mt-2"
                 placeholder="Ej: 79.90" data-price-previous />
        </div>

        <div>
          <label class="text-sm text-white/70">% DSCTO (automático)</label>
          <div class="mt-2 px-4 py-3 rounded-xl border border-white/10 bg-black/30 text-center font-semibold"
               data-discount-output>—</div>
        </div>
      </div>
    </div>

    <div class="flex gap-3">
      <label class="inline-flex items-center gap-2 text-sm text-white/75">
        <input type="checkbox" name="is_published" value="1"> Publicado
      </label>
      <label class="inline-flex items-center gap-2 text-sm text-white/75">
        <input type="checkbox" name="is_featured" value="1"> Destacado
      </label>
    </div>

    <div class="flex gap-3">
      <button class="btn btn-accent" type="submit">Guardar</button>
      <a class="btn btn-ghost" href="{{ route('admin.courses.index') }}">Cancelar</a>
    </div>
  </form>
@endsection


