<?php

namespace App\Services\HelpRequest;

use App\Services\Municipalities;

/**
 * Admin list columns, the read-only details box and the status control.
 */
final class Admin
{
    private const NONCE = '_hr_status_nonce';

    public function __construct(
        private readonly Repository $repository,
        private readonly Municipalities $municipalities,
    ) {}

    public function columns( array $columns ): array {
        return array(
            'cb'              => $columns['cb'] ?? '',
            'title'           => __( 'Title', 'omakeikka-theme' ),
            'hr_status'       => __( 'Status', 'omakeikka-theme' ),
            'hr_municipality' => __( 'Municipality', 'omakeikka-theme' ),
            'hr_contact'      => __( 'Contact details', 'omakeikka-theme' ),
            'date'            => __( 'Received', 'omakeikka-theme' ),
        );
    }

    public function renderColumn( string $column, int $post_id ): void {
        switch ( $column ) {
            case 'hr_status':
                echo esc_html( $this->repository->status( $post_id )->label() );
                break;

            case 'hr_municipality':
                echo esc_html( $this->municipalities->name( (string) get_post_meta( $post_id, '_hr_municipality_id', true ) ) );
                break;

            case 'hr_contact':
                $this->renderContact( $post_id );
                break;
        }
    }

    public function addMetaBoxes(): void {
        add_meta_box( 'omakeikka_help_request_details', __( 'Help request details', 'omakeikka-theme' ), array( $this, 'renderDetails' ), null, 'normal', 'high' );
        add_meta_box( 'omakeikka_help_request_status', __( 'Status', 'omakeikka-theme' ), array( $this, 'renderStatus' ), null, 'side', 'high' );
    }

    public function renderDetails( \WP_Post $post ): void {
        $meta   = fn( string $key ) => (string) get_post_meta( $post->ID, $key, true );
        $budget = Budget::tryFrom( $meta( '_hr_budget' ) );

        $rows = array(
            __( 'Name', 'omakeikka-theme' )          => $meta( '_hr_name' ),
            __( 'Email', 'omakeikka-theme' )         => $meta( '_hr_email' ),
            __( 'Phone', 'omakeikka-theme' )         => $meta( '_hr_phone' ),
            __( 'Municipality', 'omakeikka-theme' )  => $this->municipalities->name( $meta( '_hr_municipality_id' ) ),
            __( 'Service group', 'omakeikka-theme' ) => $meta( '_hr_group_slug' ),
            __( 'Service', 'omakeikka-theme' )       => $meta( '_hr_category_slug' ),
            __( 'Budget', 'omakeikka-theme' )        => $budget?->label() ?? '',
            __( 'Consent given', 'omakeikka-theme' ) => $meta( '_hr_consent_at' ),
        );

        echo '<table class="widefat striped"><tbody>';

        foreach ( array_filter( $rows, 'strlen' ) as $label => $value ) {
            printf( '<tr><th scope="row">%s</th><td>%s</td></tr>', esc_html( $label ), esc_html( $value ) );
        }

        echo '</tbody></table>';
    }

    public function renderStatus( \WP_Post $post ): void {
        $current = $this->repository->status( $post->ID );

        wp_nonce_field( self::NONCE, self::NONCE );

        echo '<select name="_hr_status" class="widefat">';

        foreach ( Status::cases() as $status ) {
            printf( '<option value="%s"%s>%s</option>', esc_attr( $status->value ), selected( $current->value, $status->value, false ), esc_html( $status->label() ) );
        }

        echo '</select>';
    }

    public function saveStatus( int $post_id ): void {
        if ( ! isset( $_POST['_hr_status'], $_POST[ self::NONCE ] ) || wp_is_post_revision( $post_id ) ) {
            return;
        }

        if ( ! wp_verify_nonce( sanitize_key( wp_unslash( $_POST[ self::NONCE ] ) ), self::NONCE ) || ! current_user_can( 'edit_post', $post_id ) ) {
            return;
        }

        $status = Status::tryFrom( sanitize_key( wp_unslash( $_POST['_hr_status'] ) ) );

        if ( $status ) {
            $this->repository->setStatus( $post_id, $status );
        }
    }

    private function renderContact( int $post_id ): void {
        $name  = (string) get_post_meta( $post_id, '_hr_name', true );
        $email = (string) get_post_meta( $post_id, '_hr_email', true );
        $phone = (string) get_post_meta( $post_id, '_hr_phone', true );

        if ( $name ) {
            echo esc_html( $name ) . '<br>';
        }

        if ( $email ) {
            printf( '<a href="mailto:%1$s">%1$s</a><br>', esc_attr( $email ) );
        }

        if ( $phone ) {
            printf( '<a href="tel:%s">%s</a>', esc_attr( preg_replace( '/\s+/', '', $phone ) ), esc_html( $phone ) );
        }
    }
}
