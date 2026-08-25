@extends('layouts.site')

@section('title', 'Checkout | '.config('shop.business.name'))
@section('meta_description', 'Datos necesarios para crear una orden pendiente de pago.')

@section('content')
  <h1 class="text-3xl font-extrabold">Checkout</h1>
  <p class="mt-2 text-white/70">Completa tus datos para registrar una orden pendiente. No solicitamos datos de tarjeta en esta pantalla.</p>

  @if(session('error')) <div class="mt-5 glass p-4 rounded-xl text-red-200">{{ session('error') }}</div> @endif

  <div class="mt-8 grid lg:grid-cols-[1fr,22rem] gap-6 items-start">
    <form class="glass p-6 rounded-3xl border border-white/10 grid gap-5" method="POST" action="{{ route('checkout.store') }}">
      @csrf
      <input type="hidden" name="checkout_token" value="{{ $checkoutToken }}">

      <div class="grid md:grid-cols-2 gap-5">
        <div><label for="first_name" class="block mb-2">Nombres</label><input id="first_name" name="first_name" class="input w-full" value="{{ old('first_name', auth()->user()?->name) }}" required>@error('first_name')<p class="text-red-300 text-sm mt-1">{{ $message }}</p>@enderror</div>
        <div><label for="last_name" class="block mb-2">Apellidos</label><input id="last_name" name="last_name" class="input w-full" value="{{ old('last_name') }}" required>@error('last_name')<p class="text-red-300 text-sm mt-1">{{ $message }}</p>@enderror</div>
        <div><label for="email" class="block mb-2">Correo</label><input id="email" name="email" type="email" class="input w-full" value="{{ old('email', auth()->user()?->email) }}" required>@error('email')<p class="text-red-300 text-sm mt-1">{{ $message }}</p>@enderror</div>
        <div><label for="phone" class="block mb-2">Teléfono</label><input id="phone" name="phone" class="input w-full" value="{{ old('phone') }}" required>@error('phone')<p class="text-red-300 text-sm mt-1">{{ $message }}</p>@enderror</div>
        <div><label for="document_type" class="block mb-2">Tipo de documento (opcional)</label><select id="document_type" name="document_type" class="input w-full"><option value="">Seleccionar</option>@foreach(['DNI','CE','RUC','PASAPORTE'] as $type)<option value="{{ $type }}" @selected(old('document_type') === $type)>{{ $type }}</option>@endforeach</select>@error('document_type')<p class="text-red-300 text-sm mt-1">{{ $message }}</p>@enderror</div>
        <div><label for="document_number" class="block mb-2">Número de documento</label><input id="document_number" name="document_number" class="input w-full" value="{{ old('document_number') }}">@error('document_number')<p class="text-red-300 text-sm mt-1">{{ $message }}</p>@enderror</div>
      </div>

      <div>
        <label class="flex items-start gap-3 text-sm text-white/80" for="terms_accepted">
          <input id="terms_accepted" name="terms_accepted" type="checkbox" value="1" class="mt-1" required @checked(old('terms_accepted'))>
          <span>Confirmo que he leído y acepto los <a class="text-cyan-300 underline" href="{{ route('legal.terms') }}" target="_blank" rel="noopener">términos y condiciones</a> y la <a class="text-cyan-300 underline" href="{{ route('legal.privacy') }}" target="_blank" rel="noopener">política de privacidad</a>.</span>
        </label>
        @error('terms_accepted')<p class="text-red-300 text-sm mt-2">Debes aceptar los términos y la política de privacidad para crear la orden.</p>@enderror
      </div>

      <button class="btn btn-accent" type="submit">Crear orden pendiente</button>
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
