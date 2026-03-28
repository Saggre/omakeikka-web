<?php

/**
 * Seed script: occupation CPT data for local development.
 *
 * Usage:
 *   docker compose run --rm wpcli --allow-root eval-file scripts/seed-occupations.php
 *
 * This script WIPES all existing occupation posts before inserting new ones.
 * Re-running is safe.
 */

// --- Wipe existing occupation posts ---

WP_CLI::line( '' );
WP_CLI::line( '== Wiping existing occupations ==' );

$existing_ids = get_posts( array(
	'post_type'      => 'occupation',
	'post_status'    => array( 'publish', 'draft', 'private', 'trash' ),
	'posts_per_page' => -1,
	'fields'         => 'ids',
) );

foreach ( $existing_ids as $id ) {
	wp_delete_post( $id, true );
}

WP_CLI::success( sprintf( '  Deleted %d occupation posts.', count( $existing_ids ) ) );

// --- Municipality terms (idempotent, not wiped) ---

$municipalities = array(
	array( 'name' => 'Helsinki',  'slug' => 'helsinki',  'id' => '091' ),
	array( 'name' => 'Espoo',     'slug' => 'espoo',     'id' => '049' ),
	array( 'name' => 'Tampere',   'slug' => 'tampere',   'id' => '837' ),
	array( 'name' => 'Vantaa',    'slug' => 'vantaa',    'id' => '092' ),
	array( 'name' => 'Oulu',      'slug' => 'oulu',      'id' => '564' ),
	array( 'name' => 'Turku',     'slug' => 'turku',     'id' => '853' ),
	array( 'name' => 'Jyväskylä', 'slug' => 'jyvaskyla', 'id' => '179' ),
	array( 'name' => 'Lahti',     'slug' => 'lahti',     'id' => '398' ),
);

