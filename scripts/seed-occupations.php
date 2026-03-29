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
// Top 15 by registered employee count (descending) — higher count = more active users.
// Titles are plural nominative (Finnish standard for occupation CPTs).
// cta_singular: singular nominative, used in "Aloita X-haku" and "Tarvitsetko X?" CTAs.
// cta_partitive_singular: partitive singular, used in "Tarvitsetko X?" hero H1.
// cta_partitive: plural partitive, used in "Etsitkö X?" CTAs.
// alt_titles: alternative plural names for the same ISCO group.
// municipalities: IDs from GET /api/occupations/{isco_code}/municipalities (production).
//   Empty array = not yet populated; display still works via runtime API call.
$occupations = array(
	array(
		'isco_code'              => '5223',
		'title'                  => 'Myyjät',
		'menu_order'             => 1,
		'cta_singular'           => 'myyjä',
		'cta_partitive_singular' => 'myyjää',
		'cta_partitive'          => 'myyjiä',
		'alt_titles'             => array( 'Erikoismyyjät', 'Vaatemyyjät', 'Myymäläavustajat' ),
		'excerpt'                => 'Myyjät palvelevat asiakkaita liikkeissä, verkkokaupoissa ja erilaisissa myyntiympäristöissä. Löydä kokenut myyjä sesonkiin tai hae myyntitehtäviä omakeikassa.',
		'content'                => "<h2>Myyjiä omakeikassa</h2>\n<p>Omakeikassa on myyjiä erittäin laajalta kirjolta: autokaupasta, kodinkoneista, optikkoliikkeistä, lääkemyynnistä, tekstiileistä, urheiluvälineistä, rakennustarvikkeista ja useista muista erikoisaloista. Monilla on vuosikymmenten kokemus avainasiakastyöstä, alueellisesta myynnistä tai vähittäiskaupan johtotehtävistä. Osalla tausta on tukku- tai projektikaupassa.</p>\n<h2>Mitä myyjät tekevät?</h2>\n<p>Myyjät palvelevat asiakkaita, esittelevät tuotteita, hoitavat kassan ja pitävät myymälän siistinä. Erikoismyyjät ovat syventäneet osaamistaan tiettyyn tuoteryhmään, kuten elektroniikkaan, vaatteisiin tai lääkkeisiin.</p>",
		'municipalities'         => array(),
	),
	array(
		'isco_code'              => '1221',
		'title'                  => 'Markkinointipäälliköt',
		'menu_order'             => 2,
		'cta_singular'           => 'markkinointipäällikkö',
		'cta_partitive_singular' => 'markkinointipäällikköä',
		'cta_partitive'          => 'markkinointipäälliköitä',
		'alt_titles'             => array( 'Myyntijohtajat', 'Myyntipäälliköt', 'Markkinointijohtajat' ),
		'excerpt'                => 'Markkinointipäälliköt kehittävät myynti- ja markkinointistrategioita sekä johtavat asiakkuuksia. Löydä kokenut markkinointiosaaja tai hae johtotehtäviä omakeikassa.',
		'content'                => "<h2>Markkinointipäälliköitä omakeikassa</h2>\n<p>Omakeikassa on markkinointi- ja myyntijohtajia rakennus-, huonekalu-, matkailun ja kaupan aloilta. Osalla on kansainvälinen tausta ja kokemus useista eri maista. Kokemus kattaa tuoteryhmäjohtamisen, digitaalisen markkinoinnin, jälkimarkkinoinnin sekä B2B-myynnin johtamisen. Moni soveltuu konsultiksi tai osa-aikaiseksi markkinointipäälliköksi.</p>\n<h2>Mitä markkinointipäälliköt tekevät?</h2>\n<p>Markkinointipäälliköt vastaavat yrityksen markkinointiviestinnästä, brändinrakennuksesta ja myynnin kasvattamisesta. Kokeneet päälliköt osaavat sekä digitaalisen että perinteisen markkinoinnin.</p>",
		'municipalities'         => array(),
	),
	array(
		'isco_code'              => '4110',
		'title'                  => 'Toimistoavustajat',
		'menu_order'             => 3,
		'cta_singular'           => 'toimistoavustaja',
		'cta_partitive_singular' => 'toimistoavustajaa',
		'cta_partitive'          => 'toimistoavustajia',
		'alt_titles'             => array( 'Toimistovirkailijat', 'Jäsenrekisterinhoitajat' ),
		'excerpt'                => 'Toimistoavustajat hoitavat yritysten ja organisaatioiden arkisia toimistotehtäviä. Löydä luotettava toimistoavustaja tai hae hallinnollisia tehtäviä omakeikassa.',
		'content'                => "<h2>Toimistoavustajia omakeikassa</h2>\n<p>Omakeikassa toimistoavustajilla on taustaa oppilaitosten opintosihteerinä, kirjanpidon asiakaspalvelussa, graafisen alan tuotannossa sekä koulutuskoordinoinnissa. Monella on useiden vuosikymmenten kokemus toimistotyöstä eri aloilta. He sopivat hyvin sijaisuuksiin, projektiavustukseen tai osa-aikaiseen toimistotyöhön.</p>\n<h2>Mitä toimistoavustajat tekevät?</h2>\n<p>Toimistoavustajat vastaavat päivittäisistä toimistotehtävistä: puhelujen ja sähköpostien käsittely, asiakirjojen laadinta, aikataulutus ja arkistointi. He ovat organisaation käytännön selkäranka.</p>",
		'municipalities'         => array(),
	),
	array(
		'isco_code'              => '8322',
		'title'                  => 'Taksinkuljettajat',
		'menu_order'             => 4,
		'cta_singular'           => 'taksinkuljettaja',
		'cta_partitive_singular' => 'taksinkuljettajaa',
		'cta_partitive'          => 'taksinkuljettajia',
		'alt_titles'             => array( 'Autonkuljettajat', 'Yksityisautonkuljettajat', 'Henkilökuljettajat' ),
		'excerpt'                => 'Taksinkuljettajat kuljettavat asiakkaita turvallisesti ja täsmällisesti. Löydä kokenut kuljettaja tai hae kuljetustehtäviä omakeikassa.',
		'content'                => "<h2>Taksinkuljettajia omakeikassa</h2>\n<p>Omakeikassa kuljettajien taustat ovat hyvin monipuolisia: joukossa on pitkän linjan ammattikuljettajia, merikapteeneja, tuotantojohtajia ja poliisitaustaisia ammattilaisia. Monilla on ajantasaiset ajoluvat ja useita vuosikymmeniä kokemusta erilaisista kuljetustehtävistä. Heistä löytyy taitoa myös erityisryhmien palvelemiseen.</p>\n<h2>Mitä taksinkuljettajat tekevät?</h2>\n<p>Taksinkuljettajat kuljettavat matkustajia tilauksesta ja auttavat erityisesti ikääntyneitä ja liikuntarajoitteisia asiakkaita. Työ voi sisältää myös yksityiskuljetuksia, sairaankuljetusta tai koululaiskuljetuksia.</p>",
		'municipalities'         => array(),
	),
	array(
		'isco_code'              => '1213',
		'title'                  => 'Liiketoimintajohtajat',
		'menu_order'             => 5,
		'cta_singular'           => 'liiketoimintajohtaja',
		'cta_partitive_singular' => 'liiketoimintajohtajaa',
		'cta_partitive'          => 'liiketoimintajohtajia',
		'alt_titles'             => array( 'Aluejohtajat', 'Ohjelmajohtajat', 'Vastuullisuusjohtajat' ),
		'excerpt'                => 'Liiketoimintajohtajat johtavat organisaatioiden strategiaa ja operatiivista toimintaa. Löydä kokenut johtaja konsultiksi tai hae johtotehtäviä omakeikassa.',
		'content'                => "<h2>Liiketoimintajohtajia omakeikassa</h2>\n<p>Omakeikassa johtajien kokemus kattaa laajan kirjon: EU-hankekoordinointia, hankintajohtamista, muutosjohtamista, matkailupolitiikkaa, strategista suunnittelua ja ympäristö- sekä vastuullisuusjohtamista. Monella on taustaa myös hallitustyöskentelyssä ja yrittäjyydessä. He sopivat interim-johtajan, neuvonantajan tai projektijohtajan rooleihin.</p>\n<h2>Mitä liiketoimintajohtajat tekevät?</h2>\n<p>Liiketoimintajohtajat vastaavat yrityksen tai sen osa-alueen strategisesta suunnasta, resursseista ja tavoitteiden saavuttamisesta. Heidän kokemuksensa on arvokas konsultointi- ja väliaikaisjohtotehtävissä.</p>",
		'municipalities'         => array(),
	),
	array(
		'isco_code'              => '5321',
		'title'                  => 'Lähihoitajat',
		'menu_order'             => 6,
		'cta_singular'           => 'lähihoitaja',
		'cta_partitive_singular' => 'lähihoitajaa',
		'cta_partitive'          => 'lähihoitajia',
		'alt_titles'             => array( 'Hoiva-avustajat' ),
		'excerpt'                => 'Lähihoitajat tarjoavat ammatillista hoivaa ja tukea ihmisille kotona ja hoivakodeissa. Omakeikasta löydät kokeneen lähihoitajan tai tarjoat osaamistasi työnantajille.',
		'content'                => "<h2>Lähihoitajia omakeikassa</h2>\n<p>Omakeikassa lähihoitajilla on pitkä kokemus muistisairaiden hoidosta, lasten päivähoidosta ja vuodeosastotyöstä. Osalla on myös taustaa teknisiltä aloilta ennen siirtymistä hoivaan, mikä tuo laaja-alaista ongelmanratkaisukykyä. Moni on valmis sekä keikka- että osa-aikatyöhön.</p>\n<h2>Mitä lähihoitajat tekevät?</h2>\n<p>Lähihoitajat avustavat ikääntyneitä ja toimintarajoitteisia ihmisiä päivittäisissä toiminnoissa kuten peseytymisessä, ruokailussa ja liikkumisessa. He työskentelevät kotihoidossa, palvelutaloissa ja sairaaloissa.</p>",
		'municipalities'         => array( '049','086','111','091','098','106','109','165','179','186','205','245','297','398','491','494','505','564','611','680','694','704','749','753','837','853','858','859','895','092','927' ),
	),
	array(
		'isco_code'              => '2512',
		'title'                  => 'Ohjelmistokehittäjät',
		'menu_order'             => 7,
		'cta_singular'           => 'ohjelmistokehittäjä',
		'cta_partitive_singular' => 'ohjelmistokehittäjää',
		'cta_partitive'          => 'ohjelmistokehittäjiä',
		'alt_titles'             => array( 'Käyttöliittymäkehittäjät', 'Pilviarkkitehdit', 'IoT-kehittäjät' ),
		'excerpt'                => 'Ohjelmistokehittäjät suunnittelevat ja rakentavat sovelluksia pilvessä, mobiilissa ja verkossa. Löydä kokenut kehittäjä projektiisi tai hae uusia toimeksiantoja omakeikassa.',
		'content'                => "<h2>Ohjelmistokehittäjiä omakeikassa</h2>\n<p>Omakeikassa kehittäjien kokemus alkaa monella jo 1980-luvulta. Osaaminen kattaa C, C++, C#, Java, Python, JavaScript, SQL, Dart ja pilvipalvelut. Joukossa on ohjelmistoarkkitehteja, UX-suunnittelijoita, IoT-kehittäjiä ja DevOps-osaajia. Moni on tottunut itsenäiseen työskentelyyn ja soveltuu hyvin projektimuotoiseen yhteistyöhön.</p>\n<h2>Mitä ohjelmistokehittäjät tekevät?</h2>\n<p>Ohjelmistokehittäjät vastaavat sovellusten, järjestelmien ja rajapintojen suunnittelusta ja toteutuksesta. Osaaminen kattaa web-kehityksen, mobiilisovellukset, pilvipalvelut ja IoT-ratkaisut.</p>",
		'municipalities'         => array( '049','091','106','109','186','211','235','245','257','418','604','694','702','837','092','927' ),
	),
	array(
		'isco_code'              => '9112',
		'title'                  => 'Siivoojat',
		'menu_order'             => 8,
		'cta_singular'           => 'siivooja',
		'cta_partitive_singular' => 'siivoojaa',
		'cta_partitive'          => 'siivoojia',
		'alt_titles'             => array( 'Laitossiivoojat', 'Siivoustyöntekijät' ),
		'excerpt'                => 'Siivoojat pitävät kodit, toimistot ja julkiset tilat siistinä ja viihtyisinä. Löydä luotettava siivooja tai tarjoa siivouspalvelujasi omakeikassa.',
		'content'                => "<h2>Siivoojia omakeikassa</h2>\n<p>Omakeikassa siivoojilla on taustaa hotellisiivouksesta, lentokenttä- ja lentokonesiivouksesta, laitossiivouksesta sekä toimistosiivouksesta. Joukossa on myös entisiä siivousyritysten omistajia, joilla on erityistä ammattitaitoa laadukkaaseen siivoustyöhön. Osa heistä on myös lähihoitajan koulutuksen saaneita.</p>\n<h2>Mitä siivoojat tekevät?</h2>\n<p>Siivoojat huolehtivat tilojen puhtaanapidosta kodin, toimiston, sairaalan tai hotellin tarpeisiin. Työ sisältää pintapuhdistuksen lisäksi perussiivousta, ikkunanpesua ja lattiahoitoa.</p>",
		'municipalities'         => array( '016','049','091','445','853','092' ),
	),
	array(
		'isco_code'              => '2320',
		'title'                  => 'Ammatilliset opettajat',
		'menu_order'             => 9,
		'cta_singular'           => 'ammatillinen opettaja',
		'cta_partitive_singular' => 'ammatillista opettajaa',
		'cta_partitive'          => 'ammatillisia opettajia',
		'alt_titles'             => array( 'Kouluttajat', 'Opettajat' ),
		'excerpt'                => 'Ammatilliset opettajat kouluttavat ja ohjaavat oppilaita käytännön ammattitaidoissa. Löydä kokenut alan opettaja tai hae opetustehtäviä omakeikassa.',
		'content'                => "<h2>Ammatillisia opettajia omakeikassa</h2>\n<p>Omakeikassa ammatillisilla opettajilla on opetuskokemusta muun muassa autoalalta, elektroniikasta, hiusalalta, hotelli- ja ravintola-alalta, liikuntatoiminnasta ja sähköalalta. Osalla on myös tutkimus- ja kehitystaustaa tekoälyn ja uuden teknologian hyödyntämisestä. He sopivat opetus- ja koulutustehtävien lisäksi mentoriksi tai sisällön kehittäjäksi.</p>\n<h2>Mitä ammatilliset opettajat tekevät?</h2>\n<p>Ammatilliset opettajat opettavat käytännön taitoja ja ammatillista teoriaa oppilaitoksissa. He voivat myös kouluttaa yrityksissä ja järjestöissä tai toimia mentorina nuoremmille ammattilaisille.</p>",
		'municipalities'         => array(),
	),
	array(
		'isco_code'              => '9313',
		'title'                  => 'Rakennustyöntekijät',
		'menu_order'             => 10,
		'cta_singular'           => 'rakennustyöntekijä',
		'cta_partitive_singular' => 'rakennustyöntekijää',
		'cta_partitive'          => 'rakennustyöntekijöitä',
		'alt_titles'             => array( 'Rakennusalan avustavat työntekijät', 'Rakennusmiehiset' ),
		'excerpt'                => 'Rakennustyöntekijät toteuttavat rakennustöitä uudisrakentamisessa ja saneerauskohteissa. Löydä kokenut rakentaja projektiisi tai ilmoita rakennusosaamisesi omakeikassa.',
		'content'                => "<h2>Rakennustyöntekijöitä omakeikassa</h2>\n<p>Omakeikassa rakennustyöntekijöillä on kokemusta rakentamisesta Suomessa ja ulkomailla vuosikymmenien ajalta. Joukossa on kiinteistöhuoltoa, lumitöitä, hautausmaatyötä ja voimalaitostöitä tehneitä moniosaajia. Osalla on myös teollisuustausta, mikä tuo kokemusta koneista ja laitteiden käytöstä.</p>\n<h2>Mitä rakennustyöntekijät tekevät?</h2>\n<p>Rakennustyöntekijät tekevät erilaisia rakennustöitä: maanrakennusta, perustuksia, runkotöitä ja viimeistelyä. He voivat toimia sekä uudisrakentamisessa että korjausrakentamisessa.</p>",
		'municipalities'         => array(),
	),
	array(
		'isco_code'              => '1212',
		'title'                  => 'Henkilöstöjohtajat',
		'menu_order'             => 11,
		'cta_singular'           => 'henkilöstöjohtaja',
		'cta_partitive_singular' => 'henkilöstöjohtajaa',
		'cta_partitive'          => 'henkilöstöjohtajia',
		'alt_titles'             => array( 'HR-johtajat', 'Rekrytointipäälliköt', 'HR-päälliköt' ),
		'excerpt'                => 'Henkilöstöjohtajat kehittävät organisaatioiden henkilöstöprosesseja ja rekrytointia. Löydä kokenut HR-osaaja tai hae henkilöstöhallinnon tehtäviä omakeikassa.',
		'content'                => "<h2>Henkilöstöjohtajia omakeikassa</h2>\n<p>Omakeikassa henkilöstöjohtajilla on taustaa IT-alalta, joukkoliikenteen suunnittelusta, yritysostoista ja -fuusioista sekä kansainväliseltä uralta. Monella on kokemus sekä yksityisen että julkisen sektorin henkilöstöhallinnosta. He soveltuvat HR-konsultiksi, rekrytointiasiantuntijaksi tai organisaation kehittämisen tukihenkilöksi.</p>\n<h2>Mitä henkilöstöjohtajat tekevät?</h2>\n<p>Henkilöstöjohtajat vastaavat rekrytoinnista, työsuhteista, palkitsemisesta ja henkilöstön kehittämisestä. Heidän osaamisensa on arvokasta erityisesti kasvavissa organisaatioissa ja muutostilanteissa.</p>",
		'municipalities'         => array(),
	),
	array(
		'isco_code'              => '2421',
		'title'                  => 'Liiketoimintakonsultit',
		'menu_order'             => 12,
		'cta_singular'           => 'liiketoimintakonsultti',
		'cta_partitive_singular' => 'liiketoimintakonsulttia',
		'cta_partitive'          => 'liiketoimintakonsultteja',
		'alt_titles'             => array( 'Analyytikot', 'Prosessikehittäjät', 'Liiketoiminta-analyytikot' ),
		'excerpt'                => 'Liiketoimintakonsultit auttavat yrityksiä kehittämään prosessejaan ja kasvamaan kannattavasti. Löydä kokenut konsultti projektiisi tai hae konsultointitehtäviä omakeikassa.',
		'content'                => "<h2>Liiketoimintakonsultteja omakeikassa</h2>\n<p>Omakeikassa konsulteilla on taustaa Lean- ja Kaizen-johtamisesta, toimitusketjujen suunnittelusta, liiketoiminta-analytiikasta sekä viestintä- ja elintarviketeollisuuden johtotehtävistä. Osalla on kokemus toimialan johtamisesta alimmalta tasolta toimitusjohtajaksi asti, mikä tuo ainutlaatuisen kokonaiskuvan liiketoiminnasta.</p>\n<h2>Mitä liiketoimintakonsultit tekevät?</h2>\n<p>Liiketoimintakonsultit analysoivat yrityksen toimintaa, tunnistavat kehityskohteet ja auttavat toteuttamaan parannuksia. He voivat erikoistua esimerkiksi prosesseihin, logistiikkaan tai digitalisaatioon.</p>",
		'municipalities'         => array(),
	),
	array(
		'isco_code'              => '5153',
		'title'                  => 'Kiinteistönhoitajat',
		'menu_order'             => 13,
		'cta_singular'           => 'kiinteistönhoitaja',
		'cta_partitive_singular' => 'kiinteistönhoitajaa',
		'cta_partitive'          => 'kiinteistönhoitajia',
		'alt_titles'             => array( 'Talovahdit' ),
		'excerpt'                => 'Kiinteistönhoitajat vastaavat rakennusten teknisestä kunnossapidosta, siisteyden ylläpidosta ja pienimuotoisista huoltotöistä. Löydä ammattitaitoinen kiinteistönhoitaja taloyhtiöllesi tai kerrostalollesi.',
		'content'                => "<h2>Kiinteistönhoitajia omakeikassa</h2>\n<p>Omakeikassa kiinteistönhoitajilla on monipuolinen kokemus: joukossa on teollisuudessa pitkän uran tehneitä, kaupan alan ammattilaisia sekä julkisen sektorin kiinteistöhuollosta kokemusta saaneita tekijöitä. Monella on kiinteistönhoitajakoulutus ja käytännön kokemus useiden erikokoisten kiinteistöjen ylläpidosta.</p>\n<h2>Mitä kiinteistönhoitajat tekevät?</h2>\n<p>Kiinteistönhoitajat hoitavat rakennusten ja niiden ympäristön teknistä ylläpitoa: laitteiden tarkistuksia, pieniä korjauksia, lämmitysjärjestelmien säätöjä ja puhtaanapitoa.</p>",
		'municipalities'         => array( '020','010','049','061','069','091','103','106','108','143','169','182','186','211','232','245','249','297','301','398','418','430','433','505','535','562','581','592','604','609','611','616','626','691','698','734','790','743','749','753','761','834','837','858','908','092','922','980','981' ),
	),
	array(
		'isco_code'              => '1211',
		'title'                  => 'Talousjohtajat',
		'menu_order'             => 14,
		'cta_singular'           => 'talousjohtaja',
		'cta_partitive_singular' => 'talousjohtajaa',
		'cta_partitive'          => 'talousjohtajia',
		'alt_titles'             => array( 'Rahoitusjohtajat', 'Talouspäälliköt' ),
		'excerpt'                => 'Talousjohtajat vastaavat yrityksen taloudesta, rahoituksesta ja taloushallinnosta. Löydä kokenut talousosaaja konsultiksi tai hae taloushallinnon johtotehtäviä omakeikassa.',
		'content'                => "<h2>Talousjohtajia omakeikassa</h2>\n<p>Omakeikassa talousjohtajilla on taustaa kuntasektorin taloushallinnosta, julkisen sektorin kassavirtojen johdosta, yhteisöjen taloushallinnosta sekä yritysten rahoitusjohtamisesta. Monella on kokemus hallitustyöskentelystä useissa organisaatioissa. He sopivat interim-CFO:n, talousneuvonantajan tai osa-aikaisen controller-tehtävän rooleihin.</p>\n<h2>Mitä talousjohtajat tekevät?</h2>\n<p>Talousjohtajat johtavat yrityksen taloushallintoa, vastaavat budjetoinnista, raportoinnista ja rahoitusstrategiasta. Heidän osaamisensa on arvokasta erityisesti kasvuyrityksissä ja muutostilanteissa.</p>",
		'municipalities'         => array(),
	),
	array(
		'isco_code'              => '3343',
		'title'                  => 'Sihteerit ja assistentit',
		'menu_order'             => 15,
		'cta_singular'           => 'sihteeri',
		'cta_partitive_singular' => 'sihteeriä',
		'cta_partitive'          => 'sihteereitä',
		'alt_titles'             => array( 'Johdon assistentit', 'Hallintoassistentit', 'Projektiassistentit' ),
		'excerpt'                => 'Sihteerit ja assistentit tukevat johtoa ja tiimejä organisaatioiden hallinnollisissa tehtävissä. Löydä kokenut assistentti tai hae hallintotehtäviä omakeikassa.',
		'content'                => "<h2>Sihteereitä ja assistentteja omakeikassa</h2>\n<p>Omakeikassa sihteereiden ja assistenttien kokemus kattaa rahoitusalan pöytäkirjanpidon ja back-officen, kansainvälisten järjestöjen sihteeristötyön sekä EU-instituutioiden hallintotehtävät. Monella on kokemus sekä johdon tukemisesta että itsenäisestä projektinhallinnasta kansainvälisessä ympäristössä.</p>\n<h2>Mitä sihteerit ja assistentit tekevät?</h2>\n<p>Sihteerit ja assistentit hoitavat kokousjärjestelyjä, kirjeenvaihtoa, aikataulutusta ja muita hallinnollisia tehtäviä. Johdon assistentit toimivat usein johtoryhmän tai toimitusjohtajan tukena.</p>",
		'municipalities'         => array(),
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
		'menu_order'   => $occ['menu_order'],
	), true );

	if ( is_wp_error( $post_id ) ) {
		WP_CLI::warning( sprintf( '  error %s: %s', $occ['title'], $post_id->get_error_message() ) );
		continue;
	}

	update_post_meta( $post_id, 'isco_code', $occ['isco_code'] );
	update_post_meta( $post_id, 'cta_singular', $occ['cta_singular'] );
	update_post_meta( $post_id, 'cta_partitive_singular', $occ['cta_partitive_singular'] );
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
