@extends('layouts.site')

@section('content')
  <section class="flex flex-col md:flex-row md:items-end md:justify-between gap-4">
    <div>
      <h1 class="text-3xl font-extrabold">Precio</h1>
      <p class="text-white/75 mt-2">Planes por área.</p>
    </div>

    <div class="flex flex-wrap gap-2">
      @foreach($areas as $a)
        <a class="chip {{ $selected->id === $a->id ? 'chip-accent' : '' }}"
           href="{{ route('price', ['area' => $a->slug]) }}">
          {{ $a->name }}
        </a>
      @endforeach
    </div>
  </section>

  <section class="mt-6 grid md:grid-cols-3 gap-6">
    @forelse($plans as $p)
      @php
        $msg = "Hola, me interesa el plan {$p['name']} del área {$selected->name}. ¿Me brinda información para adquirirlo?";
        $wa = 'https://wa.me/'.env('WHATSAPP_NUMBER').'?text='.urlencode($msg);
      @endphp

      <div class="glass p-6 rounded-2xl border border-white/10">
        <h2 class="text-xl font-bold">{{ $p['name'] }}</h2>
        <div class="mt-2 text-3xl font-extrabold text-cyan-300">{{ $p['price'] }}</div>
        <ul class="mt-4 space-y-2 text-white/80 list-disc list-inside">
          @foreach($p['features'] as $f)
            <li>{{ $f }}</li>
          @endforeach
        </ul>
        <a class="btn btn-accent mt-5 w-full" target="_blank" href="{{ $wa }}">Adquirir por WhatsApp</a>
      </div>
    @empty
      <p class="text-white/70">No hay planes configurados para esta área.</p>
    @endforelse
  </section>

  <section class="mt-10 text-center text-white/80">
    <p>Para adquirir, escríbenos por WhatsApp.</p>
    <a class="btn btn-ghost mt-4" target="_blank"
       href="https://wa.me/{{ env('WHATSAPP_NUMBER') }}?text={{ urlencode('Hola, deseo adquirir un plan. ¿Me brinda información?') }}">
      WhatsApp
    </a>
  </section>
@endsection
