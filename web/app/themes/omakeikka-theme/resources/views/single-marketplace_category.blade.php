@extends('layouts.app')

@section('content')
@while(have_posts())
@php
  the_post();
@endphp

<article {{ post_class('marketplace-single') }}>

  {{-- Hero --}}
  <section class="marketplace-hero py-5">
    <div class="container">
      <div class="row align-items-center g-4">

        <div class="col-lg-7">
          @if(!empty($category_icon))
            <span class="marketplace-grid__icon mb-3" aria-hidden="true">
              <i class="{{ $category_icon }}"></i>
            </span>
          @endif
          <h1 class="marketplace-hero__title display-5 fw-bold text-white">
            {{ sprintf( __('Tarvitsetko apua: %s', 'omakeikka-theme'), $category_title_lc ) }}
          </h1>
          @if(get_the_excerpt())
            <p class="marketplace-hero__sub lead text-white mt-3">{{ get_the_excerpt() }}</p>
          @else
            <p class="marketplace-hero__sub lead text-white mt-3">
              {{ sprintf( __('Julkaise tehtävä palvelutorille — kokeneet eläkeläiset tarjoavat apuaan tunneissa. Sovi hinta ja yksityiskohdat suoraan tekijän kanssa.', 'omakeikka-theme') ) }}
            </p>
          @endif
          <ol class="marketplace-steps list-unstyled d-flex flex-column flex-sm-row gap-3 mt-4 mb-0">
            <li class="d-flex align-items-center gap-2 text-white">
              <span class="marketplace-steps__number">1</span>
              {{ __('Rekisteröidy palveluun', 'omakeikka-theme') }}
            </li>
            <li class="d-flex align-items-center gap-2 text-white">
              <span class="marketplace-steps__number">2</span>
              {{ __('Julkaise tehtävä', 'omakeikka-theme') }}
            </li>
            <li class="d-flex align-items-center gap-2 text-white">
              <span class="marketplace-steps__number">3</span>
              {{ __('Valitse tekijä', 'omakeikka-theme') }}
            </li>
          </ol>
        </div>

        <div class="col-lg-5">
          <div class="card shadow-lg border-0 p-4">
            <h2 class="h5 mb-1">{{ sprintf( __('Etsitkö tekijää tehtävään: %s?', 'omakeikka-theme'), $category_title_lc ) }}</h2>
            <p class="text-muted small mb-3">{{ __('Julkaise tehtävä palvelutorille ja saat vastauksia jo tunneissa. Kategoria on valmiina lomakkeella.', 'omakeikka-theme') }}</p>
            <a href="{{ esc_url( $app_create_url ) }}" class="btn btn-primary w-100 fw-semibold" rel="noopener">
              {{ __('Julkaise tehtävä ilmaiseksi', 'omakeikka-theme') }}
              <i class="bi bi-arrow-right ms-2"></i>
            </a>
            <hr class="my-3">
            <p class="text-muted small text-center mb-2">{{ __('Etkö ole vielä rekisteröitynyt?', 'omakeikka-theme') }}</p>
            <a href="https://app.omakeikka.fi/register" class="btn btn-outline-primary w-100" rel="noopener">
              {{ __('Luo tili omakeikkaan', 'omakeikka-theme') }}
            </a>
          </div>
        </div>

      </div>
    </div>
  </section>

  {{-- Trust strip --}}
  <section class="marketplace-trust py-4 border-bottom bg-light">
    <div class="container">
      <div class="row g-3 text-center">
        <div class="col-6 col-md-3">
          <div class="marketplace-trust__item p-3 rounded border h-100">
            <i class="bi bi-shield-check fs-3 text-success"></i>
            <div class="fw-bold mt-2">{{ __('Varmennetut profiilit', 'omakeikka-theme') }}</div>
          </div>
        </div>
        <div class="col-6 col-md-3">
          <div class="marketplace-trust__item p-3 rounded border h-100">
            <i class="bi bi-cash-coin fs-3 text-success"></i>
            <div class="fw-bold mt-2">{{ __('Sovi hinta etukäteen', 'omakeikka-theme') }}</div>
          </div>
        </div>
        <div class="col-6 col-md-3">
          <div class="marketplace-trust__item p-3 rounded border h-100">
            <i class="bi bi-chat-heart fs-3 text-success"></i>
            <div class="fw-bold mt-2">{{ __('Tuki suomeksi', 'omakeikka-theme') }}</div>
          </div>
        </div>
        <div class="col-6 col-md-3">
          <div class="marketplace-trust__item p-3 rounded border h-100">
            <i class="bi bi-geo-alt fs-3 text-success"></i>
            <div class="fw-bold mt-2">{{ __('Tekijä paikallisesti', 'omakeikka-theme') }}</div>
          </div>
        </div>
      </div>
    </div>
  </section>

  {{-- Long-form content (editable in WP admin per category) --}}
  @if(get_the_content())
    <section class="py-5">
      <div class="container">
        <div class="row justify-content-center">
          <div class="col-lg-8">
            {!! apply_filters('the_content', get_the_content()) !!}
          </div>
        </div>
      </div>
    </section>
  @else
    {{-- Fallback intro when category has no body content --}}
    <section class="py-5">
      <div class="container">
        <div class="row justify-content-center">
          <div class="col-lg-8">
            <h2 class="h4 mb-3">{{ sprintf( __('Mitä %s tarkoittaa palvelutorilla?', 'omakeikka-theme'), $category_title_lc ) }}</h2>
            <p>
              {{ sprintf( __('Omakeikan palvelutorilla voit julkaista tehtävän, johon tarvitset tekijää: %s. Tekijät ovat kokeneita eläkeläisiä eri ammateista, ja toimivat kevytyrittäjinä omaan lukuunsa.', 'omakeikka-theme'), $category_title_lc ) }}
            </p>
            <p>
              {{ __('Tehtävän julkaisu on ilmaista. Saat useita vastauksia, voit vertailla profiileja ja arvioita, ja sovit hinnan suoraan valitsemasi tekijän kanssa.', 'omakeikka-theme') }}
            </p>
          </div>
        </div>
      </div>
    </section>
  @endif

  {{-- Mid-page CTA --}}
  <section class="marketplace-cta py-5 text-center">
    <div class="container">
      <h2 class="h3 text-white mb-2">{{ sprintf( __('Aloita: %s', 'omakeikka-theme'), $category_title ) }}</h2>
      <p class="lead text-white mb-4">{{ __('Julkaise tehtävä ilmaiseksi ja saat vastauksia tunneissa.', 'omakeikka-theme') }}</p>
      <div class="d-flex flex-column flex-sm-row gap-2 justify-content-center">
        <a href="{{ esc_url( $app_create_url ) }}" class="btn btn-primary btn-lg fw-semibold" rel="noopener">
          {{ __('Julkaise tehtävä ilmaiseksi', 'omakeikka-theme') }}
          <i class="bi bi-arrow-right ms-2"></i>
        </a>
        <a href="https://app.omakeikka.fi/register" class="btn btn-outline-light btn-lg" rel="noopener">
          {{ __('Rekisteröidy palveluun', 'omakeikka-theme') }}
        </a>
      </div>
    </div>
  </section>

  {{-- Related categories --}}
  @if(!empty($related))
    <section class="py-5 bg-light">
      <div class="container">
        <h2 class="h4 mb-3">
          @if(!empty($category_group))
            {{ sprintf( __('Muut palvelut ryhmässä %s', 'omakeikka-theme'), $category_group['name'] ) }}
          @else
            {{ __('Muut palvelut', 'omakeikka-theme') }}
          @endif
        </h2>
        <div class="row g-3">
          @foreach($related as $rel)
            <div class="col-6 col-md-4 col-lg-3">
              <a
                href="{{ esc_url( $rel['url'] ) }}"
                class="marketplace-grid__card text-decoration-none d-flex flex-column align-items-center text-center h-100"
              >
                @if(!empty($rel['icon_class']))
                  <span class="marketplace-grid__icon mb-3" aria-hidden="true">
                    <i class="{{ $rel['icon_class'] }}"></i>
                  </span>
                @endif
                <span class="marketplace-grid__title">{{ $rel['title'] }}</span>
              </a>
            </div>
          @endforeach
        </div>
      </div>
    </section>
  @endif

</article>

@endwhile
@endsection
