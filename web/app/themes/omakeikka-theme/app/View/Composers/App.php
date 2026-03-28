<?php

namespace App\View\Composers;

use Roots\Acorn\View\Composer;

class App extends Composer
{
    /**
     * List of views served by this composer.
     *
     * @var array
     */
    protected static $views = [
        '*',
    ];

    /**
     * Data to be passed to view before rendering.
     *
     * @return array
     */
    public function with()
    {
        return [
            'siteName'        => $this->siteName(),
            'occupation_links' => $this->occupation_links(),
        ];
    }

    /**
     * Returns the site name.
     *
     * @return string
     */
    public function siteName()
    {
        return get_bloginfo('name', 'display');
    }

    /**
     * Returns published occupation posts for the sitewide links partial.
     * Result is cached for one hour.
     *
     * @return array
     */
    public function occupation_links(): array
    {
        $cached = get_transient( 'occupation_links' );

        if ( false !== $cached ) {
            return $cached;
        }

        $posts = get_posts( array(
            'post_type'      => 'occupation',
            'posts_per_page' => -1,
            'post_status'    => 'publish',
            'orderby'        => 'title',
            'order'          => 'ASC',
        ) );

        $links = array();
        foreach ( $posts as $post ) {
            $links[] = array(
                'title' => $post->post_title,
                'url'   => get_permalink( $post->ID ),
            );
        }

        set_transient( 'occupation_links', $links, HOUR_IN_SECONDS );

        return $links;
    }
}
