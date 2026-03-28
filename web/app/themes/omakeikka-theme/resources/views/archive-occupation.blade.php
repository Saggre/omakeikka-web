@extends('layouts.app')

@section('content')

<div class="occupation-archive">

  <header class="occupation-archive__header py-5 bg-light">
    <div class="container">
      <h1>{{ __('Ammatit', 'omakeikka-wp-theme') }}</h1>
      <p class="lead">
        {{ __('Löydä töitä ammattiasi vastaavissa tehtävissä tai löydä oikeat tekijät omakeikasta.', 'omakeikka-wp-theme') }}
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

        <div class="mt-4">
          {!! get_the_posts_navigation() !!}
        </div>
      @else
        <p>{{ __('Ei ammatteja.', 'omakeikka-wp-theme') }}</p>
      @endif
    </div>
  </section>

</div>

@endsection
