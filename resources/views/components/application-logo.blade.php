<picture>
    <source type="image/webp" srcset="{{ asset('images/logo.webp') }}">
    <img
        src="{{ asset('images/logo.png') }}"
        alt="Cursos de Ingenier?a"
        {{ $attributes->merge(['class' => 'object-contain']) }}
    >
</picture>