// Occupations sourced from prompts/occupations_28032026.json.
// Ordered by employee count (descending) — higher count = more active users.
// Titles are plural nominative (Finnish standard for occupation CPTs).
// cta_singular: singular nominative, used in "Aloita X-haku" and "Oletko X?" CTAs.
// cta_partitive: plural partitive, used in "Etsitkö X?" CTAs.
// alt_titles: alternative plural names for the same ISCO group.
$occupations = array(
	array(
		'isco_code'     => '5321',
		'title'         => 'Lähihoitajat',
		'cta_singular'  => 'lähihoitaja',
		'cta_partitive' => 'lähihoitajia',
		'alt_titles'    => array( 'Hoiva-avustajat' ),
		'excerpt'       => 'Lähihoitajat tarjoavat ammatillista hoivaa ja tukea ihmisille kotona ja hoivakodeissa. Omakeikasta löydät kokeneen lähihoitajan tai tarjoat osaamistasi työnantajille.',
		'content'       => "<h2>Mitä lähihoitajat tekevät?</h2>\n<p>Lähihoitajat avustavat ikääntyneitä ja toimintarajoitteisia ihmisiä päivittäisissä toiminnoissa kuten peseytymisessä, ruokailussa ja liikkumisessa. He työskentelevät kotihoidossa, palvelutaloissa ja sairaaloissa.</p>\n<h2>Lähihoitajien kysyntä Suomessa</h2>\n<p>Väestön ikääntyessä lähihoitajien tarve kasvaa jatkuvasti. Erityisesti kotihoidossa ja ympärivuorokautisessa palveluasumisessa on pula ammattitaitoisista hoitajista ympäri Suomen.</p>",
		'municipalities' => array( 'helsinki', 'espoo', 'tampere', 'vantaa', 'oulu', 'turku', 'jyvaskyla', 'lahti' ),
	),
	array(
		'isco_code'     => '2512',
		'title'         => 'Ohjelmistokehittäjät',
		'cta_singular'  => 'ohjelmistokehittäjä',
		'cta_partitive' => 'ohjelmistokehittäjiä',
		'alt_titles'    => array( 'Käyttöliittymäkehittäjät', 'Pilviarkkitehdit', 'IoT-kehittäjät' ),
		'excerpt'       => 'Ohjelmistokehittäjät suunnittelevat ja rakentavat sovelluksia pilvessä, mobiilissa ja verkossa. Löydä kokenut kehittäjä projektiisi tai hae uusia toimeksiantoja omakeikassa.',
		'content'       => "<h2>Mitä ohjelmistokehittäjät tekevät?</h2>\n<p>Ohjelmistokehittäjät vastaavat sovellusten, järjestelmien ja rajapintojen suunnittelusta ja toteutuksesta. Osaaminen kattaa web-kehityksen, mobiilisovellukset, pilvipalvelut ja IoT-ratkaisut.</p>\n<h2>Ohjelmistokehittäjien kysyntä Suomessa</h2>\n<p>IT-alan ammattilaisilla on jatkuva kysyntä erityisesti pääkaupunkiseudulla, Tampereella ja Oulussa. Etätyömahdollisuudet laajentavat markkinan koko maahan.</p>",
		'municipalities' => array( 'helsinki', 'espoo', 'tampere', 'vantaa', 'oulu', 'turku' ),
	),
	array(
		'isco_code'     => '9112',
		'title'         => 'Siivoojat',
		'cta_singular'  => 'siivooja',
		'cta_partitive' => 'siivoojia',
		'alt_titles'    => array( 'Laitossiivoojat', 'Siivoustyöntekijät' ),
		'excerpt'       => 'Siivoojat pitävät kodit, toimistot ja julkiset tilat siistinä ja viihtyisinä. Löydä luotettava siivooja tai tarjoa siivouspalvelujasi omakeikassa.',
		'content'       => "<h2>Mitä siivoojat tekevät?</h2>\n<p>Siivoojat huolehtivat tilojen puhtaanapidosta kodin, toimiston, sairaalan tai hotellin tarpeisiin. Työ sisältää pintapuhdistuksen lisäksi perussiivousta, ikkunanpesua ja lattiahoitoa.</p>\n<h2>Siivouspalvelujen kysyntä Suomessa</h2>\n<p>Siivouspalveluilla on tasainen kysyntä kotipalveluista suuriin kiinteistöihin. Kotitalousvähennys tekee yksityishenkilöille siivoajan palkkaamisesta taloudellisesti kannattavaa.</p>",
		'municipalities' => array( 'helsinki', 'espoo', 'tampere', 'vantaa', 'oulu', 'turku', 'jyvaskyla', 'lahti' ),
	),
	array(
		'isco_code'     => '5153',
		'title'         => 'Kiinteistönhoitajat',
		'cta_singular'  => 'kiinteistönhoitaja',
		'cta_partitive' => 'kiinteistönhoitajia',
		'alt_titles'    => array( 'Talovahdit' ),
		'excerpt'       => 'Kiinteistönhoitajat vastaavat rakennusten teknisestä kunnossapidosta, siisteyden ylläpidosta ja pienimuotoisista huoltotöistä. Löydä ammattitaitoinen kiinteistönhoitaja taloyhtiöllesi tai kerrostalollesi.',
		'content'       => "<h2>Mitä kiinteistönhoitajat tekevät?</h2>\n<p>Kiinteistönhoitajat hoitavat rakennusten ja niiden ympäristön teknistä ylläpitoa: laitteiden tarkistuksia, pieniä korjauksia, lämmitysjärjestelmien säätöjä ja puhtaanapitoa.</p>\n<h2>Kiinteistönhoitajien kysyntä Suomessa</h2>\n<p>Taloyhtiöt, vuokranantajat ja kiinteistösijoittajat tarvitsevat luotettavia kiinteistönhoitajia ympäri vuoden. Palvelu on erityisen kysyttyä asuntoyhteisöissä.</p>",
		'municipalities' => array( 'helsinki', 'espoo', 'tampere', 'vantaa', 'oulu', 'turku', 'lahti' ),
	),
	array(
		'isco_code'     => '2221',
		'title'         => 'Sairaanhoitajat',
		'cta_singular'  => 'sairaanhoitaja',
		'cta_partitive' => 'sairaanhoitajia',
		'alt_titles'    => array( 'Erikoissairaanhoitajat' ),
		'excerpt'       => 'Sairaanhoitajat tarjoavat ammatillista hoitotyötä sairaaloissa, terveyskeskuksissa ja kotihoidossa. Löydä pätevä sairaanhoitaja hoivapalveluun tai hae sijaisuuksia ja keikkoja omakeikassa.',
		'content'       => "<h2>Mitä sairaanhoitajat tekevät?</h2>\n<p>Sairaanhoitajat toteuttavat lääketieteellistä hoitotyötä, antavat lääkkeitä, seuraavat potilaan vointia ja tekevät toimenpiteitä. Erikoissairaanhoitajat ovat syventäneet osaamistaan tietylle erikoisalalle.</p>\n<h2>Sairaanhoitajien kysyntä Suomessa</h2>\n<p>Suomessa on merkittävä sairaanhoitajapula erityisesti erikoissairaanhoidossa ja kotihoidossa. Kokeneet sairaanhoitajat ovat erittäin haluttuja keikkatyöntekijöitä.</p>",
		'municipalities' => array( 'helsinki', 'espoo', 'tampere', 'vantaa', 'oulu', 'turku', 'jyvaskyla', 'lahti' ),
	),
	array(
		'isco_code'     => '7125',
		'title'         => 'Lasittajat',
		'cta_singular'  => 'lasittaja',
		'cta_partitive' => 'lasittajia',
		'alt_titles'    => array( 'Tuulilasiasentajat', 'Lasiasentajat' ),
		'excerpt'       => 'Lasittajat asentavat ja uusivat ikkunoita, lasiovia ja tuulisuoja-aitauksia asuinkohteissa ja toimitiloissa. Löydä ammattitaitoinen lasittaja tai tarjoa palveluitasi omakeikassa.',
		'content'       => "<h2>Mitä lasittajat tekevät?</h2>\n<p>Lasittajat mittaavat, leikkaavat ja asentavat lasilevyjä ikkunoihin, oviin, kylpyhuoneisiin ja muihin kohteisiin. He korjaavat myös särkyneitä laseja ja tiivistävät ikkunarakenteita.</p>\n<h2>Lasittajien kysyntä Suomessa</h2>\n<p>Saneeraushankkeissa ja uudisrakentamisessa lasitustarve on jatkuvaa. Ammattitaitoiset lasittajat ovat kysyttyjä erityisesti energiatehokkuusremonttien yhteydessä.</p>",
		'municipalities' => array( 'helsinki', 'espoo', 'tampere', 'vantaa', 'lahti' ),
	),
	array(
		'isco_code'     => '5322',
		'title'         => 'Kotihoidon lähihoitajat',
		'cta_singular'  => 'kotihoidon lähihoitaja',
		'cta_partitive' => 'kotihoidon lähihoitajia',
		'alt_titles'    => array( 'Kotiavustajat' ),
		'excerpt'       => 'Kotihoidon lähihoitajat auttavat ikääntyneitä ja toimintarajoitteisia henkilöitä kotona selviytymisessä. Löydä luotettava kotihoitaja tai hae kotihoidon keikkatöitä omakeikassa.',
		'content'       => "<h2>Mitä kotihoidon lähihoitajat tekevät?</h2>\n<p>Kotihoidon lähihoitajat käyvät asiakkaiden kotona auttamassa päivittäisistä toiminnoista: peseytyminen, ruoan laitto, lääkkeiden ottaminen ja kodin siisteys. He myös seuraavat asiakkaan vointia ja raportoivat muutoksista.</p>\n<h2>Kotihoidon kysyntä Suomessa</h2>\n<p>Kotihoidon palveluille on kasvava tarve, kun yhä useampi ikääntynyt haluaa asua kotona mahdollisimman pitkään. Kokeneet kotihoidon ammattilaiset ovat erittäin arvostettuja.</p>",
		'municipalities' => array( 'helsinki', 'espoo', 'tampere', 'vantaa', 'oulu', 'turku', 'jyvaskyla', 'lahti' ),
	),
	array(
		'isco_code'     => '4120',
		'title'         => 'Sihteerit',
		'cta_singular'  => 'sihteeri',
		'cta_partitive' => 'sihteereitä',
		'alt_titles'    => array( 'Toimistosihteerit', 'Assistentit' ),
		'excerpt'       => 'Sihteerit hoitavat yritysten toimistotehtäviä, kuten kirjeenvaihtoa, aikataulutusta ja dokumenttien hallintaa. Löydä kokenut sihteeri projektiisi tai hae toimistotehtäviä omakeikassa.',
		'content'       => "<h2>Mitä sihteerit tekevät?</h2>\n<p>Sihteerit tukevat organisaatioiden päivittäistä toimintaa hoitamalla viestintää, arkistointia, kokousjärjestelyjä ja muita hallinnollisia tehtäviä. He ovat usein johtajien tai tiimien tärkeä tuki.</p>\n<h2>Sihteerien kysyntä Suomessa</h2>\n<p>Kokeneet sihteerit ja toimistoassistentit ovat kysyttyjä kaikenkokoisissa yrityksissä ja julkishallinnossa. Osa-aikainen tai projektiluonteinen työ sopii hyvin omakeikka-malliin.</p>",
		'municipalities' => array( 'helsinki', 'espoo', 'tampere', 'vantaa', 'oulu', 'turku' ),
	),
	array(
		'isco_code'     => '5120',
		'title'         => 'Kokit',
		'cta_singular'  => 'kokki',
		'cta_partitive' => 'kokkeja',
		'alt_titles'    => array( 'Keittäjät', 'Keittiötyöntekijät' ),
		'excerpt'       => 'Kokit valmistavat ruokaa ravintoloissa, henkilöstöravintoloissa ja yksityistilaisuuksissa. Löydä ammattikokki keikaksi tai tarjoa keittiöosaamistasi työnantajille omakeikassa.',
		'content'       => "<h2>Mitä kokit tekevät?</h2>\n<p>Kokit suunnittelevat ja valmistavat ruoka-annoksia keittiöissä. Osaaminen voi kattaa a la carte -ruoanlaiton, lounaskokkauksen, erityisruokavaliot tai suurtalouskeittiön.</p>\n<h2>Kokkien kysyntä Suomessa</h2>\n<p>Ravintola-alalla on jatkuva tarve ammattitaitoisille kokeille erityisesti sesonkiaikoina ja tapahtumissa. Keikkatyö ja tilapäiset sijaisuudet ovat alalla yleisiä.</p>",
		'municipalities' => array( 'helsinki', 'espoo', 'tampere', 'vantaa', 'turku', 'lahti' ),
	),
	array(
		'isco_code'     => '3313',
		'title'         => 'Kirjanpitäjät',
		'cta_singular'  => 'kirjanpitäjä',
		'cta_partitive' => 'kirjanpitäjiä',
		'alt_titles'    => array( 'Taloushallinnon assistentit', 'Reskontranhoitajat' ),
		'excerpt'       => 'Kirjanpitäjät hoitavat yritysten taloushallintoa, tilinpäätöksiä ja viranomaisraportointia. Löydä luotettava kirjanpitäjä yrityksellesi tai tarjoa taloushallinto-osaamistasi omakeikassa.',
		'content'       => "<h2>Mitä kirjanpitäjät tekevät?</h2>\n<p>Kirjanpitäjät kirjaavat liiketapahtumat, laativat tilinpäätökset, hoitavat palkanlaskennan ja varmistavat, että yrityksen taloudenpito täyttää lainmukaiset vaatimukset.</p>\n<h2>Kirjanpitäjien kysyntä Suomessa</h2>\n<p>Kirjanpitäjillä on tasainen kysyntä pk-yrityksissä ja tilitoimistoissa. Osa-aikainen tai etänä tehtävä kirjanpitotyö sopii hyvin eläkkeellä oleville taloushallinnon ammattilaisille.</p>",
		'municipalities' => array( 'helsinki', 'espoo', 'tampere', 'vantaa', 'oulu', 'turku', 'jyvaskyla' ),
	),
	array(
		'isco_code'     => '7111',
		'title'         => 'Talonrakentajat',
		'cta_singular'  => 'talonrakentaja',
		'cta_partitive' => 'talonrakentajia',
		'alt_titles'    => array( 'Rakennustyöntekijät' ),
		'excerpt'       => 'Talonrakentajat toteuttavat rakennustöitä uudisrakentamisessa ja saneerauskohteissa. Löydä kokenut rakentaja projektiisi tai ilmoita rakennusosaamisesi omakeikassa.',
		'content'       => "<h2>Mitä talonrakentajat tekevät?</h2>\n<p>Talonrakentajat tekevät kaikentyyppisiä rakennustöitä: perustukset, runkorakenteet, eristykset ja viimeistelyn. He voivat toimia sekä uudisrakentamisessa että korjausrakentamisessa.</p>\n<h2>Talonrakentajien kysyntä Suomessa</h2>\n<p>Rakennusalan ammattilaisia tarvitaan jatkuvasti. Kokeneet rakentajat ovat erittäin arvostettuja remonteissa ja pientalorakentamisessa koko Suomessa.</p>",
		'municipalities' => array( 'helsinki', 'espoo', 'tampere', 'vantaa', 'oulu', 'lahti' ),
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
	update_post_meta( $post_id, 'cta_singular', $occ['cta_singular'] );
	update_post_meta( $post_id, 'cta_partitive', $occ['cta_partitive'] );

	if ( ! empty( $occ['alt_titles'] ) ) {
		update_post_meta( $post_id, 'alt_titles', wp_json_encode( $occ['alt_titles'], JSON_UNESCAPED_UNICODE ) );
	}

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
		'  create %s (ISCO %s, post_id=%d, alt: %s)',
		$occ['title'],
		$occ['isco_code'],
		$post_id,
		implode( ', ', $occ['alt_titles'] )
	) );
}

// Clear transients so sitewide partials and occupation pages reflect new data.
delete_transient( 'occupation_links' );
global $wpdb;
$wpdb->query( "DELETE FROM {$wpdb->options} WHERE option_name LIKE '_transient_occupation_%'" );
$wpdb->query( "DELETE FROM {$wpdb->options} WHERE option_name LIKE '_transient_timeout_occupation_%'" );

WP_CLI::line( '' );
WP_CLI::success( 'Done. Run `wp --allow-root cache flush` if object caching is active.' );
