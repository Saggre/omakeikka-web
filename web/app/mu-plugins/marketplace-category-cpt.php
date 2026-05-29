<?php

/**
 * Registers the 'marketplace_category' custom post type and 'marketplace_group' taxonomy.
 *
 * URL structure:
 *   /palvelut/                          -> CPT archive
 *   /palvelut/{slug}/                   -> single category
 *   /palvelut/ryhma/{group-slug}/       -> group taxonomy archive
 */

add_action( 'init', 'omakeikka_register_marketplace_category_cpt' );
add_action( 'init', 'omakeikka_marketplace_group_rewrite_rule', 11 );

/**
 * Register the marketplace_category CPT and marketplace_group taxonomy.
 */
function omakeikka_register_marketplace_category_cpt(): void {
	register_post_type(
		'marketplace_category',
		array(
			'labels'        => array(
				'name'               => __( 'Palvelut', 'omakeikka-theme' ),
				'singular_name'      => __( 'Palvelu', 'omakeikka-theme' ),
				'add_new_item'       => __( 'Lisää uusi palvelu', 'omakeikka-theme' ),
				'edit_item'          => __( 'Muokkaa palvelua', 'omakeikka-theme' ),
				'new_item'           => __( 'Uusi palvelu', 'omakeikka-theme' ),
				'view_item'          => __( 'Näytä palvelu', 'omakeikka-theme' ),
				'search_items'       => __( 'Etsi palveluja', 'omakeikka-theme' ),
				'not_found'          => __( 'Palveluja ei löydy', 'omakeikka-theme' ),
				'not_found_in_trash' => __( 'Palveluja ei löydy roskakorista', 'omakeikka-theme' ),
				'menu_name'          => __( 'Palvelut', 'omakeikka-theme' ),
			),
			'public'        => true,
			'has_archive'   => true,
			'show_in_rest'  => true,
			'rewrite'       => array(
				'slug'       => 'palvelut',
				'with_front' => false,
			),
			'supports'      => array( 'title', 'editor', 'excerpt', 'thumbnail', 'custom-fields', 'page-attributes' ),
			'menu_icon'     => 'dashicons-cart',
			'menu_position' => 6,
		)
	);

	register_taxonomy(
		'marketplace_group',
		'marketplace_category',
		array(
			'labels'       => array(
				'name'          => __( 'Palveluryhmät', 'omakeikka-theme' ),
				'singular_name' => __( 'Palveluryhmä', 'omakeikka-theme' ),
				'search_items'  => __( 'Etsi ryhmiä', 'omakeikka-theme' ),
				'all_items'     => __( 'Kaikki ryhmät', 'omakeikka-theme' ),
				'edit_item'     => __( 'Muokkaa ryhmää', 'omakeikka-theme' ),
				'add_new_item'  => __( 'Lisää uusi ryhmä', 'omakeikka-theme' ),
				'menu_name'     => __( 'Palveluryhmät', 'omakeikka-theme' ),
			),
			'public'       => true,
			'hierarchical' => false,
			'show_in_rest' => true,
			'rewrite'      => array(
				'slug'       => 'palvelut/ryhma',
				'with_front' => false,
			),
		)
	);
}

/**
 * WordPress auto-generates 'palvelut/([^/]+)' for the CPT before the taxonomy
 * rule, so group archive URLs would 404. Register the group rule with 'top'
 * priority so it matches before the single-CPT rule.
 */
function omakeikka_marketplace_group_rewrite_rule(): void {
	add_rewrite_rule(
		'^palvelut/ryhma/([^/]+)/?$',
		'index.php?marketplace_group=$matches[1]',
		'top'
	);
}
