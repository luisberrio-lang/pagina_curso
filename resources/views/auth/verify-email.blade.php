@extends('layouts.site')
@section('title', 'Verificar correo | '.config('shop.business.name'))
@section('content')
<div class="max-w-lg mx-auto glass p-6 rounded-2xl border border-white/10"><h1 class="text-2xl font-bold">Verifica tu correo electrónico</h1><p class="mt-2 text-white/70">Revisa el enlace enviado a tu correo. Si no lo recibiste, solicita uno nuevo.</p>@if(session('status') === 'verification-link-sent')<p class="mt-4 text-sm text-emerald-200">Enviamos un nuevo enlace de verificación.</p>@endif<div class="mt-6 flex flex-wrap gap-3"><form method="POST" action="{{ route('verification.send') }}">@csrf<button class="btn btn-accent" type="submit">Reenviar enlace</button></form><form method="POST" action="{{ route('logout') }}">@csrf<button class="chip" type="submit">Cerrar sesión</button></form></div></div>
@endsection
