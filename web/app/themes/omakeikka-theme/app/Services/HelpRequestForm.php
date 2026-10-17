<?php

namespace App\Services;

/**
 * Validates, stores and announces a household help request.
 *
 * The form is public and unauthenticated, so every input is treated as
 * hostile. The record is written before the notification is attempted, so a
 * mail failure can never lose a lead.
 *
 * Copy note: several user facing strings below are marked COPY GAP. They are
 * placeholders pending the owner's wording. Per AGENTS.md, Finnish copy that
 * the app does not already define is flagged rather than invented. Strings
 * without that marker are verbatim from ~/repos/omakeikka/lang/fi.json.
 */
class HelpRequestForm
{
    public const ACTION = 'omakeikka_help_request';

    public const NONCE = '_hr_nonce';

    /**
     * Bumped whenever the consent wording changes, so an old record still
     * says what was actually agreed to.
     */
    public const CONSENT_VERSION = '2026-09';

    private const MAX_TITLE = 255;

    private const MAX_DESCRIPTION = 5000;

    private const MAX_NAME = 100;

    /** Submissions faster than this are not human. */
    private const MIN_SECONDS = 3;

    /** A form page open longer than this has a stale nonce anyway. */
    private const MAX_SECONDS = 3600;

    private const RATE_PER_IP = 5;

    private const RATE_GLOBAL = 200;

    /**
     * admin-post handler. Always redirects, never renders.
     */
    public static function handle(): void {
        $page_url = MarketplaceCategoryQuery::helpRequestPageUrl();
        $raw      = wp_unslash( $_POST );

        // A bot that trips the honeypot or the timing trap is sent to the
        // success page and nothing is stored. Never tell it why.
        if ( ! self::passesTraps( $raw ) ) {
            self::redirect( add_query_arg( 'kiitos', '1', $page_url ) . '#hr-success' );
        }

        [ $clean, $errors ] = self::validate( $raw );

        if ( ! $errors && ! self::withinRateLimit() ) {
            /* COPY GAP C11: neutral failure message, should tell the visitor to try again or call. */
            $errors['_form'] = __( 'Pyyntöä ei voitu lähettää juuri nyt. Yritä hetken kuluttua uudelleen.', 'omakeikka-theme' );
        }

        if ( $errors ) {
            self::redirect( add_query_arg( 'virhe', self::stashErrors( $errors, $clean ), $page_url ) . '#hr-errors' );
        }

        $post_id = self::store( $clean );

        if ( ! $post_id ) {
            $errors['_form'] = __( 'Pyyntöä ei voitu tallentaa. Yritä hetken kuluttua uudelleen.', 'omakeikka-theme' );
            self::redirect( add_query_arg( 'virhe', self::stashErrors( $errors, $clean ), $page_url ) . '#hr-errors' );
        }

        self::notify( $post_id, $clean );

        self::redirect( add_query_arg( 'kiitos', '1', $page_url ) . '#hr-success' );
    }

    /**
     * Honeypot and timing trap.
     */
    private static function passesTraps( array $raw ): bool {
        if ( ! empty( $raw['hr_website'] ) ) {
            return false;
        }

        $token = isset( $raw['hr_t'] ) ? (string) $raw['hr_t'] : '';

        if ( ! str_contains( $token, '|' ) ) {
            return false;
        }

        [ $hash, $issued ] = explode( '|', $token, 2 );
        $issued            = (int) $issued;

        if ( ! hash_equals( wp_hash( (string) $issued ), $hash ) ) {
            return false;
        }

        $elapsed = time() - $issued;

        return $elapsed >= self::MIN_SECONDS && $elapsed <= self::MAX_SECONDS;
    }

    /**
     * Issue the signed timestamp the form embeds.
     */
    public static function timestampToken(): string {
        $now = time();

        return wp_hash( (string) $now ) . '|' . $now;
    }

