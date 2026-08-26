@extends('layouts.site')

@section('title', 'Términos y condiciones | '.config('shop.business.name'))
@section('meta_description', 'Condiciones generales aplicables al catálogo y acceso a los cursos digitales.')

@section('content')
  <article class="glass p-6 md:p-8 rounded-3xl border border-white/10 max-w-4xl mx-auto">
    <h1 class="text-3xl font-extrabold">Términos y condiciones</h1>
    <p class="mt-3 text-white/70">Condiciones aplicables al uso del sitio y a la adquisición de cursos y contenidos digitales.</p>

    <div class="mt-6 space-y-6 text-white/80 leading-relaxed">
      <section>
        <h2 class="text-xl font-semibold text-white">Alcance</h2>
        <p class="mt-2">Estos términos regulan el uso del sitio de {{ config('shop.business.name') }}, la adquisición de cursos, el acceso a contenidos digitales y la relación comercial con el comprador. Al confirmar un pedido, el comprador declara haber revisado la información del curso y estas condiciones.</p>
      </section>

      <section>
        <h2 class="text-xl font-semibold text-white">Catálogo y precios</h2>
        <p class="mt-2">Los cursos disponibles, sus características y sus precios vigentes se muestran en el catálogo. Los precios comerciales se expresan en {{ config('shop.currency') }}. Cuando corresponda, puede mostrarse un precio anterior como referencia promocional junto al precio vigente.</p>
        <p class="mt-2">Antes de adquirir un curso, el comprador debe revisar su descripción, contenido, modalidad e información de acceso. La información comercial aplicable es la publicada en el sitio al momento de confirmar el pedido.</p>
      </section>

      <section>
        <h2 class="text-xl font-semibold text-white">Acceso y entrega digital</h2>
        <p class="mt-2">Los productos ofrecidos son principalmente cursos y materiales digitales. No existe envío físico, salvo indicación expresa en la descripción de un producto. El acceso o la entrega se realiza mediante el mecanismo informado para cada curso y actualmente puede coordinarse a través de los canales comerciales disponibles.</p>
        <p class="mt-2">La información completa sobre esta modalidad se encuentra en la <a class="text-cyan-300 underline" href="{{ route('legal.delivery') }}">política de entrega y acceso digital</a>.</p>
      </section>

      <section>
        <h2 class="text-xl font-semibold text-white">Uso personal del contenido</h2>
        <p class="mt-2">El acceso a los cursos y materiales adquiridos es de carácter personal y está destinado al comprador. Los contenidos pueden utilizarse con fines de aprendizaje, consulta y desarrollo profesional.</p>
      </section>

      <section>
        <h2 class="text-xl font-semibold text-white">Propiedad intelectual y restricciones</h2>
        <p class="mt-2">Los cursos y materiales deben respetarse conforme a las autorizaciones comunicadas para su uso. Sin autorización expresa, no está permitido:</p>
        <ul class="mt-3 list-disc pl-6 space-y-2">
          <li>revender cursos o comercializar total o parcialmente su contenido;</li>
          <li>redistribuir materiales o compartir públicamente los archivos;</li>
          <li>reproducir el contenido con fines comerciales;</li>
          <li>publicar copias en otras plataformas; ni</li>
          <li>facilitar a terceros el acceso personal adquirido por el comprador.</li>
        </ul>
      </section>

      <section>
        <h2 class="text-xl font-semibold text-white">Responsabilidad del comprador</h2>
        <p class="mt-2">El comprador debe revisar el contenido y la modalidad del curso antes de adquirirlo, proporcionar información válida para la coordinación y el soporte, utilizar los materiales de forma adecuada y respetar las condiciones de acceso indicadas.</p>
      </section>

      <section>
        <h2 class="text-xl font-semibold text-white">Cambios, devoluciones y reembolsos</h2>
        <p class="mt-2">Las solicitudes relacionadas con cambios, devoluciones o reembolsos se revisan conforme a la <a class="text-cyan-300 underline" href="{{ route('legal.refunds') }}">política de cambios, devoluciones y reembolsos</a> publicada en el sitio.</p>
      </section>

      <section>
        <h2 class="text-xl font-semibold text-white">Privacidad</h2>
        <p class="mt-2">El tratamiento general de los datos proporcionados al utilizar el sitio se describe en la <a class="text-cyan-300 underline" href="{{ route('legal.privacy') }}">política de privacidad</a>.</p>
      </section>

      <section>
        <h2 class="text-xl font-semibold text-white">Consultas y soporte</h2>
        <p class="mt-2">Para consultar sobre contenido, acceso, modalidad, soporte o condiciones particulares de un curso, utiliza los canales oficiales disponibles en la <a class="text-cyan-300 underline" href="{{ route('contact') }}">página de contacto</a>.</p>
      </section>
    </div>
  </article>
@endsection
