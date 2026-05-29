<?php

/**
 * Seed script: marketplace_category CPT data for local development.
 *
 * Usage:
 *   docker compose run --rm wpcli --allow-root eval-file scripts/seed-marketplace-categories.php
 *
 * Wipes all existing marketplace_category posts and recreates them.
 * marketplace_group terms are created idempotently (looked up by group_key term meta).
 *
 * Data is copied verbatim from the omakeikka Laravel app to keep the
 * marketplace categories identical across the two systems:
 *   - Categories: database/seeders/MarketplaceCategorySeeder.php
 *   - Icon classes: resources/ts/composables/useCategoryIcon.ts
 *   - Group names + group icons: resources/ts/composables/useCategoryGroups.ts
 */

// --- Wipe existing marketplace_category posts ---

WP_CLI::line( '' );
WP_CLI::line( '== Wiping existing marketplace categories ==' );

$existing_ids = get_posts( array(
	'post_type'      => 'marketplace_category',
	'post_status'    => array( 'publish', 'draft', 'private', 'trash' ),
	'posts_per_page' => -1,
	'fields'         => 'ids',
) );

foreach ( $existing_ids as $id ) {
	wp_delete_post( $id, true );
}

WP_CLI::success( sprintf( '  Deleted %d marketplace category posts.', count( $existing_ids ) ) );

// --- Groups ---
// key, name (Finnish), Bootstrap Icons class, sort_order range [min, max]
// Names match the Finnish UI in omakeikka (the TS file uses English keys for i18n).

$groups = array(
	'piha'        => array( 'name' => 'Piha & ulkotyöt',       'icon' => 'bi bi-tree',           'range' => array( 10, 99 ) ),
	'kodinhoito'  => array( 'name' => 'Kodinhoito & siivous',  'icon' => 'bi bi-house',          'range' => array( 100, 199 ) ),
	'kauppa'      => array( 'name' => 'Kauppa & asiointi',     'icon' => 'bi bi-basket2',        'range' => array( 200, 299 ) ),
	'muutto'      => array( 'name' => 'Muutto & kantaminen',   'icon' => 'bi bi-box-seam',       'range' => array( 300, 399 ) ),
	'hoiva'       => array( 'name' => 'Hoiva & apu',           'icon' => 'bi bi-heart-pulse',    'range' => array( 400, 499 ) ),
	'auto'        => array( 'name' => 'Auto & liikenne',       'icon' => 'bi bi-car-front',      'range' => array( 500, 599 ) ),
	'tekniikka'   => array( 'name' => 'Tekniikka & laitteet',  'icon' => 'bi bi-laptop',         'range' => array( 600, 699 ) ),
	'asennukset'  => array( 'name' => 'Asennukset',            'icon' => 'bi bi-tools',          'range' => array( 700, 799 ) ),
	'remontti'    => array( 'name' => 'Remontti',              'icon' => 'bi bi-hammer',         'range' => array( 800, 899 ) ),
	'ruoka'       => array( 'name' => 'Ruoka & ateria',        'icon' => 'bi bi-cup-hot',        'range' => array( 900, 999 ) ),
	'etaapu'      => array( 'name' => 'Etäapu & toimisto',     'icon' => 'bi bi-headset',        'range' => array( 1000, 1099 ) ),
	'muu'         => array( 'name' => 'Muu',                   'icon' => 'bi bi-three-dots',     'range' => array( 9900, 9999 ) ),
);

// --- Categories ---
// Verbatim copy of omakeikka database/seeders/MarketplaceCategorySeeder.php
// plus icon_class from useCategoryIcon.ts (one icon per slug).

