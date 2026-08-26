@extends('layouts.site')

@section('title', 'Crear cuenta | '.config('shop.business.name'))

@section('content')
  <div class="max-w-md mx-auto glass p-6 rounded-2xl border border-white/10">
    <h1 class="text-2xl font-bold">Crear cuenta</h1>
    <p class="mt-2 text-white/70">El registro es opcional: puedes realizar tu pedido como invitado.</p>
    @if ($errors->any())<div class="mt-4 p-3 rounded-xl bg-red-500/10 border border-red-500/20 text-red-200 text-sm" role="alert">{{ $errors->first() }}</div>@endif
    <form class="mt-6 space-y-4" method="POST" action="{{ route('register') }}">
      @csrf
      <div><label for="name" class="text-white/75 text-sm">Nombre</label><input id="name" class="mt-1 w-full rounded-xl bg-white/10 border border-white/10 px-4 py-3 text-white" type="text" name="name" value="{{ old('name') }}" required autofocus autocomplete="name"></div>
      <div><label for="email" class="text-white/75 text-sm">Correo electrónico</label><input id="email" class="mt-1 w-full rounded-xl bg-white/10 border border-white/10 px-4 py-3 text-white" type="email" name="email" value="{{ old('email') }}" required autocomplete="username"></div>
      <div><label for="password" class="text-white/75 text-sm">Contraseña</label><input id="password" class="mt-1 w-full rounded-xl bg-white/10 border border-white/10 px-4 py-3 text-white" type="password" name="password" required autocomplete="new-password"></div>
      <div><label for="password_confirmation" class="text-white/75 text-sm">Confirmar contraseña</label><input id="password_confirmation" class="mt-1 w-full rounded-xl bg-white/10 border border-white/10 px-4 py-3 text-white" type="password" name="password_confirmation" required autocomplete="new-password"></div>
      <div class="flex items-center justify-between"><a class="text-sm text-white/70 hover:text-white underline" href="{{ route('login') }}">¿Ya tienes cuenta?</a><button class="btn btn-accent" type="submit">Crear cuenta</button></div>
    </form>
  </div>
@endsection
