@extends('layouts.app')

@section('content')
@while(have_posts()) @php(the_post())

<script type="application/ld+json">{!! $json_ld !!}</script>

<article @php(post_class('occupation'))>

  {{-- Hero --}}
  <section class="occupation-hero">
    @if(has_post_thumbnail())
      <div class="occupation-hero__image">
        {!! get_the_post_thumbnail(null, 'large', ['class' => 'occupation-hero__img']) !!}
      </div>
    @endif

    <div class="occupation-hero__content container">
      <h1 class="occupation-hero__title">{{ get_the_title() }}</h1>

      @if(get_the_excerpt())
        <p class="occupation-hero__intro lead">{{ get_the_excerpt() }}</p>
      @endif

      <ol class="occupation-steps list-unstyled d-flex gap-4 my-4">
        <li><span class="occupation-steps__number">1</span> {{ __('Luo profiili', 'omakeikka-wp-theme') }}</li>
        <li><span class="occupation-steps__number">2</span> {{ __('Löydä yhteensopivat mahdollisuudet', 'omakeikka-wp-theme') }}</li>
        <li><span class="occupation-steps__number">3</span> {{ __('Tee töitä', 'omakeikka-wp-theme') }}</li>
      </ol>

      <a href="https://app.omakeikka.fi/register" class="btn btn-primary btn-lg">
        {{ __('Luo profiili', 'omakeikka-wp-theme') }}
      </a>
    </div>
  </section>

  {{-- TODO Phase 5: Trust strip (live API, cached 1h) --}}

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

  {{-- Employee CTA --}}
  <section class="occupation-cta occupation-cta--employee py-5 bg-primary text-white">
    <div class="container text-center">
      <h2>{{ sprintf(__('Oletko %s?', 'omakeikka-wp-theme'), get_the_title()) }}</h2>
      <p class="lead">{{ __('Luo profiilisi omakeikassa ja löydä uusia mahdollisuuksia.', 'omakeikka-wp-theme') }}</p>
      <a href="https://app.omakeikka.fi/register" class="btn btn-light btn-lg mt-2">
        {{ __('Luo profiili', 'omakeikka-wp-theme') }}
      </a>
    </div>
  </section>

  {{-- Employer CTA --}}
  <section class="occupation-cta occupation-cta--employer py-5 bg-dark text-white">
    <div class="container text-center">
      <h2>{{ sprintf(__('Etsitkö %s?', 'omakeikka-wp-theme'), mb_strtolower(get_the_title())) }}</h2>
      <p class="lead">{{ __('Rekisteröidy työnantajana omakeikassa ja löydä sopivat tekijät.', 'omakeikka-wp-theme') }}</p>
      <a href="https://app.omakeikka.fi/employer/register" class="btn btn-primary btn-lg mt-2">
        {{ __('Rekisteröidy työnantajana', 'omakeikka-wp-theme') }}
      </a>
    </div>
  </section>

  {{-- TODO Phase 5: Featured professionals (live API, cached 15min) --}}

  {{-- Related occupations --}}
  @if(!empty($related_occupations))
    <section class="occupation-related py-5">
      <div class="container">
        <h2>{{ __('Samankaltaisia ammatteja', 'omakeikka-wp-theme') }}</h2>
        <ul class="list-unstyled d-flex flex-wrap gap-2 mt-3">
          @foreach($related_occupations as $related)
            <li>
              <a href="{{ $related['url'] }}" class="badge bg-secondary text-decoration-none fs-6">
                {{ $related['title'] }}
              </a>
            </li>
          @endforeach
        </ul>
      </div>
    </section>
  @endif

  {{-- City switcher --}}
  @if(!empty($municipality_links))
    <section class="occupation-municipalities py-5 bg-light">
      <div class="container">
        <h2>{{ sprintf(__('%s kaupungeittain', 'omakeikka-wp-theme'), get_the_title()) }}</h2>
        <ul class="list-unstyled d-flex flex-wrap gap-2 mt-3">
          @foreach($municipality_links as $link)
            <li>
              <a href="{{ $link['url'] }}" class="btn btn-outline-primary btn-sm">
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
