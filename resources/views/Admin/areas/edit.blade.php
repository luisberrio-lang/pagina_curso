@extends('layouts.site')

@section('content')
  <section class="glass p-8 rounded-3xl border border-white/10">
    <div class="flex items-end justify-between gap-4">
      <div>
        <h1 class="text-3xl font-extrabold">Editar Área</h1>
        <p class="mt-2 text-white/70">Actualiza la información del área.</p>
      </div>
      <a href="{{ route('admin.areas.index') }}" class="btn btn-ghost">Volver</a>
    </div>

    @if ($errors->any())
      <div class="mt-6 glass border border-red-500/30 bg-red-500/10 rounded-2xl p-4 text-red-100">
        <b>Corrige estos errores:</b>
        <ul class="mt-2 list-disc list-inside text-sm">
          @foreach ($errors->all() as $e) <li>{{ $e }}</li> @endforeach
        </ul>
      </div>
    @endif

    <form class="mt-6 grid md:grid-cols-2 gap-6" method="POST" action="{{ route('admin.areas.update', $area) }}">
      @csrf
      @method('PUT')

      <div class="md:col-span-2">
        <label class="text-sm text-white/70">Nombre</label>
        <input name="name" value="{{ old('name', $area->name) }}"
               class="mt-2 w-full rounded-2xl bg-white/5 border border-white/10 px-4 py-3 outline-none" required>
      </div>

      <div>
        <label class="text-sm text-white/70">Slug</label>
        <input name="slug" value="{{ old('slug', $area->slug) }}"
               class="mt-2 w-full rounded-2xl bg-white/5 border border-white/10 px-4 py-3 outline-none" required>
      </div>

      <div>
        <label class="text-sm text-white/70">Orden</label>
        <input name="sort_order" type="number" value="{{ old('sort_order', $area->sort_order) }}"
               class="mt-2 w-full rounded-2xl bg-white/5 border border-white/10 px-4 py-3 outline-none">
      </div>

      <div class="md:col-span-2">
        <label class="text-sm text-white/70">Descripción (opcional)</label>
        <textarea name="description" rows="3"
                  class="mt-2 w-full rounded-2xl bg-white/5 border border-white/10 px-4 py-3 outline-none">{{ old('description', $area->description) }}</textarea>
      </div>

      <div class="md:col-span-2 flex items-center gap-3">
        <input id="is_default" name="is_default" type="checkbox" value="1"
               class="rounded border-white/20 bg-white/5"
               {{ old('is_default', $area->is_default) ? 'checked' : '' }}>
        <label for="is_default" class="text-white/80">Marcar como área por defecto</label>
      </div>

      <div class="md:col-span-2 flex flex-wrap gap-3">
        <button class="btn btn-accent" type="submit">Guardar cambios</button>
        <a class="btn btn-ghost" href="{{ route('admin.areas.index') }}">Cancelar</a>
      </div>
    </form>
  </section>
@endsection
