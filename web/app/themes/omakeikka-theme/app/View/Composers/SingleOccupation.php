<?php

namespace App\View\Composers;

use Roots\Acorn\View\Composer;

class SingleOccupation extends Composer
{
    /**
     * List of views served by this composer.
     *
     * @var array
     */
    protected static $views = [
        'single-occupation',
    ];

    /**
     * Data to be passed to view before rendering.
     *
     * @return array
     */
    public function with(): array
    {
        $post_id   = get_queried_object_id();
        $isco_code = (string) get_post_meta( $post_id, 'isco_code', true );

        $cta_singular          = (string) get_post_meta( $post_id, 'cta_singular', true );
        $cta_partitive         = (string) get_post_meta( $post_id, 'cta_partitive', true );
        $cta_partitive_singular = (string) get_post_meta( $post_id, 'cta_partitive_singular', true );
        $alt_titles            = $this->alt_titles( $post_id );
        $title_lower           = mb_strtolower( get_the_title() );

        return array(
            'isco_code'              => $isco_code,
            'cta_singular'           => $cta_singular ?: $title_lower,
            'cta_partitive'          => $cta_partitive ?: $title_lower,
            'cta_partitive_singular' => $cta_partitive_singular ?: $title_lower,
            'alt_titles'             => $alt_titles,
            'related_occupations'    => $this->other_occupations( $post_id ),
            'municipality_links'     => $this->municipality_links( $isco_code ),
        );
    }

    /**
     * Return all published occupation posts except the current one, ordered by menu_order.
     *
     * @param int $post_id
     * @return array
     */
    private function other_occupations( int $post_id ): array {
        $cached = get_transient( 'occupation_all_links' );

        if ( false === $cached ) {
            $posts = get_posts( array(
                'post_type'      => 'occupation',
                'posts_per_page' => -1,
                'post_status'    => 'publish',
                'orderby'        => 'menu_order',
                'order'          => 'ASC',
            ) );

            $cached = array();
            foreach ( $posts as $post ) {
                $cached[] = array(
                    'id'    => $post->ID,
                    'title' => $post->post_title,
                    'url'   => get_permalink( $post->ID ),
                );
            }

            set_transient( 'occupation_all_links', $cached, HOUR_IN_SECONDS );
        }

        return array_values( array_filter( $cached, fn( $item ) => $item['id'] !== $post_id ) );
    }

    /**
     * Return alternative plural titles stored as JSON in post meta.
     *
     * @param int $post_id
     * @return array
     */
    private function alt_titles( int $post_id ): array {
        $raw = get_post_meta( $post_id, 'alt_titles', true );

        if ( ! $raw ) {
            return array();
        }

        $decoded = json_decode( $raw, true );

        return is_array( $decoded ) ? $decoded : array();
    }

    /**
     * Return municipalities that have at least one registered employee with this occupation,
     * fetched from the omakeikka app API and cached for one hour.
     *
     * @param string $isco_code
     * @return array
     */
    private function municipality_links( string $isco_code ): array {
        if ( '' === $isco_code ) {
            return array();
        }

        $transient_key = 'occupation_municipalities_' . $isco_code;
        $cached        = get_transient( $transient_key );

        if ( false !== $cached ) {
            return $cached;
        }

        $url      = 'https://app.omakeikka.fi/api/occupations/' . rawurlencode( $isco_code ) . '/municipalities';
        $response = wp_remote_get( $url, array(
            'timeout' => 5,
            'headers' => array( 'Accept' => 'application/json' ),
        ) );

        if ( is_wp_error( $response ) || 200 !== wp_remote_retrieve_response_code( $response ) ) {
            return array();
        }

        $body = json_decode( wp_remote_retrieve_body( $response ), true );

        if ( ! isset( $body['municipalities'] ) || ! is_array( $body['municipalities'] ) ) {
            return array();
        }

        $links = array_map(
            fn( $item ) => array( 'name' => $item['title'] ),
            $body['municipalities']
        );

        set_transient( $transient_key, $links, HOUR_IN_SECONDS );

        return $links;
    }

}

