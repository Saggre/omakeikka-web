<?php

namespace App\View\Composers;

use App\Services\HelpRequestForm;
use App\Services\HouseholdDeduction;
use App\Services\MarketplaceCategoryQuery;
use App\Services\Municipalities;
use Roots\Acorn\View\Composer;

/**
 * Data for the household help request form.
 *
 * All $_GET reading happens here so the Blade templates stay free of
 * superglobals.
 */
class HelpRequestPage extends Composer
{
    /**
     * List of views served by this composer.
     *
     * Only the page template is listed. The form partial must NOT be here:
     * with() consumes a one-time error transient, so a second composer run
     * for the @include would return an empty stash and the visitor would lose
     * their validation errors. The partial inherits this data from the parent
     * view scope instead.
     *
     * @var array
     */
    protected static $views = [
        'template-help-request',
    ];

    /**
     * Data to be passed to view before rendering.
     *
     * @return array
     */
    public function with(): array
    {
        $stash = HelpRequestForm::takeStash(
            isset( $_GET['virhe'] ) ? sanitize_key( wp_unslash( $_GET['virhe'] ) ) : ''
        );

        $group    = isset( $_GET['ryhma'] ) ? sanitize_key( wp_unslash( $_GET['ryhma'] ) ) : '';
        $category = isset( $_GET['palvelu'] ) ? sanitize_key( wp_unslash( $_GET['palvelu'] ) ) : '';

        // A value the visitor already submitted beats a preselection from the
        // query string, otherwise a failed submit would silently reset it.
        $values = $stash['values'];

        if ( '' !== $group && empty( $values['group_slug'] ) ) {
            $values['group_slug'] = $group;
        }

        if ( '' !== $category && empty( $values['category_slug'] ) ) {
            $values['category_slug'] = $category;
        }

        // The app links here with only the category, since it has no groups.
        if ( empty( $values['group_slug'] ) && ! empty( $values['category_slug'] ) ) {
            $values['group_slug'] = MarketplaceCategoryQuery::groupKeyForCategory( $values['category_slug'] );
        }

        $selected_group = (string) ( $values['group_slug'] ?? '' );

        return array(
            'hr_groups'         => MarketplaceCategoryQuery::groups(),
            'hr_municipalities' => Municipalities::options(),
            'hr_budgets'        => \OMAKEIKKA_HELP_REQUEST_BUDGETS,
            'hr_errors'         => $stash['errors'],
            'hr_values'         => $values,
            'hr_success'        => isset( $_GET['kiitos'] ),
            'hr_action'         => admin_url( 'admin-post.php' ),
            'hr_action_name'    => HelpRequestForm::ACTION,
            'hr_nonce_name'     => HelpRequestForm::NONCE,
            'hr_timestamp'      => HelpRequestForm::timestampToken(),
            'hr_source_url'     => $this->sourceUrl(),
            'hr_deduction'      => HouseholdDeduction::forGroup( $selected_group ),
            'hr_deduction_url'  => HouseholdDeduction::INFO_URL,
            'hr_deduction_date' => HouseholdDeduction::updatedLabel(),
        );
    }

    /**
     * The page the visitor is on, recorded so we can tell which entry point
     * produced the lead.
     */
    private function sourceUrl(): string
    {
        $referer = wp_get_referer();

        return $referer ? (string) $referer : (string) get_permalink();
    }
}
