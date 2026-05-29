<?php

namespace App\View\Composers;

use App\Services\MarketplaceCategoryQuery;
use Roots\Acorn\View\Composer;

class SingleMarketplaceCategory extends Composer
{
    /**
     * List of views served by this composer.
     *
     * @var array
     */
    protected static $views = [
        'single-marketplace_category',
    ];

    /**
     * Data to be passed to view before rendering.
     *
     * @return array
     */
    public function with(): array
    {
        $post_id   = get_queried_object_id();
        $app_id    = (int) get_post_meta( $post_id, 'app_category_id', true );
        $icon      = (string) get_post_meta( $post_id, 'icon_class', true );
        $group_key = (string) get_post_meta( $post_id, 'group_key', true );

        $title = html_entity_decode( get_the_title( $post_id ), ENT_QUOTES, 'UTF-8' );

        return array(
            'category_title'    => $title,
            'category_title_lc' => mb_strtolower( $title ),
            'category_icon'     => $icon,
            'category_group'    => $this->group( $group_key ),
            'app_create_url'    => MarketplaceCategoryQuery::appCreateUrl( $app_id ),
            'related'           => $this->relatedInGroup( $post_id, $group_key ),
        );
    }

    /**
     * Return group term (name, slug, url, icon) by its group_key meta value.
     */
    private function group( string $group_key ): ?array {
        if ( '' === $group_key ) {
            return null;
        }

        $term = get_term_by( 'slug', $group_key, 'marketplace_group' );

        if ( ! $term || is_wp_error( $term ) ) {
            return null;
        }

        return array(
            'name' => html_entity_decode( $term->name, ENT_QUOTES, 'UTF-8' ),
            'slug' => $term->slug,
            'url'  => get_term_link( $term ),
            'icon' => (string) get_term_meta( $term->term_id, 'group_icon', true ),
        );
    }

    /**
     * Other categories in the same group, current one excluded.
     */
    private function relatedInGroup( int $post_id, string $group_key ): array {
        $all = MarketplaceCategoryQuery::categoriesByGroupKey( $group_key );

        return array_values( array_filter( $all, fn( $item ) => $item['id'] !== $post_id ) );
    }
}
