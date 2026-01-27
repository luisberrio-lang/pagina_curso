@extends('layouts.site')

@section('title', 'Cursos | Cursos de Ingeniería')

@section('content')
  <section class="flex items-end justify-between gap-4">
    <div>
      <h1 class="text-3xl font-extrabold">Cursos</h1>
      <p class="mt-2 text-white/70">Listado general (admin).</p>
    </div>
    <a href="{{ route('admin.courses.create') }}" class="btn btn-accent">Crear curso</a>
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
            <th class="text-left px-5 py-4">Curso</th>
            <th class="text-left px-5 py-4">Área</th>
            <th class="text-left px-5 py-4">Estado</th>
            <th class="text-left px-5 py-4">Precio</th>
            <th class="text-right px-5 py-4">Acciones</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-white/10">
          @forelse($courses as $c)
            <tr>
              <td class="px-5 py-4 font-semibold">{{ $c->title }}</td>
              <td class="px-5 py-4 text-white/70">{{ $c->area?->name }}</td>
              <td class="px-5 py-4">
                @if($c->is_published)
                  <span class="chip chip-accent">Publicado</span>
                @else
                  <span class="chip">Borrador</span>
                @endif
              </td>
              <td class="px-5 py-4 text-white/70">
                {{ $c->price_anual !== null ? number_format((float)$c->price_anual,2) : '—' }}
                <span class="text-white/50">(Pago único)</span>
              </td>
              <td class="px-5 py-4">
                <div class="flex justify-end gap-2">
                  <a class="chip" href="{{ route('admin.courses.edit', $c) }}">Editar</a>
                  <form method="POST" action="{{ route('admin.courses.destroy', $c) }}"
                        onsubmit="return confirm('¿Eliminar este curso?');">
                    @csrf
                    @method('DELETE')
                    <button class="chip" type="submit">Eliminar</button>
                  </form>
                </div>
              </td>
            </tr>
          @empty
            <tr><td class="px-5 py-10 text-white/70" colspan="5">No hay cursos.</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </section>

  <div class="mt-6">
    {{ $courses->links() }}
  </div>
@endsection
