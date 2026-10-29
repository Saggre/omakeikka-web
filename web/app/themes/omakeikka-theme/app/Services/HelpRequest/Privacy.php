<?php

namespace App\Services\HelpRequest;

/**
 * Retention schedule and the GDPR export and erase hooks.
 *
 * Anonymise runs before delete so the funnel counts survive the personal data
 * purge. The three day counts are the schedule in the privacy policy.
 */
final class Privacy
{
    public const CRON_HOOK = 'omakeikka_help_request_cleanup';

    private const ID = 'omakeikka-help-request';

    private const IP_DAYS = 30;

    private const ANONYMISE_DAYS = 365;

    private const DELETE_DAYS = 730;

    private const PER_PAGE = 20;

    public function __construct(
        private readonly Repository $repository,
    ) {}

    public function schedule(): void {
        if ( ! wp_next_scheduled( self::CRON_HOOK ) ) {
            wp_schedule_event( time() + HOUR_IN_SECONDS, 'daily', self::CRON_HOOK );
        }
    }

    public function cleanup(): void {
        foreach ( $this->repository->idsOlderThan( self::IP_DAYS, '_hr_ip_hash' ) as $post_id ) {
            delete_post_meta( $post_id, '_hr_ip_hash' );
        }

        foreach ( $this->repository->idsOlderThan( self::ANONYMISE_DAYS, '_hr_email' ) as $post_id ) {
            $this->repository->anonymise( $post_id );
        }

        foreach ( $this->repository->idsOlderThan( self::DELETE_DAYS ) as $post_id ) {
            $this->repository->delete( $post_id );
        }
    }

    public function registerExporter( array $exporters ): array {
        $exporters[ self::ID ] = array(
            'exporter_friendly_name' => __( 'Help requests', 'omakeikka-theme' ),
            'callback'               => array( $this, 'export' ),
        );

        return $exporters;
    }

    public function registerEraser( array $erasers ): array {
        $erasers[ self::ID ] = array(
            'eraser_friendly_name' => __( 'Help requests', 'omakeikka-theme' ),
            'callback'             => array( $this, 'erase' ),
        );

        return $erasers;
    }

    public function export( string $email, int $page = 1 ): array {
        $ids   = $this->repository->idsByEmail( $email, $page, self::PER_PAGE );
        $items = array();

        foreach ( $ids as $post_id ) {
            $fields = array(
                __( 'Title', 'omakeikka-theme' )         => get_the_title( $post_id ),
                __( 'Description', 'omakeikka-theme' )   => get_post_field( 'post_content', $post_id ),
                __( 'Name', 'omakeikka-theme' )          => get_post_meta( $post_id, '_hr_name', true ),
                __( 'Email', 'omakeikka-theme' )         => get_post_meta( $post_id, '_hr_email', true ),
                __( 'Phone', 'omakeikka-theme' )         => get_post_meta( $post_id, '_hr_phone', true ),
                __( 'Municipality', 'omakeikka-theme' )  => get_post_meta( $post_id, '_hr_municipality_id', true ),
                __( 'Consent given', 'omakeikka-theme' ) => get_post_meta( $post_id, '_hr_consent_at', true ),
            );

            $items[] = array(
                'group_id'    => self::ID,
                'group_label' => __( 'Help requests', 'omakeikka-theme' ),
                'item_id'     => 'help-request-' . $post_id,
                'data'        => array_map(
                    fn( $name, $value ) => compact( 'name', 'value' ),
                    array_keys( $fields ),
                    array_values( $fields )
                ),
            );
        }

        return array(
            'data' => $items,
            'done' => count( $ids ) < self::PER_PAGE,
        );
    }

    /**
     * Anonymise rather than delete, so the request still counts towards the
     * funnel while the personal data is gone.
     */
    public function erase( string $email, int $page = 1 ): array {
        // Anonymising removes the email, so the next batch is always page 1.
        $ids = $this->repository->idsByEmail( $email, 1, self::PER_PAGE );

        foreach ( $ids as $post_id ) {
            $this->repository->anonymise( $post_id );
        }

        return array(
            'items_removed'  => count( $ids ) > 0,
            'items_retained' => false,
            'messages'       => array(),
            'done'           => count( $ids ) < self::PER_PAGE,
        );
    }
}
