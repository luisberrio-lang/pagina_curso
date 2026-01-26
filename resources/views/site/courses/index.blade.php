@extends('layouts.site')

@section('title', 'Programas/Cursos | Cursos de Ingeniería')

@section('content')
  <section class="flex flex-col md:flex-row md:items-end md:justify-between gap-4">
    <div>
      <h1 class="text-3xl font-extrabold">Programas/Cursos</h1>
      <p class="text-white/75 mt-2">Explora por área y revisa todo antes de escribir.</p>
    </div>

    {{-- Selector de área --}}
    <div class="flex flex-wrap gap-2">
      @foreach($areas as $a)
        <a href="{{ route('courses.byArea', $a) }}"
           class="chip {{ (isset($selected) && $selected && $selected->id === $a->id) ? 'chip-accent' : '' }}">
          {{ $a->name }}
        </a>
      @endforeach
    </div>
  </section>

  {{-- Cabecera del área --}}
  <section class="mt-6 glass p-6 rounded-2xl border border-white/10">
    <div class="flex flex-col items-center text-center gap-2">
      <h2 class="text-2xl font-bold">
        Programas/Cursos de
        <span class="text-cyan-300">{{ $selected?->name ?? 'Cursos' }}</span>
      </h2>
      <p class="text-white/75">{{ $selected?->description ?? '' }}</p>
    </div>
  </section>

  {{-- Listado de cursos --}}
  <section class="mt-6 grid md:grid-cols-3 gap-6">
    @forelse($courses as $c)
      <div class="glass rounded-2xl border border-white/10 overflow-hidden">
        <div class="aspect-[3/2] bg-white/5 flex items-center justify-center">
          @if($c->coverUrl())
            <img class="w-full h-full object-contain"
                 src="{{ $c->coverUrl() }}"
                 width="1536"
                 height="1024"
                 alt="">
          @endif
        </div>

        <div class="p-5">
          <div class="flex items-start justify-between gap-3">
            <h3 class="font-semibold">{{ $c->title }}</h3>
            <span class="area-pill">{{ $c->area->name ?? '' }}</span>
          </div>

          <p class="mt-2 text-white/75">{{ $c->short_desc }}</p>

          {{-- Beneficios clave --}}
          @if(is_array($c->benefits) && count($c->benefits))
            <ul class="mt-3 text-white/75 text-sm list-disc list-inside space-y-1">
              @foreach(array_slice($c->benefits, 0, 4) as $b)
                <li>{{ $b }}</li>
              @endforeach
            </ul>
          @endif

          {{-- ✅ PRECIO ÚNICO --}}
          <div class="mt-4 glass p-4 rounded-xl border border-white/10">
            @if(!is_null($c->price_anual))
              <div class="flex items-center justify-between gap-3">
                <div>
                  <div class="text-white/70 text-sm">Pago único</div>
                  <div class="text-2xl font-extrabold">
                    S/ {{ number_format((float)$c->price_anual, 2) }}
                  </div>
                </div>
                <span class="chip chip-accent">Acceso de por vida</span>
              </div>

              <div class="mt-2 text-white/70 text-sm">
                Incluye actualizaciones y acceso permanente al material.
              </div>
            @else
              <div class="text-white/70">Precio disponible por WhatsApp</div>
              <div class="mt-2 text-white/60 text-sm">Escríbenos y te lo enviamos al instante.</div>
            @endif
          </div>

          <a class="btn btn-accent mt-4 w-full" href="{{ route('courses.show', $c) }}">
            Ver detalles
          </a>
        </div>
      </div>
    @empty
      <p class="text-white/70">No hay cursos publicados en esta área.</p>
    @endforelse
  </section>
@endsection
