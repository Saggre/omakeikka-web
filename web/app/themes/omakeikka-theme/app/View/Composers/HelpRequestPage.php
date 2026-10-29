<?php

namespace App\View\Composers;

use App\Services\HelpRequest\Budget;
use App\Services\HelpRequest\Form;
use App\Services\HelpRequest\Prefill;
use App\Services\MarketplaceCategoryQuery;
use App\Services\Municipalities;
use Roots\Acorn\View\Composer;

class HelpRequestPage extends Composer
{
    /**
     * List of views served by this composer.
     *
     * @var array
     */
    protected static $views = [
        'template-help-request',
    ];

    public function __construct(
        private readonly Form $form,
        private readonly Municipalities $municipalities,
    ) {}

    /**
     * Data to be passed to view before rendering.
     *
     * @return array
     */
    public function with(): array
    {
        $success = isset( $_GET['kiitos'] );
        $stash   = $this->form->takeStash( sanitize_key( wp_unslash( $_GET[ $success ? 'kiitos' : 'virhe' ] ?? '' ) ) );
        $values  = $stash['values'];

        // Submitted values beat the query string preselection.
        $values['group_slug']    = ( $values['group_slug'] ?? '' ) ?: sanitize_key( wp_unslash( $_GET['ryhma'] ?? '' ) );
        $values['category_slug'] = ( $values['category_slug'] ?? '' ) ?: sanitize_key( wp_unslash( $_GET['palvelu'] ?? '' ) );

        // The app links here with only the category, since it has no groups.
        if ( '' === $values['group_slug'] && '' !== $values['category_slug'] ) {
            $values['group_slug'] = MarketplaceCategoryQuery::groupKeyForCategory( $values['category_slug'] );
        }

        $groups = MarketplaceCategoryQuery::groups();

        // A fresh visit starts with editable text for the chosen group, and a
        // category link titles the request after the category.
        if ( ! $stash['values'] ) {
            $prefill = Prefill::forGroup( $values['group_slug'] );

            $values['title']       = MarketplaceCategoryQuery::categoryTitle( $values['category_slug'] ) ?: $prefill['title'];
            $values['description'] = $prefill['description'];
        }

        return array(
            'form_errors'    => $stash['errors'],
            'form_values'    => $values,
            'form_success'   => $success,
            'form_prefilled' => ! $stash['values'],
            'prefills'       => array_combine( array_column( $groups, 'slug' ), array_map( fn( $group ) => Prefill::forGroup( $group['slug'] ), $groups ) ),
            'groups'         => $groups,
            'municipalities' => $this->municipalities->options(),
            'budgets'        => Budget::cases(),
        );
    }
}
