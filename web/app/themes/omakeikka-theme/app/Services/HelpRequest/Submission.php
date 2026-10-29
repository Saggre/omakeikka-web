<?php

namespace App\Services\HelpRequest;

/**
 * Sanitised form input. Property names match the form field names so the
 * values can be echoed back into the form after a failed submit.
 */
final readonly class Submission
{
    public function __construct(
        public string $title,
        public string $description,
        public string $group_slug,
        public string $category_slug,
        public string $municipality_id,
        public string $budget,
        public string $name,
        public string $email,
        public string $phone,
        public bool $consent,
    ) {}

    public static function fromInput( array $input ): self {
        return new self(
            title: self::text( $input['title'] ?? '', 255 ),
            description: self::multiline( $input['description'] ?? '', 5000 ),
            group_slug: sanitize_key( $input['group_slug'] ?? '' ),
            category_slug: sanitize_key( $input['category_slug'] ?? '' ),
            municipality_id: sanitize_text_field( $input['municipality_id'] ?? '' ),
            budget: Budget::tryFrom( sanitize_key( $input['budget'] ?? '' ) )?->value ?? '',
            name: self::text( $input['name'] ?? '', 100 ),
            // Kept as typed so a typo can be shown back. Form validates it with
            // is_email(), and the _hr_email meta runs sanitize_email() on write.
            email: self::text( $input['email'] ?? '', 254 ),
            phone: self::phone( $input['phone'] ?? '' ),
            consent: ! empty( $input['consent'] ),
        );
    }

    /**
     * Digits, spaces and the punctuation a phone number can carry.
     */
    public static function phone( $value ): string {
        return trim( substr( preg_replace( '/[^0-9+()\- ]/', '', (string) $value ), 0, 32 ) );
    }

    /**
     * Length is counted in characters so that ä and ö do not cost double.
     */
    private static function text( $value, int $max ): string {
        return trim( mb_substr( sanitize_text_field( (string) $value ), 0, $max ) );
    }

    private static function multiline( $value, int $max ): string {
        $value = preg_replace( '/\R/u', "\n", wp_strip_all_tags( (string) $value ) );

        return trim( mb_substr( $value, 0, $max ) );
    }
}
