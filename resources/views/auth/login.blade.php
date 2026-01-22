@extends('layouts.site')

@section('content')
  <div class="max-w-md mx-auto glass p-6 rounded-2xl border border-white/10">
    <h1 class="text-2xl font-bold">Iniciar sesión</h1>
    <p class="mt-2 text-white/70">Accede para administrar Programas/Cursos.</p>

    @if ($errors->any())
      <div class="mt-4 p-3 rounded-xl bg-red-500/10 border border-red-500/20 text-red-200 text-sm">
        {{ $errors->first() }}
      </div>
    @endif

    <form class="mt-6 space-y-4" method="POST" action="{{ route('login') }}">
      @csrf

      <div>
        <label class="text-white/75 text-sm">Email</label>
        <input
          id="email"
          class="mt-1 w-full rounded-xl bg-white/10 border border-white/10 px-4 py-3 text-white"
          type="email"
          name="email"
          value="{{ old('email') }}"
          required autofocus autocomplete="username"
        >
      </div>

      <div>
        <label class="text-white/75 text-sm">Contraseña</label>
        <input
          id="password"
          class="mt-1 w-full rounded-xl bg-white/10 border border-white/10 px-4 py-3 text-white"
          type="password"
          name="password"
          required autocomplete="current-password"
        >
      </div>

      <label class="flex items-center gap-2 text-white/70 text-sm">
        <input id="remember_me" type="checkbox" name="remember"
               class="rounded border-white/20 bg-white/10 text-cyan-400 focus:ring-cyan-400">
        Recordarme
      </label>

      <div class="flex items-center justify-between">
        @if (Route::has('password.request'))
          <a class="text-sm text-white/70 hover:text-white underline" href="{{ route('password.request') }}">
            ¿Olvidaste tu contraseña?
          </a>
        @endif

        <button class="btn btn-accent" type="submit">Ingresar</button>
      </div>
    </form>
  </div>
@endsection
