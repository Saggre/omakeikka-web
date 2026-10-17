<?php

namespace App\Services;

/**
 * Whether a palvelutori service can honestly carry a kotitalousvähennys
 * mention on the site.
 *
 * Deliberately carries NO percentages, caps or euro amounts. Those change with
 * the tax year, the 2026 increase was still an unratified proposal when this
 * was written, and the exact rate depends on how the individual helper
 * invoices. The site states that the deduction may be available and links to
 * vero.fi, which is accurate regardless of the year.
 *
 * Eligibility itself still has to be gated, because saying the deduction may
 * apply to work that is excluded by name is also a false claim.
 *
 * Statuses:
 *   yes         show the notice
 *   caveat      show the notice with the group specific restriction
 *   conditional show the notice only on the single category page, never on a card
 *   no          render nothing at all, a negative claim is also a claim
 *
 * Sources checked 2026-09-16, all vero.fi:
 *   Kotitalousvähennys verotuksessa, VH/555/00.01.00/2026
 *   Kotitalousvähennys tavanomaisesta kotitalous-, hoiva- ja hoitotyöstä
 *   Taulukko kotitalousvähennykseen oikeuttavista töistä
 */
class HouseholdDeduction
{
    /**
     * Review date for the eligibility mapping below. Surfaced to the visitor
     * and used by the stale-data admin notice.
     */
    public const UPDATED = '2026-09-16';

    public const INFO_URL = 'https://www.vero.fi/henkiloasiakkaat/verokortti-ja-veroilmoitus/tulot-ja-vahennykset/kotitalousvahennys/';

    /**
     * Group slug to status. Unknown groups fail closed.
     *
     * @var array<string, string>
     */
    private const GROUPS = array(
        'piha'       => 'yes',
        'kodinhoito' => 'yes',
        'remontti'   => 'yes',
        'ruoka'      => 'yes',
        'hoiva'      => 'caveat',
        'asennukset' => 'conditional',
        'tekniikka'  => 'conditional',
        'kauppa'     => 'no',
        'muutto'     => 'no',
        'auto'       => 'no',
        'etaapu'     => 'no',
        'muu'        => 'no',
    );

    /**
     * Categories whose status differs from their group.
     *
     * Group level status is not sound on its own: a deductible group can hold
     * a service that is transport rather than household work, and a
     * conditional group can hold services that clearly do qualify.
     *
     * @var array<string, string>
     */
    private const CATEGORIES = array(
        // Hoiva, pet care is not tavanomainen hoivatyö, and work performed
        // outside the dwelling fails the location test.
        'lemmikkien-hoito'    => 'no',
        'koiran-ulkoilutus'   => 'no',
        'lastenkuljetus'      => 'no',
        'saattajapalvelu'     => 'no',
        'lastenhoitoapu'      => 'yes',

        // Ruoka, cooking in the home qualifies, delivery does not, and party
        // catering is often performed at a venue rather than the home.
        'aterioiden-kuljetus' => 'no',
        'juhlien-valmistelu'  => 'caveat',
        'ruoanlaitto'         => 'yes',
        'leivonta'            => 'yes',

        // Tekniikka, the statutory category is tieto- ja viestintätekniikka.
        'tietokone-apu'       => 'yes',
        'nettiyhteys'         => 'yes',
        'tv-ja-aani'          => 'yes',
        'valvontakamerat'     => 'no',

        // Asennukset, fixtures of the dwelling qualify, ordinary white goods
        // are not covered by the ICT exception.
        'valaisimien-asennus' => 'yes',
        'hyllyt-ja-taulut'    => 'yes',
        'verhotangot'         => 'yes',
        'liesituuletin'       => 'yes',
        'ilmalampopumppu'     => 'yes',
        'suodattimet'         => 'yes',
        'suihku-ja-allas'     => 'yes',
        'kodinkoneet'         => 'no',
    );

    /**
     * Status for a group slug. Fails closed so a group added to the seeder
     * later never silently inherits a tax claim.
     */
    public static function forGroup( string $group_slug ): string {
        return self::GROUPS[ $group_slug ] ?? 'no';
    }

    /**
     * Status for a single category, falling back to its group.
     */
    public static function forCategory( string $category_slug, string $group_slug = '' ): string {
        return self::CATEGORIES[ $category_slug ] ?? self::forGroup( $group_slug );
    }

    /**
     * True when the notice may be rendered at all.
     */
    public static function shows( string $status ): bool {
        return in_array( $status, array( 'yes', 'caveat', 'conditional' ), true );
    }

    /**
     * True when a compact card badge is allowed. Only unambiguous cases: a
     * badge has no room for "not if you paid with a palveluseteli".
     */
    public static function allowsBadge( string $status ): bool {
        return 'yes' === $status;
    }

    /**
     * Review date as a Finnish formatted string.
     */
    public static function updatedLabel(): string {
        return date_i18n( 'j.n.Y', strtotime( self::UPDATED ) );
    }

    /**
     * True when the mapping has not been reviewed for over a year.
     */
    public static function isStale(): bool {
        return strtotime( self::UPDATED ) < strtotime( '-1 year' );
    }
}
