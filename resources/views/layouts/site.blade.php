<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>@yield('title', config('shop.business.name'))</title>
  <meta name="description" content="@yield('meta_description', 'Cursos y contenido digital organizado por áreas de especialidad.')">
  <link rel="icon" href="{{ asset('images/logo.webp') }}">

  @vite(['resources/css/app.css','resources/js/app.js'])
</head>

<body class="min-h-screen bg-tech text-white flex flex-col" style="--site-bg-image: url('{{ asset('images/fondo.webp') }}'); --site-bg-x: -32px; --site-bg-x-lg: -42px; --site-bg-x-2xl: -52px;">
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
