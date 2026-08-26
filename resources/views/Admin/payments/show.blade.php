@extends('layouts.site')

@section('title', 'Pago '.$payment->payment_number.' | '.config('shop.business.name'))

@section('content')
  <section class="flex flex-wrap items-end justify-between gap-4"><div><p class="text-white/60">Pago</p><h1 class="text-3xl font-extrabold">{{ $payment->payment_number }}</h1></div><a class="btn btn-ghost" href="{{ route('admin.payments.index') }}">Volver a pagos</a></section>
  <div class="mt-6 grid lg:grid-cols-2 gap-6">
    <section class="glass p-6 rounded-2xl border border-white/10"><h2 class="text-xl font-semibold">Intento</h2><dl class="mt-4 space-y-2"><div><dt class="text-white/50">Orden</dt><dd><a class="text-cyan-300" href="{{ route('admin.orders.show', $payment->order) }}">{{ $payment->order->order_number }}</a></dd></div><div><dt class="text-white/50">Proveedor</dt><dd>{{ $payment->provider }}</dd></div><div><dt class="text-white/50">Transacción</dt><dd>{{ $payment->provider_transaction_id }}</dd></div><div><dt class="text-white/50">Referencia</dt><dd>{{ $payment->provider_reference ?: '—' }}</dd></div></dl></section>
    <section class="glass p-6 rounded-2xl border border-white/10"><h2 class="text-xl font-semibold">Estado</h2><dl class="mt-4 space-y-2"><div class="flex justify-between"><dt>Estado</dt><dd>{{ $payment->status }}</dd></div><div class="flex justify-between"><dt>Monto base</dt><dd>{{ \App\Support\Money::format($payment->base_amount, $payment->base_currency) }}</dd></div><div class="flex justify-between"><dt>Código proveedor</dt><dd>{{ $payment->provider_code ?: '—' }}</dd></div><div class="flex justify-between"><dt>Pagado</dt><dd>{{ $payment->paid_at?->format('d/m/Y H:i') ?: '—' }}</dd></div></dl></section>
  </div>
  <section class="mt-6 glass p-6 rounded-2xl border border-white/10"><h2 class="text-xl font-semibold">Eventos sanitizados</h2><div class="mt-4 space-y-3">@forelse($payment->events as $event)<div class="border-t border-white/10 pt-3 flex flex-wrap justify-between gap-3"><span>{{ $event->provider_code ?: 'sin código' }} · {{ $event->processing_status }}</span><span class="text-white/60">{{ $event->created_at->format('d/m/Y H:i:s') }}</span></div>@empty<p class="text-white/60">Sin eventos registrados.</p>@endforelse</div></section>
@endsection
