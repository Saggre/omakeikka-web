{{--
  Kotitalousvähennys notice.

  Carries no percentages, caps or euro amounts by design. The rate depends on
  the tax year and on how the individual helper invoices, and the 2026
  increase was an unratified proposal when this was written. Stating that the
  deduction may be available and linking to vero.fi is accurate in every year.

  Renders nothing for status 'no'. A negative claim is also a claim, and for a
  grab bag group like 'muu' we cannot make one.

  Required variable: $status. Optional: $compact.
--}}

@php
  $status   = $status ?? 'no';
  $compact  = $compact ?? false;
  $info_url = \App\Services\HouseholdDeduction::INFO_URL;
  $updated  = \App\Services\HouseholdDeduction::updatedLabel();
@endphp

@if ( \App\Services\HouseholdDeduction::shows( $status ) )
<div class="deduction-notice alert alert-light border" role="note">
  <h3 class="h6 fw-bold">
    <i class="bi bi-info-circle me-2" aria-hidden="true"></i>
    {{-- COPY GAP C13 heading --}}
    {{ __( 'Kotitalousvähennys', 'omakeikka-theme' ) }}
  </h3>

  {{-- COPY GAP C13 body --}}
  <p class="mb-2">
    {{ __( 'Tästä työstä voi olla mahdollista saada kotitalousvähennys. Vähennyksen saa vain työn osuudesta, ei tarvikkeista, matkakuluista eikä laskutuspalvelun palkkiosta.', 'omakeikka-theme' ) }}
  </p>

  @if ( 'caveat' === $status || 'conditional' === $status )
    {{-- COPY GAP C14: one variant per group, hoiva, asennukset, tekniikka. --}}
    <p class="mb-2">
      {{ __( 'Vähennyskelpoisuus riippuu työn sisällöstä. Tarkista oma tilanteesi verottajalta ennen kuin teet päätöksiä.', 'omakeikka-theme' ) }}
    </p>
  @endif

  @unless ( $compact )
    <p class="mb-2">
      {{ __( 'Työn on oltava tehty omassa kodissa tai vapaa-ajan asunnossa, tai omien tai puolison vanhempien tai isovanhempien kodissa.', 'omakeikka-theme' ) }}
    </p>
  @endunless

  <p class="small mb-0">
    <a href="{{ esc_url( $info_url ) }}" rel="noopener noreferrer" target="_blank">
      {{ __( 'Lue kotitalousvähennyksestä vero.fi:ssä', 'omakeikka-theme' ) }}
    </a>
    <span class="text-muted">
      {{-- COPY GAP C16 --}}
      · {{ sprintf( __( 'Tiedot päivitetty %s', 'omakeikka-theme' ), $updated ) }}
      · {{ __( 'Tämä on yleistä tietoa, ei veroneuvontaa.', 'omakeikka-theme' ) }}
    </span>
  </p>
</div>
@endif
