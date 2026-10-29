<?php

namespace App\Services\HelpRequest;

/**
 * Reads and writes help_request posts and their protected meta.
 */
final class Repository
{
    public const POST_TYPE = 'help_request';

    /**
     * Bumped whenever the consent wording changes, so an old record still says
     * what was agreed to.
     */
    public const CONSENT_VERSION = '2026-09';

    /** Meta holding personal data, dropped on anonymisation. */
    private const PERSONAL_META = array( '_hr_name', '_hr_email', '_hr_phone', '_hr_ip_hash' );

    /**
     * Insert a request. Returns 0 on failure.
     */
    public function create( Submission $submission, string $ip_hash ): int {
        $post_id = wp_insert_post(
            array(
                'post_type'    => self::POST_TYPE,
                'post_status'  => 'private',
                'post_title'   => wp_slash( $submission->title ),
                'post_content' => wp_slash( $submission->description ),
            )
        );

        if ( ! $post_id ) {
            return 0;
        }

        $meta = array(
            '_hr_name'            => $submission->name,
            '_hr_email'           => $submission->email,
            '_hr_phone'           => $submission->phone,
            '_hr_municipality_id' => $submission->municipality_id,
            '_hr_group_slug'      => $submission->group_slug,
            '_hr_category_slug'   => $submission->category_slug,
            '_hr_budget'          => $submission->budget,
            '_hr_status'          => Status::New->value,
            '_hr_consent_at'      => gmdate( 'Y-m-d H:i:s' ),
            '_hr_consent_version' => self::CONSENT_VERSION,
            '_hr_ip_hash'         => $ip_hash,
        );

        foreach ( array_filter( $meta, 'strlen' ) as $key => $value ) {
            update_post_meta( $post_id, $key, wp_slash( $value ) );
        }

        return $post_id;
    }

    public function status( int $post_id ): Status {
        return Status::tryFrom( (string) get_post_meta( $post_id, '_hr_status', true ) ) ?? Status::New;
    }

    public function setStatus( int $post_id, Status $status ): void {
        update_post_meta( $post_id, '_hr_status', $status->value );
    }

    /**
     * Strip the personal data from one request, keeping the countable fields.
     */
    public function anonymise( int $post_id ): void {
        foreach ( self::PERSONAL_META as $key ) {
            delete_post_meta( $post_id, $key );
        }

        wp_update_post(
            array(
                'ID'           => $post_id,
                'post_title'   => __( 'Anonymised request', 'omakeikka-theme' ),
                'post_content' => __( 'Content removed after the retention period.', 'omakeikka-theme' ),
            )
        );
    }

    public function delete( int $post_id ): void {
        wp_delete_post( $post_id, true );
    }

    /**
     * @return int[]
     */
    public function idsByEmail( string $email, int $page, int $per_page ): array {
        return $this->ids(
            array(
                'posts_per_page' => $per_page,
                'paged'          => max( 1, $page ),
                'meta_key'       => '_hr_email',
                'meta_value'     => sanitize_email( $email ),
            )
        );
    }

    /**
     * Up to 100 requests older than $days, optionally only those still
     * carrying $meta_key. Batched so a backlog cannot time out cron.
     *
     * @return int[]
     */
    public function idsOlderThan( int $days, string $meta_key = '' ): array {
        $args = array(
            'posts_per_page' => 100,
            'date_query'     => array( array( 'before' => gmdate( 'Y-m-d H:i:s', time() - $days * DAY_IN_SECONDS ) ) ),
        );

        if ( '' !== $meta_key ) {
            $args['meta_query'] = array(
                array(
                    'key'     => $meta_key,
                    'compare' => 'EXISTS',
                ),
            );
        }

        return $this->ids( $args );
    }

    /**
     * @return int[]
     */
    private function ids( array $args ): array {
        return get_posts(
            $args + array(
                'post_type'   => self::POST_TYPE,
                'post_status' => 'any',
                'fields'      => 'ids',
            )
        );
    }
}
