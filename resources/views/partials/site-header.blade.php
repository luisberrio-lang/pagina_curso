@php
  $waBase = 'https://wa.me/'.env('WHATSAPP_NUMBER');
  $fbUrl  = env('FACEBOOK_URL') ?: '#';
@endphp

<header class="sticky top-0 z-50 border-b border-white/10 bg-black/40 backdrop-blur">
  <div class="mx-auto max-w-7xl px-4 py-3 flex items-center gap-4">

    {{-- Izquierda: Logo --}}
    <a href="{{ route('home') }}" class="flex items-center gap-2">
      <div class="h-9 w-9 rounded-xl bg-gradient-to-br from-cyan-400 to-blue-600 shadow-glow"></div>
      <span class="font-semibold tracking-wide">{{ config('app.name') }}</span>
    </a>

    {{-- Centro: Menú (SIN "Mi perfil") --}}
    <nav class="hidden md:flex items-center gap-6 mx-auto">
      <a class="navlink" href="{{ route('home') }}">Inicio</a>
      <a class="navlink" href="{{ route('courses.index') }}">Programas/Cursos</a>
      <a class="navlink" href="{{ route('price') }}">Precio</a>
      <a class="navlink" href="{{ route('faq') }}">FAQ</a>

      {{-- Solo admin ve Dashboard --}}
      @if(auth()->check() && auth()->user()->is_admin)
        <a class="navlink" href="{{ route('admin.dashboard') }}">Dashboard</a>
      @endif
    </nav>

    {{-- Derecha --}}
    <div class="ml-auto flex items-center gap-3">

      <a class="iconbtn" target="_blank"
         href="{{ $waBase }}?text={{ urlencode('Hola, deseo información.') }}"
         title="WhatsApp">
        <span class="font-bold">WA</span>
      </a>

      <a class="iconbtn" target="_blank" href="{{ $fbUrl }}" title="Facebook">
        <span class="font-bold">FB</span>
      </a>

      @guest
        <a class="chip" href="{{ route('register') }}">Crear cuenta</a>
        <a class="chip chip-accent" href="{{ route('login') }}">Iniciar sesión</a>
      @endguest

      @auth
        <form method="POST" action="{{ route('logout') }}">
          @csrf
          <button class="chip chip-accent" type="submit">Cerrar sesión</button>
        </form>
      @endauth

    </div>
  </div>
</header>
