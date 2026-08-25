@extends('layouts.site')

@section('title', 'Configuración del administrador | '.config('shop.business.name'))

@section('content')
  <section class="glass p-6 md:p-8 rounded-3xl border border-white/10 max-w-3xl mx-auto">
    <div class="flex flex-wrap items-start justify-between gap-4">
      <div>
        <h1 class="text-3xl font-extrabold">Configuración del administrador</h1>
        <p class="mt-2 text-white/70">Sincroniza únicamente el administrador principal con la configuración segura del entorno.</p>
      </div>
      <a class="btn btn-ghost" href="{{ route('admin.dashboard') }}">Volver al dashboard</a>
    </div>

    @if(session('ok'))
      <div class="mt-6 rounded-2xl border border-emerald-500/30 bg-emerald-500/10 p-4 text-emerald-100">{{ session('ok') }}</div>
    @endif

    @if($errors->any())
      <div class="mt-6 rounded-2xl border border-red-500/30 bg-red-500/10 p-4 text-red-100">
        <ul class="list-disc list-inside space-y-1">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
      </div>
    @endif

    <dl class="mt-8 grid sm:grid-cols-2 gap-5">
      <div><dt class="text-sm text-white/60">Nombre configurado</dt><dd class="mt-1 font-semibold">{{ $configuredAdmin['name'] ?: 'No configurado' }}</dd></div>
      <div><dt class="text-sm text-white/60">Correo configurado</dt><dd class="mt-1 font-semibold">{{ $configuredAdmin['email'] ?: 'No configurado' }}</dd></div>
      <div><dt class="text-sm text-white/60">Teléfono configurado</dt><dd class="mt-1 font-semibold">{{ $configuredAdmin['phone'] ?: 'No configurado' }}</dd></div>
      <div><dt class="text-sm text-white/60">Contraseña configurada</dt><dd class="mt-1 font-semibold">{{ $configuredAdmin['password_configured'] ? 'SÍ' : 'NO' }}</dd></div>
    </dl>

    <div class="mt-8 rounded-2xl border border-amber-400/20 bg-amber-400/10 p-4 text-sm text-amber-100">
      Si existen varios administradores, solo se sincroniza el de menor ID. Una contraseña vacía conserva la contraseña actual.
    </div>

    <form class="mt-6" method="POST" action="{{ route('admin.configuration.sync') }}">
      @csrf
      <label class="flex items-start gap-3 text-sm text-white/80" for="confirm_sync">
        <input id="confirm_sync" name="confirm_sync" type="checkbox" value="1" class="mt-1" required>
        <span>Confirmo que deseo aplicar al administrador principal los valores configurados en el entorno.</span>
      </label>
      @error('confirm_sync')<p class="mt-2 text-sm text-red-300">Debes confirmar la sincronización.</p>@enderror
      <button class="btn btn-accent mt-6" type="submit">Sincronizar administrador desde entorno</button>
    </form>
  </section>
@endsection
