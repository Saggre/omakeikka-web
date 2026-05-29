<?php

namespace App\Services;

class MarketplaceCategoryQuery
{
    /**
     * Return all marketplace_group terms, sorted by group_sort_min meta.
     * Term names are HTML-decoded so Blade output is single-escaped.
     *
     * Cached for one hour. Invalidated by hooks in the marketplace-category-cpt
     * mu-plugin on category and group edits.
     *
     * @param int[]|null $include Limit to specific term IDs.
     * @return array<int, array{name:string, slug:string, icon_class:string, sort_min:int, count:int, url:string, term_id:int}>
     */
    public static function groups( ?array $include = null ): array {
        $cache_key = 'marketplace_groups_' . ( $include ? implode( '_', $include ) : 'all' );
        $cached    = get_transient( $cache_key );

        if ( false !== $cached ) {
            return $cached;
        }

        $args = array(
            'taxonomy'   => 'marketplace_group',
            'hide_empty' => false,
        );

        if ( $include ) {
            $args['include'] = $include;
        }

        $terms = get_terms( $args );

        if ( is_wp_error( $terms ) ) {
            return array();
        }

        $groups = array();
        foreach ( $terms as $term ) {
            $groups[] = array(
                'term_id'    => $term->term_id,
                'name'       => html_entity_decode( $term->name, ENT_QUOTES, 'UTF-8' ),
                'slug'       => $term->slug,
                'icon_class' => (string) get_term_meta( $term->term_id, 'group_icon', true ),
                'sort_min'   => (int) get_term_meta( $term->term_id, 'group_sort_min', true ),
                'count'      => (int) $term->count,
                'url'        => (string) get_term_link( $term ),
            );
        }

        usort( $groups, fn( $a, $b ) => $a['sort_min'] <=> $b['sort_min'] );

        set_transient( $cache_key, $groups, HOUR_IN_SECONDS );

        return $groups;
    }

    /**
     * Return all categories assigned to a group term, ordered by menu_order.
     *
     * @return array<int, array{id:int, title:string, url:string, icon_class:string, app_create_url:string}>
     */
    public static function categoriesInGroup( int $term_id ): array {
        $cache_key = 'marketplace_categories_in_group_' . $term_id;
        $cached    = get_transient( $cache_key );

        if ( false !== $cached ) {
            return $cached;
        }

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
            $items[] = self::mapCategory( $post );
        }

        set_transient( $cache_key, $items, HOUR_IN_SECONDS );

        return $items;
    }

    /**
     * Return all categories matching a group_key post meta, ordered by menu_order.
     * Used by the single-category view to render related categories without a
     * taxonomy term lookup.
     *
     * @return array<int, array{id:int, title:string, url:string, icon_class:string, app_create_url:string}>
     */
    public static function categoriesByGroupKey( string $group_key ): array {
        if ( '' === $group_key ) {
            return array();
        }

        $cache_key = 'marketplace_categories_by_key_' . $group_key;
        $cached    = get_transient( $cache_key );

        if ( false !== $cached ) {
            return $cached;
        }

        $posts = get_posts( array(
            'post_type'      => 'marketplace_category',
            'posts_per_page' => -1,
            'orderby'        => 'menu_order',
            'order'          => 'ASC',
            'meta_query'     => array(
                array(
                    'key'   => 'group_key',
                    'value' => $group_key,
                ),
            ),
        ) );

        $items = array();
        foreach ( $posts as $post ) {
            $items[] = self::mapCategory( $post );
        }

        set_transient( $cache_key, $items, HOUR_IN_SECONDS );

        return $items;
    }

    /**
     * Build the omakeikka app deep-link URL for a category.
     */
    public static function appCreateUrl( int $app_category_id ): string {
        return $app_category_id > 0
            ? 'https://app.omakeikka.fi/marketplace-requests/create?category_id=' . $app_category_id
            : 'https://app.omakeikka.fi/marketplace-requests/create';
    }

    /**
     * @return array{id:int, title:string, url:string, icon_class:string, app_create_url:string}
     */
    private static function mapCategory( \WP_Post $post ): array {
        $app_id = (int) get_post_meta( $post->ID, 'app_category_id', true );

        return array(
            'id'             => $post->ID,
            'title'          => $post->post_title,
            'url'            => get_permalink( $post->ID ),
            'icon_class'     => (string) get_post_meta( $post->ID, 'icon_class', true ),
            'app_create_url' => self::appCreateUrl( $app_id ),
        );
    }
}
