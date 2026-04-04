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
				'name'               => __( 'Ammatit', 'omakeikka-theme' ),
				'singular_name'      => __( 'Ammatti', 'omakeikka-theme' ),
				'add_new_item'       => __( 'Lisää uusi ammatti', 'omakeikka-theme' ),
				'edit_item'          => __( 'Muokkaa ammattia', 'omakeikka-theme' ),
				'new_item'           => __( 'Uusi ammatti', 'omakeikka-theme' ),
				'view_item'          => __( 'Näytä ammatti', 'omakeikka-theme' ),
				'search_items'       => __( 'Etsi ammatteja', 'omakeikka-theme' ),
				'not_found'          => __( 'Ammatteja ei löydy', 'omakeikka-theme' ),
				'not_found_in_trash' => __( 'Ammatteja ei löydy roskakorista', 'omakeikka-theme' ),
				'menu_name'          => __( 'Ammatit', 'omakeikka-theme' ),
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
				'name'          => __( 'Kunnat', 'omakeikka-theme' ),
				'singular_name' => __( 'Kunta', 'omakeikka-theme' ),
				'search_items'  => __( 'Etsi kuntia', 'omakeikka-theme' ),
				'all_items'     => __( 'Kaikki kunnat', 'omakeikka-theme' ),
				'edit_item'     => __( 'Muokkaa kuntaa', 'omakeikka-theme' ),
				'add_new_item'  => __( 'Lisää uusi kunta', 'omakeikka-theme' ),
				'menu_name'     => __( 'Kunnat', 'omakeikka-theme' ),
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
