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
            {{ sprintf(__('Tarvitsetko %s?', 'omakeikka-wp-theme'), mb_strtolower(get_the_title())) }}
          </h1>
          @if(get_the_excerpt())
            <p class="occupation-hero__intro lead mt-3">{{ get_the_excerpt() }}</p>
          @endif
          <ol class="occupation-steps list-unstyled d-flex flex-column flex-sm-row gap-3 mt-4">
            <li class="d-flex align-items-center gap-2">
              <span class="occupation-steps__number">1</span>
              {{ __('Luo profiili', 'omakeikka-wp-theme') }}
            </li>
            <li class="d-flex align-items-center gap-2">
              <span class="occupation-steps__number">2</span>
              {{ __('Löydä yhteensopivat mahdollisuudet', 'omakeikka-wp-theme') }}
            </li>
            <li class="d-flex align-items-center gap-2">
              <span class="occupation-steps__number">3</span>
              {{ __('Tee töitä', 'omakeikka-wp-theme') }}
            </li>
          </ol>
        </div>

        <div class="col-lg-5">
          <div class="occupation-hero__card card shadow-lg border-0 p-4">
            <h2 class="h5 mb-1">{{ sprintf(__('Etsitkö %s?', 'omakeikka-wp-theme'), mb_strtolower(get_the_title())) }}</h2>
            <p class="text-muted small mb-3">{{ __('Rekisteröidy työnantajana ja löydä sopivat tekijät.', 'omakeikka-wp-theme') }}</p>
            <a href="https://app.omakeikka.fi/employer/register" class="btn btn-primary w-100">
              {{ __('Rekisteröidy työnantajana', 'omakeikka-wp-theme') }}
            </a>
            <hr class="my-3">
            <p class="text-muted small text-center mb-2">{{ __('Oletko ammattilainen?', 'omakeikka-wp-theme') }}</p>
            <a href="https://app.omakeikka.fi/register" class="btn btn-outline-primary w-100">
              {{ __('Luo oma profiili', 'omakeikka-wp-theme') }}
            </a>
          </div>
        </div>

      </div>
    </div>
  </section>

  {{-- Trust strip - Phase 5: replace dashes with live API stats --}}
  <section class="occupation-trust py-4 border-bottom">
    <div class="container">
      <p class="text-muted small text-center mb-3">{{ __('Omakeikassa on viimeisten 12 kuukauden aikana:', 'omakeikka-wp-theme') }}</p>
      <div class="row g-3 text-center">
        <div class="col-4">
          <div class="occupation-trust__stat p-3 rounded border">
            <div class="fw-bold fs-5">—</div>
            <div class="text-muted small">{{ __('profiilia', 'omakeikka-wp-theme') }}</div>
          </div>
        </div>
        <div class="col-4">
          <div class="occupation-trust__stat p-3 rounded border">
            <div class="fw-bold fs-5">—</div>
            <div class="text-muted small">{{ __('arviointia', 'omakeikka-wp-theme') }}</div>
          </div>
        </div>
        <div class="col-4">
          <div class="occupation-trust__stat p-3 rounded border">
            <div class="fw-bold fs-5">—</div>
            <div class="text-muted small">{{ __('keikkaa', 'omakeikka-wp-theme') }}</div>
          </div>
        </div>
      </div>
    </div>
  </section>

  {{-- Long-form SEO content --}}
  @if(get_the_content())
    <section class="occupation-content py-5">
      <div class="container">
        <div class="row justify-content-center">
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
      <h2 class="mb-2">{{ sprintf(__('Aloita %s-haku tänään', 'omakeikka-wp-theme'), mb_strtolower(get_the_title())) }}</h2>
      <p class="lead mb-4 text-white">{{ __('Rekisteröi tarve omakeikassa ja vastaanota tarjouksia.', 'omakeikka-wp-theme') }}</p>
      <a href="https://app.omakeikka.fi/employer/register" class="btn btn-light btn-lg">
        {{ __('Rekisteröidy työnantajana', 'omakeikka-wp-theme') }}
      </a>
    </div>
  </section>

  {{-- TODO Phase 5: Featured professionals --}}
  {{-- Pull top-rated profiles for this occupation from the API (cached 15min).
       Show 3 cards: avatar, name, star rating, review count, city tags, "Pyydä tarjous" CTA. --}}

  {{-- Related occupations --}}
  @if(!empty($related_occupations))
    <section class="occupation-related py-5 bg-light">
      <div class="container">
        <h2 class="h4 mb-3">{{ __('Samankaltaisia ammatteja', 'omakeikka-wp-theme') }}</h2>
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

  {{-- City directory --}}
  @if(!empty($municipality_links))
    <section class="occupation-municipalities py-5">
      <div class="container">
        <h2 class="h4 mb-3">
          {{ sprintf(__('Löydä %s läheltäsi', 'omakeikka-wp-theme'), mb_strtolower(get_the_title())) }}
        </h2>
        <ul class="list-unstyled d-flex flex-wrap gap-2">
          @foreach($municipality_links as $link)
            <li>
              <a href="{{ $link['url'] }}" class="btn btn-primary btn-sm">
                {{ $link['name'] }}
              </a>
            </li>
          @endforeach
        </ul>
      </div>
    </section>
  @endif

</article>

@endwhile
@endsection
