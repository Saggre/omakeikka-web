<?php

namespace App\View\Composers;

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

        $app_create_url = $app_id > 0
            ? 'https://app.omakeikka.fi/marketplace-requests/create?category_id=' . $app_id
            : 'https://app.omakeikka.fi/marketplace-requests/create';

        return array(
            'category_title'    => html_entity_decode( get_the_title( $post_id ), ENT_QUOTES, 'UTF-8' ),
            'category_title_lc' => mb_strtolower( html_entity_decode( get_the_title( $post_id ), ENT_QUOTES, 'UTF-8' ) ),
            'category_icon'     => $icon,
            'category_group'    => $this->group( $group_key ),
            'app_create_url'    => $app_create_url,
            'related'           => $this->related_in_group( $post_id, $group_key ),
        );
    }

    /**
     * Return group term (name, slug, url, icon).
     *
     * @param string $group_key
     * @return array|null
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
     * Return other categories in the same group, excluding the current one.
     *
     * @param int    $post_id
     * @param string $group_key
     * @return array
     */
    private function related_in_group( int $post_id, string $group_key ): array {
        if ( '' === $group_key ) {
            return array();
        }

        $cache_key = 'marketplace_related_' . $group_key;
        $cached    = get_transient( $cache_key );

        if ( false === $cached ) {
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

            $cached = array();
            foreach ( $posts as $post ) {
                $cached[] = array(
                    'id'         => $post->ID,
                    'title'      => $post->post_title,
                    'url'        => get_permalink( $post->ID ),
                    'icon_class' => (string) get_post_meta( $post->ID, 'icon_class', true ),
                );
            }

            set_transient( $cache_key, $cached, HOUR_IN_SECONDS );
        }

        return array_values( array_filter( $cached, fn( $item ) => $item['id'] !== $post_id ) );
    }
}
