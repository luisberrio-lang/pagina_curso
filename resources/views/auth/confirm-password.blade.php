@extends('layouts.site')
@section('title', 'Confirmar contraseña | '.config('shop.business.name'))
@section('content')
<div class="max-w-md mx-auto glass p-6 rounded-2xl border border-white/10"><h1 class="text-2xl font-bold">Confirmar contraseña</h1><p class="mt-2 text-white/70">Esta es un área protegida. Confirma tu contraseña para continuar.</p><form class="mt-6 space-y-4" method="POST" action="{{ route('password.confirm') }}">@csrf<div><label for="password" class="text-sm text-white/75">Contraseña</label><input id="password" class="mt-1 w-full rounded-xl bg-white/10 border border-white/10 px-4 py-3 text-white" type="password" name="password" required autocomplete="current-password">@error('password')<p class="mt-2 text-sm text-red-300">{{ $message }}</p>@enderror</div><button class="btn btn-accent" type="submit">Confirmar</button></form></div>
@endsection
