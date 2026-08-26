<picture>
    <source type="image/webp" srcset="{{ asset('images/logo.webp') }}">
    <img
        src="{{ asset('images/logo.webp') }}"
        alt="{{ config('shop.business.name') }}"
        decoding="async"
        {{ $attributes->merge(['class' => 'object-contain']) }}
    >
</picture>
