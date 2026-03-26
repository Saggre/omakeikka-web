<?php

require_once __DIR__ . '/post-shortcodes.php';

add_action( 'init', 'omakeikka_register_shortcodes' );

function omakeikka_register_shortcodes(): void {
	add_shortcode( 'query-message', 'omakeikka_form_query_message' );
	add_shortcode( 'referer-message', 'omakeikka_form_referer_message' );
}

/**
 * Handle the query-message shortcode.
 *
 * @param array $atts
 *
 * @return string
 */
function omakeikka_form_query_message( array $atts ): string {
	$atts = shortcode_atts( array(
		'method'   => 'GET',
		'param'    => 'form-submit',
		'message'  => __( 'Kiitos! Vastaamme mahdollisimman pian.', 'omakeikka' ),
		'class'    => 'form-message',
		'fragment' => null,
	), $atts, 'query-message' );

	if ( empty( $atts['param'] ) ) {
		return '';
	}

	$method = strtoupper( $atts['method'] );

	switch ( $method ) {
		case 'GET':
			if ( isset( $_GET[ $atts['param'] ] ) ) {
				return omakeikka_form_query_message_content( $atts['message'], $atts['class'], $atts['fragment'] );
			}
			break;
		case 'POST':
			if ( isset( $_POST[ $atts['param'] ] ) ) {
				return omakeikka_form_query_message_content( $atts['message'], $atts['class'], $atts['fragment'] );
			}
			break;
	}

	return '';
}

/**
 * Handle the referer-message shortcode.
 *
 * @param array $atts
 *
 * @return string
 */
function omakeikka_form_referer_message( array $atts ): string {
	$atts = shortcode_atts( array(
		'referer'  => 'crm.zoho.eu',
		'message'  => __( 'Kiitos! Vastaamme mahdollisimman pian.', 'omakeikka' ),
		'class'    => 'form-message',
		'fragment' => null,
	), $atts, 'query-message' );

	if ( empty( $atts['referer'] ) ) {
		return '';
	}

	$referer = trim( $atts['referer'] );

	if ( str_contains( $_SERVER['HTTP_REFERER'] ?? '', $referer ) ) {
		return omakeikka_form_query_message_content( $atts['message'], $atts['class'], $atts['fragment'] );
	}

	return '';
}

/**
 * Get query-message shortcode content.
 *
 * @param string      $message
 * @param string      $class
 * @param string|null $fragment
 *
 * @return string
 */
function omakeikka_form_query_message_content( string $message, string $class, ?string $fragment ): string {
	ob_start();
	?>
	<div class="<?php echo esc_attr( $class ); ?>"><?php echo esc_html( $message ); ?></div>
	<?php

	if ( ! empty( $fragment ) ) {
		$fragment = esc_attr( $fragment );
		$fragment = ltrim( $fragment, '#' );
		?>
		<script>
			document.addEventListener( "DOMContentLoaded", function ( e ) {
				if ( window.location.hash ) {
					return;
				}

				document.location.hash = '#<?php echo $fragment; ?>';
			} );
		</script>
		<?php
	}

	return ob_get_clean();
}
