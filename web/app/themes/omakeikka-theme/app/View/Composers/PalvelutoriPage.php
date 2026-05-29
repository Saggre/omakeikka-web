<?php

namespace App\View\Composers;

use App\Services\MarketplaceCategoryQuery;
use Roots\Acorn\View\Composer;

class PalvelutoriPage extends Composer
{
    /**
     * List of views served by this composer.
     *
     * @var array
     */
    protected static $views = [
        'template-palvelutori',
    ];

    /**
     * Data to be passed to view before rendering.
     *
     * @return array
     */
    public function with(): array
    {
        return array(
            'palvelutori_groups' => MarketplaceCategoryQuery::groups(),
            'palvelutori_faq'    => $this->faq(),
        );
    }

    /**
     * Static FAQ. Edit here when the wording needs updating.
     *
     * @return array
     */
    private function faq(): array {
        return array(
            array(
                'q' => __( 'Paljonko palvelun käyttö maksaa?', 'omakeikka-theme' ),
                'a' => __( 'Omakeikan palvelutorin käyttö on maksutonta. Tehtävän julkaiseminen, vastausten saaminen ja tekijöiden vertailu eivät maksa mitään. Sovit hinnan ja maksat palkkion suoraan tekijälle.', 'omakeikka-theme' ),
            ),
            array(
                'q' => __( 'Miten tekijälle maksetaan?', 'omakeikka-theme' ),
                'a' => __( 'Maksu kulkee suoraan teidän välillänne, ei Omakeikan kautta. Tekijä toimii kevytyrittäjänä omaan lukuunsa ja vastaa itse laskutuksesta, veroista ja vakuutuksista. Yleensä tekijä käyttää kevytyrittäjäpalvelua (esim. UKKO, OP Kevytyrittäjä, Eezy), joka huolehtii laskutuksesta ja ennakonpidätyksestä.', 'omakeikka-theme' ),
            ),
            array(
                'q' => __( 'Kuka avustaa tehtävissä?', 'omakeikka-theme' ),
                'a' => __( 'Tehtäviä tekevät Omakeikkaan rekisteröityneet eläkeläiset, joilla on monipuolista työ- ja elämänkokemusta. Profiilit ovat varmennettuja ja voit lukea muiden arviot ennen valintaa.', 'omakeikka-theme' ),
            ),
            array(
                'q' => __( 'Voinko valita kuka tekee työn?', 'omakeikka-theme' ),
                'a' => __( 'Kyllä. Saat useamman vastauksen tehtävääsi ja voit vertailla profiileja, hintoja ja arvioita ennen kuin valitset sopivimman tekijän.', 'omakeikka-theme' ),
            ),
            array(
                'q' => __( 'Onko palvelu turvallinen?', 'omakeikka-theme' ),
                'a' => __( 'Profiilit on varmennettu ja keskustelu käydään palvelun kautta. Hinta ja työn sisältö sovitaan etukäteen, eikä piilokuluja tule. Tekijän kevytyrittäjyys tuo läpinäkyvyyttä: saat kuitin tai laskun työstä.', 'omakeikka-theme' ),
            ),
            array(
                'q' => __( 'Missä päin Suomea palvelu toimii?', 'omakeikka-theme' ),
                'a' => __( 'Omakeikkaan on rekisteröitynyt eläkeläisiä lähes 300 kunnasta ympäri Suomen. Tekijöiden saatavuus vaihtelee alueittain ja tehtävän mukaan.', 'omakeikka-theme' ),
            ),
        );
    }

}
