<?php

namespace App\Services;

use AltchaOrg\Altcha\Algorithm\DeriveKeyInterface;
use AltchaOrg\Altcha\Altcha;
use AltchaOrg\Altcha\CreateChallengeOptions;
use AltchaOrg\Altcha\VerifySolutionOptions;

/**
 * ALTCHA proof of work for public forms. The browser solves a challenge
 * before submit, with no third party service and no cookies.
 */
final class Captcha
{
    /** Name of the field the widget posts the solved payload in. */
    public const FIELD = 'altcha';

    public const REST_NAMESPACE = 'omakeikka/v1';

    public const REST_ROUTE = '/captcha';

    /**
     * PBKDF2 iterations per attempt. With the counter range in challenge() a
     * desktop solves in about a second, and an old phone in a few.
     */
    private const COST = 1000;

    /**
     * Long enough for a slow typist, since the widget solves the challenge
     * when the visitor first focuses the form.
     */
    private const TTL = HOUR_IN_SECONDS;

    public function __construct(
        private readonly Altcha $altcha,
        private readonly DeriveKeyInterface $algorithm,
    ) {}

    /**
     * @return array<string, mixed> The challenge in the shape the widget fetches.
     */
    public function challenge(): array {
        return $this->altcha->createChallenge(
            new CreateChallengeOptions(
                algorithm: $this->algorithm,
                cost: self::COST,
                counter: random_int( 1000, 3000 ),
                expiresAt: time() + self::TTL,
            )
        )->toArray();
    }

    /**
     * A solved challenge is accepted once, so a captured payload cannot be replayed.
     * Each rejection is logged with its reason, without the payload or the visitor's IP.
     */
    public function verify( string $payload ): bool {
        $reason = $this->rejection( $payload );

        if ( null !== $reason ) {
            error_log( 'omakeikka: captcha rejected, ' . $reason );
        }

        return null === $reason;
    }

    /**
     * Why the payload fails, or null when it passes and is now spent.
     */
    private function rejection( string $payload ): ?string {
        // The widget posts nothing when JavaScript is off or the solve failed, and most bots post nothing at all.
        if ( '' === $payload ) {
            return 'no payload';
        }

        try {
            $options = new VerifySolutionOptions( $payload, $this->algorithm );
        } catch ( \InvalidArgumentException $e ) {
            return 'malformed payload: ' . $e->getMessage();
        }

        $result = $this->altcha->verifySolution( $options );

        if ( ! $result->verified ) {
            return match ( true ) {
                $result->expired          => 'expired challenge',
                $result->invalidSignature => 'invalid signature',
                default                   => 'invalid solution',
            };
        }

        $used = 'altcha_used_' . md5( (string) $options->payload->challenge->signature );

        if ( false !== get_transient( $used ) ) {
            return 'replayed payload';
        }

        set_transient( $used, 1, self::TTL );

        return null;
    }
}
