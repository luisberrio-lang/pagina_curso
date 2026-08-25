@extends('layouts.site')

@section('title', 'Cambios, devoluciones y reembolsos | '.config('shop.business.name'))
@section('meta_description', 'Criterios generales para solicitar revisión de cambios, devoluciones o reembolsos de contenido digital.')

@section('content')
  <article class="glass p-6 md:p-8 rounded-3xl border border-white/10 max-w-4xl mx-auto">
    <h1 class="text-3xl font-extrabold">Cambios, devoluciones y reembolsos</h1>
    <div class="mt-6 space-y-6 text-white/80 leading-relaxed">
      <section>
        <h2 class="text-xl font-semibold text-white">Productos digitales</h2>
        <p class="mt-2">Los cursos y materiales del catálogo son productos digitales. Por su naturaleza, cada solicitud debe revisarse considerando el contenido adquirido, la forma de acceso proporcionada y las circunstancias comunicadas.</p>
      </section>
      <section>
        <h2 class="text-xl font-semibold text-white">Solicitud de revisión</h2>
        <p class="mt-2">Si el contenido recibido no corresponde con la descripción publicada, existe un problema de acceso o se produjo una incidencia en la coordinación, contacta al soporte indicando el curso y una explicación verificable del inconveniente.</p>
      </section>
      <section>
        <h2 class="text-xl font-semibold text-white">Evaluación</h2>
        <p class="mt-2">La solicitud será evaluada de forma individual. Según el caso, podrá proponerse corregir el acceso, aclarar el contenido, realizar un cambio o revisar la procedencia de un reembolso.</p>
      </section>
      <p>Envía tu solicitud desde la <a class="text-cyan-300 underline" href="{{ route('contact') }}">página de contacto</a>.</p>
    </div>
  </article>
@endsection
