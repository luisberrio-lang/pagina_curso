@extends('layouts.site')

@section('title', 'Checkout | '.config('shop.business.name'))
@section('meta_description', 'Completa tus datos para continuar con el pedido de tus cursos digitales.')

@section('content')
  <h1 class="text-3xl font-extrabold">Checkout</h1>
  <p class="mt-2 text-white/70">Completa tus datos para continuar con tu pedido.</p>

  @if(session('error')) <div class="mt-5 glass p-4 rounded-xl text-red-200">{{ session('error') }}</div> @endif

  <div class="mt-8 grid lg:grid-cols-[1fr,22rem] gap-6 items-start">
    <form class="glass p-6 rounded-3xl border border-white/10 grid gap-5" method="POST" action="{{ route('checkout.store') }}">
      @csrf
      <input type="hidden" name="checkout_token" value="{{ $checkoutToken }}">

      <div class="grid gap-5">
        <div><label for="full_name" class="block mb-2">Nombres completos</label><input id="full_name" name="full_name" class="input w-full" autocomplete="name" value="{{ old('full_name', $buyerDefaults['full_name']) }}" required>@error('full_name')<p class="text-red-300 text-sm mt-1">{{ $message }}</p>@enderror</div>
        <div><label for="email" class="block mb-2">Correo</label><input id="email" name="email" type="email" autocomplete="email" class="input w-full" value="{{ old('email', $buyerDefaults['email']) }}" required>@error('email')<p class="text-red-300 text-sm mt-1">{{ $message }}</p>@enderror</div>
        <div><label for="phone" class="block mb-2">Celular</label><input id="phone" name="phone" type="tel" autocomplete="tel" class="input w-full" value="{{ old('phone', $buyerDefaults['phone']) }}" required>@error('phone')<p class="text-red-300 text-sm mt-1">{{ $message }}</p>@enderror</div>
      </div>

      <div>
        <label class="flex items-start gap-3 text-sm text-white/80" for="terms_accepted">
          <input id="terms_accepted" name="terms_accepted" type="checkbox" value="1" class="mt-1" required @checked(old('terms_accepted'))>
          <span>Confirmo que he leído y acepto los <a class="text-cyan-300 underline" href="{{ route('legal.terms') }}" target="_blank" rel="noopener">términos y condiciones</a> y la <a class="text-cyan-300 underline" href="{{ route('legal.privacy') }}" target="_blank" rel="noopener">política de privacidad</a>.</span>
        </label>
        @error('terms_accepted')<p class="text-red-300 text-sm mt-2">Debes aceptar los términos y la política de privacidad para crear la orden.</p>@enderror
      </div>

      <button class="btn btn-accent" type="submit">Confirmar pedido</button>
    </form>

    <aside class="glass p-6 rounded-2xl border border-white/10">
      <h2 class="text-xl font-semibold">Resumen</h2>
      <ul class="mt-4 space-y-3">
        @foreach($cart['items'] as $item)
          <li class="flex justify-between gap-4"><span>{{ $item['course']->title }}</span><strong>{{ \App\Support\Money::format($item['line_total'], $item['currency']) }}</strong></li>
        @endforeach
      </ul>
      <div class="mt-5 pt-4 border-t border-white/10 flex justify-between text-lg"><span>Total</span><strong>{{ $cart['formatted_total'] }}</strong></div>
      <p class="mt-2 text-xs text-white/60">{{ $cart['currency'] }} · calculado por el servidor</p>
    </aside>
  </div>
@endsection
