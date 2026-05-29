<?php

namespace App\Widgets;

use Elementor\Widget_Base;

class Marketplace_Category_Grid_Widget extends Widget_Base {

    public function get_name() {
        return 'marketplace_category_grid_widget';
    }

    public function get_title() {
        return esc_html__( 'Marketplace Category Grid', 'omakeikka-theme' );
    }

    public function get_icon() {
        return 'eicon-posts-grid';
    }

    public function get_categories() {
        return array( 'basic' );
    }

    public function get_keywords() {
        return array( 'marketplace', 'palvelut', 'palvelutori', 'categories' );
    }

    protected function render() {
        echo view( 'widgets/marketplace-category-grid-widget', array(
            'groups' => $this->groups_with_top_categories(),
        ) )->render();
    }

    /**
     * Return all marketplace_group terms with their first few categories,
     * sorted by the group's sort_min meta. Cached for one hour.
     *
     * @return array
     */
    private function groups_with_top_categories(): array {
        $cached = get_transient( 'marketplace_category_grid' );

        if ( false !== $cached ) {
            return $cached;
        }

        $terms = get_terms( array(
            'taxonomy'   => 'marketplace_group',
            'hide_empty' => false,
        ) );

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
                'url'        => get_term_link( $term ),
                'categories' => $this->top_categories_for_term( $term->term_id ),
            );
        }

        usort( $groups, fn( $a, $b ) => $a['sort_min'] <=> $b['sort_min'] );

        set_transient( 'marketplace_category_grid', $groups, HOUR_IN_SECONDS );

        return $groups;
    }

    /**
     * Return all categories assigned to a group term, ordered by menu_order.
     *
     * @param int $term_id
     * @return array
     */
    private function top_categories_for_term( int $term_id ): array {
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
            $items[] = array(
                'title'      => $post->post_title,
                'url'        => get_permalink( $post->ID ),
                'icon_class' => (string) get_post_meta( $post->ID, 'icon_class', true ),
            );
        }

        return $items;
    }
}
