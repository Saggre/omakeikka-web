<?php

/**
 * Theme setup.
 */

namespace App;

use App\Widgets\Municipality_Map_Widget;
use App\Widgets\Occupation_Scroll_Widget;
use App\Widgets\Test_Widget;
use function Roots\bundle;

/**
 * Disable the error exception.
 *
 * @see https://github.com/roots/sage/issues/2965
 */
add_filter( 'acorn/throw_error_exception', '__return_false' );

/**
 * Order occupation archive by menu_order (ascending) and show all posts on one page.
 * menu_order is set in the seed script to reflect employee count rank (1 = most popular).
 */
add_action( 'pre_get_posts', function ( \WP_Query $query ) {
    if ( ! $query->is_main_query() || ! $query->is_post_type_archive( 'occupation' ) ) {
        return;
    }

    $query->set( 'orderby', 'menu_order' );
    $query->set( 'order', 'ASC' );
    $query->set( 'posts_per_page', -1 );
} );

/**
 * Inject schema.org/Occupation JSON-LD via Rank Math's filter.
 * Rank Math strips <script type="application/ld+json"> added in the body,
 * so the schema must be provided through this filter during wp_head.
 */
add_filter( 'rank_math/json_ld', function ( array $entities ) {
    if ( ! is_singular( 'occupation' ) ) {
        return $entities;
    }

    $post_id   = get_queried_object_id();
    $isco_code = (string) get_post_meta( $post_id, 'isco_code', true );

    $entity = array(
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
        $entity['description'] = wp_strip_all_tags( $excerpt );
    }

    $entities['occupation'] = $entity;

    return $entities;
} );

add_action( 'elementor/widgets/register', function ( $widgets_manager ) {
    $widgets_manager->register( new Test_Widget() );
    $widgets_manager->register( new Municipality_Map_Widget() );
    $widgets_manager->register( new Occupation_Scroll_Widget() );
} );

/**
 * Register the theme assets.
 *
 * @return void
 */
add_action( 'wp_enqueue_scripts', function () {
    bundle( 'app' )->enqueue();
}, 100 );

/**
 * Register the theme assets with the block editor.
 *
 * @return void
 */
add_action( 'enqueue_block_editor_assets', function () {
    bundle( 'editor' )->enqueue();
}, 100 );

/**
 * Register the initial theme setup.
 *
 * @return void
 */
add_action( 'after_setup_theme', function () {
    /**
     * Enable features from the Soil plugin if activated.
     *
     * @link https://roots.io/plugins/soil/
     */
    add_theme_support( 'soil', [
        'clean-up',
        'nav-walker',
        'nice-search',
        'relative-urls',
    ] );

    /**
     * Disable full-site editing support.
     *
     * @link https://wptavern.com/gutenberg-10-5-embeds-pdfs-adds-verse-block-color-options-and-introduces-new-patterns
     */
    remove_theme_support( 'block-templates' );

    /**
     * Register the navigation menus.
     *
     * @link https://developer.wordpress.org/reference/functions/register_nav_menus/
     */
    register_nav_menus( [
        'primary_navigation' => __( 'Primary Navigation', 'sage' ),
    ] );

    /**
     * Disable the default block patterns.
     *
     * @link https://developer.wordpress.org/block-editor/developers/themes/theme-support/#disabling-the-default-block-patterns
     */
    remove_theme_support( 'core-block-patterns' );

    /**
     * Enable plugins to manage the document title.
     *
     * @link https://developer.wordpress.org/reference/functions/add_theme_support/#title-tag
     */
    add_theme_support( 'title-tag' );

    /**
     * Enable post thumbnail support.
     *
     * @link https://developer.wordpress.org/themes/functionality/featured-images-post-thumbnails/
     */
    add_theme_support( 'post-thumbnails' );

    /**
     * Enable responsive embed support.
     *
     * @link https://developer.wordpress.org/block-editor/how-to-guides/themes/theme-support/#responsive-embedded-content
     */
    add_theme_support( 'responsive-embeds' );

    /**
     * Enable HTML5 markup support.
     *
     * @link https://developer.wordpress.org/reference/functions/add_theme_support/#html5
     */
    add_theme_support( 'html5', [
        'caption',
        'comment-form',
        'comment-list',
        'gallery',
        'search-form',
        'script',
        'style',
    ] );

    /**
     * Enable selective refresh for widgets in customizer.
     *
     * @link https://developer.wordpress.org/reference/functions/add_theme_support/#customize-selective-refresh-widgets
     */
    add_theme_support( 'customize-selective-refresh-widgets' );
}, 20 );

/**
 * Register the theme sidebars.
 *
 * @return void
 */
add_action( 'widgets_init', function () {
    $config = [
        'before_widget' => '<section class="widget %1$s %2$s">',
        'after_widget'  => '</section>',
        'before_title'  => '<h3>',
        'after_title'   => '</h3>',
    ];

    register_sidebar( [
                          'name' => __( 'Primary', 'sage' ),
                          'id'   => 'sidebar-primary',
                      ] + $config );

    register_sidebar( [
                          'name' => __( 'Footer', 'sage' ),
                          'id'   => 'sidebar-footer',
                      ] + $config );
} );
