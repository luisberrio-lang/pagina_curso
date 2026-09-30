@extends('layouts.site')

@section('title', 'Enlaces de pago | '.config('shop.business.name'))

@section('content')
  <section class="flex flex-wrap items-end justify-between gap-4">
    <div>
      <h1 class="text-3xl font-extrabold">Enlaces de pago</h1>
      <p class="mt-2 text-white/70">Crea enlaces con montos definidos exclusivamente desde el panel administrativo.</p>
    </div>
    <div class="flex flex-wrap gap-3">
      <a class="btn btn-ghost" href="{{ route('admin.dashboard') }}">Volver al dashboard</a>
      <a class="btn btn-accent" href="{{ route('admin.payment-links.create') }}">Crear enlace de pago</a>
    </div>
  </section>

  @if(session('ok'))
    <div class="mt-6 glass rounded-2xl border border-emerald-500/30 bg-emerald-500/10 p-4 text-emerald-100">{{ session('ok') }}</div>
  @endif
  @error('payment_link')
    <div class="mt-6 glass rounded-2xl border border-red-500/30 bg-red-500/10 p-4 text-red-100">{{ $message }}</div>
  @enderror

  <div class="mt-6 glass overflow-x-auto rounded-2xl border border-white/10">
    <table class="w-full text-sm">
      <thead class="bg-white/5 text-left">
        <tr>
          <th class="p-4">Referencia</th><th class="p-4">Cliente</th><th class="p-4">Correo</th>
          <th class="p-4">Concepto</th><th class="p-4">Monto</th><th class="p-4">Moneda</th>
          <th class="p-4">Estado</th><th class="p-4">Creación</th><th class="p-4">Expiración</th><th class="p-4">Acciones</th>
        </tr>
      </thead>
      <tbody>
        @forelse($paymentLinks as $paymentLink)
          @php
            $publicUrl = $paymentLink->publicUrl();
            $phone = preg_replace('/\D+/', '', (string) $paymentLink->customer_phone);
            $message = "Hola {$paymentLink->customer_name}, este es tu enlace de pago:\n{$publicUrl}\n\nMonto acordado:\n".\App\Support\Money::format($paymentLink->amount, $paymentLink->currency);
            $whatsappUrl = 'https://wa.me/'.($phone ?: '').'?text='.rawurlencode($message);
          @endphp
          <tr class="border-t border-white/10 align-top">
            <td class="p-4 font-semibold">{{ $paymentLink->reference }}</td>
            <td class="p-4">{{ $paymentLink->customer_name }}</td>
            <td class="p-4">{{ $paymentLink->customer_email }}</td>
            <td class="p-4">{{ $paymentLink->concept }}</td>
            <td class="p-4 whitespace-nowrap font-semibold">{{ \App\Support\Money::format($paymentLink->amount, $paymentLink->currency) }}</td>
            <td class="p-4">{{ $paymentLink->currency }}</td>
            <td class="p-4">{{ $paymentLink->statusLabel() }}</td>
            <td class="p-4 whitespace-nowrap">{{ $paymentLink->created_at->format('d/m/Y H:i') }}</td>
            <td class="p-4 whitespace-nowrap">{{ $paymentLink->expires_at?->format('d/m/Y H:i') ?? 'Sin expiración' }}</td>
            <td class="p-4">
              <div class="flex min-w-48 flex-wrap gap-2">
                <button class="chip" type="button" data-copy-url="{{ $publicUrl }}">Copiar enlace</button>
                <a class="chip" href="{{ $publicUrl }}" target="_blank" rel="noopener">Abrir enlace</a>
                <a class="chip" href="{{ $whatsappUrl }}" target="_blank" rel="noopener">Enviar por WhatsApp</a>
                @if($paymentLink->canBeCancelled())
                  <form method="POST" action="{{ route('admin.payment-links.cancel', $paymentLink) }}" onsubmit="return confirm('¿Cancelar este enlace de pago?')">
                    @csrf
                    @method('PATCH')
                    <button class="chip" type="submit">Cancelar enlace</button>
                  </form>
                @endif
              </div>
            </td>
          </tr>
        @empty
          <tr><td class="p-6 text-white/60" colspan="10">Aún no hay enlaces de pago.</td></tr>
        @endforelse
      </tbody>
    </table>
  </div>
  <div class="mt-6">{{ $paymentLinks->links() }}</div>

  <script>
    document.querySelectorAll('[data-copy-url]').forEach(function (button) {
      button.addEventListener('click', async function () {
        await navigator.clipboard.writeText(button.dataset.copyUrl);
        button.textContent = 'Enlace copiado';
      });
    });
  </script>
@endsection
