@extends('layouts.site')

@section('title', ($course->title ?? 'Curso') . ' | Cursos de Ingeniería')

@section('content')
  <section class="grid md:grid-cols-2 gap-8">
    <div class="glass rounded-2xl border border-white/10 overflow-hidden">
      <div class="aspect-[3/2] bg-white/5 flex items-center justify-center">
        @if($course->coverUrl())
          <img class="w-full h-full object-contain" src="{{ $course->coverUrl() }}" alt="">
        @endif
      </div>

      <div class="p-6">
        <h1 class="text-3xl font-extrabold">{{ $course->title }}</h1>
        <p class="mt-2 text-white/75">{{ $course->short_desc }}</p>

        {{-- ✅ PRECIO ÚNICO (Pago único + Acceso de por vida) --}}
        <div class="mt-5 glass p-5 rounded-xl border border-white/10">
          @php
            $priceCurrent = $course->price_anual !== null ? (float)$course->price_anual : null;
            $pricePrevious = $course->price_previous !== null ? (float)$course->price_previous : null;
            $discountPct = null;
            if ($pricePrevious !== null && $priceCurrent !== null && $pricePrevious > 0) {
              $raw = (($pricePrevious - $priceCurrent) / $pricePrevious) * 100;
              $discountPct = (int) round(max(0, $raw));
            }
          @endphp

          <div class="flex items-start justify-between gap-3">
            <h3 class="font-semibold">Precio</h3>
            @if($pricePrevious !== null && $priceCurrent !== null)
              <div class="flex items-center gap-2">
                <span class="price-old">S/ {{ number_format($pricePrevious, 2) }}</span>
                <span class="discount-badge">{{ $discountPct }}% DSCTO</span>
              </div>
            @endif
          </div>

          @if(!is_null($course->price_anual))
            <div class="mt-3 flex items-center justify-between gap-3">
              <div>
                <div class="text-white/70 text-sm">Pago único</div>
                <div class="text-2xl font-extrabold">
                  S/ {{ number_format((float)$course->price_anual, 2) }}
                </div>
              </div>
              <span class="chip chip-accent">Acceso de por vida</span>
            </div>

            <div class="mt-2 text-white/70 text-sm">
              Incluye acceso permanente al material y actualizaciones.
            </div>
          @else
            <p class="mt-2 text-white/70">Precio disponible por WhatsApp.</p>
          @endif
        </div>
      </div>
    </div>

    <div class="space-y-6">
      <div class="glass p-6 rounded-2xl border border-white/10">
        <h2 class="font-semibold text-xl">Contenido</h2>
        @php
          $desc = $course->description ?? '';
          $descHtml = ($desc && strip_tags($desc) === $desc) ? nl2br(e($desc)) : $desc;
        @endphp
        <div class="mt-3 text-white/80 wysiwyg-content">{!! $descHtml !!}</div>
      </div>

      <div class="glass p-6 rounded-2xl border border-white/10">
        <h2 class="font-semibold text-xl">Para quién es</h2>
        <p class="mt-3 text-white/80 whitespace-pre-line">{{ $course->audience }}</p>
      </div>

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

  {{-- Temario --}}
  <section id="temario" class="mt-10 glass p-6 rounded-2xl border border-white/10">
    <h2 class="text-2xl font-bold">Temario</h2>

    @php
      $syllabus = $course->syllabus ?? null;
    @endphp

    @if(is_string($syllabus) && trim($syllabus) !== '')
      @php
        $syllabusHtml = (strip_tags($syllabus) === $syllabus) ? nl2br(e($syllabus)) : $syllabus;
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
    @else
      <p class="mt-3 text-white/70">Temario disponible pronto.</p>
    @endif
  </section>

  {{-- Muestras --}}
  <section class="mt-10">
    <h2 class="text-2xl font-bold">Muestras del curso</h2>
    <div class="mt-4 grid md:grid-cols-4 gap-4">
      @forelse($course->images as $img)
        <a href="{{ $img->url() }}" target="_blank" class="glass rounded-2xl overflow-hidden border border-white/10">
          <img class="w-full h-40 object-cover" src="{{ $img->url() }}" alt="">
        </a>
      @empty
        <p class="text-white/70">No hay muestras todavía.</p>
      @endforelse
    </div>
  </section>

  {{-- WhatsApp final obligatorio --}}
  @php
    $wa = 'https://wa.me/'.env('WHATSAPP_NUMBER').'?text='.urlencode($course->whatsappText());
  @endphp

  <section class="mt-12 text-center">
    <a class="btn btn-accent text-lg px-8 py-4" target="_blank" href="{{ $wa }}">
      Me interesa este curso (WhatsApp)
    </a>
  </section>
@endsection