    /**
     * @return array{0: array<string, string>, 1: array<string, string>}
     */
    public static function validate( array $raw ): array {
        $errors = array();

        $clean = array(
            'title'           => self::text( $raw['title'] ?? '', self::MAX_TITLE ),
            'description'     => self::multiline( $raw['description'] ?? '', self::MAX_DESCRIPTION ),
            'group_slug'      => sanitize_key( $raw['group_slug'] ?? '' ),
            'category_slug'   => sanitize_key( $raw['category_slug'] ?? '' ),
            'municipality_id' => sanitize_text_field( $raw['municipality_id'] ?? '' ),
            'budget'          => sanitize_key( $raw['budget'] ?? '' ),
            'name'            => self::text( $raw['name'] ?? '', self::MAX_NAME ),
            'email'           => sanitize_email( $raw['email'] ?? '' ),
            'phone'           => \omakeikka_sanitize_phone( $raw['phone'] ?? '' ),
            'consent'         => ! empty( $raw['consent'] ) ? '1' : '',
            'source_url'      => esc_url_raw( $raw['source_url'] ?? '' ),
        );

        // The nonce is checked last of the cheap checks so that a stale nonce
        // still gets the visitor their values back. A logged out nonce is a
        // rotating site token, so a page left open overnight, or served from
        // the page cache, produces a stale one for a perfectly real person.
        $nonce = isset( $raw[ self::NONCE ] ) ? sanitize_text_field( $raw[ self::NONCE ] ) : '';

        if ( ! wp_verify_nonce( $nonce, self::ACTION ) ) {
            /* COPY GAP C11 variant: session expired, please resend. */
            $errors['_form'] = __( 'Lomake vanheni. Tarkista tiedot ja lähetä uudelleen.', 'omakeikka-theme' );
            error_log( 'omakeikka: help request nonce failed' );
        }

        if ( '' === $clean['title'] ) {
            /* COPY GAP C8 */
            $errors['title'] = __( 'Otsikko vaaditaan', 'omakeikka-theme' );
        }

        if ( '' === $clean['description'] ) {
            /* COPY GAP C9 */
            $errors['description'] = __( 'Kuvaus vaaditaan', 'omakeikka-theme' );
        }

        if ( '' === $clean['group_slug'] ) {
            $errors['group_slug'] = __( 'Valitse kategoria', 'omakeikka-theme' );
        }

        if ( ! Municipalities::isValid( $clean['municipality_id'] ) ) {
            /* COPY GAP C10 */
            $errors['municipality_id'] = __( 'Valitse paikkakunta', 'omakeikka-theme' );
            $clean['municipality_id']  = '';
        }

        if ( '' === $clean['name'] ) {
            /* COPY GAP C8 variant */
            $errors['name'] = __( 'Nimi vaaditaan', 'omakeikka-theme' );
        }

        if ( '' === $clean['email'] || ! is_email( $clean['email'] ) ) {
            $errors['email'] = __( 'Sähköposti vaaditaan', 'omakeikka-theme' );
        }

        if ( '' === $clean['consent'] ) {
            $errors['consent'] = __( 'Sinun on annettava suostumus tietojesi käsittelyyn', 'omakeikka-theme' );
        }

        // Link spam is what a form like this actually attracts.
        if ( preg_match_all( '#https?://#i', $clean['description'] ) > 2 ) {
            $errors['description'] = __( 'Kuvauksessa voi olla enintään kaksi linkkiä', 'omakeikka-theme' );
        }

        return array( $clean, $errors );
    }

    /**
     * Single line text, length counted in characters rather than bytes so
     * that ä and ö do not cost double.
     */
    private static function text( $value, int $max ): string {
        return trim( mb_substr( sanitize_text_field( (string) $value ), 0, $max ) );
    }

    /**
     * Free text. No HTML survives an anonymous form, so strip rather than
     * filter.
     */
    private static function multiline( $value, int $max ): string {
        $value = wp_strip_all_tags( (string) $value, false );
        $value = preg_replace( '/\R/u', "\n", $value );

        return trim( mb_substr( $value, 0, $max ) );
    }

