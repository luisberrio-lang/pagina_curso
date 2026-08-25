@extends('layouts.site')

@section('title', 'Orden '.$order->order_number.' | '.config('shop.business.name'))
@section('meta_description', 'Confirmación de orden pendiente de pago.')

@section('content')
  <section class="glass p-6 md:p-8 rounded-3xl border border-white/10 max-w-4xl mx-auto">
    <div class="flex flex-wrap items-start justify-between gap-4">
      <div><p class="text-cyan-300 font-semibold">Orden creada</p><h1 class="text-3xl font-extrabold mt-1">{{ $order->order_number }}</h1></div>
      <span class="chip">Pendiente de pago</span>
    </div>
    <p class="mt-5 text-white/75">La orden fue registrada, pero todavía no existe un pago aprobado.</p>

    <div class="mt-8 space-y-4">
      @foreach($order->items as $item)
        <div class="flex justify-between gap-4 border-b border-white/10 pb-4"><div><div class="font-semibold">{{ $item->course_title }}</div><div class="text-sm text-white/60">Cantidad: {{ $item->quantity }}</div></div><strong>{{ \App\Support\Money::format($item->line_total, $item->currency) }}</strong></div>
      @endforeach
    </div>

    <div class="mt-6 flex justify-between text-xl"><span>Total</span><strong>{{ \App\Support\Money::format($order->total, $order->currency) }}</strong></div>
    <div class="mt-6 text-sm text-white/65"><p>Comprador: {{ $order->publicCustomerName() }}</p><p>Correo: {{ $order->maskedEmail() }}</p></div>
    @unless(config('services.izipay.payments_enabled'))
      <p class="mt-5 text-sm text-amber-200">Medio de pago temporalmente en proceso de habilitación. Tu orden permanecerá pendiente hasta que exista un medio de pago disponible.</p>
    @endunless
    <a class="btn btn-ghost mt-8" href="{{ route('courses.index') }}">Volver al catálogo</a>
  </section>
@endsection
