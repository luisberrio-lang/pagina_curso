@extends('layouts.site')

@section('content')
  {{-- Sección 1: Hero --}}
  <section class="glass p-8 md:p-10 rounded-3xl border border-white/10 relative overflow-hidden">
    <div class="absolute -top-20 -right-20 h-72 w-72 rounded-full bg-cyan-500/20 blur-3xl pointer-events-none" aria-hidden="true"></div>
    <div class="absolute -bottom-24 -left-20 h-72 w-72 rounded-full bg-blue-600/20 blur-3xl pointer-events-none" aria-hidden="true"></div>

    <h1 class="text-3xl md:text-5xl font-extrabold tracking-tight">
      Cursos por áreas con enfoque <span class="text-cyan-300">tecnológico</span>
    </h1>
    <p class="mt-4 text-white/80 max-w-2xl">
      Cursos organizados, temarios claros y material listo para estudiar, repasar y aplicar.
    </p>

    <div class="mt-6 flex flex-wrap gap-3">
      <a href="{{ route('courses.index') }}" class="btn btn-accent">Ver Programas/Cursos</a>
      <a href="{{ route('price') }}" class="btn btn-ghost">Ver Precio</a>
    </div>
  </section>

  {{-- Sección 2 y 3 --}}
  <section class="mt-10 grid md:grid-cols-2 gap-6">
    <div class="glass p-6 rounded-2xl border border-white/10">
      <h2 class="text-xl font-semibold">¿Cómo funciona?</h2>
      <ol class="mt-4 space-y-2 text-white/80 list-decimal list-inside">
       <li>Entras a <b>Programas/Cursos</b></li>
<li>Seleccionas el <b>área</b> y eliges el curso que necesitas</li>
<li>En cada curso revisas: <b>qué incluye</b> + <b>temario</b> + <b>beneficios</b> + <b>muestras</b></li>
<li>Accedes a cursos organizados a partir de diferentes <b>instituciones</b>, con su <b>material de estudio</b> y el <b>programa/software</b> correspondiente</li>
<li>El acceso es <b>ilimitado</b>, para que puedas <b>estudiar y repasar</b> cuando quieras</li>
<li>El pago es <b>único</b>, sin <b>costos adicionales</b></li>

      </ol>
    </div>

    <div class="glass p-6 rounded-2xl border border-white/10">
      <h2 class="text-xl font-semibold">¿Cómo damos acceso a los cursos?</h2>
<ol class="mt-4 space-y-2 text-white/80 list-decimal list-inside">
  <li><b>Método de acceso:</b> Acceso directo a <b>Google Drive</b> y/o entrega por <b>correo Gmail</b></li>
  <li><b>Tiempo de entrega:</b> Acceso <b>inmediato</b> (tiempo estimado: <b>5 minutos</b>)</li>
  <li><b>Almacenamiento:</b> El material se comparte mediante <b>Drive</b>, por lo que <b>no necesitas espacio</b> en tu correo/Drive. Cada curso puede incluir <b>más de 100 GB</b> de contenido</li>
  <li><b>Soporte por WhatsApp (24/7):</b> Disponible para consultas y asistencia</li>
</ol>

    </div>
  </section>

  {{-- Sección 4: Áreas --}}
  <section class="mt-10">
    <h2 class="text-2xl font-bold">Áreas</h2>
    <div class="mt-4 grid md:grid-cols-3 gap-6">
      @forelse($areas as $a)
        <div class="glass p-6 rounded-2xl border border-white/10">
          <h3 class="font-semibold text-lg">{{ $a->name }}</h3>
          <p class="mt-2 text-white/75">{{ $a->description ?? 'Programas y cursos por especialidad.' }}</p>
          <a class="btn btn-ghost mt-4" href="{{ route('courses.byArea', $a) }}">Ver cursos</a>
        </div>
      @empty
        <p class="text-white/70">Aún no hay áreas registradas.</p>
      @endforelse
    </div>
  </section>

  {{-- Sección 5: Cursos destacados (SIN precios aquí) --}}
  <section class="mt-10">
    <h2 class="text-2xl font-bold">Cursos destacados</h2>
    <div class="mt-4 grid md:grid-cols-3 gap-6">
      @forelse($featured as $c)
        <div class="glass rounded-2xl border border-white/10 overflow-hidden">
          <div class="h-44 bg-white/5">
            @if(method_exists($c, 'coverUrl') && $c->coverUrl())
              <img class="h-44 w-full object-cover" src="{{ $c->coverUrl() }}" alt="">
            @endif
          </div>
          <div class="p-5">
            <h3 class="font-semibold">{{ $c->title }}</h3>
            <p class="mt-2 text-white/75">{{ $c->short_desc }}</p>
            <a class="btn btn-accent mt-4" href="{{ route('courses.show', $c) }}">Ver detalles</a>
          </div>
        </div>
      @empty
        <p class="text-white/70">Aún no hay cursos destacados.</p>
      @endforelse
    </div>
  </section>

  {{-- Sección 6: Beneficios generales --}}
  <section class="mt-10 glass p-6 rounded-2xl border border-white/10">
    <h2 class="text-2xl font-bold">Beneficios generales</h2>
    <ul class="mt-4 space-y-2 text-white/80 list-disc list-inside">
<li>Acceso de por vida al material (Google Drive)</li>
<li>Archivos descargables</li>
<li>Entrega vía correo Gmail / Google Drive</li>
<li>Futuras actualizaciones incluidas</li>
<li>Videos en calidad Full HD</li>
<li>Pago único, sin costos adicionales</li>

    </ul>
  </section>

  {{-- Sección 7: Muestras generales (placeholder) --}}
  <section class="mt-10">
    <h2 class="text-2xl font-bold">Muestras generales</h2>
    <div class="mt-4 grid grid-cols-2 md:grid-cols-4 gap-4">
      @for($i = 1; $i <= 4; $i++)
        <div class="glass rounded-2xl border border-white/10 h-28 md:h-36"></div>
      @endfor
    </div>
  </section>
@endsection