$categories = array(
	// Piha & ulkotyöt
	array( 'sort_order' => 10,   'slug' => 'ruohonleikkuu',           'name_fi' => 'Ruohonleikkuu',                       'icon' => 'bi bi-scissors' ),
	array( 'sort_order' => 20,   'slug' => 'lumityot',                'name_fi' => 'Lumityöt',                            'icon' => 'bi bi-snow' ),
	array( 'sort_order' => 30,   'slug' => 'haravointi',              'name_fi' => 'Haravointi',                          'icon' => 'bi bi-tree' ),
	array( 'sort_order' => 40,   'slug' => 'puutarhan-hoito',         'name_fi' => 'Puutarhan hoito',                     'icon' => 'bi bi-flower2' ),
	array( 'sort_order' => 50,   'slug' => 'pensaiden-leikkaus',      'name_fi' => 'Pensaiden ja puiden leikkaus',        'icon' => 'bi bi-flower1' ),
	array( 'sort_order' => 60,   'slug' => 'pihan-siistiminen',       'name_fi' => 'Pihan siistiminen',                   'icon' => 'bi bi-bucket' ),
	array( 'sort_order' => 70,   'slug' => 'mullan-levitys',          'name_fi' => 'Mullan, hiekan tai soran levitys',    'icon' => 'bi bi-basket3' ),
	array( 'sort_order' => 80,   'slug' => 'istutukset',              'name_fi' => 'Istutukset ja multaus',               'icon' => 'bi bi-flower3' ),
	array( 'sort_order' => 90,   'slug' => 'hiekoitus',               'name_fi' => 'Hiekoitus ja liukkaudentorjunta',     'icon' => 'bi bi-thermometer-snow' ),

	// Kodinhoito & siivous
	array( 'sort_order' => 110,  'slug' => 'siivous',                 'name_fi' => 'Siivous',                             'icon' => 'bi bi-brush' ),
	array( 'sort_order' => 120,  'slug' => 'ikkunanpesu',             'name_fi' => 'Ikkunanpesu',                         'icon' => 'bi bi-window' ),
	array( 'sort_order' => 130,  'slug' => 'pyykinpesu',              'name_fi' => 'Pyykinpesu ja silitys',               'icon' => 'bi bi-droplet-half' ),
	array( 'sort_order' => 140,  'slug' => 'astianpesu',              'name_fi' => 'Astianpesu',                          'icon' => 'bi bi-cup' ),
	array( 'sort_order' => 150,  'slug' => 'roskien-vienti',          'name_fi' => 'Roskien vienti ja kierrätys',         'icon' => 'bi bi-trash3' ),
	array( 'sort_order' => 160,  'slug' => 'verhot',                  'name_fi' => 'Verhojen pesu ja vaihto',             'icon' => 'bi bi-layout-text-window' ),
	array( 'sort_order' => 170,  'slug' => 'jarjestely',              'name_fi' => 'Kodin järjestely ja siistiminen',     'icon' => 'bi bi-box-seam' ),

	// Kauppa & asiointi
	array( 'sort_order' => 210,  'slug' => 'ruokaostokset',           'name_fi' => 'Ruokaostokset',                       'icon' => 'bi bi-basket2' ),
	array( 'sort_order' => 220,  'slug' => 'apteekkiasiointi',        'name_fi' => 'Apteekkiasiointi',                    'icon' => 'bi bi-capsule' ),
	array( 'sort_order' => 230,  'slug' => 'posti-ja-paketit',        'name_fi' => 'Postin ja pakettien nouto',           'icon' => 'bi bi-mailbox' ),
	array( 'sort_order' => 240,  'slug' => 'virastoasiointi',         'name_fi' => 'Virastoasiointi',                     'icon' => 'bi bi-building' ),
	array( 'sort_order' => 250,  'slug' => 'pankkiasiointi',          'name_fi' => 'Pankkiasiointi',                      'icon' => 'bi bi-bank' ),
	array( 'sort_order' => 260,  'slug' => 'kirjastoasiointi',        'name_fi' => 'Kirjastoasiointi',                    'icon' => 'bi bi-book' ),
	array( 'sort_order' => 270,  'slug' => 'muut-asioinnit',          'name_fi' => 'Muut asioinnit',                      'icon' => 'bi bi-bag-check' ),

	// Muutto & kantaminen
	array( 'sort_order' => 310,  'slug' => 'muuttonapu',              'name_fi' => 'Muuttonapu',                          'icon' => 'bi bi-truck' ),
	array( 'sort_order' => 320,  'slug' => 'tavaroiden-siirto',       'name_fi' => 'Tavaroiden siirto',                   'icon' => 'bi bi-box-arrow-right' ),
	array( 'sort_order' => 330,  'slug' => 'kuljetusapu',             'name_fi' => 'Kuljetusapu (kirpputori, IKEA jne.)', 'icon' => 'bi bi-truck-flatbed' ),
	array( 'sort_order' => 340,  'slug' => 'huonekalujen-siirto',     'name_fi' => 'Huonekalujen siirto',                 'icon' => 'bi bi-arrows-move' ),

	// Hoiva & apu
	array( 'sort_order' => 410,  'slug' => 'lastenhoitoapu',          'name_fi' => 'Lastenhoitoapu',                                  'icon' => 'bi bi-emoji-smile' ),
	array( 'sort_order' => 420,  'slug' => 'lastenkuljetus',          'name_fi' => 'Lasten kuljettaminen (koulu, harrastukset)',     'icon' => 'bi bi-mortarboard' ),
	array( 'sort_order' => 430,  'slug' => 'lemmikkien-hoito',        'name_fi' => 'Lemmikkien hoito',                                'icon' => 'bi bi-heart-pulse' ),
	array( 'sort_order' => 440,  'slug' => 'koiran-ulkoilutus',       'name_fi' => 'Koiran ulkoilutus',                               'icon' => 'bi bi-moon-stars' ),
	array( 'sort_order' => 450,  'slug' => 'vanhusapu',               'name_fi' => 'Vanhusapu ja seuralaistoiminta',                  'icon' => 'bi bi-person-hearts' ),
	array( 'sort_order' => 460,  'slug' => 'saattajapalvelu',         'name_fi' => 'Saattajapalvelu (lääkäri, virasto)',              'icon' => 'bi bi-people' ),

	// Auto & liikenne
	array( 'sort_order' => 510,  'slug' => 'kyyditys',                'name_fi' => 'Kyyditys',                            'icon' => 'bi bi-car-front' ),
	array( 'sort_order' => 520,  'slug' => 'lentokenttakyyti',        'name_fi' => 'Lentokenttäkyyditys',                 'icon' => 'bi bi-airplane' ),
	array( 'sort_order' => 530,  'slug' => 'renkaanvaihto',           'name_fi' => 'Renkaanvaihto',                       'icon' => 'bi bi-gear-wide-connected' ),
	array( 'sort_order' => 540,  'slug' => 'auton-pesu',              'name_fi' => 'Auton pesu ja siistiminen',           'icon' => 'bi bi-droplet' ),
	array( 'sort_order' => 550,  'slug' => 'perakarry',               'name_fi' => 'Peräkärryn lainaus tai kuljetus',     'icon' => 'bi bi-truck-front' ),

	// Tekniikka & laitteet
	array( 'sort_order' => 610,  'slug' => 'tietokone-apu',           'name_fi' => 'Tietokone- ja puhelinapu',            'icon' => 'bi bi-laptop' ),
	array( 'sort_order' => 620,  'slug' => 'nettiyhteys',             'name_fi' => 'Nettiyhteysapu ja reititin',          'icon' => 'bi bi-wifi' ),
	array( 'sort_order' => 630,  'slug' => 'tv-ja-aani',              'name_fi' => 'TV- ja äänijärjestelmät',             'icon' => 'bi bi-tv' ),
	array( 'sort_order' => 640,  'slug' => 'valvontakamerat',         'name_fi' => 'Valvontakameroiden asennus',          'icon' => 'bi bi-camera-video' ),

	// Asennukset
	array( 'sort_order' => 710,  'slug' => 'valaisimien-asennus',     'name_fi' => 'Valaisimien asennus',                 'icon' => 'bi bi-lightbulb' ),
	array( 'sort_order' => 720,  'slug' => 'hyllyt-ja-taulut',        'name_fi' => 'Hyllyjen ja taulujen ripustus',       'icon' => 'bi bi-easel' ),
	array( 'sort_order' => 730,  'slug' => 'verhotangot',             'name_fi' => 'Verhotankojen asennus',               'icon' => 'bi bi-layout-three-columns' ),
	array( 'sort_order' => 740,  'slug' => 'liesituuletin',           'name_fi' => 'Liesituulettimen asennus',            'icon' => 'bi bi-fan' ),
	array( 'sort_order' => 750,  'slug' => 'kodinkoneet',             'name_fi' => 'Kodinkoneiden asennus ja käyttöönotto', 'icon' => 'bi bi-plug' ),
	array( 'sort_order' => 760,  'slug' => 'ilmalampopumppu',         'name_fi' => 'Ilmalämpöpumpun asennus tai puhdistus', 'icon' => 'bi bi-thermometer-sun' ),
	array( 'sort_order' => 770,  'slug' => 'suodattimet',             'name_fi' => 'Ilmanvaihdon suodatinten vaihto',     'icon' => 'bi bi-funnel' ),
	array( 'sort_order' => 780,  'slug' => 'suihku-ja-allas',         'name_fi' => 'Suihkukaapin ja altaan asennus',      'icon' => 'bi bi-droplet-fill' ),

	// Remontti
	array( 'sort_order' => 810,  'slug' => 'huonekalujen-kokoaminen', 'name_fi' => 'Huonekalujen kokoaminen (IKEA jne.)', 'icon' => 'bi bi-hammer' ),
	array( 'sort_order' => 820,  'slug' => 'pienet-korjaukset',       'name_fi' => 'Pienet korjaustyöt',                  'icon' => 'bi bi-wrench-adjustable' ),
	array( 'sort_order' => 830,  'slug' => 'maalaus',                 'name_fi' => 'Maalaus (sisätilat)',                 'icon' => 'bi bi-palette' ),
	array( 'sort_order' => 840,  'slug' => 'tapetoiminen',            'name_fi' => 'Tapetoiminen',                        'icon' => 'bi bi-card-image' ),
	array( 'sort_order' => 850,  'slug' => 'lattia',                  'name_fi' => 'Lattiatyöt (laminaatti, parketti)',   'icon' => 'bi bi-grid-1x2' ),
	array( 'sort_order' => 860,  'slug' => 'kaakelointi',             'name_fi' => 'Kaakelityöt',                         'icon' => 'bi bi-grid-3x3-gap' ),
	array( 'sort_order' => 870,  'slug' => 'tiivistys',               'name_fi' => 'Tiivistys- ja saumaustyöt',           'icon' => 'bi bi-paint-bucket' ),
	array( 'sort_order' => 880,  'slug' => 'aita',                    'name_fi' => 'Aidan rakennus tai korjaus',          'icon' => 'bi bi-columns-gap' ),
	array( 'sort_order' => 890,  'slug' => 'terassi',                 'name_fi' => 'Terassin rakennus tai korjaus',       'icon' => 'bi bi-house' ),
	array( 'sort_order' => 895,  'slug' => 'ulkomaalaus',             'name_fi' => 'Maalaus (ulkotilat ja rakenteet)',    'icon' => 'bi bi-brush-fill' ),

	// Ruoka & ateria
	array( 'sort_order' => 910,  'slug' => 'ruoanlaitto',             'name_fi' => 'Ruoanlaittoapu',                      'icon' => 'bi bi-cup-hot' ),
	array( 'sort_order' => 920,  'slug' => 'juhlien-valmistelu',      'name_fi' => 'Juhlien ja tapahtumien valmistelu',   'icon' => 'bi bi-balloon' ),
	array( 'sort_order' => 930,  'slug' => 'leivonta',                'name_fi' => 'Leivonta',                            'icon' => 'bi bi-egg-fried' ),
	array( 'sort_order' => 940,  'slug' => 'aterioiden-kuljetus',     'name_fi' => 'Aterioiden kuljetus',                 'icon' => 'bi bi-bag' ),

	// Etäapu & toimisto
	array( 'sort_order' => 1010, 'slug' => 'tekstin-kirjoitus',       'name_fi' => 'Tekstien kirjoittaminen ja muokkaus', 'icon' => 'bi bi-pencil' ),
	array( 'sort_order' => 1020, 'slug' => 'tulkkaus',                'name_fi' => 'Tulkkaus ja kääntäminen',             'icon' => 'bi bi-translate' ),
	array( 'sort_order' => 1030, 'slug' => 'it-tuki',                 'name_fi' => 'IT-tuki etänä',                       'icon' => 'bi bi-headset' ),
	array( 'sort_order' => 1040, 'slug' => 'valokuvaus',              'name_fi' => 'Valokuvausapu (myynti-ilmoitukset jne.)', 'icon' => 'bi bi-camera' ),
	array( 'sort_order' => 1050, 'slug' => 'tulostaminen',            'name_fi' => 'Tulostamis- ja skannausapu',          'icon' => 'bi bi-printer' ),

	// Muu
	array( 'sort_order' => 9990, 'slug' => 'muu-apu',                 'name_fi' => 'Muu apu',                             'icon' => 'bi bi-three-dots' ),
);

