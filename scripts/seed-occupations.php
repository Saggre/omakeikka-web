<?php

/**
 * Seed script: occupation CPT mock data for local development.
 *
 * Usage:
 *   docker compose run --rm wpcli --allow-root eval-file scripts/seed-occupations.php
 *
 * Re-running is safe - existing posts and terms are skipped.
 */

$municipalities = array(
	array( 'name' => 'Helsinki',  'slug' => 'helsinki',  'id' => '091' ),
	array( 'name' => 'Espoo',     'slug' => 'espoo',     'id' => '049' ),
	array( 'name' => 'Tampere',   'slug' => 'tampere',   'id' => '837' ),
	array( 'name' => 'Vantaa',    'slug' => 'vantaa',    'id' => '092' ),
	array( 'name' => 'Oulu',      'slug' => 'oulu',      'id' => '564' ),
	array( 'name' => 'Turku',     'slug' => 'turku',     'id' => '853' ),
);

$occupations = array(
	array(
		'isco_code'      => '2514',
		'title'          => 'Sovellusohjelmoijat',
		'excerpt'        => 'Sovellusohjelmoijat suunnittelevat, kehittävät ja testaavat ohjelmistosovelluksia. Omakeikasta löydät osaavat sovelluskehittäjät tai löydät itsellesi uusia projekteja.',
		'content'        => "<h2>Mitä sovellusohjelmoijat tekevät?</h2>\n<p>Sovellusohjelmoijat vastaavat ohjelmistosovellusten suunnittelusta, kehittämisestä ja ylläpidosta. Tyypillisiä tehtäviä ovat ohjelmakoodin kirjoittaminen, testaaminen ja dokumentointi.</p>\n<h2>Sovellusohjelmoijien kysyntä Suomessa</h2>\n<p>Ohjelmistokehittäjien kysyntä on Suomessa korkea erityisesti suurissa kaupungeissa kuten Helsingissä, Espoossa ja Tampereella. Alalle tullaan usein tietojenkäsittelytieteen tai insinööriopintojen kautta.</p>",
		'municipalities' => array( 'helsinki', 'espoo', 'tampere', 'vantaa', 'oulu', 'turku' ),
	),
	array(
		'isco_code'      => '2511',
		'title'          => 'Sovellusarkkitehdit',
		'excerpt'        => 'Sovellusarkkitehdit suunnittelevat ohjelmistojärjestelmien rakenteen ja varmistavat teknisten ratkaisujen laadun. Löydä kokenut arkkitehti tai tarjoa osaamistasi omakeikassa.',
		'content'        => "<h2>Mitä sovellusarkkitehdit tekevät?</h2>\n<p>Sovellusarkkitehdit vastaavat ohjelmistojärjestelmien kokonaisarkkitehtuurista. He tekevät päätöksiä teknologioista, ohjelmistorakenteesta ja parhaista käytännöistä.</p>\n<h2>Sovellusarkkitehtien kysyntä Suomessa</h2>\n<p>Kokeneiden sovellusarkkitehtien kysyntä on jatkuvaa erityisesti finanssialan ja teknologiayritysten parissa pääkaupunkiseudulla.</p>",
		'municipalities' => array( 'helsinki', 'espoo', 'tampere', 'turku' ),
	),
	array(
		'isco_code'      => '7126',
		'title'          => 'Putkiasentajat',
		'excerpt'        => 'Putkiasentajat asentavat, huoltavat ja korjaavat vesi-, viemäri- ja lämmitysjärjestelmiä. Löydä ammattitaitoinen putkiasentaja lähistöltä tai tarjoa palveluitasi omakeikassa.',
		'content'        => "<h2>Mitä putkiasentajat tekevät?</h2>\n<p>Putkiasentajat vastaavat rakennusten vesi-, viemäri- ja lämmitysjärjestelmien asennuksesta, huollosta ja korjauksesta. Työ edellyttää ammattitutkintoa ja käytännön kokemusta.</p>\n<h2>Putkiasentajien kysyntä Suomessa</h2>\n<p>Putkiasennusalan ammattilaisilla on jatkuva kysyntä ympäri Suomen. Erityisesti uudisrakentamisen vilkkaina aikoina osaajista on pulaa kaikissa suurissa kaupungeissa.</p>",
		'municipalities' => array( 'helsinki', 'espoo', 'tampere', 'vantaa', 'oulu', 'turku' ),
	),
	array(
		'isco_code'      => '7121',
		'title'          => 'Rakennuspuusepät',
		'excerpt'        => 'Rakennuspuusepät vastaavat puuosien asennuksesta, viimeistelyistä ja korjauksista rakennuskohteissa. Löydä kokenut rakennuspuuseppä tai ilmoita osaamisesi omakeikassa.',
		'content'        => "<h2>Mitä rakennuspuusepät tekevät?</h2>\n<p>Rakennuspuusepät tekevät puuosien asennuksia, kuten ovia, ikkunoita, listoja ja kalusteita rakennustyömailla sekä remonttikohteissa.</p>\n<h2>Rakennuspuuseppien kysyntä Suomessa</h2>\n<p>Rakennuspuuseppiä tarvitaan erityisesti uudiskohteissa ja saneeraushankkeissa. Kysyntä on tasaista ympäri vuoden.</p>",
		'municipalities' => array( 'helsinki', 'espoo', 'tampere', 'vantaa' ),
	),
	array(
		'isco_code'      => '2411',
		'title'          => 'Kirjanpitäjät',
		'excerpt'        => 'Kirjanpitäjät vastaavat yritysten taloudellisten tapahtumien kirjaamisesta ja raportoinnista. Löydä luotettava kirjanpitäjä yrityksellesi tai tarjoa palveluitasi omakeikassa.',
		'content'        => "<h2>Mitä kirjanpitäjät tekevät?</h2>\n<p>Kirjanpitäjät hoitavat yritysten kirjanpidon, tilinpäätökset, palkanlaskennan ja viranomaisraportoinnin. Työ vaatii taloushallinnon koulutuksen ja tarkkuutta.</p>\n<h2>Kirjanpitäjien kysyntä Suomessa</h2>\n<p>Taloushallinnon osaajia tarvitaan kaikenkokoisissa yrityksissä. Kirjanpitäjät voivat toimia palkansaajina tai itsenäisinä yrittäjinä.</p>",
		'municipalities' => array( 'helsinki', 'espoo', 'tampere', 'oulu', 'turku' ),
	),
	array(
		'isco_code'      => '2521',
		'title'          => 'Tietokantasuunnittelijat',
		'excerpt'        => 'Tietokantasuunnittelijat suunnittelevat ja ylläpitävät tietokantojen rakennetta ja toimivuutta. Löydä osaava tietokanta-asiantuntija tai hae uutta haastetta omakeikassa.',
		'content'        => "<h2>Mitä tietokantasuunnittelijat tekevät?</h2>\n<p>Tietokantasuunnittelijat vastaavat tietokantojen mallintamisesta, optimoinnista ja ylläpidosta. He työskentelevät usein tiiviissä yhteistyössä ohjelmistokehittäjien kanssa.</p>\n<h2>Tietokantasuunnittelijoiden kysyntä Suomessa</h2>\n<p>Data-alan kasvun myötä tietokantaosaaminen on entistä arvostetumpaa. Erityisesti pilvi- ja analytiikkaosaaminen on kysyttyä.</p>",
		'municipalities' => array( 'helsinki', 'espoo', 'tampere', 'turku' ),
	),
);

