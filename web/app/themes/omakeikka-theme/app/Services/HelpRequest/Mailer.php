<?php

namespace App\Services\HelpRequest;

use App\Services\MarketplaceCategoryQuery;
use App\Services\Municipalities;

/**
 * Notifies the team of a new request and confirms it to the submitter.
 */
final class Mailer
{
    public function __construct(
        private readonly Municipalities $municipalities,
    ) {}

    public function send( int $post_id, Submission $submission ): void {
        $recipients = $this->recipients();

        // Always the site's own domain. The submitter's address in From fails
        // SPF and DKIM for their domain.
        $headers = array(
            sprintf( 'From: Omakeikka <no-reply@%s>', preg_replace( '/^www\./', '', (string) wp_parse_url( home_url(), PHP_URL_HOST ) ) ),
            'Content-Type: text/plain; charset=UTF-8',
        );

        $details = array(
            __( 'Title', 'omakeikka-theme' ) . ': ' . $submission->title,
            __( 'Category', 'omakeikka-theme' ) . ': ' . $this->groupName( $submission->group_slug ),
            __( 'Municipality', 'omakeikka-theme' ) . ': ' . $this->municipalities->name( $submission->municipality_id ),
            __( 'Budget', 'omakeikka-theme' ) . ': ' . ( Budget::tryFrom( $submission->budget )?->label() ?? '' ),
        );

        $notified = wp_mail(
            $recipients,
            /* translators: %s: help request title */
            sprintf( __( 'New help request: %s', 'omakeikka-theme' ), $submission->title ),
            implode( "\n", array_merge(
                $details,
                array(
                    __( 'Name', 'omakeikka-theme' ) . ': ' . $submission->name,
                    __( 'Email', 'omakeikka-theme' ) . ': ' . $submission->email,
                    __( 'Phone', 'omakeikka-theme' ) . ': ' . $submission->phone,
                    '',
                    __( 'Description', 'omakeikka-theme' ) . ':',
                    $submission->description,
                    '',
                    admin_url( 'post.php?post=' . $post_id . '&action=edit' ),
                )
            ) ),
            // wp_mail splits Reply-To on commas, so the display name must not carry address syntax.
            array_merge( $headers, array( sprintf( 'Reply-To: %s <%s>', str_replace( array( ',', ';', '"', '<', '>' ), '', $submission->name ), $submission->email ) ) )
        );

        // Replies reach the team, so the submitter can correct or cancel the request.
        $confirmed = wp_mail(
            $submission->email,
            /* translators: %s: help request reference number */
            sprintf( __( 'Your task has been received (#%s)', 'omakeikka-theme' ), $post_id ),
            implode( "\n", array_merge(
                array(
                    /* translators: %s: submitter's name */
                    sprintf( __( 'Hi %s,', 'omakeikka-theme' ), $submission->name ),
                    '',
                    __( 'Thank you! We will reply as soon as possible.', 'omakeikka-theme' ),
                    /* translators: %s: help request reference number */
                    sprintf( __( 'Task number: %s', 'omakeikka-theme' ), '#' . $post_id ),
                    '',
                    __( 'What you sent:', 'omakeikka-theme' ),
                ),
                $details,
                array(
                    '',
                    $submission->description,
                    '',
                    __( 'What happens next?', 'omakeikka-theme' ),
                    '1. ' . __( 'We go through your task and look for a suitable konkari for it.', 'omakeikka-theme' ),
                    '2. ' . __( 'We will contact you using the details you gave.', 'omakeikka-theme' ),
                    '3. ' . __( 'You agree the price and the content of the work directly with the helper. You pay only after the work is done: the helper invoices it as a light entrepreneur through an invoicing service.', 'omakeikka-theme' ),
                    '',
                    __( 'To change or cancel your request, reply to this email.', 'omakeikka-theme' ),
                    '',
                    'Omakeikka',
                    home_url( '/' ),
                )
            ) ),
            array_merge( $headers, array( 'Reply-To: Omakeikka <' . $recipients[0] . '>' ) )
        );

        if ( ! $notified || ! $confirmed ) {
            error_log( 'omakeikka: help request mail failed for post ' . $post_id );
        }
    }

    private function groupName( string $slug ): string {
        foreach ( MarketplaceCategoryQuery::groups() as $group ) {
            if ( $slug === $group['slug'] ) {
                return $group['name'];
            }
        }

        return $slug;
    }

    /**
     * @return string[]
     */
    private function recipients(): array {
        $configured = defined( 'OMAKEIKKA_HELP_REQUEST_EMAIL' ) ? (string) OMAKEIKKA_HELP_REQUEST_EMAIL : '';
        $configured = array_values( array_filter( array_map( 'trim', explode( ',', $configured ) ) ) );

        return $configured ?: array( get_option( 'admin_email' ) );
    }
}