// --- Resolve sort_order to group_key ---

$group_key_for_sort = function ( int $sort_order ) use ( $groups ): string {
	foreach ( $groups as $key => $g ) {
		if ( $sort_order >= $g['range'][0] && $sort_order <= $g['range'][1] ) {
			return $key;
		}
	}
	return 'muu';
};

// --- Create group terms idempotently ---

WP_CLI::line( '' );
WP_CLI::line( '== Marketplace groups ==' );

$group_term_ids = array(); // group_key => term_id

foreach ( $groups as $key => $g ) {
	$found = get_terms( array(
		'taxonomy'   => 'marketplace_group',
		'meta_key'   => 'group_key',
		'meta_value' => $key,
		'hide_empty' => false,
		'number'     => 1,
	) );

	if ( ! empty( $found ) && ! is_wp_error( $found ) ) {
		$term_id                = $found[0]->term_id;
		$group_term_ids[ $key ] = $term_id;
		update_term_meta( $term_id, 'group_icon', $g['icon'] );
		update_term_meta( $term_id, 'group_sort_min', $g['range'][0] );
		WP_CLI::line( sprintf( '  skip  %s (key=%s, term_id=%d)', $g['name'], $key, $term_id ) );
		continue;
	}

	$result = wp_insert_term( $g['name'], 'marketplace_group', array( 'slug' => $key ) );

	if ( is_wp_error( $result ) ) {
		WP_CLI::warning( sprintf( '  error %s (%s): %s', $g['name'], $key, $result->get_error_message() ) );
		continue;
	}

	$term_id                = $result['term_id'];
	$group_term_ids[ $key ] = $term_id;
	update_term_meta( $term_id, 'group_key', $key );
	update_term_meta( $term_id, 'group_icon', $g['icon'] );
	update_term_meta( $term_id, 'group_sort_min', $g['range'][0] );

	WP_CLI::success( sprintf( '  create %s (key=%s, term_id=%d)', $g['name'], $key, $term_id ) );
}

