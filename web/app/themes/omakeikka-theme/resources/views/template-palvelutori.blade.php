{{--
  Template Name: Palvelutori-laskeutumissivu
--}}

@extends('layouts.app')

@section('content')
  @php
    $register_url = \App\Services\MarketplaceCategoryQuery::formUrl();
    $groups       = $palvelutori_groups ?? array();
    $faq          = $palvelutori_faq ?? array();
  @endphp

  <article class="palvelutori-page">

    {{-- Hero --}}
    <section class="marketplace-hero py-5">
      <div class="container">
        <div class="row align-items-center g-4">
          <div class="col-lg-7">
            <h1 class="marketplace-hero__title display-5 fw-bold mb-3">
              {{ __('Tarvitsetko apua kotona, pihalla tai arjessa?', 'omakeikka-theme') }}
            </h1>
            <p class="marketplace-hero__sub lead mb-4">
              {{ __('Valitse palvelu ja kerro, mitä tarvitset. Tekijät vastaavat lähialueeltasi. Sinä valitset tekijän, sovit yksityiskohdat ja saat avun.', 'omakeikka-theme') }}
            </p>
            <div class="marketplace-hero__cta-row d-flex flex-column flex-sm-row gap-3 mb-3">
              <a href="{{ esc_url( $register_url ) }}" class="btn btn-primary btn-lg fw-semibold" data-cta="hero-primary">
                {{ __('Julkaise tehtävä ilmaiseksi', 'omakeikka-theme') }}
                <i class="bi bi-arrow-right ms-2"></i>
              </a>
              <a href="#palvelut" class="btn btn-outline-light btn-lg" data-cta="hero-secondary">
                {{ __('Selaa palveluja', 'omakeikka-theme') }}
              </a>
            </div>
            <p class="small mb-0 text-white-50">
              <i class="bi bi-check-circle me-1"></i>
              {{ __('Maksuton julkaisu · Suomalainen palvelu · Käytössä koko Suomessa', 'omakeikka-theme') }}
            </p>
          </div>
          <div class="col-lg-5 d-none d-lg-block text-center">
            <i class="bi bi-people-fill" style="font-size: 14rem; color: rgba(255,255,255,0.12);" aria-hidden="true"></i>
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

    @include('partials.who-can-buy')

    {{-- Category grid --}}
    <section id="palvelut" class="py-5">
      <div class="container">
        <div class="text-center mb-4">
          <h2 class="h3 fw-bold mb-2">{{ __('Mihin tarvitset apua?', 'omakeikka-theme') }}</h2>
          <p class="text-muted mb-0">
            {{ __('Yli 50 palvelua arjen tehtäviin. Valitse ryhmä ja katso, mihin saat apua.', 'omakeikka-theme') }}
          </p>
        </div>

        @if(!empty($groups))
          <div class="marketplace-grid">
            <div class="row g-3">
              @foreach($groups as $group)
                <div class="col-6 col-md-4 col-lg-3">
                  <a
                    href="{{ esc_url( $group['form_url'] ) }}"
                    class="marketplace-grid__card text-decoration-none d-flex flex-column align-items-center text-center h-100"
                    data-cta="category-card"
                    data-category="{{ esc_attr( $group['slug'] ) }}"
                  >
                    @if(!empty($group['icon_class']))
                      <span class="marketplace-grid__icon mb-3" aria-hidden="true">
                        <i class="{{ $group['icon_class'] }}"></i>
                      </span>
                    @endif
                    <span class="marketplace-grid__title">{{ $group['name'] }}</span>
                    @if(!empty($group['count']))
                      <span class="marketplace-grid__meta mt-2">
                        {{ sprintf( _n( '%d palvelu', '%d palvelua', $group['count'], 'omakeikka-theme' ), $group['count'] ) }}
                      </span>
                    @endif
                  </a>
                </div>
              @endforeach
            </div>
          </div>
        @endif

        <div class="text-center mt-4">
          <a href="{{ esc_url( get_post_type_archive_link( 'marketplace_category' ) ) }}" class="btn btn-link">
            {{ __('Näytä kaikki palvelut', 'omakeikka-theme') }}
            <i class="bi bi-arrow-right ms-1"></i>
          </a>
        </div>
      </div>
    </section>

    {{-- How it works --}}
    <section class="py-5 bg-light">
      <div class="container">
        <h2 class="h3 fw-bold text-center mb-5">{{ __('Näin se toimii', 'omakeikka-theme') }}</h2>
        <div class="row g-4 marketplace-steps">
          <div class="col-md-4 text-center">
            <span class="marketplace-steps__number mb-3">1</span>
            <h3 class="marketplace-steps__title h5">{{ __('Kerro mitä tarvitset', 'omakeikka-theme') }}</h3>
            <p class="text-muted small">
              {{ __('Julkaise tehtäväsi muutamassa minuutissa. Kerro mitä, missä ja milloin.', 'omakeikka-theme') }}
            </p>
          </div>
          <div class="col-md-4 text-center">
            <span class="marketplace-steps__number mb-3">2</span>
            <h3 class="marketplace-steps__title h5">{{ __('Saat vastauksia tekijöiltä', 'omakeikka-theme') }}</h3>
            <p class="text-muted small">
              {{ __('Eläkeläiset tarjoavat apuaan tunneissa. Vertaile, lue arviot ja kysy lisää.', 'omakeikka-theme') }}
            </p>
          </div>
          <div class="col-md-4 text-center">
            <span class="marketplace-steps__number mb-3">3</span>
            <h3 class="marketplace-steps__title h5">{{ __('Valitse tekijä ja sovi', 'omakeikka-theme') }}</h3>
            <p class="text-muted small">
              {{ __('Valitse sopivin, sovi yksityiskohdat ja maksu suoraan tekijälle. Tekijä toimii kevytyrittäjänä omaan lukuunsa.', 'omakeikka-theme') }}
            </p>
          </div>
        </div>
      </div>
    </section>

    {{-- Page content (editable from WP admin) --}}
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
    @endif

    {{-- FAQ --}}
    @if(!empty($faq))
      <section class="py-5 bg-light">
        <div class="container">
          <h2 class="h3 fw-bold text-center mb-4">{{ __('Usein kysyttyä', 'omakeikka-theme') }}</h2>
          <div class="row justify-content-center">
            <div class="col-lg-8">
              <div class="marketplace-faq accordion" id="palvelutoriFaq">
                @foreach($faq as $i => $item)
                  @php $heading_id = 'faq-heading-' . $i; $collapse_id = 'faq-collapse-' . $i; @endphp
                  <div class="accordion-item">
                    <h3 class="accordion-header" id="{{ $heading_id }}">
                      <button
                        class="accordion-button {{ $i === 0 ? '' : 'collapsed' }}"
                        type="button"
                        data-bs-toggle="collapse"
                        data-bs-target="#{{ $collapse_id }}"
                        aria-expanded="{{ $i === 0 ? 'true' : 'false' }}"
                        aria-controls="{{ $collapse_id }}"
                      >
                        {{ $item['q'] }}
                      </button>
                    </h3>
                    <div
                      id="{{ $collapse_id }}"
                      class="accordion-collapse collapse {{ $i === 0 ? 'show' : '' }}"
                      aria-labelledby="{{ $heading_id }}"
                      data-bs-parent="#palvelutoriFaq"
                    >
                      <div class="accordion-body text-muted">
                        {!! wp_kses_post( $item['a'] ) !!}
                      </div>
                    </div>
                  </div>
                @endforeach
              </div>
            </div>
          </div>
        </div>
      </section>
    @endif

    {{-- Final CTA --}}
    <section class="marketplace-cta py-5 text-center">
      <div class="container">
        <h2 class="h3 mb-3">{{ __('Aloita ilmaiseksi tänään', 'omakeikka-theme') }}</h2>
        <p class="lead mb-4">{{ __('Julkaise tehtäväsi ja saat vastauksia jo tunneissa.', 'omakeikka-theme') }}</p>
        <a href="{{ esc_url( $register_url ) }}" class="btn btn-primary btn-lg fw-semibold" data-cta="final-cta">
          {{ __('Julkaise tehtävä ilmaiseksi', 'omakeikka-theme') }}
          <i class="bi bi-arrow-right ms-2"></i>
        </a>
      </div>
    </section>

  </article>
@endsection
