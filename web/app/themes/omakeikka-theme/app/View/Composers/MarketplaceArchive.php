<?php

namespace App\View\Composers;

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
            'marketplace_groups' => $this->groups_with_categories(),
        );
    }

    /**
     * Return all marketplace_group terms with their categories, sorted by sort_min.
     * On a group taxonomy archive only the current group is returned.
     * Cached for one hour.
     *
     * @return array
     */
    private function groups_with_categories(): array {
        $current_term = is_tax( 'marketplace_group' ) ? get_queried_object() : null;
        $cache_key    = $current_term ? 'marketplace_archive_group_' . $current_term->term_id : 'marketplace_archive_all';
        $cached       = get_transient( $cache_key );

        if ( false !== $cached ) {
            return $cached;
        }

        $term_args = array(
            'taxonomy'   => 'marketplace_group',
            'hide_empty' => false,
        );

        if ( $current_term ) {
            $term_args['include'] = array( $current_term->term_id );
        }

        $terms = get_terms( $term_args );

        if ( is_wp_error( $terms ) ) {
            return array();
        }

        $groups = array();
        foreach ( $terms as $term ) {
            $groups[] = array(
                'name'       => html_entity_decode( $term->name, ENT_QUOTES, 'UTF-8' ),
                'slug'       => $term->slug,
                'icon_class' => (string) get_term_meta( $term->term_id, 'group_icon', true ),
                'sort_min'   => (int) get_term_meta( $term->term_id, 'group_sort_min', true ),
                'count'      => (int) $term->count,
                'categories' => $this->categories_for_term( $term->term_id ),
            );
        }

        usort( $groups, fn( $a, $b ) => $a['sort_min'] <=> $b['sort_min'] );

        set_transient( $cache_key, $groups, HOUR_IN_SECONDS );

        return $groups;
    }

    /**
     * Return all categories assigned to a group term, ordered by menu_order.
     *
     * @param int $term_id
     * @return array
     */
    private function categories_for_term( int $term_id ): array {
        $posts = get_posts( array(
            'post_type'      => 'marketplace_category',
            'posts_per_page' => -1,
            'orderby'        => 'menu_order',
            'order'          => 'ASC',
            'tax_query'      => array(
                array(
                    'taxonomy' => 'marketplace_group',
                    'field'    => 'term_id',
                    'terms'    => $term_id,
                ),
            ),
        ) );

        $items = array();
        foreach ( $posts as $post ) {
            $app_id = (int) get_post_meta( $post->ID, 'app_category_id', true );

            $items[] = array(
                'title'         => $post->post_title,
                'url'           => get_permalink( $post->ID ),
                'icon_class'    => (string) get_post_meta( $post->ID, 'icon_class', true ),
                'app_create_url' => $app_id > 0
                    ? 'https://app.omakeikka.fi/marketplace-requests/create?category_id=' . $app_id
                    : 'https://app.omakeikka.fi/marketplace-requests/create',
            );
        }

        return $items;
    }
}
