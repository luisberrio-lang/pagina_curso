@extends('layouts.site')

@section('title', ($course->title ?? 'Curso') . ' | ' . config('shop.business.name'))
@section('meta_description', $course->short_desc ?: 'Información, precio y contenido del curso digital.')

@section('content')
  <section class="grid md:grid-cols-2 gap-8 items-start">
    <div class="glass rounded-2xl border border-white/10 overflow-hidden md:sticky md:top-10">
      <div class="h-[360px] bg-white/5 flex items-center justify-center">
        @if($course->coverUrl())
          <img class="w-full h-full object-fill" src="{{ $course->coverUrl() }}" width="900" height="500" decoding="async" alt="Portada de {{ $course->title }}">
        @endif
      </div>

      <div class="p-6">
        <h1 class="text-3xl font-extrabold">{{ $course->title }}</h1>
        <p class="mt-2 text-white/75">{{ $course->short_desc }}</p>

        {{-- ✅ PRECIO ÚNICO (Pago único + Acceso de por vida) --}}
        <div class="mt-5 glass p-5 rounded-xl border border-white/10">
          @php
            $priceCurrent = $course->currentPrice();
            $pricePrevious = $course->previousPrice();
            $discountPct = $course->discountPercentage();
          @endphp

          <div class="flex items-start justify-between gap-3">
            <h3 class="font-semibold">Precio</h3>
            @if($discountPct !== null)
              <div class="flex items-center gap-2">
                <span class="price-old">{{ $course->formattedPreviousPrice() }}</span>
                <span class="discount-badge">{{ $discountPct }}% DSCTO</span>
              </div>
            @endif
          </div>

          @if($course->hasCommercialPrice())
            <div class="mt-3 flex items-center justify-between gap-3">
              <div>
                <div class="text-white/70 text-sm">Pago único</div>
                <div class="text-2xl font-extrabold">
                  {{ $course->formattedCurrentPrice() }}
                </div>
              </div>
              <span class="chip chip-accent">Contenido digital</span>
            </div>

            <div class="mt-2 text-white/70 text-sm">
              Producto digital. La entrega y el acceso se coordinan según la política publicada del sitio.
            </div>

            @php
              $orderMessage = "Hola, deseo realizar el pedido de este curso:\n{$course->title}\nPrecio: {$course->formattedCurrentPrice()}\nEnlace: ".route('courses.show', $course);
              $orderWhatsappUrl = 'https://wa.me/'.config('shop.business.whatsapp').'?text='.rawurlencode($orderMessage);
            @endphp

            <div class="mt-4 grid gap-3">
              <a class="btn-whatsapp w-full gap-2" target="_blank" rel="noopener" href="{{ $orderWhatsappUrl }}" data-detail-whatsapp>
                <picture>
                  <source type="image/webp" srcset="{{ asset('images/watsapp.webp') }}">
                  <img src="{{ asset('images/watsapp.webp') }}" alt="" class="h-6 w-6 object-contain" loading="lazy" decoding="async">
                </picture>
                <span>Pedir por WhatsApp</span>
              </a>

              <form method="POST" action="{{ route('cart.store', $course) }}">
                @csrf
                <button class="btn btn-accent w-full gap-2" type="submit">
                  <svg data-add-cart-icon aria-hidden="true" class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.75 4.75h2l1.7 9.2a2 2 0 001.97 1.64h8.93a2 2 0 001.95-1.57l1.2-5.52H6.1" />
                    <circle cx="9" cy="19" r="1.25" />
                    <circle cx="17.5" cy="19" r="1.25" />
                  </svg>
                  <span>Agregar al carrito</span>
                </button>
              </form>
            </div>
          @else
            <p class="mt-2 text-white/70">Precio disponible por WhatsApp.</p>
          @endif
        </div>
      </div>
    </div>

    <div class="space-y-6">
      @if(filled($course->description))
      <div class="glass p-6 rounded-2xl border border-white/10">
        <h2 class="font-semibold text-xl">Contenido</h2>
        @php
          $desc = $course->description ?? '';
          $descHtml = ($desc && strip_tags($desc) === $desc)
            ? nl2br(e($desc))
            : \App\Support\SafeHtml::sanitize($desc);
        @endphp
        <div class="mt-3 text-white/80 wysiwyg-content">{!! $descHtml !!}</div>
      </div>
      @endif

      @if(filled($course->audience))
      <div class="glass p-6 rounded-2xl border border-white/10">
        <h2 class="font-semibold text-xl">Para quién es</h2>
        <p class="mt-3 text-white/80 whitespace-pre-line">{{ $course->audience }}</p>
      </div>
      @endif

      @if(is_array($course->learning) && count($course->learning))
        <div class="glass p-6 rounded-2xl border border-white/10">
          <h2 class="font-semibold text-xl">Qué aprenderás</h2>
          <ul class="mt-3 text-white/80 list-disc list-inside space-y-1">
            @foreach($course->learning as $it) <li>{{ $it }}</li> @endforeach
          </ul>
        </div>
      @endif

      @if(is_array($course->benefits) && count($course->benefits))
        <div class="glass p-6 rounded-2xl border border-white/10">
          <h2 class="font-semibold text-xl">Beneficios</h2>
          <ul class="mt-3 text-white/80 list-disc list-inside space-y-1">
            @foreach($course->benefits as $it) <li>{{ $it }}</li> @endforeach
          </ul>
        </div>
      @endif

      @if(is_array($course->includes) && count($course->includes))
        <div class="glass p-6 rounded-2xl border border-white/10">
          <h2 class="font-semibold text-xl">Qué incluye (extras)</h2>
          <ul class="mt-3 text-white/80 list-disc list-inside space-y-1">
            @foreach($course->includes as $it) <li>{{ $it }}</li> @endforeach
          </ul>
        </div>
      @endif
    </div>
  </section>

  @php
    $syllabus = $course->syllabus ?? null;
    $hasSyllabus = (is_string($syllabus) && trim($syllabus) !== '') || (is_array($syllabus) && count($syllabus));
  @endphp

  @if($hasSyllabus)
  <section id="temario" class="mt-10 glass p-6 rounded-2xl border border-white/10">
    <h2 class="text-2xl font-bold">Temario</h2>

    @if(is_string($syllabus) && trim($syllabus) !== '')
      @php
        $syllabusHtml = (strip_tags($syllabus) === $syllabus)
          ? nl2br(e($syllabus))
          : \App\Support\SafeHtml::sanitize($syllabus);
      @endphp
      <div class="mt-4 wysiwyg-content">{!! $syllabusHtml !!}</div>
    @elseif(is_array($syllabus) && count($syllabus))
      <div class="mt-4 space-y-4">
        @foreach($syllabus as $m)
          <div class="p-5 rounded-2xl border border-white/10 bg-white/5">
            <div class="font-semibold">{{ $m['title'] ?? 'Módulo' }}</div>
            @if(!empty($m['topics']) && is_array($m['topics']))
              <ul class="mt-2 text-white/80 list-disc list-inside space-y-1">
                @foreach($m['topics'] as $t) <li>{{ $t }}</li> @endforeach
              </ul>
            @endif
          </div>
        @endforeach
      </div>
    @endif
  </section>
  @endif

  @php
    $availableImages = $course->images
      ->map(fn ($image) => ['model' => $image, 'url' => $image->url()])
      ->filter(fn ($image) => $image['url'] !== null);
  @endphp
  @if($availableImages->isNotEmpty())
  <section class="mt-10">
    <h2 class="text-2xl font-bold">Muestras del curso</h2>
    <div class="mt-4 grid md:grid-cols-4 gap-4">
      @foreach($availableImages as $image)
        <a href="{{ $image['url'] }}" target="_blank" rel="noopener" class="glass rounded-2xl overflow-hidden border border-white/10">
          <img class="w-full h-40 object-cover" src="{{ $image['url'] }}" width="900" height="500" loading="lazy" decoding="async" alt="Muestra de {{ $course->title }}">
        </a>
      @endforeach
    </div>
  </section>
  @endif

  {{-- WhatsApp final obligatorio --}}
  @php
    $wa = 'https://wa.me/'.config('shop.business.whatsapp').'?text='.urlencode($course->whatsappText());
  @endphp

  <section class="mt-12 text-center">
    <a class="btn-whatsapp text-lg px-8 py-4 inline-flex items-center gap-3" target="_blank" href="{{ $wa }}">
      <picture>
        <source type="image/webp" srcset="{{ asset('images/watsapp.webp') }}">
        <img src="{{ asset('images/watsapp.webp') }}" alt="WhatsApp" class="h-[2.3rem] w-[2.3rem] object-contain" loading="lazy" decoding="async">
      </picture>
      <span>Me interesa este curso (WhatsApp)</span>
    </a>
  </section>
@endsection
