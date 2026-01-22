@extends('layouts.site')

@section('content')
  <section class="flex flex-col md:flex-row md:items-end md:justify-between gap-4">
    <div>
      <h1 class="text-3xl font-extrabold">Áreas</h1>
      <p class="mt-2 text-white/70">Gestiona las áreas del catálogo (Programas/Cursos).</p>
    </div>

    <div class="flex gap-2">
      <a href="{{ route('admin.areas.create') }}" class="btn btn-accent">Crear Área</a>
    </div>
  </section>

  @if(session('ok'))
    <div class="mt-6 glass border border-emerald-500/30 bg-emerald-500/10 rounded-2xl p-4 text-emerald-100">
      {{ session('ok') }}
    </div>
  @endif

  <section class="mt-6 glass border border-white/10 rounded-3xl overflow-hidden">
    <div class="overflow-x-auto">
      <table class="min-w-full text-sm">
        <thead class="bg-white/5 text-white/80">
          <tr>
            <th class="text-left px-5 py-4">Nombre</th>
            <th class="text-left px-5 py-4">Slug</th>
            <th class="text-left px-5 py-4">Orden</th>
            <th class="text-left px-5 py-4">Default</th>
            <th class="text-right px-5 py-4">Acciones</th>
          </tr>
        </thead>

        <tbody class="divide-y divide-white/10">
          @forelse($areas as $a)
            <tr class="hover:bg-white/[0.03] transition">
              <td class="px-5 py-4 font-semibold">{{ $a->name }}</td>
              <td class="px-5 py-4 text-white/70">{{ $a->slug }}</td>
              <td class="px-5 py-4 text-white/70">{{ $a->sort_order }}</td>

              <td class="px-5 py-4">
                @if($a->is_default)
                  <span class="chip chip-accent">Default</span>
                @else
                  <span class="chip">No</span>
                @endif
              </td>

              <td class="px-5 py-4">
                <div class="flex justify-end gap-2">
                  @if(!$a->is_default)
                    <form method="POST" action="{{ route('admin.areas.default', $a) }}"
                          onsubmit="return confirm('¿Seguro que quieres marcar esta área como DEFAULT?');">
                      @csrf
                      <button class="chip" type="submit">Hacer default</button>
                    </form>
                  @endif

                  <a class="chip" href="{{ route('admin.areas.edit', $a) }}">Editar</a>

                  <form method="POST" action="{{ route('admin.areas.destroy', $a) }}"
                        onsubmit="return confirm('¿Eliminar esta área?');">
                    @csrf
                    @method('DELETE')
                    <button class="chip" type="submit">Eliminar</button>
                  </form>
                </div>
              </td>
            </tr>
          @empty
            <tr>
              <td class="px-5 py-10 text-white/70" colspan="5">No hay áreas creadas.</td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </section>
@endsection
