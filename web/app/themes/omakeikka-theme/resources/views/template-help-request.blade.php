{{--
  Template Name: Avunpyyntölomake
--}}

@extends('layouts.app')

@section('content')

  <article class="help-request-page">

    <section class="help-request-hero py-5">
      <div class="container">
        <div class="row">
          <div class="col-lg-8">
            <h1 class="display-6 fw-bold mb-3">
              {{ __( 'Julkaise avunpyyntö', 'omakeikka-theme' ) }}
            </h1>
            {{-- COPY GAP C1 --}}
            <p class="lead mb-0">
              {{ __( 'Kerro mitä tarvitset, niin etsimme sinulle tekijän. Tilin luominen ei ole tarpeen.', 'omakeikka-theme' ) }}
            </p>
          </div>
        </div>
      </div>
    </section>

    @include('partials.who-can-buy', ['compact' => true])

    <section class="py-5">
      <div class="container">
        <div class="row">
          <div class="col-lg-8">

            @include('partials.help-request-form')

            @if ( \App\Services\HouseholdDeduction::shows( $hr_deduction ?? 'no' ) )
              <div class="mt-4">
                @include('partials.deduction-notice', ['status' => $hr_deduction, 'compact' => true])
              </div>
            @endif

          </div>
        </div>
      </div>
    </section>

  </article>

@endsection
