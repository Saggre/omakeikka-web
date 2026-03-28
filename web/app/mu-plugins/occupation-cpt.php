<?php

/**
 * Registers the 'occupation' custom post type and 'municipality' taxonomy.
 */

add_action( 'init', 'omakeikka_register_occupation_cpt' );

/**
 * Register the occupation CPT and municipality taxonomy.
 */
function omakeikka_register_occupation_cpt(): void {
	register_post_type(
		'occupation',
		array(
			'labels'        => array(
				'name'               => __( 'Ammatit', 'omakeikka-wp-theme' ),
				'singular_name'      => __( 'Ammatti', 'omakeikka-wp-theme' ),
				'add_new_item'       => __( 'Lisää uusi ammatti', 'omakeikka-wp-theme' ),
				'edit_item'          => __( 'Muokkaa ammattia', 'omakeikka-wp-theme' ),
				'new_item'           => __( 'Uusi ammatti', 'omakeikka-wp-theme' ),
				'view_item'          => __( 'Näytä ammatti', 'omakeikka-wp-theme' ),
				'search_items'       => __( 'Etsi ammatteja', 'omakeikka-wp-theme' ),
				'not_found'          => __( 'Ammatteja ei löydy', 'omakeikka-wp-theme' ),
				'not_found_in_trash' => __( 'Ammatteja ei löydy roskakorista', 'omakeikka-wp-theme' ),
				'menu_name'          => __( 'Ammatit', 'omakeikka-wp-theme' ),
			),
			'public'        => true,
			'has_archive'   => true,
			'show_in_rest'  => true,
			'rewrite'       => array(
				'slug'       => 'ammatit',
				'with_front' => false,
			),
			'supports'      => array( 'title', 'editor', 'excerpt', 'thumbnail', 'custom-fields' ),
			'menu_icon'     => 'dashicons-businessman',
			'menu_position' => 5,
		)
	);

	register_taxonomy(
		'municipality',
		'occupation',
		array(
			'labels'       => array(
				'name'          => __( 'Kunnat', 'omakeikka-wp-theme' ),
				'singular_name' => __( 'Kunta', 'omakeikka-wp-theme' ),
				'search_items'  => __( 'Etsi kuntia', 'omakeikka-wp-theme' ),
				'all_items'     => __( 'Kaikki kunnat', 'omakeikka-wp-theme' ),
				'edit_item'     => __( 'Muokkaa kuntaa', 'omakeikka-wp-theme' ),
				'add_new_item'  => __( 'Lisää uusi kunta', 'omakeikka-wp-theme' ),
				'menu_name'     => __( 'Kunnat', 'omakeikka-wp-theme' ),
			),
			'public'       => true,
			'hierarchical' => false,
			'show_in_rest' => true,
			'rewrite'      => array(
				'slug'       => 'ammatit',
				'with_front' => false,
			),
		)
	);
}
