@extends('layouts.site')

@section('title', 'Carrito | '.config('shop.business.name'))
@section('meta_description', 'Cursos seleccionados para continuar al checkout.')

@section('content')
  <section class="flex flex-wrap items-end justify-between gap-4">
    <div>
      <h1 class="text-3xl font-extrabold">Carrito</h1>
      <p class="mt-2 text-white/70">Cada curso digital se agrega una sola vez.</p>
    </div>
    <a class="btn btn-ghost" href="{{ route('courses.index') }}">Continuar comprando</a>
  </section>

  @if(session('ok')) <div class="mt-5 glass p-4 rounded-xl text-emerald-200">{{ session('ok') }}</div> @endif
  @if(session('error')) <div class="mt-5 glass p-4 rounded-xl text-red-200">{{ session('error') }}</div> @endif

  @if($cart['items'] === [])
    <div class="mt-8 glass p-8 rounded-3xl border border-white/10 text-center">
      <h2 class="text-xl font-semibold">Tu carrito está vacío</h2>
      <a class="btn btn-accent mt-5" href="{{ route('courses.index') }}">Ver cursos</a>
    </div>
  @else
    <div class="mt-8 grid lg:grid-cols-[1fr,22rem] gap-6 items-start">
      <div class="space-y-4">
        @foreach($cart['items'] as $item)
          @php($course = $item['course'])
          <article class="glass p-5 rounded-2xl border border-white/10 flex flex-col sm:flex-row gap-5">
            <div class="h-28 sm:w-44 rounded-xl overflow-hidden bg-white/5 shrink-0">
              @if($course->coverUrl())
                <img class="h-full w-full object-cover" src="{{ $course->coverUrl() }}" alt="Portada de {{ $course->title }}">
              @endif
            </div>
            <div class="flex-1">
              <h2 class="text-lg font-semibold">{{ $course->title }}</h2>
              <p class="mt-2 text-white/65">Cantidad: 1</p>
              <p class="mt-2 text-xl font-bold">{{ \App\Support\Money::format($item['line_total'], $item['currency']) }}</p>
            </div>
            <form method="POST" action="{{ route('cart.destroy', $course) }}">
              @csrf @method('DELETE')
              <button class="chip" type="submit">Eliminar</button>
            </form>
          </article>
        @endforeach
      </div>

      <aside class="glass p-6 rounded-2xl border border-white/10">
        <h2 class="text-xl font-semibold">Resumen</h2>
        <div class="mt-4 flex justify-between"><span>Subtotal</span><strong>{{ $cart['formatted_total'] }}</strong></div>
        <div class="mt-3 flex justify-between text-lg"><span>Total</span><strong>{{ $cart['formatted_total'] }}</strong></div>
        <p class="mt-2 text-xs text-white/60">Moneda: {{ $cart['currency'] }}</p>
        <a class="btn btn-accent mt-6 w-full" href="{{ route('checkout.create') }}">Proceder al checkout</a>
        <form class="mt-3" method="POST" action="{{ route('cart.clear') }}">
          @csrf @method('DELETE')
          <button class="btn btn-ghost w-full" type="submit">Vaciar carrito</button>
        </form>
      </aside>
    </div>
  @endif
@endsection
