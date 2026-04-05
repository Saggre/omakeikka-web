<?php

namespace App\Widgets;

use Elementor\Widget_Base;

class Occupation_Grid_Widget extends Widget_Base {

    public function get_name() {
        return 'occupation_grid_widget';
    }

    public function get_title() {
        return esc_html__( 'Occupation Grid', 'omakeikka-theme' );
    }

    public function get_icon() {
        return 'eicon-posts-grid';
    }

    public function get_categories() {
        return array( 'basic' );
    }

    public function get_keywords() {
        return array( 'occupations', 'grid', 'ammatit' );
    }

    protected function render() {
        echo view( 'widgets/occupation-grid-widget', array(
            'occupations' => $this->occupations(),
        ) )->render();
    }

    /**
     * Return all published occupation posts with title, URL, and resolved icon URL,
     * ordered by menu_order. Cached for one hour.
     *
     * @return array
     */
    private function occupations(): array {
        $cached = get_transient( 'occupation_grid_widget' );

        if ( false !== $cached ) {
            return $cached;
        }

        $posts = get_posts( array(
            'post_type'      => 'occupation',
            'posts_per_page' => -1,
            'post_status'    => 'publish',
            'orderby'        => 'menu_order',
            'order'          => 'ASC',
        ) );

        $occupations = array();
        foreach ( $posts as $post ) {
            $isco_code     = (string) get_post_meta( $post->ID, 'isco_code', true );
            $occupations[] = array(
                'title'    => $post->post_title,
                'url'      => get_permalink( $post->ID ),
                'icon_url' => $isco_code ? $this->icon_url( $isco_code ) : '',
            );
        }

        set_transient( 'occupation_grid_widget', $occupations, HOUR_IN_SECONDS );

        return $occupations;
    }

    /**
     * Resolve the public URL for an occupation icon via the Bud asset manifest.
     *
     * @param string $isco_code
     * @return string
     */
    private function icon_url( string $isco_code ): string {
        static $manifest = null;

        if ( null === $manifest ) {
            $path     = get_theme_file_path( 'public/manifest.json' );
            $manifest = file_exists( $path )
                ? (array) json_decode( file_get_contents( $path ), true )
                : array();
        }

        $key  = 'images/occupations/' . $isco_code . '.svg';
        $file = $manifest[ $key ] ?? $key;

        return get_theme_file_uri( 'public/' . $file );
    }
}
