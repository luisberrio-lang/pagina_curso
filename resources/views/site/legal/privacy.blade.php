@extends('layouts.site')

@section('title', 'Política de privacidad | '.config('shop.business.name'))
@section('meta_description', 'Información general sobre los datos tratados al utilizar el sitio y sus cuentas de usuario.')

@section('content')
  <article class="glass p-6 md:p-8 rounded-3xl border border-white/10 max-w-4xl mx-auto">
    <h1 class="text-3xl font-extrabold">Política de privacidad</h1>
    <div class="mt-6 space-y-6 text-white/80 leading-relaxed">
      <section>
        <h2 class="text-xl font-semibold text-white">Datos utilizados por el sitio</h2>
        <p class="mt-2">Si creas una cuenta, el sitio trata el nombre y correo que proporcionas. La contraseña se almacena mediante un mecanismo de hash. Al crear una orden, se registran el nombre completo, correo, celular y el detalle comercial de la orden. También pueden tratarse datos técnicos de sesión, como dirección IP, agente de usuario y actividad necesaria para mantener la sesión.</p>
      </section>
      <section>
        <h2 class="text-xl font-semibold text-white">Finalidad</h2>
        <p class="mt-2">Estos datos se utilizan para autenticar usuarios, proteger el acceso, crear y gestionar órdenes, atender solicitudes y mantener el funcionamiento del sitio. El checkout actual no solicita ni almacena números de tarjeta, CVV, credenciales bancarias ni tokens de pago.</p>
      </section>
      <section>
        <h2 class="text-xl font-semibold text-white">Canales externos</h2>
        <p class="mt-2">Al contactar mediante WhatsApp u otro enlace externo, la comunicación también queda sujeta a las condiciones y políticas de ese servicio.</p>
      </section>
      <section>
        <h2 class="text-xl font-semibold text-white">Consultas sobre datos</h2>
        <p class="mt-2">Puedes utilizar los canales de <a class="text-cyan-300 underline" href="{{ route('contact') }}">contacto</a> para solicitar información relacionada con tus datos o con una cuenta registrada.</p>
      </section>
    </div>
  </article>
@endsection
