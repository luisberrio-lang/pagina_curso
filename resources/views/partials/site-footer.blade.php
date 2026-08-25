<footer class="border-t border-white/10 py-8 text-white/70">
  <div class="mx-auto max-w-7xl px-4 grid gap-6 md:grid-cols-[1fr,2fr] md:items-start">
    <div>
      <div class="font-semibold text-white">{{ config('shop.business.name') }}</div>
      <p class="mt-2 text-sm">{{ config('shop.business.support_text') }}</p>
    </div>

    <nav aria-label="Información legal" class="flex flex-wrap gap-x-5 gap-y-3 text-sm md:justify-end">
      <a class="hover:text-white" href="{{ route('legal.terms') }}">Términos y condiciones</a>
      <a class="hover:text-white" href="{{ route('legal.privacy') }}">Privacidad</a>
      <a class="hover:text-white" href="{{ route('legal.refunds') }}">Cambios y reembolsos</a>
      <a class="hover:text-white" href="{{ route('legal.delivery') }}">Entrega y acceso</a>
      <a class="hover:text-white" href="{{ route('contact') }}">Contacto</a>
    </nav>
  </div>
</footer>
