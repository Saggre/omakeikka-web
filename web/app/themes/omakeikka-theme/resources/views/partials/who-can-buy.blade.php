{{--
  "Kuka voi ostaa apua" explainer.

  Corrects the perception that Omakeikka is a platform for pensioners only.
  The asymmetry is the whole product: anyone may buy, pensioners do the work.
  That has to be the first thing the section says.

  Included from template-palvelutori, the help request template, the category
  archive, and from the [omakeikka-kuka-voi-ostaa] shortcode on the Elementor
  homepage.

  Every string here is a COPY GAP. The omakeikka app defines nothing about who
  may buy, so per AGENTS.md these are placeholders for the owner to confirm,
  not final copy.

  Optional variable: $compact (bool) drops the heading and the service list.
--}}

@php
  $compact  = $compact ?? false;
  $form_url = \App\Services\MarketplaceCategoryQuery::formUrl();
@endphp

<section class="who-can-buy py-5" aria-labelledby="who-can-buy-title">
  <div class="container">

    @unless ( $compact )
      {{-- COPY GAP C2 --}}
      <h2 class="who-can-buy__title h3 fw-bold mb-4" id="who-can-buy-title">
        {{ __( 'Kuka voi ostaa apua?', 'omakeikka-theme' ) }}
      </h2>
    @endunless

    <div class="row g-4">

      <div class="col-md-6">
        <div class="who-can-buy__card h-100 p-4 rounded border">
          <h3 class="h5 fw-bold">
            <i class="bi bi-people-fill me-2 text-success" aria-hidden="true"></i>
            {{ __( 'Kuka tahansa', 'omakeikka-theme' ) }}
          </h3>
          {{-- COPY GAP C3 --}}
          <p class="mb-0">
            {{ __( 'Apua voi tilata kuka tahansa: kotitaloudet, perheet ja yksityishenkilöt. Sinun ei tarvitse olla eläkeläinen, eikä sinun tarvitse luoda tiliä tai liittyä jäseneksi.', 'omakeikka-theme' ) }}
          </p>
        </div>
      </div>

      <div class="col-md-6">
        <div class="who-can-buy__card h-100 p-4 rounded border">
          <h3 class="h5 fw-bold">
            <i class="bi bi-hammer me-2 text-success" aria-hidden="true"></i>
            {{ __( 'Kuka työt tekee?', 'omakeikka-theme' ) }}
          </h3>
          {{-- COPY GAP C4 --}}
          <p class="mb-0">
            {{ __( 'Työt tekevät Omakeikkaan rekisteröityneet eläkeläiset, joilla on pitkä työ- ja elämänkokemus. Tekijä toimii kevytyrittäjänä omaan lukuunsa.', 'omakeikka-theme' ) }}
          </p>
        </div>
      </div>

      <div class="col-12">
        <div class="who-can-buy__card p-4 rounded border">
          <h3 class="h5 fw-bold">
            <i class="bi bi-receipt me-2 text-success" aria-hidden="true"></i>
            {{ __( 'Miten maksaminen toimii?', 'omakeikka-theme' ) }}
          </h3>
          {{-- COPY GAP C5: the Omapaja invoicing explainer.
               NOTE for the owner: the palvelutori FAQ in
               app/View/Composers/PalvelutoriPage.php currently names UKKO,
               OP Kevytyrittäjä and Eezy instead of Omapaja. The two need to
               agree before this ships. --}}
          <p class="mb-0">
            {{ __( 'Sovit hinnan ja työn sisällön suoraan tekijän kanssa. Tekijä laskuttaa työn kevytyrittäjänä laskutuspalvelun kautta, joten saat työstä laskun.', 'omakeikka-theme' ) }}
          </p>
        </div>
      </div>

    </div>

    @unless ( $compact )
      <div class="mt-4">
        <a href="{{ esc_url( $form_url ) }}" class="btn btn-primary btn-lg fw-semibold" data-cta="who-can-buy">
          {{ __( 'Julkaise avunpyyntö', 'omakeikka-theme' ) }}
          <i class="bi bi-arrow-right ms-2" aria-hidden="true"></i>
        </a>
      </div>
    @endunless

  </div>
</section>