    /**
     * Per-IP and global throttle, both transient backed.
     */
    private static function withinRateLimit(): bool {
        $ip_key = 'hr_rl_' . wp_hash( self::clientIp() );
        $ip_hit = (int) get_transient( $ip_key );

        if ( $ip_hit >= self::RATE_PER_IP ) {
            return false;
        }

        $global = (int) get_transient( 'hr_rl_global' );

        if ( $global >= self::RATE_GLOBAL ) {
            return false;
        }

        set_transient( $ip_key, $ip_hit + 1, HOUR_IN_SECONDS );
        set_transient( 'hr_rl_global', $global + 1, HOUR_IN_SECONDS );

        return true;
    }

    /**
     * Client address.
     *
     * The site runs behind a proxy in production, so X-Forwarded-For is read,
     * but only the LAST hop. The first entry is attacker controlled and
     * trusting it makes the rate limiter trivially bypassable.
     */
    private static function clientIp(): string {
        $remote = isset( $_SERVER['REMOTE_ADDR'] ) ? (string) $_SERVER['REMOTE_ADDR'] : '';

        if ( ! empty( $_SERVER['HTTP_X_FORWARDED_FOR'] ) ) {
            $hops = array_map( 'trim', explode( ',', (string) $_SERVER['HTTP_X_FORWARDED_FOR'] ) );
            $last = end( $hops );

            if ( $last && filter_var( $last, FILTER_VALIDATE_IP ) ) {
                return $last;
            }
        }

        return $remote;
    }

    /**
     * Park errors and submitted values behind a one-time token.
     *
     * A cookie would trip the consent banner and break page caching, and WP
     * has no session. Values are stored as well as errors so nobody has to
     * retype a long description.
     */
    private static function stashErrors( array $errors, array $values ): string {
        $token = wp_generate_password( 20, false, false );

        unset( $values['consent'] );

        set_transient(
            'hr_err_' . $token,
            array(
                'errors' => $errors,
                'values' => $values,
            ),
            5 * MINUTE_IN_SECONDS
        );

        return $token;
    }

    /**
     * Read and consume a stash. Deleting on read means a refresh does not
     * resurrect a stale error state.
     *
     * @return array{errors: array<string, string>, values: array<string, string>}
     */
    public static function takeStash( string $token ): array {
        $empty = array(
            'errors' => array(),
            'values' => array(),
        );

        $token = sanitize_key( $token );

        if ( '' === $token ) {
            return $empty;
        }

        $data = get_transient( 'hr_err_' . $token );
        delete_transient( 'hr_err_' . $token );

        if ( ! is_array( $data ) ) {
            return $empty;
        }

        return array(
            'errors' => isset( $data['errors'] ) && is_array( $data['errors'] ) ? $data['errors'] : array(),
            'values' => isset( $data['values'] ) && is_array( $data['values'] ) ? $data['values'] : array(),
        );
    }

    /**
     * Insert the record.
     */
    private static function store( array $clean ): int {
        $post_id = wp_insert_post(
            array(
                'post_type'    => 'help_request',
                'post_status'  => 'private',
                'post_title'   => wp_slash( $clean['title'] ),
                'post_content' => wp_slash( $clean['description'] ),
            ),
            true
        );

        if ( is_wp_error( $post_id ) || ! $post_id ) {
            error_log( 'omakeikka: help request insert failed' );

            return 0;
        }

        $now = gmdate( 'Y-m-d H:i:s' );

        $meta = array(
            '_hr_name'            => $clean['name'],
            '_hr_email'           => $clean['email'],
            '_hr_phone'           => $clean['phone'],
            '_hr_municipality_id' => $clean['municipality_id'],
            '_hr_group_slug'      => $clean['group_slug'],
            '_hr_category_slug'   => $clean['category_slug'],
            '_hr_budget'          => $clean['budget'],
            '_hr_status'          => 'uusi',
            '_hr_status_changed'  => $now,
            '_hr_consent_at'      => $now,
            '_hr_consent_version' => self::CONSENT_VERSION,
            '_hr_source_url'      => $clean['source_url'],
            '_hr_ip_hash'         => wp_hash( self::clientIp() ),
        );

        foreach ( $meta as $key => $value ) {
            if ( '' !== $value ) {
                update_post_meta( $post_id, $key, $value );
            }
        }

        update_post_meta( $post_id, '_hr_consent', true );

        return (int) $post_id;
    }

