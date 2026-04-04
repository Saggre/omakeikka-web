@extends('layouts.app')

@section('content')

<div class="occupation-archive">

  <header class="occupation-archive__header py-5 bg-light">
    <div class="container">
      <h1>{{ __('Ammatit', 'omakeikka-wp-theme') }}</h1>
      <p class="lead">
        {{ __('Löydä töitä ammattiasi vastaavissa tehtävissä tai löydä oikeat tekijät omakeikasta.', 'omakeikka-wp-theme') }}
      </p>
      <p class="text-muted mb-0">
        {{ __('Alla on näyte omakeikassa olevista ammattialoista. Omakeikassa on rekisteröityneitä eläkeläisiä huomattavasti laajemmalta ammattikirjolta — ', 'omakeikka-wp-theme') }}<a href="/rekisteroidy/">{{ __('rekisteröidy ja selaa kaikkia profiileja', 'omakeikka-wp-theme') }}</a>.
      </p>
    </div>
  </header>

  <section class="occupation-archive__list py-5">
    <div class="container">
      @if(have_posts())
        <div class="row g-4">
          @while(have_posts()) @php(the_post())
            <div class="col-md-4">
              <div class="card h-100">
                <div class="card-body">
                  <h2 class="card-title h5">
                    <a href="{{ get_permalink() }}" class="text-decoration-none stretched-link">
                      {{ get_the_title() }}
                    </a>
                  </h2>
                  @if(get_the_excerpt())
                    <p class="card-text text-muted small">{{ get_the_excerpt() }}</p>
                  @endif
                </div>
              </div>
            </div>
          @endwhile
        </div>
      @else
        <p>{{ __('Ei ammatteja.', 'omakeikka-wp-theme') }}</p>
      @endif
    </div>
  </section>

  <section class="occupation-archive__cta py-5 bg-dark text-white text-center">
    <div class="container">
      <h2 class="text-white mb-2">{{ __('Löydät lisää ammatteja omakeikasta', 'omakeikka-wp-theme') }}</h2>
      <p class="lead text-white mb-4">
        {{ __('Tällä sivulla näkyy vain osa omakeikassa olevista ammattialoista. Rekisteröidy ja selaa kaikkia profiileja.', 'omakeikka-wp-theme') }}
      </p>
      <div class="d-flex flex-column flex-sm-row gap-2 justify-content-center">
        <a href="/rekisteroidy/" class="btn btn-primary btn-lg">
          {{ __('Rekisteröidy työnantajana', 'omakeikka-wp-theme') }}
        </a>
        <a href="/rekisteroidy/" class="btn btn-outline-light btn-lg">
          {{ __('Rekisteröidy ja selaa profiileja', 'omakeikka-wp-theme') }}
        </a>
      </div>
    </div>
  </section>

</div>

@endsection
