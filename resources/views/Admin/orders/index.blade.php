@extends('layouts.site')

@section('title', 'Órdenes | Administración')

@section('content')
  <section class="flex flex-wrap items-end justify-between gap-4">
    <div><h1 class="text-3xl font-extrabold">Órdenes</h1><p class="mt-2 text-white/70">Consulta de órdenes y estados. Sin acciones de pago.</p></div>
    <a class="btn btn-ghost" href="{{ route('admin.dashboard') }}">Volver al dashboard</a>
  </section>

  <form class="mt-6 flex gap-3" method="GET" action="{{ route('admin.orders.index') }}">
    <label class="sr-only" for="search">Buscar orden o correo</label>
    <input id="search" name="search" class="input flex-1" value="{{ $search }}" placeholder="Número de orden o correo">
    <button class="btn btn-accent" type="submit">Buscar</button>
  </form>

  <div class="mt-6 glass rounded-2xl border border-white/10 overflow-x-auto">
    <table class="w-full text-sm">
      <thead class="bg-white/5 text-left"><tr><th class="p-4">Orden</th><th class="p-4">Comprador</th><th class="p-4">Total</th><th class="p-4">Orden</th><th class="p-4">Pago</th><th class="p-4">Fecha</th><th class="p-4"></th></tr></thead>
      <tbody>
        @forelse($orders as $order)
          <tr class="border-t border-white/10"><td class="p-4 font-semibold">{{ $order->order_number }}</td><td class="p-4"><div>{{ $order->customerFullName() }}</div><div class="text-white/60">{{ $order->email }}</div><div class="text-white/60">{{ $order->phone }}</div></td><td class="p-4">{{ \App\Support\Money::format($order->total, $order->currency) }}</td><td class="p-4">{{ $order->status }}</td><td class="p-4">{{ $order->payment_status }}</td><td class="p-4">{{ $order->created_at->format('d/m/Y H:i') }}</td><td class="p-4"><a class="chip" href="{{ route('admin.orders.show', $order) }}">Ver</a></td></tr>
        @empty
          <tr><td class="p-6 text-white/60" colspan="7">No hay órdenes registradas.</td></tr>
        @endforelse
      </tbody>
    </table>
  </div>
  <div class="mt-6">{{ $orders->links() }}</div>
@endsection
