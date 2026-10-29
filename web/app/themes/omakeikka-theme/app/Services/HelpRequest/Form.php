<?php

namespace App\Services\HelpRequest;

use App\Services\MarketplaceCategoryQuery;
use App\Services\Municipalities;

/**
 * admin-post handler for the public help request form.
 *
 * The request is stored before mail is attempted, so a mail failure cannot
 * lose a lead. Errors and values are passed back through a one-time transient
 * token, since cookies would trip the consent banner and WP has no session.
 */
final class Form
{
    public const ACTION = 'omakeikka_help_request';

    public const NONCE = '_hr_nonce';

    public const HONEYPOT = 'hr_website';

    private const RATE_LIMIT_PER_HOUR = 5;

    public function __construct(
        private readonly Municipalities $municipalities,
        private readonly Repository $repository,
        private readonly Mailer $mailer,
    ) {}

    public function handle(): void {
        $input = wp_unslash( $_POST );

        // A bot that fills the honeypot sees the success page and nothing is stored.
        if ( ! empty( $input[ self::HONEYPOT ] ) ) {
            $this->redirect( array( 'kiitos' => '1' ) );
        }

        $submission = Submission::fromInput( $input );
        $errors     = $this->validate( $submission, (string) ( $input[ self::NONCE ] ?? '' ) );

        if ( ! $errors && ! $this->withinRateLimit() ) {
            $errors['_form'] = __( 'The request could not be sent right now. Please try again shortly.', 'omakeikka-theme' );
        }

        $post_id = $errors ? 0 : $this->repository->create( $submission, wp_hash( $this->clientIp() ) );

        if ( ! $errors && ! $post_id ) {
            $errors['_form'] = __( 'The request could not be saved. Please try again shortly.', 'omakeikka-theme' );
        }

        if ( $errors ) {
            $this->redirect( array( 'virhe' => $this->stash( $errors, $submission ) ), 'hr-errors' );
        }

        $this->mailer->send( $post_id, $submission );

        // The values go along so the thank you page can show what was sent and where.
        $this->redirect( array( 'kiitos' => $this->stash( array(), $submission, $post_id ) ) );
    }

    /**
     * Read and consume a stash, so a refresh does not resurrect a stale error.
     *
     * @return array{errors: array<string, string>, values: array<string, mixed>}
     */
    public function takeStash( string $token ): array {
        $key  = 'hr_err_' . sanitize_key( $token );
        $data = get_transient( $key );

        delete_transient( $key );

        return is_array( $data ) ? $data : array(
            'errors' => array(),
            'values' => array(),
        );
    }

    /**
     * @return array<string, string> Field name to message, '_form' for the whole form.
     */
    private function validate( Submission $submission, string $nonce ): array {
        $errors = array();

        // A stale nonce still returns the visitor's values, since a page left
        // open overnight produces one for a real person.
        if ( ! wp_verify_nonce( sanitize_text_field( $nonce ), self::ACTION ) ) {
            $errors['_form'] = __( 'The form expired. Check your details and send again.', 'omakeikka-theme' );
        }

        // The service is a hidden field, so a mismatch is reported on the visible group select.
        $groups = array_column( MarketplaceCategoryQuery::groups(), 'slug' );

        if ( ! in_array( $submission->group_slug, $groups, true )
            || ( '' !== $submission->category_slug && MarketplaceCategoryQuery::groupKeyForCategory( $submission->category_slug ) !== $submission->group_slug ) ) {
            $errors['group_slug'] = __( 'Choose a category', 'omakeikka-theme' );
        }

        if ( '' === $submission->title ) {
            $errors['title'] = __( 'Title is required', 'omakeikka-theme' );
        }

        if ( '' === $submission->description ) {
            $errors['description'] = __( 'Description is required', 'omakeikka-theme' );
        } elseif ( preg_match_all( '#https?://#i', $submission->description ) > 2 ) {
            $errors['description'] = __( 'The description can contain at most two links', 'omakeikka-theme' );
        }

        if ( ! $this->municipalities->isValid( $submission->municipality_id ) ) {
            $errors['municipality_id'] = __( 'Choose a municipality', 'omakeikka-theme' );
        }

        if ( '' === $submission->name ) {
            $errors['name'] = __( 'Name or nickname is required', 'omakeikka-theme' );
        }

        if ( '' === $submission->email ) {
            $errors['email'] = __( 'Email is required', 'omakeikka-theme' );
        } elseif ( ! is_email( $submission->email ) || sanitize_email( $submission->email ) !== $submission->email ) {
            $errors['email'] = __( 'Check the email address', 'omakeikka-theme' );
        }

        if ( ! $submission->consent ) {
            $errors['consent'] = __( 'You must consent to the processing of your details', 'omakeikka-theme' );
        }

        return $errors;
    }

    private function withinRateLimit(): bool {
        $key  = 'hr_rl_' . wp_hash( $this->clientIp() );
        $hits = (int) get_transient( $key );

        if ( $hits >= self::RATE_LIMIT_PER_HOUR ) {
            return false;
        }

        set_transient( $key, $hits + 1, HOUR_IN_SECONDS );

        return true;
    }

    /**
     * The site runs behind a proxy, so the last X-Forwarded-For hop is used.
     * Earlier hops are client controlled and would bypass the rate limit.
     */
    private function clientIp(): string {
        $hops = explode( ',', (string) ( $_SERVER['HTTP_X_FORWARDED_FOR'] ?? '' ) );
        $last = trim( end( $hops ) );

        return filter_var( $last, FILTER_VALIDATE_IP ) ? $last : (string) ( $_SERVER['REMOTE_ADDR'] ?? '' );
    }

    private function stash( array $errors, Submission $submission, int $reference = 0 ): string {
        $token = bin2hex( random_bytes( 10 ) );

        set_transient(
            'hr_err_' . $token,
            array(
                'errors' => $errors,
                'values' => get_object_vars( $submission ) + array( 'reference' => $reference ),
            ),
            5 * MINUTE_IN_SECONDS
        );

        return $token;
    }

    private function redirect( array $args, string $fragment = '' ): never {
        wp_safe_redirect( add_query_arg( $args, MarketplaceCategoryQuery::helpRequestPageUrl() ) . ( $fragment ? '#' . $fragment : '' ), 303 );
        exit;
    }
}
