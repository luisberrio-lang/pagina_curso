@extends('layouts.site')

@section('title', 'Crear enlace de pago | '.config('shop.business.name'))

@section('content')
  <section class="flex flex-wrap items-end justify-between gap-4">
    <div><h1 class="text-3xl font-extrabold">Crear enlace de pago</h1><p class="mt-2 text-white/70">El monto quedará fijado en el servidor y el cliente no podrá modificarlo.</p></div>
    <a class="btn btn-ghost" href="{{ route('admin.payment-links.index') }}">Volver</a>
  </section>

  <form class="glass mt-6 grid gap-6 rounded-3xl p-6 md:grid-cols-2 md:p-8" method="POST" action="{{ route('admin.payment-links.store') }}">
    @csrf
    <label>Nombre del cliente<input class="input mt-2 w-full" name="customer_name" maxlength="150" required value="{{ old('customer_name') }}">@error('customer_name')<span class="mt-1 block text-sm text-red-300">{{ $message }}</span>@enderror</label>
    <label>Correo<input class="input mt-2 w-full" type="email" name="customer_email" maxlength="255" required value="{{ old('customer_email') }}">@error('customer_email')<span class="mt-1 block text-sm text-red-300">{{ $message }}</span>@enderror</label>
    <label>Celular opcional<input class="input mt-2 w-full" name="customer_phone" maxlength="30" value="{{ old('customer_phone') }}">@error('customer_phone')<span class="mt-1 block text-sm text-red-300">{{ $message }}</span>@enderror</label>
    <label>Concepto<input class="input mt-2 w-full" name="concept" maxlength="255" required value="{{ old('concept') }}">@error('concept')<span class="mt-1 block text-sm text-red-300">{{ $message }}</span>@enderror</label>
    <label>Monto (PEN)<input class="input mt-2 w-full text-2xl font-bold" type="number" name="amount" min="0.01" max="100000" step="0.01" required value="{{ old('amount') }}">@error('amount')<span class="mt-1 block text-sm text-red-300">{{ $message }}</span>@enderror</label>
    <label>Moneda<select class="input mt-2 w-full" name="currency" required><option value="PEN" @selected(old('currency', 'PEN') === 'PEN')>PEN — Sol peruano</option></select>@error('currency')<span class="mt-1 block text-sm text-red-300">{{ $message }}</span>@enderror</label>
    <label>Fecha de expiración opcional<input class="input mt-2 w-full" type="datetime-local" name="expires_at" value="{{ old('expires_at') }}">@error('expires_at')<span class="mt-1 block text-sm text-red-300">{{ $message }}</span>@enderror</label>
    <label class="md:col-span-2">Nota interna opcional<textarea class="input mt-2 w-full" name="internal_note" maxlength="2000" rows="4">{{ old('internal_note') }}</textarea>@error('internal_note')<span class="mt-1 block text-sm text-red-300">{{ $message }}</span>@enderror</label>
    <div class="md:col-span-2 flex justify-end"><button class="btn btn-accent" type="submit">Crear enlace de pago</button></div>
  </form>
@endsection
