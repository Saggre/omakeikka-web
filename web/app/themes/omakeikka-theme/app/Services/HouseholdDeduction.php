<?php

namespace App\Services;

use App\Services\DeductionEligibility as E;

/**
 * Whether a palvelutori service can honestly carry a kotitalousvähennys
 * mention.
 *
 * Carries no percentages, caps or euro amounts by design: they change with the
 * tax year and depend on how the helper invoices. The site only says the
 * deduction may be available and links to vero.fi.
 *
 * Sources checked 2026-09-16, all vero.fi: Kotitalousvähennys verotuksessa
 * (VH/555/00.01.00/2026), Kotitalousvähennys tavanomaisesta kotitalous-, hoiva-
 * ja hoitotyöstä, Taulukko kotitalousvähennykseen oikeuttavista töistä.
 */
final class HouseholdDeduction
{
    /** Review date of the mapping below, shown to the visitor. */
    public const UPDATED = '2026-09-16';

    public const INFO_URL = 'https://www.vero.fi/henkiloasiakkaat/verokortti-ja-veroilmoitus/tulot-ja-vahennykset/kotitalousvahennys/';

    private const GROUPS = array(
        'piha'       => E::Yes,
        'kodinhoito' => E::Yes,
        'remontti'   => E::Yes,
        'ruoka'      => E::Caveat,
        'hoiva'      => E::Caveat,
        'asennukset' => E::Caveat,
        'tekniikka'  => E::Caveat,
    );

    /**
     * Categories whose eligibility differs from their group.
     */
    private const CATEGORIES = array(
        // Pet care is not tavanomainen hoivatyö, and work outside the dwelling
        // fails the location test.
        'lemmikkien-hoito'    => E::No,
        'koiran-ulkoilutus'   => E::No,
        'lastenkuljetus'      => E::No,
        'saattajapalvelu'     => E::No,
        'lastenhoitoapu'      => E::Yes,

        // Cooking in the home qualifies, delivery does not, and catering is
        // often done at a venue.
        'ruoanlaitto'         => E::Yes,
        'leivonta'            => E::Yes,
        'aterioiden-kuljetus' => E::No,
        'juhlien-valmistelu'  => E::Caveat,

        // The statutory category is tieto- ja viestintätekniikka.
        'tietokone-apu'       => E::Yes,
        'nettiyhteys'         => E::Yes,
        'tv-ja-aani'          => E::Yes,
        'valvontakamerat'     => E::No,

        // Fixtures of the dwelling qualify, ordinary white goods do not.
        'valaisimien-asennus' => E::Yes,
        'hyllyt-ja-taulut'    => E::Yes,
        'verhotangot'         => E::Yes,
        'liesituuletin'       => E::Yes,
        'ilmalampopumppu'     => E::Yes,
        'suodattimet'         => E::Yes,
        'suihku-ja-allas'     => E::Yes,
        'kodinkoneet'         => E::No,
    );

    /**
     * Unknown groups and categories fail closed, so a group added to the
     * seeder later never silently inherits a tax claim.
     */
    public function eligibility( string $group_slug, string $category_slug = '' ): E {
        return self::CATEGORIES[ $category_slug ] ?? self::GROUPS[ $group_slug ] ?? E::No;
    }

    public function updatedLabel(): string {
        return date_i18n( 'j.n.Y', strtotime( self::UPDATED ) );
    }
}
