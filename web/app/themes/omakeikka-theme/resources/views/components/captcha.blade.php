{{--
  Invisible ALTCHA widget for a public form. The browser starts solving the challenge when the visitor first focuses
  the form, and app.ts holds back a submit that beats the solve. Must sit inside a v-pre element, otherwise Vue tries
  to resolve <altcha-widget> as a component.
--}}

<altcha-widget
  {{ $attributes->merge( array( 'class' => 'd-block' ) ) }}
  name="{{ \App\Services\Captcha::FIELD }}"
  challenge="{{ esc_url( rest_url( \App\Services\Captcha::REST_NAMESPACE . \App\Services\Captcha::REST_ROUTE ) ) }}"
  auto="onfocus"
  display="invisible"
  language="{{ esc_attr( substr( determine_locale(), 0, 2 ) ) }}"
></altcha-widget>
