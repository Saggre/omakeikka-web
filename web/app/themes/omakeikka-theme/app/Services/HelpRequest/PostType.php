<?php

namespace App\Services\HelpRequest;

use App\Services\Municipalities;

/**
 * Registers the private help_request post type and its meta.
 *
 * The type holds personal data, so it is never public, queryable or exposed
 * over REST. It has no rewrite rules, so it never needs a flush. 'Add New' is
 * disabled so the validated form is the only write path.
 */
final class PostType
{
    public function __construct(
        private readonly Municipalities $municipalities,
    ) {}

    public function register(): void {
        register_post_type(
            Repository::POST_TYPE,
            array(
                'labels'              => array(
                    'name'          => __( 'Help requests', 'omakeikka-theme' ),
                    'singular_name' => __( 'Help request', 'omakeikka-theme' ),
                    'edit_item'     => __( 'Edit help request', 'omakeikka-theme' ),
                    'search_items'  => __( 'Search help requests', 'omakeikka-theme' ),
                    'not_found'     => __( 'No help requests found', 'omakeikka-theme' ),
                ),
                'public'              => false,
                'publicly_queryable'  => false,
                'exclude_from_search' => true,
                'show_ui'             => true,
                'show_in_nav_menus'   => false,
                'show_in_admin_bar'   => false,
                'show_in_rest'        => false,
                'rewrite'             => false,
                'query_var'           => false,
                'menu_icon'           => 'dashicons-megaphone',
                'menu_position'       => 7,
                'supports'            => array( 'title', 'editor' ),
                'map_meta_cap'        => true,
                'capabilities'        => array( 'create_posts' => 'do_not_allow' ),
                'delete_with_user'    => false,
            )
        );

        // Underscore prefixed keys are protected meta, hidden from the Custom
        // Fields UI and not settable through meta_input. The sanitize callback
        // runs on every write path, not only the form.
        $keys = array(
            '_hr_name'            => 'sanitize_text_field',
            '_hr_email'           => 'sanitize_email',
            '_hr_phone'           => array( Submission::class, 'phone' ),
            '_hr_municipality_id' => fn( $value ) => $this->municipalities->isValid( (string) $value ) ? (string) $value : '',
            '_hr_group_slug'      => 'sanitize_key',
            '_hr_category_slug'   => 'sanitize_key',
            '_hr_budget'          => fn( $value ) => Budget::tryFrom( (string) $value )?->value ?? '',
            '_hr_status'          => fn( $value ) => ( Status::tryFrom( (string) $value ) ?? Status::New )->value,
            '_hr_consent_at'      => 'sanitize_text_field',
            '_hr_consent_version' => 'sanitize_key',
            '_hr_ip_hash'         => 'sanitize_text_field',
        );

        foreach ( $keys as $key => $sanitize ) {
            register_post_meta(
                Repository::POST_TYPE,
                $key,
                array(
                    'type'              => 'string',
                    'single'            => true,
                    'show_in_rest'      => false,
                    'sanitize_callback' => $sanitize,
                    'auth_callback'     => fn() => current_user_can( 'edit_posts' ),
                )
            );
        }
    }
}
