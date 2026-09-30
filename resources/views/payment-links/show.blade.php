@extends('layouts.site')

@section('title', 'Pago personalizado | '.config('shop.business.name'))

@section('content')
  <section class="mx-auto max-w-2xl glass rounded-3xl p-6 md:p-8">
    <p class="text-sm uppercase tracking-widest text-cyan-200">Pago personalizado</p>
    <h1 class="mt-2 text-3xl font-extrabold">{{ $paymentLink->concept }}</h1>

    <dl class="mt-8 grid gap-5">
      <div><dt class="text-sm text-white/60">Cliente</dt><dd class="mt-1 text-lg font-semibold">{{ $paymentLink->customer_name }}</dd></div>
      <div><dt class="text-sm text-white/60">Concepto</dt><dd class="mt-1">{{ $paymentLink->concept }}</dd></div>
      <div class="rounded-2xl border border-cyan-300/30 bg-cyan-300/10 p-5"><dt class="text-sm text-cyan-100">Monto</dt><dd class="mt-1 text-4xl font-extrabold">{{ \App\Support\Money::format($paymentLink->amount, $paymentLink->currency) }}</dd></div>
      <div><dt class="text-sm text-white/60">Estado</dt><dd class="mt-1 font-semibold">{{ $paymentLink->statusLabel() }}</dd></div>
    </dl>

    <p class="mt-8 rounded-2xl border border-white/10 bg-white/5 p-4 text-white/80">El monto fue definido previamente y no puede modificarse.</p>

    @if($paymentLink->status === \App\Models\PaymentLink::STATUS_EXPIRED)
      <div class="mt-6 rounded-2xl border border-amber-500/30 bg-amber-500/10 p-4 text-amber-100">Enlace expirado.</div>
    @elseif($paymentLink->status === \App\Models\PaymentLink::STATUS_CANCELLED)
      <div class="mt-6 rounded-2xl border border-red-500/30 bg-red-500/10 p-4 text-red-100">Este enlace fue cancelado.</div>
    @elseif($paymentLink->status === \App\Models\PaymentLink::STATUS_PAID)
      <div class="mt-6 rounded-2xl border border-emerald-500/30 bg-emerald-500/10 p-4 text-emerald-100">Pago confirmado.</div>
    @else
      <div class="mt-6 rounded-2xl border border-cyan-500/30 bg-cyan-500/10 p-4 text-cyan-100">La pasarela de pago se encuentra en proceso de habilitación. Pago en línea temporalmente no disponible.</div>
    @endif
  </section>
@endsection
