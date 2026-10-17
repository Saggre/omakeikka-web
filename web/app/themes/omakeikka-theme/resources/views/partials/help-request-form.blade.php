{{--
  Household help request form.

  v-pre is mandatory on the wrapper. index.php wraps every Blade template in
  <div id="app"> and bud.config.ts builds Vue with the in-DOM compiler, so
  without it any {{ }} a visitor types into a field would be compiled as a Vue
  expression when the form re-renders with their values.

  Strings without a COPY GAP marker are verbatim from
  ~/repos/omakeikka/lang/fi.json. Markers indicate wording the owner still
  needs to confirm.
--}}

@php
  $errors = $hr_errors ?? array();
  $values = $hr_values ?? array();

  $value = function ( string $key ) use ( $values ) {
    return isset( $values[ $key ] ) ? (string) $values[ $key ] : '';
  };

  $invalid = function ( string $key ) use ( $errors ) {
    return isset( $errors[ $key ] );
  };

  $described = function ( string $key, string $hint = '' ) use ( $errors ) {
    $ids = array();

    if ( '' !== $hint ) {
      $ids[] = 'hr-' . $key . '-help';
    }

    if ( isset( $errors[ $key ] ) ) {
      $ids[] = 'hr-' . $key . '-error';
    }

    return implode( ' ', $ids );
  };
@endphp

