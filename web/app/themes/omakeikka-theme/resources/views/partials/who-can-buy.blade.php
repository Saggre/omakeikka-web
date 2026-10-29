{{--
  "Who can buy" explainer. Also rendered by the [omakeikka-kuka-voi-ostaa] shortcode on the Elementor homepage.
  $compact drops the heading and the call to action.
--}}

@php
  $compact = $compact ?? false;
@endphp

<section class="who-can-buy py-5" aria-labelledby="who-can-buy-title">
  <div class="container">

    @unless ( $compact )
      <h2 class="who-can-buy__title h3 fw-bold mb-4" id="who-can-buy-title">{{ __( 'How to hire a konkari', 'omakeikka-theme' ) }}</h2>
    @endunless

    <div class="row g-4">

      <div class="col-md-6">
        <div class="who-can-buy__card h-100 p-4 rounded border">
          <h3 class="h5 fw-bold">
            <i class="bi bi-people-fill me-2 text-success" aria-hidden="true"></i>
            {{ __( 'Who can hire a konkari?', 'omakeikka-theme' ) }}
          </h3>
          <p class="mb-0">{{ __( 'Anyone can hire a helper: households, families and individuals. You do not have to be a pensioner, and you do not need to create an account or become a member.', 'omakeikka-theme' ) }}</p>
        </div>
      </div>

      <div class="col-md-6">
        <div class="who-can-buy__card h-100 p-4 rounded border">
          <h3 class="h5 fw-bold">
            <i class="bi bi-hammer me-2 text-success" aria-hidden="true"></i>
            {{ __( 'Who does the work?', 'omakeikka-theme' ) }}
          </h3>
          <p class="mb-0">{{ __( 'The work is done by pensioners registered with Omakeikka, who have long work and life experience. The helper works on their own account as a light entrepreneur.', 'omakeikka-theme' ) }}</p>
        </div>
      </div>

      <div class="col-md-6">
        <div class="who-can-buy__card h-100 p-4 rounded border">
          <h3 class="h5 fw-bold">
            <i class="bi bi-receipt me-2 text-success" aria-hidden="true"></i>
            {{ __( 'How does payment work?', 'omakeikka-theme' ) }}
          </h3>
          <p class="mb-0">{{ __( 'You agree the price and the content of the work directly with the helper. You pay only after the work is done: the helper invoices it as a light entrepreneur through an invoicing service.', 'omakeikka-theme' ) }}</p>
        </div>
      </div>

      <div class="col-md-6">
        <div class="who-can-buy__card h-100 p-4 rounded border">
          <h3 class="h5 fw-bold">
            <i class="bi bi-shield-check me-2 text-success" aria-hidden="true"></i>
            {{ __( 'Is it safe?', 'omakeikka-theme' ) }}
          </h3>
          <p class="mb-0">{{ __( 'Omakeikka goes through every task and passes it on to a suitable konkari. Your contact details are not published on the site, and you agree everything with the helper before the work starts.', 'omakeikka-theme' ) }}</p>
        </div>
      </div>

    </div>

    @unless ( $compact )
      <div class="mt-4">
        <a href="{{ esc_url( \App\Services\MarketplaceCategoryQuery::formUrl() ) }}" class="btn btn-primary btn-lg fw-semibold" data-cta="who-can-buy">
          {{ __( 'Find someone for your task', 'omakeikka-theme' ) }}
          <i class="bi bi-arrow-right ms-2" aria-hidden="true"></i>
        </a>
      </div>
    @endunless

  </div>
</section>
