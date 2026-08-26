@extends('layouts.site')

@section('title', 'Pagos | '.config('shop.business.name'))

@section('content')
  <section class="flex flex-wrap items-end justify-between gap-4">
    <div><h1 class="text-3xl font-extrabold">Pagos</h1><p class="mt-2 text-white/70">Intentos y eventos del proveedor. Vista de solo lectura.</p></div>
    <a class="btn btn-ghost" href="{{ route('admin.dashboard') }}">Volver al dashboard</a>
  </section>
  <form class="mt-6 flex gap-3" method="GET" action="{{ route('admin.payments.index') }}">
    <label class="sr-only" for="search">Buscar pago</label><input id="search" name="search" class="input flex-1" maxlength="100" value="{{ $search }}" placeholder="Pago, transacción u orden"><button class="btn btn-accent" type="submit">Buscar</button>
  </form>
  <div class="mt-6 glass rounded-2xl border border-white/10 overflow-x-auto"><table class="w-full text-sm">
    <thead class="bg-white/5 text-left"><tr><th class="p-4">Pago</th><th class="p-4">Orden</th><th class="p-4">Base</th><th class="p-4">Estado</th><th class="p-4">Intento</th><th class="p-4">Fecha</th><th class="p-4"></th></tr></thead>
    <tbody>@forelse($payments as $payment)<tr class="border-t border-white/10"><td class="p-4 font-semibold">{{ $payment->payment_number }}</td><td class="p-4">{{ $payment->order->order_number }}</td><td class="p-4">{{ \App\Support\Money::format($payment->base_amount, $payment->base_currency) }}</td><td class="p-4">{{ $payment->status }}</td><td class="p-4">{{ $payment->attempt }}</td><td class="p-4">{{ $payment->created_at->format('d/m/Y H:i') }}</td><td class="p-4"><a class="chip" href="{{ route('admin.payments.show', $payment) }}">Ver</a></td></tr>@empty<tr><td class="p-6 text-white/60" colspan="7">No hay intentos de pago registrados.</td></tr>@endforelse</tbody>
  </table></div><div class="mt-6">{{ $payments->links() }}</div>
@endsection