// --- Create municipality terms ---

WP_CLI::line( '' );
WP_CLI::line( '== Municipalities ==' );

$term_ids = array();

foreach ( $municipalities as $m ) {
	$existing = get_term_by( 'slug', $m['slug'], 'municipality' );

	if ( $existing ) {
		$term_ids[ $m['slug'] ] = $existing->term_id;
		WP_CLI::line( sprintf( '  skip  %s (exists, term_id=%d)', $m['name'], $existing->term_id ) );
		continue;
	}

	$result = wp_insert_term( $m['name'], 'municipality', array( 'slug' => $m['slug'] ) );

	if ( is_wp_error( $result ) ) {
		WP_CLI::warning( sprintf( '  error %s: %s', $m['name'], $result->get_error_message() ) );
		continue;
	}

	$term_id = $result['term_id'];
	update_term_meta( $term_id, 'municipality_id', $m['id'] );
	$term_ids[ $m['slug'] ] = $term_id;

	WP_CLI::success( sprintf( '  create %s (term_id=%d, municipality_id=%s)', $m['name'], $term_id, $m['id'] ) );
}

// --- Create occupation posts ---

WP_CLI::line( '' );
WP_CLI::line( '== Occupations ==' );

foreach ( $occupations as $occ ) {
	$existing = get_posts( array(
		'post_type'      => 'occupation',
		'post_status'    => 'publish',
		'posts_per_page' => 1,
		'meta_key'       => 'isco_code',
		'meta_value'     => $occ['isco_code'],
		'fields'         => 'ids',
	) );

	if ( ! empty( $existing ) ) {
		WP_CLI::line( sprintf( '  skip  %s (ISCO %s, post_id=%d)', $occ['title'], $occ['isco_code'], $existing[0] ) );
		continue;
	}

	$post_id = wp_insert_post( array(
		'post_type'    => 'occupation',
		'post_status'  => 'publish',
		'post_title'   => $occ['title'],
		'post_excerpt' => $occ['excerpt'],
		'post_content' => $occ['content'],
	), true );

	if ( is_wp_error( $post_id ) ) {
		WP_CLI::warning( sprintf( '  error %s: %s', $occ['title'], $post_id->get_error_message() ) );
		continue;
	}

	update_post_meta( $post_id, 'isco_code', $occ['isco_code'] );

	$assigned_term_ids = array();
	foreach ( $occ['municipalities'] as $slug ) {
		if ( isset( $term_ids[ $slug ] ) ) {
			$assigned_term_ids[] = $term_ids[ $slug ];
		}
	}

	if ( ! empty( $assigned_term_ids ) ) {
		wp_set_post_terms( $post_id, $assigned_term_ids, 'municipality' );
	}

	WP_CLI::success( sprintf(
		'  create %s (ISCO %s, post_id=%d, municipalities: %s)',
		$occ['title'],
		$occ['isco_code'],
		$post_id,
		implode( ', ', $occ['municipalities'] )
	) );
}

// Clear the occupation_links transient so the sitewide partial reflects new data.
delete_transient( 'occupation_links' );

WP_CLI::line( '' );
WP_CLI::success( 'Done. Run `wp --allow-root cache flush` if object caching is active.' );
