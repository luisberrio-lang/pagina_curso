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
      <label class="text-sm text-white/70">Portada</label>
      <input type="file" name="cover" accept="image/*"
             class="w-full rounded-xl bg-black/30 border border-white/10 px-4 py-3 mt-2">
    </div>

    <div>
      <label class="text-sm text-white/70">Precio (Pago único / Acceso de por vida)</label>
      <input name="price_anual" class="w-full rounded-xl bg-black/30 border border-white/10 px-4 py-3 mt-2" placeholder="Ej: 49.90" />
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
