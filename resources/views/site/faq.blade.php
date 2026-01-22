@extends('layouts.site')

@section('content')
  <h1 class="text-3xl font-extrabold">FAQ</h1>

  <div class="mt-6 grid md:grid-cols-2 gap-6">
    <div class="glass p-6 rounded-2xl border border-white/10">
      <h2 class="font-semibold text-xl">Acceso y entrega</h2>
      <p class="mt-2 text-white/80">
        Acceso por Drive/enlaces/plataforma según el curso. Entrega rápida.
      </p>
    </div>

    <div class="glass p-6 rounded-2xl border border-white/10">
      <h2 class="font-semibold text-xl">Contenido</h2>
      <p class="mt-2 text-white/80">
        Temario organizado, material estructurado y muestras.
      </p>
    </div>

    <div class="glass p-6 rounded-2xl border border-white/10">
      <h2 class="font-semibold text-xl">Precios/planes y compra</h2>
      <p class="mt-2 text-white/80">
        Los planes se muestran por área en la sección Precio. Compra por WhatsApp.
      </p>
    </div>

    <div class="glass p-6 rounded-2xl border border-white/10">
      <h2 class="font-semibold text-xl">Soporte</h2>
      <p class="mt-2 text-white/80">
        Soporte por WhatsApp según horario.
      </p>
    </div>
  </div>
@endsection
