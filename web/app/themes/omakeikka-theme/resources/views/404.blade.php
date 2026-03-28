@extends('layouts.app')

@section('content')
  <section class="error-404 py-5">
    <div class="container text-center" style="max-width: 540px;">
      <div class="display-1 fw-bold text-primary mb-2">404</div>
      <h1 class="h3 mb-3">{{ __('Sivua ei löydy', 'omakeikka-wp-theme') }}</h1>
      <p class="text-muted mb-4">
        {{ __('Etsimääsi sivua ei löydy. Se on saatettu siirtää tai poistaa.', 'omakeikka-wp-theme') }}
      </p>
      <div class="d-flex flex-column flex-sm-row gap-2 justify-content-center">
        <a href="{{ home_url('/') }}" class="btn btn-primary">
          {{ __('Palaa etusivulle', 'omakeikka-wp-theme') }}
        </a>
        <a href="{{ get_post_type_archive_link('occupation') }}" class="btn btn-outline-secondary">
          {{ __('Selaa ammatteja', 'omakeikka-wp-theme') }}
        </a>
      </div>
    </div>
  </section>
@endsection
