@extends('layouts.app')

@section('content')

@php
  $term     = get_queried_object();
  $form_url = \App\Services\MarketplaceCategoryQuery::formUrl( $term->slug ?? '' );
@endphp

<div class="marketplace-archive">

  <header class="marketplace-archive__header marketplace-hero py-5">
    <div class="container">
      <p class="text-white-50 small mb-2">
        <a href="{{ esc_url( get_post_type_archive_link( 'marketplace_category' ) ) }}" class="text-white-50 text-decoration-none">
          <i class="bi bi-arrow-left me-1"></i>{{ __('Kaikki palvelut', 'omakeikka-theme') }}
        </a>
      </p>
      <h1 class="marketplace-hero__title display-5 fw-bold text-white mb-3">
        {{ html_entity_decode( $term->name, ENT_QUOTES, 'UTF-8' ) }}
      </h1>
      <p class="marketplace-hero__sub lead text-white mb-0">
        {{ __('Selaa tämän ryhmän palveluja tai julkaise oma tehtäväsi suoraan.', 'omakeikka-theme') }}
      </p>
      <div class="mt-4">
        <a href="{{ esc_url( $form_url ) }}" class="btn btn-primary btn-lg fw-semibold">
          {{ __('Julkaise tehtävä ilmaiseksi', 'omakeikka-theme') }}
          <i class="bi bi-arrow-right ms-2"></i>
        </a>
      </div>
    </div>
  </header>

  @if(!empty($marketplace_groups))
    @foreach($marketplace_groups as $group)
      <section class="marketplace-archive__group py-5">
        <div class="container">
          <x-deduction-notice :group="$group['slug']" />

          @if(!empty($group['categories']))
            <div class="row g-3">
              @foreach($group['categories'] as $category)
                <div class="col-6 col-md-4 col-lg-3">
                  <a
                    href="{{ esc_url( $category['form_url'] ) }}"
                    class="marketplace-grid__card text-decoration-none d-flex flex-column align-items-center text-center h-100"
                    rel="noopener"
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
      <h2 class="h3 mb-3">{{ __('Tarvitsetko apua näissä tehtävissä?', 'omakeikka-theme') }}</h2>
      <p class="lead mb-4">{{ __('Julkaise oma tehtäväsi vapaasti. Tekijät vastaavat tunneissa.', 'omakeikka-theme') }}</p>
      <a href="{{ esc_url( $form_url ) }}" class="btn btn-primary btn-lg fw-semibold">
        {{ __('Julkaise tehtävä ilmaiseksi', 'omakeikka-theme') }}
        <i class="bi bi-arrow-right ms-2"></i>
      </a>
    </div>
  </section>

</div>

@endsection
