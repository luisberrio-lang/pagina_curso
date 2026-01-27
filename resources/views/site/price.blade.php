@extends('layouts.site')

@section('title', 'Precios | Cursos de Ingeniería')

@section('content')
  <section class="flex flex-col md:flex-row md:items-end md:justify-between gap-4">
    <div>
      <h1 class="text-3xl font-extrabold">Precios</h1>
      <p class="text-white/75 mt-2">Precios por curso, según área.</p>
    </div>

    <div class="flex flex-wrap gap-2">
      @foreach($areas as $a)
        <a class="chip {{ ($selected && $selected->id === $a->id) ? 'chip-accent' : '' }}"
           href="{{ route('price', ['area' => $a->slug]) }}">
          {{ $a->name }}
        </a>
      @endforeach
    </div>
  </section>

  {{-- Cabecera del área --}}
  <section class="mt-6 glass p-6 rounded-2xl border border-white/10">
    <div class="flex flex-col items-center text-center gap-2">
      <h2 class="text-2xl font-bold">
        Precios de
        <span class="text-cyan-300">{{ $selected?->name ?? 'Cursos' }}</span>
      </h2>
      <p class="text-white/75">{{ $selected?->description ?? '' }}</p>
    </div>
  </section>

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

          {{-- Pago único --}}
          <div class="mt-4 glass p-4 rounded-xl border border-white/10">
            <div class="flex items-center justify-between gap-3">
              <div class="text-white/70 text-sm">Pago único</div>
              <span class="chip chip-accent">Acceso de por vida</span>
            </div>
            @if(!is_null($c->price_anual))
              <div class="text-2xl font-extrabold">
                S/ {{ number_format((float)$c->price_anual, 2) }}
              </div>
              @if(!is_null($c->price_previous))
                @php
                  $prev = (float) $c->price_previous;
                  $curr = (float) $c->price_anual;
                  $discount = $prev > 0 && $curr > 0 && $curr < $prev
                    ? round((1 - ($curr / $prev)) * 100)
                    : null;
                @endphp
                <div class="mt-2 flex items-center gap-2">
                  <span class="price-old">S/ {{ number_format($prev, 2) }}</span>
                  @if($discount)
                    <span class="discount-badge">{{ $discount }}% DSCTO</span>
                  @endif
                </div>
              @endif
              <div class="mt-2 text-white/70 text-sm">Incluye actualizaciones y acceso permanente.</div>
            @else
              <div class="text-white/80">Consultar precio</div>
              <div class="mt-2 text-white/60 text-sm">Disponible por WhatsApp.</div>
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