// --- Create category posts ---

WP_CLI::line( '' );
WP_CLI::line( '== Marketplace categories ==' );

// menu_order doubles as the omakeikka app's marketplace_categories.id.
// Both systems iterate this same array in this same order, so 1-based
// insertion order matches the Laravel auto-increment id. Stored as
// app_category_id meta for explicit, robust deep-linking.
$menu_order = 0;
foreach ( $categories as $cat ) {
	$menu_order++;

	$post_id = wp_insert_post( array(
		'post_type'   => 'marketplace_category',
		'post_status' => 'publish',
		'post_title'  => $cat['name_fi'],
		'post_name'   => $cat['slug'],
		'menu_order'  => $menu_order,
	), true );

	if ( is_wp_error( $post_id ) ) {
		WP_CLI::warning( sprintf( '  error %s: %s', $cat['name_fi'], $post_id->get_error_message() ) );
		continue;
	}

	$group_key = $group_key_for_sort( $cat['sort_order'] );

	update_post_meta( $post_id, 'app_category_id', $menu_order );
	update_post_meta( $post_id, 'category_slug', $cat['slug'] );
	update_post_meta( $post_id, 'sort_order', $cat['sort_order'] );
	update_post_meta( $post_id, 'icon_class', $cat['icon'] );
	update_post_meta( $post_id, 'group_key', $group_key );

	if ( isset( $group_term_ids[ $group_key ] ) ) {
		wp_set_post_terms( $post_id, array( $group_term_ids[ $group_key ] ), 'marketplace_group' );
	}

	WP_CLI::success( sprintf(
		'  create %s (slug=%s, group=%s, post_id=%d)',
		$cat['name_fi'],
		$cat['slug'],
		$group_key,
		$post_id
	) );
}

// --- Clear caches ---

global $wpdb;
$wpdb->query( "DELETE FROM {$wpdb->options} WHERE option_name LIKE '_transient_marketplace_%' OR option_name LIKE '_transient_timeout_marketplace_%'" );

WP_CLI::line( '' );
WP_CLI::success( 'Done. Run `wp --allow-root cache flush` if object caching is active, then `wp rewrite flush --hard`.' );
