@extends('layouts.site')

@section('title', 'Contacto | '.config('shop.business.name'))
@section('meta_description', 'Canales de contacto y soporte para consultas sobre cursos y acceso digital.')

@section('content')
  <section class="glass p-6 md:p-8 rounded-3xl border border-white/10 max-w-3xl mx-auto">
    <h1 class="text-3xl font-extrabold">Contacto</h1>
    <p class="mt-4 text-white/80">{{ config('shop.business.support_text') }}</p>

    <dl class="mt-8 grid gap-5">
      <div>
        <dt class="font-semibold text-white">Nombre comercial</dt>
        <dd class="mt-1 text-white/75">{{ config('shop.business.name') }}</dd>
      </div>

      @if(config('shop.business.email'))
        <div>
          <dt class="font-semibold text-white">Correo</dt>
          <dd class="mt-1"><a class="text-cyan-300 underline" href="mailto:{{ config('shop.business.email') }}">{{ config('shop.business.email') }}</a></dd>
        </div>
      @endif

      @if(config('shop.business.whatsapp'))
        <div>
          <dt class="font-semibold text-white">WhatsApp</dt>
          <dd class="mt-2"><a class="btn btn-accent" target="_blank" rel="noopener" href="https://wa.me/{{ config('shop.business.whatsapp') }}">Contactar por WhatsApp</a></dd>
        </div>
      @endif

      @if(config('shop.business.support_hours'))
        <div>
          <dt class="font-semibold text-white">Horario de atención</dt>
          <dd class="mt-1 text-white/75">{{ config('shop.business.support_hours') }}</dd>
        </div>
      @endif
    </dl>
  </section>
@endsection
