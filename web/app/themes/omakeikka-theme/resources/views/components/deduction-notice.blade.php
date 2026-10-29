<div {{ $attributes->merge( ['class' => 'deduction-notice alert alert-light border'] ) }} role="note">
  <h3 class="h6 fw-bold">
    <i class="bi bi-info-circle me-2" aria-hidden="true"></i>
    {{ __( 'Household tax deduction', 'omakeikka-theme' ) }}
  </h3>

  <p class="mb-2">
    {{ __( 'You may be able to claim the household tax deduction for this work. The deduction only covers the labour, not supplies, travel costs or the invoicing service fee.', 'omakeikka-theme' ) }}
  </p>

  @if ( \App\Services\DeductionEligibility::Caveat === $eligibility )
    <p class="mb-2">
      {{ __( 'Eligibility depends on the content of the work. Check your own situation with the Tax Administration before making decisions.', 'omakeikka-theme' ) }}
    </p>
  @endif

  @unless ( $compact )
    <p class="mb-2">
      {{ __( 'The work must be done in your own home or holiday home, or in the home of your or your spouse\'s parents or grandparents.', 'omakeikka-theme' ) }}
    </p>
  @endunless

  <p class="small mb-0">
    <a href="{{ esc_url( $infoUrl ) }}" rel="noopener noreferrer" target="_blank">
      {{ __( 'Read about the household tax deduction on vero.fi', 'omakeikka-theme' ) }}
    </a>
    <span class="text-muted">
      · {{ sprintf( __( 'Information updated %s', 'omakeikka-theme' ), $updated ) }}
      · {{ __( 'This is general information, not tax advice.', 'omakeikka-theme' ) }}
    </span>
  </p>
</div>
