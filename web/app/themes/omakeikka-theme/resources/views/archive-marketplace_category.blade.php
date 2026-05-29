@extends('layouts.app')

@section('content')

<div class="marketplace-archive">

  <header class="marketplace-archive__header marketplace-hero py-5">
    <div class="container">
      <h1 class="marketplace-hero__title display-5 fw-bold text-white mb-3">
        {{ __('Kaikki palvelut palvelutorilla', 'omakeikka-theme') }}
      </h1>
      <p class="marketplace-hero__sub lead text-white mb-0">
        {{ __('Selaa eläkeläisten tarjoamia palveluita, pyydä tarvitsemaasi apua tai jaa omaa osaamistasi vertaisillesi.', 'omakeikka-theme') }}
      </p>
      <div class="mt-4">
        <a href="https://app.omakeikka.fi/register" class="btn btn-primary btn-lg fw-semibold">
          {{ __('Julkaise tehtävä ilmaiseksi', 'omakeikka-theme') }}
          <i class="bi bi-arrow-right ms-2"></i>
        </a>
      </div>
    </div>
  </header>

  {{-- Intro: copy is verbatim from the omakeikka app (resources/ts/Pages/Marketplace/Index.vue) --}}
  <section class="py-5">
    <div class="container">
      <div class="row justify-content-center">
        <div class="col-lg-8">
          <p class="lead text-muted text-center mb-5">
            {{ __('Selaa eläkeläisten tarjoamia palveluita, pyydä tarvitsemaasi apua tai jaa omaa osaamistasi vertaisillesi.', 'omakeikka-theme') }}
          </p>
          <div class="row g-4">
            <div class="col-md-6">
              <h2 class="h4 fw-bold mb-2">{{ __('Tarvitsetko apua?', 'omakeikka-theme') }}</h2>
              <p class="text-muted mb-0">
                {{ __('Valitse palvelu ja julkaise pyyntö. Tekijät vastaavat lähialueeltasi.', 'omakeikka-theme') }}
              </p>
            </div>
            <div class="col-md-6">
              <h2 class="h4 fw-bold mb-2">{{ __('Oletko tekijä?', 'omakeikka-theme') }}</h2>
              <p class="text-muted mb-0">
                {{ __('Tarjoa apuasi ja tienaa lisää.', 'omakeikka-theme') }}
              </p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>

  {{-- How it works --}}
  <section class="py-5 bg-light border-top">
    <div class="container">
      <h2 class="h3 fw-bold text-center mb-5">{{ __('Näin se toimii', 'omakeikka-theme') }}</h2>
      <div class="row g-4 marketplace-steps">
        <div class="col-md-4 text-center">
          <span class="marketplace-steps__number mb-3">1</span>
          <h3 class="marketplace-steps__title h5">{{ __('Julkaise ilmoitus', 'omakeikka-theme') }}</h3>
          <p class="text-muted small">
            {{ __('Kerro mitä tarvitset ja milloin.', 'omakeikka-theme') }}
          </p>
        </div>
        <div class="col-md-4 text-center">
          <span class="marketplace-steps__number mb-3">2</span>
          <h3 class="marketplace-steps__title h5">{{ __('Saat vastauksia', 'omakeikka-theme') }}</h3>
          <p class="text-muted small">
            {{ __('Tekijät tarjoavat apuaan.', 'omakeikka-theme') }}
          </p>
        </div>
        <div class="col-md-4 text-center">
          <span class="marketplace-steps__number mb-3">3</span>
          <h3 class="marketplace-steps__title h5">{{ __('Valitse tekijä', 'omakeikka-theme') }}</h3>
          <p class="text-muted small">
            {{ __('Sovi yksityiskohdat ja hoitakaa homma.', 'omakeikka-theme') }}
          </p>
        </div>
      </div>
    </div>
  </section>

  @if(!empty($marketplace_groups))
    @foreach($marketplace_groups as $group)
      <section class="marketplace-archive__group py-5 {{ $loop->even ? 'bg-light' : '' }}">
        <div class="container">
          <div class="d-flex align-items-center mb-4">
            @if(!empty($group['icon_class']))
              <span class="marketplace-archive__group-icon me-3" aria-hidden="true">
                <i class="{{ $group['icon_class'] }}"></i>
              </span>
            @endif
            <div>
              <h2 class="h3 fw-bold mb-1">{{ $group['name'] }}</h2>
              @if(!empty($group['count']))
                <p class="text-muted small mb-0">
                  {{ sprintf( _n( '%d palvelu', '%d palvelua', $group['count'], 'omakeikka-theme' ), $group['count'] ) }}
                </p>
              @endif
            </div>
          </div>

          @if(!empty($group['categories']))
            <div class="row g-3">
              @foreach($group['categories'] as $category)
                <div class="col-6 col-md-4 col-lg-3">
                  <a
                    href="{{ esc_url( $category['url'] ) }}"
                    class="marketplace-grid__card text-decoration-none d-flex flex-column align-items-center text-center h-100"
                  >
                    @if(!empty($category['icon_class']))
                      <span class="marketplace-grid__icon mb-3" aria-hidden="true">
                        <i class="{{ $category['icon_class'] }}"></i>
                      </span>
                    @endif
                    <span class="marketplace-grid__title">{{ $category['title'] }}</span>
                  </a>
                </div>
              @endforeach
            </div>
          @endif
        </div>
      </section>
    @endforeach
  @endif

  <section class="marketplace-cta py-5 text-center">
    <div class="container">
      <h2 class="h3 mb-3">{{ __('Etkö löytänyt sopivaa palvelua?', 'omakeikka-theme') }}</h2>
      <p class="lead mb-4">{{ __('Julkaise oma tehtäväsi vapaasti. Tekijät vastaavat tunneissa.', 'omakeikka-theme') }}</p>
      <a href="https://app.omakeikka.fi/register" class="btn btn-primary btn-lg fw-semibold">
        {{ __('Julkaise tehtävä ilmaiseksi', 'omakeikka-theme') }}
        <i class="bi bi-arrow-right ms-2"></i>
      </a>
    </div>
  </section>

</div>

@endsection