    /**
     * Notify the team, then confirm to the submitter.
     *
     * The confirmation is sent only after every check has passed. An
     * unconditional confirmation would turn this form into an open relay that
     * reflects attacker text to attacker chosen addresses.
     */
    private static function notify( int $post_id, array $clean ): void {
        $failed = false;

        $listener = function () use ( &$failed ) {
            $failed = true;
        };

        add_action( 'wp_mail_failed', $listener );

        $municipality = Municipalities::name( $clean['municipality_id'] );
        $from         = sprintf( 'From: Omakeikka <no-reply@%s>', self::mailDomain() );

        $lines = array(
            __( 'Otsikko', 'omakeikka-theme' ) . ': ' . $clean['title'],
            __( 'Paikkakunta', 'omakeikka-theme' ) . ': ' . $municipality,
            __( 'Palveluryhmä', 'omakeikka-theme' ) . ': ' . $clean['group_slug'],
            __( 'Nimi', 'omakeikka-theme' ) . ': ' . $clean['name'],
            __( 'Sähköposti', 'omakeikka-theme' ) . ': ' . $clean['email'],
            __( 'Puhelin', 'omakeikka-theme' ) . ': ' . $clean['phone'],
            '',
            __( 'Kuvaus', 'omakeikka-theme' ) . ':',
            $clean['description'],
            '',
            admin_url( 'post.php?post=' . $post_id . '&action=edit' ),
        );

        wp_mail(
            self::recipients(),
            sprintf( '%s: %s', __( 'Uusi avunpyyntö', 'omakeikka-theme' ), $clean['title'] ),
            implode( "\n", $lines ),
            array(
                $from,
                sprintf( 'Reply-To: %s <%s>', $clean['name'], $clean['email'] ),
                'Content-Type: text/plain; charset=UTF-8',
            )
        );

        /* COPY GAP C6: confirmation body, must not promise a response time the ops loop cannot keep. */
        wp_mail(
            $clean['email'],
            __( 'Kiitos avunpyynnöstäsi', 'omakeikka-theme' ),
            implode( "\n", array(
                __( 'Kiitos! Vastaamme mahdollisimman pian.', 'omakeikka-theme' ),
                '',
                __( 'Otsikko', 'omakeikka-theme' ) . ': ' . $clean['title'],
            ) ),
            array( $from, 'Content-Type: text/plain; charset=UTF-8' )
        );

        remove_action( 'wp_mail_failed', $listener );

        if ( $failed ) {
            update_post_meta( $post_id, '_hr_mail_failed', true );
            error_log( 'omakeikka: help request mail failed for post ' . $post_id );
        }
    }

    /**
     * Sending domain, always the site's own. Putting the submitter in From:
     * fails SPF and DKIM for their domain and burns our sending reputation.
     */
    private static function mailDomain(): string {
        $host = wp_parse_url( home_url(), PHP_URL_HOST );

        return is_string( $host ) ? preg_replace( '/^www\./', '', $host ) : 'omakeikka.fi';
    }

    /**
     * @return string[]
     */
    private static function recipients(): array {
        $configured = defined( 'OMAKEIKKA_HELP_REQUEST_EMAIL' ) ? (string) OMAKEIKKA_HELP_REQUEST_EMAIL : '';

        $list = '' !== $configured
            ? array_filter( array_map( 'trim', explode( ',', $configured ) ) )
            : array( get_option( 'admin_email' ) );

        return apply_filters( 'omakeikka_help_request_recipients', $list );
    }

    /**
     * 303 so the redirect is unambiguously a GET.
     */
    private static function redirect( string $url ): void {
        wp_safe_redirect( $url, 303 );
        exit;
    }
}
