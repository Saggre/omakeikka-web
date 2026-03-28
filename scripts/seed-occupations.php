<?php

/**
 * Seed script: occupation CPT data for local development.
 *
 * Usage:
 *   docker compose run --rm wpcli --allow-root eval-file scripts/seed-occupations.php
 *
 * This script WIPES all existing occupation posts before inserting new ones.
 * Municipality taxonomy terms are created idempotently (looked up by municipality_id meta).
 * Re-running is safe.
 *
 * Municipality assignments sourced from production API:
 *   GET https://app.omakeikka.fi/api/occupations/{isco_code}/municipalities
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

// --- Municipality data: id => name ---
// All municipalities that appear in at least one occupation's API response.

$municipalities = array(
	'010' => 'Alavus',
	'016' => 'Asikkala',
	'018' => 'Askola',
	'020' => 'Akaa',
	'049' => 'Espoo',
	'061' => 'Forssa',
	'069' => 'Haapajärvi',
	'075' => 'Hamina',
	'082' => 'Hattula',
	'086' => 'Hausjärvi',
	'091' => 'Helsinki',
	'092' => 'Vantaa',
	'098' => 'Hollola',
	'102' => 'Huittinen',
	'103' => 'Humppila',
	'106' => 'Hyvinkää',
	'108' => 'Hämeenkyrö',
	'109' => 'Hämeenlinna',
	'111' => 'Heinola',
	'142' => 'Iitti',
	'143' => 'Ikaalinen',
	'165' => 'Janakkala',
	'167' => 'Joensuu',
	'169' => 'Jokioinen',
	'179' => 'Jyväskylä',
	'182' => 'Jämsä',
	'186' => 'Järvenpää',
	'202' => 'Kaarina',
	'205' => 'Kajaani',
	'211' => 'Kangasala',
	'224' => 'Karkkila',
	'232' => 'Kauhajoki',
	'235' => 'Kauniainen',
	'245' => 'Kerava',
	'249' => 'Keuruu',
	'257' => 'Kirkkonummi',
	'276' => 'Kontiolahti',
	'285' => 'Kotka',
	'286' => 'Kouvola',
	'297' => 'Kuopio',
	'301' => 'Kurikka',
	'398' => 'Lahti',
	'405' => 'Lappeenranta',
	'418' => 'Lempäälä',
	'423' => 'Lieto',
	'426' => 'Liperi',
	'430' => 'Loimaa',
	'433' => 'Loppi',
	'444' => 'Lohja',
	'445' => 'Parainen',
	'481' => 'Masku',
	'491' => 'Mikkeli',
	'494' => 'Muhos',
	'503' => 'Mynämäki',
	'505' => 'Mäntsälä',
	'529' => 'Naantali',
	'535' => 'Nivala',
	'536' => 'Nokia',
	'538' => 'Nousiainen',
	'543' => 'Nurmijärvi',
	'560' => 'Orimattila',
	'562' => 'Orivesi',
	'564' => 'Oulu',
	'578' => 'Paltamo',
	'581' => 'Parkano',
	'592' => 'Petäjävesi',
	'593' => 'Pieksämäki',
	'604' => 'Pirkkala',
	'609' => 'Pori',
	'611' => 'Pornainen',
	'616' => 'Pukkila',
	'624' => 'Pyhtää',
	'626' => 'Pyhäjärvi',
	'636' => 'Pöytyä',
	'638' => 'Porvoo',
	'680' => 'Raisio',
	'684' => 'Rauma',
	'687' => 'Rautavaara',
	'691' => 'Reisjärvi',
	'694' => 'Riihimäki',
	'698' => 'Rovaniemi',
	'702' => 'Ruovesi',
	'704' => 'Rusko',
	'734' => 'Salo',
	'738' => 'Sauvo',
	'743' => 'Seinäjoki',
	'749' => 'Siilinjärvi',
	'753' => 'Sipoo',
	'761' => 'Somero',
	'762' => 'Sonkajärvi',
	'765' => 'Sotkamo',
	'783' => 'Säkylä',
	'790' => 'Sastamala',
	'834' => 'Tammela',
	'837' => 'Tampere',
	'853' => 'Turku',
	'858' => 'Tuusula',
	'859' => 'Tyrnävä',
	'887' => 'Urjala',
	'895' => 'Uusikaupunki',
	'908' => 'Valkeakoski',
	'915' => 'Varkaus',
	'918' => 'Vehmaa',
	'922' => 'Vesilahti',
	'927' => 'Vihti',
	'935' => 'Virolahti',
	'980' => 'Ylöjärvi',
	'981' => 'Ypäjä',
);

// Occupations sourced from prompts/occupations_28032026.json.
// Ordered by employee count (descending) — higher count = more active users.
// Titles are plural nominative (Finnish standard for occupation CPTs).
// cta_singular: singular nominative, used in "Aloita X-haku" and "Tarvitsetko X?" CTAs.
// cta_partitive: plural partitive, used in "Etsitkö X?" CTAs.
// alt_titles: alternative plural names for the same ISCO group.
// municipalities: IDs from GET /api/occupations/{isco_code}/municipalities (production).
$occupations = array(
	array(
		'isco_code'      => '5321',
		'title'          => 'Lähihoitajat',
		'cta_singular'   => 'lähihoitaja',
		'cta_partitive'  => 'lähihoitajia',
		'alt_titles'     => array( 'Hoiva-avustajat' ),
		'excerpt'        => 'Lähihoitajat tarjoavat ammatillista hoivaa ja tukea ihmisille kotona ja hoivakodeissa. Omakeikasta löydät kokeneen lähihoitajan tai tarjoat osaamistasi työnantajille.',
		'content'        => "<h2>Mitä lähihoitajat tekevät?</h2>\n<p>Lähihoitajat avustavat ikääntyneitä ja toimintarajoitteisia ihmisiä päivittäisissä toiminnoissa kuten peseytymisessä, ruokailussa ja liikkumisessa. He työskentelevät kotihoidossa, palvelutaloissa ja sairaaloissa.</p>\n<h2>Lähihoitajien kysyntä Suomessa</h2>\n<p>Väestön ikääntyessä lähihoitajien tarve kasvaa jatkuvasti. Erityisesti kotihoidossa ja ympärivuorokautisessa palveluasumisessa on pula ammattitaitoisista hoitajista ympäri Suomen.</p>",
		'municipalities' => array( '049','086','111','091','098','106','109','165','179','186','205','245','297','398','491','494','505','564','611','680','694','704','749','753','837','853','858','859','895','092','927' ),
	),
	array(
		'isco_code'      => '2512',
		'title'          => 'Ohjelmistokehittäjät',
		'cta_singular'   => 'ohjelmistokehittäjä',
		'cta_partitive'  => 'ohjelmistokehittäjiä',
		'alt_titles'     => array( 'Käyttöliittymäkehittäjät', 'Pilviarkkitehdit', 'IoT-kehittäjät' ),
		'excerpt'        => 'Ohjelmistokehittäjät suunnittelevat ja rakentavat sovelluksia pilvessä, mobiilissa ja verkossa. Löydä kokenut kehittäjä projektiisi tai hae uusia toimeksiantoja omakeikassa.',
		'content'        => "<h2>Mitä ohjelmistokehittäjät tekevät?</h2>\n<p>Ohjelmistokehittäjät vastaavat sovellusten, järjestelmien ja rajapintojen suunnittelusta ja toteutuksesta. Osaaminen kattaa web-kehityksen, mobiilisovellukset, pilvipalvelut ja IoT-ratkaisut.</p>\n<h2>Ohjelmistokehittäjien kysyntä Suomessa</h2>\n<p>IT-alan ammattilaisilla on jatkuva kysyntä erityisesti pääkaupunkiseudulla, Tampereella ja Oulussa. Etätyömahdollisuudet laajentavat markkinan koko maahan.</p>",
		'municipalities' => array( '049','091','106','109','186','211','235','245','257','418','604','694','702','837','092','927' ),
	),
	array(
		'isco_code'      => '9112',
		'title'          => 'Siivoojat',
		'cta_singular'   => 'siivooja',
		'cta_partitive'  => 'siivoojia',
		'alt_titles'     => array( 'Laitossiivoojat', 'Siivoustyöntekijät' ),
		'excerpt'        => 'Siivoojat pitävät kodit, toimistot ja julkiset tilat siistinä ja viihtyisinä. Löydä luotettava siivooja tai tarjoa siivouspalvelujasi omakeikassa.',
		'content'        => "<h2>Mitä siivoojat tekevät?</h2>\n<p>Siivoojat huolehtivat tilojen puhtaanapidosta kodin, toimiston, sairaalan tai hotellin tarpeisiin. Työ sisältää pintapuhdistuksen lisäksi perussiivousta, ikkunanpesua ja lattiahoitoa.</p>\n<h2>Siivouspalvelujen kysyntä Suomessa</h2>\n<p>Siivouspalveluilla on tasainen kysyntä kotipalveluista suuriin kiinteistöihin. Kotitalousvähennys tekee yksityishenkilöille siivoajan palkkaamisesta taloudellisesti kannattavaa.</p>",
		'municipalities' => array( '016','049','091','445','853','092' ),
	),
	array(
		'isco_code'      => '5153',
		'title'          => 'Kiinteistönhoitajat',
		'cta_singular'   => 'kiinteistönhoitaja',
		'cta_partitive'  => 'kiinteistönhoitajia',
		'alt_titles'     => array( 'Talovahdit' ),
		'excerpt'        => 'Kiinteistönhoitajat vastaavat rakennusten teknisestä kunnossapidosta, siisteyden ylläpidosta ja pienimuotoisista huoltotöistä. Löydä ammattitaitoinen kiinteistönhoitaja taloyhtiöllesi tai kerrostalollesi.',
		'content'        => "<h2>Mitä kiinteistönhoitajat tekevät?</h2>\n<p>Kiinteistönhoitajat hoitavat rakennusten ja niiden ympäristön teknistä ylläpitoa: laitteiden tarkistuksia, pieniä korjauksia, lämmitysjärjestelmien säätöjä ja puhtaanapitoa.</p>\n<h2>Kiinteistönhoitajien kysyntä Suomessa</h2>\n<p>Taloyhtiöt, vuokranantajat ja kiinteistösijoittajat tarvitsevat luotettavia kiinteistönhoitajia ympäri vuoden. Palvelu on erityisen kysyttyä asuntoyhteisöissä.</p>",
		'municipalities' => array( '020','010','049','061','069','091','103','106','108','143','169','182','186','211','232','245','249','297','301','398','418','430','433','505','535','562','581','592','604','609','611','616','626','691','698','734','790','743','749','753','761','834','837','858','908','092','922','980','981' ),
	),
	array(
		'isco_code'      => '2221',
		'title'          => 'Sairaanhoitajat',
		'cta_singular'   => 'sairaanhoitaja',
		'cta_partitive'  => 'sairaanhoitajia',
		'alt_titles'     => array( 'Erikoissairaanhoitajat' ),
		'excerpt'        => 'Sairaanhoitajat tarjoavat ammatillista hoitotyötä sairaaloissa, terveyskeskuksissa ja kotihoidossa. Löydä pätevä sairaanhoitaja hoivapalveluun tai hae sijaisuuksia ja keikkoja omakeikassa.',
		'content'        => "<h2>Mitä sairaanhoitajat tekevät?</h2>\n<p>Sairaanhoitajat toteuttavat lääketieteellistä hoitotyötä, antavat lääkkeitä, seuraavat potilaan vointia ja tekevät toimenpiteitä. Erikoissairaanhoitajat ovat syventäneet osaamistaan tietylle erikoisalalle.</p>\n<h2>Sairaanhoitajien kysyntä Suomessa</h2>\n<p>Suomessa on merkittävä sairaanhoitajapula erityisesti erikoissairaanhoidossa ja kotihoidossa. Kokeneet sairaanhoitajat ovat erittäin haluttuja keikkatyöntekijöitä.</p>",
		'municipalities' => array( '049','091','202','423','430','529','680','853' ),
	),
	array(
		'isco_code'      => '7125',
		'title'          => 'Lasittajat',
		'cta_singular'   => 'lasittaja',
		'cta_partitive'  => 'lasittajia',
		'alt_titles'     => array( 'Tuulilasiasentajat', 'Lasiasentajat' ),
		'excerpt'        => 'Lasittajat asentavat ja uusivat ikkunoita, lasiovia ja tuulisuoja-aitauksia asuinkohteissa ja toimitiloissa. Löydä ammattitaitoinen lasittaja tai tarjoa palveluitasi omakeikassa.',
		'content'        => "<h2>Mitä lasittajat tekevät?</h2>\n<p>Lasittajat mittaavat, leikkaavat ja asentavat lasilevyjä ikkunoihin, oviin, kylpyhuoneisiin ja muihin kohteisiin. He korjaavat myös särkyneitä laseja ja tiivistävät ikkunarakenteita.</p>\n<h2>Lasittajien kysyntä Suomessa</h2>\n<p>Saneeraushankkeissa ja uudisrakentamisessa lasitustarve on jatkuvaa. Ammattitaitoiset lasittajat ovat kysyttyjä erityisesti energiatehokkuusremonttien yhteydessä.</p>",
		'municipalities' => array( '049','106','109','245','444','543','560','624','837','858','092','927' ),
	),
	array(
		'isco_code'      => '5322',
		'title'          => 'Kotihoidon lähihoitajat',
		'cta_singular'   => 'kotihoidon lähihoitaja',
		'cta_partitive'  => 'kotihoidon lähihoitajia',
		'alt_titles'     => array( 'Kotiavustajat' ),
		'excerpt'        => 'Kotihoidon lähihoitajat auttavat ikääntyneitä ja toimintarajoitteisia henkilöitä kotona selviytymisessä. Löydä luotettava kotihoitaja tai hae kotihoidon keikkatöitä omakeikassa.',
		'content'        => "<h2>Mitä kotihoidon lähihoitajat tekevät?</h2>\n<p>Kotihoidon lähihoitajat käyvät asiakkaiden kotona auttamassa päivittäisistä toiminnoista: peseytyminen, ruoan laitto, lääkkeiden ottaminen ja kodin siisteys. He myös seuraavat asiakkaan vointia ja raportoivat muutoksista.</p>\n<h2>Kotihoidon kysyntä Suomessa</h2>\n<p>Kotihoidon palveluille on kasvava tarve, kun yhä useampi ikääntynyt haluaa asua kotona mahdollisimman pitkään. Kokeneet kotihoidon ammattilaiset ovat erittäin arvostettuja.</p>",
		'municipalities' => array( '111','091','564','445','609' ),
	),
	array(
		'isco_code'      => '4120',
		'title'          => 'Sihteerit',
		'cta_singular'   => 'sihteeri',
		'cta_partitive'  => 'sihteereitä',
		'alt_titles'     => array( 'Toimistosihteerit', 'Assistentit' ),
		'excerpt'        => 'Sihteerit hoitavat yritysten toimistotehtäviä, kuten kirjeenvaihtoa, aikataulutusta ja dokumenttien hallintaa. Löydä kokenut sihteeri projektiisi tai hae toimistotehtäviä omakeikassa.',
		'content'        => "<h2>Mitä sihteerit tekevät?</h2>\n<p>Sihteerit tukevat organisaatioiden päivittäistä toimintaa hoitamalla viestintää, arkistointia, kokousjärjestelyjä ja muita hallinnollisia tehtäviä. He ovat usein johtajien tai tiimien tärkeä tuki.</p>\n<h2>Sihteerien kysyntä Suomessa</h2>\n<p>Kokeneet sihteerit ja toimistoassistentit ovat kysyttyjä kaikenkokoisissa yrityksissä ja julkishallinnossa. Osa-aikainen tai projektiluonteinen työ sopii hyvin omakeikka-malliin.</p>",
		'municipalities' => array( '018','049','075','091','142','186','224','245','257','285','286','398','405','444','481','503','536','538','604','611','638','636','680','684','698','704','738','753','783','837','853','858','895','092','918','927','935','980' ),
	),
	array(
		'isco_code'      => '5120',
		'title'          => 'Kokit',
		'cta_singular'   => 'kokki',
		'cta_partitive'  => 'kokkeja',
		'alt_titles'     => array( 'Keittäjät', 'Keittiötyöntekijät' ),
		'excerpt'        => 'Kokit valmistavat ruokaa ravintoloissa, henkilöstöravintoloissa ja yksityistilaisuuksissa. Löydä ammattikokki keikaksi tai tarjoa keittiöosaamistasi työnantajille omakeikassa.',
		'content'        => "<h2>Mitä kokit tekevät?</h2>\n<p>Kokit suunnittelevat ja valmistavat ruoka-annoksia keittiöissä. Osaaminen voi kattaa a la carte -ruoanlaiton, lounaskokkauksen, erityisruokavaliot tai suurtalouskeittiön.</p>\n<h2>Kokkien kysyntä Suomessa</h2>\n<p>Ravintola-alalla on jatkuva tarve ammattitaitoisille kokeille erityisesti sesonkiaikoina ja tapahtumissa. Keikkatyö ja tilapäiset sijaisuudet ovat alalla yleisiä.</p>",
		'municipalities' => array( '049','091','186','202','211','245','286','297','505','687','749','762','853','858','092' ),
	),
	array(
		'isco_code'      => '3313',
		'title'          => 'Kirjanpitäjät',
		'cta_singular'   => 'kirjanpitäjä',
		'cta_partitive'  => 'kirjanpitäjiä',
		'alt_titles'     => array( 'Taloushallinnon assistentit', 'Reskontranhoitajat' ),
		'excerpt'        => 'Kirjanpitäjät hoitavat yritysten taloushallintoa, tilinpäätöksiä ja viranomaisraportointia. Löydä luotettava kirjanpitäjä yrityksellesi tai tarjoa taloushallinto-osaamistasi omakeikassa.',
		'content'        => "<h2>Mitä kirjanpitäjät tekevät?</h2>\n<p>Kirjanpitäjät kirjaavat liiketapahtumat, laativat tilinpäätökset, hoitavat palkanlaskennan ja varmistavat, että yrityksen taloudenpito täyttää lainmukaiset vaatimukset.</p>\n<h2>Kirjanpitäjien kysyntä Suomessa</h2>\n<p>Kirjanpitäjillä on tasainen kysyntä pk-yrityksissä ja tilitoimistoissa. Osa-aikainen tai etänä tehtävä kirjanpitotyö sopii hyvin eläkkeellä oleville taloushallinnon ammattilaisille.</p>",
		'municipalities' => array( '020','049','061','091','102','103','106','108','167','182','211','224','257','276','405','418','426','481','503','536','538','593','604','638','636','680','684','704','738','783','837','853','887','895','908','092','915','918','927','980' ),
	),
	array(
		'isco_code'      => '7111',
		'title'          => 'Talonrakentajat',
		'cta_singular'   => 'talonrakentaja',
		'cta_partitive'  => 'talonrakentajia',
		'alt_titles'     => array( 'Rakennustyöntekijät' ),
		'excerpt'        => 'Talonrakentajat toteuttavat rakennustöitä uudisrakentamisessa ja saneerauskohteissa. Löydä kokenut rakentaja projektiisi tai ilmoita rakennusosaamisesi omakeikassa.',
		'content'        => "<h2>Mitä talonrakentajat tekevät?</h2>\n<p>Talonrakentajat tekevät kaikentyyppisiä rakennustöitä: perustukset, runkorakenteet, eristykset ja viimeistelyn. He voivat toimia sekä uudisrakentamisessa että korjausrakentamisessa.</p>\n<h2>Talonrakentajien kysyntä Suomessa</h2>\n<p>Rakennusalan ammattilaisia tarvitaan jatkuvasti. Kokeneet rakentajat ovat erittäin arvostettuja remonteissa ja pientalorakentamisessa koko Suomessa.</p>",
		'municipalities' => array( '020','049','082','091','106','109','186','205','211','245','418','430','433','505','578','604','611','616','702','734','753','765','837','858','092' ),
	),
);

// --- Create municipality terms ---
// Terms are looked up by municipality_id term meta to handle existing terms
// regardless of their slug. New terms use a slug derived from the name.

WP_CLI::line( '' );
WP_CLI::line( '== Municipalities ==' );

$term_ids = array(); // keyed by municipality id string

foreach ( $municipalities as $muni_id => $name ) {
	// Look up by municipality_id meta first.
	$found = get_terms( array(
		'taxonomy'   => 'municipality',
		'meta_key'   => 'municipality_id',
		'meta_value' => $muni_id,
		'hide_empty' => false,
		'number'     => 1,
	) );

	if ( ! empty( $found ) && ! is_wp_error( $found ) ) {
		$term_ids[ $muni_id ] = $found[0]->term_id;
		WP_CLI::line( sprintf( '  skip  %s (%s, term_id=%d)', $name, $muni_id, $found[0]->term_id ) );
		continue;
	}

	$slug   = sanitize_title( $name );
	$result = wp_insert_term( $name, 'municipality', array( 'slug' => $slug ) );

	if ( is_wp_error( $result ) ) {
		WP_CLI::warning( sprintf( '  error %s (%s): %s', $name, $muni_id, $result->get_error_message() ) );
		continue;
	}

	update_term_meta( $result['term_id'], 'municipality_id', $muni_id );
	$term_ids[ $muni_id ] = $result['term_id'];

	WP_CLI::success( sprintf( '  create %s (%s, term_id=%d)', $name, $muni_id, $result['term_id'] ) );
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
	update_post_meta( $post_id, 'alt_titles', wp_json_encode( $occ['alt_titles'], JSON_UNESCAPED_UNICODE ) );

	$assigned_term_ids = array();
	foreach ( $occ['municipalities'] as $muni_id ) {
		if ( isset( $term_ids[ $muni_id ] ) ) {
			$assigned_term_ids[] = $term_ids[ $muni_id ];
		}
	}

	if ( ! empty( $assigned_term_ids ) ) {
		wp_set_post_terms( $post_id, $assigned_term_ids, 'municipality' );
	}

	WP_CLI::success( sprintf(
		'  create %s (ISCO %s, post_id=%d, %d municipalities)',
		$occ['title'],
		$occ['isco_code'],
		$post_id,
		count( $assigned_term_ids )
	) );
}

// Clear all occupation-related transients.
delete_transient( 'occupation_links' );
global $wpdb;
$wpdb->query( "DELETE FROM {$wpdb->options} WHERE option_name LIKE '_transient_occupation_%'" );
$wpdb->query( "DELETE FROM {$wpdb->options} WHERE option_name LIKE '_transient_timeout_occupation_%'" );

WP_CLI::line( '' );
WP_CLI::success( 'Done. Run `wp --allow-root cache flush` if object caching is active.' );
