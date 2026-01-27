<header class="sticky top-0 z-50 border-b border-white/10 bg-black/40 backdrop-blur">
  <div class="w-full px-2 md:px-6 py-3 grid grid-cols-[auto,1fr,auto] items-center gap-4">

    {{-- Izquierda: Logo --}}
    <a href="{{ route('home') }}" class="flex items-center gap-2">
      <picture>
        <source type="image/webp" srcset="{{ asset('images/logo.webp') }}">
        <img
          src="{{ asset('images/logo.png') }}"
          alt="Cursos de Ingenier?a"
          class="h-9 w-9 rounded-xl object-contain shadow-glow"
        >
      </picture>
      <span class="font-semibold tracking-wide">
        Cursos de <span class="text-cyan-300 font-semibold">Ingeniería</span>
      </span>
    </a>

    {{-- Centro: Menú --}}
    {{-- ✅ Móvil: mostrar Programas/Cursos (SIEMPRE ese texto) --}}
    <div class="md:hidden flex items-center justify-center">
      <a class="navlink inline-flex items-center gap-2 whitespace-nowrap"
         href="{{ route('courses.index') }}">
        <svg aria-hidden="true" class="h-4 w-4 text-cyan-200" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
          <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 4.5h10.5a2.25 2.25 0 012.25 2.25v11.25a2.25 2.25 0 00-2.25-2.25H3.75M3.75 4.5A2.25 2.25 0 001.5 6.75v11.25a2.25 2.25 0 002.25 2.25h10.5M12.75 4.5h7.5a2.25 2.25 0 012.25 2.25v11.25a2.25 2.25 0 00-2.25-2.25h-7.5"/>
        </svg>
        <span>Programas/Cursos</span>
      </a>
    </div>

    {{-- ✅ Desktop: Menú completo (SIN "Mi perfil") --}}
    <nav class="hidden md:flex items-center justify-center gap-6">
      <a class="navlink inline-flex items-center gap-2" href="{{ route('home') }}">
        <svg aria-hidden="true" class="h-4 w-4 text-cyan-200" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
          <path stroke-linecap="round" stroke-linejoin="round" d="M3 10.5L12 3l9 7.5M5.5 9.5V21h13V9.5"/>
        </svg>
        Inicio
      </a>

      <a class="navlink inline-flex items-center gap-2" href="{{ route('courses.index') }}">
        <svg aria-hidden="true" class="h-4 w-4 text-cyan-200" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
          <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 4.5h10.5a2.25 2.25 0 012.25 2.25v11.25a2.25 2.25 0 00-2.25-2.25H3.75M3.75 4.5A2.25 2.25 0 001.5 6.75v11.25a2.25 2.25 0 002.25 2.25h10.5M12.75 4.5h7.5a2.25 2.25 0 012.25 2.25v11.25a2.25 2.25 0 00-2.25-2.25h-7.5"/>
        </svg>
        Programas/Cursos
      </a>

      <a class="navlink inline-flex items-center gap-2" href="{{ route('price') }}">
        <span aria-hidden="true" class="inline-flex h-5 w-5 items-center justify-center rounded-full border border-cyan-200 text-[10px] font-semibold text-cyan-200">
          S/
        </span>
        Precios
      </a>

      <a class="navlink inline-flex items-center gap-2" href="{{ route('faq') }}">
        <svg aria-hidden="true" class="h-4 w-4 text-cyan-200" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
          <circle cx="12" cy="12" r="9" />
          <path stroke-linecap="round" stroke-linejoin="round" d="M9.3 9a2.7 2.7 0 115.4 0c0 1.6-1.8 2-1.8 3.4M12 17.2h.01"/>
        </svg>
        FAQ
      </a>

      {{-- Solo admin ve Dashboard --}}
      @if(auth()->check() && auth()->user()->is_admin)
        <a class="navlink" href="{{ route('admin.dashboard') }}">Dashboard</a>
      @endif
    </nav>

    {{-- Derecha --}}
    <div class="flex items-center gap-3 justify-end">

      {{-- ✅ Desktop (md+): tu bloque original (WA/FB + auth) --}}
      <div class="hidden md:flex items-center gap-3">

        <a class="iconbtn" target="_blank" rel="noopener"
           href="https://wa.me/51929765265"
           title="WhatsApp">
          <picture>
            <source type="image/webp" srcset="{{ asset('images/watsapp.webp') }}">
            <img src="{{ asset('images/watsapp.png') }}" alt="WhatsApp" class="h-full w-full object-contain scale-125" loading="lazy">
          </picture>
        </a>

        <a class="iconbtn" target="_blank" rel="noopener"
           href="https://www.facebook.com/share/1CMUNbgoXX/"
           title="Facebook">
          <picture>
            <source type="image/webp" srcset="{{ asset('images/facebook.webp') }}">
            <img src="{{ asset('images/facebook.png') }}" alt="Facebook" class="h-full w-full object-contain scale-125" loading="lazy">
          </picture>
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

      {{-- ✅ Móvil: mostrar WA/FB en la barra (además de la hamburguesa) --}}
      <div class="md:hidden flex items-center gap-2">
        <a class="iconbtn" target="_blank" rel="noopener"
           href="https://wa.me/51929765265"
           title="WhatsApp">
          <picture>
            <source type="image/webp" srcset="{{ asset('images/watsapp.webp') }}">
            <img src="{{ asset('images/watsapp.png') }}" alt="WhatsApp" class="h-full w-full object-contain scale-125" loading="lazy">
          </picture>
        </a>

        <a class="iconbtn" target="_blank" rel="noopener"
           href="https://www.facebook.com/share/1CMUNbgoXX/"
           title="Facebook">
          <picture>
            <source type="image/webp" srcset="{{ asset('images/facebook.webp') }}">
            <img src="{{ asset('images/facebook.png') }}" alt="Facebook" class="h-full w-full object-contain scale-125" loading="lazy">
          </picture>
        </a>
      </div>

      {{-- ✅ Móvil: botón hamburguesa --}}
      <button id="mobileMenuBtn" type="button"
              class="md:hidden iconbtn"
              aria-controls="mobileMenu"
              aria-expanded="false"
              title="Menú">
        <svg aria-hidden="true" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
          <path stroke-linecap="round" d="M4 7h16M4 12h16M4 17h16"/>
        </svg>
      </button>

    </div>
  </div>

  {{-- ✅ Menú móvil: contiene links + WA/FB + auth --}}
  <div id="mobileMenu" class="md:hidden hidden border-t border-white/10 bg-black/60 backdrop-blur">
    <div class="px-2 py-3 space-y-3">

      {{-- Links del menú (mobile) --}}
      <nav class="space-y-2">
        <a class="block rounded-xl px-3 py-2 text-white/85 hover:bg-white/10 transition"
           href="{{ route('home') }}">Inicio</a>

        <a class="block rounded-xl px-3 py-2 text-white/85 hover:bg-white/10 transition"
           href="{{ route('courses.index') }}">Programas/Cursos</a>

        <a class="block rounded-xl px-3 py-2 text-white/85 hover:bg-white/10 transition"
           href="{{ route('price') }}">Precios</a>

        <a class="block rounded-xl px-3 py-2 text-white/85 hover:bg-white/10 transition"
           href="{{ route('faq') }}">FAQ</a>

        {{-- Solo admin ve Dashboard (mobile) --}}
        @if(auth()->check() && auth()->user()->is_admin)
          <a class="block rounded-xl px-3 py-2 text-white/85 hover:bg-white/10 transition"
             href="{{ route('admin.dashboard') }}">Dashboard</a>
        @endif
      </nav>

      <div class="h-px bg-white/10"></div>

      {{-- WA / FB en móvil (también quedan aquí, además de la barra) --}}
      <div class="flex items-center gap-3">
        <a class="iconbtn" target="_blank" rel="noopener"
           href="https://wa.me/51929765265"
           title="WhatsApp">
          <picture>
            <source type="image/webp" srcset="{{ asset('images/watsapp.webp') }}">
            <img src="{{ asset('images/watsapp.png') }}" alt="WhatsApp" class="h-full w-full object-contain scale-125" loading="lazy">
          </picture>
        </a>

        <a class="iconbtn" target="_blank" rel="noopener"
           href="https://www.facebook.com/share/1CMUNbgoXX/"
           title="Facebook">
          <picture>
            <source type="image/webp" srcset="{{ asset('images/facebook.webp') }}">
            <img src="{{ asset('images/facebook.png') }}" alt="Facebook" class="h-full w-full object-contain scale-125" loading="lazy">
          </picture>
        </a>
      </div>

      {{-- Auth en móvil --}}
      <div class="space-y-2">
        @guest
          <a class="chip w-full justify-center" href="{{ route('register') }}">Crear cuenta</a>
          <a class="chip chip-accent w-full justify-center" href="{{ route('login') }}">Iniciar sesión</a>
        @endguest

        @auth
          <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button class="chip chip-accent w-full justify-center" type="submit">Cerrar sesión</button>
          </form>
        @endauth
      </div>

    </div>
  </div>

  {{-- ✅ Script: abrir/cerrar menú móvil --}}
  <script>
    (function () {
      const btn  = document.getElementById('mobileMenuBtn');
      const menu = document.getElementById('mobileMenu');
      if (!btn || !menu) return;

      function closeMenu() {
        menu.classList.add('hidden');
        btn.setAttribute('aria-expanded', 'false');
      }

      btn.addEventListener('click', function () {
        const isOpen = !menu.classList.contains('hidden');
        if (isOpen) closeMenu();
        else {
          menu.classList.remove('hidden');
          btn.setAttribute('aria-expanded', 'true');
        }
      });

      document.addEventListener('click', function (e) {
        if (menu.classList.contains('hidden')) return;
        if (menu.contains(e.target) || btn.contains(e.target)) return;
        closeMenu();
      });

      window.addEventListener('resize', function () {
        if (window.innerWidth >= 768) closeMenu(); // md breakpoint
      });
    })();
  </script>

</header>
