@extends('layouts.site')

@section('title', 'Inicio | Cursos de Ingeniería')

@section('content')

  {{-- ✅ SOLO AGREGADO: Bootstrap Icons + Lordicon + estilos para quitar 1,2,3 y alinear iconos (SIN desorden de texto) --}}
  @once
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">
    <script src="https://cdn.lordicon.com/lordicon.js"></script>

    <style>
      /* Quita numeración (1,2,3) sin eliminar tus clases list-decimal/list-inside */
      .ol-icons{
        list-style: none !important;
        padding-left: 0 !important;
        margin-left: 0 !important;
      }

      /* Cada LI: icono + texto (el texto va envuelto en .li-text para que NO se desordene) */
      .ol-icons > li{
        display: flex;
        gap: .75rem;
        align-items: center;
      }

      .ol-icons .li-ico{
        flex: 0 0 auto;
        width: 2.25rem;
        height: 2.25rem;
        display: grid;
        place-items: center;
        border-radius: 1rem;
        border: 1px solid rgba(255,255,255,.12);
        background: rgba(255,255,255,.06);
        margin-top: 0;
      }

      .ol-icons .li-text{
        flex: 1 1 auto;
        min-width: 0;
        line-height: 1.45;
        text-align: left !important; /* por si en tu tema hay justify */
      }

      .ol-icons lord-icon{ width: 22px; height: 22px; }

      .ol-icons .bi{
        font-size: 1.05rem;
        line-height: 1;
        color: rgba(165,243,252,.95);
      }

      .ol-icons .soles{
        font-weight: 900;
        font-size: 0.95rem;
        letter-spacing: .02em;
        color: rgba(165,243,252,.95);
      }

      /* ✅ Beneficios con iconos (sin tocar tu estructura, solo estilo extra) */
      .ul-icons{
        list-style: none !important;
        padding-left: 0 !important;
        margin-left: 0 !important;
      }
      .ul-icons > li{
        display:flex;
        gap:.75rem;
        align-items:center;
      }
      .ul-icons .li-ico{
        flex: 0 0 auto;
        width: 2.25rem;
        height: 2.25rem;
        display: grid;
        place-items: center;
        border-radius: 1rem;
        border: 1px solid rgba(255,255,255,.12);
        background: rgba(255,255,255,.06);
        margin-top: 0;
      }
      .ul-icons .li-text{
        flex: 1 1 auto;
        min-width: 0;
        line-height: 1.45;
        text-align: left !important;
      }
      .ul-icons .bi{
        font-size: 1.05rem;
        line-height: 1;
        color: rgba(165,243,252,.95);
      }
    </style>
  @endonce

  {{-- Sección 1: Hero --}}
  <section class="glass px-4 pb-4 pt-2 md:px-6 md:pb-6 md:pt-3 rounded-3xl border border-white/10 relative overflow-hidden min-h-[28rem] md:min-h-[36rem] flex flex-col">
    <div class="absolute -top-20 -right-20 h-72 w-72 rounded-full bg-cyan-500/20 blur-3xl pointer-events-none" aria-hidden="true"></div>
    <div class="absolute -bottom-24 -left-20 h-72 w-72 rounded-full bg-blue-600/20 blur-3xl pointer-events-none" aria-hidden="true"></div>

    <div class="flex items-center justify-center text-center">
      <h1 class="text-3xl md:text-5xl font-extrabold tracking-tight">
        Cursos por áreas con enfoque <span class="text-cyan-300">tecnológico</span>
      </h1>
    </div>

    {{-- ✅ Imagen debajo del título (usa public/img/portad.png) --}}
    <div class="mt-6 md:mt-8 overflow-hidden rounded-2xl border border-white/10 bg-white/5 -mx-4 md:-mx-6">
      <img
        src="{{ asset('images/portadaofi.png') }}"
        alt="Cursos de ingeniería por áreas"
        class="block w-full h-auto max-h-64 md:max-h-[26rem] object-contain"
        loading="lazy"
      >
    </div>

    <div class="mt-auto">
      <p class="mt-4 text-white/80 max-w-2xl">
        Cursos organizados, temarios claros y material listo para estudiar, repasar y aplicar.
      </p>

      <div class="mt-6 flex flex-wrap gap-3">
        <a href="{{ route('courses.index') }}" class="btn btn-accent btn-accent-soft">Ver Programas/Cursos</a>
        <a href="{{ route('price') }}" class="btn btn-ghost">Ver Precio</a>
      </div>
    </div>
  </section>

  {{-- Sección 2 y 3 --}}
  <section class="mt-10 grid md:grid-cols-2 gap-6">
    <div class="glass p-6 rounded-2xl border border-white/10">
      <h2 class="text-xl font-semibold">¿Cómo funciona?</h2>

      <ol class="mt-4 space-y-2 text-white/80 list-decimal list-inside ol-icons">
        <li>
          <span class="li-ico">
            {{-- ✅ Libro (Bootstrap Icon) --}}
            <i class="bi bi-book"></i>
          </span>
          <span class="li-text">Entras a <b>Programas/Cursos</b></span>
        </li>

        <li>
          <span class="li-ico">
            {{-- Dinámico (Lordicon) --}}
            <lord-icon
              src="https://cdn.lordicon.com/lbjtvqiv.json"
              trigger="hover"
              colors="primary:#22d3ee,secondary:#93c5fd">
            </lord-icon>
          </span>
          <span class="li-text">Seleccionas el <b>área</b> y eliges el curso que necesitas</span>
        </li>

        <li>
          <span class="li-ico">
            {{-- Estático --}}
            <i class="bi bi-list-check"></i>
          </span>
          <span class="li-text">En cada curso revisas: <b>qué incluye</b> + <b>temario</b> + <b>beneficios</b> + <b>muestras</b></span>
        </li>

        <li>
          <span class="li-ico">
            {{-- Estático --}}
            <i class="bi bi-buildings"></i>
          </span>
          <span class="li-text">Accedes a cursos organizados a partir de diferentes <b>instituciones</b>, con su <b>material de estudio</b> y el <b>programa/software</b> correspondiente</span>
        </li>

        <li>
          <span class="li-ico">
            {{-- Estático --}}
            <i class="bi bi-infinity"></i>
          </span>
          <span class="li-text">El acceso es <b>ilimitado</b>, para que puedas <b>estudiar y repasar</b> cuando quieras</span>
        </li>

        <li>
          <span class="li-ico">
            {{-- ✅ Símbolo de soles --}}
            <span class="soles">S/</span>
          </span>
          <span class="li-text">El pago es <b>único</b>, sin <b>costos adicionales</b></span>
        </li>
      </ol>
    </div>

    <div class="glass p-6 rounded-2xl border border-white/10">
      <h2 class="text-xl font-semibold">¿Cómo damos acceso a los cursos?</h2>

      <ol class="mt-4 space-y-2 text-white/80 list-decimal list-inside ol-icons">
        <li>
          <span class="li-ico">
            <i class="bi bi-google"></i>
          </span>
          <span class="li-text"><b>Método de acceso:</b> Acceso directo a <b>Google Drive</b> y/o entrega por <b>correo Gmail</b></span>
        </li>

        <li>
          <span class="li-ico">
            <i class="bi bi-clock-history"></i>
          </span>
          <span class="li-text"><b>Tiempo de entrega:</b> Acceso <b>inmediato</b> (tiempo estimado: <b>5 minutos</b>)</span>
        </li>

        <li>
          <span class="li-ico">
            <i class="bi bi-hdd-stack"></i>
          </span>
          <span class="li-text"><b>Almacenamiento:</b> El material se comparte mediante <b>Drive</b>, por lo que <b>no necesitas espacio</b> en tu correo/Drive. Cada curso puede incluir <b>más de 100 GB</b> de contenido</span>
        </li>

        <li>
          <span class="li-ico">
            <i class="bi bi-whatsapp"></i>
          </span>
          <span class="li-text"><b>Soporte por WhatsApp (24/7):</b> Disponible para consultas y asistencia</span>
        </li>
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
          <a class="btn btn-accent btn-accent-soft mt-4" href="{{ route('courses.byArea', $a) }}">Ver cursos</a>
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

    {{-- ✅ Iconos adecuados en cada beneficio --}}
    <ul class="mt-4 space-y-2 text-white/80 list-disc list-inside ul-icons">
      <li>
        <span class="li-ico"><i class="bi bi-infinity"></i></span>
        <span class="li-text">Acceso de por vida al material (Google Drive)</span>
      </li>
      <li>
        <span class="li-ico"><i class="bi bi-cloud-download"></i></span>
        <span class="li-text">Archivos descargables</span>
      </li>
      <li>
        <span class="li-ico"><i class="bi bi-envelope"></i></span>
        <span class="li-text">Entrega vía correo Gmail / Google Drive</span>
      </li>
      <li>
        <span class="li-ico"><i class="bi bi-arrow-repeat"></i></span>
        <span class="li-text">Futuras actualizaciones incluidas</span>
      </li>
      <li>
        <span class="li-ico"><i class="bi bi-badge-hd"></i></span>
        <span class="li-text">Videos en calidad Full HD</span>
      </li>
      <li>
        <span class="li-ico"><span class="soles">S/</span></span>
        <span class="li-text">Pago único, sin costos adicionales</span>
      </li>
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
