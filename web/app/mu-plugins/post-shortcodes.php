<?php

function omakeikka_post_title_shortcode( $atts ): string {
	$atts = shortcode_atts(
		array(
			'id' => get_the_ID(),
		),
		$atts,
		'post_title'
	);

	return get_the_title( $atts['id'] );
}

function omakeikka_add_shortcodes(): void {
	add_shortcode( 'post_title', 'omakeikka_post_title_shortcode' );
}

add_action( 'init', 'omakeikka_add_shortcodes' );