<div class="help-request" v-pre>

  @if ( ! empty( $hr_success ) )
    {{-- role="status" alone does not announce a region that was present at page
         load. The #hr-success redirect fragment plus tabindex is what makes it
         perceivable, both with and without JavaScript. --}}
    <div class="alert alert-success" role="status" tabindex="-1" id="hr-success">
      <h2 class="h5 mb-1">{{ __( 'Kiitos! Vastaamme mahdollisimman pian.', 'omakeikka-theme' ) }}</h2>
      {{-- COPY GAP C6: what happens next, without promising a response time. --}}
      <p class="mb-0">{{ __( 'Otamme sinuun yhteyttä antamillasi yhteystiedoilla.', 'omakeikka-theme' ) }}</p>
    </div>
  @endif

  @if ( $errors )
    <div class="alert alert-danger" role="alert" tabindex="-1" id="hr-errors">
      {{-- COPY GAP C11 heading --}}
      <h2 class="h5">{{ __( 'Tarkista nämä kohdat', 'omakeikka-theme' ) }}</h2>
      <ul class="mb-0">
        @foreach ( $errors as $field => $message )
          <li>
            @if ( '_form' === $field )
              {{ $message }}
            @else
              <a href="#hr-{{ $field }}">{{ $message }}</a>
            @endif
          </li>
        @endforeach
      </ul>
    </div>
  @endif

  <form method="post" action="{{ esc_url( $hr_action ) }}" class="help-request__form">
    <input type="hidden" name="action" value="{{ esc_attr( $hr_action_name ) }}">
    {{-- Keep the original entry point across a failed submit. After a redirect
         the referer is admin-post.php, which is not where the lead came from. --}}
    <input type="hidden" name="source_url" value="{{ esc_attr( $value( 'source_url' ) ?: $hr_source_url ) }}">
    <input type="hidden" name="category_slug" value="{{ esc_attr( $value( 'category_slug' ) ) }}">
    <input type="hidden" name="hr_t" value="{{ esc_attr( $hr_timestamp ) }}">
    {!! wp_nonce_field( $hr_action_name, $hr_nonce_name, true, false ) !!}

    {{-- Honeypot. Positioned off-screen rather than display:none, which some
         bots know to skip. --}}
    <div class="help-request__hp" aria-hidden="true">
      <label for="hr-website">Website</label>
      <input type="text" id="hr-website" name="hr_website" tabindex="-1" autocomplete="off">
    </div>

    <div class="mb-3">
      <label for="hr-group_slug" class="form-label">{{ __( 'Kategoria', 'omakeikka-theme' ) }}</label>
      <select id="hr-group_slug" name="group_slug" required
              class="form-select form-select-lg @if ( $invalid( 'group_slug' ) ) is-invalid @endif"
              @if ( $invalid( 'group_slug' ) ) aria-invalid="true" aria-describedby="hr-group_slug-error" @endif>
        <option value="">{{ __( 'Valitse kategoria', 'omakeikka-theme' ) }}</option>
        @foreach ( $hr_groups as $group )
          <option value="{{ esc_attr( $group['slug'] ) }}" @selected( $value( 'group_slug' ) === $group['slug'] )>
            {{ $group['name'] }}
          </option>
        @endforeach
      </select>
      @if ( $invalid( 'group_slug' ) )
        <div class="invalid-feedback d-block" id="hr-group_slug-error">{{ $errors['group_slug'] }}</div>
      @endif
    </div>

    <div class="mb-3">
      <label for="hr-title" class="form-label">{{ __( 'Otsikko', 'omakeikka-theme' ) }}</label>
      <input type="text" id="hr-title" name="title" required maxlength="255"
             value="{{ esc_attr( $value( 'title' ) ) }}"
             placeholder="{{ esc_attr__( 'Esim. Ruohonleikkuu', 'omakeikka-theme' ) }}"
             class="form-control form-control-lg @if ( $invalid( 'title' ) ) is-invalid @endif"
             @if ( $invalid( 'title' ) ) aria-invalid="true" aria-describedby="hr-title-error" @endif>
      @if ( $invalid( 'title' ) )
        <div class="invalid-feedback d-block" id="hr-title-error">{{ $errors['title'] }}</div>
      @endif
    </div>

    <div class="mb-3">
      <label for="hr-description" class="form-label">{{ __( 'Kuvaile tarpeesi', 'omakeikka-theme' ) }}</label>
      <div class="form-text" id="hr-description-help">
        {{ __( 'Kerro mitä pitää tehdä, millaiset tilat tai välineet, ja muita huomioita.', 'omakeikka-theme' ) }}
      </div>
      <textarea id="hr-description" name="description" rows="6" required maxlength="5000"
                class="form-control form-control-lg @if ( $invalid( 'description' ) ) is-invalid @endif"
                aria-describedby="{{ $described( 'description', 'hint' ) }}"
                @if ( $invalid( 'description' ) ) aria-invalid="true" @endif>{{ $value( 'description' ) }}</textarea>
      @if ( $invalid( 'description' ) )
        <div class="invalid-feedback d-block" id="hr-description-error">{{ $errors['description'] }}</div>
      @endif
    </div>

    <div class="mb-3">
      <label for="hr-municipality_id" class="form-label">{{ __( 'Paikkakunta', 'omakeikka-theme' ) }}</label>
      <select id="hr-municipality_id" name="municipality_id" required autocomplete="address-level2"
              class="form-select form-select-lg @if ( $invalid( 'municipality_id' ) ) is-invalid @endif"
              @if ( $invalid( 'municipality_id' ) ) aria-invalid="true" aria-describedby="hr-municipality_id-error" @endif>
        <option value="">{{ __( 'Valitse kunta', 'omakeikka-theme' ) }}</option>
        @foreach ( $hr_municipalities as $code => $name )
          <option value="{{ esc_attr( $code ) }}" @selected( $value( 'municipality_id' ) === (string) $code )>{{ $name }}</option>
        @endforeach
      </select>
      @if ( $invalid( 'municipality_id' ) )
        <div class="invalid-feedback d-block" id="hr-municipality_id-error">{{ $errors['municipality_id'] }}</div>
      @endif
    </div>

    <div class="mb-3">
      {{-- COPY GAP C7: budget field label and helper text. --}}
      <label for="hr-budget" class="form-label">{{ __( 'Budjetti', 'omakeikka-theme' ) }}</label>
      <div class="form-text" id="hr-budget-help">
        {{ __( 'Valinnainen. Auttaa tekijää arvioimaan, sopiiko tehtävä hänelle.', 'omakeikka-theme' ) }}
      </div>
      <select id="hr-budget" name="budget" class="form-select form-select-lg" aria-describedby="hr-budget-help">
        <option value="">{{ __( 'Valitse', 'omakeikka-theme' ) }}</option>
        @foreach ( $hr_budgets as $key => $label )
          <option value="{{ esc_attr( $key ) }}" @selected( $value( 'budget' ) === (string) $key )>{{ $label }}</option>
        @endforeach
      </select>
    </div>

    <hr class="my-4">

    <div class="mb-3">
      <label for="hr-name" class="form-label">{{ __( 'Nimi', 'omakeikka-theme' ) }}</label>
      <input type="text" id="hr-name" name="name" required maxlength="100" autocomplete="name"
             value="{{ esc_attr( $value( 'name' ) ) }}"
             class="form-control form-control-lg @if ( $invalid( 'name' ) ) is-invalid @endif"
             @if ( $invalid( 'name' ) ) aria-invalid="true" aria-describedby="hr-name-error" @endif>
      @if ( $invalid( 'name' ) )
        <div class="invalid-feedback d-block" id="hr-name-error">{{ $errors['name'] }}</div>
      @endif
    </div>

    <div class="mb-3">
      <label for="hr-email" class="form-label">{{ __( 'Sähköposti', 'omakeikka-theme' ) }}</label>
      <input type="email" id="hr-email" name="email" required autocomplete="email"
             value="{{ esc_attr( $value( 'email' ) ) }}"
             class="form-control form-control-lg @if ( $invalid( 'email' ) ) is-invalid @endif"
             @if ( $invalid( 'email' ) ) aria-invalid="true" aria-describedby="hr-email-error" @endif>
      @if ( $invalid( 'email' ) )
        <div class="invalid-feedback d-block" id="hr-email-error">{{ $errors['email'] }}</div>
      @endif
    </div>

    <div class="mb-3">
      <label for="hr-phone" class="form-label">{{ __( 'Puhelin', 'omakeikka-theme' ) }}</label>
      <div class="form-text" id="hr-phone-help">{{ __( 'Valinnainen', 'omakeikka-theme' ) }}</div>
      <input type="tel" id="hr-phone" name="phone" inputmode="tel" autocomplete="tel"
             value="{{ esc_attr( $value( 'phone' ) ) }}"
             class="form-control form-control-lg" aria-describedby="hr-phone-help">
    </div>

    <div class="mb-4 form-check">
      <input type="checkbox" id="hr-consent" name="consent" value="1" required
             class="form-check-input @if ( $invalid( 'consent' ) ) is-invalid @endif"
             @if ( $invalid( 'consent' ) ) aria-invalid="true" aria-describedby="hr-consent-error" @else aria-describedby="hr-consent-help" @endif>
      <label for="hr-consent" class="form-check-label">
        {{ __( 'Annan suostumukseni yhteystietojeni käsittelyyn', 'omakeikka-theme' ) }}
      </label>
      {{-- COPY GAP C12: consent helper, must link the privacy policy and name the retention period. --}}
      <div class="form-text" id="hr-consent-help">
        {{ __( 'Käsittelemme tietojasi avunpyynnön välittämiseksi tekijöille.', 'omakeikka-theme' ) }}
      </div>
      @if ( $invalid( 'consent' ) )
        <div class="invalid-feedback d-block" id="hr-consent-error">{{ $errors['consent'] }}</div>
      @endif
    </div>

    <button type="submit" class="btn btn-primary btn-lg fw-semibold">
      {{ __( 'Lähetä', 'omakeikka-theme' ) }}
    </button>
  </form>

</div>
