<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>{{ config('app.name') }}</title>

  {{-- ✅ Solo mostrar preloader 1 vez por pestaña (sessionStorage) --}}
  <script>
    (function () {
      try {
        if (sessionStorage.getItem('preloader_shown') === '1') {
          document.documentElement.classList.add('skip-preloader');
        }
      } catch (e) {}
    })();
  </script>

  @vite(['resources/css/app.css','resources/js/app.js'])
</head>

<body class="min-h-screen bg-tech text-white flex flex-col" style="--site-bg-image: url('{{ asset('images/fondo.png') }}'); --site-bg-x: -32px; --site-bg-x-lg: -42px; --site-bg-x-2xl: -52px;">

  {{-- ✅ PRELOADER --}}
  <div id="app-preloader"
       data-min="1800"
       data-fade="520"
       aria-busy="true"
       aria-label="Cargando sitio">

    <div class="preloader-particles" aria-hidden="true"></div>

    <div class="preloader-center">
      <div class="preloader-orb" aria-hidden="true"></div>

      <h1 class="preloader-title glitch" data-text="Bienvenido">
        <span class="wave" data-wave>Bienvenido</span>
      </h1>

      <div class="preloader-sub">
        <span class="preloader-loading">Cargando…</span>
        <span class="preloader-percent" data-preloader-percent>0%</span>
      </div>

      <div class="preloader-bar" role="progressbar" aria-valuemin="0" aria-valuemax="100" aria-valuenow="0">
        <div class="preloader-bar-fill"></div>
      </div>
    </div>
  </div>

  <div class="site-bg" aria-hidden="true"></div>
  <div class="site-overlay" aria-hidden="true"></div>

  <div class="relative z-10 min-h-screen flex flex-col">
    @include('partials.site-header')

    <main class="mx-auto max-w-7xl px-4 py-10 w-full flex-1">
      @yield('content')
    </main>

    @include('partials.site-footer')
  </div>
</body>
</html>
