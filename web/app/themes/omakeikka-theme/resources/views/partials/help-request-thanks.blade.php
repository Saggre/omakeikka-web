{{--
  Shown in place of the form after a successful submit. The summary is empty on a refresh,
  since the stash is read once, and the page still confirms the request was sent.
--}}

@php
  $group_name        = ( collect( $groups )->firstWhere( 'slug', $form_values['group_slug'] ?? '' ) ?? array() )['name'] ?? '';
  $municipality_name = $municipalities[ $form_values['municipality_id'] ?? '' ] ?? '';
@endphp

{{-- v-pre for the same reason as on the form: the title is visitor text. --}}
<section class="help-request-thanks py-5" v-pre>
  <div class="container">
    <div class="row">
      <div class="col-lg-8">

        <div class="help-request-thanks__panel p-4 p-md-5 rounded-3 mb-5">
          <i class="bi bi-check-circle-fill help-request-thanks__icon" aria-hidden="true"></i>
          <h1 class="display-6 fw-bold mt-3 mb-3">{{ __( 'We have received your task', 'omakeikka-theme' ) }}</h1>
          @if ( ! empty( $form_values['reference'] ) )
            <p class="fs-5 mb-2">{{ sprintf( __( 'Task number: %s', 'omakeikka-theme' ), '#' . (int) $form_values['reference'] ) }}</p>
            {{-- The reference exists only on the first view after a submit, so a refresh does not count the lead twice. --}}
            <span hidden data-analytics-event="{{ wp_json_encode( array( 'name' => 'generate_lead', 'params' => array( 'form' => 'help_request', 'group' => $form_values['group_slug'] ?? '', 'category' => $form_values['category_slug'] ?? '' ) ) ) }}"></span>
          @endif
          @if ( ! empty( $form_values['email'] ) )
            <p class="mb-0">{{ sprintf( __( 'We sent a confirmation to %s.', 'omakeikka-theme' ), $form_values['email'] ) }}</p>
          @else
            <p class="mb-0">{{ __( 'Thank you! We will reply as soon as possible.', 'omakeikka-theme' ) }}</p>
          @endif
        </div>

        @if ( ! empty( $form_values['title'] ) )
          <h2 class="h4 fw-bold">{{ __( 'Your task', 'omakeikka-theme' ) }}</h2>
          <dl class="row mb-5">
            <dt class="col-sm-4">{{ __( 'Title', 'omakeikka-theme' ) }}</dt>
            <dd class="col-sm-8">{{ $form_values['title'] }}</dd>
            @if ( $group_name )
              <dt class="col-sm-4">{{ __( 'Category', 'omakeikka-theme' ) }}</dt>
              <dd class="col-sm-8">{{ $group_name }}</dd>
            @endif
            @if ( $municipality_name )
              <dt class="col-sm-4">{{ __( 'Municipality', 'omakeikka-theme' ) }}</dt>
              <dd class="col-sm-8 mb-0">{{ $municipality_name }}</dd>
            @endif
          </dl>
        @endif

        <h2 class="h4 fw-bold">{{ __( 'What happens next?', 'omakeikka-theme' ) }}</h2>
        <ol class="help-request-thanks__steps mb-4">
          <li>{{ __( 'We go through your task and look for a suitable konkari for it.', 'omakeikka-theme' ) }}</li>
          <li>{{ __( 'We will contact you using the details you gave.', 'omakeikka-theme' ) }}</li>
          <li>{{ __( 'You agree the price and the content of the work directly with the helper. You pay only after the work is done: the helper invoices it as a light entrepreneur through an invoicing service.', 'omakeikka-theme' ) }}</li>
        </ol>

        <p class="text-body-secondary">{{ __( 'No confirmation email? Check your spam folder.', 'omakeikka-theme' ) }}</p>

        <div class="d-flex flex-wrap gap-3 mt-4">
          <a href="{{ esc_url( home_url( '/' ) ) }}" class="btn btn-primary btn-lg fw-semibold">{{ __( 'Back to the front page', 'omakeikka-theme' ) }}</a>
          <a href="{{ esc_url( \App\Services\MarketplaceCategoryQuery::formUrl() ) }}" class="btn btn-outline-primary btn-lg fw-semibold">{{ __( 'Find a helper for another task', 'omakeikka-theme' ) }}</a>
        </div>

      </div>
    </div>
  </div>
</section>
