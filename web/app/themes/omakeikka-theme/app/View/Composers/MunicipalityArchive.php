<?php

namespace App\View\Composers;

use Roots\Acorn\View\Composer;

class MunicipalityArchive extends Composer
{
    /**
     * List of views served by this composer.
     *
     * @var array
     */
    protected static $views = [
        'taxonomy-municipality',
    ];

    /**
     * Data to be passed to view before rendering.
     *
     * @return array
     */
    public function with(): array
    {
        $term            = get_queried_object();
        $municipality_id = (string) get_term_meta( $term->term_id, 'municipality_id', true );

        return array(
            'municipality'    => $term,
            'municipality_id' => $municipality_id,
            'occupations'     => $this->occupations( $term->term_id ),
            'json_ld'         => $this->json_ld( $term ),
        );
    }

    /**
     * Return published occupation posts tagged with this municipality term.
     *
     * @param int $term_id
     * @return array
     */
    private function occupations( int $term_id ): array {
        $posts = get_posts( array(
            'post_type'      => 'occupation',
            'posts_per_page' => -1,
            'post_status'    => 'publish',
            'tax_query'      => array(
                array(
                    'taxonomy' => 'municipality',
                    'field'    => 'term_id',
                    'terms'    => $term_id,
                ),
            ),
            'orderby' => 'title',
            'order'   => 'ASC',
        ) );

        $result = array();
        foreach ( $posts as $post ) {
            $result[] = array(
                'title'     => $post->post_title,
                'url'       => get_permalink( $post->ID ),
                'excerpt'   => get_the_excerpt( $post->ID ),
                'isco_code' => (string) get_post_meta( $post->ID, 'isco_code', true ),
            );
        }

        return $result;
    }

    /**
     * Build the schema.org/Occupation JSON-LD for the municipality archive.
     *
     * Uses ItemList to represent the collection of occupations in this city.
     *
     * @param \WP_Term $term
     * @return string
     */
    private function json_ld( \WP_Term $term ): string {
        $data = array(
            '@context'        => 'https://schema.org',
            '@type'           => 'ItemList',
            'name'            => sprintf( 'Ammatit %s', $term->name ),
            'description'     => sprintf( 'Ammatit ja työnhakijat %s', $term->name ),
            'url'             => get_term_link( $term ),
        );

        return (string) wp_json_encode( $data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT );
    }
}
