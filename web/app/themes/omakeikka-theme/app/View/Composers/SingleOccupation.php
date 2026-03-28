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

        $cta_singular  = (string) get_post_meta( $post_id, 'cta_singular', true );
        $cta_partitive = (string) get_post_meta( $post_id, 'cta_partitive', true );
        $alt_titles    = $this->alt_titles( $post_id );

        return array(
            'isco_code'            => $isco_code,
            'cta_singular'         => $cta_singular ?: mb_strtolower( get_the_title() ),
            'cta_partitive'        => $cta_partitive ?: mb_strtolower( get_the_title() ),
            'alt_titles'           => $alt_titles,
            'related_occupations'  => $this->related_occupations( $isco_code, $post_id ),
            'municipality_links'   => $this->municipality_links( $isco_code ),
            'json_ld'              => $this->json_ld( $post_id, $isco_code ),
        );
    }

    /**
     * Return occupations sharing the same 3-digit ISCO prefix, excluding the current post.
     *
     * @param string $isco_code
     * @param int    $post_id
     * @return array
     */
    private function related_occupations( string $isco_code, int $post_id ): array {
        if ( strlen( $isco_code ) < 3 ) {
            return array();
        }

        $prefix        = substr( $isco_code, 0, 3 );
        $transient_key = 'occupation_related_' . $prefix;
        $cached        = get_transient( $transient_key );

        if ( false === $cached ) {
            $posts = get_posts( array(
                'post_type'      => 'occupation',
                'posts_per_page' => -1,
                'post_status'    => 'publish',
                'fields'         => 'ids',
            ) );

            $cached = array();
            foreach ( $posts as $id ) {
                $code = (string) get_post_meta( $id, 'isco_code', true );
                if ( strncmp( $code, $prefix, 3 ) === 0 ) {
                    $cached[] = array(
                        'id'    => $id,
                        'title' => get_the_title( $id ),
                        'url'   => get_permalink( $id ),
                    );
                }
            }

            set_transient( $transient_key, $cached, HOUR_IN_SECONDS );
        }

        return array_values( array_filter( $cached, function ( $item ) use ( $post_id ) {
            return $item['id'] !== $post_id;
        } ) );
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
        $response = wp_remote_get( $url, array( 'timeout' => 5 ) );

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

    /**
     * Build the schema.org/Occupation JSON-LD string for this post.
     *
     * @param int    $post_id
     * @param string $isco_code
     * @return string
     */
    private function json_ld( int $post_id, string $isco_code ): string {
        $data = array(
            '@context'             => 'https://schema.org',
            '@type'                => 'Occupation',
            'name'                 => get_the_title( $post_id ),
            'occupationalCategory' => $isco_code,
            'occupationLocation'   => array(
                '@type' => 'Country',
                'name'  => 'Finland',
            ),
            'url' => get_permalink( $post_id ),
        );

        $excerpt = get_the_excerpt( $post_id );
        if ( $excerpt ) {
            $data['description'] = wp_strip_all_tags( $excerpt );
        }

        return (string) wp_json_encode( $data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT );
    }
}
