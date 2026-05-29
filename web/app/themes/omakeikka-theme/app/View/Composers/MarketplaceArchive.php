<?php

namespace App\View\Composers;

use App\Services\MarketplaceCategoryQuery;
use Roots\Acorn\View\Composer;

class MarketplaceArchive extends Composer
{
    /**
     * List of views served by this composer.
     *
     * @var array
     */
    protected static $views = [
        'archive-marketplace_category',
        'taxonomy-marketplace_group',
    ];

    /**
     * Data to be passed to view before rendering.
     *
     * @return array
     */
    public function with(): array
    {
        return array(
            'marketplace_groups' => $this->groupsWithCategories(),
        );
    }

    /**
     * Each group plus its category list. On a single group archive only the
     * queried group is returned, so the same template handles both archives.
     *
     * @return array
     */
    private function groupsWithCategories(): array {
        $current = is_tax( 'marketplace_group' ) ? get_queried_object() : null;
        $include = $current ? array( $current->term_id ) : null;

        $groups = MarketplaceCategoryQuery::groups( $include );

        foreach ( $groups as &$group ) {
            $group['categories'] = MarketplaceCategoryQuery::categoriesInGroup( $group['term_id'] );
        }

        return $groups;
    }
}
