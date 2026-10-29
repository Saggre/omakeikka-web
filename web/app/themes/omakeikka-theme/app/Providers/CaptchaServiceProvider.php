<?php

namespace App\Providers;

use AltchaOrg\Altcha\Algorithm\Pbkdf2;
use AltchaOrg\Altcha\Altcha;
use App\Services\Captcha;
use Illuminate\Support\ServiceProvider;

/**
 * Builds the ALTCHA service and serves its challenges over REST.
 */
class CaptchaServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        // The secrets come from the WordPress salts, so no extra config is needed per environment.
        $this->app->singleton(
            Captcha::class,
            fn() => new Captcha(
                new Altcha(
                    hmacSignatureSecret: wp_salt( 'altcha' ),
                    hmacKeySignatureSecret: wp_salt( 'altcha_signature' ),
                ),
                new Pbkdf2(),
            )
        );
    }

    public function boot(): void
    {
        add_action( 'rest_api_init', array( $this, 'registerRoute' ) );
    }

    public function registerRoute(): void
    {
        register_rest_route(
            Captcha::REST_NAMESPACE,
            Captcha::REST_ROUTE,
            array(
                'methods'             => \WP_REST_Server::READABLE,
                'permission_callback' => '__return_true',
                'callback'            => array( $this, 'challenge' ),
            )
        );
    }

    /**
     * Every visitor needs a fresh challenge, so neither the browser, a proxy nor LiteSpeed's REST cache may keep one.
     * A shared challenge would be rejected as replayed for everyone after the first visitor.
     */
    public function challenge(): \WP_REST_Response
    {
        do_action( 'litespeed_control_set_nocache', 'altcha challenge' );

        return new \WP_REST_Response( $this->app->make( Captcha::class )->challenge(), 200, array( 'Cache-Control' => 'no-store' ) );
    }
}
