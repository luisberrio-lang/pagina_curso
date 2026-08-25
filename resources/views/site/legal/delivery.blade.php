@extends('layouts.site')

@section('title', 'Entrega y acceso digital | '.config('shop.business.name'))
@section('meta_description', 'Información sobre la coordinación de entrega y acceso a cursos y contenidos digitales.')

@section('content')
  <article class="glass p-6 md:p-8 rounded-3xl border border-white/10 max-w-4xl mx-auto">
    <h1 class="text-3xl font-extrabold">Entrega y acceso a productos digitales</h1>
    <div class="mt-6 space-y-6 text-white/80 leading-relaxed">
      <section>
        <h2 class="text-xl font-semibold text-white">Modalidad digital</h2>
        <p class="mt-2">Los productos del catálogo son cursos y materiales digitales. No incluyen envío físico, salvo que la descripción de un producto indique expresamente algo diferente.</p>
      </section>
      <section>
        <h2 class="text-xl font-semibold text-white">Coordinación del acceso</h2>
        <p class="mt-2">Actualmente el acceso se coordina mediante los canales de contacto y puede proporcionarse mediante enlaces o recursos compartidos, incluido Google Drive, según el contenido del curso. No se promete un proceso automático de entrega.</p>
      </section>
      <section>
        <h2 class="text-xl font-semibold text-white">Datos necesarios</h2>
        <p class="mt-2">Verifica que el canal o correo proporcionado durante la coordinación sea correcto. La información concreta necesaria para cada entrega se solicitará únicamente cuando corresponda.</p>
      </section>
      <section>
        <h2 class="text-xl font-semibold text-white">Problemas de acceso</h2>
        <p class="mt-2">Si un enlace no funciona o el contenido coordinado no está disponible, comunícate mediante la <a class="text-cyan-300 underline" href="{{ route('contact') }}">página de contacto</a> e identifica el curso.</p>
      </section>
    </div>
  </article>
@endsection
