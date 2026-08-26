@extends('layouts.site')

@section('title', 'Orden '.$order->order_number.' | '.config('shop.business.name'))

@section('content')
  <section class="flex flex-wrap items-end justify-between gap-4">
    <div><p class="text-white/60">Orden</p><h1 class="text-3xl font-extrabold">{{ $order->order_number }}</h1></div>
    <a class="btn btn-ghost" href="{{ route('admin.orders.index') }}">Volver a órdenes</a>
  </section>

  <div class="mt-6 grid lg:grid-cols-2 gap-6">
    <section class="glass p-6 rounded-2xl border border-white/10"><h2 class="text-xl font-semibold">Comprador</h2><dl class="mt-4 space-y-2 text-white/75"><div><dt class="text-white/50">Nombres completos</dt><dd>{{ $order->customerFullName() }}</dd></div><div><dt class="text-white/50">Correo</dt><dd>{{ $order->email }}</dd></div><div><dt class="text-white/50">Celular</dt><dd>{{ $order->phone }}</dd></div>@if(filled($order->document_type) && filled($order->document_number))<div><dt class="text-white/50">Documento histórico</dt><dd>{{ $order->document_type }} · {{ $order->document_number }}</dd></div>@endif</dl></section>
    <section class="glass p-6 rounded-2xl border border-white/10"><h2 class="text-xl font-semibold">Estado</h2><dl class="mt-4 space-y-2"><div class="flex justify-between"><dt>Orden</dt><dd>{{ $order->status }}</dd></div><div class="flex justify-between"><dt>Pago</dt><dd>{{ $order->payment_status }}</dd></div><div class="flex justify-between"><dt>Moneda</dt><dd>{{ $order->currency }}</dd></div><div class="flex justify-between"><dt>Total</dt><dd class="font-bold">{{ \App\Support\Money::format($order->total, $order->currency) }}</dd></div></dl></section>
  </div>

  <section class="mt-6 glass p-6 rounded-2xl border border-white/10"><h2 class="text-xl font-semibold">Productos congelados</h2><div class="mt-4 space-y-4">@foreach($order->items as $item)<div class="flex justify-between gap-4 border-t border-white/10 pt-4"><div><div class="font-semibold">{{ $item->course_title }}</div><div class="text-sm text-white/60">{{ $item->course_slug }} · cantidad {{ $item->quantity }}</div></div><div class="text-right"><div>{{ \App\Support\Money::format($item->unit_price, $item->currency) }}</div><strong>{{ \App\Support\Money::format($item->line_total, $item->currency) }}</strong></div></div>@endforeach</div></section>
@endsection
