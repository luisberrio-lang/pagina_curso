<picture>
    <source type="image/webp" srcset="{{ asset('images/logo.webp') }}">
    <img
        src="{{ asset('images/logo.webp') }}"
        alt="Cursos de Ingeniería"
        decoding="async"
        {{ $attributes->merge(['class' => 'object-contain']) }}
    >
</picture>
