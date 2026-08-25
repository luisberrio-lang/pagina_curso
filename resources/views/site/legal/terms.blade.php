@extends('layouts.site')

@section('title', 'Términos y condiciones | '.config('shop.business.name'))
@section('meta_description', 'Condiciones generales aplicables al catálogo y acceso a los cursos digitales.')

@section('content')
  <article class="glass p-6 md:p-8 rounded-3xl border border-white/10 max-w-4xl mx-auto">
    <h1 class="text-3xl font-extrabold">Términos y condiciones</h1>
    <div class="mt-6 space-y-6 text-white/80 leading-relaxed">
      <section>
        <h2 class="text-xl font-semibold text-white">Alcance</h2>
        <p class="mt-2">Estas condiciones describen el uso del sitio de {{ config('shop.business.name') }} y la información comercial de sus cursos y contenidos digitales.</p>
      </section>
      <section>
        <h2 class="text-xl font-semibold text-white">Catálogo y precios</h2>
        <p class="mt-2">El catálogo identifica el contenido disponible, su precio vigente en {{ config('shop.currency') }} y, cuando corresponda, un precio anterior de referencia promocional. Antes de coordinar una adquisición, revisa la descripción y solicita aclaraciones sobre el contenido que necesites.</p>
      </section>
      <section>
        <h2 class="text-xl font-semibold text-white">Acceso digital</h2>
        <p class="mt-2">Los productos ofrecidos son digitales. La modalidad concreta de acceso o entrega se informa en el curso y se coordina mediante los canales de contacto disponibles. No se realiza envío físico salvo que se indique expresamente.</p>
      </section>
      <section>
        <h2 class="text-xl font-semibold text-white">Uso del contenido</h2>
        <p class="mt-2">El acceso adquirido es para uso del comprador. No se autoriza redistribuir, revender o publicar materiales cuando ello no haya sido permitido expresamente por el titular correspondiente.</p>
      </section>
      <section>
        <h2 class="text-xl font-semibold text-white">Consultas</h2>
        <p class="mt-2">Para confirmar condiciones particulares de un curso, utiliza la <a class="text-cyan-300 underline" href="{{ route('contact') }}">página de contacto</a> antes de adquirirlo.</p>
      </section>
    </div>
  </article>
@endsection
