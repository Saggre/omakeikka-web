@extends('layouts.app')

@section('content')
@while(have_posts())
@php
  the_post();
  $dots_url = content_url('uploads/2023/08/dots-e1693256708136.png');
  $hero_bg  = has_post_thumbnail()
    ? "background-image: url('" . esc_url(get_the_post_thumbnail_url(null, 'large')) . "'), url('" . esc_url($dots_url) . "'); background-size: cover, 140px; background-position: center, 0 0;"
    : "background-image: url('" . esc_url($dots_url) . "'); background-size: 140px; background-repeat: repeat;";
@endphp

<article {{ post_class('occupation-single') }}>

  {{-- Hero: dots/image bg, H1 left, dual-CTA card right --}}
  <section class="occupation-hero position-relative overflow-hidden" style="{{ $hero_bg }}">
    <div class="occupation-hero__overlay"></div>
    <div class="container position-relative py-5">
      <div class="row align-items-center g-4">

        <div class="col-lg-7 text-white">
          <h1 class="occupation-hero__title display-5 fw-bold">
            {{ sprintf(__('Tarvitsetko %s?', 'omakeikka-wp-theme'), $cta_partitive_singular) }}
          </h1>
          @if(!empty($alt_titles))
            <p class="occupation-hero__alt-titles small text-white-50 mt-1 mb-0">
              {{ __('Myös:', 'omakeikka-wp-theme') }} {{ implode(', ', array_map('mb_strtolower', $alt_titles)) }}
            </p>
          @endif
          @if(get_the_excerpt())
            <p class="occupation-hero__intro lead mt-3">{{ get_the_excerpt() }}</p>
          @endif
          <ol class="occupation-steps list-unstyled d-flex flex-column flex-sm-row gap-3 mt-4">
            <li class="d-flex align-items-center gap-2">
              <span class="occupation-steps__number">1</span>
              {{ __('Rekisteröidy työnantajana', 'omakeikka-wp-theme') }}
            </li>
            <li class="d-flex align-items-center gap-2">
              <span class="occupation-steps__number">2</span>
              {{ __('Selaa profiileja', 'omakeikka-wp-theme') }}
            </li>
            <li class="d-flex align-items-center gap-2">
              <span class="occupation-steps__number">3</span>
              {{ __('Ota yhteyttä tekijään', 'omakeikka-wp-theme') }}
            </li>
          </ol>
        </div>

        <div class="col-lg-5">
          <div class="occupation-hero__card card shadow-lg border-0 p-4">
            <h2 class="h5 mb-1">{{ sprintf(__('Etsitkö %s?', 'omakeikka-wp-theme'), $cta_partitive) }}</h2>
            <p class="text-muted small mb-3">{{ __('Rekisteröidy työnantajana, selaa profiileja ja ota yhteyttä sopiviin tekijöihin.', 'omakeikka-wp-theme') }}</p>
            <a href="https://app.omakeikka.fi/employer/register" class="btn btn-primary w-100">
              {{ __('Rekisteröidy työnantajana', 'omakeikka-wp-theme') }}
            </a>
            <hr class="my-3">
            <p class="text-muted small text-center mb-2">{{ __('Oletko ammattilainen?', 'omakeikka-wp-theme') }}</p>
            <a href="https://app.omakeikka.fi/register" class="btn btn-outline-primary text-dark w-100">
              {{ __('Luo oma profiili', 'omakeikka-wp-theme') }}
            </a>
          </div>
        </div>

      </div>
    </div>
  </section>

  {{-- Trust strip --}}
  {{-- Employee count: Phase 5 - replace placeholder with live API count --}}
  <section class="occupation-trust py-4 border-bottom">
    <div class="container">
      <div class="row g-3 text-center">
        <div class="col-6 col-md-3">
          <div class="occupation-trust__stat p-3 rounded border h-100">
            <div class="fw-bold fs-5">{{ __('Tuhansia', 'omakeikka-wp-theme') }}</div>
            <div class="text-muted small">{{ __('rekisteröityneitä eläkeläistä', 'omakeikka-wp-theme') }}</div>
          </div>
        </div>
        <div class="col-6 col-md-3">
          <div class="occupation-trust__stat p-3 rounded border h-100">
            <div class="fw-bold fs-5">294</div>
            <div class="text-muted small">{{ __('kuntaa', 'omakeikka-wp-theme') }}</div>
          </div>
        </div>
        <div class="col-6 col-md-3">
          <div class="occupation-trust__stat p-3 rounded border h-100">
            <div class="fw-bold fs-5">{{ __('1 kk', 'omakeikka-wp-theme') }}</div>
            <div class="text-muted small">{{ __('ilmainen kokeilu työnantajille', 'omakeikka-wp-theme') }}</div>
          </div>
        </div>
        <div class="col-6 col-md-3">
          <div class="occupation-trust__stat p-3 rounded border h-100">
            <div class="fw-bold fs-5">{{ __('Maksuton', 'omakeikka-wp-theme') }}</div>
            <div class="text-muted small">{{ __('rekisteröityminen eläkeläisille', 'omakeikka-wp-theme') }}</div>
          </div>
        </div>
      </div>
    </div>
  </section>

  {{-- Long-form SEO content --}}
  @if(get_the_content())
    <section class="occupation-content py-5">
      <div class="container">
        <div class="row">
          <div class="col-lg-8">
            {!! apply_filters('the_content', get_the_content()) !!}
          </div>
        </div>
      </div>
    </section>
  @endif

  {{-- Mid-page CTA --}}
  <section class="occupation-cta py-5 bg-dark text-white text-center">
    <div class="container">
      <h2 class="mb-2 text-white">{{ sprintf(__('Aloita %s-haku tänään', 'omakeikka-wp-theme'), $cta_singular) }}</h2>
      <p class="lead mb-4 text-white">{{ __('Rekisteröidy, selaa profiileja ja ota yhteyttä sopiviin tekijöihin.', 'omakeikka-wp-theme') }}</p>
      <div class="d-flex flex-column flex-sm-row gap-2 justify-content-center">
        <a href="https://app.omakeikka.fi/employer/register" class="btn btn-primary btn-lg">
          {{ __('Rekisteröidy työnantajana', 'omakeikka-wp-theme') }}
        </a>
        <a href="https://app.omakeikka.fi/employee-profiles" class="btn btn-outline-light btn-lg">
          {{ __('Selaa kaikkia profiileja', 'omakeikka-wp-theme') }}
        </a>
      </div>
    </div>
  </section>

  {{-- TODO Phase 5: Featured professionals --}}
  {{-- Pull top-rated profiles for this occupation from the API (cached 15min).
       Show 3 cards: avatar, name, star rating, review count, city tags, "Pyydä tarjous" CTA. --}}

  {{-- Related occupations --}}
  @if(!empty($related_occupations))
    <section class="occupation-related py-5 bg-light">
      <div class="container">
        <h2 class="h4 mb-3">{{ __('Muut ammatit', 'omakeikka-wp-theme') }}</h2>
        <ul class="list-unstyled d-flex flex-wrap gap-2">
          @foreach($related_occupations as $related)
            <li>
              <a href="{{ $related['url'] }}" class="btn btn-outline-secondary btn-sm">
                {{ $related['title'] }}
              </a>
            </li>
          @endforeach
        </ul>
      </div>
    </section>
  @endif

  {{-- Cities with registered professionals --}}
  @if(!empty($municipality_links))
    <section class="occupation-municipalities py-5">
      <div class="container">
        <h2 class="h4 mb-1">
          {{ sprintf(__('%s paikkakunnittain', 'omakeikka-wp-theme'), get_the_title()) }}
        </h2>
        <p class="text-muted small mb-3">
          {{ sprintf(__('Omakeikassa on rekisteröityneitä %s seuraavilla paikkakunnilla:', 'omakeikka-wp-theme'), $cta_partitive) }}
        </p>
        <ul class="list-unstyled d-flex flex-wrap gap-2">
          @foreach($municipality_links as $link)
            <li>
              <span class="badge bg-secondary fs-6 fw-normal">{{ $link['name'] }}</span>
            </li>
          @endforeach
        </ul>
      </div>
    </section>
  @endif

</article>

@endwhile
@endsection
