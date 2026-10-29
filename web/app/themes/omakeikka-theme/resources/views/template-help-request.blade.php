{{--
  Template Name: Avunpyyntölomake
--}}

@extends('layouts.app')

@section('content')

  @php
    $field_class = fn( string $base, string $field ) => $base . ( isset( $form_errors[ $field ] ) ? ' is-invalid' : '' );
  @endphp

  <article class="help-request-page">

  @if ( $form_success )
    @include('partials.help-request-thanks')
  @else

    <section class="help-request-hero py-5">
      <div class="container">
        <div class="row">
          <div class="col-lg-8">
            <h1 class="display-6 fw-bold mb-3">{{ __( 'Find someone for your task', 'omakeikka-theme' ) }}</h1>
            <p class="lead mb-3">{{ __( 'Tell us what you need and we will find an experienced konkari for the task.', 'omakeikka-theme' ) }}</p>
            <ul class="help-request-hero__points list-unstyled d-flex flex-wrap gap-2 gap-md-4 mb-0">
              <li><i class="bi bi-check-circle-fill me-2" aria-hidden="true"></i>{{ __( 'You pay only after the work is done', 'omakeikka-theme' ) }}</li>
              <li><i class="bi bi-check-circle-fill me-2" aria-hidden="true"></i>{{ __( 'Your contact details are not published', 'omakeikka-theme' ) }}</li>
              <li><i class="bi bi-check-circle-fill me-2" aria-hidden="true"></i>{{ __( 'No account needed', 'omakeikka-theme' ) }}</li>
            </ul>
          </div>
        </div>
      </div>
    </section>

    <section class="pb-5">
      <div class="container">
        <div class="row gy-5">

          <aside class="col-lg-4 order-lg-last" aria-labelledby="hr-how-title">
            <div class="help-request-how p-4 rounded border">
              <h2 class="h5 fw-bold mb-3" id="hr-how-title">{{ __( 'How it works', 'omakeikka-theme' ) }}</h2>
              <ol class="help-request-how__steps list-unstyled mb-0">
                <li>
                  <strong>{{ __( 'Tell us what you need', 'omakeikka-theme' ) }}</strong>
                  <p>{{ __( 'Fill in the form. You do not need an account.', 'omakeikka-theme' ) }}</p>
                </li>
                <li>
                  <strong>{{ __( 'We find a konkari', 'omakeikka-theme' ) }}</strong>
                  <p>{{ __( 'We go through your task and look for a suitable konkari for it.', 'omakeikka-theme' ) }}</p>
                </li>
                <li>
                  <strong>{{ __( 'You agree the details', 'omakeikka-theme' ) }}</strong>
                  <p>{{ __( 'You agree the price and the content of the work directly with the helper before the work starts.', 'omakeikka-theme' ) }}</p>
                </li>
                <li>
                  <strong>{{ __( 'You pay when the work is done', 'omakeikka-theme' ) }}</strong>
                  <p class="mb-0">{{ __( 'The helper invoices the work through an invoicing service once it is done. You may be able to claim the household tax deduction for some work done at home.', 'omakeikka-theme' ) }}</p>
                </li>
              </ol>
            </div>
          </aside>

          <div class="col-lg-8">

            {{-- v-pre is required: Vue compiles the whole page in DOM, so a visitor's {{ }} would run as an expression when the form re-renders. --}}
            <div class="help-request" v-pre>

              @if ( $form_errors )
                <div class="alert alert-danger" role="alert" tabindex="-1" id="hr-errors">
                  <h2 class="h5">{{ __( 'Please check these fields', 'omakeikka-theme' ) }}</h2>
                  <ul class="mb-0">
                    @foreach ( $form_errors as $field => $message )
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

              <form method="post" action="{{ esc_url( admin_url( 'admin-post.php' ) ) }}" class="help-request__form">
                <input type="hidden" name="action" value="{{ \App\Services\HelpRequest\Form::ACTION }}">
                <input type="hidden" name="category_slug" value="{{ $form_values['category_slug'] }}">
                {!! wp_nonce_field( \App\Services\HelpRequest\Form::ACTION, \App\Services\HelpRequest\Form::NONCE, true, false ) !!}

                <div class="help-request__hp" aria-hidden="true">
                  <label for="hr-website">Website</label>
                  <input type="text" id="hr-website" name="{{ \App\Services\HelpRequest\Form::HONEYPOT }}" tabindex="-1" autocomplete="off">
                </div>

                <p class="text-body-secondary">{{ __( 'Fields marked with * are required.', 'omakeikka-theme' ) }}</p>

                <div class="mb-3">
                  <label for="hr-group_slug" class="form-label">{{ __( 'Category', 'omakeikka-theme' ) }} <span class="text-danger" aria-hidden="true">*</span></label>
                  <select id="hr-group_slug" name="group_slug" required data-prefill="{{ wp_json_encode( $prefills ) }}" class="{{ $field_class( 'form-select form-select-lg', 'group_slug' ) }}">
                    <option value="">{{ __( 'Choose a category', 'omakeikka-theme' ) }}</option>
                    @foreach ( $groups as $group )
                      <option value="{{ $group['slug'] }}" @selected( $form_values['group_slug'] === $group['slug'] )>{{ $group['name'] }}</option>
                    @endforeach
                  </select>
                  @isset( $form_errors['group_slug'] )
                    <div class="invalid-feedback">{{ $form_errors['group_slug'] }}</div>
                  @endisset
                </div>

                <div class="mb-3">
                  <label for="hr-title" class="form-label">{{ __( 'Title', 'omakeikka-theme' ) }} <span class="text-danger" aria-hidden="true">*</span></label>
                  <input type="text" id="hr-title" name="title" required maxlength="255" @if ( $form_prefilled ) data-prefilled @endif value="{{ $form_values['title'] ?? '' }}"
                         placeholder="{{ __( 'For example: Lawn mowing', 'omakeikka-theme' ) }}" class="{{ $field_class( 'form-control form-control-lg', 'title' ) }}">
                  @isset( $form_errors['title'] )
                    <div class="invalid-feedback">{{ $form_errors['title'] }}</div>
                  @endisset
                </div>

                <div class="mb-3">
                  <label for="hr-description" class="form-label">{{ __( 'Describe what you need', 'omakeikka-theme' ) }} <span class="text-danger" aria-hidden="true">*</span></label>
                  <div class="form-text" id="hr-description-help">{{ __( 'Tell us what needs doing, what kind of space or tools are involved, and anything else to note.', 'omakeikka-theme' ) }}</div>
                  <textarea id="hr-description" name="description" rows="6" required maxlength="5000" @if ( $form_prefilled ) data-prefilled @endif aria-describedby="hr-description-help"
                            class="{{ $field_class( 'form-control form-control-lg', 'description' ) }}">{{ $form_values['description'] ?? '' }}</textarea>
                  @isset( $form_errors['description'] )
                    <div class="invalid-feedback">{{ $form_errors['description'] }}</div>
                  @endisset
                </div>

                <div class="mb-3">
                  <label for="hr-municipality_id" class="form-label">{{ __( 'Municipality', 'omakeikka-theme' ) }} <span class="text-danger" aria-hidden="true">*</span></label>
                  <select id="hr-municipality_id" name="municipality_id" required autocomplete="address-level2" class="{{ $field_class( 'form-select form-select-lg', 'municipality_id' ) }}">
                    <option value="">{{ __( 'Choose a municipality', 'omakeikka-theme' ) }}</option>
                    @foreach ( $municipalities as $code => $name )
                      <option value="{{ $code }}" @selected( ( $form_values['municipality_id'] ?? '' ) === (string) $code )>{{ $name }}</option>
                    @endforeach
                  </select>
                  @isset( $form_errors['municipality_id'] )
                    <div class="invalid-feedback">{{ $form_errors['municipality_id'] }}</div>
                  @endisset
                </div>

                <div class="mb-3">
                  <label for="hr-budget" class="form-label">{{ __( 'Budget (optional)', 'omakeikka-theme' ) }}</label>
                  <div class="form-text" id="hr-budget-help">{{ __( 'Helps the helper judge whether the task suits them.', 'omakeikka-theme' ) }}</div>
                  <select id="hr-budget" name="budget" class="form-select form-select-lg" aria-describedby="hr-budget-help">
                    <option value="">{{ __( 'Choose', 'omakeikka-theme' ) }}</option>
                    @foreach ( $budgets as $budget )
                      <option value="{{ $budget->value }}" @selected( ( $form_values['budget'] ?? '' ) === $budget->value )>{{ $budget->label() }}</option>
                    @endforeach
                  </select>
                </div>

                <hr class="my-4">

                <div class="mb-3">
                  <label for="hr-name" class="form-label">{{ __( 'Name or nickname', 'omakeikka-theme' ) }} <span class="text-danger" aria-hidden="true">*</span></label>
                  <input type="text" id="hr-name" name="name" required maxlength="100" autocomplete="name" value="{{ $form_values['name'] ?? '' }}"
                         class="{{ $field_class( 'form-control form-control-lg', 'name' ) }}">
                  @isset( $form_errors['name'] )
                    <div class="invalid-feedback">{{ $form_errors['name'] }}</div>
                  @endisset
                </div>

                <div class="mb-3">
                  <label for="hr-email" class="form-label">{{ __( 'Email', 'omakeikka-theme' ) }} <span class="text-danger" aria-hidden="true">*</span></label>
                  <div class="form-text" id="hr-email-help">{{ __( 'We send a confirmation to this address.', 'omakeikka-theme' ) }}</div>
                  <input type="email" id="hr-email" name="email" required autocomplete="email" value="{{ $form_values['email'] ?? '' }}" aria-describedby="hr-email-help"
                         class="{{ $field_class( 'form-control form-control-lg', 'email' ) }}">
                  @isset( $form_errors['email'] )
                    <div class="invalid-feedback">{{ $form_errors['email'] }}</div>
                  @endisset
                </div>

                <div class="mb-3">
                  <label for="hr-phone" class="form-label">{{ __( 'Phone (optional)', 'omakeikka-theme' ) }}</label>
                  <input type="tel" id="hr-phone" name="phone" autocomplete="tel" value="{{ $form_values['phone'] ?? '' }}" class="form-control form-control-lg">
                </div>

                <div class="mb-4 form-check">
                  <input type="checkbox" id="hr-consent" name="consent" value="1" required aria-describedby="hr-consent-help"
                         class="{{ $field_class( 'form-check-input', 'consent' ) }}">
                  <label for="hr-consent" class="form-check-label">{{ __( 'I consent to the processing of my contact details', 'omakeikka-theme' ) }} <span class="text-danger ms-1" aria-hidden="true">*</span></label>
                  <div class="form-text" id="hr-consent-help">{{ __( 'We process your details to pass your task on to helpers.', 'omakeikka-theme' ) }}</div>
                  @isset( $form_errors['consent'] )
                    <div class="invalid-feedback">{{ $form_errors['consent'] }}</div>
                  @endisset
                </div>

                <button type="submit" class="btn btn-primary btn-lg fw-semibold">{{ __( 'Find me a helper', 'omakeikka-theme' ) }}</button>
              </form>

            </div>
          </div>
        </div>
      </div>
    </section>

    @include('partials.who-can-buy', ['compact' => true])

  @endif

  </article>

@endsection
