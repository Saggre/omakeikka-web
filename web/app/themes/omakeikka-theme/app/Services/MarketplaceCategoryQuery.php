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
     * @return array<int, array{name:string, slug:string, icon_class:string, sort_min:int, count:int, url:string, group_key:string, form_url:string, term_id:int}>
     */
    public static function groups( ?array $include = null ): array {
        $cache_key = 'marketplace_groups_v3_' . ( $include ? implode( '_', $include ) : 'all' );
        $cached    = get_transient( $cache_key );

        if ( false !== $cached ) {
            return self::withFormUrls( $cached );
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
                'group_key'  => $term->slug,
            );
        }

        usort( $groups, fn( $a, $b ) => $a['sort_min'] <=> $b['sort_min'] );

        set_transient( $cache_key, $groups, HOUR_IN_SECONDS );

        return self::withFormUrls( $groups );
    }

    /**
     * Return all categories assigned to a group term, ordered by menu_order.
     *
     * @return array<int, array{id:int, title:string, url:string, icon_class:string, app_create_url:string}>
     */
    public static function categoriesInGroup( int $term_id ): array {
        $cache_key = 'marketplace_categories_in_group_v3_' . $term_id;
        $cached    = get_transient( $cache_key );

        if ( false !== $cached ) {
            return self::withFormUrls( $cached );
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

        return self::withFormUrls( $items );
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

        $cache_key = 'marketplace_categories_by_key_v3_' . $group_key;
        $cached    = get_transient( $cache_key );

        if ( false !== $cached ) {
            return self::withFormUrls( $cached );
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

        return self::withFormUrls( $items );
    }

    /**
     * Group key of a category by its slug, empty when the category is unknown.
     */
    public static function groupKeyForCategory( string $category_slug ): string {
        $post = get_page_by_path( $category_slug, OBJECT, 'marketplace_category' );

        return $post ? (string) get_post_meta( $post->ID, 'group_key', true ) : '';
    }

    /**
     * Title of a category by its slug, empty when the category is unknown.
     */
    public static function categoryTitle( string $category_slug ): string {
        $post = get_page_by_path( $category_slug, OBJECT, 'marketplace_category' );

        return $post ? html_entity_decode( $post->post_title, ENT_QUOTES, 'UTF-8' ) : '';
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
     * Permalink of the page using the help request template.
     *
     * Falls back to the app create URL when the page does not exist yet.
     * Deploy order is code first, content second, so without the fallback
     * every buyer CTA on the site would render an empty href for the window
     * between deploying and creating the page.
     *
     * Cached under the marketplace_ prefix so the existing
     * omakeikka_flush_marketplace_caches() already clears it.
     */
    public static function helpRequestPageUrl(): string {
        $cached = get_transient( 'marketplace_help_request_page' );

        if ( false !== $cached ) {
            return $cached;
        }

        $ids = get_posts( array(
            'post_type'      => 'page',
            'post_status'    => 'publish',
            'posts_per_page' => 1,
            'fields'         => 'ids',
            'meta_key'       => '_wp_page_template',
            'meta_value'     => 'template-help-request.blade.php',
        ) );

        // The fallback is not cached, so the form works as soon as its page is published.
        if ( ! $ids ) {
            return self::appCreateUrl( 0 );
        }

        $url = (string) get_permalink( $ids[0] );

        set_transient( 'marketplace_help_request_page', $url, HOUR_IN_SECONDS );

        return $url;
    }

    /**
     * form_url is added after the cache, so publishing or moving the form page applies without a flush.
     */
    private static function withFormUrls( array $items ): array {
        return array_map( fn( $item ) => $item + array( 'form_url' => self::formUrl( $item['group_key'], $item['category_slug'] ?? '' ) ), $items );
    }

    /**
     * Help request form URL, optionally preselecting a group or category.
     */
    public static function formUrl( string $group_slug = '', string $category_slug = '' ): string {
        $url  = self::helpRequestPageUrl();
        $args = array_filter( array(
            'ryhma'   => $group_slug,
            'palvelu' => $category_slug,
        ) );

        return $args ? add_query_arg( $args, $url ) : $url;
    }

    /**
     * @return array{id:int, title:string, url:string, icon_class:string, app_create_url:string, group_key:string, category_slug:string}
     */
    private static function mapCategory( \WP_Post $post ): array {
        $app_id    = (int) get_post_meta( $post->ID, 'app_category_id', true );
        $group_key = (string) get_post_meta( $post->ID, 'group_key', true );

        return array(
            'id'             => $post->ID,
            'title'          => $post->post_title,
            'url'            => get_permalink( $post->ID ),
            'icon_class'     => (string) get_post_meta( $post->ID, 'icon_class', true ),
            'app_create_url' => self::appCreateUrl( $app_id ),
            'group_key'      => $group_key,
            'category_slug'  => $post->post_name,
        );
    }
}
